<?php

use Illuminate\Support\Facades\Input;
class EclaimController extends \BaseController {


	public function __construct( )
	{
		\Cloudinary::config(array(
			"cloud_name" => "mednefits-com",
			"api_key" => "881921989926795",
			"api_secret" => "zNoFc7EHPMtafUEt0r8gxkv4V5U"
		));
	}

    // logout
	public function logoutEmployee( )
	{
		Session::forget('employee-session');
	}

	// login
	public function loginEmployee( )
	{
		$input = Input::all();
    $email = $input['email'];
    $password = $input['password'];

		$check = DB::table('user')
		->where(function($query) use ($email, $password) {
			$query->where('UserType', 5)
			->where('Email', $email)
		  ->where('password', md5($password))
		  ->where('Active', 1);
		})
    ->orWhere(function($query) use ($email, $password){
    	$query->where('UserType', 5)
			->where('NRIC', 'like', '%'.$email.'%')
		  ->where('password', md5($password))
		  ->where('Active', 1);
    })
    ->orWhere(function($query) use ($email, $password){
    	$query->where('UserType', 5)
			->where('PhoneNo', $email)
		  ->where('password', md5($password))
		  ->where('Active', 1);
    })
		->first();

		if($check) {
			Session::put('employee-session', $check->UserID);
			$admin_logs = array(
        'admin_id'  => $check->UserID,
        'admin_type' => 'member',
        'type'      => 'admin_employee_login_portal',
        'data'      => SystemLogLibrary::serializeData($input)
      );
      SystemLogLibrary::createAdminLog($admin_logs);
			return array('status' => TRUE, 'message' => 'Success.');
		}

		return array('status' => FALSE, 'message' => 'Invalid Credentials.');
	}

	public function getEmployeeLists( )
	{
		$result = DB::table('user')->where('UserType', 5)->get();
		return array('status' => TRUE, 'data' => $result);
	}

	public function updateEmployeePassword( )
	{
		$employee = StringHelper::getEmployeeSession( );
		return array( 'result' => AuthLibrary::ChangePassword($employee->UserID));
	}

	public function checkPendingEclaimsMedical($user_id)
	{
		$amount = DB::table('e_claim')
		->where('user_id', $user_id)
		->where('status', 0)
		->where('spending_type', 'medical')
		->sum('amount');

		return $amount;
	}

	public function checkPendingEclaimsWellness($user_id)
	{
		$amount = DB::table('e_claim')
		->where('user_id', $user_id)
		->where('status', 0)
		->where('spending_type', 'wellness')
		->sum('amount');

		return $amount;
	}

	public function createEclaimMedical( )
	{
		$employee = StringHelper::getEmployeeSession( );
		$admin_id = Session::get('admin-session-id');
		$input = Input::all();
		$check = DB::table('user')->where('UserID', $input['user_id'])->first( );

		if(!$check) {
			return array('status' => FALSE, 'message' => 'User does not exist.');
		}

		// check if their is receipts
		if(sizeof($input['receipts']) == 0) {
			return array('status' => FALSE, 'message' => 'E-Claim receipt is required.');
		}

		$ids = [];
        // get real userid for dependents
		$type = StringHelper::checkUserType($input['user_id']);
		if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
		{
			$user_id = $input['user_id'];
			$customer_id = $input['user_id'];
			$email_address = $check->Email;
			$ids[] = $user_id;
		} else {
            // find owner
			$owner = DB::table('employee_family_coverage_sub_accounts')
			->where('user_id', $input['user_id'])
			->first();
			$user_id = $owner->owner_id;
			$user_email = DB::table('user')->where('UserID', $user_id)->first();
			$email_address = $user_email->Email;
			$customer_id = $input['user_id'];
			$ids = [$user_id, $customer_id];
		}

        // check if employee plan is expired
		$check_plan = PlanHelper::checkEmployeePlanStatus($user_id);
		// $check_plan = PlanHelper::checkEmployeePlanStatus($employee->UserID);
		if($check_plan) {
			if($check_plan['expired'] == true) {
				return array('status' => FALSE, 'message' => 'Employee Plan has expired. You cannot submit an e-claim request.');
			}
		}

        // recalculate employee balance
		PlanHelper::reCalculateEmployeeBalance($user_id);

        // check if e-claim can proceed
		$check_user_balance = DB::table('e_wallet')->where('UserID', $user_id)->first();
        // return $check_user_balance->balance;
        $balance = round($check_user_balance->balance, 2);

		if($input['amount'] > $balance || $balance <= 0) {
			return array('status' => FALSE, 'message' => 'You have insufficient Benefits Credits for this transaction. Please check with your company HR for more details.');
		}

        // check user pending e-claims amount
		$claim_amounts = EclaimHelper::checkPendingEclaims($ids, 'medical');
		$total_claim_amount = $check_user_balance->balance - $claim_amounts;
		$amount = trim($input['amount']);
		$total_claim_amount = trim($total_claim_amount);

		if($amount > $total_claim_amount) {
			return array('status' => FALSE, 'message' => 'Sorry, we are not able to process your claim. You have a claim currently waiting for approval and might exceed your credits limit. You might want to check with your company’s benefits administrator for more information.', 'amount' => floatval($input['amount']), 'remaining_credits' => floatval($total_claim_amount));
		}
		
		$time = date('h:i A', strtotime($input['time']));
		$claim = new Eclaim();
		$data = array(
			'user_id'	=> $input['user_id'],
			'service'	=> $input['service'],
			'merchant'	=> $input['merchant'],
			'amount'	=> $amount,
			'date'		=> date('Y-m-d', strtotime($input['date'])),
			'approved_date' => null,
			'time'		=> $time,
			'spending_type' => 'medical'
		);

		try {
			$result = $claim->createEclaim($data);
			$id = $result->id;

			if($result) {
				$e_claim_docs = new EclaimDocs( );
				foreach ($input['receipts'] as $key => $doc) {
					$file = $doc['receipt_file'];
					$type = $doc['receipt_type'];
					$receipt = array(
						'e_claim_id'    => $id,
						'doc_file'      => $file,
						'file_type'     => $type
					);

					try {
						$e_claim_docs->createEclaimDocs($receipt);
						// if(StringHelper::Deployment()==1){
							if($doc['receipt_type'] != "image") {
                                //   aws
								$s3 = AWS::get('s3');
								$s3->putObject(array(
									'Bucket'     => 'mednefits',
									'Key'        => 'receipts/'.$file,
									'SourceFile' => storage_path().'/receipts/'.$file,
								));
								// unlink(storage_path().'/receipts/'.$file);
							}
						// }
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('employee/create/e_claim', $parameter = array(), $secure = null);
						$email['logs'] = 'E-Claim Submission Save Docs Medical - '.$e->getMessage();
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
					}

				}

                // get customer id
				$customer_id = StringHelper::getCustomerId($employee->UserID);

				if($customer_id) {
                    // send notification
					$user = DB::table('user')->where('UserID', $employee->UserID)->first();
					Notification::sendNotificationToHR('Employee E-Claim', 'Employee '.ucwords($user->Name).' created an E-Claim.', url('company-benefits-dashboard#/e-claim', $parameter = array(), $secure = null), $customer_id, 'https://www.medicloud.sg/assets/new_landing/images/favicon.ico');
					EclaimHelper::sendEclaimEmail($user_id, $id);
					$data['files'] = $input['receipts'];
					if($admin_id) {
						$admin_logs = array(
		                    'admin_id'  => $admin_id,
		                    'admin_type' => 'mednefits',
		                    'type'      => 'admin_employee_create_e_claim_details',
		                    'data'      => SystemLogLibrary::serializeData($data)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					} else {
						$admin_logs = array(
		                    'admin_id'  => $employee->UserID,
		                    'admin_type' => 'member',
		                    'type'      => 'admin_employee_create_e_claim_details',
		                    'data'      => SystemLogLibrary::serializeData($data)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					}
				}
				return array('status' => TRUE, 'message' => 'Success.', 'data' => $result);
			}
		} catch(Exception $e) {
            // send email logs
			$email = [];
			$email['end_point'] = url('employee/create/e_claim', $parameter = array(), $secure = null);
			$email['logs'] = 'E-Claim Submission - '.$e->getMessage();
			$email['emailSubject'] = 'Error log.';
			EmailHelper::sendErrorLogs($email);
			return array('status' => FALSE, 'message' => 'Error.', 'e' => $e->getMessage());
		}

		return array('status' => FALSE, 'message' => 'Error.');
	}

	public function createEclaimWellness( )
	{
		$employee = StringHelper::getEmployeeSession( );
		$admin_id = Session::get('admin-session-id');
		$input = Input::all();
		$check = DB::table('user')->where('UserID', $input['user_id'])->first( );

		if(!$check) {
			return array('status' => FALSE, 'message' => 'User does not exist.');
		}

		// check if their is receipts
		if(sizeof($input['receipts']) == 0) {
			return array('status' => FALSE, 'message' => 'E-Claim receipt is required.');
		}

		$ids = [];
        // get real userid for dependents
		$type = StringHelper::checkUserType($input['user_id']);
		if((int)$type['user_type'] == 5 && (int)$type['access_type'] == 0 || (int)$type['user_type'] == 5 && (int)$type['access_type'] == 1)
		{
			$user_id = $input['user_id'];
			$customer_id = $input['user_id'];
			$email_address = $check->Email;
			$ids[] = $user_id;
		} else {
            // find owner
			$owner = DB::table('employee_family_coverage_sub_accounts')
			->where('user_id', $input['user_id'])
			->first();
			$user_id = $owner->owner_id;
			$user_email = DB::table('user')->where('UserID', $user_id)->first();
			$email_address = $user_email->Email;
			$customer_id = $input['user_id'];
			$ids = [$user_id, $customer_id];
		}

        // check if employee plan is expired
		$check_plan = PlanHelper::checkEmployeePlanStatus($employee->UserID);

		if($check_plan) {
			if($check_plan['expired'] == true) {
				return array('status' => FALSE, 'message' => 'Employee Plan is expired. You cannot submit an e-claim request.');
			}
		}

		// recalculate employee balance
		PlanHelper::reCalculateEmployeeWellnessBalance($user_id);
        // check if e-claim can proceed
		$check_user_balance = DB::table('e_wallet')->where('UserID', $employee->UserID)->first();
		$balance = round($check_user_balance->wellness_balance, 2);
		
		if($input['amount'] > $balance || $balance <= 0) {
			return array('status' => FALSE, 'message' => 'You have insufficient Wellness Benefits Credits for this transaction. Please check with your company HR for more details.');
		}

         // check user pending e-claims amount
		$claim_amounts = EclaimHelper::checkPendingEclaims($ids, 'wellness');

		$total_claim_amount = $check_user_balance->wellness_balance - $claim_amounts;
        // return $total_claim_amount;
		if(floatval($input['amount']) > floatval($total_claim_amount)) {
			return array('status' => FALSE, 'message' => 'Sorry, we are not able to process your claim. You have a claim currently waiting for approval and might exceed your credits limit. You might want to check with your company’s benefits administrator for more information.');
		}

        // return $total_claim_amount;

		$time = date('h:i A', strtotime($input['time']));
		$claim = new Eclaim();
		$data = array(
			'user_id'   => $input['user_id'],
			'service'   => $input['service'],
			'merchant'  => $input['merchant'],
			'amount'    => $input['amount'],
			'date'      => date('Y-m-d', strtotime($input['date'])),
			'time'      => $time,
			'spending_type' => 'wellness'
		);

		try {
			$result = $claim->createEclaim($data);
			$id = $result->id;


			if($result) {
				$e_claim_docs = new EclaimDocs( );
				foreach ($input['receipts'] as $key => $doc) {
					$file = $doc['receipt_file'];
					$type = $doc['receipt_type'];
					$receipt = array(
						'e_claim_id'    => $id,
						'doc_file'      => $file,
						'file_type'     => $type
					);

					try {
						$e_claim_docs->createEclaimDocs($receipt);
						// if(StringHelper::Deployment()==1){
							if($doc['receipt_type'] != "image" || $doc['receipt_type'] !== "image") {
                                //   aws
								$s3 = AWS::get('s3');
								$s3->putObject(array(
									'Bucket'     => 'mednefits',
									'Key'        => 'receipts/'.$file,
									'SourceFile' => storage_path().'/receipts/'.$file,
								));
							}
						// }
					} catch(Exception $e) {
						$email = [];
						$email['end_point'] = url('employee/create/e_claim', $parameter = array(), $secure = null);
						$email['logs'] = 'E-Claim Wellness Submission Save Docs- '.$e;
						$email['emailSubject'] = 'Error log.';
						EmailHelper::sendErrorLogs($email);
					}

				}

                // get customer id
				$customer_id = StringHelper::getCustomerId($employee->UserID);

				if($customer_id) {
                    // send notification
					$user = DB::table('user')->where('UserID', $employee->UserID)->first();
					Notification::sendNotificationToHR('Employee E-Claim Wellness', 'Employee '.ucwords($user->Name).' created an E-Claim.', url('company-benefits-dashboard#/e-claim', $parameter = array(), $secure = null), $customer_id, 'https://www.medicloud.sg/assets/new_landing/images/favicon.ico');
					EclaimHelper::sendEclaimEmail($user_id, $id);
					$data['files'] = $input['receipts'];
					if($admin_id) {
						$admin_logs = array(
		                    'admin_id'  => $admin_id,
		                    'admin_type' => 'mednefits',
		                    'type'      => 'admin_employee_create_e_claim_details',
		                    'data'      => SystemLogLibrary::serializeData($data)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					} else {
						$admin_logs = array(
		                    'admin_id'  => $employee->UserID,
		                    'admin_type' => 'member',
		                    'type'      => 'admin_employee_create_e_claim_details',
		                    'data'      => SystemLogLibrary::serializeData($data)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					}
				}
				return array('status' => TRUE, 'message' => 'Success.', 'data' => $result);
			}
		} catch(Exception $e) {
            // send email logs
			$email = [];
			$email['end_point'] = url('employee/create/e_claim', $parameter = array(), $secure = null);
			$email['logs'] = 'E-Claim Submission Wellness - '.$e->getMessage();
			$email['emailSubject'] = 'Error log.';
			// send
			EmailHelper::sendErrorLogs($email);
			return array('status' => FALSE, 'message' => 'Error.', 'e' => $e->getMessage());
		}

		return array('status' => FALSE, 'message' => 'Error.');
	}

	public function createEclaimReceipt( )
	{
		$input = Input::all();
		$receipt_all = [];

		if(Input::file('file')) {
			$file_types = ["jpeg","jpg","png","pdf","xls","xlsx","PNG"];
			// $rules = array(
   //              'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx',
   //          );

   //        $validator = Validator::make(Input::all(), $rules);

   //        if($validator->fails()) {
   //          return array('status' => FALSE, 'message' => 'Invalid file. Only accepts Image, PDF and Excel.');
   //        }

			$file = $input['file'];
			$result_type = in_array($file->getClientOriginalExtension(), $file_types);

			if(!$result_type) {
				return array('status' => FALSE, 'message' => 'Invalid file. Only accepts Image, PDF and Excel.');
			}

			$file_folder = 'receipts';
			$file_name = time().' - '.str_random(30).'.'.$file->getClientOriginalExtension();
			$file_size = $file->getSize();

			// check file size if exceeds 10 mb
			if($file_size > 10000000) {
				return array('status' => false, 'message' => 'File must be 10mb.');
			}

			if($file->getClientOriginalExtension() == "pdf") {
				$receipt = array(
					'receipt_file'	=> $file_name,
					'receipt_type'	=> "pdf"
				);
                // upload to folder
				$file->move(storage_path().'/receipts/', $file_name);
				// check file
				$check_file = FileHelper::checkFile($file_name);
				if(!$check_file) {
					return array('status' => false, 'message' => 'File is damage or corrupt. Please make sure the file you are trying to upload is not damage or corrupted.');
				}
			} else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
				$receipt = array(
					'receipt_file'  => $file_name,
					'receipt_type'  => "xls"
				);
                // upload to folder
				$file->move(storage_path().'/receipts/', $file_name);
			} else {
				$image = \Cloudinary\Uploader::upload($file->getPathName());
				$receipt = array(
					'receipt_file'	=> $image['secure_url'],
					'receipt_type'	=> "image"
				);
			}

			return array('status' => TRUE, 'receipt' => $receipt);
		} else {
			return array('status' => FALSE, 'message' => 'Please select a file.');
		}
	}

	public function saveEclaim( )
	{
		$input = Input::all();
		$admin_id = Session::get('admin-session-id');
		$employee = StringHelper::getEmployeeSession( );
		$receipt_all = [];

		if(empty($input['e_claim_id']) || $input['e_claim_id'] == null) {
			return array('status' => false, 'message' => 'E-Claim ID is required.');
		}

		$id = $transaction_id = (int)preg_replace('/[^0-9]/', '', $input['e_claim_id']);
		$check = DB::table('e_claim')->where('e_claim_id', $id)->first();

		if(!$check) {
			return array('status' => FALSE, 'message' => 'E-Claim data does not exist.');
		}

		if(Input::file('file')) {
			$rules = array(
				'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx',
			);

			$validator = Validator::make(Input::all(), $rules);

			if($validator->fails()) {
				return array('status' => FALSE, 'message' => 'Invalid file. Only accepts Image and PDF.');
			}

			$file = $input['file'];
			$file_name = time().' - '.$file->getClientOriginalName();
			$s3 = AWS::get('s3');

			if($file->getClientOriginalExtension() == "pdf") {
				$receipt_file = $file_name;
				$receipt_type = "pdf";
				$file->move(storage_path().'/receipts/', $file_name);
				$s3->putObject(array(
					'Bucket'     => 'mednefits',
					'Key'        => 'receipts/'.$file_name,
					'SourceFile' => storage_path().'/receipts/'.$file_name,
				));
			} else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
				$receipt_file = $file_name;
				$receipt_type = "xls";
				$file->move(storage_path().'/receipts/', $file_name);
				$s3->putObject(array(
					'Bucket'     => 'mednefits',
					'Key'        => 'receipts/'.$file_name,
					'SourceFile' => storage_path().'/receipts/'.$file_name,
				));
			} else {
				$image = \Cloudinary\Uploader::upload($file->getPathName());
				$receipt_file = $image['secure_url'];
				$receipt_type = "image";
			}

			$e_claim_docs = new EclaimDocs( );
			$receipt = array(
				'e_claim_id'    => $id,
				'doc_file'      => $receipt_file,
				'file_type'     => $receipt_type
			);

			$result = $e_claim_docs->createEclaimDocs($receipt);

			if($result) {
				if($file->getClientOriginalExtension() == "pdf" || $file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
					$result->doc_file = EclaimHelper::createPreSignedUrl($file_name);
					unlink(storage_path().'/receipts/'.$file_name);
				}
				$receipt['user_id'] = $check->user_id;
				if($admin_id) {
					$admin_logs = array(
	                    'admin_id'  => $admin_id,
	                    'admin_type' => 'mednefits',
	                    'type'      => 'admin_employee_uploaded_out_of_network_receipt',
	                    'data'      => SystemLogLibrary::serializeData($receipt)
	                );
	                SystemLogLibrary::createAdminLog($admin_logs);
				} else {
					$admin_logs = array(
	                    'admin_id'  => $employee->UserID,
	                    'admin_type' => 'member',
	                    'type'      => 'admin_employee_uploaded_out_of_network_receipt',
	                    'data'      => SystemLogLibrary::serializeData($receipt)
	                );
	                SystemLogLibrary::createAdminLog($admin_logs);
				}
				return array('status' => TRUE, 'receipt' => $result);
			} else {
				return array('status' => FALSE, 'message' => 'Failed to save e-claim receipt.');
			}
		} else {
			return array('status' => FALSE, 'message' => 'Please select a file.');
		}
	}

	public function getEclaims( )
	{
		$final_data = [];
		$result = DB::table('e_claim')
							// ->join('e_claim', 'e_claim.user_id', '=', 'user.UserID')
							// ->join('e_claim_docs', 'e_claim_docs.e_claim_id', '=', 'e_claim.e_claim_id')
		->where('e_claim.user_id', Session::get('employee-session'))
		->get();

		if($result) {
			// foreach ($result as $key => $value) {
			// 	$temp = array(
			// 		'e_claim'	=> $value,
			// 		'docs'		=> DB::table('e_claim_docs')->where('e_claim_id', $value->e_claim_id)->get()
			// 	);
			// 	array_push($final_data, $temp);
			// }
			return array('status' => TRUE, 'message' => 'Success.', 'data' => $result);
		}

		return array('status' => FALSE, 'message' => 'No Eclaim data.');
	}

	public function getEclaimDetails($id)
	{
		// return $id;
		$result = DB::table('e_claim')->where('e_claim_id', $id)->first();

		if($result) {
			$final_data = array(
				'e_claim'	=> $result,
				'docs'		=> DB::table('e_claim_docs')->where('e_claim_id', $result->e_claim_id)->get()
			);

			return array('status' => TRUE, 'message' => 'Success.', 'data' => $final_data);
		}

		return array('status' => FALSE, 'message' => 'No Eclaim data.');
	}

	public function getUserData( )
	{
		$data = StringHelper::getEmployeeSession( );
    $user = PlanHelper::checkEmployeePlanStatus($data->UserID);

		return array('status' => TRUE, 'data' => $user);
	}

	// upload image for preview
	public function uploadImage( )
	{
		$uploadFile = \Cloudinary\Uploader::upload(Input::file('file'));
		return $uploadFile['secure_url'];
	}

	public function logout( )
	{
		Session::flush();
	}

	public function updateProfile( )
	{
		$input = Input::all();

		if(Input::file('file')) {
			// return "has file";
			$rules = array(
				'file' => 'required|mimes:jpeg,jpg,png'
			);

			$validator = \Validator::make( Input::all() , $rules);

			if($validator->passes()){
				$uploadFile = \Cloudinary\Uploader::upload(Input::file('file'));
				$image = $uploadFile['secure_url'];
			} else {
				return array('status' => FALSE, 'mesage' => 'Invalid Image.');
			}
    	// return $image;
			$data = array(
				'Name'				=> $input['name'],
				'NRIC'				=> $input['nric'],
				'PhoneNo'			=> $input['phone_no'],
				'PhoneCode'		=> $input['phone_code'],
				'DOB'					=> $input['dob'],
				'Address'			=> $input['address'],
				'Country'			=> $input['country'],
				'City'				=> $input['city'],
				'State'				=> $input['state'],
				'Image'				=> $image,
				'userid'			=> Session::get('employee-session')
			);
		} else {
			// return "no file";
			$data = array(
				'Name'				=> $input['name'],
				'NRIC'				=> $input['nric'],
				'PhoneNo'			=> $input['phone_no'],
				'PhoneCode'		=> $input['phone_code'],
				'DOB'					=> $input['dob'],
				'Address'			=> $input['address'],
				'Country'			=> $input['country'],
				'City'				=> $input['city'],
				'State'				=> $input['state'],
				'userid'			=> Session::get('employee-session')
			);
		}

		$user = new User();
		$result = $user->updateUserProfile($data);

		if($result) {
			return array('status' => TRUE);
		}

		return array('status' => FALSE, 'mesage' => 'Error updating user data.');
	}

	public function getUserCoverage( )
	{
		$result = DB::table('user_package')
		->join('care_package', 'care_package.care_package_id', '=', 'user_package.care_package_id')
		->where('user_package.user_id', Session::get('employee-session'))->get();

		return $result;
	}

	public function getActivity( )
	{
		$input = Input::all();
		$user_id = Session::get('employee-session');
		$start = date('Y-m-d', strtotime($input['start']));
		$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
		$lite_plan_status = false;
		$end = PlanHelper::endDate($input['end']);

		$e_claim = [];
		$transaction_details = [];
		$total_in_network_transactions = 0;
		$total_deleted_in_network_transactions = 0;
		$total_in_network_spent = 0;

		$in_network_spent = 0;
		$e_claim_spent = 0;
		$balance = 0;

		$total_credits = 0;
		$total_cash = 0;
		$total_lite_plan_consultation = 0;
		$total_employee_lite_plan_spent = 0;
		$wallet_status = false;
		$lite_plan_status = StringHelper::litePlanStatus($user_id);

		$company_wallet_status = PlanHelper::getCompanyAccountType($user_id);
		if($company_wallet_status) {
			if($company_wallet_status == "Health Wallet") {
				$wallet_status = true;
			}
		}

		if($spending_type == 'medical') {
			$table_wallet_history = 'wallet_history';
			$history_column_id = "wallet_history_id";
		} else {
			$table_wallet_history = 'wellness_wallet_history';
			$history_column_id = "wellness_wallet_history_id";
		}

		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

		$wallet_reset = PlanHelper::getResetWallet($user_id, $spending_type, $start, $input['end'], 'employee');

		$spending_end_date = PlanHelper::endDate($input['end']);
		if($wallet_reset) {
			$wallet_history_id = $wallet_reset->wallet_history_id;
			$wallet_start_date = $wallet_reset->date_resetted;
	        // get credits allocation
			$allocation = DB::table('e_wallet')
			->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			// ->where($table_wallet_history.".".$history_column_id, '>=', $wallet_history_id)
			->where($table_wallet_history.".created_at", '>=', $wallet_start_date)
			->where($table_wallet_history.'.logs', 'added_by_hr')
			->sum($table_wallet_history.'.credit');

			$deducted_allocation = DB::table('e_wallet')
			->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
			// ->where($table_wallet_history.".".$history_column_id, '>=', $wallet_history_id)
			->where($table_wallet_history.".created_at", '>=', $wallet_start_date)
			->where('e_wallet.UserID', $user_id)
			->where('logs', 'deducted_by_hr')
			->sum($table_wallet_history.'.credit');
		} else {
			$allocation = DB::table('e_wallet')
			->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->where($table_wallet_history.'.logs', 'added_by_hr')
			->sum($table_wallet_history.'.credit');

			$deducted_allocation = DB::table('e_wallet')
			->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->where('logs', 'deducted_by_hr')
			->sum($table_wallet_history.'.credit');
		}

		$e_claim_total = DB::table($table_wallet_history)
		->where('wallet_id', $wallet->wallet_id)
		->where('where_spend', 'e_claim_transaction')
		->sum('credit');

		$ids = StringHelper::getSubAccountsID($user_id);

        // get e claim
		$e_claim_result = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $spending_end_date)
		->orderBy('created_at', 'desc')
		->get();
        // get in-network transactions
		$transactions = DB::table('transaction_history')
		->whereIn('UserID', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $spending_end_date)
		->where('paid', 1)
		->orderBy('created_at', 'desc')
		->get();
		foreach ($transactions as $key => $trans) {
			if($trans) {
				$consultation_fees = $trans->consultation_fees;
				$consultation_cash = false;
				$consultation_credits = false;
				$service_cash = false;
				$service_credits = false;

				$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
				$procedure_temp = "";
				$procedure = "";

            // if($trans->procedure_cost >= 0) {

				if((int)$trans->deleted == 0) {
					$in_network_spent += floatval($trans->credit_cost);
					$total_in_network_transactions++;

					if((int)$trans->lite_plan_enabled == 1) {
						$logs_lite_plan = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('lite_plan_enabled', 1)
						->where('id', $trans->transaction_id)
						->first();

						if($logs_lite_plan && $trans->credit_cost > 0 && (int)$trans->lite_plan_use_credits == 0 || $logs_lite_plan && $trans->credit_cost == 0 && (int)$trans->lite_plan_use_credits == 0) {
							$in_network_spent += floatval($logs_lite_plan->credit);
							$consultation_fees = floatval($logs_lite_plan->credit);
							$total_lite_plan_consultation += floatval($logs_lite_plan->credit);
							$consultation_credits = true;
							$service_credits = true;
						} else if($logs_lite_plan && $trans->procedure_cost >= 0 && (int)$trans->lite_plan_use_credits == 1){
							$in_network_spent += floatval($logs_lite_plan->credit);
							$consultation_fees = floatval($logs_lite_plan->credit);
							$total_lite_plan_consultation += floatval($logs_lite_plan->credit);
							$consultation_credits = true;
							$service_credits = true;
						} else {
							$consultation_fees = floatval($trans->consultation_fees);
							$total_lite_plan_consultation += floatval($trans->consultation_fees);
						}
					}
				} else {
					$total_deleted_in_network_transactions++;
					if((int)$trans->lite_plan_enabled == 1) {
						$logs_lite_plan = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('lite_plan_enabled', 1)
						->where('id', $trans->transaction_id)
						->first();

						if($logs_lite_plan && $trans->credit_cost > 0 && (int)$trans->lite_plan_use_credits == 0 || $logs_lite_plan && $trans->credit_cost == 0 && (int)$trans->lite_plan_use_credits == 0) {
							$consultation_credits = true;
							$service_credits = true;
						} else if($trans->procedure_cost >= 0 && (int)$trans->lite_plan_use_credits == 1){
							$consultation_credits = true;
							$service_credits = true;
						}
					}
				}

                // get services
				if((int)$trans->multiple_service_selection == 1)
				{
                    // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).',';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						$procedure = ucwords($service_lists->Name);
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
                        // $procedure = "";
						$clinic_name = ucwords($clinic_type->Name);
					}
				}

                // check if there is a receipt image
                // $receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();
                // $receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();

				if(sizeof($receipt_images) > 0) {
					$receipt_status = TRUE;
					$transaction_files = [];
					foreach ($receipt_images as $key => $image) {
						if($image->type == "pdf" || $image->type == "excel") {
							$fil = url('').'/receipts/'.$image->file;
						} else if($image->type == "image") {
							// $fil = $image->file;
							$fil = FileHelper::formatImageAutoQualityCustomer($image->file, 40);
						}

						$temp_doc = array(
							'image_receipt_id'  => $image->image_receipt_id,
							'transaction_details'    => $image->transaction_id,
							'user_id'           => $image->user_id,
							'file'              => $fil,
							'file_type'         => $image->type
						);

						array_push($transaction_files, $temp_doc);
					}
				} else {
					$receipt_status = FALSE;
					$transaction_files = FALSE;
				}
				$refund_text = 'NO';

				if((int)$trans->refunded == 1 && (int)$trans->deleted == 1) {
					$status_text = 'REFUNDED';
					$refund_text = 'YES';
				} else if((int)$trans->health_provider_done == 1 && (int)$trans->deleted == 1) {
					$status_text = 'REMOVED';
					$refund_text = 'YES';
				} else {
					$status_text = FALSE;
				}

				$total_amount = number_format($trans->procedure_cost, 2);

				if((int)$trans->health_provider_done == 1 && (int)$trans->deleted == 0) {
					if((int)$trans->lite_plan_enabled == 1) {
						$total_in_network_spent += $trans->procedure_cost + $trans->consultation_fees;
					} else {
						$total_in_network_spent += $trans->procedure_cost;
					}
					$total_cash += $trans->procedure_cost;
				} else if($trans->credit_cost > 0 && (int)$trans->deleted == 0) {
					if((int)$trans->lite_plan_enabled == 1) {
						$total_in_network_spent += $trans->credit_cost + $trans->consultation_fees;
					} else {
						$total_in_network_spent += $trans->credit_cost;
					}
					$total_credits += $trans->credit_cost;
				}

				$half_credits = false;

				if((int)$trans->health_provider_done == 1) {
					$receipt_status = TRUE;
					$health_provider_status = TRUE;
					$transaction_type = "cash";
					$payment_type = "Cash";
					// $cash = number_format($trans->procedure_cost);
					$credit_status = FALSE;
					if((int)$trans->lite_plan_enabled == 1) {
            if((int)$trans->half_credits == 1) {
              $total_amount = $trans->credit_cost + $trans->consultation_fees;
              $cash = $transation->cash_cost;
            } else {
              $total_amount = $trans->procedure_cost + $trans->consultation_fees;
              // $total_amount = $trans->procedure_cost;
              $cash = $trans->procedure_cost;
            }
          } else {
            if((int)$trans->half_credits == 1) {
              $cash = $trans->cash_cost;
            } else {
              $cash = $trans->procedure_cost;
            }
          }
				} else {
					$health_provider_status = FALSE;
					$credit_status = TRUE;
					$transaction_type = "credits";
					// $cash = number_format($trans->credit_cost, 2);

					if($trans->credit_cost > 0 && $trans->cash_cost > 0) {
				      $half_credits = true;
				    } else {
				    }

					// if((int)$trans->lite_plan_enabled == 1 && $wallet_status == true) {
					// 	$total_amount = number_format($trans->credit_cost + $trans->consultation_fees, 2);
					// }
					if((int)$trans->lite_plan_enabled == 1) {
	            if((int)$trans->half_credits == 1) {
	              $total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
	              $cash = $trans->cash_cost;
	              $payment_type = 'Mednefits Credits + Cash';
	            } else {
	              $total_amount = $trans->credit_cost + $trans->consultation_fees;
	              // $total_amount = $trans->procedure_cost;
	              if($trans->credit_cost > 0) {
	                $cash = 0;
	                $payment_type = 'Mednefits Credits';
	              } else {
	                $cash = $trans->procedure_cost - $trans->consultation_fees;
	              }
	            }
	        } else {
	            $total_amount = $trans->procedure_cost;
	            if((int)$trans->half_credits == 1) {
	              $cash = $trans->cash_cost;
	            } else {
	              if($trans->credit_cost > 0) {
	                $cash = 0;
	              } else {
	                $cash = $trans->procedure_cost;
	              }
	            }
	            $payment_type = 'Mednefits Credits';
	        }
				}

				$bill_amount = 0;
				if((int)$trans->half_credits == 1) {
					if((int)$trans->lite_plan_enabled == 1) {
						$bill_amount = $trans->procedure_cost - $trans->consultation_fees;
					} else {
						$bill_amount = 	$trans->procedure_cost;
					}
				} else {
					if((int)$trans->lite_plan_enabled == 1) {
						if((int)$trans->health_provider_done == 1) {
							if((int)$trans->lite_plan_use_credits == 1) {
								$bill_amount = 	$trans->procedure_cost;
							} else {
								$bill_amount = 	$trans->procedure_cost;
							}
						} else {
							if((int)$trans->lite_plan_use_credits == 1) {
								$bill_amount = 	$trans->procedure_cost;
							} else {
								// $cost_temp = $trans->credit_cost + $trans->cash_cost;
								$bill_amount = 	$trans->credit_cost + $trans->cash_cost;
							}
						}
					} else {
						$bill_amount = 	$trans->procedure_cost;
					}
				}


                // get clinic type
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$type = "";
				$clinic_type_name = "";
				$image = "";
				if((int)$clinic_type->head == 1 || $clinic_type->head == "1") {
					if($clinic_type->Name == "General Practitioner") {
						$type = "general_practitioner";
						$clinic_type_name = "General Practitioner";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
					} else if($clinic_type->Name == "Dental Care") {
						$type = "dental_care";
						$clinic_type_name = "Dental Care";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
					} else if($clinic_type->Name == "Traditional Chinese Medicine") {
						$type = "tcm";
						$clinic_type_name = "Traditional Chinese Medicine";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
					} else if($clinic_type->Name == "Health Screening") {
						$type = "health_screening";
						$clinic_type_name = "Health Screening";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
					} else if($clinic_type->Name == "Wellness") {
						$type = "wellness";
						$clinic_type_name = "Wellness";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
					} else if($clinic_type->Name == "Health Specialist") {
						$type = "health_specialist";
						$clinic_type_name = "Health Specialist";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
					}
				} else {
					$find_head = DB::table('clinic_types')
					->where('ClinicTypeID', $clinic_type->sub_id)
					->first();
					if($find_head->Name == "General Practitioner") {
						$type = "general_practitioner";
						$clinic_type_name = "General Practitioner";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
					} else if($find_head->Name == "Dental Care") {
						$type = "dental_care";
						$clinic_type_name = "Dental Care";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
					} else if($find_head->Name == "Traditional Chinese Medicine") {
						$type = "tcm";
						$clinic_type_name = "Traditional Chinese Medicine";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
					} else if($find_head->Name == "Health Screening") {
						$type = "health_screening";
						$clinic_type_name = "Health Screening";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
					} else if($find_head->Name == "Wellness") {
						$type = "wellness";
						$clinic_type_name = "Wellness";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
					} else if($find_head->Name == "Health Specialist") {
						$type = "health_specialist";
						$clinic_type_name = "Health Specialist";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
					}
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

				$format = array(
					'clinic_name'       => $clinic->Name,
					'clinic_image'      => $clinic->image,
					'clinic_type'       => $type,
					'amount'            => number_format($total_amount, 2),
					'procedure_cost'    => number_format($bill_amount, 2),
					'procedure'         => $procedure,
					'clinic_type_and_service' => $clinic_name,
					'clinic_type_name'  => $clinic_type_name,
					'clinic_type_image' => $image,
					'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
					'member'          => ucwords($customer->Name),
					'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'trans_id'          => $trans->transaction_id,
					'transaction_files' => $transaction_files,
					'receipt_status'    => $receipt_status,
					'cash_status'       => $health_provider_status,
					'credit_status'     => $credit_status,
					'user_id'           => $trans->UserID,
					'type'				=> 'In-Network',
					'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
					'refund_text'       => $refund_text,
					'cash'              => $cash,
					'payment_type'      => $payment_type,
					'status_text'       => $status_text,
					'consultation'      => (int) $trans->lite_plan_enabled == 1 ? number_format($consultation_fees, 2) : "0.00",
					'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
					'consultation_credits' => $consultation_credits,
					'service_credits'   => $service_credits,
					'transaction_type'  => $transaction_type,
					'cap_transaction'   => $half_credits,
			    'cap_per_visit'     => number_format($trans->cap_per_visit, 2),
			    'paid_by_cash'      => number_format($trans->cash_cost, 2),
			    'paid_by_credits'   => number_format($trans->credit_cost, 2),
			    "currency_symbol" 	=> $trans->currency_type == "myr" ? "RM" : "S$"
				);

				array_push($transaction_details, $format);
            // }
			}
		}


		foreach($e_claim_result as $key => $res) {
			if($res->status == 0) {
				$status_text = 'Pending';
			} else if($res->status == 1) {
				$status_text = 'Approved';
				$e_claim_spent += $res->amount;
			} else if($res->status == 2) {
				$status_text = 'Rejected';
			} else {
				$status_text = 'Pending';
			}

			$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
        	// get docs
			$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

			if(sizeof($docs) > 0) {
				$e_claim_receipt_status = TRUE;
				$doc_files = [];
				foreach ($docs as $key => $doc) {
					if($doc->file_type == "pdf" || $doc->file_type == "xls" || $doc->file_type == "xlsx") {
						// $fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->doc_file;
						$fil = EclaimHelper::createPreSignedUrl($doc->doc_file);
					} else if($doc->file_type == "image") {
						$fil = $doc->doc_file;
					}

					$temp_doc = array(
						'e_claim_doc_id'	=> $doc->e_claim_doc_id,
						'e_claim_id'			=> $doc->e_claim_id,
						'file'						=> $fil,
						'file_type'				=> $doc->file_type
					);

					array_push($doc_files, $temp_doc);
				}
			} else {
				$e_claim_receipt_status = FALSE;
				$doc_files = FALSE;
			}

			$member = DB::table('user')->where('UserID', $res->user_id)->first();

			$temp = array(
				'status'			=> $res->status,
				'status_text'	=> $status_text,
				'claim_date'	=> date('d F Y h:i A', strtotime($res->created_at)),
				'visit_date'    => date('d F Y', strtotime($res->date)).' '.$res->time,
				'time'				=> $res->time,
				'service'			=> $res->service,
				'merchant'		=> $res->merchant,
				'amount'			=> $res->amount,
				'member'			=> ucwords($member->Name),
				'type'				=> 'E-Claim',
				'transaction_id' => 'MNF'.$id,
				'files'				=> $doc_files,
        		// 'visit_date'	=> date('d F Y', strtotime($res->date)).', '.$res->time,
				'receipt_status' => $e_claim_receipt_status,
				'rejected_reason'   => $res->rejected_reason,
				'rejected_date'     => date('d F Y h:i A', strtotime($res->updated_at)),
				'spending_type'     => $res->spending_type == 'medical' ? 'Medical' : 'Wellness',
				'approved_date'     => date('d F Y h:i A', strtotime($res->updated_at)),
				'remarks'			=> $res->rejected_reason
			);

			array_push($e_claim, $temp);
		}


		$total_spent = $in_network_spent + $e_claim_spent;
		// $balance = $total_allocation - $deducted_allocation - $total_spent;

		$pro_allocation = DB::table($table_wallet_history)
			->where('wallet_id', $wallet->wallet_id)
			->where('logs', 'pro_allocation')
			->sum('credit');


		if($pro_allocation > 0) {
			$final_allocation = $pro_allocation;
			$balance = $pro_allocation - $total_spent;
			if($balance < 0) {
				$balance = 0;
			}
		} else {
			$final_allocation = $allocation - $deducted_allocation;
			$balance = $final_allocation - $total_spent;
		}

		return array(
			'status' 				   => TRUE,
			'e_claim' 				   => $e_claim,
			'in_network_transactions'  => $transaction_details,
			'in_network_spent'	       => number_format($in_network_spent, 2),
			'e_claim_spent'			   => number_format($e_claim_spent, 2),
			'total_allocation'		   => number_format($final_allocation, 2),
			'total_spent'			   => number_format($total_spent, 2),
			'balance'				   => $balance >= 0 ? number_format($balance, 2) : "0.00",
			'total_in_network_transactions' => $total_in_network_transactions,
			'total_deleted_in_network_transactions' => $total_deleted_in_network_transactions,
			'total_in_network_spent'    => number_format($total_in_network_spent, 2),
			'total_cash'            => $total_cash,
			'total_credits'         => $total_credits + $e_claim_spent,
			'spending_type'         => $spending_type == 'medical' ? 'Medical' : 'Wellness',
			'total_consultation'    => $total_lite_plan_consultation,
			'total_employee_lite_plan_spent'    => number_format($total_employee_lite_plan_spent, 2),
			'lite_plan'             => $lite_plan_status,
			'wallet_status'        => $wallet_status
		);
	}

	public function getHealthPartnerLists( )
	{
		$input = Input::all();
		return DB::table('health_types')->where('type', $input['type'])->get();
	}

	public function getWellnessActivity( )
	{
		$input = Input::all();
		$user_id = Session::get('employee-session');
        // $user_id = $input['user_id'];
		$start = date('Y-m-d', strtotime($input['start']));
        // $temp_end = date('Y-m-d', strtotime($input['end']));

		$end = date('Y-m-d', strtotime('+1 day', strtotime($input['end'])));
		$trans_end = date('Y-m-d H:i:s', strtotime('+22 hours', strtotime($input['end'])));

		$e_claim = [];
		$transaction_details = [];
		$total_in_network_transactions = 0;
		$total_deleted_in_network_transactions = 0;
		$total_in_network_spent = 0;

		$in_network_spent = 0;
		$e_claim_spent = 0;
		$balance = 0;

		$total_credits = 0;
		$total_cash = 0;

		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

        // get credits allocation
		$allocation = DB::table('e_wallet')
		->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
		->where('e_wallet.UserID', $user_id)
		->whereYear('wellness_wallet_history.created_at', '>=', date('Y', strtotime($start)))
		->whereYear('wellness_wallet_history.created_at', '<=', date('Y', strtotime($start)))
		->where('wellness_wallet_history.logs', 'added_by_hr')
                        // ->get();
		->sum('wellness_wallet_history.credit');

		$deducted_allocation = DB::table('e_wallet')
		->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
		->where('e_wallet.UserID', $user_id)
		->where('logs', 'deducted_by_hr')
		->sum('wellness_wallet_history.credit');
        // $allocation = $temp_allocation - $deducted_allocation;

		$total_allocation = DB::table('e_wallet')
		->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
		->where('e_wallet.UserID', $user_id)
		->where('wellness_wallet_history.logs', 'added_by_hr')
                        // ->get();
		->sum('wellness_wallet_history.credit');

		$e_claim_total = DB::table('wellness_wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('where_spend', 'e_claim_transaction')
		->sum('credit');

		$in_network_total = DB::table('wellness_wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('where_spend', 'in_network_transaction')
		->sum('credit');

		$credits_back_total = DB::table('wellness_wallet_history')
		->where('wallet_id', $wallet->wallet_id)
		->where('where_spend', 'credits_back_from_in_network')
		->sum('credit');

		$in_network_num = $in_network_total - $credits_back_total;

		$balance = $total_allocation - $in_network_num - $e_claim_total - $deducted_allocation;

		$ids = StringHelper::getSubAccountsID($user_id);

        // get e claim
		$e_claim_result = DB::table('e_claim')
		->where('spending_type', 'wellness')
		->whereIn('user_id', $ids)
		->where('date', '>=', $start)
		->where('date', '<=', $end)
		->orderBy('created_at', 'desc')
		->get();

        // get in-network transactions
		$transactions = DB::table('transaction_history')
		->where('spending_type', 'wellness')
		->whereIn('UserID', $ids)
		->where('date_of_transaction', '>=', $start)
		->where('date_of_transaction', '<=', $trans_end)
		->orderBy('created_at', 'desc')
		->get();

		foreach ($transactions as $key => $trans) {
			if($trans) {
				$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
				$procedure_temp = "";

				if($trans->procedure_cost > 0) {

					if($trans->deleted == 0 || $trans->deleted == "0") {
						$in_network_spent += $trans->credit_cost;
						$total_in_network_transactions++;
					} else {
						$total_deleted_in_network_transactions++;
					}

                // get services
					if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
					{
                    // get multiple service
						$service_lists = DB::table('transaction_services')
						->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
						->where('transaction_services.transaction_id', $trans->transaction_id)
						->get();

						foreach ($service_lists as $key => $service) {
							if(sizeof($service_lists) - 2 == $key) {
								$procedure_temp .= ucwords($service->Name).' and ';
							} else {
								$procedure_temp .= ucwords($service->Name).',';
							}
							$procedure = rtrim($procedure_temp, ',');
						}
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
						$service_lists = DB::table('clinic_procedure')
						->where('ProcedureID', $trans->ProcedureID)
						->first();
						if($service_lists) {
							$procedure = ucwords($service_lists->Name);
							$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
						} else {
                        // $procedure = "";
							$clinic_name = ucwords($clinic_type->Name);
						}
					}

					if(sizeof($receipt_images) > 0) {
						$receipt_status = TRUE;
						$transaction_files = [];
						foreach ($receipt_images as $key => $image) {
							if($image->type == "pdf" || $image->type == "excel") {
								$fil = url('').'/receipts/'.$image->file;
							} else if($image->type == "image") {
								$fil = $image->file;
							}

							$temp_doc = array(
								'image_receipt_id'  => $image->image_receipt_id,
								'transaction_details'    => $image->transaction_id,
								'user_id'           => $image->user_id,
								'file'              => $fil,
								'file_type'         => $image->type
							);

							array_push($transaction_files, $temp_doc);
						}
					} else {
						$receipt_status = FALSE;
						$transaction_files = FALSE;
					}
					$refund_text = 'NO';

					if($trans->refunded == 1 && $trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
						$status_text = 'REFUNDED';
						$refund_text = 'YES';
					} else if($trans->health_provider_done == 1 && $trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
						$status_text = 'REMOVED';
						$refund_text = 'YES';
					} else {
						$status_text = FALSE;
					}

					if( $trans->health_provider_done == 1 && $trans->deleted == 0 || $trans->health_provider_done == "1" && $trans->deleted == "0" ) {
						$total_in_network_spent += $trans->procedure_cost;
						$total_cash += $trans->procedure_cost;
					} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
						$total_in_network_spent += $trans->credit_cost;
						$total_credits += $trans->credit_cost;
					}


					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
						$receipt_status = TRUE;
						$health_provider_status = TRUE;
						$transaction_type = "Health Provider";
						$payment_type = "Cash";
						$cash = number_format($trans->procedure_cost);
						$credit_status = FALSE;
					} else {
						$health_provider_status = FALSE;
						$credit_status = TRUE;
						$transaction_type = "In-Network";
						$payment_type = "Mednefits Credits";
						$cash = number_format($trans->credit_cost, 2);
					}

                // get clinic type
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$type = "";
					if($clinic_type->head == 1 || $clinic_type->head == "1") {
						if($clinic_type->Name == "General Practitioner") {
							$type = "general_practitioner";
						} else if($clinic_type->Name == "Dental Care") {
							$type = "dental_care";
						} else if($clinic_type->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
						} else if($clinic_type->Name == "Health Screening") {
							$type = "health_screening";
						} else if($clinic_type->Name == "Wellness") {
							$type = "wellness";
						} else if($clinic_type->Name == "Health Specialist") {
							$type = "health_specialist";
						}
					} else {
						$find_head = DB::table('clinic_types')
						->where('ClinicTypeID', $clinic_type->sub_id)
						->first();
						if($find_head->Name == "General Practitioner") {
							$type = "general_practitioner";
						} else if($find_head->Name == "Dental Care") {
							$type = "dental_care";
						} else if($find_head->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
						} else if($find_head->Name == "Health Screening") {
							$type = "health_screening";
						} else if($find_head->Name == "Wellness") {
							$type = "wellness";
						} else if($find_head->Name == "Health Specialist") {
							$type = "health_specialist";
						}
					}

					$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

					$format = array(
						'clinic_name'       => $clinic->Name,
						'clinic_image'      => $clinic->image,
						'clinic_type'       => $type,
						'amount'            => number_format($trans->procedure_cost, 2),
						'clinic_type_and_service' => $clinic_name,
						'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
						'member'          => ucwords($customer->Name),
						'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
						'trans_id'          => $trans->transaction_id,
						'transaction_files' => $transaction_files,
						'receipt_status'    => $receipt_status,
						'cash_status'       => $health_provider_status,
						'credit_status'     => $credit_status,
						'user_id'           => $trans->UserID,
						'type'              => 'In-Network',
						'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
						'refund_text'       => $refund_text,
						'transaction_type'  => $transaction_type,
						'cash'              => $cash,
						'payment_type'      => $payment_type,
						'status_text'       => $status_text
					);

					array_push($transaction_details, $format);
				}
			}
		}


		foreach($e_claim_result as $key => $res) {
			if($res->status == 0) {
				$status_text = 'Pending';
			} else if($res->status == 1) {
				$status_text = 'Approved';
				$e_claim_spent += $res->amount;
			} else if($res->status == 2) {
				$status_text = 'Rejected';
			} else {
				$status_text = 'Pending';
			}

			$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
            // get docs
			$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

			if(sizeof($docs) > 0) {
				$e_claim_receipt_status = TRUE;
				$doc_files = [];
				foreach ($docs as $key => $doc) {
					if($doc->file_type == "pdf" || $doc->file_type == "xls" || $doc->file_type == "xlsx") {
						$fil = url('').'/receipts/'.$doc->doc_file;
					} else if($doc->file_type == "image") {
						$fil = $doc->doc_file;
					}

					$temp_doc = array(
						'e_claim_doc_id'    => $doc->e_claim_doc_id,
						'e_claim_id'            => $doc->e_claim_id,
						'file'                      => $fil,
						'file_type'             => $doc->file_type
					);

					array_push($doc_files, $temp_doc);
				}
			} else {
				$e_claim_receipt_status = FALSE;
				$doc_files = FALSE;
			}

			$member = DB::table('user')->where('UserID', $res->user_id)->first();

			$temp = array(
				'status'            => $res->status,
				'status_text'   => $status_text,
				'claim_date'    => date('d F Y H:i A', strtotime($res->created_at)),
				'visit_date'    => date('d F Y', strtotime($res->date)).' '.$res->time,
				'time'              => $res->time,
				'service'           => $res->service,
				'merchant'      => $res->merchant,
				'amount'            => $res->amount,
				'member'            => ucwords($member->Name),
				'type'              => 'E-Claim',
				'transaction_id' => 'MNF'.$id,
				'files'             => $doc_files,
                // 'visit_date' => date('d F Y', strtotime($res->date)).', '.$res->time,
				'receipt_status' => $e_claim_receipt_status,
				'rejected_reason'   => $res->rejected_reason,
				'spending_type'     => $res->spending_type == 'medical' ? 'Medical' : 'Wellness',
				'approved_date'     => date('d F Y, h:ia', strtotime($res->approved_date))
			);

			array_push($e_claim, $temp);
		}


		$total_spent = $in_network_spent + $e_claim_spent;
		return array(
			'status'                   => TRUE,
			'e_claim'                  => $e_claim,
			'in_network_transactions'  => $transaction_details,
			'in_network_spent'         => number_format($in_network_spent, 2),
			'e_claim_spent'            => number_format($e_claim_spent, 2),
			'total_allocation'         => number_format($allocation - $deducted_allocation, 2),
			'total_spent'              => number_format($total_spent, 2),
			'balance'                  => $balance > 0 ? number_format($balance, 2) : number_format(0, 2),
			'total_in_network_transactions' => $total_in_network_transactions,
			'total_deleted_in_network_transactions' => $total_deleted_in_network_transactions,
			'total_in_network_spent'    => number_format($total_in_network_spent, 2),
			'total_cash'            => $total_cash,
			'total_credits'         => $total_credits + $e_claim_spent
		);
	}

	public function currentSpending( )
	{
		$user_id = Session::get('employee-session');
		$check = DB::table('user')->where('UserID', $user_id)->count();

		if($check == 0) {
			return array('status' => FALSE, 'message' => 'Employee does not exist.');
		}

		$input = Input::all();

		$spending_type = !empty($input['spending_type']) ? $input['spending_type'] : 'medical';
		


		$e_claim = [];
		$transaction_details = [];
		$in_network_spent = 0;
		$ids = StringHelper::getSubAccountsID($user_id);

		$lite_plan_status = false;
		$lite_plan_status = StringHelper::litePlanStatus($user_id);
        // get user wallet_id
		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

		if($spending_type == 'medical') {
			$table_wallet_history = 'wallet_history';
			$credit_data = PlanHelper::memberMedicalAllocatedCredits($wallet->wallet_id, $user_id);
		} else {
			$table_wallet_history = 'wellness_wallet_history';
			$credit_data = PlanHelper::memberWellnessAllocatedCredits($wallet->wallet_id, $user_id);
		}

		$allocation = $credit_data['allocation'];
		$current_spending = $credit_data['get_allocation_spent'];
		$e_claim_spent = $credit_data['e_claim_spent'];
		$in_network_spent = $credit_data['in_network_spent'];
		$balance = $credit_data['balance'];
		// $wallet_reset = DB::table('credit_reset')
		// ->where('id', $user_id)
		// ->where('user_type', 'employee')
		// ->where('spending_type', $spending_type)
		// ->orderBy('created_at', 'desc')
		// ->first();

		// if($wallet_reset) {
		// 	// get in-network transactions
		// 	$transactions = DB::table('transaction_history')
		// 	->whereIn('UserID', $ids)
		// 	->where('spending_type', $spending_type)
		// 	->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
		// 	->orderBy('created_at', 'desc')
		// 	->take(3)
		// 	->get();

		// // 	// get e_claim last 3 transactions
		// 	$e_claim_result = DB::table('e_claim')
		// 	->where('spending_type', $spending_type)
		// 	->whereIn('user_id', $ids)
		// 	->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
		// 	->orderBy('created_at', 'desc')
		// 	->take(3)
		// 	->get();

		// } else {
			// get in-network transactions
			$transactions = DB::table('transaction_history')
			->whereIn('UserID', $ids)
			->where('spending_type', $spending_type)
			->where('paid', 1)
			->orderBy('created_at', 'desc')
			->take(3)
			->get();

			// get e_claim last 3 transactions
			$e_claim_result = DB::table('e_claim')
			->where('spending_type', $spending_type)
			->whereIn('user_id', $ids)
			->orderBy('created_at', 'desc')
			->take(3)
			->get();
		// }

		foreach($e_claim_result as $key => $res) {
			if($res->status == 0) {
				$status_text = 'Pending';
			} else if($res->status == 1) {
				$status_text = 'Approved';
			} else if($res->status == 2) {
				$status_text = 'Rejected';
			} else {
				$status_text = 'Pending';
			}


        	// get docs
			$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

			if(sizeof($docs) > 0) {
				$doc_files = TRUE;
			} else {
				$doc_files = FALSE;
			}

			$member = DB::table('user')->where('UserID', $res->user_id)->first();

			$temp = array(
				'status'			=> $res->status,
				'status_text'	=> $status_text,
				'claim_date'	=> date('d F Y', strtotime($res->created_at)),
				'time'				=> $res->time,
				'service'			=> $res->service,
				'merchant'		=> $res->merchant,
				'amount'			=> number_format($res->amount, 2),
				'member'			=> ucwords($member->Name),
				'type'				=> 'E-Claim',
				'receipt_status' => $doc_files,
				'transaction_id' => $res->e_claim_id,
				'visit_date'	=> date('d F Y', strtotime($res->date)).', '.$res->time,
				'spending_type' => $res->spending_type
			);

			array_push($e_claim, $temp);
		}

		foreach ($transactions as $key => $trans) {
			if($trans) {

            // if($trans->deleted == 0) {
            //     $in_network_spent += $trans->credit_cost;
            // }

				// $receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
				$procedure_temp = "";
				$procedure = "";
				$wallet_status = false;

				$company_wallet_status = PlanHelper::getCompanyAccountType($user_id);

				if($company_wallet_status) {
				   if($company_wallet_status == "Health Wallet") {
				      $wallet_status = true;
				  }
				}

            // get services
				if((int)$trans->multiple_service_selection == 1)
				{
                // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).',';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						$procedure = ucwords($service_lists->Name);
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
                    // $procedure = "";
						$clinic_name = ucwords($clinic_type->Name);
					}
				}

            // check if there is a receipt image
				// $receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

				// if($receipt > 0) {
				// 	$receipt_status = TRUE;
				// } else {
				// 	$receipt_status = FALSE;
				// }

				$total_amount = $trans->procedure_cost;

				// if((int)$trans->health_provider_done == 1) {
				// 	$receipt_status = TRUE;
				// 	$health_provider_status = TRUE;
				// 	$credit_status = FALSE;
				// 	if((int)$trans->lite_plan_enabled == 1) {
				// 		$total_amount = $trans->procedure_cost + $trans->co_paid_amount;
				// 	}
				// } else {
				// 	$health_provider_status = FALSE;
				// 	$credit_status = TRUE;

				// 	if((int)$trans->lite_plan_enabled == 1) {
				// 		$total_amount = $trans->procedure_cost + $trans->co_paid_amount;
				// 	}
				// }

				if(strripos($trans->procedure_cost, '$') !== false) {
                     $temp_cost = explode('$', $trans->procedure_cost);
                     $cost = $temp_cost[1];
                 } else {
                     $cost = floatval($trans->procedure_cost);
                 }

				if((int)$trans->health_provider_done == 1) {
                      $receipt_status = TRUE;
                      $health_provider_status = TRUE;
                      $credit_status = FALSE;
                       if((int)$trans->lite_plan_enabled == 1 && $wallet_status == true) {
	                    if((int)$trans->half_credits == 1) {
	                      $total_amount = $trans->credit_cost + $trans->consultation_fees;
	                      $cash_cost = $transation->cash_cost;
	                    } else {
	                    	if($trans->credit_cost > 0) {
	                    		$total_amount = $trans->procedure_cost;
	                    	} else {
	                      		$total_amount = $trans->procedure_cost + $trans->consultation_fees;
	                    	}
	                      $cash_cost = $trans->procedure_cost;
	                    }
	                  } else {
	                    if((int)$trans->half_credits == 1) {
	                      $cash_cost = $trans->cash_cost;
	                    } else {
	                      $cash_cost = $trans->procedure_cost;
	                    }
	                  }
                } else {
                  $health_provider_status = FALSE;
                  $credit_status = TRUE;

                  if((int)$trans->lite_plan_enabled == 1 && $wallet_status == true) {
                    if((int)$trans->half_credits == 1) {
                      $total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
                      $cash_cost = $trans->cash_cost;
                    } else {
                      // $total_amount = $trans->credit_cost + $trans->consultation_fees;
                      $total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
                      if($trans->credit_cost > 0) {
                        $cash_cost = 0;
                      } else {
                        $cash_cost = $trans->procedure_cost - $trans->consultation_fees;
                      }
                    }
                  } else {
                    $total_amount = $trans->procedure_cost;
                    if((int)$trans->half_credits == 1) {
                      $cash_cost = $trans->cash_cost;
                    } else {
                      if($trans->credit_cost > 0) {
                        $cash_cost = 0;
                      } else {
                        $cash_cost = $trans->procedure_cost;
                      }
                    }
                  }
                }

				$format = array(
					'clinic_name'       => $clinic->Name,
					'clinic_image'      => $clinic->image,
					'amount'            => number_format($total_amount, 2),
					'clinic_type_and_service' => $clinic_name,
					'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
					'customer'          => ucwords($customer->Name),
					'transaction_id'    => $trans->transaction_id,
					// 'receipt_status'    => $receipt_status,
					'cash_status'       => $health_provider_status,
					'credit_status'     => $credit_status,
					'user_id'           => $trans->UserID,
					'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE
				);

				array_push($transaction_details, $format);
			}
		}

        // recalculate employee
		PlanHelper::reCalculateEmployeeBalance($user_id);

		return array(
			'current_spending' 	=> number_format($current_spending, 2),
			'e_claim_spent'			=> number_format($e_claim_spent, 2),
			'in_network_spent'	=> number_format($in_network_spent, 2),
			'total_allocation'	=> number_format($allocation, 2),
			'total_allocation_format_number' => $allocation,
			'in_network_spent_format_number' => $in_network_spent,
			'current_spending_format_number' => $current_spending,
			'e_claim'						=> $e_claim,
			'in_network_transactions' => $transaction_details,
			'balance'           => $balance >= 0 ? number_format($balance, 2) : "0.00",
			'spending_type'	=> $spending_type
		);
	}

