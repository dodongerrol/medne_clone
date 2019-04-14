<?php

class Admin_DoctorController extends \BaseController {


	/* Use          :       Used to add new doctor
     * Access       :       public
     * Return       :       
     * 
     */
    public function AddNewDoctor()
    {
        $getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){
            $dataArray = array();
            $dataArray['title'] = "Add a new doctor";
            $clinicData = new Admin_Clinic();
            $dataArray['allClinics'] = $clinicData->GetClinicList();
            return View::make('admin.add-doctor', $dataArray);
            }else{
                return Redirect::to('admin/auth/login');
            }
	}

	/* Use          :       Used to insert new doctor
     * Access       :       public
     * Return       :       none
     * 
     */
	public function InsertDoctor()
	{
		$validate = Validator::make(Input::all(), 

			array(
					'name'=>'required',					
					'email' => 'required|email|unique:doctor'					
				),
			array(
					'name.required'=>'Name is required'					
				)
			);

		if ($validate->fails()) {
			Input::flash();
			return Redirect::to('admin/clinic/new-doctor')->withErrors($validate);
		}
		else
		{

			//insert doctor details			
			$doctorData = new Admin_Doctor();
			$doctorId = $doctorData->AddDoctor();

			// Insert user details			
			$userData = new Admin_User();
			
		  	$userData->Ref_ID = $doctorId;
			$userId = $userData->AddUserTypeDoctor();			

				if($userId)
				{
                    //Send email when add new doctor
                    $doctorDetails = Admin_User::find($userId);
                    $emailDdata['emailName']= $doctorDetails->Name;
                    $emailDdata['emailPage']= 'email-templates.new-doctor';
                    $emailDdata['emailTo']= $doctorDetails->Email;
                    $emailDdata['emailSubject'] = 'Please complete your regitration';
                    $emailDdata['activeLink'] = "<a href='".URL::to('app/auth/register?activate='.$doctorDetails->ActiveLink)."'> Create Password Link</a>";
                    EmailHelper::sendEmail($emailDdata);
                }		
			

			//Insert insurance company details						
			$clinicId      = Input::get('clinic_name');
			if (!empty($clinicId))
			{
				$doctorAvailabilityData = new Admin_Doctor_Availability();
				$doctorAvailabilityData->AddDoctorAvailability($doctorId, $clinicId);									
			}
		}		
					
			return Redirect::to('admin/clinic/all-doctors');
	}

	/* Use          :       Used to show all doctors details
     * Access       :       public
     * Return       :       view
     * 
     */
	public function ShowAllDoctors()
	{
		$getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){	
			$dataArray = array();
	        $dataArray['title'] = "All doctors"; 
	        $doctor = new Admin_Doctor(); 
		 	$dataArray['resultSet'] = $doctor->GetDoctorDetails();	
			return View::make('admin.manage-doctors', $dataArray);
		}else{
                return Redirect::to('admin/auth/login');
            }
	}

	/* Use          :       Used to edit doctor details
     * Access       :       public
     * Return       :       view
     * 
     */
	public function EditDoctor($id)
	{
		$getSessionData = AdminHelper::AuthSession();
        if($getSessionData !=FALSE){	
			$dataArray = array();
			$dataArray['title'] = "Edit doctor";		
			// get the doctor details by id
			$doctor = Admin_Doctor::find($id);		
			return View::make('admin.edit-doctor', $dataArray)
			->with('doctor', $doctor);
		}else{
                return Redirect::to('admin/auth/login');
            }		
	}

	/* Use          :       Used to update doctor details
     * Access       :       public
     * Return       :       view
     * 
     */
	public function UpdateDoctor($id)
	{	
		$validate = Validator::make(Input::all(), 

			array(
				'name'=>'required',				
				'email' => 'email|unique:user,Email,'.$id.',Ref_ID'
				
				),
			array(
				'name.required'=>'Name is required'				
				)
			);

		if ($validate->fails())
		{
			Input::flash();
			return Redirect::to('admin/clinic/doctor/'.$id.'/edit')
			       ->withErrors($validate)
			       // ->withInput(Input::except('file'))
		       	   ->withInput(Input::except('email'))
		       	   ->withInput(Input::except('password'));
		}
		else
		{
			//Update doctor details
			$data 			        = Admin_Doctor::find($id);
			$data->Name             = Input::get('name');
			$data->Qualifications   = Input::get('qualifications');
			$data->Specialty        = Input::get('specialty');
			$data->Emergency        = Input::get('emergency');            
			$data->Phone            = Input::get('phone');            		
			$data->Email            = Input::get('email');			
			$data->Active           = Input::get('status'); 


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
	        }

	        
	        // Update user details
			$userData             	= Admin_User::find($id);
			if (!empty($userData))
			{
				$userData->Email    = Input::get('email');			
				$userData->Password = StringHelper::encode(Input::get ('password'));
				$userData->save();
			}			
            	        	
        }
            // redirect
			Session::flash('message', 'Successfully updated');
			return Redirect::to('admin/clinic/all-doctors');
		 
	}

}