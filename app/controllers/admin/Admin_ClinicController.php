<?php
// use Excel;
use Illuminate\Http\Response;

class Admin_ClinicController extends \BaseController {


	/* Use          :       Used to show all clinics details
     * Access       :       public
     * Return       :       view
     *
     */
		 public function getAllClinics()
    {
	 	$dataArray = array();
        $dataArray['title'] = "All Clinics";
        $getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
            $clinic = new Admin_Clinic();
            $dataArray['resultSet'] = $clinic->GetClinicDetails();
            return View::make('admin.manage-clinics',$dataArray);
        }else{
            return Redirect::to('admin/auth/login');
        }

    }
    public function getClinicsInfo( ) {
    	$clinic = new Admin_Clinic();
    	$id = Input::all();
    	return $clinic->getClinicdata($id['clinicID']);
    }

		public function getSignedUsers()
	 {
		 $dataArray['title'] = "Signed Users";
		 	$getSessionData = AdminHelper::AuthSession();
			 if($getSessionData !=FALSE){
					 return View::make('admin.download-signed-users', $dataArray);
			 }else{
					 return Redirect::to('admin/auth/login');
			 }
		}

    public function showAllClinic()
    {
    	$allClinicsData = new Admin_Clinic();
	 	$selection = $allClinicsData->GetClinicDetails();
	 	$collection = Datatable::query($selection)
	        ->showColumns('ClinicID','Name', 'image','City','Country','Lat', 'Lng','Phone','Opening','ActiveCount','InactiveCount', 'medicloud_transaction_fees')
	        ->setSearchWithAlias()
	        ->searchColumns('ClinicID','Name')
	        ->addColumn('medicloud_transaction_fees', function($model){
	        	return $model->medicloud_transaction_fees.'%';
	        })
	        ->addColumn('Active',function($model)
	        {
	           if ($model->Active == 1)
			   {
					return 'Active';
			   }
			   else
		   	   {
		   	   		return 'Inactive';
			   }
	        })
	        ->addColumn('Edit',function($model)
	        {
	            return "<a href=".$model->ClinicID."/edit class='btn btn-info'>Edit</a>" ;
	        })
	        ->addColumn('Set Time',function($model)
	        {
	            return "<a href=".$model->ClinicID."/new-time class='btn btn-info'>Set Time</a>" ;
	        })
	        ->make();
		return $collection;
    }
	/* Use          :       Used to add new clinic details
     * Access       :       public
     * Return       :
     *
     */
	public function AddNewClinic(){
            $getSessionData = AdminHelper::AuthSession();
            if($getSessionData !=FALSE){
            $dataArray = array();
            $dataArray['title'] = "Add a new clinic";
            $insuranceCompanyData = new Admin_Insurance_Company();
            $clinicType = new Admin_Clinic_Type();
            $dataArray['company'] = $insuranceCompanyData->GetInsuranceCompanyList();
            $dataArray['clinicTypes'] = $clinicType->GetClinicTypes();
            return View::make('admin.add-clinic', $dataArray);
            }else{
                return Redirect::to('admin/auth/login');
            }
	}

// nhr add new new clinic