	public function currentSpendingWellness( )
	{
		$input = Input::all();
		$user_id = Session::get('employee-session');
        // $user_id = $input['user_id'];
		$check = DB::table('user')->where('UserID', $user_id)->count();

		if($check == 0) {
			return array('status' => FALSE, 'message' => 'Employee does not exist.');
		}

		$e_claim = [];
		$transaction_details = [];
		$in_network_spent = 0;
		$ids = StringHelper::getSubAccountsID($user_id);

        // get user wallet_id
		$wallet = DB::table('e_wallet')->where('UserID', $user_id)->orderBy('created_at', 'desc')->first();

		$wallet_reset = DB::table('credit_reset')
		->where('id', $user_id)
		->where('user_type', 'employee')
		->where('spending_type', 'medical')
		->orderBy('created_at', 'desc')
		->first();

		if($wallet_reset) {
			$e_claim_spent = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
			->sum('credit');

			$in_network_temp_spent = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
			->sum('credit');
			$credits_back = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->where('created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
			->sum('credit');
		} else {
			$e_claim_spent = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'e_claim_transaction')
			->sum('credit');

			$in_network_temp_spent = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'in_network_transaction')
			->sum('credit');
			$credits_back = DB::table('wellness_wallet_history')
			->where('wallet_id', $wallet->wallet_id)
			->where('where_spend', 'credits_back_from_in_network')
			->sum('credit');
		}

		$in_network_spent = $in_network_temp_spent - $credits_back;
        // get e_claim last 3 transactions
		$e_claim_result = DB::table('e_claim')
		->where('spending_type', 'wellness')
		->whereIn('user_id', $ids)
		->orderBy('created_at', 'desc')
		->take(3)
		->get();

		if($wallet_reset) {
            // get credits allocation
			$temp_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('wellness_wallet_history.logs', ['added_by_hr'])
			->where('wellness_wallet_history.created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
			->sum('wellness_wallet_history.credit');
			$deducted_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('wellness_wallet_history.logs', ['deducted_by_hr'])
			->where('wellness_wallet_history.created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
			->sum('wellness_wallet_history.credit');
			$pro_allocation_deduction = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->where('logs', 'pro_allocation_deduction')
			->where('wellness_wallet_history.created_at', '>=', date('Y-m-d', strtotime($wallet_reset->date_resetted)))
			->sum('wellness_wallet_history.credit');
			$allocation = $temp_allocation - $deducted_allocation - $pro_allocation_deduction;
		} else {
            // get credits allocation
			$temp_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['added_by_hr'])
			->sum('wellness_wallet_history.credit');
			$deducted_allocation = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->whereIn('logs', ['deducted_by_hr'])
			->sum('wellness_wallet_history.credit');
			$pro_allocation_deduction = DB::table('e_wallet')
			->join('wellness_wallet_history', 'wellness_wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
			->where('e_wallet.UserID', $user_id)
			->where('logs', 'pro_allocation_deduction')
			->sum('wellness_wallet_history.credit');
			$allocation = $temp_allocation - $deducted_allocation - $pro_allocation_deduction;
		}


		foreach($e_claim_result as $key => $res) {
			if($res->status == 0) {
				$status_text = 'Pending';
			} else if($res->status == 1) {
				$status_text = 'Approved';
			} else if($res->status == 2) {
				$status_text = 'Rejected';
			} else {
				$status_text = 'Pending';
			}


            // get docs
			$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

			if(sizeof($docs) > 0) {
				$doc_files = TRUE;
			} else {
				$doc_files = FALSE;
			}

			$member = DB::table('user')->where('UserID', $res->user_id)->first();

			$temp = array(
				'status'            => $res->status,
				'status_text'   => $status_text,
				'claim_date'    => date('d F Y', strtotime($res->created_at)),
				'time'              => $res->time,
				'service'           => $res->service,
				'merchant'      => $res->merchant,
				'amount'            => $res->amount,
				'member'            => ucwords($member->Name),
				'type'              => 'E-Claim',
				'receipt_status' => $doc_files,
				'transaction_id' => $res->e_claim_id,
				'visit_date'    => date('d F Y', strtotime($res->date)).', '.$res->time
			);

			array_push($e_claim, $temp);
		}

       // get in-network transactions
		$transactions = DB::table('transaction_history')
		->where('spending_type', 'wellness')
		->whereIn('UserID', $ids)
                        // ->where('mobile', 1)
                        // ->where('in_network', 1)
		->orderBy('created_at', 'desc')
		->take(3)
		->get();

		foreach ($transactions as $key => $trans) {
			if($trans) {

            // if($trans->deleted == 0) {
            //     $in_network_spent += $trans->credit_cost;
            // }

				$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
				$procedure_temp = "";

            // get services
				if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
				{
                // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).',';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						$procedure = ucwords($service_lists->Name);
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
                    // $procedure = "";
						$clinic_name = ucwords($clinic_type->Name);
					}
				}

            // check if there is a receipt image
				$receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

				if($receipt > 0) {
					$receipt_status = TRUE;
				} else {
					$receipt_status = FALSE;
				}

				if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
					$receipt_status = TRUE;
					$health_provider_status = TRUE;
					$credit_status = FALSE;
				} else {
					$health_provider_status = FALSE;
					$credit_status = TRUE;
				}

				$format = array(
					'clinic_name'       => $clinic->Name,
					'clinic_image'      => $clinic->image,
					'amount'            => number_format($trans->procedure_cost, 2),
					'clinic_type_and_service' => $clinic_name,
					'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
					'customer'          => ucwords($customer->Name),
					'transaction_id'    => $trans->transaction_id,
					'receipt_status'    => $receipt_status,
					'cash_status'       => $health_provider_status,
					'credit_status'     => $credit_status,
					'user_id'           => $trans->UserID,
					'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE
				);

				array_push($transaction_details, $format);
			}
		}

