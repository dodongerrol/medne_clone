<?php
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
class CorporateController extends BaseController {

	function __construct()
	{
		
    }

	public function index( )
	{	
		$data['title'] = 'Corporate';
		return View::make('admin.corporate', $data);
	}

	public function createCorporate( )
	{
		$corporate_data = new Corporate();
		$input = Input::all();

		$check_email = $corporate_data->checkEmail($input['email']);
		if($check_email > 0) {
			return array(
				'status'	=> 400,
				'message'	=> 'Email Already Taken.'
			);
		}

		$check_identification = $corporate_data->checkIdentification($input['identification_number']);

		if($check_identification > 0) {
			return array(
				'status'	=> 400,
				'message'	=> 'Identifcation Number Already Taken.'
			);
		}

		$user = new User();
		// return $input;
		// return $input;
		$password = StringHelper::get_random_password(8);
		// $user_id = StringHelper::get_random_password(8).time();
		$name = $input['fname'].' '.$input['lname'];
		$user_id = $user->createUserFromCorporate($input['email'], $input['phone'], $input['code'], $name, $password, $input['credit']);

        $corporate = array(
        	'UserID'			=> $user_id,
        	'first_name'		=> $input['fname'],
        	'last_name'			=> $input['lname'],
        	'email'				=> $input['email'],
        	'credit'			=> $input['credit'],
        	'company_name'		=> $input['company_name'],
        	'identification_number'		=> $input['identification_number'],
        	'password'			=> StringHelper::encode($password),
        	'created_at'		=> Carbon::now(),
        	'updated_at'		=> Carbon::now()
        );
        // return $corporate;
        $result = $corporate_data->createCorporate($corporate);
        // return var_dump($result);
        if( $result ) {
	        $emailDdata['emailName']= 'New Medicloud Corporate Account';
	        $emailDdata['emailPage']= 'email-templates.welcome-corporate';
	        $emailDdata['emailTo']= $input['email'];
	        $emailDdata['name']= $input['fname'].' '.$input['fname'];
	        $emailDdata['credit']= $input['credit'];
	        $emailDdata['emailSubject'] = "Welcome To Mednefits";
	        $emailDdata['data'] = $result;
	        $emailDdata['pw'] = $password;
	        $emailDdata['user_id'] = $user_id;
	        return EmailHelper::sendEmail($emailDdata);
        } else {
        	return "false";
        }
	}

	public function getListCorporate( )
	{
		$corporate_data = new Corporate();
		$data['result'] = $corporate_data->getListCorporate( );
		return View::make('admin.corporate-list', $data);
	}

	public function updateCorporate( ) 
	{
		$corporate_data = new Corporate();
		$input = Input::all();
		$corporate = array(
        	'first_name'		=> $input['fname'],
        	'last_name'			=> $input['lname'],
        	'email'				=> $input['email'],
        	'credit'			=> $input['credit'],
        	'company_name'		=> $input['company_name'],
        	'updated_at'		=> Carbon::now()
        );

		$result = $corporate_data->updateCorporate($corporate, $input['id']);
		if($result) {
			$wallet = new Wallet();
			$id = $corporate_data->findUserID($input['id']);
			return $wallet->updateWallet($id->UserID, $input['credit']);
		}
	}

	public function activateAccount($id) 
	{	
		// return $id;
		$corporate_data = new Corporate();
		$result['result'] = $corporate_data->activateAccount($id);
		if($result != "false") {
			return View::make('email-templates.corporate_confirmation', $result);
		}
	}

	public function searchCoporate( )
	{
		$input = Input::all();
		$corporate_data = new Corporate();
		return $corporate_data->searchCoporate($input['search']);
	}

	public function getCorporateById($id)
	{
		$corporate_data = new Corporate();
		return json_encode($corporate_data->getCorporateById($id));
	}

	public function getDoctors( )
	{
		// $input = Input::all();
		// return $input;
		$doctors = new CalendarController();
		$getSessionData = StringHelper::getMainSession(3);
		// $getSessionData->Ref_ID
		return $doctors->getClinicDoctors($getSessionData->Ref_ID);
	}
	public function allCorporate( ) 
	{
		$corporate_data = new Corporate();
		return $corporate_data->allCorporate();
	}