public function newClinic()
{
	$input = Input::all();

	$name = $input['name'];
	$email = $input['email'];
	$password = $input['password'];

	$user = new Admin_User();
	$finduser = $user->FindExistingClinic($email);

	if (!$finduser){

		$clinic = new Admin_Clinic();
		$clinic_id = $clinic->newClinic();


		$user->AddUser(array('name' => $name,'email'=>$email,'password'=>$password, 'clinic_id'=>$clinic_id ));

		return 1;

	}else{

		return 0;
	}

}




	/* Use          :       Used to insert new clinic details
     * Access       :       public
     * Return       :       none
     *
     */
	public function InsertClinic()
	{
		$validate = Validator::make(Input::all(),

			array(
					'name'=>'required',
					'address'=>'required',
					'file'=>'image',
					'city'=>'required',
					'country'=>'required',
					'postal'=>'required',
					'latitude'=>'required',
					'longitude'=>'required',
					'phone'=>'required',
					'opening'=>'required',
					'email' => 'required|email|unique:user',
					'password' => 'required | min:6',
					'clinic_price'=>'image'

				),
			array(
					'name.required'=>'Name is required'
				)
			);

		if ($validate->fails()) {
			Input::flash();
			return Redirect::to('admin/clinic/new-clinic')->withErrors($validate);
		}
		else
		{
			$clinicData = new Admin_Clinic();
			$clinicId = $clinicData->AddClinic();

			$clinicTypeId               = Input::get('clinic_type');
			if(!empty($clinicTypeId))
			{
				$clinicTypeDetail = new Admin_Clinic_Types_Detail();
				$clinicTypeDetail->AddClinicTypeDetail($clinicTypeId, $clinicId);
			}
			// Insert user details
			$userData = new Admin_User();

			$insuranceID               = array();
			$insuranceID               = Input::get('insurance_company');

			if (!empty(Input::get('email')))
			{

				$userData->Ref_ID  = $clinicId;
				$allData = $userData->AddUser();

				// if($clinicId)
				// {
				// 	//Send email when add new clinic
	   //              $clinicDetails = Admin_User::find($clinicId);
	   //              $emailDdata['emailName']= $clinicDetails->Name;
	   //              //$emailDdata['emailPage']= 'email-templates.test';
    //                 $emailDdata['emailPage']= 'email-templates.new-clinic';
	   //              $emailDdata['emailTo']= $clinicDetails->Email;
	   //              $emailDdata['emailSubject'] = 'Login credentials in your clinic';
	   //              $emailDdata['email'] = $clinicDetails->Email;
	   //              $emailDdata['password'] = Input::get ('password');
	   //              EmailHelper::sendEmail($emailDdata);
				// }
			}

			if (!empty($insuranceID))
			{
				foreach($insuranceID as $id)
				{
					$clinicInsuranceData = new Admin_Clinic_Insurance_Company();
					$clinicInsuranceData->AddClinicInsuranceData($id, $clinicId);
				}
			}


		}

		return Redirect::to('admin/clinic/all-clinics');
	}



	/* Use          :       Used to edit clinic details
     * Access       :       public
     * Return       :       view
     *
     */
	public function EditClinic($id)
	{
        $getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
		$dataArray = array();
		$dataArray['title'] = "Edit clinic";
		$insuranceCompanyData = new Admin_Insurance_Company();
		$dataArray['company'] = $insuranceCompanyData->GetInsuranceCompanyList();
		$dataArray['insuranceID'] = $insuranceCompanyData->InsuranceCompanyByID($id);
		$clinicType = new Admin_Clinic_Type();
		$dataArray['clinicTypes'] = $clinicType->GetClinicTypes();
		$clinicTypeDetail = new Admin_Clinic_Types_Detail();
		$dataArray['cliniTypeID'] = $clinicTypeDetail->ClinicTypeByID($id);
		// get the clinic details by id
		$clinic = Admin_Clinic::find($id);
		// get the user details by id
		$user 	= Admin_User::find($id);
		// show the edit form and pass the clinic
		return View::make('admin.edit-clinics', $dataArray)
		->with('clinic', $clinic)
		->with('user', $user);
             }else{
                return Redirect::to('admin/auth/login');
            }
	}



	/* Use          :       Used to update clinic details
     * Access       :       public
     * Return       :       view
     *
     */
	public function UpdateClinic($id)
	{
		$validate = Validator::make(Input::all(),

			array(
				'name'=>'required',
				'file'=>'image',
				'email' => 'email|unique:user,Email,'.$id.',Ref_ID',
				'password' => 'min:6',
				'clinic_price'=>'image'


				),
			array(
				'name.required'=>'Name is required'
				)
			);
                $findEmailUser = Admin_User::FindUserByEmail(Input::get('email'));
                $foundEmail = 0;
                if($findEmailUser){
                    if($findEmailUser->Ref_ID != $id){
                        $foundEmail = 1;
                    }
                }
		if ($validate->fails() || $foundEmail==1)

		//if ($validate->fails())

		{
			Input::flash();
			return Redirect::to('admin/clinic/'.$id.'/edit')
			       ->withErrors($validate)
			       ->withInput(Input::except('file'))
		       	   ->withInput(Input::except('password'));
		}
		else
		{
			//Update clinic details
			$data 			     = Admin_Clinic::find($id);
			$data->Custom_title	 = Input::get('custom_title');
			$data->Name          = Input::get('name');
			$data->Description   = Input::get('description');
			$data->Website  	 = Input::get('website');
			$data->Address       = Input::get('address');
			$data->City          = Input::get('city');
			$data->State         = Input::get('state');
			$data->Country       = Input::get('country');
			$data->Postal        = Input::get('postal');
			$data->District      = Input::get('district');
			$data->Lat           = Input::get('latitude');
			$data->Lng           = Input::get('longitude');
			$data->Phone         = Input::get('phone');
			$data->MRT           = Input::get('mrt');
			$data->Opening       = Input::get('opening');
			$data->Active        = Input::get('status');
			$data->medicloud_transaction_fees = Input::get('transaction_fees');

	        // Update user details
			$userData             = Admin_User::find($id);
			if (!empty($userData))
			{
				$userData->Email      = Input::get('email');
				$passwordVal		  = Input::get('password');

				if(!empty($passwordVal))
				{
					$userData->Password   = StringHelper::encode(Input::get('password'));
				}
				else
				{
					Input::except('password');
				}

	    		$userData->save();

			}
		 	else
           	{		$userDetails = new Admin_User();
	           		$userDetails->AddUser();
           	}

			//check image
		 	if(Input::hasFile('file'))
		 	{
	            $upload = Image_Library::CloudinaryUpload();
	            $data->image = $upload;
	            $data->save();
	        }
	        else
	        {
	        	$data->save();
	        	if (!empty($userData))
				{
		        	$userData->save();
	        	}
	        }

	        if(Input::hasFile('clinic_price'))
		 	{
		 		$uploadClinicPrice = Image_Library::CloudinaryUploadFile(Input::file('clinic_price'));
	            $data->Clinic_Price = $uploadClinicPrice;
	            $data->save();
	        }


	        //Update clinic types
	        $clinicTypeId               = Input::get('clinic_type');
	        $clinicId               	= Input::get('clinicid');
	        $clinicTypeDetail = new Admin_Clinic_Types_Detail();
			if(!empty($clinicTypeDetail->ClinicTypeByID($id)))
			{
				$clinicTypeDetail = new Admin_Clinic_Types_Detail();
				$clinicTypeDetail->UpdateClinicTypeDetails($id, $clinicTypeId);
			}
			else
			{
				$clinicTypeDetail = new Admin_Clinic_Types_Detail();
				$clinicTypeDetail->AddClinicTypeDetail($clinicTypeId, $clinicId);
			}

	        //Update insurance company details
			$insuranceID = Input::get('insurance_company');
			$insuranceIDCount = count($insuranceID);

			if($insuranceIDCount > 0)
			{
				$insuranceStatus = new Admin_Clinic_Insurance_Company();
				$insuranceStatus->UpdateInsurenceStatusInactive($id);

				foreach($insuranceID as $insuranceIDNew)
				{

					$insuranceValues = new Admin_Clinic_Insurance_Company();
					$checkValues = $insuranceValues->GetInsurenceCompanyDetails($id, $insuranceIDNew);

					//check values
					if(is_null($checkValues))
					{
						$insuranceData = new Admin_Clinic_Insurance_Company();
						$insuranceData->AddClinicInsuranceData($insuranceIDNew,$id);
					}
					else
					{
						$insuranceData = new Admin_Clinic_Insurance_Company();
						$insuranceData->UpdateInsurenceStatusActive($id,$insuranceIDNew);
					}
				}

			}
			else
			{

			}

            // redirect
			Session::flash('message', 'Successfully updated');
			return Redirect::to('admin/clinic/all-clinics');
		}
	}

	/* Use          :       Used to show clinic time set interface
     * Access       :       public
     * Return       :       view
     *
     */
	public function AddClinicTime($id)
	{
			$dataArray = array();
            $dataArray['title'] = "Add Time";
            $getSessionData = AdminHelper::AuthSession();
            if($getSessionData !=FALSE){

                $clinic = Admin_Clinic::find($id);
                $clinicData = Admin_Clinic_Time::find($id);

                $clinicTime = new Admin_Clinic_Time();
				$dataArray['clinicTime'] = $clinicTime->GetClinicTime($id);
                //show the edit form and pass the clinic id
                return View::make('admin.add-clinic-time', $dataArray)
                ->with('clinic', $clinic)
                ->with('clinicTimeData', $clinicData);
            }else{
                return Redirect::to('admin/auth/login');
            }
	}

 	public function InsertClinicTimeDetails()
    {
    	// Setup the validator
		// $rules = array('startTime' => 'required', 'endTime' => 'required');
		// $validator = Validator::make(Input::all(), $rules);

		// // Validate the input and return correct response
		// if ($validator->fails())
		// {
		//     return Response::json(array(
		//         'success' => false,
		//         'errors' => $validator->getMessageBag()->toArray()

		//     ), 400); // 400 being the HTTP code for an invalid request.
		// }
		// return Response::json_encode(array('success' => true), 200);

			$returnValues           = array();
        	$returnValues['status'] = "";
        	$returnValues['msg']    = "";

    	 if(Request::ajax()){
			$clinicTimeData = new Admin_Clinic_Time();
			$clinicTimeData->ClinicID = Input::get("clinicID");
			$clinicTimeData->StartTime = Input::get("clinicStartDate");
			$clinicTimeData->EndTime = Input::get("clinicEndDate");
			$clinicTimeData->Mon = Input::get("clinicDateMon");
			$clinicTimeData->Tue = Input::get("clinicDateTue");
			$clinicTimeData->Wed = Input::get("clinicDateWed");
			$clinicTimeData->Thu = Input::get("clinicDateThu");
			$clinicTimeData->Fri = Input::get("clinicDateFri");
			$clinicTimeData->Sat = Input::get("clinicDateSat");
			$clinicTimeData->Sun = Input::get("clinicDateSun");
			$clinicTimeData->Active = '1';

			// remove spaces between AM/PM
			$removeSpaceStartTime = Input::get("clinicStartDate");
			$clinicStartTime = str_replace(' ', '', $removeSpaceStartTime);
			$removeSpaceEndTime = Input::get("clinicEndDate");
			$clinicEndTime = str_replace(' ', '', $removeSpaceEndTime);
			// $clinicTimeData->save();
		 	$clinicTimeId = DB::table('clinic_time')->insertGetId(
	 		array(
			 		'ClinicTimeID'=>$clinicTimeData,
			 		'ClinicID'=>Input::get("clinicID"),
			 		'StartTime'=>$clinicStartTime,
			 		'EndTime'=>$clinicEndTime,
			 		'Mon'=>Input::get("clinicDateMon"),
			 		'Tue'=>Input::get("clinicDateTue"),
			 		'Wed'=>Input::get("clinicDateWed"),
			 		'Thu'=>Input::get("clinicDateThu"),
			 		'Fri'=>Input::get("clinicDateFri"),
			 		'Sat'=>Input::get("clinicDateSat"),
			 		'Sun'=>Input::get("clinicDateSun"),
			 		'Created_on'=>time(),
			 		'created_at'=>time(),
			 		'updated_at'=>'0',
			 		'Active'=>'1'
	 		));

			// $response = array(
   //          	'status' => 'success',
   //          	'msg' => 'Time set successfully',
   //          	'StartDate' => Input::get('clinicStartDate')
		 //        );
		        // return Response::json($response);
		 	$returnValues['status']        = "success";
            $returnValues['msg']           = "Time Set Successfully Completed.";
            $returnValues['clinicTimeId']  = $clinicTimeId;
            $returnValues['startTime']     = Input::get("clinicStartDate");
            $returnValues['endTime'] 	   = Input::get("clinicEndDate");
            $returnValues['monday'] 	   = Input::get("clinicDateMon");
            $returnValues['tuesday'] 	   = Input::get("clinicDateTue");
            $returnValues['wednesday'] 	   = Input::get("clinicDateWed");
            $returnValues['thursday'] 	   = Input::get("clinicDateThu");
            $returnValues['friday'] 	   = Input::get("clinicDateFri");
            $returnValues['saturday'] 	   = Input::get("clinicDateSat");
            $returnValues['sunday'] 	   = Input::get("clinicDateSun");
            $returnValues['active'] 	   = '1';

	     	echo json_encode ($returnValues);

		    }else{

	         	$returnValues['status'] = "error";
            	$returnValues['msg']    = "Time Set Unsuccessfull.";

        		echo json_encode ($returnValues);
		    }


    }

    public function EditTimeSchedule($id)
    {
    	$getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
		$dataArray = array();
		$dataArray['title'] = "Edit Time Schedule";

		$clinic = Admin_Clinic::find($id);
		$clinicTimeData = new Admin_Clinic_Time();
		$dataArray['clinicTime'] = $clinicTimeData->GetClinicTimeIdDetails($id);
		return View::make('admin.edit-clinic-time', $dataArray);
     	}else{
            return Redirect::to('admin/auth/login');
        }
    }

    public function UpdateTimeSchedule($id)
    {
    	$getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
			//Update clinic time details
        	$clinicID = Input::get('clinicid');
        	$clinictimeid = Input::get('clinictimeid');

			$startTime  = Input::get('startTime');
			$endTime   	= Input::get('endTime');
			$monday     = Input::get('monday');
			$tuesday    = Input::get('tuesday');
			$wednesday  = Input::get('wednesday');
			$thursday   = Input::get('thursday');
			$friday     = Input::get('friday');
			$saturday   = Input::get('saturday');
			$sunday     = Input::get('sunday');
			$status     = Input::get('status');
			//update table
			Admin_Clinic_Time::where('ClinicTimeID', $clinictimeid)->update(array(
	            'StartTime' =>  $startTime,
	            'EndTime'   =>  $endTime,
	            'Mon'       =>  $monday,
	            'Tue'       =>  $tuesday,
	            'Wed'       =>  $wednesday,
	            'Thu'       =>  $thursday,
	            'Fri'       =>  $friday,
	            'Sat'       =>  $saturday,
	            'Sun'       =>  $sunday,
	            'Active'    =>  $status
        	));
            // redirect
			return Redirect::to('admin/clinic/'.$clinicID.'/new-time');

     	}else{
            return Redirect::to('admin/auth/login');
        }
    }

    public function SearchBookingPage(){
        $clinic = new Admin_Clinic();
            $getAllClinics = $clinic->GetAllClinics();
            //echo '<pre>'; print_r($getAllClinics); echo '</pre>';

        $getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
            $dataArray['title'] = "Search Booking Page";
            $dataArray['cliniclist'] = $getAllClinics;
            return View::make('admin.search_booking_page', $dataArray);
        }else{
            return Redirect::to('admin/auth/login');
        }
    }
    public function SearchBooking(){
        $inputdata = Input::all();

        $getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
            $appoinment = new Admin_Appoinment();

            if($inputdata['bookingid']){
                $findAppoinments = $appoinment->BookingById($inputdata['bookingid']);
			}else{
                $starttime = strtotime($inputdata['startdate']);
                $endtime = strtotime($inputdata['enddate']);
				$created_startbooking = strtotime($inputdata['created_startbooking']);
				$created_endbooking = strtotime($inputdata['created_endbooking']);
                $findAppoinments = $appoinment->FindCustomBooking($starttime,$endtime,$created_startbooking,$created_endbooking,$inputdata['clinic'],$inputdata['doctor']);
            }

						// echo json_encode($findAppoinments);
            if($findAppoinments){
                $returnObject['myloadArray'] = $findAppoinments;
								// return $returnObject;
                $view = View::make('admin.search_results', $returnObject);
                return $view;
            }else{
                return 0;
            }
            //$dataArray['title'] = "Search Booking Page";
            //return View::make('admin.search_booking_page', $dataArray);
        }else{
            return 0;
        }
    }

		public function searchSignedUsers()
		{
			$inputdata = Input::all();
			$getSessionData = AdminHelper::AuthSession();
			if($getSessionData !=FALSE){
				$users = new Admin_User();
				$starttime = $inputdata['startdate'];
				$endtime = $inputdata['enddate'];
				if($inputdata['all_users'] == "true") {
					$findUsers = $users->findSignUsersByDate($starttime, $endtime, $inputdata['all_users']);
					// return $findUsers;
					if($findUsers) {
						$returnObject['myloadArray'] = $findUsers;
						return View::make('admin.sign_users_results', $returnObject);
					} else {
						return 0;
					}
				} else if($inputdata['startdate'] && $inputdata['enddate']) {

					$findUsers = $users->findSignUsersByDate($starttime, $endtime, $endtime, $inputdata['all_users']);
					// return $findUsers;
					if($findUsers) {
						$returnObject['myloadArray'] = $findUsers;
						return View::make('admin.sign_users_results', $returnObject);
					} else {
						return 0;
					}

				} else {
					return 0;
				}
			}
		}

    public function ClinicDoctor(){
        $inputdata = Input::all();
        $getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
            $docavailability = new Admin_Doctor_Availability();
            $findAllDoctors = $docavailability->FindAllClinicDoctors($inputdata['clinicid']);
            if($findAllDoctors){
                $returnObject['doctorlist'] = $findAllDoctors;
                $view = View::make('admin.doctor_results', $returnObject);
                return $view;
                //return $findAllDoctors;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

		// generate user sign csv
		public function downloadSignedUsers() {
			$inputdata = Input::all();
			$getSessionData = AdminHelper::AuthSession();
			if($getSessionData !=FALSE){
				$users = new Admin_User();
				$starttime = $inputdata['startdate'];
				$endtime = $inputdata['enddate'];
				if($inputdata['all_users'] == "true") {
					$findUsers = $users->findSignUsersByDate($starttime, $endtime, $inputdata['all_users']);
					// return $findUsers;
					if(!$findUsers) {
						return 0;
					}
				} else if($inputdata['startdate'] && $inputdata['enddate']) {

					$findUsers = $users->findSignUsersByDate($starttime, $endtime, $endtime, $inputdata['all_users']);
					// return $findUsers;
					if(!$findUsers) {
						return 0;
					}
				} else {
					return 0;
				}
			}

			// generate data here
			$container = array();
			foreach ($findUsers as $key => $user) {
				if($user->source_type == 0) {
		      $source_type = '';
		    } else if($user->source_type == 1) {
		      $source_type = 'Web';
		    } else if($user->source_type == 2) {
		      $source_type = 'Mobile';
		    } else if($user->source_type == 3) {
		      $source_type = 'Widget';
		    }

				if($user->Status==0){
		        $Status = "Active";
		    }elseif($user->Status==1){
		        $Status = "Processing";
		    }elseif($user->Status==2){
		        $Status = "Concluded";
		    }elseif($user->Status==3){
		        $Status = "Cancelled";
		    }
					$container[] = array(
						'UserID' => (string) $user->UserID,
						'UserType' => (string) $user->UserType,
						'ClinicID' => (string) $user->ClinicID,
						'TimeSlotDuration' => (string) $user->TimeSlotDuration,
						'Name' => (string) $user->Name,
						'Image' => (string) $user->Image,
						'NRIC' => (string) $user->NRIC,
						'FIN' => (string) $user->FIN,
						'PhoneCode' => (string) $user->PhoneCode,
						'PhoneNo' => (string) $user->PhoneNo,
						'OTPCode' => (string) $user->OTPCode,
						'OTPStatus' => (string) $user->OTPStatus,
						'Email' => (string) $user->Email,
						'Password' => (string) $user->Password,
						'DOB' => (string) $user->DOB,
						'Age' => (string) $user->Age,
						'Bmi' => (string) $user->Bmi,
						'Weight' => (string) $user->Weight,
						'Height' => (string) $user->Height,
						'Blood_Type' => (string) $user->Blood_Type,
						'Insurance_Company' => (string) $user->Insurance_Company,
						'Insurance_Policy_No' => (string) $user->Insurance_Policy_No,
						'Lat' => (string) $user->Lat,
						'Lng' => (string) $user->Lng,
						'Address' => (string) $user->Address,
						'Country' => (string) $user->Country,
						'City' => (string) $user->City,
						'State' => (string) $user->State,
						'Zip_Code' => (string) $user->Zip_Code,
						'Ref_ID' => (string) $user->Ref_ID,
						'ActiveLink' => (string) $user->ActiveLink,
						'Status' => (string) $Status,
						'ResetLink' => (string) $user->ResetLink,
						'Recon' => (string) $user->Recon,
						'Active' => (string) $user->Active,
						'source_type' => (string) $source_type,
						'Created At' => (string) $user->created_at,
						'Updated At' => (string) $user->updated_at
					);
			}

			if($inputdata['all_users'] == "true") {
				$title = 'All Signed Users (' . date('Y-m-d') . ')';
			} else {
					$title = 'All Signed Users from '.$inputdata['startdate'].' to '.$inputdata['enddate'];
			}

			$excel = Excel::create($title, function($excel) use($container) {

						$excel->sheet('Sheetname', function($sheet) use($container) {
								// $sheet->row(1, $header);
								$sheet->fromArray( $container );

						});

				})->store('csv');

				// return Request::server('HTTP_HOST').'/nuclei_mc_dev/app/storage/exports/'.$title.'.csv';
				return [
					'success' => true,
					'path'		=> '/medicloud_v2/app/storage/exports/'.$title.'.csv'
				];
		}
		// generate user csv
		public function generateUserCsv( ) {
			// return "csv generate here!";
			// $header = ["UserID","UserType","ClinicID","TimeSlotDuration","Name","Image","NRIC","FIN","PhoneCode","PhoneNo","OTPCode","OTPStatus","Email","Password","DOB","Age","Bmi","Weight","Height","Blood_Type","Insurance_Company","Insurance_Policy_No","Lat","Lng","Address","Country","City","State","Zip_Code","created_at","updated_at","Ref_ID","ActiveLink","Status","ResetLink","Recon","Active","source_type"];
			$user = new Admin_User();
			$userList = $user->UserList();
			// $column = $user->getColumns();

			$container = array();

			foreach ($userList as $key => $user) {
				if($user->source_type == 0) {
		      $source_type = '';
		    } else if($user->source_type == 1) {
		      $source_type = 'Web';
		    } else if($user->source_type == 2) {
		      $source_type = 'Mobile';
		    } else if($user->source_type == 3) {
		      $source_type = 'Widget';
		    }

				if($user->Status==0){
		        $Status = "Active";
		    }elseif($user->Status==1){
		        $Status = "Processing";
		    }elseif($user->Status==2){
		        $Status = "Concluded";
		    }elseif($user->Status==3){
		        $Status = "Cancelled";
		    }
				$container[] = array(
					'UserID' => (string) $user->UserID,
					'UserType' => (string) $user->UserType,
					'ClinicID' => (string) $user->ClinicID,
					'TimeSlotDuration' => (string) $user->TimeSlotDuration,
					'Name' => (string) $user->Name,
					'Image' => (string) $user->Image,
					'NRIC' => (string) $user->NRIC,
					'FIN' => (string) $user->FIN,
					'PhoneCode' => (string) $user->PhoneCode,
					'PhoneNo' => (string) $user->PhoneNo,
					'OTPCode' => (string) $user->OTPCode,
					'OTPStatus' => (string) $user->OTPStatus,
					'Email' => (string) $user->Email,
					'Password' => (string) $user->Password,
					'DOB' => (string) $user->DOB,
					'Age' => (string) $user->Age,
					'Bmi' => (string) $user->Bmi,
					'Weight' => (string) $user->Weight,
					'Height' => (string) $user->Height,
					'Blood_Type' => (string) $user->Blood_Type,
					'Insurance_Company' => (string) $user->Insurance_Company,
					'Insurance_Policy_No' => (string) $user->Insurance_Policy_No,
					'Lat' => (string) $user->Lat,
					'Lng' => (string) $user->Lng,
					'Address' => (string) $user->Address,
					'Country' => (string) $user->Country,
					'City' => (string) $user->City,
					'State' => (string) $user->State,
					'Zip_Code' => (string) $user->Zip_Code,
					'Ref_ID' => (string) $user->Ref_ID,
					'ActiveLink' => (string) $user->ActiveLink,
					'Status' => (string) $Status,
					'ResetLink' => (string) $user->ResetLink,
					'Recon' => (string) $user->Recon,
					'Active' => (string) $user->Active,
					'source_type' => (string) $source_type,
					'Created At' => (string) $user->created_at,
					'Updated At' => (string) $user->updated_at
				);
			}

			$title = 'All Users (' . date('Y-m-d') . ')';
			Excel::create($title, function($excel) use($container) {

            $excel->sheet('Sheetname', function($sheet) use($container) {
								// $sheet->row(1, $header);
                $sheet->fromArray( $container );

            });

        })->export('csv');
		}
		// generate booking csv report
		public function DownloadBookingCSV( ) {
			$inputdata = Input::all();
			$getSessionData = AdminHelper::AuthSession();

			if($getSessionData != FALSE){
					$appoinment = new Admin_Appoinment();

					if($inputdata['bookingid']){
							$findAppoinments = $appoinment->BookingById($inputdata['bookingid']);
					} else {
						$starttime = strtotime($inputdata['startdate']);
						$endtime = strtotime($inputdata['enddate']);
						$created_startbooking = strtotime($inputdata['created_startbooking']);
						$created_endbooking = strtotime($inputdata['created_endbooking']);
						$findAppoinments = $appoinment->FindCustomBooking($starttime,$endtime,$created_startbooking,$created_endbooking,$inputdata['clinic'],$inputdata['doctor']);
					}

					// return $findAppoinments;
					$container = array();

					foreach ($findAppoinments as $key => $user) {
						if($user->event_type==1){
				    		$booktype = 'Google';
				    }elseif($user->event_type==3){
				        $booktype = 'Widget';
				    }elseif($user->event_type==0 && $user->MediaType==0){
				        $booktype = 'Mobile';
				    }elseif($user->event_type==0 && $user->MediaType==1){
				        $booktype = 'Web';
				    }
				    if($user->Status==0){
				        $bookStatus = "Active";
				    }elseif($user->Status==1){
				        $bookStatus = "Processing";
				    }elseif($user->Status==2){
				        $bookStatus = "Concluded";
				    }elseif($user->Status==3){
				        $bookStatus = "Cancelled";
				    }

						$container[] = array(
							'BookDate' 			=> (string) date('d-m-Y', $user->BookDate),
							'BookType' 			=> (string) $booktype,
							'CLName'  			=> (string) $user->CLName,
							'ClinicID'			=> (string) $user->ClinicID,
							'ClinicTimeID'	=> (string) $user->ClinicTimeID,
							'Created_on'		=> (string) date('d-m-Y', $user->Created_on),
							'DocName'				=> (string) $user->DocName,
							'DoctorID'			=> (string) $user->DoctorID,
							'Duration'			=> (string) $user->Duration,
							'StartTime'			=> (string) date('h:i A', $user->StartTime),
							'EndTime'				=> (string) date('h:i A', $user->EndTime),
							'Price'					=> (string) $user->Price,
							'ProName'				=> (string) $user->ProName,
							'ProcedureID'		=> (string) $user->ProcedureID,
							'Remarks'				=> (string) $user->Remarks,
							'Status'				=> (string) $user->Status,
							'USEmail'				=> (string) $user->USEmail,
							'USNRIC'				=> (string) $user->USNRIC,
							'USPhone'				=> (string) $user->USPhone,
							'UserAppoinmentID' => (string) $user->UserAppoinmentID,
							'UserID'				=> (string) $user->UserID,
							'UsrName'				=> (string) $user->UsrName,
							'Book Status'		=> $bookStatus
						);
				}
				$title = 'All Booking Users (' . date('Y-m-d') . ')';
			 	$excel = Excel::create($title, function($excel) use($container) {

	            $excel->sheet('Sheetname', function($sheet) use($container) {
									// $sheet->row(1, $header);
	                $sheet->fromArray( $container );

	            });

	        })->store('csv');

					// return Request::server('HTTP_HOST').'/nuclei_mc_dev/app/storage/exports/'.$title.'.csv';
					return [
						'success' => true,
						'path'		=> '/medicloud_v2/app/storage/exports/'.$title.'.csv'
					];

			}
		}

		public function DownloadBookingCSVData( $csv_name ) {

		}

	public function getTransactionHistoryView( )
	{
		$data['title'] = 'Transaction History';
		return View::make('admin.transaction_history', $data);
	}

	public function viewTransactionByDate( )
	{
		$input = Input::all();
		$transaction = new Transaction( );
		$data['result'] = $transaction->paymentAdminTransactionHistory($input['start'], $input['end'], $input['search']);
		return View::make('admin.payment-views', $data);
	}

	public function viewTransaction( )
	{
		$input = Input::all();
		$transaction = new Transaction( );
		$data['result'] = $transaction->paymentAdminViewTransactionHistory($input['start'], $input['end'], $input['filter'], $input['clinicID']);
		$data['filter'] = $input['filter'];
		return View::make('admin.invoice', $data);
	}

	public function paymentDownloadTransactionHistory($start, $end)
	{
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$container = array();
		$data = $transaction->paymentAdminTransactionHistory($start, $end, null);
		$title = 'Payment Transaction History ('.$start.' - '.$end.')';

		foreach ($data as $key => $value) {
			if((int)$value->credit_cost == 0){
				$transaction_fees = 0;
				$pay_to_clinic = 0;
			} else if((int)$value->credit_cost > 0) {
				$transaction_fees = (int)$value->procedure_cost * $value->medi_percent;
				$pay_to_clinic = (int)$value->procedure_cost - $transaction_fees;
			}

			if($value->paid_medi == 1) {
                  if((int)$value->credit_cost > 0) {
                        $status = 'Paid';
                  } else {
                        $status = '';
                  }
            } else {
                  if((int)$value->credit_cost > 0) {
                        $status = 'Not Paid';
                  } else {
                        $status = '';
                  }
            }
			$collected_amount = (int)$value->procedure_cost - (int)$value->credit_cost;
			$container[] = array(
				'Clinic'						=> ucwords($value->clinic_name),
				'PaymentDate'					=> date('M', strtotime($value->updated_at)).' '.date('d', strtotime($value->updated_at)).' '.date('Y', strtotime($value->updated_at)),
				'Customer'						=> ucwords($value->Name),
				'Staff'							=> ucwords($value->doctor_name),
				'Service/Class'					=> ucwords($value->clinic_procedure_name),
				'Initial Booking Date'			=> date('M', $value->Created_on).' '.date('d', $value->Created_on).' '.date('Y', $value->Created_on),
				'Appt/Class Date'				=> date('M', $value->BookDate).' '.date('d', $value->BookDate).' '.date('Y', $value->BookDate),
				'Total Amount'					=> "$".$value->procedure_cost,
				'Collected Amount'				=> "$".$collected_amount,
				'Medi-Credit'					=> $value->credit_cost,
				'Medicloud Transaction Fees'	=> $transaction_fees,
				'Payment to Clinic'				=> $pay_to_clinic,
				'Paid By Medicloud Status'      => $status
			);
		}

		$excel = Excel::create($title, function($excel) use($container) {
				$excel->sheet('Sheetname', function($sheet) use($container) {
						$sheet->fromArray( $container );

				});
		})->export('xls');
	}

	public function paymentSearchDownloadTransactionHistory($search)
	{	
		// return "search download";
		$clinic = StringHelper::getAuthSession();
		$transaction = new Transaction( );
		$container = array();
		$data = $transaction->paymentAdminTransactionHistory(null, null, $search);
		$title = 'Payment Transaction History - ( Search Term - '.$search.' )';

		foreach ($data as $key => $value) {
			if((int)$value->credit_cost == 0){
				$transaction_fees = 0;
				$pay_to_clinic = 0;
			} else if((int)$value->credit_cost > 0) {
				$transaction_fees = (int)$value->procedure_cost * $value->medi_percent;
				$pay_to_clinic = (int)$value->procedure_cost - $transaction_fees;
			}
			if($value->paid_medi == 1) {
                  if((int)$value->credit_cost > 0) {
                        $status = 'Paid';
                  } else {
                        $status = '';
                  }
            } else {
                  if((int)$value->credit_cost > 0) {
                        $status = 'Not Paid';
                  } else {
                        $status = '';
                  }
            }
			$collected_amount = (int)$value->procedure_cost - (int)$value->credit_cost;
			$container[] = array(
				'Clinic'						=> ucwords($value->clinic_name),
				'PaymentDate'					=> date('M', strtotime($value->updated_at)).' '.date('d', strtotime($value->updated_at)).' '.date('Y', strtotime($value->updated_at)),
				'Customer'						=> ucwords($value->Name),
				'Staff'							=> ucwords($value->doctor_name),
				'Service/Class'					=> ucwords($value->clinic_procedure_name),
				'Initial Booking Date'			=> date('M', $value->Created_on).' '.date('d', $value->Created_on).' '.date('Y', $value->Created_on),
				'Appt/Class Date'				=> date('M', $value->BookDate).' '.date('d', $value->BookDate).' '.date('Y', $value->BookDate),
				'Total Amount'					=> "$".$value->procedure_cost,
				'Collected Amount'				=> "$".$collected_amount,
				'Medi-Credit'					=> $value->credit_cost,
				'Medicloud Transaction Fees'	=> $transaction_fees,
				'Payment to Clinic'				=> $pay_to_clinic,
				'Paid By Medicloud Status'      => $status
			);
		}

		$excel = Excel::create($title, function($excel) use($container) {
				$excel->sheet('Sheetname', function($sheet) use($container) {
						$sheet->fromArray( $container );

				});
		})->export('xls');
	}

	public function getCreditPaymentsView( )
	{
		$data['title'] = 'Credit Payments';
		return View::make('admin.credit_payments', $data);
	}
	
	public function searchClinic( )
	{
		$clinic = new Clinic( );
		$input = Input::all();
		return $clinic->search($input['search']);
	}

	public function downloadInvoice($start, $end, $filter, $id)
	{
		$transaction = new Transaction( );
		$data = $transaction->paymentAdminViewTransactionHistory($start, $end, $filter, $id);
		if($filter == 0) {
			$filterName = "All View";
		} else if($filter == 1) {
			$filterName = "View By Payment to Clinic";
		} else if($filter == 2) {
			$filterName = "View By Payment to MediCloud";
		}
		$title = 'Invoice History ('.$start.' - '.$end.')('.$filterName.')';
		$total_medi = 0;
      	$total_clinic = 0;

		foreach ($data as $key => $value) {
			if((int)$value->credit_cost > 0) {
                  $transaction_fee = (int)$value->procedure_cost * $value->medi_percent;
                  // $transaction_fee = (int)$value->procedure_cost - $tf;
                  // $sum = (int)$value->credit_cost - $transaction_fee;
                  // if($sum < 0) {
                  //       $medi = $sum;
                  //       $clinic = 0;
                  //       $total_medi = $total_medi + $medi;
                  // } else {
                  //       $medi = 0;
                  //       $clinic = $sum;
                  //       $total_clinic = $total_clinic + $clinic;
                  // }
                  $clinic = (int)$value->credit_cost - $transaction_fee;
                  $total_clinic = $total_clinic + $clinic;
                  $total_revenue = (int)$value->procedure_cost - $transaction_fee;

            } else {
                  $medi = 0;
                  $clinic = 0;
                  $transaction_fee = 0;
                  $total_revenue = $value->procedure_cost;
            }

            if($value->paid_medi != 0) {
                  $status = 'Paid';
            } else {
                  $status = 'Not Paid';
            }
            $collected_amount = (int)$value->procedure_cost - (int)$value->credit_cost;
			$container[] = array(
				'Clinic'						=> ucwords($value->clinic_name),
				'PaymentDate'					=> date('M', strtotime($value->updated_at)).' '.date('d', strtotime($value->updated_at)).' '.date('Y', strtotime($value->updated_at)),
				'Customer'						=> ucwords($value->Name),
				'Staff'							=> ucwords($value->doctor_name),
				'Service/Class'					=> ucwords($value->clinic_procedure_name),
				'Initial Booking Date'			=> date('M', $value->Created_on).' '.date('d', $value->Created_on).' '.date('Y', $value->Created_on),
				'Appt/Class Date'				=> date('M', $value->BookDate).' '.date('d', $value->BookDate).' '.date('Y', $value->BookDate),
				'Total Bill'					=> "$".$value->procedure_cost,
				'Collected'						=> "$".$collected_amount,
				'Medi-Credit Deducted'			=> $value->credit_cost,
				'Transaction Fee'				=> $transaction_fee,
				'Payment to Clinic'				=> $clinic,
				'Total Revenue (Clinic)'		=> $total_revenue,
				'Paid Status'      				=> $status
			);

			$medi = 0;
            $clinic = 0;
            $transaction_fee = 0;
            $total_revenue = 0;

		}
		if($filter == 0) {
			$container_payment =  array(
				array(
					'Total Payment to Clinic',
					$total_clinic
				),
				array(
					'',
					''
				)
			);
			
		} else if($filter == 1) {
			$container_payment = array(
				array(
					'Total Payment to Clinic',
					$total_clinic
				),
				array(
					'',
					''
				)
			);
		} else if($filter == 2) {
			$container_payment = array(
				array(
					'Total Payment to Medicloud',
					$total_clinic
				),
				array(
					'',
					''
				)
			);
		}

		$excel = Excel::create($title, function($excel) use($container, $container_payment) {
				$excel->sheet('Sheetname', function($sheet) use($container, $container_payment) {
						$sheet->fromArray( $container );
						$sheet->rows( $container_payment );

				});
		})->export('xls');
	}

	public function updateToPaid( )
	{
		$input = Input::all();
		$transaction = new Transaction( );
		$array = array();
		foreach ($input['id'] as $key => $value) {
			array_push($array, $transaction->updateToPaid($value));
		}

		return $array;
	}
}