		$current_spending = $in_network_spent + $e_claim_spent;

		return array(
			'current_spending'  => number_format($current_spending, 2),
			'e_claim_spent'         => number_format($e_claim_spent, 2),
			'in_network_spent'  => number_format($in_network_spent, 2),
			'total_allocation'  => number_format($allocation, 2),
			'total_allocation_format_number' => $allocation,
			'in_network_spent_format_number' => $in_network_spent,
			'current_spending_format_number' => $in_network_spent + $e_claim_spent,
			'e_claim'                       => $e_claim,
			'in_network_transactions' => $transaction_details,
			'balance'           => number_format($allocation - $current_spending, 2)
		);
	}

	public function processQueryTransactionChunk($id, $start, $end, $trans_end, $total_allocation)
	{
		$results = CorporateMembers::where('corporate_id', $id)
		->chunk(5, function($corporate_members) use ($start, $end, $trans_end, $total_allocation){
			$e_claim = [];
			$transaction_details = [];

			$in_network_spent = 0;
			$e_claim_spent = 0;
			$e_claim_pending = 0;
			$health_screening_breakdown = 0;
			$general_practitioner_breakdown = 0;
			$dental_care_breakdown = 0;
			$tcm_breakdown = 0;
			$health_specialist_breakdown = 0;
			$wellness_breakdown = 0;
			$allocation = 0;
			$total_credits = 0;
			$total_cash = 0;
			$deleted_employee_allocation = 0;
			$deleted_transaction_cash = 0;
			$deleted_transaction_credits = 0;

			$total_in_network_transactions = 0;
			$total_deleted_in_network_transactions = 0;
			$total_search_cash = 0;
			$total_search_credits = 0;
			$total_in_network_spent = 0;
			$total_deducted_allocation = 0;

			foreach ($corporate_members as $key => $member) {
				$employee_allocation = 0;
				$ids = StringHelper::getSubAccountsID($member['user_id']);
				$wallet = DB::table('e_wallet')->where('UserID', $member['user_id'])->orderBy('created_at', 'desc')->first();
                                            // get e claim
				$e_claim_result = DB::table('e_claim')
				->whereIn('user_id', $ids)
				->where('date', '>=', $start)
				->where('date', '<=', $end)
                                                            // ->where('status', 1)
				->orderBy('created_at', 'desc')
				->get();

                                            // get employee allocation
				$employee_allocation = DB::table('wallet_history')
				->where('wallet_id', $wallet->wallet_id)
				->whereYear('created_at', '>=', date('Y', strtotime($start)))
				->whereYear('created_at', '<=', date('Y', strtotime($end)))
                                                            // ->where('created_at', '>=', $start)
                                                            // ->where('created_at', '<=', $end)
				->where('logs', 'added_by_hr')
				->sum('credit');
				$deducted_allocation = DB::table('e_wallet')
				->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
				->where('e_wallet.UserID', $member['user_id'])
				->whereIn('logs', ['deducted_by_hr'])
				->sum('wallet_history.credit');

				$total_deducted_allocation += $deducted_allocation;
				$allocation += $employee_allocation;
				if($member->removed_status == 1) {
					$deleted_employee_allocation += $employee_allocation - $deducted_allocation;
				}

                                            // get in-network transactions
				$transactions = DB::table('transaction_history')
				->whereIn('UserID', $ids)
                                                            // ->where('mobile', 1)
                                                            // ->where('in_network', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $trans_end)
				->orderBy('date_of_transaction', 'desc')
				->get();

                                            // in-network transactions
				foreach ($transactions as $key => $trans) {
					if($trans) {
						if($trans->procedure_cost > 0) {
							if($trans->deleted == 0) {
								$in_network_spent += $trans->credit_cost;
								$total_in_network_transactions++;
							} else {
								$total_deleted_in_network_transactions++;
							}
							$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
							$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
							$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
							$procedure_temp = "";

                                                    // get services
							if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
							{
                                                        // get multiple service
								$service_lists = DB::table('transaction_services')
								->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
								->where('transaction_services.transaction_id', $trans->transaction_id)
								->get();

								foreach ($service_lists as $key => $service) {
									if(sizeof($service_lists) - 2 == $key) {
										$procedure_temp .= ucwords($service->Name).' and ';
									} else {
										$procedure_temp .= ucwords($service->Name).',';
									}
									$procedure = rtrim($procedure_temp, ',');
								}
								$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
							} else {
								$service_lists = DB::table('clinic_procedure')
								->where('ProcedureID', $trans->ProcedureID)
								->first();
								if($service_lists) {
									$procedure = ucwords($service_lists->Name);
									$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
								} else {
                                                            // $procedure = "";
									$clinic_name = ucwords($clinic_type->Name);
								}
							}

                                                    // check if there is a receipt image
							$receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

							if($receipt > 0) {
								$receipt_status = TRUE;
							} else {
								$receipt_status = FALSE;
							}

							if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
								$receipt_status = TRUE;
								$health_provider_status = TRUE;
							} else {
								$health_provider_status = FALSE;
							}

                                                    // get clinic type
							$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
							$type = "";
							if($clinic_type->head == 1 || $clinic_type->head == "1") {
								if($clinic_type->Name == "General Practitioner") {
									$type = "general_practitioner";
									if($trans->deleted == 0) {
										$general_practitioner_breakdown += $trans->credit_cost;
									}
								} else if($clinic_type->Name == "Dental Care") {
									$type = "dental_care";
									if($trans->deleted == 0) {
										$dental_care_breakdown += $trans->credit_cost;
									}
								} else if($clinic_type->Name == "Traditional Chinese Medicine") {
									$type = "tcm";
									if($trans->deleted == 0) {
										$tcm_breakdown += $trans->credit_cost;
									}
								} else if($clinic_type->Name == "Health Screening") {
									$type = "health_screening";
									if($trans->deleted == 0) {
										$health_screening_breakdown += $trans->credit_cost;
									}
								} else if($clinic_type->Name == "Wellness") {
									$type = "wellness";
									if($trans->deleted == 0) {
										$wellness_breakdown += $trans->credit_cost;
									}
								} else if($clinic_type->Name == "Health Specialist") {
									$type = "health_specialist";
									if($trans->deleted == 0) {
										$health_specialist_breakdown += $trans->credit_cost;
									}
								}
							} else {
								$find_head = DB::table('clinic_types')
								->where('ClinicTypeID', $clinic_type->sub_id)
								->first();
								if($find_head->Name == "General Practitioner") {
									$type = "general_practitioner";
									if($trans->deleted == 0) {
										$general_practitioner_breakdown += $trans->credit_cost;
									}
								} else if($find_head->Name == "Dental Care") {
									$type = "dental_care";
									if($trans->deleted == 0) {
										$dental_care_breakdown += $trans->credit_cost;
									}
								} else if($find_head->Name == "Traditional Chinese Medicine") {
									$type = "tcm";
									if($trans->deleted == 0) {
										$tcm_breakdown += $trans->credit_cost;
									}
								} else if($find_head->Name == "Health Screening") {
									$type = "health_screening";
									if($trans->deleted == 0) {
										$health_screening_breakdown += $trans->credit_cost;
									}
								} else if($find_head->Name == "Wellness") {
									$type = "wellness";
									if($trans->deleted == 0) {
										$wellness_breakdown += $trans->credit_cost;
									}
								} else if($find_head->Name == "Health Specialist") {
									$type = "health_specialist";
									if($trans->deleted == 0) {
										$health_specialist_breakdown += $trans->credit_cost;
									}
								}
							}

                                                    // check user if it is spouse or dependent
							if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
								$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
								$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
								$sub_account = ucwords($temp_account->Name);
								$sub_account_type = $temp_sub->user_type;
								$owner_id = $temp_sub->owner_id;
							} else {
								$sub_account = FALSE;
								$sub_account_type = FALSE;
								$owner_id = $customer->UserID;
							}

							if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
								$payment_type = "Cash";
								$cash = number_format($trans->procedure_cost, 2);
								if($trans->deleted == 0 || $trans->deleted == "0") {
									$total_cash += $trans->procedure_cost;
								} else if($trans->deleted == 1 || $trans->deleted == "1") {
									$deleted_transaction_cash = $trans->procedure_cost;
								}
							} else {
								$payment_type = "Mednefits Credits";
								$cash = number_format($trans->credit_cost, 2);
								if($trans->deleted == 0 || $trans->deleted == "0") {
									$total_credits += $trans->credit_cost;
								} else if($trans->deleted == 1 || $trans->deleted == "1") {
									$deleted_transaction_credits = $trans->credit_cost;
								}
							}

							if( $trans->health_provider_done == 1 && $trans->deleted == 0 || $trans->health_provider_done == "1" && $trans->deleted == "0" ) {
								$total_in_network_spent += $trans->procedure_cost;
								$total_search_cash += $trans->procedure_cost;
							} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
								$total_in_network_spent += $trans->credit_cost;
								$total_search_credits += $trans->credit_cost;
							}

							$refund_text = 'NO';

							if($trans->refunded == 1 && $trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
								$status_text = 'REFUNDED';
								$refund_text = 'YES';
							} else if($trans->health_provider_done == 1 && $trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
								$status_text = 'REMOVED';
								$refund_text = 'YES';
							} else {
								$status_text = FALSE;
							}


							$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

							$format = array(
								'clinic_name'       => $clinic->Name,
								'clinic_image'      => $clinic->image,
								'amount'            => number_format($trans->procedure_cost, 2),
								'clinic_type_and_service' => $clinic_name,
								'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
								'member'            => ucwords($customer->Name),
								'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
								'trans_id'          => $trans->transaction_id,
								'receipt_status'    => $receipt_status,
								'health_provider_status' => $health_provider_status,
								'user_id'           => $trans->UserID,
								'type'              => $payment_type,
								'month'             => date('M', strtotime($trans->date_of_transaction)),
								'day'               => date('d', strtotime($trans->date_of_transaction)),
								'time'              => date('h:ia', strtotime($trans->date_of_transaction)),
								'clinic_type'       => $type,
								'owner_account'     => $sub_account,
								'owner_id'          => $owner_id,
								'sub_account_user_type' => $sub_account_type,
								'co_paid'           => $trans->co_paid_amount,
								'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
								'refund_text'       => $refund_text,
								'cash'              => $cash,
								'status_text'       => $status_text
							);

							array_push($transaction_details, $format);
						}
					}


				}

                                            // e-claim transactions
				foreach($e_claim_result as $key => $res) {
					if($res->status == 0) {
						$status_text = 'Pending';
						$e_claim_pending += $res->amount;
					} else if($res->status == 1) {
						$status_text = 'Approved';
						$e_claim_spent += $res->amount;
					} else if($res->status == 2) {
						$status_text = 'Rejected';
					} else {
						$status_text = 'Pending';
					}

					if($res->status == 1) {
						$member = DB::table('user')->where('UserID', $res->user_id)->first();

                                                    // check user if it is spouse or dependent
						if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
							$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
							$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
							$sub_account = ucwords($temp_account->Name);
							$sub_account_type = $temp_sub->user_type;
							$owner_id = $temp_sub->owner_id;
						} else {
							$sub_account = FALSE;
							$sub_account_type = FALSE;
							$owner_id = $member->UserID;
						}

                                                    // get docs
						$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

						if(sizeof($docs) > 0) {
							$e_claim_receipt_status = TRUE;
							$doc_files = [];
							foreach ($docs as $key => $doc) {
								if($doc->file_type == "pdf") {
									$fil = url('').'/receipts/'.$doc->doc_file;
								} else if($doc->file_type == "image") {
									$fil = $doc->doc_file;
								}

								$temp_doc = array(
									'e_claim_doc_id'    => $doc->e_claim_doc_id,
									'e_claim_id'            => $doc->e_claim_id,
									'file'                      => $fil,
									'file_type'             => $doc->file_type
								);

								array_push($doc_files, $temp_doc);
							}
						} else {
							$e_claim_receipt_status = FALSE;
							$doc_files = FALSE;
						}

						$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
						$temp = array(
							'status'            => $res->status,
							'status_text'       => $status_text,
							'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
							'approved_date'     => date('d F Y', strtotime($res->approved_date)),
							'time'              => $res->time,
							'service'           => $res->service,
							'merchant'          => $res->merchant,
							'amount'            => $res->amount,
							'member'            => ucwords($member->Name),
							'type'              => 'E-Claim',
							'transaction_id'    => 'MNF'.$id,
							'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
							'owner_id'          => $owner_id,
							'sub_account_type'  => $sub_account_type,
							'sub_account'       => $sub_account,
							'month'             => date('M', strtotime($res->approved_date)),
							'day'               => date('d', strtotime($res->approved_date)),
							'time'              => date('h:ia', strtotime($res->approved_date)),
							'receipt_status'    => $e_claim_receipt_status,
							'files'             => $doc_files,
						);

						array_push($e_claim, $temp);
					}
				}
			}

			$total_spent = $e_claim_spent + $in_network_spent;

			$in_network_breakdown = array(
				'general_practitioner_breakdown' => $general_practitioner_breakdown > 0 && $in_network_spent > 0 ? number_format($general_practitioner_breakdown / $in_network_spent * 100, 0) : 0,
				'health_screening_breakdown'     => $health_screening_breakdown > 0 && $in_network_spent > 0 ? number_format($health_screening_breakdown / $in_network_spent * 100, 0) : 0,
				'dental_care_breakdown'          => $dental_care_breakdown > 0 && $in_network_spent > 0 ? number_format($dental_care_breakdown / $in_network_spent * 100, 0) : 0,
				'tcm_breakdown'                  => $tcm_breakdown > 0 && $in_network_spent > 0 ? number_format($tcm_breakdown / $in_network_spent * 100, 0) : 0,
				'health_specialist_breakdown'    => $health_specialist_breakdown > 0 && $in_network_spent > 0 ? number_format($health_specialist_breakdown / $in_network_spent * 100, 0) : 0,
				'wellness_breakdown'             => $wellness_breakdown > 0 && $in_network_spent > 0 ? number_format($wellness_breakdown / $in_network_spent * 100, 0) : 0
			);


                                        // sort in-network transaction
			usort($transaction_details, function($a, $b) {
				return strtotime($b['date_of_transaction']) - strtotime($a['date_of_transaction']);
			});

			$grand_total_credits_cash = $total_credits + $total_cash - $deleted_transaction_credits - $deleted_transaction_cash;

			$temp_allocation = $allocation - $deleted_employee_allocation;
			$balance = $temp_allocation - $total_spent - $total_deducted_allocation;

			$temp_results = array(
				'deleted_employee_allocation' => $deleted_employee_allocation,
				'total_allocation'  => number_format($total_allocation, 2),
				'total_spent'       => number_format($total_spent, 2),
				'balance'           => number_format($balance, 2),
				'pending_e_claim_amount' => number_format($e_claim_pending, 2),
				'in_network_spent'  => number_format($in_network_spent, 2),
				'e_claim_spent'     => number_format($e_claim_spent, 2),
				'in_network_breakdown' => $in_network_breakdown,
				'in_network_transactions' => $transaction_details,
				'e_claim_transactions'  => $e_claim,
				'allocation'        => $allocation > 0 ? number_format($allocation - $deleted_employee_allocation - $total_deducted_allocation, 2) : number_format(0, 2),
				'total_in_network_credits_cash' => $grand_total_credits_cash > 0 ? number_format($grand_total_credits_cash, 2) : number_format(0, 2),
				'deleted_transaction_cash'  => $deleted_transaction_cash,
				'deleted_transaction_credits' => $deleted_transaction_credits,
				'total_credits_cash' => $total_credits,
				'in_network_spending_format_number' => $in_network_spent,
				'e_claim_spending_format_number' => $e_claim_spent,
				'total_in_network_spent'    => number_format($total_in_network_spent, 2),
				'total_cash'            => $total_search_cash,
				'total_credits'         => $total_search_credits,
				'total_deleted_in_network_transactions' => $total_deleted_in_network_transactions,
				'total_in_network_transactions' => $total_in_network_transactions
			);

			echo json_encode($temp_results);
		});
}

public function getActivityOutNetworkTransactions( )
{
	$session = self::checkSession();
	$customer_id = $session->customer_buy_start_id;
	$input = Input::all();
        // $customer_id = $input['customer_id'];

	$start = date('Y-m-d', strtotime($input['start']));
	$end = SpendingInvoiceLibrary::getEndDate($input['end']);
	$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
	$e_claim = [];
	$paginate = [];

	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

	if(!empty($input['user_id']) && $input['user_id'] != null) {
		$e_claim_result = DB::table('corporate_members')
		->join('e_claim', 'e_claim.user_id', '=', 'corporate_members.user_id')
		->where('corporate_members.corporate_id', $account->corporate_id)
		->where('corporate_members.user_id', $input['user_id'])
		->where('e_claim.spending_type', $spending_type)
		->where('e_claim.status', 1)
		->where('e_claim.created_at', '>=', $start)
		->where('e_claim.created_at', '<=', $end)
		->orderBy('e_claim.created_at', 'desc')
		->paginate($input['per_page']);
	} else {
		$user_ids = PlanHelper::getCompanyMemberIds($customer_id);
		$e_claim_result = DB::table('e_claim')
		->where('spending_type', $spending_type)
		->whereIn('user_id', $user_ids)
		->where('status', 1)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
		->orderBy('created_at', 'desc')
		->paginate($input['per_page']);
	}

        // return $e_claim_result;
	$paginate['current_page'] = $e_claim_result->getCurrentPage();
	$paginate['from'] = $e_claim_result->getFrom();
	$paginate['last_page'] = $e_claim_result->getLastPage();
	$paginate['per_page'] = $e_claim_result->getPerPage();
	$paginate['to'] = $e_claim_result->getTo();
	$paginate['total'] = $e_claim_result->getTotal();

	foreach($e_claim_result as $key => $res) {
		if($res->status == 0) {
			$status_text = 'Pending';
		} else if($res->status == 1) {
			$status_text = 'Approved';
		} else if($res->status == 2) {
			$status_text = 'Rejected';
		} else {
			$status_text = 'Pending';
		}

		if($res->status == 1) {
			$member = DB::table('user')->where('UserID', $res->user_id)->first();

                // check user if it is spouse or dependent
			if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
				$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
				$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
				$sub_account = ucwords($temp_account->Name);
				$sub_account_type = $temp_sub->user_type;
				$owner_id = $temp_sub->owner_id;
				$dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
			} else {
				$sub_account = FALSE;
				$sub_account_type = FALSE;
				$owner_id = $member->UserID;
				$dependent_relationship = FALSE;
			}

                // get docs
			$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

			if(sizeof($docs) > 0) {
				$e_claim_receipt_status = TRUE;
				$doc_files = [];
				foreach ($docs as $key => $doc) {
					if($doc->file_type == "pdf" || $doc->file_type == "xls") {
						// $fil = url('').'/receipts/'.$doc->doc_file;
						$fil = EclaimHelper::createPreSignedUrl($doc->doc_file);
					} else if($doc->file_type == "image") {
						// $fil = $doc->doc_file;
						$fil = FileHelper::formatImageAutoQualityCustomer($doc->doc_file, 40);
					}

					$temp_doc = array(
						'e_claim_doc_id'    => $doc->e_claim_doc_id,
						'e_claim_id'            => $doc->e_claim_id,
						'file'                      => $fil,
						'file_type'             => $doc->file_type
					);

					array_push($doc_files, $temp_doc);
				}
			} else {
				$e_claim_receipt_status = FALSE;
				$doc_files = FALSE;
			}

			$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
			$temp = array(
				'status'            => $res->status,
				'status_text'       => $status_text,
				'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
				'approved_date'     => date('d F Y', strtotime($res->approved_date)),
				'time'              => $res->time,
				'service'           => $res->service,
				'merchant'          => $res->merchant,
				'amount'            => number_format($res->amount, 2),
				'member'            => ucwords($member->Name),
				'type'              => 'E-Claim',
				'transaction_id'    => 'MNF'.$id,
				'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
				'owner_id'          => $owner_id,
				'sub_account_type'  => $sub_account_type,
				'sub_account'       => $sub_account,
				'month'             => date('M', strtotime($res->approved_date)),
				'day'               => date('d', strtotime($res->approved_date)),
				'time'              => date('h:ia', strtotime($res->approved_date)),
				'receipt_status'    => $e_claim_receipt_status,
				'files'             => $doc_files,
				'spending_type'     => ucwords($res->spending_type),
				'dependent_relationship'    => $dependent_relationship
			);

			array_push($e_claim, $temp);
		}
	}

	$paginate['data'] = $e_claim;
	$paginate['status'] = true;
	return $paginate;
}

public function getActivityInNetworkTransactions( )
{
	$input = Input::all();
	$session = self::checkSession();
	$customer_id = $session->customer_buy_start_id;
        // $customer_id = $input['customer_id'];

	$start = date('Y-m-d', strtotime($input['start']));
	$end = SpendingInvoiceLibrary::getEndDate($input['end']);
	$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';

	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
	$lite_plan = false;
	$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
	$transaction_details = [];
	$in_network_spent = 0;
	$health_screening_breakdown = 0;
	$general_practitioner_breakdown = 0;
	$dental_care_breakdown = 0;
	$tcm_breakdown = 0;
	$health_specialist_breakdown = 0;
	$wellness_breakdown = 0;
	$allocation = 0;
	$total_credits = 0;
	$total_cash = 0;
	$deleted_employee_allocation = 0;
	$deleted_transaction_cash = 0;
	$deleted_transaction_credits = 0;

	$total_in_network_transactions = 0;
	$total_deleted_in_network_transactions = 0;
	$total_search_cash = 0;
	$total_search_credits = 0;
	$total_in_network_spent = 0;
	$total_deducted_allocation = 0;
	$break_down_calculation = 0;

	$total_credits_transactions = 0;
	$total_cash_transactions = 0;
	$total_credits_transactions_deleted = 0;
	$total_cash_transactions_deleted = 0;

	$total_in_network_spent_credits_transaction = 0;
	$total_in_network_spent_cash_transaction = 0;
	$total_lite_plan_consultation = 0;
	$paginate = [];

	if(!empty($input['user_id']) && $input['user_id'] != null) {
		$transactions = DB::table('corporate_members')
		->join('transaction_history', 'transaction_history.UserID', '=', 'corporate_members.user_id')
		->where('corporate_members.corporate_id', $account->corporate_id)
		->where('corporate_members.user_id', $input['user_id'])
		->where('transaction_history.spending_type', $spending_type)
		->where('transaction_history.paid', 1)
		->where('transaction_history.date_of_transaction', '>=', $start)
		->where('transaction_history.date_of_transaction', '<=', $end)
		->orderBy('transaction_history.date_of_transaction', 'desc')
		->paginate($input['per_page']);
	} else {
		$user_ids = PlanHelper::getCompanyMemberIds($customer_id);
		$transactions = DB::table('transaction_history')
		->where('spending_type', $spending_type)
		->whereIn('UserID', $user_ids)
		->where('paid', 1)
		->where('date_of_transaction', '>=', $start)
		->where('date_of_transaction', '<=', $end)
		->orderBy('date_of_transaction', 'desc')
		->paginate($input['per_page']);
	}

	$paginate['current_page'] = $transactions->getCurrentPage();
	$paginate['from'] = $transactions->getFrom();
	$paginate['last_page'] = $transactions->getLastPage();
	$paginate['per_page'] = $transactions->getPerPage();
	$paginate['to'] = $transactions->getTo();
	$paginate['total'] = $transactions->getTotal();

	if($spending_type == 'medical') {
		$table_wallet_history = 'wallet_history';
	} else {
		$table_wallet_history = 'wellness_wallet_history';
	}
	
	foreach ($transactions as $key => $trans) {
		$consultation_cash = false;
		$consultation_credits = false;
		$service_cash = false;
		$service_credits = false;
		$consultation = 0;

		if($trans) {

			if($trans->procedure_cost >= 0 && $trans->paid == 1 || $trans->procedure_cost >= 0 && $trans->paid == "1") {
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$in_network_spent += $trans->credit_cost;
					$total_in_network_transactions++;

					if($trans->lite_plan_enabled == 1) {


						$logs_lite_plan = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('lite_plan_enabled', 1)
						->where('id', $trans->transaction_id)
						->first();

						if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
							$in_network_spent += floatval($logs_lite_plan->credit);
							$consultation_credits = true;
							$service_credits = true;
							$total_lite_plan_consultation += floatval($trans->consultation_fees);
							$consultation = floatval($logs_lite_plan->credit);
						} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
							$in_network_spent += floatval($logs_lite_plan->credit);
							$consultation_credits = true;
							$service_credits = true;
							$total_lite_plan_consultation += floatval($trans->consultation_fees);
							$consultation = floatval($logs_lite_plan->credit);
						} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
							$total_lite_plan_consultation += floatval($trans->consultation_fees);
							$consultation = floatval($trans->consultation_fees);
						}
					}
				} else {
					$total_deleted_in_network_transactions++;
					if($trans->lite_plan_enabled == 1) {
						$logs_lite_plan = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('lite_plan_enabled', 1)
						->where('id', $trans->transaction_id)
						->first();

						if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
							$consultation_credits = true;
							$service_credits = true;
						} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
							$consultation_credits = true;
							$service_credits = true;
						}
					}
				}


				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
				$procedure_temp = "";
				$procedure = "";

                        // get services
				if((int)$trans->multiple_service_selection == 1)
				{
                            // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).',';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						$procedure = ucwords($service_lists->Name);
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
                                // $procedure = "";
						$clinic_name = ucwords($clinic_type->Name);
					}
				}

                        // check if there is a receipt image
				$receipts = DB::table('user_image_receipt')
							->where('transaction_id', $trans->transaction_id)
							->get();

				$doc_files = [];
				if(sizeof($receipts) > 0) {
					foreach ($receipts as $key => $doc) {
						if($doc->type == "pdf" || $doc->type == "xls") {
							if(StringHelper::Deployment()==1){
							   $fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->file;
							} else {
							   $fil = url('').'/receipts/'.$doc->file;
							}
						} else if($doc->type == "image") {
							// $fil = FileHelper::formatImageAutoQuality($doc->file);
							$fil = FileHelper::formatImageAutoQualityCustomer($doc->file, 40);
						}

						$temp_doc = array(
							'tranasaction_doc_id'    => $doc->image_receipt_id,
							'transaction_id'            => $doc->transaction_id,
							'file'                      => $fil,
							'file_type'             => $doc->type
						);

						array_push($doc_files, $temp_doc);
					}
					$receipt_status = TRUE;
				} else {
					$receipt_status = FALSE;
				}

				if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
                            // $receipt_status = TRUE;
					$health_provider_status = TRUE;
				} else {
					$health_provider_status = FALSE;
				}

				$type = "";
				if($clinic_type->head == 1 || $clinic_type->head == "1") {
					if($clinic_type->Name == "General Practitioner") {
						$type = "general_practitioner";
						if($trans->deleted == 0) {
							$general_practitioner_breakdown += $trans->credit_cost;
							if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
								$general_practitioner_breakdown += $trans->consultation_fees;
							}
						}
					} else if($clinic_type->Name == "Dental Care") {
						$type = "dental_care";
						if($trans->deleted == 0) {
							$dental_care_breakdown += $trans->credit_cost;
						}
					} else if($clinic_type->Name == "Traditional Chinese Medicine") {
						$type = "tcm";
						if($trans->deleted == 0) {
							$tcm_breakdown += $trans->credit_cost;
						}
					} else if($clinic_type->Name == "Health Screening") {
						$type = "health_screening";
						if($trans->deleted == 0) {
							$health_screening_breakdown += $trans->credit_cost;
						}
					} else if($clinic_type->Name == "Wellness") {
						$type = "wellness";
						if($trans->deleted == 0) {
							$wellness_breakdown += $trans->credit_cost;
						}
					} else if($clinic_type->Name == "Health Specialist") {
						$type = "health_specialist";
						if($trans->deleted == 0) {
							$health_specialist_breakdown += $trans->credit_cost;
						}
					}
				} else {
					$find_head = DB::table('clinic_types')
					->where('ClinicTypeID', $clinic_type->sub_id)
					->first();
					if($find_head->Name == "General Practitioner") {
						$type = "general_practitioner";
						if($trans->deleted == 0) {
							$general_practitioner_breakdown += $trans->credit_cost;
							if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
								$general_practitioner_breakdown += $trans->consultation_fees;
							}
						}
					} else if($find_head->Name == "Dental Care") {
						$type = "dental_care";
						if($trans->deleted == 0) {
							$dental_care_breakdown += $trans->credit_cost;
						}
					} else if($find_head->Name == "Traditional Chinese Medicine") {
						$type = "tcm";
						if($trans->deleted == 0) {
							$tcm_breakdown += $trans->credit_cost;
						}
					} else if($find_head->Name == "Health Screening") {
						$type = "health_screening";
						if($trans->deleted == 0) {
							$health_screening_breakdown += $trans->credit_cost;
						}
					} else if($find_head->Name == "Wellness") {
						$type = "wellness";
						if($trans->deleted == 0) {
							$wellness_breakdown += $trans->credit_cost;
						}
					} else if($find_head->Name == "Health Specialist") {
						$type = "health_specialist";
						if($trans->deleted == 0) {
							$health_specialist_breakdown += $trans->credit_cost;
						}
					}
				}

                        // check user if it is spouse or dependent
				if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
					$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
					$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
					$sub_account = ucwords($temp_account->Name);
					$sub_account_type = $temp_sub->user_type;
					$owner_id = $temp_sub->owner_id;
					$dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
				} else {
					$sub_account = FALSE;
					$sub_account_type = FALSE;
					$dependent_relationship = FALSE;
					$owner_id = $customer->UserID;
				}

				$half_credits = false;
				$total_amount = number_format($trans->procedure_cost, 2);

				if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
					$payment_type = "Cash";
					$transaction_type = "cash";
					if((int)$trans->lite_plan_enabled == 1) {
              if((int)$trans->half_credits == 1) {
                $total_amount = $trans->credit_cost + $trans->consultation_fees;
                $cash = $transation->cash_cost;
              } else {
                $total_amount = $trans->procedure_cost;
                $total_amount = $trans->procedure_cost + $trans->consultation_fees;
                $cash = $trans->procedure_cost;
              }
          } else {
            if((int)$trans->half_credits == 1) {
              $cash = $trans->cash_cost;
            } else {
              $cash = $trans->procedure_cost;
            }
          }
				} else {
					if($trans->credit_cost > 0 && $trans->cash_cost > 0) {
				      $payment_type = 'Mednefits Credits + Cash';
				      $half_credits = true;
				    } else {
				      $payment_type = 'Mednefits Credits';
				    }
					$transaction_type = "credits";
					// $cash = number_format($trans->credit_cost, 2);
					if((int)$trans->lite_plan_enabled == 1) {
              if((int)$trans->half_credits == 1) {
                $total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
                $cash = $trans->cash_cost;
              } else {
                $total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
                // $total_amount = $trans->procedure_cost;
                if($trans->credit_cost > 0) {
                  $cash = 0;
                } else {
                  $cash = $trans->procedure_cost - $trans->consultation_fees;
                }
              }
            } else {
              $total_amount = $trans->procedure_cost;
              if((int)$trans->half_credits == 1) {
                $cash = $trans->cash_cost;
              } else {
                if($trans->credit_cost > 0) {
                  $cash = 0;
                } else {
                  $cash = $trans->procedure_cost;
                }
              }
            }
				}

				$bill_amount = 0;
				if((int)$trans->half_credits == 1) {
					if((int)$trans->lite_plan_enabled == 1) {
						if((int)$trans->health_provider_done == 1) {
							$bill_amount = $trans->procedure_cost;
						} else {
							$bill_amount = $trans->procedure_cost - $trans->consultation_fees;
						}
					} else {
						$bill_amount = 	$trans->procedure_cost;
					}
				} else {
					if((int)$trans->lite_plan_enabled == 1) {
						if((int)$trans->lite_plan_use_credits == 1) {
							$bill_amount = 	$trans->procedure_cost;
						} else {
							if((int)$trans->health_provider_done == 1) {
								$bill_amount = 	$trans->procedure_cost;
							} else {
								$bill_amount = 	$trans->credit_cost + $trans->cash_cost;
							}
						}
					} else {
						$bill_amount = 	$trans->procedure_cost;
					}
				}

				if( $trans->health_provider_done == 1 && $trans->deleted == 0 || $trans->health_provider_done == "1" && $trans->deleted == "0" ) {
					$total_search_cash += $trans->procedure_cost;
					$total_in_network_spent_cash_transaction += $trans->procedure_cost;
					$total_cash_transactions++;
					if((int)$trans->lite_plan_enabled == 1) {
						$total_in_network_spent += $trans->procedure_cost + $trans->consultation_fees;
					} else {
						$total_in_network_spent += $trans->procedure_cost;
					}
				} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
					if((int)$trans->lite_plan_enabled == 1) {
						$total_in_network_spent += $trans->credit_cost + $trans->consultation_fees;
					} else {
						$total_in_network_spent += $trans->credit_cost;
					}
					$total_search_credits += $trans->credit_cost;
					$total_in_network_spent_credits_transaction = $trans->credit_cost;
					$total_credits_transactions++;
				}

				$refund_text = 'NO';

				if($trans->refunded == 1 && $trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
					$status_text = 'REFUNDED';
					$refund_text = 'YES';
				} else if($trans->health_provider_done == 1 && $trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
					$status_text = 'REMOVED';
					$refund_text = 'YES';
				} else {
					$status_text = FALSE;
				}

				$paid_by_credits = $trans->credit_cost;
				if((int)$trans->lite_plan_enabled == 1) {
					if($consultation_credits == true) {
						$paid_by_credits += $consultation;
					}
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

				$format = array(
					'clinic_name'       => $clinic->Name,
					'clinic_image'      => $clinic->image,
					'amount'            => number_format($total_amount, 2),
					'procedure_cost'    => number_format($bill_amount, 2),
					'clinic_type_and_service' => $clinic_name,
					'procedure'         => $procedure,
					'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
					'member'            => ucwords($customer->Name),
					'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'trans_id'          => $trans->transaction_id,
					'receipt_status'    => $receipt_status,
					'health_provider_status' => $health_provider_status,
					'user_id'           => $trans->UserID,
					'type'              => $payment_type,
					'month'             => date('M', strtotime($trans->date_of_transaction)),
					'day'               => date('d', strtotime($trans->date_of_transaction)),
					'time'              => date('h:ia', strtotime($trans->date_of_transaction)),
					'clinic_type'       => $type,
					'owner_account'     => $sub_account,
					'owner_id'          => $owner_id,
					'sub_account_user_type' => $sub_account_type,
					'co_paid'           => $trans->consultation_fees,
					'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
					'refund_text'       => $refund_text,
					'cash'              => $cash,
					'status_text'       => $status_text,
					'spending_type'     => ucwords($trans->spending_type),
					'consultation'      => (int)$trans->lite_plan_enabled == 1 ?number_format($trans->consultation_fees, 2) : "0.00",
					'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
					'consultation_credits' => $consultation_credits,
					'service_credits'   => $service_credits,
					'transaction_type'  => $transaction_type,
					'logs_lite_plan'    => isset($logs_lite_plan) ? $logs_lite_plan : null,
					'dependent_relationship'    => $dependent_relationship,
					'cap_transaction'   => $half_credits,
				    'cap_per_visit'     => number_format($trans->cap_per_visit, 2),
				    'paid_by_cash'      => number_format($trans->cash_cost, 2),
				    'paid_by_credits'   => number_format($paid_by_credits, 2),
				    "currency_symbol" 	=> $trans->currency_type == "myr" ? "RM" : "S$",
					'files'				=> $doc_files
				);

				array_push($transaction_details, $format);
			}
		}

	}

	$paginate['data'] = $transaction_details;
	$paginate['status'] = true;

	return $paginate;
}