	public function getDoctorProcedure($id)
	{
		$procedure = new CalendarController();
		$getSessionData = StringHelper::getMainSession(3);
		return $procedure->loadDoctorProcedures($getSessionData->Ref_ID, $id);
	}

	// corporate members
	public function addCorporateMembers( )
	{
		$input = Input::all();
	}

	public function getCompanyDateTerms( )
	{
		$input = Input::all();
		$result = StringHelper::getJwtHrSession();
		$user_id = $result->customer_buy_start_id;
		$current_term = CustomerHelper::getCustomerDateTerms($user_id, 'current_term', 'medical');
		$last_term = CustomerHelper::getCustomerLastTerm($user_id);

		return ['status' => true, 'current_term' => $current_term, 'last_term' => $last_term];
	}

	public function updateCompanyHrDetails (Request $request)
	{
		if(empty($request->get('customer_id')) || $request->get('customer_id') == null) {
			return array('status' => false, 'message' => 'Customer ID is required.');
		}

		if(empty($request->get('customer_business_contact_id')) || $request->get('customer_business_contact_id') == null) {
			return array('status' => false, 'message' => 'Customer Business Contact ID is required.');
		}

		if(empty($request->get('first_name')) || $request->get('first_name') == null) {
			return array('status' => false, 'message' => 'Customer Business Contact First Name is required.');
		}

		$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $request->get('customer_id'))->first();
		
		if(!$check) {
			return array('status' => false, 'message' => 'Company does not exist.');
		}

		$data = array(
			'first_name'				=> $request->get('first_name'),
			'last_name'					=> $request->get('last_name'),
			'billing_email'				=> $request->get('work_email'),
			'phone'						=> !empty($request->get('phone')) ? $request->get('phone') : null,
			'updated_at'				=> date('Y-m-d H:i:s')
		);

		if(!empty($request->get('billing_name')) || $request->get('billing_name') != null) {
			$data['billing_name'] = $request->get('billing_name');
		}

		$result = DB::table('customer_billing_contact')
		->where('customer_billing_contact_id', $request->get('customer_billing_contact_id'))
		->update($data);