public function getActivityTransactionsold( )
{
	$session = self::checkSession();
	$customer_id = $session->customer_buy_start_id;
	$input = Input::all();

	$start = date('Y-m-d', strtotime($input['start']));
	$end = SpendingInvoiceLibrary::getEndDate($input['end']);
	$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';

	$lite_plan = false;
	$transaction_details = [];
	$e_claim = [];
	$in_network_spent = 0;
	$e_claim_spent = 0;
	$e_claim_pending = 0;
	$health_screening_breakdown = 0;
	$general_practitioner_breakdown = 0;
	$dental_care_breakdown = 0;
	$tcm_breakdown = 0;
	$health_specialist_breakdown = 0;
	$wellness_breakdown = 0;
	$allocation = 0;
	$total_credits = 0;
	$total_cash = 0;
	$deleted_employee_allocation = 0;
	$deleted_transaction_cash = 0;
	$deleted_transaction_credits = 0;

	$total_in_network_transactions = 0;
	$total_deleted_in_network_transactions = 0;
	$total_search_cash = 0;
	$total_search_credits = 0;
	$total_in_network_spent = 0;
	$total_deducted_allocation = 0;
	$break_down_calculation = 0;

	$total_credits_transactions = 0;
	$total_cash_transactions = 0;
	$total_credits_transactions_deleted = 0;
	$total_cash_transactions_deleted = 0;

	$total_in_network_spent_credits_transaction = 0;
	$total_in_network_spent_cash_transaction = 0;
	$total_lite_plan_consultation = 0;

        // get all hr employees, spouse and dependents
	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
	$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);

	$corporate_members = DB::table('corporate_members')
	->where('corporate_id', $account->corporate_id)

	->paginate(10);
        // return $corporate_members;
	$paginate['current_page'] = $corporate_members->getCurrentPage();
	$paginate['from'] = $corporate_members->getFrom();
	$paginate['last_page'] = $corporate_members->getLastPage();
	$paginate['per_page'] = $corporate_members->getPerPage();
	$paginate['to'] = $corporate_members->getTo();
	$paginate['total'] = $corporate_members->getTotal();

	if($spending_type == 'medical') {
		$table_wallet_history = 'wallet_history';
	} else {
		$table_wallet_history = 'wellness_wallet_history';
	}


	foreach ($corporate_members as $key => $member) {
		$employee_allocation = 0;
		$ids = StringHelper::getSubAccountsID($member->user_id);
            // $wallet = DB::table('e_wallet')->where('UserID', $member->user_id)->orderBy('created_at', 'desc')->first();
            // get e claim
		$e_claim_result = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
                            // ->where('status', 1)
		->orderBy('created_at', 'desc')
		->get();

            // get in-network transactions
		$transactions = DB::table('transaction_history')
		->whereIn('UserID', $ids)
		->where('spending_type', $spending_type)
                            // ->where('in_network', 1)
		->where('paid', 1)
		->where('date_of_transaction', '>=', $start)
		->where('date_of_transaction', '<=', $end)
		->orderBy('date_of_transaction', 'desc')
		->get();

            // in-network transactions
		foreach ($transactions as $key => $trans) {
			$consultation_cash = false;
			$consultation_credits = false;
			$service_cash = false;
			$service_credits = false;

			if($trans) {

				if($trans->procedure_cost >= 0 && $trans->paid == 1 || $trans->procedure_cost >= 0 && $trans->paid == "1") {
					if($trans->deleted == 0 || $trans->deleted == "0") {
						$in_network_spent += $trans->credit_cost;
						$total_in_network_transactions++;

						if($trans->lite_plan_enabled == 1) {


							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
								$in_network_spent += floatval($trans->co_paid_amount);
								$consultation_credits = true;
								$service_credits = true;
								$total_lite_plan_consultation += floatval($trans->co_paid_amount);
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
								$in_network_spent += floatval($trans->co_paid_amount);
								$consultation_credits = true;
								$service_credits = true;
								$total_lite_plan_consultation += floatval($trans->co_paid_amount);
							} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
								$total_lite_plan_consultation += floatval($trans->co_paid_amount);
							}
						}
					} else {
						$total_deleted_in_network_transactions++;
						if($trans->lite_plan_enabled == 1) {
							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
								$consultation_credits = true;
								$service_credits = true;
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
								$consultation_credits = true;
								$service_credits = true;
							}
						}
					}


					$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
					$procedure_temp = "";

                        // get services
					if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
					{
                            // get multiple service
						$service_lists = DB::table('transaction_services')
						->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
						->where('transaction_services.transaction_id', $trans->transaction_id)
						->get();

						foreach ($service_lists as $key => $service) {
							if(sizeof($service_lists) - 2 == $key) {
								$procedure_temp .= ucwords($service->Name).' and ';
							} else {
								$procedure_temp .= ucwords($service->Name).',';
							}
							$procedure = rtrim($procedure_temp, ',');
						}
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
						$service_lists = DB::table('clinic_procedure')
						->where('ProcedureID', $trans->ProcedureID)
						->first();
						if($service_lists) {
							$procedure = ucwords($service_lists->Name);
							$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
						} else {
                                // $procedure = "";
							$clinic_name = ucwords($clinic_type->Name);
						}
					}

                        // check if there is a receipt image
					$receipt = DB::table('user_image_receipt')
					->where('transaction_id', $trans->transaction_id)->count();

					if($receipt > 0) {
						$receipt_status = TRUE;
					} else {
						$receipt_status = FALSE;
					}

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
                            // $receipt_status = TRUE;
						$health_provider_status = TRUE;
					} else {
						$health_provider_status = FALSE;
					}

					$type = "";
					if($clinic_type->head == 1 || $clinic_type->head == "1") {
						if($clinic_type->Name == "General Practitioner") {
							$type = "general_practitioner";
							if($trans->deleted == 0) {
								$general_practitioner_breakdown += $trans->credit_cost;
								if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
									$general_practitioner_breakdown += $trans->co_paid_amount;
								}
							}
						} else if($clinic_type->Name == "Dental Care") {
							$type = "dental_care";
							if($trans->deleted == 0) {
								$dental_care_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
							if($trans->deleted == 0) {
								$tcm_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Health Screening") {
							$type = "health_screening";
							if($trans->deleted == 0) {
								$health_screening_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Wellness") {
							$type = "wellness";
							if($trans->deleted == 0) {
								$wellness_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Health Specialist") {
							$type = "health_specialist";
							if($trans->deleted == 0) {
								$health_specialist_breakdown += $trans->credit_cost;
							}
						}
					} else {
						$find_head = DB::table('clinic_types')
						->where('ClinicTypeID', $clinic_type->sub_id)
						->first();
						if($find_head->Name == "General Practitioner") {
							$type = "general_practitioner";
							if($trans->deleted == 0) {
								$general_practitioner_breakdown += $trans->credit_cost;
								if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
									$general_practitioner_breakdown += $trans->co_paid_amount;
								}
							}
						} else if($find_head->Name == "Dental Care") {
							$type = "dental_care";
							if($trans->deleted == 0) {
								$dental_care_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
							if($trans->deleted == 0) {
								$tcm_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Health Screening") {
							$type = "health_screening";
							if($trans->deleted == 0) {
								$health_screening_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Wellness") {
							$type = "wellness";
							if($trans->deleted == 0) {
								$wellness_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Health Specialist") {
							$type = "health_specialist";
							if($trans->deleted == 0) {
								$health_specialist_breakdown += $trans->credit_cost;
							}
						}
					}

                        // check user if it is spouse or dependent
					if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$owner_id = $customer->UserID;
					}

					$total_amount = number_format($trans->procedure_cost, 2);

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
						$payment_type = "Cash";
						$transaction_type = "cash";
						$cash = number_format($trans->procedure_cost, 2);
						if($trans->deleted == 0 || $trans->deleted == "0") {
							$total_cash += $trans->procedure_cost;
						} else if($trans->deleted == 1 || $trans->deleted == "1") {
							$deleted_transaction_cash = $trans->procedure_cost;
                                // $total_cash_transactions_deleted++;
						}
						if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == "1") {
							$total_amount = number_format($trans->procedure_cost + $trans->co_paid_amount, 2);
						}
					} else {
						$payment_type = "Mednefits Credits";
						$transaction_type = "credits";
						$cash = number_format($trans->credit_cost, 2);
						if($trans->deleted == 0 || $trans->deleted == "0") {
							$total_credits += $trans->credit_cost;

						} else if($trans->deleted == 1 || $trans->deleted == "1") {
							$deleted_transaction_credits = $trans->credit_cost;
                                // $total_credits_transactions_deleted++;
						}

						if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == "1") {
							$total_amount = number_format($trans->procedure_cost + $trans->co_paid_amount, 2);
						}
					}

					if( $trans->health_provider_done == 1 && $trans->deleted == 0 || $trans->health_provider_done == "1" && $trans->deleted == "0" ) {
						$total_search_cash += $trans->procedure_cost;
						$total_in_network_spent_cash_transaction += $trans->procedure_cost;
						$total_cash_transactions++;
						if((int)$trans->lite_plan_enabled == 1) {
							$total_in_network_spent += $trans->procedure_cost + $trans->co_paid_amount;
						} else {
							$total_in_network_spent += $trans->procedure_cost;
						}
					} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
						if((int)$trans->lite_plan_enabled == 1) {
							$total_in_network_spent += $trans->credit_cost + $trans->co_paid_amount;
						} else {
							$total_in_network_spent += $trans->credit_cost;
						}
						$total_search_credits += $trans->credit_cost;
						$total_in_network_spent_credits_transaction = $trans->credit_cost;
						$total_credits_transactions++;
					}

					$refund_text = 'NO';

					if($trans->refunded == 1 && $trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
						$status_text = 'REFUNDED';
						$refund_text = 'YES';
					} else if($trans->health_provider_done == 1 && $trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
						$status_text = 'REMOVED';
						$refund_text = 'YES';
					} else {
						$status_text = FALSE;
					}


					$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

					$format = array(
						'clinic_name'       => $clinic->Name,
						'clinic_image'      => $clinic->image,
						'amount'            => $total_amount,
						'procedure_cost'    => number_format($trans->procedure_cost, 2),
						'clinic_type_and_service' => $clinic_name,
						'procedure'         => $procedure,
						'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
						'member'            => ucwords($customer->Name),
						'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
						'trans_id'          => $trans->transaction_id,
						'receipt_status'    => $receipt_status,
						'health_provider_status' => $health_provider_status,
						'user_id'           => $trans->UserID,
						'type'              => $payment_type,
						'month'             => date('M', strtotime($trans->date_of_transaction)),
						'day'               => date('d', strtotime($trans->date_of_transaction)),
						'time'              => date('h:ia', strtotime($trans->date_of_transaction)),
						'clinic_type'       => $type,
						'owner_account'     => $sub_account,
						'owner_id'          => $owner_id,
						'sub_account_user_type' => $sub_account_type,
						'co_paid'           => $trans->co_paid_amount,
						'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
						'refund_text'       => $refund_text,
						'cash'              => $cash,
						'status_text'       => $status_text,
						'spending_type'     => ucwords($trans->spending_type),
						'consultation'      => (int)$trans->lite_plan_enabled == 1 ?number_format($trans->co_paid_amount, 2) : "0.00",
						'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
						'consultation_credits' => $consultation_credits,
						'service_credits'   => $service_credits,
						'transaction_type'  => $transaction_type,
						'logs_lite_plan'    => isset($logs_lite_plan) ? $logs_lite_plan : null
					);

					array_push($transaction_details, $format);
				}
			}
		}

            // e-claim transactions
		foreach($e_claim_result as $key => $res) {
			if($res->status == 0) {
				$status_text = 'Pending';
				$e_claim_pending += $res->amount;
			} else if($res->status == 1) {
				$status_text = 'Approved';
				$e_claim_spent += $res->amount;
			} else if($res->status == 2) {
				$status_text = 'Rejected';
			} else {
				$status_text = 'Pending';
			}

			if($res->status == 1) {
				$member = DB::table('user')->where('UserID', $res->user_id)->first();

                    // check user if it is spouse or dependent
				if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
					$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
					$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
					$sub_account = ucwords($temp_account->Name);
					$sub_account_type = $temp_sub->user_type;
					$owner_id = $temp_sub->owner_id;
				} else {
					$sub_account = FALSE;
					$sub_account_type = FALSE;
					$owner_id = $member->UserID;
				}

                    // get docs
				$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

				if(sizeof($docs) > 0) {
					$e_claim_receipt_status = TRUE;
					$doc_files = [];
					foreach ($docs as $key => $doc) {
						if($doc->file_type == "pdf" || $doc->file_type == "xls") {
							$fil = url('').'/receipts/'.$doc->doc_file;
						} else if($doc->file_type == "image") {
							$fil = $doc->doc_file;
						}

						$temp_doc = array(
							'e_claim_doc_id'    => $doc->e_claim_doc_id,
							'e_claim_id'            => $doc->e_claim_id,
							'file'                      => $fil,
							'file_type'             => $doc->file_type
						);

						array_push($doc_files, $temp_doc);
					}
				} else {
					$e_claim_receipt_status = FALSE;
					$doc_files = FALSE;
				}

				$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
				$temp = array(
					'status'            => $res->status,
					'status_text'       => $status_text,
					'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
					'approved_date'     => date('d F Y', strtotime($res->approved_date)),
					'time'              => $res->time,
					'service'           => $res->service,
					'merchant'          => $res->merchant,
					'amount'            => $res->amount,
					'member'            => ucwords($member->Name),
					'type'              => 'E-Claim',
					'transaction_id'    => 'MNF'.$id,
					'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
					'owner_id'          => $owner_id,
					'sub_account_type'  => $sub_account_type,
					'sub_account'       => $sub_account,
					'month'             => date('M', strtotime($res->approved_date)),
					'day'               => date('d', strtotime($res->approved_date)),
					'time'              => date('h:ia', strtotime($res->approved_date)),
					'receipt_status'    => $e_claim_receipt_status,
					'files'             => $doc_files,
					'spending_type'     => ucwords($res->spending_type)
				);

				array_push($e_claim, $temp);
			}
		}
	}

	$paginate['data'] = array(
		'in_network_transactions'   => $transaction_details,
		'e_claim_transactions'      => $e_claim
	);

	return $paginate;
}
public function getHrActivityOld( )
{
	$input = Input::all();
	$START = time();
	// $start = date('Y-m-d', strtotime($input['start']));
	$end = SpendingInvoiceLibrary::getEndDate($input['end']);
	$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
	$paginate = [];

	$session = self::checkSession();
	$e_claim = [];
	$transaction_details = [];

	$in_network_spent = 0;
	$e_claim_spent = 0;
	$e_claim_pending = 0;
	$health_screening_breakdown = 0;
	$general_practitioner_breakdown = 0;
	$dental_care_breakdown = 0;
	$tcm_breakdown = 0;
	$health_specialist_breakdown = 0;
	$wellness_breakdown = 0;
	$allocation = 0;
	$total_credits = 0;
	$total_cash = 0;
	$deleted_employee_allocation = 0;
	$deleted_transaction_cash = 0;
	$deleted_transaction_credits = 0;
	$total_e_claim_spent = 0;

	$total_in_network_transactions = 0;
	$total_deleted_in_network_transactions = 0;
	$total_search_cash = 0;
	$total_search_credits = 0;
	$total_in_network_spent = 0;
	$total_deducted_allocation = 0;
	$break_down_calculation = 0;

	$total_credits_transactions = 0;
	$total_cash_transactions = 0;
	$total_credits_transactions_deleted = 0;
	$total_cash_transactions_deleted = 0;

	$total_in_network_spent_credits_transaction = 0;
	$total_in_network_spent_cash_transaction = 0;
	$total_lite_plan_consultation = 0;
	$lite_plan = false;

        // get all hr employees, spouse and dependents
	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $session->customer_buy_start_id)->first();
	$lite_plan = StringHelper::liteCompanyPlanStatus($session->customer_buy_start_id);
	$wallet = DB::table('customer_credits')->where('customer_id', $session->customer_buy_start_id)->first();
	$corporate_members = DB::table('corporate_members')
	->where('corporate_id', $account->corporate_id)
	->paginate(10);

	$paginate['current_page'] = $corporate_members->getCurrentPage();
	$paginate['from'] = $corporate_members->getFrom();
	$paginate['last_page'] = $corporate_members->getLastPage();
	$paginate['per_page'] = $corporate_members->getPerPage();
	$paginate['to'] = $corporate_members->getTo();
	$paginate['total'] = $corporate_members->getTotal();
    
    $start = date('Y-m-d', strtotime($wallet->created_at));

	if($spending_type == 'medical') {
		$table_wallet_history = 'wallet_history';
	} else {
		$table_wallet_history = 'wellness_wallet_history';
	}

	foreach ($corporate_members as $key => $member) {
		$employee_allocation = 0;
		$ids = StringHelper::getSubAccountsID($member->user_id);
		$wallet = DB::table('e_wallet')->where('UserID', $member->user_id)->orderBy('created_at', 'desc')->first();

            // get wallet reset date
		$wallet_reset = PlanHelper::getResetWalletDate($member->user_id, $spending_type, $start, $input['end'], 'employee');
		if($wallet_reset) {
			$wallet_start_date = $wallet_reset;
		} else {
			$wallet_start_date = $start;
		}

            // get e claim
		$e_claim_result = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $wallet_start_date)
		->where('created_at', '<=', $input['end'])
                            // ->where('status', 1)
		->orderBy('created_at', 'desc')
		->get();

            // get employee allocation
		$employee_allocation = DB::table($table_wallet_history)
		->where('wallet_id', $wallet->wallet_id)
		->where('created_at', '>=', date('Y-m-d', strtotime($wallet_start_date)))
		->where('created_at', '<=', date('Y-m-d', strtotime($input['end'])))
		->where('logs', 'added_by_hr')
		->sum('credit');

		$deducted_allocation = DB::table('e_wallet')
		->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
		->where('e_wallet.UserID', $member->user_id)
		->where($table_wallet_history.'.created_at', '>=', date('Y-m-d', strtotime($wallet_start_date)))
		->where($table_wallet_history.'.created_at', '<=', date('Y-m-d', strtotime($input['end'])))
		->whereIn($table_wallet_history.'.logs', ['deducted_by_hr'])
		->sum($table_wallet_history.'.credit');

		$total_deducted_allocation += $deducted_allocation;

		$allocation += $employee_allocation;
		if($member->removed_status == 1) {
			$deleted_employee_allocation += $employee_allocation - $deducted_allocation;
		}

            // get in-network transactions
		$transactions = DB::table('transaction_history')
		->whereIn('UserID', $ids)
		->where('spending_type', $spending_type)
                            // ->where('in_network', 1)
		->where('paid', 1)
		->where('date_of_transaction', '>=', $start)
		->where('date_of_transaction', '<=', $end)
		->orderBy('date_of_transaction', 'desc')
		->get();

            // in-network transactions
		foreach ($transactions as $key => $trans) {
			$consultation_cash = false;
			$consultation_credits = false;
			$service_cash = false;
			$service_credits = false;

			if($trans) {

				if($trans->procedure_cost >= 0 && $trans->paid == 1 || $trans->procedure_cost >= 0 && $trans->paid == "1") {
					if($trans->deleted == 0 || $trans->deleted == "0") {
						$in_network_spent += $trans->credit_cost;

                        if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {
							$total_in_network_transactions++;
                        }

						if($trans->lite_plan_enabled == 1) {
							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
								$in_network_spent += floatval($trans->consultation_fees);
								$consultation_credits = true;
								$service_credits = true;
								$total_lite_plan_consultation += floatval($trans->consultation_fees);
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
								$in_network_spent += floatval($trans->consultation_fees);
								$consultation_credits = true;
								$service_credits = true;
								$total_lite_plan_consultation += floatval($trans->consultation_fees);
							} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
								$total_lite_plan_consultation += floatval($trans->consultation_fees);
							}
						}
					} else {
						$total_deleted_in_network_transactions++;
						if($trans->lite_plan_enabled == 1) {
							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
								$consultation_credits = true;
								$service_credits = true;
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
								$consultation_credits = true;
								$service_credits = true;
							}
						}
					}


					$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
					$procedure_temp = "";

                        // get services
					if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
					{
                            // get multiple service
						$service_lists = DB::table('transaction_services')
						->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
						->where('transaction_services.transaction_id', $trans->transaction_id)
						->get();

						foreach ($service_lists as $key => $service) {
							if(sizeof($service_lists) - 2 == $key) {
								$procedure_temp .= ucwords($service->Name).' and ';
							} else {
								$procedure_temp .= ucwords($service->Name).',';
							}
							$procedure = rtrim($procedure_temp, ',');
						}
						$clinic_name = $procedure;
					} else {
						$service_lists = DB::table('clinic_procedure')
						->where('ProcedureID', $trans->ProcedureID)
						->first();
						if($service_lists) {
							$procedure = ucwords($service_lists->Name);
							$clinic_name = $procedure;
						} else {
                                // $procedure = "";
							$clinic_name = ucwords($clinic_type->Name);
						}
					}

                        // check if there is a receipt image
					$receipt = DB::table('user_image_receipt')
					->where('transaction_id', $trans->transaction_id)
					->get();
					$receipt_data = null;
					if(sizeof($receipt) > 0) {
						$receipt_status = TRUE;
						$receipt_data = $receipt;
					} else {
						$receipt_status = FALSE;
					}

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
                            // $receipt_status = TRUE;
						$health_provider_status = TRUE;
					} else {
						$health_provider_status = FALSE;
					}

					$type = "";
					if($clinic_type->head == 1 || $clinic_type->head == "1") {
						if($clinic_type->Name == "General Practitioner") {
							$type = "general_practitioner";
							if($trans->deleted == 0) {
								$general_practitioner_breakdown += $trans->credit_cost;
								if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
									$general_practitioner_breakdown += $trans->consultation_fees;
								}
							}
						} else if($clinic_type->Name == "Dental Care") {
							$type = "dental_care";
							if($trans->deleted == 0) {
								$dental_care_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
							if($trans->deleted == 0) {
								$tcm_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Health Screening") {
							$type = "health_screening";
							if($trans->deleted == 0) {
								$health_screening_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Wellness") {
							$type = "wellness";
							if($trans->deleted == 0) {
								$wellness_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Health Specialist") {
							$type = "health_specialist";
							if($trans->deleted == 0) {
								$health_specialist_breakdown += $trans->credit_cost;
							}
						}
					} else {
						$find_head = DB::table('clinic_types')
						->where('ClinicTypeID', $clinic_type->sub_id)
						->first();
						if($find_head->Name == "General Practitioner") {
							$type = "general_practitioner";
							if($trans->deleted == 0) {
								$general_practitioner_breakdown += $trans->credit_cost;
								if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
									$general_practitioner_breakdown += $trans->consultation_fees;
								}
							}
						} else if($find_head->Name == "Dental Care") {
							$type = "dental_care";
							if($trans->deleted == 0) {
								$dental_care_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
							if($trans->deleted == 0) {
								$tcm_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Health Screening") {
							$type = "health_screening";
							if($trans->deleted == 0) {
								$health_screening_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Wellness") {
							$type = "wellness";
							if($trans->deleted == 0) {
								$wellness_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Health Specialist") {
							$type = "health_specialist";
							if($trans->deleted == 0) {
								$health_specialist_breakdown += $trans->credit_cost;
							}
						}
					}

                        // check user if it is spouse or dependent
					if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$owner_id = $customer->UserID;
					}

					$total_amount = number_format($trans->procedure_cost, 2);

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
						$payment_type = "Cash";
						$transaction_type = "cash";
						$cash = number_format($trans->procedure_cost, 2);
						if($trans->deleted == 0 || $trans->deleted == "0") {
							$total_cash += $trans->procedure_cost;
						} else if($trans->deleted == 1 || $trans->deleted == "1") {
							$deleted_transaction_cash = $trans->procedure_cost;
                                // $total_cash_transactions_deleted++;
						}
						if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == "1") {
							$total_amount = number_format($trans->procedure_cost + $trans->consultation_fees, 2);
						}
					} else {
						$payment_type = "Mednefits Credits";
						$transaction_type = "credits";
						$cash = number_format($trans->credit_cost, 2);
						if($trans->deleted == 0 || $trans->deleted == "0") {
							$total_credits += $trans->credit_cost;

						} else if($trans->deleted == 1 || $trans->deleted == "1") {
							$deleted_transaction_credits = $trans->credit_cost;
                                // $total_credits_transactions_deleted++;
						}

						if($lite_plan && $trans->lite_plan_enabled == 1 || $lite_plan && $trans->lite_plan_enabled == "1") {
							$total_amount = number_format($trans->procedure_cost + $trans->consultation_fees, 2);
						}
					}

					if( $trans->health_provider_done == 1 && $trans->deleted == 0 || $trans->health_provider_done == "1" && $trans->deleted == "0" ) {
						$total_search_cash += $trans->procedure_cost;
						$total_in_network_spent_cash_transaction += $trans->procedure_cost;
						$total_cash_transactions++;
						if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {
							if((int)$trans->lite_plan_enabled == 1) {
								$total_in_network_spent += $trans->procedure_cost + $trans->consultation_fees;
							} else {
								$total_in_network_spent += $trans->procedure_cost;
							}
						}
					} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
						if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {
							if((int)$trans->lite_plan_enabled == 1) {
								$total_in_network_spent += $trans->credit_cost + $trans->consultation_fees;
							} else {
								$total_in_network_spent += $trans->credit_cost;
							}
						}
						$total_search_credits += $trans->credit_cost;
						$total_in_network_spent_credits_transaction = $trans->credit_cost;
						$total_credits_transactions++;
					}

					$refund_text = 'NO';

					if($trans->refunded == 1 && $trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
						$status_text = 'REFUNDED';
						$refund_text = 'YES';
					} else if($trans->health_provider_done == 1 && $trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
						$status_text = 'REMOVED';
						$refund_text = 'YES';
					} else {
						$status_text = FALSE;
					}

                        if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {
                            $transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

                            $format = array(
                                'clinic_name'       => $clinic->Name,
                                'clinic_image'      => $clinic->image,
                                'amount'            => $total_amount,
                                'procedure_cost'    => number_format($trans->procedure_cost, 2),
                                'clinic_type_and_service' => $clinic_name,
                                'procedure'         => $procedure,
                                'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
                                'member'            => ucwords($customer->Name),
                                'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
                                'trans_id'          => $trans->transaction_id,
                                'receipt_status'    => $receipt_status,
                                'files'     => $receipt_data,
                                'health_provider_status' => $health_provider_status,
                                'user_id'           => $trans->UserID,
                                'type'              => $payment_type,
                                'month'             => date('M', strtotime($trans->date_of_transaction)),
                                'day'               => date('d', strtotime($trans->date_of_transaction)),
                                'time'              => date('h:ia', strtotime($trans->date_of_transaction)),
                                'clinic_type'       => $type,
                                'owner_account'     => $sub_account,
                                'owner_id'          => $owner_id,
                                'sub_account_user_type' => $sub_account_type,
                                'co_paid'           => $trans->consultation_fees,
                                'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
                                'refund_text'       => $refund_text,
                                'cash'              => $cash,
                                'status_text'       => $status_text,
                                'spending_type'     => ucwords($trans->spending_type),
                                'consultation'      => (int)$trans->lite_plan_enabled == 1 ?number_format($trans->co_paid_amount, 2) : "0.00",
                                'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
                                'consultation_credits' => $consultation_credits,
                                'service_credits'   => $service_credits,
                                'transaction_type'  => $transaction_type,
                                'logs_lite_plan'    => isset($logs_lite_plan) ? $logs_lite_plan : null
                            );
                            array_push($transaction_details, $format);
                        }
				}
			}
		}

            // e-claim transactions
		foreach($e_claim_result as $key => $res) {
			if($res->status == 0) {
				$status_text = 'Pending';
				$e_claim_pending += $res->amount;
			} else if($res->status == 1) {
				$status_text = 'Approved';
				$e_claim_spent += $res->amount;
				$total_e_claim_spent += $res->amount;
			} else if($res->status == 2) {
				$status_text = 'Rejected';
			} else {
				$status_text = 'Pending';
			}

			if(date('Y-m-d', strtotime($res->created_at)) >= $start && date('Y-m-d', strtotime($res->created_at)) <= $end) {
				if($res->status == 1) {

					$member = DB::table('user')->where('UserID', $res->user_id)->first();

                        // check user if it is spouse or dependent
					if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$owner_id = $member->UserID;
					}

                        // get docs
					$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

					if(sizeof($docs) > 0) {
						$e_claim_receipt_status = TRUE;
						$doc_files = [];
						foreach ($docs as $key => $doc) {
							if($doc->file_type == "pdf" || $doc->file_type == "xls") {
								$fil = url('').'/receipts/'.$doc->doc_file;
							} else if($doc->file_type == "image") {
								$fil = $doc->doc_file;
							}

							$temp_doc = array(
								'e_claim_doc_id'    => $doc->e_claim_doc_id,
								'e_claim_id'            => $doc->e_claim_id,
								'file'                      => $fil,
								'file_type'             => $doc->file_type
							);

							array_push($doc_files, $temp_doc);
						}
					} else {
						$e_claim_receipt_status = FALSE;
						$doc_files = FALSE;
					}

					$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
					$temp = array(
						'status'            => $res->status,
						'status_text'       => $status_text,
						'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
						'approved_date'     => date('d F Y', strtotime($res->approved_date)),
						'time'              => $res->time,
						'service'           => $res->service,
						'merchant'          => $res->merchant,
						'amount'            => $res->amount,
						'member'            => ucwords($member->Name),
						'type'              => 'E-Claim',
						'transaction_id'    => 'MNF'.$id,
						'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
						'owner_id'          => $owner_id,
						'sub_account_type'  => $sub_account_type,
						'sub_account'       => $sub_account,
						'month'             => date('M', strtotime($res->approved_date)),
						'day'               => date('d', strtotime($res->approved_date)),
						'time'              => date('h:ia', strtotime($res->approved_date)),
						'receipt_status'    => $e_claim_receipt_status,
						'files'             => $doc_files,
						'spending_type'     => ucwords($res->spending_type)
					);

					array_push($e_claim, $temp);
				}
			}

		}
	}

	$total_spent = $e_claim_spent + $in_network_spent;

	$in_network_breakdown = array(
		'general_practitioner_breakdown' => $general_practitioner_breakdown,
		'health_screening_breakdown'     => $health_screening_breakdown,
		'dental_care_breakdown'          => $dental_care_breakdown,
		'tcm_breakdown'                  => $tcm_breakdown,
		'health_specialist_breakdown'    => $health_specialist_breakdown,
		'wellness_breakdown'             => $wellness_breakdown
	);


        // sort in-network transaction
        usort($transaction_details, function($a, $b) {
            return strtotime($b['date_of_transaction']) - strtotime($a['date_of_transaction']);
        });

	$grand_total_credits_cash = $total_credits + $total_cash - $deleted_transaction_credits - $deleted_transaction_cash;

	$temp_allocation = $allocation - $deleted_employee_allocation;
	$balance = $temp_allocation - $total_spent - $total_deducted_allocation;

	$total_transactions = $total_credits_transactions + $total_cash_transactions;

	$total_transaction_spent = $total_in_network_spent_credits_transaction + $total_in_network_spent_cash_transaction;
	$allocation_final = $allocation - $deleted_employee_allocation - $total_deducted_allocation;

	$paginate['data'] = array(
		'deleted_employee_allocation' => $deleted_employee_allocation,
		'total_spent'       => number_format($total_spent, 2),
		'total_spent_format_number'       => $total_spent,
		'balance'           => number_format($balance, 2),
		'pending_e_claim_amount' => number_format($e_claim_pending, 2),
		'in_network_spent'  => number_format($in_network_spent, 2),
		'e_claim_spent'     => number_format($e_claim_spent, 2),
		'in_network_breakdown' => $in_network_breakdown,
		'in_network_transactions' => $transaction_details,
		'e_claim_transactions'  => $e_claim,
		'allocation'        => number_format($allocation_final, 2),
		'total_in_network_credits_cash' => $grand_total_credits_cash > 0 ? number_format($grand_total_credits_cash, 2) : number_format(0, 2),
		'deleted_transaction_cash'  => $deleted_transaction_cash,
		'deleted_transaction_credits' => $deleted_transaction_credits,
		'total_credits_cash' => $total_credits,
		'in_network_spending_format_number' => $in_network_spent,
		'e_claim_spending_format_number' => $total_e_claim_spent,
		'total_in_network_spent'    => number_format($total_in_network_spent, 2),
		'total_in_network_spent_format_number'    => $total_in_network_spent,
		'total_lite_plan_consultation'      => floatval($total_lite_plan_consultation),
		'total_cash'            => $total_search_cash,
		'total_credits'         => $total_search_credits,
		'total_deleted_in_network_transactions' => $total_deleted_in_network_transactions,
		'total_in_network_transactions' => $total_in_network_transactions,
            // 'total_in_network_transactions'    => $total_transactions,
		'total_transaction_spent'       => $total_transaction_spent,
		'total_in_network_spent_credits_transaction' => $total_in_network_spent_credits_transaction,
		'total_in_network_spent_cash_transaction'   => $total_in_network_spent_cash_transaction,
		'spending_type' => $spending_type,
		'execution_time'    => time() - $START.' seconds',
		'lite_plan'     => $lite_plan
	);


	return $paginate;

}

public function getHrActivity( )
{
	$input = Input::all();
	$start = date('Y-m-d', strtotime($input['start']));
	$end = SpendingInvoiceLibrary::getEndDate($input['end']);
	$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
	$paginate = [];

	$session = self::checkSession();
	$e_claim = [];
	$transaction_details = [];

	$in_network_spent = 0;
	$e_claim_spent = 0;
	$e_claim_pending = 0;
	$health_screening_breakdown = 0;
	$general_practitioner_breakdown = 0;
	$dental_care_breakdown = 0;
	$tcm_breakdown = 0;
	$health_specialist_breakdown = 0;
	$wellness_breakdown = 0;
	$allocation = 0;
	$total_credits = 0;
	$total_cash = 0;
	$deleted_employee_allocation = 0;
	$deleted_transaction_cash = 0;
	$deleted_transaction_credits = 0;
	$total_e_claim_spent = 0;

	$total_in_network_transactions = 0;
	$total_deleted_in_network_transactions = 0;
	$total_search_cash = 0;
	$total_search_credits = 0;
	$total_in_network_spent = 0;
	$total_deducted_allocation = 0;
	$break_down_calculation = 0;

	$total_credits_transactions = 0;
	$total_cash_transactions = 0;
	$total_credits_transactions_deleted = 0;
	$total_cash_transactions_deleted = 0;

	$total_in_network_spent_credits_transaction = 0;
	$total_in_network_spent_cash_transaction = 0;
	$total_lite_plan_consultation = 0;
	$lite_plan = false;

        // get all hr employees, spouse and dependents
	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $session->customer_buy_start_id)->first();
	$lite_plan = StringHelper::liteCompanyPlanStatus($session->customer_buy_start_id);
	$corporate_members = DB::table('corporate_members')
	->where('corporate_id', $account->corporate_id)
	->paginate(10);

	$paginate['current_page'] = $corporate_members->getCurrentPage();
	$paginate['from'] = $corporate_members->getFrom();
	$paginate['last_page'] = $corporate_members->getLastPage();
	$paginate['per_page'] = $corporate_members->getPerPage();
	$paginate['to'] = $corporate_members->getTo();
	$paginate['total'] = $corporate_members->getTotal();
    
    // $start = date('Y-m-d', strtotime($wallet->created_at));

	if($spending_type == 'medical') {
		$table_wallet_history = 'wallet_history';
	} else {
		$table_wallet_history = 'wellness_wallet_history';
	}

	foreach ($corporate_members as $key => $member) {
		$ids = StringHelper::getSubAccountsID($member->user_id);
            // get e claim
		$e_claim_result = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
        ->where('status', 1)
		->orderBy('created_at', 'desc')
		->get();

        // get in-network transactions
		$transactions = DB::table('transaction_history')
		->whereIn('UserID', $ids)
		->where('spending_type', $spending_type)
		->where('paid', 1)
		->where('date_of_transaction', '>=', $start)
		->where('date_of_transaction', '<=', $end)
		->orderBy('date_of_transaction', 'desc')
		->get();

        // in-network transactions
		foreach ($transactions as $key => $trans) {
			$consultation_cash = false;
			$consultation_credits = false;
			$service_cash = false;
			$service_credits = false;
			$consultation = 0;

			if($trans) {

				if($trans->procedure_cost >= 0 && $trans->paid == 1 || $trans->procedure_cost >= 0 && $trans->paid == "1") {
					if($trans->deleted == 0 || $trans->deleted == "0") {
						$in_network_spent += $trans->credit_cost;
						$total_in_network_transactions++;

						if($trans->lite_plan_enabled == 1) {


							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
								$in_network_spent += floatval($logs_lite_plan->credit);
								$consultation_credits = true;
								$service_credits = true;
								$total_lite_plan_consultation += floatval($trans->consultation_fees);
								$consultation = floatval($logs_lite_plan->credit);
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
								$in_network_spent += floatval($logs_lite_plan->credit);
								$consultation_credits = true;
								$service_credits = true;
								$total_lite_plan_consultation += floatval($trans->consultation_fees);
								$consultation = floatval($logs_lite_plan->credit);
							} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
								$total_lite_plan_consultation += floatval($trans->consultation_fees);
								$consultation = floatval($trans->consultation_fees);
							}
						}
					} else {
						$total_deleted_in_network_transactions++;
						if($trans->lite_plan_enabled == 1) {
							$logs_lite_plan = DB::table($table_wallet_history)
							->where('logs', 'deducted_from_mobile_payment')
							->where('lite_plan_enabled', 1)
							->where('id', $trans->transaction_id)
							->first();

							if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
								$consultation_credits = true;
								$service_credits = true;
							} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
								$consultation_credits = true;
								$service_credits = true;
							}
						}
					}


					$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
					$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
					$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
					$procedure_temp = "";
					$procedure = "";

	                        // get services
					if((int)$trans->multiple_service_selection == 1)
					{
	                            // get multiple service
						$service_lists = DB::table('transaction_services')
						->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
						->where('transaction_services.transaction_id', $trans->transaction_id)
						->get();

						foreach ($service_lists as $key => $service) {
							if(sizeof($service_lists) - 2 == $key) {
								$procedure_temp .= ucwords($service->Name).' and ';
							} else {
								$procedure_temp .= ucwords($service->Name).',';
							}
							$procedure = rtrim($procedure_temp, ',');
						}
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
						$service_lists = DB::table('clinic_procedure')
						->where('ProcedureID', $trans->ProcedureID)
						->first();
						if($service_lists) {
							$procedure = ucwords($service_lists->Name);
							$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
						} else {
	                                // $procedure = "";
							$clinic_name = ucwords($clinic_type->Name);
						}
					}

	                        // check if there is a receipt image
					$receipts = DB::table('user_image_receipt')
								->where('transaction_id', $trans->transaction_id)
								->get();

					$doc_files = [];
					if(sizeof($receipts) > 0) {
						foreach ($receipts as $key => $doc) {
							if($doc->type == "pdf" || $doc->type == "xls") {
								if(StringHelper::Deployment()==1){
								   $fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->file;
								} else {
								   $fil = url('').'/receipts/'.$doc->file;
								}
							} else if($doc->type == "image") {
								// $fil = FileHelper::formatImageAutoQuality($doc->file);
								$fil = FileHelper::formatImageAutoQualityCustomer($doc->file, 40);
							}

							$temp_doc = array(
								'tranasaction_doc_id'    => $doc->image_receipt_id,
								'transaction_id'            => $doc->transaction_id,
								'file'                      => $fil,
								'file_type'             => $doc->type
							);

							array_push($doc_files, $temp_doc);
						}
						$receipt_status = TRUE;
					} else {
						$receipt_status = FALSE;
					}

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
	                            // $receipt_status = TRUE;
						$health_provider_status = TRUE;
					} else {
						$health_provider_status = FALSE;
					}

					$type = "";
					if($clinic_type->head == 1 || $clinic_type->head == "1") {
						if($clinic_type->Name == "General Practitioner") {
							$type = "general_practitioner";
							if($trans->deleted == 0) {
								$general_practitioner_breakdown += $trans->credit_cost;
								if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
									$general_practitioner_breakdown += $trans->consultation_fees;
								}
							}
						} else if($clinic_type->Name == "Dental Care") {
							$type = "dental_care";
							if($trans->deleted == 0) {
								$dental_care_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
							if($trans->deleted == 0) {
								$tcm_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Health Screening") {
							$type = "health_screening";
							if($trans->deleted == 0) {
								$health_screening_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Wellness") {
							$type = "wellness";
							if($trans->deleted == 0) {
								$wellness_breakdown += $trans->credit_cost;
							}
						} else if($clinic_type->Name == "Health Specialist") {
							$type = "health_specialist";
							if($trans->deleted == 0) {
								$health_specialist_breakdown += $trans->credit_cost;
							}
						}
					} else {
						$find_head = DB::table('clinic_types')
						->where('ClinicTypeID', $clinic_type->sub_id)
						->first();
						if($find_head->Name == "General Practitioner") {
							$type = "general_practitioner";
							if($trans->deleted == 0) {
								$general_practitioner_breakdown += $trans->credit_cost;
								if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
									$general_practitioner_breakdown += $trans->consultation_fees;
								}
							}
						} else if($find_head->Name == "Dental Care") {
							$type = "dental_care";
							if($trans->deleted == 0) {
								$dental_care_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Traditional Chinese Medicine") {
							$type = "tcm";
							if($trans->deleted == 0) {
								$tcm_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Health Screening") {
							$type = "health_screening";
							if($trans->deleted == 0) {
								$health_screening_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Wellness") {
							$type = "wellness";
							if($trans->deleted == 0) {
								$wellness_breakdown += $trans->credit_cost;
							}
						} else if($find_head->Name == "Health Specialist") {
							$type = "health_specialist";
							if($trans->deleted == 0) {
								$health_specialist_breakdown += $trans->credit_cost;
							}
						}
					}

	                        // check user if it is spouse or dependent
					if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
						$dependent_relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$dependent_relationship = FALSE;
						$owner_id = $customer->UserID;
					}

					$half_credits = false;
					$total_amount = number_format($trans->procedure_cost, 2);

					if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
						$payment_type = "Cash";
						$transaction_type = "cash";
						if((int)$trans->lite_plan_enabled == 1) {
	              if((int)$trans->half_credits == 1) {
	                $total_amount = $trans->credit_cost + $trans->consultation_fees;
	                $cash = $transation->cash_cost;
	              } else {
	                $total_amount = $trans->procedure_cost;
	                $total_amount = $trans->procedure_cost + $trans->consultation_fees;
	                $cash = $trans->procedure_cost;
	              }
	          } else {
	            if((int)$trans->half_credits == 1) {
	              $cash = $trans->cash_cost;
	            } else {
	              $cash = $trans->procedure_cost;
	            }
	          }
					} else {
						if($trans->credit_cost > 0 && $trans->cash_cost > 0) {
					      $payment_type = 'Mednefits Credits + Cash';
					      $half_credits = true;
					    } else {
					      $payment_type = 'Mednefits Credits';
					    }
						$transaction_type = "credits";
						// $cash = number_format($trans->credit_cost, 2);
						if((int)$trans->lite_plan_enabled == 1) {
	              if((int)$trans->half_credits == 1) {
	                $total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
	                $cash = $trans->cash_cost;
	              } else {
	                $total_amount = $trans->credit_cost + $trans->cash_cost + $trans->consultation_fees;
	                // $total_amount = $trans->procedure_cost;
	                if($trans->credit_cost > 0) {
	                  $cash = 0;
	                } else {
	                  $cash = $trans->procedure_cost - $trans->consultation_fees;
	                }
	              }
	            } else {
	              $total_amount = $trans->procedure_cost;
	              if((int)$trans->half_credits == 1) {
	                $cash = $trans->cash_cost;
	              } else {
	                if($trans->credit_cost > 0) {
	                  $cash = 0;
	                } else {
	                  $cash = $trans->procedure_cost;
	                }
	              }
	            }
					}

					$bill_amount = 0;
					if((int)$trans->half_credits == 1) {
						if((int)$trans->lite_plan_enabled == 1) {
							if((int)$trans->health_provider_done == 1) {
								$bill_amount = $trans->procedure_cost;
							} else {
								$bill_amount = $trans->procedure_cost - $trans->consultation_fees;
							}
						} else {
							$bill_amount = 	$trans->procedure_cost;
						}
					} else {
						if((int)$trans->lite_plan_enabled == 1) {
							if((int)$trans->lite_plan_use_credits == 1) {
								$bill_amount = 	$trans->procedure_cost;
							} else {
								if((int)$trans->health_provider_done == 1) {
									$bill_amount = 	$trans->procedure_cost;
								} else {
									$bill_amount = 	$trans->credit_cost + $trans->cash_cost;
								}
							}
						} else {
							$bill_amount = 	$trans->procedure_cost;
						}
					}

					if( $trans->health_provider_done == 1 && $trans->deleted == 0 || $trans->health_provider_done == "1" && $trans->deleted == "0" ) {
						$total_search_cash += $trans->procedure_cost;
						$total_in_network_spent_cash_transaction += $trans->procedure_cost;
						$total_cash_transactions++;
						if((int)$trans->lite_plan_enabled == 1) {
							$total_in_network_spent += $trans->procedure_cost + $trans->consultation_fees;
						} else {
							$total_in_network_spent += $trans->procedure_cost;
						}
					} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
						if((int)$trans->lite_plan_enabled == 1) {
							$total_in_network_spent += $trans->credit_cost + $trans->consultation_fees;
						} else {
							$total_in_network_spent += $trans->credit_cost;
						}
						$total_search_credits += $trans->credit_cost;
						$total_in_network_spent_credits_transaction = $trans->credit_cost;
						$total_credits_transactions++;
					}

					$refund_text = 'NO';

					if($trans->refunded == 1 && $trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
						$status_text = 'REFUNDED';
						$refund_text = 'YES';
					} else if($trans->health_provider_done == 1 && $trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
						$status_text = 'REMOVED';
						$refund_text = 'YES';
					} else {
						$status_text = FALSE;
					}

					$paid_by_credits = $trans->credit_cost;
					if((int)$trans->lite_plan_enabled == 1) {
						if($consultation_credits == true) {
							$paid_by_credits += $consultation;
						}
					}

					$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

					$format = array(
						'clinic_name'       => $clinic->Name,
						'clinic_image'      => $clinic->image,
						'amount'            => number_format($total_amount, 2),
						'procedure_cost'    => number_format($bill_amount, 2),
						'clinic_type_and_service' => $clinic_name,
						'procedure'         => $procedure,
						'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
						'member'            => ucwords($customer->Name),
						'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
						'trans_id'          => $trans->transaction_id,
						'receipt_status'    => $receipt_status,
						'health_provider_status' => $health_provider_status,
						'user_id'           => $trans->UserID,
						'type'              => $payment_type,
						'month'             => date('M', strtotime($trans->date_of_transaction)),
						'day'               => date('d', strtotime($trans->date_of_transaction)),
						'time'              => date('h:ia', strtotime($trans->date_of_transaction)),
						'clinic_type'       => $type,
						'owner_account'     => $sub_account,
						'owner_id'          => $owner_id,
						'sub_account_user_type' => $sub_account_type,
						'co_paid'           => $trans->consultation_fees,
						'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
						'refund_text'       => $refund_text,
						'cash'              => $cash,
						'status_text'       => $status_text,
						'spending_type'     => ucwords($trans->spending_type),
						'consultation'      => (int)$trans->lite_plan_enabled == 1 ?number_format($trans->consultation_fees, 2) : "0.00",
						'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
						'consultation_credits' => $consultation_credits,
						'service_credits'   => $service_credits,
						'transaction_type'  => $transaction_type,
						'logs_lite_plan'    => isset($logs_lite_plan) ? $logs_lite_plan : null,
						'dependent_relationship'    => $dependent_relationship,
						'cap_transaction'   => $half_credits,
					    'cap_per_visit'     => number_format($trans->cap_per_visit, 2),
					    'paid_by_cash'      => number_format($trans->cash_cost, 2),
					    'paid_by_credits'   => number_format($paid_by_credits, 2),
					    "currency_symbol" 	=> $trans->currency_type == "myr" ? "RM" : "S$",
						'files'				=> $doc_files
					);

					array_push($transaction_details, $format);
				}
			}

		}

            // e-claim transactions
		foreach($e_claim_result as $key => $res) {
			if($res->status == 0) {
				$status_text = 'Pending';
				$e_claim_pending += $res->amount;
			} else if($res->status == 1) {
				$status_text = 'Approved';
				$e_claim_spent += $res->amount;
				$total_e_claim_spent += $res->amount;
			} else if($res->status == 2) {
				$status_text = 'Rejected';
			} else {
				$status_text = 'Pending';
			}

			// if(date('Y-m-d', strtotime($res->created_at)) >= $start && date('Y-m-d', strtotime($res->created_at)) <= $end) {
				if($res->status == 1) {

					$member = DB::table('user')->where('UserID', $res->user_id)->first();

                        // check user if it is spouse or dependent
					if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
						$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
						$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
						$sub_account = ucwords($temp_account->Name);
						$sub_account_type = $temp_sub->user_type;
						$owner_id = $temp_sub->owner_id;
						$bank_account_number = $temp_account->bank_account;
						$bank_name = $temp_account->bank_name;
						$bank_code = $temp_account->bank_code;
						$bank_brh = $temp_account->bank_brh;
					} else {
						$sub_account = FALSE;
						$sub_account_type = FALSE;
						$owner_id = $member->UserID;
						$bank_account_number = $member->bank_account;
						$bank_name = $member->bank_name;
						$bank_code = $member->bank_code;
						$bank_brh = $member->bank_brh;
					}

                        // get docs
					$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

					if(sizeof($docs) > 0) {
						$e_claim_receipt_status = TRUE;
						$doc_files = [];
						foreach ($docs as $key => $doc) {
							if($doc->file_type == "pdf" || $doc->file_type == "xls") {
								$fil = url('').'/receipts/'.$doc->doc_file;
							} else if($doc->file_type == "image") {
								$fil = $doc->doc_file;
							}

							$temp_doc = array(
								'e_claim_doc_id'    => $doc->e_claim_doc_id,
								'e_claim_id'            => $doc->e_claim_id,
								'file'                      => $fil,
								'file_type'             => $doc->file_type
							);

							array_push($doc_files, $temp_doc);
						}
					} else {
						$e_claim_receipt_status = FALSE;
						$doc_files = FALSE;
					}

					$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
					$temp = array(
						'status'            => $res->status,
						'status_text'       => $status_text,
						'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
						'approved_date'     => date('d F Y', strtotime($res->approved_date)),
						'time'              => $res->time,
						'service'           => $res->service,
						'merchant'          => $res->merchant,
						'amount'            => $res->amount,
						'member'            => ucwords($member->Name),
						'type'              => 'E-Claim',
						'transaction_id'    => 'MNF'.$id,
						'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
						'owner_id'          => $owner_id,
						'sub_account_type'  => $sub_account_type,
						'sub_account'       => $sub_account,
						'employee_dependent_name'       => $sub_account ? $sub_account : null,
						'month'             => date('M', strtotime($res->approved_date)),
						'day'               => date('d', strtotime($res->approved_date)),
						'time'              => date('h:ia', strtotime($res->approved_date)),
						'receipt_status'    => $e_claim_receipt_status,
						'files'             => $doc_files,
						'spending_type'     => ucwords($res->spending_type),
						'bank_account_number' => $bank_account_number,
						'bank_name'					=> $bank_name,
						'bank_code'					=> $bank_code,
						'bank_brh'					=> $bank_brh,
						'nric'							=> $member->NRIC
					);

					array_push($e_claim, $temp);
				// }
			}

		}
	}

	$total_spent = $e_claim_spent + $in_network_spent;

    // sort in-network transaction
    usort($transaction_details, function($a, $b) {
        return strtotime($b['date_of_transaction']) - strtotime($a['date_of_transaction']);
    });

	$paginate['data'] = array(
		'total_spent'       => number_format($total_spent, 2),
		'total_spent_format_number'       => $total_spent,
		'in_network_spent'  => number_format($in_network_spent, 2),
		'e_claim_spent'     => number_format($e_claim_spent, 2),
		'in_network_transactions' => $transaction_details,
		'in_network_spending_format_number' => $in_network_spent,
		'e_claim_spending_format_number' => $total_e_claim_spent,
		'e_claim_transactions'	=> $e_claim,
		'total_in_network_spent'    => number_format($total_in_network_spent, 2),
		'total_in_network_spent_format_number'    => $total_in_network_spent,
		'total_lite_plan_consultation'      => floatval($total_lite_plan_consultation),
		'total_in_network_transactions' => $total_in_network_transactions,
		'spending_type' => $spending_type,
		'lite_plan'     => $lite_plan
	);


	return $paginate;

}

    // search employee activity
public function searchEmployeeActivity( )
{
	$input = Input::all();
	$start = date('Y-m-d', strtotime($input['start']));
	$end = SpendingInvoiceLibrary::getEndDate($input['end']);
	$spending_type = $input['spending_type'];
	$e_claim = [];
	$transaction_details = [];

	$session = self::checkSession();
	$lite_plan = false;

	$in_network_spent = 0;
	$e_claim_spent = 0;
	$e_claim_pending = 0;
	$health_screening_breakdown = 0;
	$general_practitioner_breakdown = 0;
	$dental_care_breakdown = 0;
	$tcm_breakdown = 0;
	$health_specialist_breakdown = 0;
	$wellness_breakdown = 0;
	$deleted_transaction_credits = 0;
	$deleted_transaction_cash = 0;
	$total_credits = 0;
	$total_lite_plan_consultation = 0;
	$total_e_claim_spent = 0;

	$total_in_network_transactions = 0;
	$total_deleted_in_network_transactions = 0;
	$total_cash = 0;
	$total_search_credits = 0;
	$total_in_network_spent = 0;


        // check user
	$check_user = DB::table('user')->where('UserID', $input['user_id'])->count();

	if($check_user == 0) {
		return array('status' => FALSE, 'message' => 'Employee does not exist');
	}

	if($spending_type == 'medical') {
		$table_wallet_history = 'wallet_history';
	} else {
		$table_wallet_history = 'wellness_wallet_history';
	}

	$lite_plan = StringHelper::liteCompanyPlanStatus($session->customer_buy_start_id);

	$user = DB::table('user')->where('UserID', $input['user_id'])->first();

	$wallet = DB::table('e_wallet')->where('UserID', $input['user_id'])->orderBy('created_at', 'desc')->first();
	$wallet_reset = PlanHelper::getResetWalletDate($input['user_id'], $spending_type, $start, $input['end'], 'employee');

	// return array('result' => $wallet_reset);
	if($wallet_reset) {
		$wallet_start_date = $wallet_reset;
	} else {
		$wallet_start_date = $start;
	}

	$spending_end_date = PlanHelper::endDate($input['end']);

    // total employee allocation
	$total_allocation = DB::table('e_wallet')
	->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
	->where('e_wallet.UserID', $input['user_id'])
	->where($table_wallet_history.'.created_at', '>=', date('Y-m-d', strtotime($wallet_start_date)))
	->where($table_wallet_history.'.created_at', '<=', date('Y-m-d', strtotime($spending_end_date)))
                                // ->where('wallet_history.created_at', '>=', $start)
                                // ->where('wallet_history.created_at', '<=', $spending_end_date)
	->where($table_wallet_history.'.logs', 'added_by_hr')
	->sum($table_wallet_history.'.credit');

	$deducted_allocation = DB::table('e_wallet')
	->join($table_wallet_history, $table_wallet_history.'.wallet_id', '=', 'e_wallet.wallet_id')
	->where('e_wallet.UserID', $input['user_id'])
	->where($table_wallet_history.'.created_at', '>=', date('Y-m-d', strtotime($wallet_start_date)))
	->where($table_wallet_history.'.created_at', '<=', date('Y-m-d', strtotime($spending_end_date)))
	->whereIn('logs', ['deducted_by_hr'])
	->sum($table_wallet_history.'.credit');

	$ids = StringHelper::getSubAccountsID($input['user_id']);

        // get e claim
	$e_claim_result = DB::table('e_claim')
	->whereIn('user_id', $ids)
	->where('created_at', '>=', $start)
	->where('created_at', '<=', $spending_end_date)
	->where('spending_type', $spending_type)
	->orderBy('created_at', 'desc')
	->get();
        // get in-network transactions
	$transactions = DB::table('transaction_history')
	->whereIn('UserID', $ids)
	->where('spending_type', $spending_type)
                        // ->where('in_network', 1)
	->where('date_of_transaction', '>=', $start)
	->where('date_of_transaction', '<=', $spending_end_date)
	->orderBy('date_of_transaction', 'desc')
	->get();
        // in-network transactions
	foreach ($transactions as $key => $trans) {
		if($trans) {
			$consultation_cash = false;
			$consultation_credits = false;
			$service_cash = false;
			$service_credits = false;

			if($trans->deleted == 0 || $trans->deleted == "0") {
				$in_network_spent += $trans->credit_cost;
				// if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {
					$total_in_network_transactions++;
				// }

				if($trans->lite_plan_enabled == 1) {
					$logs_lite_plan = DB::table($table_wallet_history)
					->where('logs', 'deducted_from_mobile_payment')
					->where('lite_plan_enabled', 1)
					->where('id', $trans->transaction_id)
					->first();

					if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
						$in_network_spent += floatval($logs_lite_plan->credit);
						$consultation_credits = true;
						$service_credits = true;
						$total_lite_plan_consultation += floatval($trans->consultation_fees);
					} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
						$in_network_spent += floatval($logs_lite_plan->credit);
						$consultation_credits = true;
						$service_credits = true;
						$total_lite_plan_consultation += floatval($trans->consultation_fees);
					} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
						$total_lite_plan_consultation += floatval($trans->consultation_fees);
					}
				}
			} else {
				$total_deleted_in_network_transactions++;
				if($trans->lite_plan_enabled == 1) {
					$logs_lite_plan = DB::table($table_wallet_history)
					->where('logs', 'deducted_from_mobile_payment')
					->where('lite_plan_enabled', 1)
					->where('id', $trans->transaction_id)
					->first();

					if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
						$consultation_credits = true;
						$service_credits = true;
					} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
						$consultation_credits = true;
						$service_credits = true;
					}
				}
			}


			$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
			$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
			$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
			$procedure_temp = "";
			$procedure = "";

            // get services
			if((int)$trans->multiple_service_selection == 1)
			{
                // get multiple service
				$service_lists = DB::table('transaction_services')
				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
				->where('transaction_services.transaction_id', $trans->transaction_id)
				->get();

				foreach ($service_lists as $key => $service) {
					if(sizeof($service_lists) - 2 == $key) {
						$procedure_temp .= ucwords($service->Name).' and ';
					} else {
						$procedure_temp .= ucwords($service->Name).',';
					}
					$procedure = rtrim($procedure_temp, ',');
				}
				$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
			} else {
				$service_lists = DB::table('clinic_procedure')
				->where('ProcedureID', $trans->ProcedureID)
				->first();
				if($service_lists) {
					$procedure = ucwords($service_lists->Name);
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
                    // $procedure = "";
					$clinic_name = ucwords($clinic_type->Name);
				}
			}

			$total_amount = number_format($trans->procedure_cost, 2);

            // check if there is a receipt image
			$receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

			if($receipt > 0) {
				$receipt_status = TRUE;
			} else {
				$receipt_status = FALSE;
			}

			if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
                // $receipt_status = TRUE;
				$health_provider_status = TRUE;
			} else {
				$health_provider_status = FALSE;
			}
            // if($trans->deleted == 0 || $trans->deleted == "0") {
                // get clinic type
			$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
			$type = "";
			if($clinic_type->head == 1 || $clinic_type->head == "1") {
				if($clinic_type->Name == "General Practitioner") {
					$type = "general_practitioner";
					if((int)$trans->deleted == 0) {
						$general_practitioner_breakdown += $trans->credit_cost;
						if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
							$general_practitioner_breakdown += $trans->consultation_fees;
						}
					}
				} else if($clinic_type->Name == "Dental Care") {
					$type = "dental_care";
					$dental_care_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Traditional Chinese Medicine") {
					$type = "tcm";
					$tcm_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Health Screening") {
					$type = "health_screening";
					$health_screening_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Wellness") {
					$type = "wellness";
					$wellness_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Health Specialist") {
					$type = "health_specialist";
					$health_specialist_breakdown += $trans->credit_cost;
				}
			} else {
				$find_head = DB::table('clinic_types')
				->where('ClinicTypeID', $clinic_type->sub_id)
				->first();
				if($find_head->Name == "General Practitioner") {
					$type = "general_practitioner";
					if((int)$trans->deleted == 0) {
						$general_practitioner_breakdown += $trans->credit_cost;
						if((int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->credit_cost > 0 || (int)$trans->deleted == 0 && (int)$trans->lite_plan_enabled == 1 && $trans->procedure_cost > 0 && (int)$trans->lite_plan_use_credits == 1) {
							$general_practitioner_breakdown += $trans->consultation_fees;
						}
					}
				} else if($find_head->Name == "Dental Care") {
					$type = "dental_care";
					$dental_care_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Traditional Chinese Medicine") {
					$type = "tcm";
					$tcm_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Health Screening") {
					$type = "health_screening";
					$health_screening_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Wellness") {
					$type = "wellness";
					$wellness_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Health Specialist") {
					$type = "health_specialist";
					$health_specialist_breakdown += $trans->credit_cost;
				}
			}

            // check user if it is spouse or dependent
			if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
				$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
				$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
				$sub_account = ucwords($temp_account->Name);
				$sub_account_type = $temp_sub->user_type;
				$owner_id = $temp_sub->owner_id;
			} else {
				$sub_account = FALSE;
				$sub_account_type = FALSE;
				$owner_id = $customer->UserID;
			}

			$total_amount = number_format($trans->procedure_cost, 2);

			if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
				$payment_type = "Cash";
				$transaction_type = "cash";
				$cash = number_format($trans->procedure_cost, 2);
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$total_cash += $trans->procedure_cost;
				} else if($trans->deleted == 1 || $trans->deleted == "1") {
					$deleted_transaction_cash = $trans->procedure_cost;
                    // $total_cash_transactions_deleted++;
				}
				if((int)$trans->lite_plan_enabled == 1 || $trans->lite_plan_enabled == "1") {
					$total_amount = number_format($trans->procedure_cost + $trans->consultation_fees, 2);
				}
			} else {
				$payment_type = "Mednefits Credits";
				$transaction_type = "credits";
				$cash = number_format($trans->credit_cost, 2);
				if($trans->deleted == 0 || $trans->deleted == "0") {
					$total_credits += $trans->credit_cost;

				} else if($trans->deleted == 1 || $trans->deleted == "1") {
					$deleted_transaction_credits = $trans->credit_cost;
                    // $total_credits_transactions_deleted++;
				}

				if((int)$trans->lite_plan_enabled == 1 || $trans->lite_plan_enabled == "1") {
					$total_amount = number_format($trans->credit_cost + $trans->consultation_fees, 2);
				}
			}

			if( $trans->health_provider_done == 1 && $trans->deleted == 0 || $trans->health_provider_done == "1" && $trans->deleted == "0" ) {
				if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {
					if((int)$trans->lite_plan_enabled == 1) {
						$total_in_network_spent += $trans->procedure_cost + $trans->consultation_fees;
					} else {
						$total_in_network_spent += $trans->procedure_cost;
					}
				}
				$total_cash += $trans->procedure_cost;
			} else if($trans->credit_cost > 0 && $trans->deleted == 0 || $trans->credit_cost > "0" && $trans->deleted == "0") {
				if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {
					if((int)$trans->lite_plan_enabled == 1) {
						$total_in_network_spent += $trans->credit_cost + $trans->consultation_fees;
					} else {
						$total_in_network_spent += $trans->credit_cost;
					}
				}
				$total_search_credits += $trans->credit_cost;
			}

			$refund_text = 'NO';

			if($trans->refunded == 1 && $trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
				$status_text = 'REFUNDED';
				$refund_text = 'YES';
			} else if($trans->health_provider_done == 1 && $trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
				$status_text = 'REMOVED';
				$refund_text = 'YES';
			} else {
				$status_text = FALSE;
			}

			// if(date('Y-m-d', strtotime($trans->date_of_transaction)) >= $start && date('Y-m-d', strtotime($trans->date_of_transaction)) <= $end) {

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

				$format = array(
					'clinic_name'       => $clinic->Name,
					'clinic_image'      => $clinic->image,
					'amount'            => $total_amount,
					'procedure_cost'    => number_format($trans->procedure_cost, 2),
					'clinic_type_and_service' => $clinic_name,
					'procedure'         => $procedure,
					'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
					'member'            => ucwords($customer->Name),
					'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'trans_id'    => $trans->transaction_id,
					'receipt_status'    => $receipt_status,
					'health_provider_status' => $health_provider_status,
					'user_id'           => $trans->UserID,
					'type'              => $payment_type,
					'month'             => date('M', strtotime($trans->date_of_transaction)),
					'day'               => date('d', strtotime($trans->date_of_transaction)),
					'time'              => date('h:ia', strtotime($trans->date_of_transaction)),
					'clinic_type'       => $type,
					'owner_account'     => $sub_account,
					'owner_id'          => $owner_id,
					'sub_account_user_type' => $sub_account_type,
					'co_paid'           => $trans->consultation_fees,
					'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
					'refund_text'       => $refund_text,
					'cash'              => $cash,
					'status_text'       => $status_text,
					'spending_type'     => $spending_type == 'medical' ? 'Medical' : 'Wellness',
					'consultation'      => number_format($trans->consultation_fees, 2),
					'lite_plan'         => $trans->lite_plan_enabled == 1 ? true : false,
					'consultation_credits' => $consultation_credits,
					'service_credits'   => $service_credits,
					'transaction_type'  => $transaction_type
				);

				array_push($transaction_details, $format);
			// }

            // }
		}
	}

        // e-claim transactions
	foreach($e_claim_result as $key => $res) {
		if($res->status == 0) {
			$status_text = 'Pending';
			$e_claim_pending += $res->amount;
		} else if($res->status == 1) {
			$status_text = 'Approved';
			$e_claim_spent += $res->amount;
			$total_e_claim_spent += $res->amount;
		} else if($res->status == 2) {
			$status_text = 'Rejected';
		} else {
			$status_text = 'Pending';
		}

		if(date('Y-m-d', strtotime($res->created_at)) >= $start && date('Y-m-d', strtotime($res->created_at)) <= $end) {
			if($res->status == 1) {
				$member = DB::table('user')->where('UserID', $res->user_id)->first();

                    // check user if it is spouse or dependent
				if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
					$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
					$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
					$sub_account = ucwords($temp_account->Name);
					$sub_account_type = $temp_sub->user_type;
					$owner_id = $temp_sub->owner_id;
					$bank_account_number = $temp_account->bank_account;
					$bank_name = $temp_account->bank_name;
					$bank_code = $temp_account->bank_code;
					$bank_brh = $temp_account->bank_brh;
				} else {
					$sub_account = FALSE;
					$sub_account_type = FALSE;
					$owner_id = $member->UserID;
					$bank_account_number = $member->bank_account;
					$bank_name = $member->bank_name;
					$bank_code = $member->bank_code;
					$bank_brh = $member->bank_brh;
				}

				$temp = array(
					'status'            => $res->status,
					'status_text'       => $status_text,
					'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
					'approved_date'     => date('d F Y', strtotime($res->approved_date)),
					'time'              => $res->time,
					'service'           => $res->service,
					'merchant'          => $res->merchant,
					'amount'            => $res->amount,
					'member'            => ucwords($member->Name),
					'type'              => 'E-Claim',
					'transaction_id'    => $res->e_claim_id,
					'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
					'owner_id'          => $owner_id,
					'sub_account_type'  => $sub_account_type,
					'sub_account'       => $sub_account,
					'month'             => date('M', strtotime($res->approved_date)),
					'day'               => date('d', strtotime($res->approved_date)),
					'time'              => date('h:ia', strtotime($res->approved_date)),
					'spending_type'     => $spending_type == 'medical' ? 'Medical' : 'Wellness',
					'bank_account_number' => $bank_account_number,
					'bank_name'					=> $bank_name,
					'bank_code'					=> $bank_code,
					'bank_brh'					=> $bank_brh,
					'nric'							=> $member->NRIC
				);

				array_push($e_claim, $temp);
			}
		}

	}


	$total_spent = $e_claim_spent + $in_network_spent;

	// $in_network_breakdown = array(
	// 	'general_practitioner_breakdown' => $general_practitioner_breakdown > 0 ? number_format($general_practitioner_breakdown / $in_network_spent * 100, 0) : 0,
	// 	'health_screening_breakdown'     => $health_screening_breakdown > 0 ? number_format($health_screening_breakdown / $in_network_spent * 100, 0) : 0,
	// 	'dental_care_breakdown'          => $dental_care_breakdown > 0 ? number_format($dental_care_breakdown / $in_network_spent * 100, 0) : 0,
	// 	'tcm_breakdown'                  => $tcm_breakdown > 0 ? number_format($tcm_breakdown / $in_network_spent * 100, 0) : 0,
	// 	'health_specialist_breakdown'    => $health_specialist_breakdown > 0 ? number_format($health_specialist_breakdown / $in_network_spent * 100, 0) : 0,
	// 	'wellness_breakdown'             => $wellness_breakdown > 0 ? number_format($wellness_breakdown / $in_network_spent * 100, 0) : 0
	// );

	$balance = $total_allocation - $total_spent - $deducted_allocation;
	$grand_total_credits_cash = $total_credits - $deleted_transaction_credits - $deleted_transaction_cash;
	return array(
		'total_allocation'  => number_format($total_allocation, 2),
		'allocation'  => number_format($total_allocation - $deducted_allocation, 2),
		'total_spent'       => number_format($total_spent, 2),
		'total_spent_format_number'       => $total_spent,
		'balance'           => $balance > 0 ? number_format($balance, 2) : number_format(0, 2),
		'pending_e_claim_amount' => number_format($e_claim_pending, 2),
		'in_network_spent'  => number_format($in_network_spent, 2),
		'e_claim_spent'     => number_format($e_claim_spent, 2),
		// 'in_network_breakdown' => $in_network_breakdown,
		'in_network_transactions' => $transaction_details,
		'e_claim_transactions'  => $e_claim,
		'employee'          => ucwords($user->Name),
		'in_network_spending_format_number' => $in_network_spent,
		'e_claim_spending_format_number' => $total_e_claim_spent,
		'total_in_network_credits_cash' => $grand_total_credits_cash > 0 ? number_format($grand_total_credits_cash, 2) : number_format(0, 2),
		'total_in_network_spent'    => number_format($total_in_network_spent, 2),
		'total_in_network_spent_format_number'    => $total_in_network_spent,
		'total_cash'            => $total_cash,
		'total_credits'         => $total_search_credits,
		'total_deleted_in_network_transactions' => $total_deleted_in_network_transactions,
		'total_in_network_transactions' => $total_in_network_transactions,
		'total_lite_plan_consultation'  => $total_lite_plan_consultation,
		'lite_plan'         => $lite_plan,
		'spending_type'     => $spending_type == 'medical' ? 'medical' : 'wellness'
	);
}