		if($result) {
			$admin_id = \AdminHelper::getAdminID();
			if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'type'      => 'admin_updated_company_billing_contact_details',
					'data'      => \AdminHelper::serializeData($data)
				);
				\AdminHelper::createAdminLog($admin_logs);
			}
			return array('status' => true, 'message' => 'Company Billing Contact Details updated.');
		} else {
			return array('status' => false, 'message' => 'Failed to update Company Billing Contact Details.');
		}
	}

	public function resendCorporateActivationEmail ( )
    {
		$input = Input::all();

		if(empty($input['id']) || $input['id'] == null) {
			return ['status' => false, 'message' => 'id is required.'];
		}

        $message = [];
        $emailData = [];
        $id = (int)$input['id'];
        $user = DB::table('user')
        ->where('UserID', $id)
		->first();

		if(!$user) {
			return array('status' => FALSE, 'message' => 'Member does not exist.');
		}

        // if(url('/') == 'https://admin.medicloud.sg') {
        //     $url = 'https://medicloud.sg/company-benefits-dashboard';
        // } else if(url('/') == 'http://stage.medicloud.sg') {
				// 		$url = 'http://staging.medicloud.sg/company-benefits-dashboard';
        // } else if(url('/') == 'http://stage_v2.medicloud.sg') {
				// 	$url = 'http://staging_v2.medicloud.sg/company-benefits-dashboard';
				// }	else {
        //     $url = 'http://medicloud.local/company-benefits-dashboard';
				// }
				
				if(url('/') == 'https://medicloud.sg') {
					$url = 'https://medicloud.sg/company-benefits-dashboard';
				} else if(url('/') == 'http://staging.medicloud.sg') {
					$url = 'http://staging.medicloud.sg/company-benefits-dashboard';
				} else if(url('/') == 'http://staging_v2.medicloud.sg') {
					$url = 'http://staging_v2.medicloud.sg/company-benefits-dashboard';
				}	else {
					$url = 'http://medicloud.local/company-benefits-dashboard';
				}

        if((int)$user->member_activated == 0) {
            $emailDdata['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
            $emailDdata['emailTo']= $user->Email;
            $emailDdata['emailName'] = ucwords($user->Name);
            $emailDdata['emailPage'] = 'email-templates.newAccountLogin.member-activation-email';
            $emailDdata['url'] = $url;
            $emailDdata['code'] = $user->PhoneCode;
            $emailDdata['phone'] = $user->PhoneNo;
            $emailDdata['button'] = $url.'/company-benefits-dashboard-login';
            
            \EmailHelper::sendEmail($emailDdata);
			return array('status' => TRUE, 'message' => 'Successfully resend activation email.');         
		} else {
			return array('status' => FALSE, 'message' => 'Member already Activated');
		}
	}

	public function unlinkCompanyAccount( )
	{
		$data = Input::all();
		$result = StringHelper::getJwtHrSession();
		// $customer_id = $result->customer_buy_start_id;

		if(empty($data['customer_id']) || $data['customer_id'] == null) {
			return ['status' => false, 'message' => 'customer_id is required'];
		}
	
		if(empty($data['hr_id']) || $data['hr_id'] == null) {
			return ['status' => false, 'message' => 'hr_id is required'];
		}

		if(empty($data['fullname']) || $data['fullname'] == null) {
			return ['status' => false, 'message' => 'fullname is required'];
		}

		if(empty($data['email']) || $data['email'] == null) {
			return ['status' => false, 'message' => 'email is required'];
		}

		if(empty($data['phone_code']) || $data['phone_code'] == null) {
			return ['status' => false, 'message' => 'phone_code is required'];
		}

		if(empty($data['phone_no']) || $data['phone_no'] == null) {
			return ['status' => false, 'message' => 'phone_no is required'];
		}

		$action_type = !empty($data['action_type']) ? $data['action_type'] : 'unlink';
		$admin_id = Session::get('admin-session-id') ? Session::get('admin-session-id') : $result->hr_dashboard_id;
		$admin_type = Session::get('admin-session-id') ? 'mednefits': 'hr';
		// check email address if there is a primary hr admin
		$primary = DB::table('customer_hr_dashboard')->where('email', $data['email'])->first();
		
		$customer_id = $data['customer_id'];
		$old_hr_id_link = $result->hr_dashboard_id;

		if($primary) {
			// check if hr account is already process the activate flow
			if((int)$primary->hr_activated == 0) {
				return ['status' => false, 'message' => 'Primary HR Account is not yet activated. Please activate the account to link the company to this Primary HR Account'];
			}

			$hr_id = $primary->hr_dashboard_id;
			$old_under_customer_id = $data['hr_id'];
			// check if customer id exist in link account
			$linkAccont = DB::table('company_link_accounts')->where('hr_id', $hr_id)->where('customer_id', $customer_id)->where('status', 1)->first();

			if($linkAccont) {
				return ['status' => false, 'message' => 'Account already linked.'];
			}

			$info = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
			$under_hr = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $old_under_customer_id)->first();
			// update existing linking from old hr
			DB::table('company_link_accounts')->where('hr_id', $data['hr_id'])->where('customer_id', $customer_id)->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
			// insert to new hr link
			DB::table('company_link_accounts')->insert(['hr_id' => $hr_id, 'customer_id' => $customer_id, 'status' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
			
			if($admin_id) {
				$unlinked = array(
					'customer_id'       => $customer_id,
					'hr_id'             => $data['hr_id'],
					'status'            => 0
				);

				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => $admin_type,
					'type'      => 'admin_unlinked_company_account',
					'data'      => serialize($unlinked)
				);
				SystemLogLibrary::createAdminLog($admin_logs);

				$newlinked = array(
					'customer_id'       => $customer_id,
					'hr_id'             => $hr_id,
					'status'            => 1
				);

				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => $admin_type,
					'type'      => 'admin_linked_company_account',
					'data'      => serialize($newlinked)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}

			if($action_type == "change_primary") {
				// get latest company that is active from hr company link
				$token = \CustomerHelper::generateNewHrAccountLinkLoginToken($result);
				return [
					'status' => true,
					'type'	=> 'change_primary',
					'company' => ucwords($info->account_name),
					'hr_account' => ucwords($primary->fullname),
					'token'		=> $token,
					'message' => 'success'
				];
			} else {
				return [
					'status' => true,
					'type'	=> 'unlink',
					'company' => ucwords($info->account_name),
					'hr_account' => ucwords($under_hr->fullname),
					'message' => 'success'
				];
			}
		} else {
			// update existing linking from old hr
			$updateUnlink = DB::table('company_link_accounts')->where('hr_id', $data['hr_id'])->where('customer_id', $customer_id)->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
			$hr_id = $data['hr_id'];
			$old_under_customer_id = $data['hr_id'];
			$under_hr = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $old_under_customer_id)->first();
			$info = DB::table('customer_buy_start')->where('customer_buy_start_id', $customer_id)->first();
			// create new activation and information for hr
			if(url('/') == 'https://medicloud.sg') {
				$url = 'https://medicloud.sg';
			} else if(url('/') == 'http://staging.medicloud.sg') {
				$url = 'http://staging.medicloud.sg';
			} else if(url('/') == 'http://staging_v2.medicloud.sg') {
				$url = 'http://staging_v2.medicloud.sg';
			} else {
				$url = 'http://medicloud.local';
			}
			
			$password = \CustomerHelper::get_random_password(8);
			// update hr information
			$reset_link = StringHelper::getEncryptValue();
			$hr = array(
				'fullname'				=> $data['fullname'],
				'email'					=> $data['email'],
				'phone_code'			=> "+".$data['phone_code'],
				'phone_number'			=> $data['phone_no'],
				'password'				=> md5($password),
				'temp_password'			=> $password,
				'qr_payment'			=> 1,
				'wallet'				=> 1,
				'active'				=> 0,
				'billing_status'		=> 1,
				'expiration_time'		=> date('Y-m-d H:i:s', strtotime('+7 days')),
				'hr_activated'			=> 0,
				'reset_link'			=> $reset_link,
				'is_account_linked' => 0,
				'created_at'		=> date('Y-m-d H:i:s'),
				'updated_at'      => date('Y-m-d H:i:s')
			);
		
			$newHRId = DB::table('customer_hr_dashboard')->insertGetId($hr);
			// create link account
			// insert to new hr link
			DB::table('company_link_accounts')->insert(['hr_id' => $newHRId, 'customer_id' => $customer_id, 'status' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
			// send hr email activation
			$email_data = array();
			$email_data['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
			$email_data['emailName'] = ucwords($data['fullname']);
			$email_data['emailPage'] = 'email-templates.activation-email';
			$email_data['emailTo'] = $data['email'];
			$email_data['button'] = $url.'/company-activation#/activation-link?activation_token='.$reset_link;
			\EmailHelper::sendEmail($email_data);

			if($admin_id) {
				$unlinked = array(
					'customer_id'       => $customer_id,
					'hr_id'             => $data['hr_id'],
					'status'            => 0
				);

				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => $admin_type,
					'type'      => 'admin_unlinked_company_account',
					'data'      => serialize($unlinked)
				);
				SystemLogLibrary::createAdminLog($admin_logs);

				$newlinked = array(
					'customer_id'       => $customer_id,
					'hr_id'             => $newHRId,
					'status'            => 1
				);

				$admin_logs = array(
					'admin_id'  => $admin_id,
					'admin_type' => $admin_type,
					'type'      => 'admin_linked_company_account',
					'data'      => serialize($newlinked)
				);
				SystemLogLibrary::createAdminLog($admin_logs);
			}

			if($action_type == "change_primary") {
				// get latest company that is active from hr company link
				$token = \CustomerHelper::generateNewHrAccountLinkLoginToken($result);
				return [
					'status' => true,
					'type'	=> 'change_primary',
					'company' => ucwords($info->account_name),
					'hr_account' => ucwords($data['fullname']),
					'token'		=> $token,
					'message' => 'success'
				];
			} else {
				return [
					'status' => true,
					'type'	=> 'unlink',
					'company' => ucwords($info->account_name),
					'hr_account' => ucwords($under_hr->fullname),
					'message' => 'success'
				];
			}
		}
	}
	
	public function getCorporateLinkedAccount( ) {

		$input = Input::all();
		$result = StringHelper::getJwtHrSession();
		$customer_id = $result->customer_buy_start_id;
		$hr_id = $result->hr_dashboard_id;
		$limit = !empty($input['limit']) ? $input['limit'] : 5;
		$search = !empty($input['search']) ? $input['search'] : null;
		$except_current = isset($input['except_current']) && $input['except_current'] == "enable" ? true : false;
		
		if($search) {
			$link_accounts = DB::table('company_link_accounts')
							->join('customer_business_information', 'customer_business_information.customer_buy_start_id', '=', 'company_link_accounts.customer_id')
							->join('customer_buy_start', 'customer_buy_start.customer_buy_start_id', '=', 'company_link_accounts.customer_id')
							->where(function($query) use ($search, $hr_id){
								$query->where('company_link_accounts.hr_id', $hr_id)
								->where('company_link_accounts.status', 1)
								->where('customer_business_information.company_name', 'like', '%'.$search.'%');
							})
							->orWhere(function($query) use ($search, $hr_id){
								$query->where('company_link_accounts.hr_id', $hr_id)
								->where('company_link_accounts.status', 1)
								->where('customer_buy_start.account_name', 'like', '%'.$search.'%');
							})
							->get();
		} else {
			if($except_current) {
				$link_accounts = DB::table('company_link_accounts')
							->where('hr_id', $hr_id)
							->where('customer_id', '!=', $customer_id)
							->where('status', 1)
							->paginate($limit);
			} else {
				$link_accounts = DB::table('company_link_accounts')
							->where('hr_id', $hr_id)
							->where('status', 1)
							->paginate($limit);
			}
		}
		

		$total_enrolled_employee_status = !empty($input['total_enrolled_employee_status']) && $input['total_enrolled_employee_status'] === "true" || !empty($input['total_enrolled_employee_status']) && $input['total_enrolled_employee_status'] === true ? true : false;
		$total_enrolled_dependent_status = !empty($input['total_enrolled_dependent_status']) && $input['total_enrolled_dependent_status'] === "true" || !empty($input['total_enrolled_dependent_status']) && $input['total_enrolled_dependent_status'] === true ? true : false;

		if(!$search) {
			$pagination['last_page'] = $link_accounts->getLastPage();
			$pagination['current_page'] = $link_accounts->getCurrentPage();
			$pagination['total_data'] = $link_accounts->getTotal();
			$pagination['from'] = $link_accounts->getFrom();
			$pagination['to'] = $link_accounts->getTo();
			$pagination['count'] = $link_accounts->count();
		}

		$format = [];
		foreach($link_accounts as $key => $account) {
			$customer = DB::table('customer_buy_start')->where('customer_buy_start_id', $account->customer_id)->first();
			$info = DB::table('customer_business_information')->where('customer_buy_start_id', $account->customer_id)->first();
			$plan = DB::table('customer_plan')->where('customer_buy_start_id', $account->customer_id)->orderBy('created_at', 'desc')->first();
			$hrAccount = DB::table('customer_hr_dashboard')->where('hr_dashboard_id', $account->hr_id)->first();

			$temp = array(
				'id'	=> $account->id,
				'hr_id'	=> $account->hr_id,
				'email' => $hrAccount->email,
				'company_id' => $account->customer_id,
				'account_name' => $customer->account_name ? ucwords($customer->account_name) : ucwords($info->company_name),
				'link_date' => date('d/m/Y', strtotime($customer->created_at)),
				'plan_type' => \PlanHelper::getAccountType($plan->account_type)
			);

			if($total_enrolled_employee_status == true || $total_enrolled_dependent_status == true) {
				if($total_enrolled_employee_status == true) {
					$employee_status = DB::table('customer_plan_status')
											->where('customer_plan_id', $plan->customer_plan_id)
											->orderBy('created_at', 'desc')
											->first();
					
					if($employee_status) {
						$temp['total_enrolled_employee_status'] = $employee_status->enrolled_employees;
					} else {
						$temp['total_enrolled_employee_status'] = 0;
					}
				}

				if($total_enrolled_dependent_status == true) {
					$dependent_status = DB::table('dependent_plan_status')
											->where('customer_plan_id', $plan->customer_plan_id)
											->orderBy('created_at', 'desc')
											->first();
					
					if($employee_status) {
						$temp['total_enrolled_dependent_status'] = $dependent_status->total_enrolled_dependents;
					} else {
						$temp['total_enrolled_dependent_status'] = 0;
					}
				}
			}

			$format[] = $temp;
		}

		$pagination['status'] = true;
		$pagination['data'] = $format;
		return $pagination;
	}
}