public function searchEmployeeEclaimActivity( )
{
	$input = Input::all();
	$start = date('Y-m-d', strtotime($input['start']));
	$end = PlanHelper::endDate($input['end']);
	$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
	$e_claim = [];
	$total_e_claim_submitted = 0;
	$total_e_claim_pending = 0;
	$total_e_claim_approved = 0;
	$total_e_claim_rejected = 0;
	$e_claim_spent = 0;
	$pending = 0;
	$rejected = 0;

        // get total e-claim spend

        // foreach ($corporate_members as $key => $member) {
	$ids = StringHelper::getSubAccountsID($input['user_id']);
	$total_e_claim_submitted +=  DB::table('e_claim')
	->where('spending_type', $spending_type)
	->whereIn('user_id', $ids)
	->where('created_at', '>=', $start)
	->where('created_at', '<=', $end)
	->sum('amount');
	$total_e_claim_pending +=  DB::table('e_claim')
	->where('spending_type', $spending_type)
	->whereIn('user_id', $ids)
	->where('created_at', '>=', $start)
	->where('created_at', '<=', $end)
	->where('status', 0)
	->sum('amount');
	$total_e_claim_approved +=  DB::table('e_claim')
	->where('spending_type', $spending_type)
	->whereIn('user_id', $ids)
	->where('created_at', '>=', $start)
	->where('created_at', '<=', $end)
	->where('status', 1)
	->sum('amount');
	$total_e_claim_rejected +=  DB::table('e_claim')
	->where('spending_type', $spending_type)
	->whereIn('user_id', $ids)
	->where('created_at', '>=', $start)
	->where('created_at', '<=', $end)
	->where('status', 2)
	->sum('amount');

	$e_claim_result = DB::table('e_claim')
	->where('spending_type', $spending_type)
	->whereIn('user_id', $ids)
	->where('created_at', '>=', $start)
	->where('created_at', '<=', $end)
	->orderBy('created_at', 'desc')
	->get();
	foreach($e_claim_result as $key => $res) {
		$approved_status = FALSE;
		$rejected_status = FALSE;
		
		if($res->status == 0) {
			$status_text = 'Pending';
			$pending += $res->amount;
		} else if($res->status == 1) {
			$status_text = 'Approved';
			$e_claim_spent += $res->amount;
		} else if($res->status == 2) {
			$status_text = 'Rejected';
			$rejected += $res->amount;
			$rejected_status = TRUE;
		} else {
			$status_text = 'Pending';
			$pending += $res->amount;
		}


                // get docs
		$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

		if(sizeof($docs) > 0) {
			$e_claim_receipt_status = TRUE;
			$doc_files = [];
			foreach ($docs as $key => $doc) {
				if($doc->file_type == "pdf" || $doc->file_type == "xls") {
					// if(StringHelper::Deployment()==1){
						// $fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->doc_file;
						$fil = EclaimHelper::createPreSignedUrl($doc->doc_file);
					// } else {
					// 	$fil = url('').'/receipts/'.$doc->doc_file;
					// }
					$image_link = null;
				} else if($doc->file_type == "image") {
					$fil = $doc->doc_file;
					$image_link = FileHelper::formatImageAutoQualityCustomer($fil, 40);
				}

				$temp_doc = array(
					'e_claim_doc_id'    => $doc->e_claim_doc_id,
					'e_claim_id'            => $doc->e_claim_id,
					'file'                      => $fil,
					'file_type'             => $doc->file_type,
					'image_link'	 	=> $image_link
				);

				array_push($doc_files, $temp_doc);
			}
		} else {
			$e_claim_receipt_status = FALSE;
			$doc_files = FALSE;
		}

		$member = DB::table('user')->where('UserID', $res->user_id)->first();

		if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
			$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
			$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
			$sub_account = ucwords($temp_account->Name);
			$sub_account_type = $temp_sub->user_type;
			$owner_id = $temp_sub->owner_id;
			$relationship = ucwords($temp_sub->relationship);
			$bank_account_number = $temp_account->bank_account;
			$bank_name = $temp_account->bank_name;
			$bank_code = $temp_account->bank_code;
			$bank_brh = $temp_account->bank_brh;
		} else {
			$sub_account = FALSE;
			$sub_account_type = FALSE;
			$owner_id = $member->UserID;
			$relationship = false;
			$bank_account_number = $member->bank_account;
			$bank_name = $member->bank_name;
			$bank_code = $member->bank_code;
			$bank_brh = $member->bank_brh;
		}

		if($res->status == 1) {
			$approved_status = true;
		} else {
			$approved_status = false;
		}
		$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
		$temp = array(
			'status'            => $res->status,
			'status_text'       => $status_text,
			'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
			'approved_date'        => $approved_status == TRUE ? date('d F Y h:i A', strtotime($res->updated_at)) : null,
			'rejected_date'        => $rejected_status == TRUE ? date('d F Y h:i A', strtotime($res->updated_at)) : null,
			'time'              => $res->time,
			'service'           => $res->service,
			'merchant'          => $res->merchant,
			'amount'            => number_format($res->amount, 2),
			'member'            => ucwords($member->Name),
			'type'              => 'E-Claim',
			'transaction_id'    => 'MNF'.$id,
			'trans_id'          => $res->e_claim_id,
			'files'             => $doc_files,
			'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
			'receipt_status'    => $e_claim_receipt_status,
			'owner_id'          => $owner_id,
			'owner_account'       => $sub_account,
			'sub_account_type'  => $sub_account_type,
			'rejected_reason'   => $res->rejected_reason,
			'spending_type'     => ucwords($res->spending_type),
			'approved_status'   => $approved_status,
			'relationship'      => $relationship,
			'remarks'			=> $res->rejected_reason,
			'bank_account_number' => $bank_account_number,
			'bank_name'					=> $bank_name,
			'bank_code'					=> $bank_code,
			'bank_brh'					=> $bank_brh,
			'nric'							=> $member->NRIC
		);

		array_push($e_claim, $temp);
	}

        // }

	return array(
		'total_e_claim_submitted'   => number_format($total_e_claim_submitted, 2),
		'total_e_claim_submitted_formatted'   => $total_e_claim_submitted,
		'total_e_claim_pending'     => number_format($total_e_claim_pending, 2),
		'total_e_claim_pending_formatted'     => $total_e_claim_pending,
		'total_e_claim_approved'    => number_format($total_e_claim_approved, 2),
		'total_e_claim_approved_formatted'    => $total_e_claim_approved,
		'total_e_claim_rejected'    => number_format($total_e_claim_rejected, 2),
		'total_e_claim_rejected_formatted'    => $total_e_claim_rejected,
		'all_transaction_total'     => number_format($e_claim_spent, 2),
		'all_transaction_total_formatted'     => $e_claim_spent,
		'pending_transaction_total' => number_format($pending, 2),
		'pending_transaction_total_formatted' => $pending,
		'rejected_transaction_total' => number_format($rejected, 2),
		'rejected_transaction_total_formatted' => $rejected,
		'e_claim_transactions'      => $e_claim,
		'spending_type'     => ucwords($spending_type)
	);
}

public function hrEclaimActivity( )
{
	$input = Input::all();
	$start = date('Y-m-d', strtotime($input['start']));
	$end = PlanHelper::endDate($input['end']);
	$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
	$e_claim = [];
	$total_e_claim_submitted = 0;
	$total_e_claim_pending = 0;
	$total_e_claim_approved = 0;
	$total_e_claim_rejected = 0;
	$e_claim_spent = 0;
	$pending = 0;
	$rejected = 0;
	$session = self::checkSession();
	$paginate = [];

        // get all hr employees, spouse and dependents
	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $session->customer_buy_start_id)->first();

	$corporate_members = DB::table('corporate_members')
	->where('corporate_id', $account->corporate_id)
	->paginate(100);

	$paginate['current_page'] = $corporate_members->getCurrentPage();
	$paginate['from'] = $corporate_members->getFrom();
	$paginate['last_page'] = $corporate_members->getLastPage();
	$paginate['per_page'] = $corporate_members->getPerPage();
	$paginate['to'] = $corporate_members->getTo();
	$paginate['total'] = $corporate_members->getTotal();
        // get total e-claim spend

	foreach ($corporate_members as $key => $member) {

		$ids = StringHelper::getSubAccountsID($member->user_id);
		$total_e_claim_submitted +=  DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
		->sum('amount');
		$total_e_claim_pending +=  DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
		->where('status', 0)
		->sum('amount');
		$total_e_claim_approved +=  DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
		->where('status', 1)
		->sum('amount');
		$total_e_claim_rejected +=  DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
		->where('status', 2)
		->sum('amount');

		$e_claim_result = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('spending_type', $spending_type)
		->where('created_at', '>=', $start)
		->where('created_at', '<=', $end)
		->orderBy('created_at', 'desc')
		->get();

		foreach($e_claim_result as $key => $res) {
			$approved_status = FALSE;
			$rejected_status = FALSE;

			if($res->status == 0) {
				$status_text = 'Pending';
				$pending += $res->amount;
			} else if($res->status == 1) {
				$status_text = 'Approved';
				$e_claim_spent += $res->amount;
			} else if($res->status == 2) {
				$status_text = 'Rejected';
				$rejected += $res->amount;
				$rejected_status = TRUE;
			} else {
				$status_text = 'Pending';
				$pending += $res->amount;
			}


                // get docs
			$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();

			if(sizeof($docs) > 0) {
				$e_claim_receipt_status = TRUE;
				$doc_files = [];
				foreach ($docs as $key => $doc) {
					if($doc->file_type == "pdf" || $doc->file_type == "xls") {
						// if(StringHelper::Deployment()==1){
							// $fil = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$doc->doc_file;
							$fil = EclaimHelper::createPreSignedUrl($doc->doc_file);
						// } else {
						// 	$fil = url('').'/receipts/'.$doc->doc_file;
						// }
						$image_link = null;
					} else if($doc->file_type == "image") {
						$image_link = FileHelper::formatImageAutoQualityCustomer($doc->doc_file, 40);
						$fil = $image_link;
					}

					$temp_doc = array(
						'e_claim_doc_id'    => $doc->e_claim_doc_id,
						'e_claim_id'            => $doc->e_claim_id,
						'file'                      => $fil,
						'file_type'             => $doc->file_type,
						'image_link'		=> $image_link
					);

					array_push($doc_files, $temp_doc);
				}
			} else {
				$e_claim_receipt_status = FALSE;
				$doc_files = FALSE;
			}

			$member = DB::table('user')->where('UserID', $res->user_id)->first();

			if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
				$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
				$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
				$sub_account = ucwords($temp_account->Name);
				$sub_account_type = $temp_sub->user_type;
				$owner_id = $temp_sub->owner_id;
				$relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
				$bank_account_number = $temp_account->bank_account;
				$bank_name = $temp_account->bank_name;
				$bank_code = $temp_account->bank_code;
				$bank_brh = $temp_account->bank_brh;
			} else {
				$sub_account = FALSE;
				$sub_account_type = FALSE;
				$owner_id = $member->UserID;
				$relationship = false;
				$bank_account_number = $member->bank_account;
				$bank_name = $member->bank_name;
				$bank_code = $member->bank_code;
				$bank_brh = $member->bank_brh;
			}

			if($res->status == 1) {
				$approved_status = true;
			}

			$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
			$temp = array(
				'status'            => $res->status,
				'status_text'       => $status_text,
				'claim_date'        => date('d F Y h:i A', strtotime($res->created_at)),
				'approved_date'        => $approved_status == TRUE ? date('d F Y h:i A', strtotime($res->updated_at)) : null,
				'rejected_date'        => $rejected_status == TRUE ? date('d F Y h:i A', strtotime($res->updated_at)) : null,
				'time'              => $res->time,
				'service'           => $res->service,
				'merchant'          => $res->merchant,
				'amount'            => number_format($res->amount, 2),
				'member'            => ucwords($member->Name),
				'type'              => 'E-Claim',
				'transaction_id'    => 'MNF'.$id,
				'trans_id'          => $res->e_claim_id,
				'files'             => $doc_files,
				'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
				'receipt_status'    => $e_claim_receipt_status,
				'owner_id'          => $owner_id,
				'owner_account'       => $sub_account,
				'employee_dependent_name' => $sub_account ? $sub_account : null,
				'claim_member_type'       => $relationship ? 'DEPENDENT' : 'EMPLOYEE',
				'sub_account_type'  => $sub_account_type,
				'rejected_reason'   => $res->rejected_reason,
				'spending_type'     => ucwords($res->spending_type),
				'approved_status'   => $approved_status,
				'relationship'      => $relationship,
				'remarks'			=> $res->rejected_reason,
				'bank_account_number' => $bank_account_number,
				'bank_name'					=> $bank_name,
				'bank_code'					=> $bank_code,
				'bank_brh'					=> $bank_brh,
				'nric'							=> $member->NRIC
			);

			array_push($e_claim, $temp);
		}

	}

	$paginate['data'] = array(
		'total_e_claim_submitted'   => number_format($total_e_claim_submitted, 2),
		'total_e_claim_submitted_formatted'   => $total_e_claim_submitted,
		'total_e_claim_pending'     => number_format($total_e_claim_pending, 2),
		'total_e_claim_pending_formatted'     => $total_e_claim_pending,
		'total_e_claim_approved'    => number_format($total_e_claim_approved, 2),
		'total_e_claim_approved_formatted'    => $total_e_claim_approved,
		'total_e_claim_rejected'    => number_format($total_e_claim_rejected, 2),
		'total_e_claim_rejected_formatted'    => $total_e_claim_rejected,
		'all_transaction_total'     => number_format($e_claim_spent, 2),
		'all_transaction_total_formatted'     => $e_claim_spent,
		'pending_transaction_total' => number_format($pending, 2),
		'pending_transaction_total_formatted' => $pending,
		'rejected_transaction_total' => number_format($rejected, 2),
		'rejected_transaction_total_formatted' => $rejected,
		'e_claim_transactions'      => $e_claim,
		'spending_type'             => $spending_type
	);

	return $paginate;
}

public function updateEclaimStatus( )
{
	$input = Input::all();

	$e_claim_id = (int)preg_replace('/[^0-9]/', '', $input['e_claim_id']);

	$check = DB::table('e_claim')->where('e_claim_id', $e_claim_id)->first();

	if(!$check) {
		return array('status' => FALSE, 'message' => 'E-Claim data does not exist.');
	}

	// get admin session from mednefits admin login
	$admin_id = Session::get('admin-session-id');
	$hr_data = StringHelper::getJwtHrSession();
	$hr_id = $hr_data->hr_dashboard_id;

	$e_claim_details = DB::table('e_claim')->where('e_claim_id', $e_claim_id)->first();
	$e_claim = new Eclaim( );

	if((int)$check->status == 1 || (int)$check->status == 2) {
		return array('status' => true, 'message' => 'E-Claim updated.', 'updated_already' => true);
	}

	if($input['status'] == 1 || $input['status'] == "1") {
		// check e-claim if already approve
		$employee = StringHelper::getUserId($e_claim_details->user_id);
            // check user balance
		// recalculate balance
		PlanHelper::reCalculateEmployeeBalance($employee);

		$wallet = DB::table('e_wallet')->where('UserID', $employee)->orderBy('created_at', 'desc')->first();
		$date = date('Y-m-d', strtotime($e_claim_details->date)).' '.date('H:i:s', strtotime($e_claim_details->time));
		// return $date;
		$balance = EclaimHelper::getSpendingBalance($employee, $date, $e_claim_details->spending_type);

		if($check->spending_type == "medical") {
			$balance_medical = round($balance['balance'], 2);
			if($e_claim_details->amount > $balance_medical) {
				return array('status' => FALSE, 'message' => 'Cannot approve e-claim request. Employee medical credits is not enough.');
			}
		} else {
			$balance_wellness = round($balance['balance'], 2);
			if($e_claim_details->amount > $balance_wellness) {
				return array('status' => FALSE, 'message' => 'Cannot approve e-claim request. Employee wellness credits is not enough.');
			}
		}

        // deduct credit and save logs
		$wallet_class = new Wallet();

            // check what type of spending wallet the e-claim is
		if($check->spending_type == "medical") {
                // create wallet logs
			// $employee_credits_left = DB::table('e_wallet')->where('wallet_id', $balance->wallet_id)->first();
			$wallet_logs = array(
				'wallet_id'     => $wallet->wallet_id,
				'credit'        => $e_claim_details->amount,
				'logs'          => 'deducted_from_e_claim',
				'running_balance' => $balance['balance'] - $e_claim_details->amount,
				'where_spend'   => 'e_claim_transaction',
				'id'            => $e_claim_id
			);

			if($balance['back_date'] == true) {
				$wallet_logs['back_date_deduction'] = 1;
				$wallet_logs['created_at'] = $e_claim_details->created_at;
			}

			$history = new WalletHistory( );

			try {
				$deduct_history = $history->createWalletHistory($wallet_logs);
				$wallet_history_id = $deduct_history->id;
				try {
					if($balance['back_date'] == false) {
						$deduct_result = $wallet_class->deductCredits($employee, $e_claim_details->amount);
					} else {
						$deduct_result =true;
					}
					$rejected_reason = isset($input['rejected_reason']) ? $input['rejected_reason'] : null;

					if($deduct_result) {
						$result = $e_claim->updateEclaimStatus($e_claim_id, $input['status'], $rejected_reason);
					}

                        // send notification to browser
					Notification::sendNotificationEmployee('Claim Approved - Mednefits', 'Your E-claim submission has been approved with Transaction ID - '.$e_claim_id, url('app/e_claim#/activity', $parameter = array(), $secure = null), $e_claim_details->user_id, "https://s3-ap-southeast-1.amazonaws.com/mednefits/images/verified.png");
					EclaimHelper::sendEclaimEmail($employee, $e_claim_id);
					if($admin_id) {
						$data = array(
							'e_claim_id' => $e_claim_id,
							'status'  	 => $input['status'] == 1 ? true : false,
							'rejected_reason' => $rejected_reason
						);
						$admin_logs = array(
		                    'admin_id'  => $admin_id,
		                    'admin_type' => 'mednefits',
		                    'type'      => 'admin_hr_approved_e_claim',
		                    'data'      => SystemLogLibrary::serializeData($data)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					} else {
						$data = array(
							'e_claim_id' => $e_claim_id,
							'status'  	 => $input['status'] == 1 ? true : false,
							'rejected_reason' => $rejected_reason
						);
						$admin_logs = array(
		                    'admin_id'  => $hr_id,
		                    'admin_type' => 'hr',
		                    'type'      => 'admin_hr_approved_e_claim',
		                    'data'      => SystemLogLibrary::serializeData($data)
		                );
		                SystemLogLibrary::createAdminLog($admin_logs);
					}
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/e_claim_update_status', $parameter = array(), $secure = null);
					$email['logs'] = 'E-Claim Update Status Medical -'.$e->getMessage();
					$email['emailSubject'] = 'Error log. Wallet History ID: '.$wallet_history_id;

					$history->deleteFailedWalletHistory($wallet_history_id);
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'E-Claim failed to update.');
				}
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/e_claim_update_status', $parameter = array(), $secure = null);
				$email['logs'] = 'E-Claim Update Status Medical - '.$e->getMessage();
				$email['emailSubject'] = 'Error log.';
				EmailHelper::sendErrorLogs($email);
				return array('status' => FALSE, 'message' => 'E-Claim failed to update.');
			}
		} else if($check->spending_type == "wellness") {
			// $employee_credits_left = DB::table('e_wallet')->where('wallet_id', $balance->wallet_id)->first();
			$wallet_logs = array(
				'wallet_id'     => $wallet->wallet_id,
				'credit'        => $e_claim_details->amount,
				'logs'          => 'deducted_from_e_claim',
				'running_balance' => $balance['balance'] - $e_claim_details->amount,
				'where_spend'   => 'e_claim_transaction',
				'id'            => $e_claim_id,
				'created_at'	=> $e_claim_details->created_at
			);

			if($balance['back_date'] == true) {
				$wallet_logs['back_date_deduction'] = 1;
				$wallet_logs['created_at'] = $e_claim_details->created_at;
			}

			try {
				$deduct_history = WellnessWalletHistory::create($wallet_logs);
				$wallet_history_id = $deduct_history->id;
				try {
					if($balance['back_date'] == false) {
						$deduct_result = $wallet_class->deductWellnessCredits($employee, $e_claim_details->amount);
					} else {
						$deduct_result = true;
					}
					$rejected_reason = isset($input['rejected_reason']) ? $input['rejected_reason'] : null;

					if($deduct_result) {
						$result = $e_claim->updateEclaimStatus($e_claim_id, $input['status'], $rejected_reason);
                            // send notification to browser
						Notification::sendNotificationEmployee('Claim Approved - Mednefits', 'Your E-claim submission has been approved with Transaction ID - '.$e_claim_id, url('app/e_claim#/activity', $parameter = array(), $secure = null), $e_claim_details->user_id, "https://s3-ap-southeast-1.amazonaws.com/mednefits/images/verified.png");
						EclaimHelper::sendEclaimEmail($employee, $e_claim_id);
						if($admin_id) {
							$data = array(
								'e_claim_id' => $e_claim_id,
								'status'  	 => $input['status'] == 1 ? true : false,
								'rejected_reason' => $rejected_reason
							);
							$admin_logs = array(
			                    'admin_id'  => $admin_id,
			                    'admin_type' => 'mednefits',
			                    'type'      => 'admin_hr_approved_e_claim',
			                    'data'      => SystemLogLibrary::serializeData($data)
			                );
			                SystemLogLibrary::createAdminLog($admin_logs);
						} else {
							$data = array(
								'e_claim_id' => $e_claim_id,
								'status'  	 => $input['status'] == 1 ? true : false,
								'rejected_reason' => $rejected_reason
							);
							$admin_logs = array(
			                    'admin_id'  => $hr_id,
			                    'admin_type' => 'hr',
			                    'type'      => 'admin_hr_approved_e_claim',
			                    'data'      => SystemLogLibrary::serializeData($data)
			                );
			                SystemLogLibrary::createAdminLog($admin_logs);
						}
					}
				} catch(Exception $e) {
					$email = [];
					$email['end_point'] = url('hr/e_claim_update_status', $parameter = array(), $secure = null);
					$email['logs'] = 'E-Claim Update Status Wellness -'.$e->getMessage();
					$email['emailSubject'] = 'Error log. Wallet History ID: '.$wallet_history_id;

					$history->deleteFailedWalletHistory($wallet_history_id);
					EmailHelper::sendErrorLogs($email);
					return array('status' => FALSE, 'message' => 'E-Claim failed to update.');
				}
			} catch(Exception $e) {
				$email = [];
				$email['end_point'] = url('hr/e_claim_update_status', $parameter = array(), $secure = null);
				$email['logs'] = 'E-Claim Update Status Wellness - '.$e->getMessage();
				$email['emailSubject'] = 'Error log.';
				EmailHelper::sendErrorLogs($email);
				return array('status' => FALSE, 'message' => 'E-Claim failed to update.');
			}
		}

	} else {
		try {
			$employee = StringHelper::getUserId($e_claim_details->user_id);
			$rejected_reason = isset($input['rejected_reason']) ? $input['rejected_reason'] : null;
			$result = $e_claim->updateEclaimStatus($e_claim_id, $input['status'], $rejected_reason);
                // send notification to browser
			Notification::sendNotificationEmployee('Claim Rejected - Mednefits', 'Your E-claim submission has been rejected with Transaction ID - '.$e_claim_id, url('app/e_claim#/activity', $parameter = array(), $secure = null), $e_claim_details->user_id, "https://s3-ap-southeast-1.amazonaws.com/mednefits/images/rejected.png");
			EclaimHelper::sendEclaimEmail($employee, $e_claim_id);
			if($admin_id) {
				$data = array(
					'e_claim_id' => $e_claim_id,
					'status'  	 => $input['status'] == 1 ? true : false,
					'rejected_reason' => $rejected_reason
				);
				$admin_logs = array(
	                'admin_id'  => $admin_id,
	                'admin_type' => 'mednefits',
	                'type'      => 'admin_hr_rejected_e_claim',
	                'data'      => SystemLogLibrary::serializeData($data)
	            );
	            SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$data = array(
					'e_claim_id' => $e_claim_id,
					'status'  	 => $input['status'] == 1 ? true : false,
					'rejected_reason' => $rejected_reason
				);
				$admin_logs = array(
                    'admin_id'  => $hr_id,
                    'admin_type' => 'hr',
                    'type'      => 'admin_hr_rejected_e_claim',
                    'data'      => SystemLogLibrary::serializeData($data)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
			}
		} catch(Exception $e) {
			$email = [];
			$email['end_point'] = url('hr/e_claim_update_status', $parameter = array(), $secure = null);
			$email['logs'] = 'E-Claim Update Status - '.$e->getMessage();
			$email['emailSubject'] = 'Error log.';
			EmailHelper::sendErrorLogs($email);
			return array('status' => FALSE, 'message' => 'E-Claim failed to update.');
		}
	}

	return array('status' => TRUE, 'message' => 'E-Claim updated.');
}

public function getEmployeeMembers( )
{
	$user_id = Session::get('employee-session');
	$check = DB::table('user')->where('UserID', $user_id)->count();

	if($check == 0) {
		return array('status' => FALSE, 'message' => 'Employee does not exist.');
	}

	$result = [];

	$owner = DB::table('user')->where('UserID', $user_id)->first();

	$temp = array(
		'user_id'   => $owner->UserID,
		'name'      => $owner->Name,
		'user_type' => 'owner'
	);

	array_push($result, $temp);

	$temp_result = DB::table('employee_family_coverage_sub_accounts')
	->join('user', 'user.UserID', '=', 'employee_family_coverage_sub_accounts.user_id')
	->where('employee_family_coverage_sub_accounts.owner_id', $user_id)
	->select('user.UserID as user_id', 'user.name', 'employee_family_coverage_sub_accounts.user_type')
	->get();
	foreach ($temp_result as $key => $value) {
		array_push($result, $value);
	}
	return $result;
}

public function checkSession( )
{
	$result = StringHelper::getJwtHrSession();
	if(!$result) {
		return array(
			'status'    => FALSE,
			'message'   => 'Need to authenticate user.'
		);
	}
	return $result;
}

public function checkCompanyTransactions($customer_id, $start, $end, $plan)
{
	$lite_plan = false;
	$final_end = date('Y-m-d H:i:s', strtotime('+22 hours', strtotime($end)));

	$users = DB::table('user')
	->join('corporate_members', 'corporate_members.user_id', '=', 'user.UserID')
	->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
	->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
	->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'customer_link_customer_buy.customer_buy_start_id')
	->where('customer_buy_start.customer_buy_start_id', $customer_id)
                // ->where('user.Active')
	->where('corporate_members.removed_status', 0)
	->groupBy('user.UserID')
	->get();

	$array_of_users = [];

	foreach($users as $key => $user) {
		array_push($array_of_users, $user->UserID);
	}

	if(sizeof($array_of_users) > 0) {
		$transactions = 0;
		$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
		if($lite_plan) {
			$transactions_lite_plan = DB::table('transaction_history')
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->where('deleted', 0)
			->where('paid', 1)
			->where('lite_plan_enabled', 1)
			->whereIn('UserID', $array_of_users)
			->count();

			$transactions_temp = DB::table('transaction_history')
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->where('deleted', 0)
			->where('paid', 1)
			->where('credit_cost', '>', 0)
			->whereIn('UserID', $array_of_users)
			->count();
			if($transactions_lite_plan > 0 || $transactions_temp > 0) {
				$transactions = 1;
			}
		} else {
			$transactions = DB::table('transaction_history')
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->where('deleted', 0)
			->where('paid', 1)
			->where('credit_cost', '>', 0)
			->whereIn('UserID', $array_of_users)
			->count();
		}

		if($transactions === 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	} else {
		return FALSE;
	}

}


public function createHrStatement( )
{
	$input = Input::all();
	$start = date('Y-m-01', strtotime($input['start']));
	$end = date('Y-m-t', strtotime($input['end']));
	$result = self::checkSession();
	$lite_plan = false;

        // check if company exist
	$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $result->customer_buy_start_id)->count();

	if($check == 0) {
		return array('status' => FALSE, 'message' => 'HR account does not exist.');
	}

        // $hr = DB::table('customer_hr_dashboard')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
        // $created = date('Y-m-d', strtotime('-1 month', strtotime($hr->created_at)));
        // if($start < $created) {
        //     return array('status' => FALSE, 'message' => 'No Transactions for this Month.');
        // }

	$e_claim = [];
	$transaction_details = [];
	$statement_in_network_amount = 0;
	$statement_e_claim_amount = 0;

	$plan = DB::table('customer_plan')->where('customer_buy_start_id', $result->customer_buy_start_id)->orderBy('created_at', 'desc')->first();

        // check if there is transactions
	$check_company_transactions = self::checkCompanyTransactions($result->customer_buy_start_id, $start, $end, $plan);

	if(!$check_company_transactions) {
		return array('status' => FALSE, 'message' => 'No Transactions for this Month.');
	}

        // return array('result' => $check_company_transactions);
        // check if there is no statement
	$statement_check = DB::table('company_credits_statement')
	->where('statement_customer_id', $result->customer_buy_start_id)
	->where('statement_start_date', $start)
                            // ->where('statement_end_date', $end)
	->count();
	if($statement_check == 0) {
		$statement = self::createStatement($result->customer_buy_start_id, $start, $end, $plan);
		if($statement) {
			$statement_id = $statement->id;
		} else {
			return array('status' => FALSE, 'message' => 'Failed to create statement record.');
		}
	} else {
		$statement = DB::table('company_credits_statement')
		->where('statement_customer_id', $result->customer_buy_start_id)
		->where('statement_start_date', $start)
		->where('statement_end_date', $end)
		->first();
            // get transaction if there is another transaction

		$statement->statement_id = $statement->statement_id;
		$statement_id = $statement->statement_id;
	}


	self::insertIfNewTransaction($result->customer_buy_start_id, $statement_id, $start, $end, $plan);

	$in_network_transaction_array = [];
	$e_claim_transaction_array = [];
        // get in-network and e-claim transactions from statement
	$in_network_transaction_temp = DB::table('statement_in_network_transactions')
	->where('statement_id', $statement_id)
	->get();

	$e_claim_transaction_temp = DB::table('statement_e_claim_transactions')
	->where('statement_id', $statement_id)
	->get();

	$statement_result = self::getStatementFull($start, $end, $plan);

	$lite_plan = StringHelper::liteCompanyPlanStatus($result->customer_buy_start_id);
        // if($plan->account_type == "lite_plan") {
        //     $lite_plan = true;
        // } else if($plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
        //     $lite_plan = true;
        // }

        // get company details
	$company = DB::table('customer_business_information')->where('customer_buy_start_id', $statement->statement_customer_id)->first();
	$new_statement = array(
		"created_at"                => $statement->created_at,
		"statement_contact_email"   => $statement->statement_contact_email,
		"statement_contact_name"    => $statement->statement_contact_name,
		"statement_contact_number"  => $statement->statement_contact_number,
		"statement_customer_id"     => $statement->statement_customer_id,
		"statement_date"            => $statement->statement_date,
		"statement_due"             => $statement->statement_due,
		"statement_e_claim_amount"  => $statement_result['total_e_claim_spent'],
		"statement_end_date"        => $statement->statement_end_date,
		"statement_id"              => $statement_id,
		"statement_in_network_amount"   => number_format($statement_result['total_transaction_spent'], 2),
		"statement_number"              => $statement->statement_number,
		"statement_reimburse_e_claim"   => $statement->statement_reimburse_e_claim,
		"statement_start_date"          => $statement->statement_start_date,
		"statement_status"              => $statement->statement_status,
		"updated_at"                    => $statement->updated_at,
		"payment_remarks"           => $statement->payment_remarks,
		'company'                   => ucwords($company->company_name),
		'company_address'           => ucwords($company->company_address)
	);

	$today = date("Y-m-d");
	$show_status = false;

	if($today >= date('Y-m-d', strtotime($statement->statement_date))) {
		$new_statement["statement_total_amount"] = number_format($statement_result['total_transaction_spent'] + $statement_result['total_consultation'], 2);
		$show_status = true;
	} else {
		$new_statement["statement_total_amount"] = "0.00";
	}



	$sub_total = floatval($statement_result['total_transaction_spent']) + floatval($statement_result['total_consultation']);

	$temp = array(
		'statement'     => $new_statement,
		'in_network_transactions'    => $statement_result['in_network_transactions'],
		'e_claim_transactions'       => $statement_result['e_claim_transactions'],
		'total_transaction_spent'   => number_format($statement_result['total_transaction_spent'], 2),
		'total_e_claim_spent'       => $statement_result['total_e_claim_spent'],
		'total_consultation'        => number_format($statement_result['total_consultation'], 2),
		'lite_plan'                 => $lite_plan,
		'sub_total'                 => number_format($sub_total, 2),
		'show_status'               => $show_status
	);
	return array('status' => TRUE, 'data' => $temp);
}

public function my_array_unique($array, $keep_key_assoc = false){
	$duplicate_keys = array();
	$tmp = array();       

	foreach ($array as $key => $val){
            // convert objects to arrays, in_array() does not support objects
		if (is_object($val))
			$val = (array)$val;

		if (!in_array($val, $tmp))
			$tmp[] = $val;
		else
			$duplicate_keys[] = $key;
	}

	foreach ($duplicate_keys as $key)
		unset($array[$key]);

	return $keep_key_assoc ? $array : array_values($array);
}

public function getStatementFull($start, $end, $plan)
{
	$input = Input::all();
        // $start = date('Y-m-01', strtotime($input['start']));
        // $end = date('Y-m-t', strtotime($input['end']));
	$result = self::checkSession();
	$lite_plan = false;
	$final_end = date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($end)));
	$e_claim = [];
	$transaction_details = [];
	$total_consultation = 0;
	$total_transaction_spent = 0;
	$total_e_claim_spent = 0;

        // get all hr employees, spouse and dependents
	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();

	$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

        // if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
        //     $lite_plan = true;
        // }
	$lite_plan = StringHelper::liteCompanyPlanStatus($result->customer_buy_start_id);

	foreach ($corporate_members as $key => $member) {
		$ids = StringHelper::getSubAccountsID($member->user_id);
            // get e claim
		$e_claim_result = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('date', '>=', $start)
		->where('date', '<=', $end)
		->where('status', 1)
		->orderBy('created_at', 'desc')
		->get();


		if($lite_plan) {
			$temp_trans_lite_plan = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('mobile', 1)
			->where('in_network', 1)
			->where('lite_plan_enabled', 1)
                                // ->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();

			$temp_trans = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('mobile', 1)
			->where('in_network', 1)
			->where('credit_cost', '>', 0)
                                // ->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();
			$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
			$transactions = self::my_array_unique($transactions_temp);
		} else {
                // get in-network transactions
			$transactions = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('in_network', 1)
			->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('date_of_transaction', 'desc')
			->get();

		}


            // in-network transactions
		foreach ($transactions as $key => $trans) {
			if($trans) {
				$consultation_cash = false;
				$consultation_credits = false;
				$service_cash = false;
				$service_credits = false;

				$total_transaction_spent += $trans->credit_cost;
				$receipt_images = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
				$procedure_temp = "";

				if((int)$trans->lite_plan_enabled == 1) {
                    // $total_consultation += floatval($trans->co_paid_amount);

					if($trans->spending_type == 'medical') {
						$table_wallet_history = 'wallet_history';
					} else {
						$table_wallet_history = 'wellness_wallet_history';
					}

					$logs_lite_plan = DB::table($table_wallet_history)
					->where('logs', 'deducted_from_mobile_payment')
					->where('lite_plan_enabled', 1)
					->where('id', $trans->transaction_id)
					->first();

					if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
						$total_consultation += floatval($trans->co_paid_amount);
						$consultation_credits = true;
						$service_credits = true;
					} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
						$total_consultation += floatval($trans->co_paid_amount);
						$consultation_credits = true;
						$service_credits = true;
					} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
						$total_consultation += floatval($trans->co_paid_amount);
					}
				}

                // get services
				if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
				{
                    // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).',';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						$procedure = ucwords($service_lists->Name);
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
                        // $procedure = "";
						$clinic_name = ucwords($clinic_type->Name);
					}
				}

                // check if there is a receipt image
				$receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

				if($receipt > 0) {
					$receipt_status = TRUE;
					$receipt_files = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->get();
				} else {
					$receipt_status = FALSE;
					$receipt_files = FALSE;
				}

				$total_amount = number_format($trans->credit_cost, 2);
				$procedure_cost = $trans->credit_cost;
				if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
					$receipt_status = TRUE;
					$health_provider_status = TRUE;
					$transaction_type = "cash";
					$payment_type = "Cash";
					if((int)$trans->lite_plan_enabled == 1 || $trans->lite_plan_enabled == "1") {
						$total_amount = number_format($trans->co_paid_amount, 2);
						$procedure_cost = "0.00";
					}
				} else {
					$transaction_type = "credits";
					$payment_type = "Mednefits Credits";
					$health_provider_status = FALSE;
					if((int)$trans->lite_plan_enabled == 1 || $trans->lite_plan_enabled == "1") {
						$total_amount = number_format($trans->credit_cost + $trans->co_paid_amount, 2);
                        // $procedure_cost = "0.00";
					}
				}

                // get clinic type
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$type = "";
				$clinic_type_name = "";
				$image = "";
				if($clinic_type->head == 1 || $clinic_type->head == "1") {
					if($clinic_type->Name == "General Practitioner") {
						$type = "general_practitioner";
						$clinic_type_name = "General Practitioner";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
					} else if($clinic_type->Name == "Dental Care") {
						$type = "dental_care";
						$clinic_type_name = "Dental Care";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
					} else if($clinic_type->Name == "Traditional Chinese Medicine") {
						$type = "tcm";
						$clinic_type_name = "Traditional Chinese Medicine";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
					} else if($clinic_type->Name == "Health Screening") {
						$type = "health_screening";
						$clinic_type_name = "Health Screening";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
					} else if($clinic_type->Name == "Wellness") {
						$type = "wellness";
						$clinic_type_name = "Wellness";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
					} else if($clinic_type->Name == "Health Specialist") {
						$type = "health_specialist";
						$clinic_type_name = "Health Specialist";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
					}
				} else {
					$find_head = DB::table('clinic_types')
					->where('ClinicTypeID', $clinic_type->sub_id)
					->first();
					if($find_head->Name == "General Practitioner") {
						$type = "general_practitioner";
						$clinic_type_name = "General Practitioner";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515238/tidzdguqbafiq4pavekj.png";
					} else if($find_head->Name == "Dental Care") {
						$type = "dental_care";
						$clinic_type_name = "Dental Care";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515231/lhp4yyltpptvpfxe3dzj.png";
					} else if($find_head->Name == "Traditional Chinese Medicine") {
						$type = "tcm";
						$clinic_type_name = "Traditional Chinese Medicine";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515256/jyocn9mr7mkdzetjjmzw.png";
					} else if($find_head->Name == "Health Screening") {
						$type = "health_screening";
						$clinic_type_name = "Health Screening";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515243/v9fcbbdzr6jdhhlba23k.png";
					} else if($find_head->Name == "Wellness") {
						$type = "wellness";
						$clinic_type_name = "Wellness";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515261/phvap8vk0suwhh2grovj.png";
					} else if($find_head->Name == "Health Specialist") {
						$type = "health_specialist";
						$clinic_type_name = "Health Specialist";
						$image = "https://res.cloudinary.com/dzh9uhsqr/image/upload/v1514515247/toj22uow68w9yf4xnn41.png";
					}
				}
                // check user if it is spouse or dependent
				if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
					$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
					$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
					$sub_account = ucwords($temp_account->Name);
					$sub_account_type = $temp_sub->user_type;
					$owner_id = $temp_sub->owner_id;
				} else {
					$sub_account = FALSE;
					$sub_account_type = FALSE;
					$owner_id = $customer->UserID;
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);

				$format = array(
					'clinic_name'       => $clinic->Name,
					'clinic_image'      => $clinic->image,
					'amount'            => number_format($procedure_cost, 2),
					'total_amount'            => $total_amount,
					'clinic_type_and_service' => $clinic_name,
					'service'           => $procedure,
					'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
					'member'            => ucwords($customer->Name),
					'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'receipt_status'    => $receipt_status,
					'health_provider_status' => $health_provider_status,
					'user_id'           => $trans->UserID,
					'type'              => 'In-Network',
					'month'             => date('M', strtotime($trans->created_at)),
					'day'               => date('d', strtotime($trans->created_at)),
					'time'              => date('h:ia', strtotime($trans->created_at)),
					'clinic_type'       => $type,
					'clinic_type_name'  => $clinic_type_name,
					'clinic_type_image' => $image,
					'owner_account'     => $sub_account,
					'owner_id'          => $owner_id,
					'sub_account_user_type' => $sub_account_type,
					'co_paid'           => $trans->co_paid_amount,
					'receipt_files'      => $receipt_files,
					'payment_type'      => $payment_type,
					'spending_type'     => $trans->spending_type,
					'consultation'      => (int)$trans->lite_plan_enabled == 1 ? number_format($trans->co_paid_amount, 2) : "0.00",
					'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
					'consultation_credits' => $consultation_credits,
					'service_credits'   => $service_credits,
					'transaction_type'  => $transaction_type
				);

				array_push($transaction_details, $format);
			}
		}

            // e-claim transactions
		foreach($e_claim_result as $key => $res) {
			$status_text = 'Approved';
			$total_e_claim_spent += $res->amount;


			$member = DB::table('user')->where('UserID', $res->user_id)->first();

                // check user if it is spouse or dependent
			if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
				$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
				$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
				$sub_account = ucwords($temp_account->Name);
				$sub_account_type = $temp_sub->user_type;
				$owner_id = $temp_sub->owner_id;
			} else {
				$sub_account = FALSE;
				$sub_account_type = FALSE;
				$owner_id = $member->UserID;
			}

			$temp = array(
				'status'            => $res->status,
				'status_text'       => $status_text,
				'claim_date'        => date('d F Y', strtotime($res->date)),
				'time'              => $res->time,
				'service'           => $res->service,
				'merchant'          => $res->merchant,
				'amount'            => $res->amount,
				'member'            => ucwords($member->Name),
				'type'              => 'E-Claim',
				'transaction_id'    => $res->e_claim_id,
				'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
				'owner_id'          => $owner_id,
				'sub_account_type'  => $sub_account_type,
				'sub_account'       => $sub_account,
				'month'             => date('M', strtotime($res->approved_date)),
				'day'               => date('d', strtotime($res->approved_date)),
				'time'              => date('h:ia', strtotime($res->approved_date)),
				'spending_type'     => $res->spending_type
			);

			array_push($e_claim, $temp);
		}
	}

        // sort in-network transaction
	usort($transaction_details, function($a, $b) {
		return strtotime($b['date_of_transaction']) - strtotime($a['date_of_transaction']);
	});

	return array(
		'total_transaction_spent'   => $total_transaction_spent,
		'total_e_claim_spent'       => $total_e_claim_spent,
		'in_network_transactions'   => $transaction_details,
		'e_claim_transactions'      => $e_claim,
		'total_consultation'        => $total_consultation
	);
}

public function insertIfNewTransaction($customer_id, $statement_id, $start, $end, $plan)
{   
	$final_end = date('Y-m-d H:i:s', strtotime('+22 hours', strtotime($end)));
	$lite_plan = false;

	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
	$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();
        // return $corporate_members;
	$statement_transactions = new CompanyStatementInNetworkTransaction( );
	$statement_e_claim_class = new CompanyStatementEclaimTransaction( );
	$statement_class = new CompanyCreditsStatement( );
	$temp = [];

        // if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
        //     $lite_plan = true;
        // }

	$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);

	foreach ($corporate_members as $key => $member) {
		$ids = StringHelper::getSubAccountsID($member->user_id);

		if($lite_plan) {
			$temp_trans_lite_plan = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('in_network', 1)
			->where('lite_plan_enabled', 1)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();

			$temp_trans = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                            // ->where('mobile', 1)
                            // ->where('in_network', 1)
			->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();
			$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
			$in_network = self::my_array_unique($transactions_temp);
		} else {
			$in_network = DB::table('transaction_history')
			->whereIn('UserID', $ids)
			->where('credit_cost', '>', 0)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('date_of_transaction', 'desc')
			->get();

		}

		$e_claim = DB::table('e_claim')
		->whereIn('user_id', $ids)
		->where('date', '>=', $start)
		->where('date', '<=', $end)
		->where('status', 1)
		->orderBy('created_at', 'desc')
		->get();

            // array_push($temp, $e_claim);
		foreach($e_claim as $key => $res) {
			$check_eclaim_transaction = DB::table('statement_e_claim_transactions')
			->where('e_claim_id', $res->e_claim_id)
			->count( );
			if($check_eclaim_transaction == 0) {
                    // return "insert";
                    // insert
				$temp_e_claim = array(
					'statement_id'  => $statement_id,
					'e_claim_id'    => $res->e_claim_id
				);
				$statement_e_claim_class->createStatementEclaimTransaction($temp_e_claim);
				$statement_class->addEclaimAmount($statement_id, $res->amount);
			}
		}

		foreach ($in_network as $key => $trans) {
			$check_in_network_transaction = DB::table('statement_in_network_transactions')
			->where('transaction_id', $trans->transaction_id)
			->count( );
			if($check_in_network_transaction == 0) {
                    // insert
				$temp_transaction = array(
					'statement_id'      => $statement_id,
					'transaction_id'    => $trans->transaction_id
				);
				$statement_transactions->createStatementInNetworkTransaction($temp_transaction);
			}
		}
	}

        // return $temp;
}

public function createStatement($customer_id, $start, $end, $plan)
{   
	$final_end = date('Y-m-d H:i:s', strtotime('+23 hours', strtotime($end)));
	$total_e_claim_amount = 0;
	$total_in_network_amount = 0;
	$lite_plan = false;

        // if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
        //     $lite_plan = true;
        // }
	$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);
        // get all hr employees, spouse and dependents
	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();
	$business_contact = DB::table('customer_business_contact')->where('customer_buy_start_id', $customer_id)->first();

	if($business_contact->billing_status === true || $business_contact->billing_status === "true") {
		$contact_name = $business_contact->first_name.' '.$business_contact->last_name;
	} else {
		$contact = DB::table('customer_billing_contact')->where('customer_buy_start_id', $customer_id)->first();
		$contact_name = $contact->billing_name;
	}

	$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

	foreach ($corporate_members as $key => $member) {
		$ids = StringHelper::getSubAccountsID($member->user_id);

		if($lite_plan) {
			$temp_trans_lite_plan = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('mobile', 1)
			->where('in_network', 1)
			->where('lite_plan_enabled', 1)
                                // ->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();

			$temp_trans = DB::table('transaction_history')
			->whereIn('UserID', $ids)
			->where('mobile', 1)
			->where('in_network', 1)
			->where('health_provider_done', 0)
			->where('lite_plan_enabled', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();
			$in_network = array_merge($temp_trans, $temp_trans_lite_plan);
		} else {
			$in_network = DB::table('transaction_history')
			->whereIn('UserID', $ids)
			->where('mobile', 1)
			->where('in_network', 1)
			->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();

		}

		$e_claim = DB::table('e_claim')
		->where('status', 1)
		->whereIn('user_id', $ids)
		->where('date', '>=', $start)
		->where('date', '<=', $end)
		->orderBy('created_at', 'desc')
		->get();

		foreach($e_claim as $key => $res) {
			$total_e_claim_amount += $res->amount;
		}

		foreach ($in_network as $key => $trans) {
			$total_in_network_amount += $trans->credit_cost;
		}
	}

	$company_details = DB::table('customer_business_information')->where('customer_buy_start_id', $customer_id)->first();

	$statement = DB::table('company_credits_statement')->count();

	$number = str_pad($statement + 1, 8, "0", STR_PAD_LEFT);

	$statement_date = date('Y-m-d', strtotime('+1 month', strtotime($start)));
	$statement_due = date('Y-m-d', strtotime('+15 days', strtotime($statement_date)));
	$statement_data = array(
		'statement_customer_id'     => $customer_id,
		'statement_number'          => 'MC'.$number,
		'statement_date'            => $statement_date,
		'statement_due'             => $statement_due,
		'statement_start_date'      => $start,
		'statement_end_date'        => $end,
		'statement_contact_name'    => $contact_name,
		'statement_contact_number'  => $business_contact->phone,
		'statement_contact_email'   => $business_contact->work_email,
		'statement_in_network_amount'   => $total_in_network_amount,
		'statement_e_claim_amount'       => $total_e_claim_amount
	);

	if($lite_plan) {
		$statement_data['lite_plan'] = 1;
	}

        // create statement
	$statement_class = new CompanyCreditsStatement( );
	$statement_result = $statement_class->createCompanyCreditsStatement($statement_data);
	$statement_id = $statement_result->id;
	if($statement_result) {
		$statement_transactions = new CompanyStatementInNetworkTransaction( );
		$statement_e_claim_class = new CompanyStatementEclaimTransaction( );
            // insert in-network transactions
		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);

			if($lite_plan) {
				$temp_trans_lite_plan = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('in_network', 1)
				->where('lite_plan_enabled', 1)
				->where('deleted', 0)
				->where('paid', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $final_end)
				->orderBy('created_at', 'desc')
				->get();

				$temp_trans = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('mobile', 1)
				->where('in_network', 1)
				->where('health_provider_done', 0)
				->where('lite_plan_enabled', 0)
				->where('deleted', 0)
				->where('paid', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $final_end)
				->orderBy('created_at', 'desc')
				->get();
				$in_network = array_merge($temp_trans, $temp_trans_lite_plan);
			} else {
				$in_network = DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('mobile', 1)
				->where('in_network', 1)
				->where('health_provider_done', 0)
				->where('deleted', 0)
				->where('paid', 1)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $final_end)
				->orderBy('created_at', 'desc')
				->get();

			}
			$e_claim = DB::table('e_claim')
			->whereIn('user_id', $ids)
			->where('date', '>=', $start)
			->where('date', '<=', $end)
			->orderBy('created_at', 'desc')
			->get();

			foreach($e_claim as $key => $res) {
				$temp_e_claim = array(
					'statement_id'  => $statement_id,
					'e_claim_id'    => $res->e_claim_id
				);

				$statement_e_claim_class->createStatementEclaimTransaction($temp_e_claim);
			}

			foreach ($in_network as $key => $trans) {
				$temp_transaction = array(
					'statement_id'      => $statement_id,
					'transaction_id'    => $trans->transaction_id
				);

				$statement_transactions->createStatementInNetworkTransaction($temp_transaction);
			}
		}
	}

	return $statement_result;
}

    // search statement employee
public function searchEmployeeStatement( )
{
	$input = Input::all();
	$start = date('Y-m-01', strtotime($input['start']));
	$end = date('Y-m-t', strtotime($input['end']));

	$e_claim = [];
	$transaction_details = [];

	$in_network_spent = 0;
	$e_claim_spent = 0;
	$e_claim_pending = 0;
	$health_screening_breakdown = 0;
	$general_practitioner_breakdown = 0;
	$dental_care_breakdown = 0;
	$tcm_breakdown = 0;
	$health_specialist_breakdown = 0;
	$wellness_breakdown = 0;


        // check user
	$check_user = DB::table('user')->where('UserID', $input['user_id'])->count();

	if($check_user == 0) {
		return array('status' => FALSE, 'message' => 'Employee does not exist');
	}

	$user = DB::table('user')->where('UserID', $input['user_id'])->first();

        // total employee allocation
	$total_allocation = DB::table('e_wallet')
	->join('wallet_history', 'wallet_history.wallet_id', '=', 'e_wallet.wallet_id')
	->where('e_wallet.UserID', $input['user_id'])
	->where('wallet_history.logs', 'added_by_hr')
	->sum('wallet_history.credit');

	$ids = StringHelper::getSubAccountsID($input['user_id']);

        // get e claim
	$e_claim_result = DB::table('e_claim')
	->whereIn('user_id', $ids)
	->where('date', '>=', $start)
	->where('date', '<=', $end)
                        // ->where('status', 1)
	->orderBy('created_at', 'desc')
	->get();
        // return $e_claim_result;
        // get in-network transactions
	$transactions_lite_plan = DB::table('transaction_history')
	->whereIn('UserID', $ids)
                        // ->where('mobile', 1)
                        // ->where('in_network', 1)
	->where('lite_plan_enabled', 1)
	->where('paid', 1)
	->where('date_of_transaction', '>=', $start)
	->where('date_of_transaction', '<=', $end)
	->orderBy('created_at', 'desc')
	->get();

	$transactions_temp = DB::table('transaction_history')
	->whereIn('UserID', $ids)
                        // ->where('mobile', 1)
                        // ->where('in_network', 1)
                        // ->where('health_provider_done', 0)
	->where('paid', 1)
	->where('credit_cost', '>', 0)
	->where('date_of_transaction', '>=', $start)
	->where('date_of_transaction', '<=', $end)
	->orderBy('created_at', 'desc')
	->get();

	$in_network_temp = array_merge($transactions_temp, $transactions_lite_plan);
	$transactions = self::my_array_unique($in_network_temp);
        // in-network transactions
	foreach ($transactions as $key => $trans) {
		if($trans) {
			$in_network_spent += $trans->credit_cost;
			$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
			$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
			$customer = DB::table('user')->where('UserID', $trans->UserID)->first();
			$procedure_temp = "";

            // get services
			if($trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
			{
                // get multiple service
				$service_lists = DB::table('transaction_services')
				->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
				->where('transaction_services.transaction_id', $trans->transaction_id)
				->get();

				foreach ($service_lists as $key => $service) {
					if(sizeof($service_lists) - 2 == $key) {
						$procedure_temp .= ucwords($service->Name).' and ';
					} else {
						$procedure_temp .= ucwords($service->Name).',';
					}
					$procedure = rtrim($procedure_temp, ',');
				}
				$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
			} else {
				$service_lists = DB::table('clinic_procedure')
				->where('ProcedureID', $trans->ProcedureID)
				->first();
				if($service_lists) {
					$procedure = ucwords($service_lists->Name);
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
                    // $procedure = "";
					$clinic_name = ucwords($clinic_type->Name);
				}
			}

            // check if there is a receipt image
			$receipt = DB::table('user_image_receipt')->where('transaction_id', $trans->transaction_id)->count();

			if($receipt > 0) {
				$receipt_status = TRUE;
			} else {
				$receipt_status = FALSE;
			}

			$total_amount = number_format($trans->credit_cost, 2);

			if($trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
				$receipt_status = TRUE;
				$health_provider_status = TRUE;
				if($trans->lite_plan_enabled == 1) {
					$total_amount = number_format($trans->co_paid_amount, 2);
				}
			} else {
				$health_provider_status = FALSE;
				if($trans->lite_plan_enabled == 1) {
					$total_amount = number_format($trans->credit_cost + $trans->co_paid_amount, 2);
				}
			}

            // get clinic type
			$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
			$type = "";
			if($clinic_type->head == 1 || $clinic_type->head == "1") {
				if($clinic_type->Name == "General Practitioner") {
					$type = "general_practitioner";
					$general_practitioner_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Dental Care") {
					$type = "dental_care";
					$dental_care_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Traditional Chinese Medicine") {
					$type = "tcm";
					$tcm_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Health Screening") {
					$type = "health_screening";
					$health_screening_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Wellness") {
					$type = "wellness";
					$wellness_breakdown += $trans->credit_cost;
				} else if($clinic_type->Name == "Health Specialist") {
					$type = "health_specialist";
					$health_specialist_breakdown += $trans->credit_cost;
				}
			} else {
				$find_head = DB::table('clinic_types')
				->where('ClinicTypeID', $clinic_type->sub_id)
				->first();
				if($find_head->Name == "General Practitioner") {
					$type = "general_practitioner";
					$general_practitioner_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Dental Care") {
					$type = "dental_care";
					$dental_care_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Traditional Chinese Medicine") {
					$type = "tcm";
					$tcm_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Health Screening") {
					$type = "health_screening";
					$health_screening_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Wellness") {
					$type = "wellness";
					$wellness_breakdown += $trans->credit_cost;
				} else if($find_head->Name == "Health Specialist") {
					$type = "health_specialist";
					$health_specialist_breakdown += $trans->credit_cost;
				}
			}

            // check user if it is spouse or dependent
			if($customer->UserType == 5 && $customer->access_type == 2 || $customer->UserType == 5 && $customer->access_type == 3) {
				$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $customer->UserID)->first();
				$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
				$sub_account = ucwords($temp_account->Name);
				$sub_account_type = $temp_sub->user_type;
				$owner_id = $temp_sub->owner_id;
			} else {
				$sub_account = FALSE;
				$sub_account_type = FALSE;
				$owner_id = $customer->UserID;
			}
			$format = array(
				'clinic_name'       => $clinic->Name,
				'clinic_image'      => $clinic->image,
				'amount'            => number_format($trans->procedure_cost, 2),
				'total_amount'            => $total_amount,
				'clinic_type_and_service' => $clinic_name,
				'service'           => $procedure,
				'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->created_at)),
				'member'            => ucwords($customer->Name),
				'transaction_id'    => $trans->transaction_id,
				'receipt_status'    => $receipt_status,
				'health_provider_status' => $health_provider_status,
				'user_id'           => $trans->UserID,
				'type'              => 'In-Network',
				'month'             => date('M', strtotime($trans->created_at)),
				'day'               => date('d', strtotime($trans->created_at)),
				'time'              => date('h:ia', strtotime($trans->created_at)),
				'clinic_type'       => $type,
				'owner_account'     => $sub_account,
				'owner_id'          => $owner_id,
				'sub_account_user_type' => $sub_account_type,
				'co_paid'           => $trans->co_paid_amount,
				'lite_plan'         => $trans->lite_plan_enabled == 1 ? true : false
			);

			array_push($transaction_details, $format);
		}
	}

        // e-claim transactions
	foreach($e_claim_result as $key => $res) {
		if($res->status == 0) {
			$status_text = 'Pending';
			$e_claim_pending += $res->amount;
		} else if($res->status == 1) {
			$status_text = 'Approved';
			$e_claim_spent += $res->amount;

		} else if($res->status == 2) {
			$status_text = 'Rejected';
		} else {
			$status_text = 'Pending';
		}

		if($res->status == 1) {
			$member = DB::table('user')->where('UserID', $res->user_id)->first();

                // check user if it is spouse or dependent
			if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
				$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
				$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
				$sub_account = ucwords($temp_account->Name);
				$sub_account_type = $temp_sub->user_type;
				$owner_id = $temp_sub->owner_id;
			} else {
				$sub_account = FALSE;
				$sub_account_type = FALSE;
				$owner_id = $member->UserID;
			}

			$temp = array(
				'status'            => $res->status,
				'status_text'       => $status_text,
				'claim_date'        => date('d F Y', strtotime($res->date)),
				'time'              => $res->time,
				'service'           => $res->service,
				'merchant'          => $res->merchant,
				'amount'            => $res->amount,
				'member'            => ucwords($member->Name),
				'type'              => 'E-Claim',
				'transaction_id'    => $res->e_claim_id,
				'visit_date'        => date('d F Y', strtotime($res->date)).', '.$res->time,
				'owner_id'          => $owner_id,
				'sub_account_type'  => $sub_account_type,
				'sub_account'       => $sub_account,
				'month'             => date('M', strtotime($res->approved_date)),
				'day'               => date('d', strtotime($res->approved_date)),
				'time'              => date('h:ia', strtotime($res->approved_date)),
			);

			array_push($e_claim, $temp);
		}

	}


	$total_spent = $e_claim_spent + $in_network_spent;

	$in_network_breakdown = array(
		'general_practitioner_breakdown' => $general_practitioner_breakdown > 0 ? number_format($general_practitioner_breakdown / $in_network_spent * 100, 0) : 0,
		'health_screening_breakdown'     => $health_screening_breakdown > 0 ? number_format($health_screening_breakdown / $in_network_spent * 100, 0) : 0,
		'dental_care_breakdown'          => $dental_care_breakdown > 0 ? number_format($dental_care_breakdown / $in_network_spent * 100, 0) : 0,
		'tcm_breakdown'                  => $tcm_breakdown > 0 ? number_format($tcm_breakdown / $in_network_spent * 100, 0) : 0,
		'health_specialist_breakdown'    => $health_specialist_breakdown > 0 ? number_format($health_specialist_breakdown / $in_network_spent * 100, 0) : 0,
		'wellness_breakdown'             => $wellness_breakdown > 0 ? number_format($wellness_breakdown / $in_network_spent * 100, 0) : 0
	);

	return array(
		'total_allocation'  => $total_allocation,
		'total_spent'       => $total_spent,
		'balance'           => $total_allocation - $total_spent,
		'pending_e_claim_amount' => $e_claim_pending,
		'in_network_spent'  => $in_network_spent,
		'e_claim_spent'     => $e_claim_spent,
		'in_network_breakdown' => $in_network_breakdown,
		'in_network_transactions' => $transaction_details,
		'e_claim_transactions'  => $e_claim,
		'employee'          => ucwords($user->Name)
	);
}

    // create transaction receipt
public function createInNetworkReceipt( )
{
	$input = Input::all();
	$receipt_all = [];
	$user_id = Session::get('employee-session');
	// get admin session from mednefits admin login
	$admin_id = Session::get('admin-session-id');

	$transaction_id = (int)preg_replace('/[^0-9]/', '', $input['transaction_id']);

	$check = DB::table('transaction_history')->where('transaction_id', $transaction_id)->count();

	if($check == 0) {
		return array('status' => FALSE, 'message' => 'Transaction data does not exist.');
	}

	if(Input::file('file')) {
		$rules = array(
			'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx',
		);

		$validator = Validator::make(Input::all(), $rules);

		if($validator->fails()) {
			return array('status' => FALSE, 'message' => 'Invalid file. Only accepts Image, PDF and Excel');
		}

		$file = $input['file'];

		$file_name = time().' - '.$file->getClientOriginalName();
		$aws_upload = false;
		if($file->getClientOriginalExtension() == "pdf") {
			$receipt = array(
				'user_id'           => $user_id,
				'transaction_id'    => $transaction_id,
				'file'  => $file_name,
				'type'  => "pdf"
			);
			$file->move(storage_path().'/receipts/', $file_name);
			$aws_upload = true;
		} else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
			$receipt = array(
				'user_id'           => $user_id,
				'transaction_id'    => $transaction_id,
				'file'  => $file_name,
				'type'  => "excel"
			);
			$file->move(storage_path().'/receipts/', $file_name);
			$aws_upload = true;
		} else {
			$image = \Cloudinary\Uploader::upload($file->getPathName());
			$receipt = array(
				'user_id'           => $user_id,
				'file'      => $image['secure_url'],
				'type'      => "image",
				'transaction_id'    => $transaction_id,
			);
		}


		$trans_docs = new UserImageReceipt( );
		$result = $trans_docs->saveReceipt($receipt);

		if($result) {
			// if(StringHelper::Deployment()==1){
				if($aws_upload == true) {
                        //   aws
					$s3 = AWS::get('s3');
					$s3->putObject(array(
						'Bucket'     => 'mednefits',
						'Key'        => 'receipts/'.$file_name,
						'SourceFile' => storage_path().'/receipts/'.$file_name,
					));
					$result->file = EclaimHelper::createPreSignedUrl($file_name);
					unlink(storage_path().'/receipts/'.$file_name);
					// $result->file ='https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$file_name;
				}
			// }
			if($admin_id) {
				$admin_logs = array(
                    'admin_id'  => $admin_id,
                    'admin_type' => 'mednefits',
                    'type'      => 'admin_employee_uploaded_in_network_receipt',
                    'data'      => SystemLogLibrary::serializeData($receipt)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
			} else {
				$admin_logs = array(
                    'admin_id'  => $user_id,
                    'admin_type' => 'member',
                    'type'      => 'admin_employee_uploaded_in_network_receipt',
                    'data'      => SystemLogLibrary::serializeData($receipt)
                );
                SystemLogLibrary::createAdminLog($admin_logs);
			}
			return array('status' => TRUE, 'receipt' => $result);
		} else {
			return array('status' => FALSE, 'message' => 'Failed to save transaction receipt.');
		}
	} else {
		return array('status' => FALSE, 'message' => 'Please select a file.');
	}
}

public function getCompanyInvoiceGenerate($start, $end, $plan, $customer_id)
{
	$lite_plan = false;
	$final_end = date('Y-m-d H:i:s', strtotime('+22 hours', strtotime($end)));
	$total_consultation = 0;
	$total_transaction_spent = 0;

        // get all hr employees, spouse and dependents
	$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $customer_id)->first();

	$corporate_members = DB::table('corporate_members')->where('corporate_id', $account->corporate_id)->get();

        // if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
        //     $lite_plan = true;
        // }

	$lite_plan = StringHelper::liteCompanyPlanStatus($customer_id);

	foreach ($corporate_members as $key => $member) {
		$ids = StringHelper::getSubAccountsID($member->user_id);

		if($lite_plan) {
			$temp_trans_lite_plan = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('mobile', 1)
			->where('in_network', 1)
			->where('lite_plan_enabled', 1)
                                // ->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();

			$temp_trans = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('mobile', 1)
			->where('in_network', 1)
			->where('credit_cost', '>', 0)
                                // ->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('created_at', 'desc')
			->get();
			$transactions_temp = array_merge($temp_trans_lite_plan, $temp_trans);
			$transactions = self::my_array_unique($transactions_temp);
		} else {
                // get in-network transactions
			$transactions = DB::table('transaction_history')
			->whereIn('UserID', $ids)
                                // ->where('in_network', 1)
			->where('health_provider_done', 0)
			->where('deleted', 0)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $final_end)
			->orderBy('date_of_transaction', 'desc')
			->get();

		}


            // in-network transactions
		foreach ($transactions as $key => $trans) {
			if($trans) {
				$consultation_cash = false;
				$consultation_credits = false;
				$service_cash = false;
				$service_credits = false;

				$total_transaction_spent += $trans->credit_cost;
				if($lite_plan && $trans->lite_plan_enabled == 1) {
                    // $total_consultation += floatval($trans->co_paid_amount);

					if($trans->spending_type == 'medical') {
						$table_wallet_history = 'wallet_history';
					} else {
						$table_wallet_history = 'wellness_wallet_history';
					}

					if($lite_plan && $trans->lite_plan_enabled == 1) {
						$logs_lite_plan = DB::table($table_wallet_history)
						->where('logs', 'deducted_from_mobile_payment')
						->where('lite_plan_enabled', 1)
						->where('id', $trans->transaction_id)
						->first();

						if($logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === 0 || $logs_lite_plan && $trans->credit_cost > 0 && $trans->lite_plan_use_credits === "0") {
							$total_consultation += floatval($trans->co_paid_amount);
							$consultation_credits = true;
							$service_credits = true;
						} else if($logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 1 || $logs_lite_plan && $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "1"){
							$total_consultation += floatval($trans->co_paid_amount);
							$consultation_credits = true;
							$service_credits = true;
						} else if($trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === 0 || $trans->procedure_cost >= 0 && $trans->lite_plan_use_credits === "0"){
							$total_consultation += floatval($trans->co_paid_amount);
						}
					}
				}
			}
		}
	}

	return array(
		'total_credits'   => $total_transaction_spent,
		'total_consultation'        => $total_consultation
	);
}

public function generateMonthlyCompanyInvoice( )
{
	set_time_limit(900);
	$companies = DB::table('corporate')
	->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
	->get();
	$start = date('Y-m-01', strtotime('-1 month'));
	$end = date('Y-m-t', strtotime('-1 month'));

	$total_success_generate = 0;
	$total_fail_generate = 0;
	$result = [];
	$total_credits_generate = 0;
	$total_consultation_generate = 0;
	foreach ($companies as $key => $company) {
		$lite_plan = false;
            // check if company exist
		$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $company->customer_buy_start_id)->first();

		$hr = DB::table('customer_hr_dashboard')->where('customer_buy_start_id', $company->customer_buy_start_id)->first();
		$created = date('Y-m-d', strtotime('-1 month', strtotime($hr->created_at)));
		if($start > $created) {
			if($check) {
				$plan = DB::table('customer_plan')->where('customer_buy_start_id', $company->customer_buy_start_id)->orderBy('created_at', 'desc')->first();
                    // check if there is a invoice transaction to made
				$credit_check = self::getCompanyInvoiceGenerate($start, $end, $plan, $company->customer_buy_start_id);

				if($credit_check['total_credits'] > 0 || $credit_check['total_consultation'] > 0) {
					$total_success_generate++;


					$total_credits_generate += $credit_check['total_credits'];
					$total_consultation_generate += $credit_check['total_consultation'];
                        // create or check statement
					$e_claim = [];
					$transaction_details = [];
					$statement_in_network_amount = 0;
					$statement_e_claim_amount = 0;

					$lite_plan = StringHelper::liteCompanyPlanStatus($company->customer_buy_start_id);

                        // check if there is no statement
					$statement_check = DB::table('company_credits_statement')
					->where('statement_customer_id', $company->customer_buy_start_id)
					->where('statement_start_date', $start)
					->count();
					if($statement_check == 0) {
						$statement = self::createStatement($company->customer_buy_start_id, $start, $end, $plan);
						if($statement) {
							$statement_id = $statement->id;
						} else {
							$result[] = array('status' => FALSE, 'message' => 'Failed to create statement record.', 'customer_id' => $company->customer_buy_start_id);
						}
					} else {
						$statement = DB::table('company_credits_statement')
						->where('statement_customer_id', $company->customer_buy_start_id)
						->where('statement_start_date', $start)
						->where('statement_end_date', $end)
						->first();
                            // get transaction if there is another transaction

						$statement_id = $statement->statement_id;
					}

					$statement = DB::table('company_credits_statement')
					->where('statement_id', $statement_id)
					->first();

					self::insertIfNewTransaction($company->customer_buy_start_id, $statement_id, $start, $end, $plan);
					$company_details = DB::table('corporate')
					->join('customer_link_customer_buy', 'customer_link_customer_buy.corporate_id', '=', 'corporate.corporate_id')
					->where('customer_link_customer_buy.customer_buy_start_id', $statement->statement_customer_id)
					->first();
					$company = DB::table('customer_business_information')->where('customer_buy_start_id', $company->customer_buy_start_id)->first();

                        // send to company
					$new_statement = array(
						"created_at"                => $statement->created_at,
						"statement_contact_email"   => $statement->statement_contact_email,
						"statement_contact_name"    => ucwords($statement->statement_contact_name),
						"statement_contact_number"  => $statement->statement_contact_number,
						"statement_customer_id"     => $statement->statement_customer_id,
						"statement_date"            => date('d F Y', strtotime($statement->statement_date)),
						'statement_due'             => date('d F Y', strtotime($statement->statement_due)),
						"statement_end_date"        => date('F d Y', strtotime($statement->statement_end_date)),
						"statement_id"              => $statement->statement_id,
						"statement_number"              => $statement->statement_number,
						"statement_reimburse_e_claim"   => $statement->statement_reimburse_e_claim,
						"statement_start_date"          => date('F d', strtotime($statement->statement_start_date)),
						"statement_status"              => $statement->statement_status,
						"statement_in_network_amount"   => number_format($credit_check['total_credits'], 2),
						"statement_total_amount"        => number_format($credit_check['total_credits'] , 2),
						'total_consultation'            => number_format($credit_check['total_consultation'] , 2),
						'sub_total'                     => number_format($credit_check['total_credits'] + $credit_check['total_consultation'] , 2),
						'total_due'                     => number_format($credit_check['total_credits'] + $credit_check['total_consultation'] , 2),
						"updated_at"                    => $statement->updated_at,
						'company'                       => ucwords($company_details->company_name),
						'company_address'           => ucwords($company->company_address),
						'emailSubject'                  => 'Company Monthly Invoice',
						'emailTo'                       => $statement->statement_contact_email,
                            // 'emailTo'                       => 'allan.alzula.work@gmail.com',
						'emailPage'                     => 'email-templates.company-monthly-invoice',
						'emailName'                      => ucwords($company_details->company_name),
						'lite_plan'                     => $lite_plan,
						'payment_remarks'               => $statement->payment_remarks
					);
					if((int)$check->spending_notification == 1) {
                            // send to email with attachment
						EmailHelper::sendEmailCompanyInvoiceWithAttachment($new_statement);
					}
					$result[] = $new_statement;
				} else {
					$total_fail_generate++;
				}
			}
		}
	}

	$currenttime = StringHelper::CurrentTime();

	$emailDdata['emailName']= 'Mednefits Company Automatic Invoice Generate';
	$emailDdata['emailPage']= 'email-templates.invoice-generate';
	$emailDdata['emailTo']= 'developer.mednefits@gmail.com';
	$emailDdata['emailSubject'] = "Company Benefits Spending Invoice";
	$emailDdata['actionDate'] = date('d-m-Y');
	$emailDdata['actionTime'] = $currenttime;
	$emailDdata['total_success'] = $total_success_generate;
	$emailDdata['total_fail'] = $total_fail_generate;
	$emailDdata['totalRecords'] = count($companies);
	$emailDdata['results'] = count($companies);
	$emailDdata['total_credits'] = number_format($total_credits_generate, 2);
	EmailHelper::sendEmail($emailDdata);
}

	public function getTotalCreditsInNetworkTransactions($corporate_id, $start, $temp_end, $plan)
	{

		$total_credits = 0;
		$total_consultation = 0;
		$corporate_members = DB::table('corporate_members')->where('corporate_id', $corporate_id)->get();
		$lite_plan = false;
		$end = date('Y-m-d', strtotime('+23 hours', strtotime($temp_end)));

		if($plan->account_type === "lite_plan" || $plan->account_type == "insurance_bundle" && $plan->secondary_account_type == "insurance_bundle_lite") {
			$lite_plan = true;
		}

		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);
	            // get in-network transactions
			$total_credits += DB::table('transaction_history')
			->whereIn('UserID', $ids)
			->where('date_of_transaction', '>=', $start)
			->where('date_of_transaction', '<=', $end)
			->where('credit_cost', '>', 0)
			->where('deleted', 0)
			->where('paid', 1)
			->sum('credit_cost');
			if($lite_plan) {
				$total_consultation += DB::table('transaction_history')
				->whereIn('UserID', $ids)
				->where('date_of_transaction', '>=', $start)
				->where('date_of_transaction', '<=', $end)
				->where('lite_plan_enabled', 1)
				->where('deleted', 0)
				->where('paid', 1)
				->sum('co_paid_amount');
			}
		}

		return array('total_credits' => $total_credits, 'total_consultation' => $total_consultation);
	}

	public function loginEmployeeAdmin( )
	{
		$input = Input::all();

		$check = DB::table('user')->where('UserType', 5)->where('UserID', $input['user_id'])->where('password', $input['password'])->where('Active', 1)->first();
		if($check) {
			Session::put('employee-session', $check->UserID);
			if(isset($input['admin_id']) || $input['admin_id'] != null) {
				Session::put('admin-session-id', $input['admin_id']);
			}
	    	return Redirect::to('member-portal/#/home');
	            // return array('status' => TRUE, 'message' => 'Success.');
		}

		return array('status' => FALSE, 'message' => 'Invalid Credentials.');
	}

	public function checkOutofNetwork()
	{
		$input = Input::all();

		$e_claim = DB::table('e_claim')->where('e_claim_id', $input['id'])->first();

		if(!$e_claim) {
			return array('status' => FALSE, 'message' => 'E-Claim does not exist.');
		}

		$user_id = $e_claim->user_id;
		$start = date('Y-m-d H:i:s' , strtotime($e_claim->date));
		$end = date('Y-m-d H:i:s' , strtotime('+23 hours', strtotime($e_claim->date)));

		$in_networks = DB::table('transaction_history')
		->where('UserID', $user_id)
		->where('date_of_transaction', '>=', $start)
		->where('date_of_transaction', '<=', $end)
		->get();

		if(sizeof($in_networks) > 0) {
			$transaction_details = [];
			foreach ($in_networks as $key => $trans) {
				$clinic = DB::table('clinic')->where('ClinicID', $trans->ClinicID)->first();
				$clinic_type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();
				$procedure_temp = "";
				$user = DB::table('user')->where('UserID', $trans->UserID)->first();

				if((int)$trans->multiple_service_selection == 1 || $trans->multiple_service_selection == "1")
				{
	                    // get multiple service
					$service_lists = DB::table('transaction_services')
					->join('clinic_procedure', 'clinic_procedure.ProcedureID', '=', 'transaction_services.service_id')
					->where('transaction_services.transaction_id', $trans->transaction_id)
					->get();

					foreach ($service_lists as $key => $service) {
						if(sizeof($service_lists) - 2 == $key) {
							$procedure_temp .= ucwords($service->Name).' and ';
						} else {
							$procedure_temp .= ucwords($service->Name).',';
						}
						$procedure = rtrim($procedure_temp, ',');
					}
					$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
				} else {
					$service_lists = DB::table('clinic_procedure')
					->where('ProcedureID', $trans->ProcedureID)
					->first();
					if($service_lists) {
						$procedure = ucwords($service_lists->Name);
						$clinic_name = ucwords($clinic_type->Name).' - '.$procedure;
					} else {
	                        // $procedure = "";
						$clinic_name = ucwords($clinic_type->Name);
					}
				}

				$total_amount = number_format($trans->procedure_cost, 2);

				if((int)$trans->health_provider_done == 1 || $trans->health_provider_done == "1") {
					$payment_type = "Cash";
					if((int)$trans->lite_plan_enabled == 1) {
						$total_amount = number_format($trans->procedure_cost + $trans->co_paid_amount, 2);
					}
				} else {
					$payment_type = "Mednefits Credits";
					if((int)$trans->lite_plan_enabled == 1) {
						$total_amount = number_format($trans->credit_cost + $trans->co_paid_amount, 2);
					}
				}

				$refund_text = 'NO';

				if((int)$trans->refunded == 1 && (int)$trans->deleted == 1 || $trans->refunded == "1" && $trans->deleted == "1") {
					$status_text = 'REFUNDED';
					$refund_text = 'YES';
				} else if((int)$trans->health_provider_done == 1 && (int)$trans->deleted == 1 || $trans->health_provider_done == "1" && $trans->deleted == "1") {
					$status_text = 'REMOVED';
					$refund_text = 'YES';
				} else {
					$status_text = FALSE;
				}

				$transaction_id = str_pad($trans->transaction_id, 6, "0", STR_PAD_LEFT);
				$format = array(
					'name'       				=> ucwords($user->Name),
					'clinic_name'       => $clinic->Name,
					'amount'            => $total_amount,
					'procedure'         => $procedure,
					'clinic_type_and_service' => $clinic_name,
					'date_of_transaction' => date('d F Y, h:ia', strtotime($trans->date_of_transaction)),
					'transaction_id'    => strtoupper(substr($clinic->Name, 0, 3)).$transaction_id,
					'user_id'           => $trans->UserID,
					'type'              => 'In-Network',
					'refunded'          => $trans->refunded == 1 || $trans->refunded == "1" ? TRUE : FALSE,
					'refund_text'       => $refund_text,
					'status_text'       => $status_text,
					'payment_type'      => $payment_type,
					'consultation'      => (int) $trans->lite_plan_enabled == 1 ? number_format($trans->co_paid_amount, 2) : "0.00",
					'lite_plan'         => (int)$trans->lite_plan_enabled == 1 ? true : false,
					'spending_type'		=> ucwords($trans->spending_type)
				);

				array_push($transaction_details, $format);
			}

			return array('status' => TRUE, 'data' => $transaction_details);
		} else {
			return array('status' => FALSE, 'message' => 'No Similar In-Network Transaction for this E-Claim.');
		}
	}

	public function revertPending( )
	{
		$result = self::checkSession();
		$input = Input::all();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;

		if(empty($input['e_claim_id']) || $input['e_claim_id'] == null) {
			return array('status' => false, 'message' => 'E Claim ID is required.');
		}

		$e_claim = DB::table('e_claim')->where('e_claim_id', $input['e_claim_id'])->first();

		if(!$e_claim) {
			return array('status' => false, 'messae' => 'E Claim does not exist.');
		}

		if((int)$e_claim->status == 0) {
			return array('status' => false, 'message' => 'E Claim status is already pending.');
		}

		$owner_id = StringHelper::getUserId($e_claim->user_id);

		if((int)$e_claim->status == 1) {
			// delete logs for approved credits and check spending type
			if($e_claim->spending_type == 'medical') {
				$table_wallet_history = 'wallet_history';
			} else {
				$table_wallet_history = 'wellness_wallet_history';
			}

			// find e_claim transaction
			$e_claim_log = DB::table($table_wallet_history)
							->where('id', $e_claim->e_claim_id)
							->where('logs', 'deducted_from_e_claim')
							->where('where_spend', 'e_claim_transaction')
							->first();
			if($e_claim_log) {
				$e_claim_log = DB::table($table_wallet_history)
							->where('id', $e_claim->e_claim_id)
							->where('logs', 'deducted_from_e_claim')
							->where('where_spend', 'e_claim_transaction')
							->delete();

				DB::table('e_claim')
						->where('e_claim_id', $e_claim->e_claim_id)
						->update(['status' => 0]);
			}

		} else if((int)$e_claim->status == 2) {
			// update to pending
			$result = DB::table('e_claim')
						->where('e_claim_id', $e_claim->e_claim_id)
						->update(['status' => 0]);
		}

		if($admin_id) {
			$data = array(
				'e_claim_id' => $e_claim->e_claim_id
			);
			$admin_logs = array(
                'admin_id'  => $admin_id,
                'admin_type' => 'mednefits',
                'type'      => 'admin_hr_revert_to_pending_e_claim',
                'data'      => SystemLogLibrary::serializeData($data)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
		} else {
			$data = array(
				'e_claim_id' => $e_claim->e_claim_id
			);
			$admin_logs = array(
                'admin_id'  => $hr_id,
                'admin_type' => 'hr',
                'type'      => 'admin_hr_revert_to_pending_e_claim',
                'data'      => SystemLogLibrary::serializeData($data)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
		}

		return array('status' => true, 'message' => 'E Claim data status revert to pending.');
	}

	public function uploadOutOfNetworkReceipt( )
	{
		$input = Input::all();

		if(empty($input['e_claim_id']) || $input['e_claim_id'] == null) {
			return array('status' => false, 'message' => 'E Claim ID is required.');
		}

		if(empty(Input::file('file')) || Input::file('file') == null) {
			return array('status' => false, 'message' => 'Please input a file.');
		}

		$transaction_id = (int)preg_replace('/[^0-9]/', '', $input['e_claim_id']);

		$rules = array(
      'file' => 'required | mimes:jpeg,jpg,png,pdf,xls,xlsx',
	  );

		$file_types = ["jpeg","jpg","png","pdf","xls","xlsx","PNG", "JPG", "JPEG"];
		$file = Input::file('file');
		$validator = Validator::make(array('file' => $file), $rules);

		if($validator->fails()){
			return array('status' => false, 'message' => $file->getClientOriginalName().' file is not valid. Only accepts Image, PDF or Excel.');
    }

    $check = DB::table('e_claim')->where('e_claim_id', $transaction_id)->first();

    if(!$check) {
    	return array('status' => false, 'message' => 'E Claim does not exist.');
    }

    $file_name = time().' - '.$file->getClientOriginalName();
    $file_link = array();
    if($file->getClientOriginalExtension() == "pdf") {
        $receipt_file = $file_name;
        $receipt_type = "pdf";
        $file->move(public_path().'/receipts/', $file_name);
        $file_link['file'] = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$file_name;
    } else if($file->getClientOriginalExtension() == "xls" || $file->getClientOriginalExtension() == "xlsx") {
        $receipt_file = $file_name;
        $receipt_type = "xls";
        $file->move(public_path().'/receipts/', $file_name);
        $file_link['file'] = 'https://s3-ap-southeast-1.amazonaws.com/mednefits/receipts/'.$file_name;
    } else {
        $image = \Cloudinary\Uploader::upload($file->getRealPath());
        $receipt_file = $image['secure_url'];
        $receipt_type = "image";
        $file_link['file'] = $image['secure_url'];
    }
    $file_link['type'] = $receipt_type;

    $e_claim_docs = new EclaimDocs( );

    $receipt = array(
        'e_claim_id'    => $transaction_id,
        'doc_file'      => $receipt_file,
        'file_type'     => $receipt_type
    );

    $result_doc = $e_claim_docs->createEclaimDocs($receipt);

    if($result_doc) {
        if($receipt['file_type'] != "image" || $receipt['file_type'] !== "image") {
                          //   aws
           $s3 = AWS::get('s3');
           $s3->putObject(array(
              'Bucket'     => 'mednefits',
              'Key'        => 'receipts/'.$file_name,
              'SourceFile' => public_path().'/receipts/'.$file_name,
          ));
       }
       return array('status' => true,  'message' => 'E-Claim Receipt created successfully.', 'file_link' => $file_link);
     } else {
        $email = [];
        $email['end_point'] = url('v2/user/create_e_claim', $parameter = array(), $secure = null);
        $email['logs'] = 'E-Claim Mobile Receipt Submission - '.$e;
        $email['emailSubject'] = 'Error log.';
        EmailHelper::sendErrorLogs($email);
        return array('status' => false, 'message' => 'E-Claim created successfully but failed to create E-Receipt.');
    }
	}

	public function getPresignedEclaimDoc( )
	{
		$input = Input::all();

		if(empty($input['id']) || $input['id'] == null) {
			return array('status' => false, 'message' => 'ID is required.');
		};

		$doc = DB::table('e_claim_docs')->where('e_claim_doc_id', $input['id'])->first();

		if(!$doc) {
			return array('status' => false, 'message' => 'E CLaim Doc does not exist.');
		}

		$file_types = ["pdf", "xls", "xlsx"];

		if(!in_array($doc->file_type, $file_types)) {
			return array('status' => false, 'message' => 'E CLaim Doc is not a pdf, xls or xlsx.');
		}

		// $s3 = AWS::get('s3');
		// $cmd = $s3->getCommand('GetObject', [
	 //    'Bucket' => 'mednefits',
	 //    'Key' => "receipts/".$doc->doc_file
		// ]);

		// // return var_dump($cmd);

		// $request = $s3->createPresignedRequest($cmd, '+20 minutes');

		// // Get the actual presigned-url
		// $presignedUrl = (string)$request->getUri();
		// $presignedUrl = $s3->getObjectUrl('mednefits/receipts', $doc->doc_file, '+120 minutes');
		
		return EclaimHelper::createPreSignedUrl($doc->doc_file);
	}

	public function downloadEclaimCsv( )
	{
		$input = Input::all();
		if(empty($input['token']) || $input['token'] == null) {
			return array('status' => false, 'message' => 'Token is required.');
		}

		if(empty($input['start']) || $input['start'] == null) {
			return array('status' => false, 'message' => 'Start Date parameter is required.');
		}

		if(empty($input['end']) || $input['end'] == null) {
			return array('status' => false, 'message' => 'End Date parameter is required.');
		}

		$result = StringHelper::getJwtHrToken($input['token']);
		if(!$result) {
			return array(
				'status'	=> FALSE,
				'message'	=> 'Need to authenticate user.'
			);
		}

		$start = date('Y-m-d', strtotime($input['start']));
		$end = PlanHelper::endDate($input['end']);
		$spending_type = isset($input['spending_type']) ? $input['spending_type'] : 'medical';
		$account = DB::table('customer_link_customer_buy')->where('customer_buy_start_id', $result->customer_buy_start_id)->first();
		$container = array();

		if(!empty($input['user_id']) && $input['user_id'] != null) {
			$corporate_members = DB::table('corporate_members')
									->where('corporate_id', $account->corporate_id)
									->where('user_id', $input['user_id'])
									->get();
		} else {
			$corporate_members = DB::table('corporate_members')
									->where('corporate_id', $account->corporate_id)
									->get();
		}

		foreach ($corporate_members as $key => $member) {
			$ids = StringHelper::getSubAccountsID($member->user_id);
			if(!empty($input['status']) && (int)$input['status'] == 2) {
				$e_claim_result = DB::table('e_claim')
												->whereIn('user_id', $ids)
												->where('spending_type', $spending_type)
												->where('status', 0)
												->where('created_at', '>=', $start)
												->where('created_at', '<=', $end)
												->orderBy('created_at', 'desc')
												->get();
			} else if(!empty($input['status']) && (int)$input['status'] == 3) {
				$e_claim_result = DB::table('e_claim')
												->whereIn('user_id', $ids)
												->where('spending_type', $spending_type)
												->where('status', 1)
												->where('created_at', '>=', $start)
												->where('created_at', '<=', $end)
												->orderBy('created_at', 'desc')
												->get();
			} else if(!empty($input['status']) && (int)$input['status'] == 4) {
				$e_claim_result = DB::table('e_claim')
												->whereIn('user_id', $ids)
												->where('spending_type', $spending_type)
												->where('status', 2)
												->where('created_at', '>=', $start)
												->where('created_at', '<=', $end)
												->orderBy('created_at', 'desc')
												->get();
			} else {
				$e_claim_result = DB::table('e_claim')
												->whereIn('user_id', $ids)
												->where('spending_type', $spending_type)
												->where('created_at', '>=', $start)
												->where('created_at', '<=', $end)
												->orderBy('created_at', 'desc')
												->get();
			}

			foreach($e_claim_result as $key => $res) {
				$approved_status = FALSE;
				$rejected_status = FALSE;

				if($res->status == 0) {
					$status_text = 'Pending';
				} else if($res->status == 1) {
					$status_text = 'Approved';
				} else if($res->status == 2) {
					$status_text = 'Rejected';
				} else {
					$status_text = 'Pending';
				}


	                // get docs
				$docs = DB::table('e_claim_docs')->where('e_claim_id', $res->e_claim_id)->get();


				$member = DB::table('user')->where('UserID', $res->user_id)->first();

				if($member->UserType == 5 && $member->access_type == 2 || $member->UserType == 5 && $member->access_type == 3) {
					$temp_sub = DB::table('employee_family_coverage_sub_accounts')->where('user_id', $member->UserID)->first();
					$temp_account = DB::table('user')->where('UserID', $temp_sub->owner_id)->first();
					$sub_account = ucwords($temp_account->Name);
					$sub_account_type = $temp_sub->user_type;
					$owner_id = $temp_sub->owner_id;
					$relationship = $temp_sub->relationship ? ucwords($temp_sub->relationship) : 'Dependent';
					$bank_account_number = $temp_account->bank_account;
					$bank_name = $temp_account->bank_name;
					$bank_code = $temp_account->bank_code;
					$bank_brh = $temp_account->bank_brh;
				} else {
					$sub_account = FALSE;
					$sub_account_type = FALSE;
					$owner_id = $member->UserID;
					$relationship = false;
					$bank_account_number = $member->bank_account;
					$bank_name = $member->bank_name;
					$bank_code = $member->bank_code;
					$bank_brh = $member->bank_brh;
				}

				if($res->status == 1) {
					$approved_status = true;
				}

				$id = str_pad($res->e_claim_id, 6, "0", STR_PAD_LEFT);
				$container[] = array(
					'MEMBER'						=> ucwords($member->Name),
					'NRIC'							=> $member->NRIC,
					'CLAIM MEMBER TYPE'	=> $relationship ? 'DEPENDENT' : 'EMPLOYEE',
					'EMPLOYEE'					=> $sub_account ? $sub_account : null,
					'CLAIM DATE'				=> date('d F Y h:i A', strtotime($res->created_at)),
					'VISITED DATE'				=> date('d F Y', strtotime($res->date)).', '.$res->time,
					'TRANSACTION #'			=> 'MNF'.$id,
					'CLAIM TYPE'				=> $res->service,
					'PROVIDER'					=> $res->merchant,
					'SPENDING ACCOUNT'	=> ucwords($res->spending_type),
					'TOTAL AMOUNT'			=> number_format($res->amount, 2),
					'TYPE'							=> 'E-Claim',
					'STATUS'						=> $status_text,
					'APPROVED DATE'			=> $approved_status == TRUE ? date('d F Y h:i A', strtotime($res->updated_at)) : null,
					'REJECTED DATE'			=> $rejected_status == TRUE ? date('d F Y h:i A', strtotime($res->updated_at)) : null,
					'REJECTED REASON'		=> $res->rejected_reason,
					'BANK ACCOUNT NUMBER'	=> (string)$bank_account_number,
					'BANK CODE'					=> $bank_code,
					'BRANCH CODE'				=> $bank_brh
				);
			}
		}
		
		usort($container, function($a, $b) {
        return strtotime($b['CLAIM DATE']) - strtotime($a['CLAIM DATE']);
    });

		// return $container;
		return \Excel::create('E-Claim Transactions - '.$start.' - '.$input['end'], function($excel) use($container) {
      $excel->sheet('E-Claim', function($sheet) use($container) {
          $sheet->fromArray( $container );
      });
    })->export('xls');

	}
}
?>
