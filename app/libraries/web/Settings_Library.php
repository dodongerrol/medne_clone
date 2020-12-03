<?php


class Settings_Library {


	public static function saveServices($clinicID)
	{
		$input = Input::all();
		$id = $input['id'];
		if ($id=="null") { ///new insert
			$dataArray = array(
				'clinicid'=> $clinicID,
				'name'=> $input['name'],
				'description'=> $input['description'],
				'duration'=> $input['duration'],
				'durationformat'=> "mins",
				'price'=> $input['cost']
				);

				$proc = new ClinicProcedures();
				$addservice = $proc->AddProcedures ($dataArray);
                return $addservice;
		}else {//update
			$dataArray = array(
				'clinicID'=> $clinicID,
				'Name'=> $input['name'],
				'Description'=> $input['description'],
				'Duration'=> $input['duration'],
				'procedureid'=> $id,
				'Price'=> $input['cost']
				);

			$proc = new ClinicProcedures();
			$proc->UpdateProcedure ($dataArray);
		}

	}

// ```````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````/
	public static function deleteServices($clinicID)
	{
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$input = Input::all();
		$id = $input['id'];

        $FindAppointment = General_Library::FindProcedureBooking($id);

        // dd($FindAppointment);

        if (!($FindAppointment)){

            $dataArray = array(
                'procedureid'=> $id,
                'active'=> 0
                );

            $proc = new ClinicProcedures();
            $proc->UpdateProcedure ($dataArray);

            $data['services'] = Clinic_Library::FindClinicProcedures($clinicID);
            return View::make('settings.services.list',$data);
        }
        else {

            return 0;
        }

	}


	####################################################################
	#                      staff                                       #
	####################################################################

 	public static function getClinicStaff($clinicID)
	{
		$staff = new Staff();
		return $staff->getStaffList($clinicID);

	}
	// ```````````````````````````````````````````````````````````````````````````````````

	public static function getStaffDetails($staff_id)
	{
		$staff = new Staff();
		return $staff->getStaffDetails($staff_id);

	}

// ``````````````````````````````````````````````````````````````````````````````````````

	public static function addStaff($clinicid)
	{
		$input = Input::all();

		$dataArray = array(
			'name' => $input['name'],
			'email' => $input['email'],
			'clinicid' => $clinicid,
			);

		$staff = new Staff();
		return $staff->insertstaff($dataArray);
	}

// ```````````````````````````````````````````````````````````````````````````````````````````````````````````````
	public static function addDoctor($clinicid)
	{
		$input = Input::all();

		$dataArray = array(
			'name' => $input['name'],
			'email' => $input['email'],
			);

		$doctor = new Doctor();
		$addDoctor =  $doctor->addDoctor($dataArray);

		//Add to Availability
        $arrayAvail['doctorid'] = $addDoctor;
        $arrayAvail['clinicid'] = $clinicid;
        Clinic_Library::AddDoctorAvailability($arrayAvail);

        $activelink = StringHelper::getEncryptValue();
        $dataUser['name'] = $input['name'];
        $dataUser['usertype'] = 2;
        $dataUser['email'] = $input['email'];
        $dataUser['ref_id'] = 71;//$addDoctor;
        $dataUser['activelink'] = $activelink;
        $dataUser['status'] = 0;

        $user = new User();
        $user->addUser($dataUser);
        return $addDoctor;

	}
// ````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````

	public static function updateDoctor()
	{
		$input = Input::all();
		$status = $input['status'];

		if ($status=='details') { //update main details
            $data['doctorid'] = $input['doctor_id'];
			$data['Name'] = $input['name'];
			$data['Email'] = $input['email'];
			$data['cc_email'] = $input['cc_email'];
            $data['phone_code'] = $input['code'];
			$data['Phone'] = $input['mobile'];
            $data['Qualifications'] = $input['qualification'];
            $data['Specialty'] = $input['specialty'];
			$data['image'] = $input['image'];
		}

		if ($status=='pin') { //update pin
			$data['doctorid'] = $input['doctor_id'];
			$data['pin'] = $input['pin'];
		}

		if ($status=='new-pin') { //update pin
			$data['doctorid'] = $input['doctor_id'];
			$data['pin'] = $input['pin'];
		}


		$doc = new Doctor();
		$doc->updateDoctor($data);
	}

// ````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````

	public static function updateStaff()
	{
		$input = Input::all();
		$status = $input['status'];

		if ($status=='details') { //update main details
			$data['staff_id'] = $input['staff_id'];
			$data['email'] = $input['email'];
			$data['cc_email'] = $input['cc_email'];
            $data['phone'] = $input['mobile'];
			$data['phone_code'] = $input['code'];
			$data['qualifcation'] = $input['qualification'];
		}

		if ($status=='pin') { //update pin
			$data['staff_id'] = $input['staff_id'];
			$data['pin_no'] = $input['pin'];
		}

		if ($status=='new-pin') { //update pin
			$data['staff_id'] = $input['staff_id'];
			$data['pin_no'] = $input['pin'];
		}


		$doc = new Staff();
		$doc->updateStaff($data);
	}

	public static function UpdateDoctorService($clinicdata){

        $allInputs = Input::all();

        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){

                $procedureChanges = FALSE;

                if($allInputs['checked']==1) { 		// checkbox is checked

                        $findSingleProcedure = Doctor_Library::FindSingleProcedures($findClinicDetails->ClinicID,$allInputs['doctorid'],$allInputs['procedure']);
                        if($findSingleProcedure){
                            $activeProcedure['Active'] = 1;
                            $activeProcedure['updated_at'] = time();
                            $procedureChanges = Doctor_Library::UpdateProcedure($activeProcedure,$findSingleProcedure->DoctorProcedureID);
                        }else{
                            $dataProcedure['clinicid'] = $findClinicDetails->ClinicID;
                            $dataProcedure['doctorid'] = $allInputs['doctorid'];
                            $dataProcedure['procedureid'] = $allInputs['procedure'];
                            $procedureChanges = Doctor_Library::AddDoctorProcedures($dataProcedure);
                        }

                }else {

                	$findSingleProcedure = Doctor_Library::FindSingleProcedures($findClinicDetails->ClinicID,$allInputs['doctorid'],$allInputs['procedure']);

                        if($findSingleProcedure){
                            $activeProcedure['Active'] = 0;
                            $activeProcedure['updated_at'] = time();
                            $procedureChanges = Doctor_Library::UpdateProcedure($activeProcedure,$findSingleProcedure->DoctorProcedureID);
                        }

                }

            if($procedureChanges){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


    public static function UpdateDoctorAllService($clinicdata){

        $allInputs = Input::all();

        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){

                $procedureChanges = FALSE;

                if($allInputs['checked']==1) { 		// checkbox is checked

                    $existingProcedureList = Clinic_Library::FindClinicProcedures($clinicdata->Ref_ID);
	                if($existingProcedureList){
	                    foreach($existingProcedureList as $procedureList){

	                        $findSingleProcedure = Doctor_Library::FindSingleProcedures($findClinicDetails->ClinicID,$allInputs['doctorid'],$procedureList->ProcedureID);
	                        if($findSingleProcedure){
	                            $activeProcedure['Active'] = 1;
	                            $activeProcedure['updated_at'] = time();
	                            $procedureChanges = Doctor_Library::UpdateProcedure($activeProcedure,$findSingleProcedure->DoctorProcedureID);
	                        }else{
	                            $dataProcedure['clinicid'] = $findClinicDetails->ClinicID;
	                            $dataProcedure['doctorid'] = $allInputs['doctorid'];
	                            $dataProcedure['procedureid'] = $procedureList->ProcedureID;
	                            $procedureChanges = Doctor_Library::AddDoctorProcedures($dataProcedure);
	                        }
	                    }
	                }

                }else {

                	$existingProcedureList = Doctor_Library::FindDoctorProcedures($findClinicDetails->ClinicID,$allInputs['doctorid']);
	                if($existingProcedureList){
	                    foreach($existingProcedureList as $procedureList){
	                        $arrayProcedure['Active'] = 0;
	                        $arrayProcedure['updated_at'] = time();
	                        Doctor_Library::UpdateProcedure($arrayProcedure,$procedureList->DoctorProcedureID);
	                    }
	                }

                }

            if($procedureChanges){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

// ``````````````````````````````add working hours in tab load````````````````````````````````````````````

    public static function addDoctorManageTimeSlots()
    {
    	$input = Input::all();
    	$doctor_id = $input['doctor_id'];

    	$manageTime = new ManageTimes();
    	$manageTimes = $manageTime->findDoctorManageTime($doctor_id);

    	// add manage time
    	if ($manageTimes) {
    		$manage_time_id = $manageTimes[0]->ManageTimeID;
    	} else {

    		$data['party'] = 2;
    		$data['partyid'] = $doctor_id;
    		$data['timerepeat'] = 1;
    		$data['timetype'] = 1;
    		$data['from_date'] = strtotime(date('d-m-Y'));
    		$data['to_date'] = 0;

    		$manage_time_id = $manageTime->AddManageTimes($data);

    	}

    	// get clinictime

    	$clinicTime = new ClinicTimes();
    	$clinicTimes = $clinicTime->FindClinicActivetimesNew($manage_time_id);
// dd(count($clinicTimes));
    	if (!$clinicTimes) {

    		// $clinicTime = new ClinicTimes();
    		// $clinicTimes = $clinicTime->deleteClinicActivetimes($manage_time_id);


    		$data['managetimeid'] = $manage_time_id;
    		$data['starttime'] = '08:00 AM';
    		$data['endtime'] = '05.30 PM';
    		$data['wemon'] = 1;
    		$data['wetus'] = 0;
    		$data['wewed'] = 0;
    		$data['wethu'] = 0;
    		$data['wefri'] = 0;
    		$data['wesat'] = 0;
    		$data['wesun'] = 0;
    		$clinicTime = new ClinicTimes();
    		$clinicTime->AddClinicTimes ($data);

    		$data1['managetimeid'] = $manage_time_id;
    		$data1['starttime'] = '08:00 AM';
    		$data1['endtime'] = '05.30 PM';
    		$data1['wemon'] = 0;
    		$data1['wetus'] = 1;
    		$data1['wewed'] = 0;
    		$data1['wethu'] = 0;
    		$data1['wefri'] = 0;
    		$data1['wesat'] = 0;
    		$data1['wesun'] = 0;
    		$clinicTime = new ClinicTimes();
    		$clinicTime->AddClinicTimes ($data1);

    		$data2['managetimeid'] = $manage_time_id;
    		$data2['starttime'] = '08:00 AM';
    		$data2['endtime'] = '05.30 PM';
    		$data2['wemon'] = 0;
    		$data2['wetus'] = 0;
    		$data2['wewed'] = 1;
    		$data2['wethu'] = 0;
    		$data2['wefri'] = 0;
    		$data2['wesat'] = 0;
    		$data2['wesun'] = 0;
    		$clinicTime = new ClinicTimes();
    		$clinicTime->AddClinicTimes ($data2);

    		$data3['managetimeid'] = $manage_time_id;
    		$data3['starttime'] = '08:00 AM';
    		$data3['endtime'] = '05.30 PM';
    		$data3['wemon'] = 0;
    		$data3['wetus'] = 0;
    		$data3['wewed'] = 0;
    		$data3['wethu'] = 1;
    		$data3['wefri'] = 0;
    		$data3['wesat'] = 0;
    		$data3['wesun'] = 0;
    		$clinicTime = new ClinicTimes();
    		$clinicTime->AddClinicTimes ($data3);

    		$data4['managetimeid'] = $manage_time_id;
    		$data4['starttime'] = '08:00 AM';
    		$data4['endtime'] = '05.30 PM';
    		$data4['wemon'] = 0;
    		$data4['wetus'] = 0;
    		$data4['wewed'] = 0;
    		$data4['wethu'] = 0;
    		$data4['wefri'] = 1;
    		$data4['wesat'] = 0;
    		$data4['wesun'] = 0;
    		$clinicTime = new ClinicTimes();
    		$clinicTime->AddClinicTimes ($data4);

    		$data5['managetimeid'] = $manage_time_id;
    		$data5['starttime'] = '08:00 AM';
    		$data5['endtime'] = '05.30 PM';
    		$data5['wemon'] = 0;
    		$data5['wetus'] = 0;
    		$data5['wewed'] = 0;
    		$data5['wethu'] = 0;
    		$data5['wefri'] = 0;
    		$data5['wesat'] = 1;
    		$data5['wesun'] = 0;
    		$clinicTime = new ClinicTimes();
    		$clinicTime->AddClinicTimes ($data5);

    		$data6['managetimeid'] = $manage_time_id;
    		$data6['starttime'] = '08:00 AM';
    		$data6['endtime'] = '05.30 PM';
    		$data6['wemon'] = 0;
    		$data6['wetus'] = 0;
    		$data6['wewed'] = 0;
    		$data6['wethu'] = 0;
    		$data6['wefri'] = 0;
    		$data6['wesat'] = 0;
    		$data6['wesun'] = 1;
    		$clinicTime = new ClinicTimes();
    		$clinicTime->AddClinicTimes ($data6);


    	}


    }





// ``````````````````````````````````````````````````````````````````````````````````````````````````````
    public static function UpdateWorkingHours($ClinicID)
    {
    	$input = Input::all();

    	$time_to = $input['time_to'];
    	$time_from = $input['time_from'];
    	$day_name = $input['day_name'];
    	$doctor_id = $input['doctor_id'];
    	$day_name = substr($day_name, 0, 3);

    	$manageTime = new ManageTimes();
    	$manageTimes = $manageTime->findDoctorManageTime($doctor_id);

    	$manage_time_id = $manageTimes[0]->ManageTimeID;
    	$clinicTime = new ClinicTimes();
    	$clinicTimes = $clinicTime->FindClinicActivetimesByDay($manage_time_id,$day_name);

        if ($clinicTimes){
            $dataArray = array('clinictimeid' => $clinicTimes[0]->ClinicTimeID, 'StartTime'=>$time_from, 'Endtime'=>$time_to);
            $clinicTime->UpdateClinicTimes($dataArray);
        }




    }


// ````````````````````````````````````````````````````````````````````````````````````````````````````

    public static function UpdateWorkingHoursStatus()
    {
    	$input = Input::all();

    	$clinicTime = new ClinicTimes();

    	$dataArray = array('clinictimeid' => $input['time_id'], 'Active'=>$input['status']);

    	return $clinicTime->UpdateClinicTimes($dataArray);
    }



// ````````````````````````````````````````````add break`````````````````````````````````````````````````

    public static function addBreak()
    {
    	$input = Input::all();

    	$datArray = array(
    		'id' => $input['guid'],
    		'doctor_id' => $input['doctorid'],
            'clinic_id' => null,
    		'type' => 3,
    		'day' => $input['day'],
    		'start_time' => $input['time_from'],
    		'end_time' => $input['time_to'],
    		);

    	$event = new ExtraEvents();
    	$event->insertBreak($datArray);
    }

    public static function updateBreak()
    {
    	$input = Input::all();

    	$datArray = array(
    		'id' => $input['id'],
    		'start_time' => $input['time_from'],
    		'end_time' => $input['time_to'],
    		);

    	$event = new ExtraEvents();
    	$event->updateBreak($datArray);
    }


    public static function getDoctorBreaks()
    {
    	$input = Input::all();

    	$datArray = array(
    		'doctor_id' => $input['doctorid'],
    		'type' => 3,
    		);

    	$event = new ExtraEvents();
    	return $event->getDoctorBreaks($datArray);
    }

    public static function removeBreak()
    {
    	$input = Input::all();
    	$event = new ExtraEvents();
    	$event->removeExtraEvents($input['id']);
    }

    public static function AddDoctorTimeOff($clinicdata){

        $allInputs = Input::all();

        if(!empty($allInputs)){

            $dataArray['party'] = 2; // doctor Holiday
            $dataArray['partyid'] = $allInputs['doctorid'];
            $dataArray['title'] = null;
            $dataArray['holidaytype'] = $allInputs['holidayType'];
            $fromholiday = date ("d-m-Y", strtotime($allInputs['dateStart']));
            $dataArray['fromholiday'] = $fromholiday;
            $toholiday = date ("d-m-Y", strtotime($allInputs['dayEnd']));
            $dataArray['toholiday'] = $toholiday;
            $dataArray['fromtime'] = $allInputs['timeStart'];
            $dataArray['totime'] = $allInputs['timeEnd'];
            $dataArray['note'] = $allInputs['note'];

            $addNewHolidays = General_Library::AddManageHolidays($dataArray);

            if($addNewHolidays){

                return 1;

            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    public static function GetDoctorTimeOff(){

        $allInputs = Input::all();
        $Holiday_id = $allInputs['Holiday_id'];

        $Holiday = new ManageHolidays();
        $data = $Holiday->FindSelectedHoliday($Holiday_id);

		$arr['Holiday_id'] = $data->ManageHolidayID;
        $arr['Start_date'] = date('d M Y', strtotime($data->From_Holiday));
		$arr['End_date'] = date('d M Y', strtotime($data->To_Holiday));
		$arr['Start_Time'] = $data->From_Time;
        $arr['End_Time'] = $data->To_Time;
		$arr['Type'] = $data->Type;
		$arr['Note'] = $data->Note;

		return $arr;

    }

    public static function UpdateDoctorTimeOff($clinicdata){

        $allInputs = Input::all();
        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){

        	$updateArray['manageholidayid'] = $allInputs['holidayid'];
            $updateArray['Type'] = $allInputs['holidayType'];
            $fromholiday = date ("d-m-Y", strtotime($allInputs['dateStart']));
            $updateArray['From_Holiday'] = $fromholiday;
            $toholiday = date ("d-m-Y", strtotime($allInputs['dayEnd']));
            $updateArray['To_Holiday'] = $toholiday;
            $updateArray['From_Time'] = $allInputs['timeStart'];
            $updateArray['To_Time'] = $allInputs['timeEnd'];
            $updateArray['Note'] = $allInputs['note'];
            $updateArray['Active'] = 1;
            $updateArray['updated_at'] = time();

            $updateClinicHolidays = General_Library::UpdateManageHolidays($updateArray);

            if($updateClinicHolidays){

            	return 1;

            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


    public static function DeleteDoctorTimeOff($clinicdata){

        $allInputs = Input::all();
        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){

        	$updateArray['manageholidayid'] = $allInputs['holidayid'];
            $updateArray['Active'] = 0;
            $updateArray['updated_at'] = time();

            $updateClinicHolidays = General_Library::UpdateManageHolidays($updateArray);

            if($updateClinicHolidays){

            	return 1;

            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    public static function UpdateDetailToggal(){

		$input = Input::all();
		$status = $input['status'];
		$value = $input['value']; // current toggal status

		if ($value == 0){
			$new_value = 1;
		}else {
			$new_value = 0;
		}

		if ($status == 1) { // update check_login

			$data['doctorid'] = $input['doctorid'];
			$data['check_login'] = $new_value;
		}

		if ($status == 2) { //update check_pin

			$data['doctorid'] = $input['doctorid'];
			$data['check_pin'] = $new_value;
		}

		if ($status == 3) { //update check_sync

			$data['doctorid'] = $input['doctorid'];
			$data['check_sync'] = $new_value;
		}


		$doc = new Doctor();
		$doc->updateDoctor($data);
	}


	public static function UpdateStaffToggal(){

		$input = Input::all();
		$value = $input['value']; // current toggal status

		if ($value == 0){
			$new_value = 1;
		}else {
			$new_value = 0;
		}

		$data['staff_id'] = $input['staffid'];
		$data['check_login'] = $new_value;

		$staff = new Staff();
		$staff->updateStaff($data);
	}


	public static function DeleteDoctorDetail(){

		$input = Input::all();

        $doctor_id = $input['doctorid'];

        $FindDoctorAppointment = General_Library::FindDoctorBooking($doctor_id);

        // dd($FindDoctorAppointment);

		if (!($FindDoctorAppointment)){ // Check whether doctor have appointments

            $data['doctorid'] = $doctor_id;
            $data['Active'] = 0;

            $doc = new Doctor();
            $updateDoctor = $doc->updateDoctor($data);
            return 1;
        }
        else {

            return 0;
        }
	}


	public static function DeleteStaffDetail(){

		$input = Input::all();

		$data['staff_id'] = $input['staffid'];
		$data['active'] = 0;

		$staff = new Staff();
		$staff->updateStaff($data);
	}


    public static function updateClinicDetails($clinicid){

        $input = Input::all();

        if($input['status'] == 1){ // update Calendar type

            $data['Calendar_type'] = $input['cal_type'];

        }elseif ($input['status'] == 2) { // update Calendar day

            $data['Calendar_day'] = $input['cal_day'];

        }elseif ($input['status'] == 3) { // update Calendar slot duration

            $data['Calendar_duration'] = $input['cal_duration'];

        }elseif ($input['status'] == 4) { // update Calendar starting hour

            $data['Calendar_Start_Hour'] = $input['start_hour'];
        }
        elseif ($input['status'] == 5) { // update clinic pin status

            $value = $input['pin_val']; // current toggal status

            if ($value == 0){
                $new_value = 1;
            }else {
                $new_value = 0;
            }

            $data['Require_pin'] = $new_value;
        }

        $data['clinicid'] = $clinicid;
        $data['active'] = 1;

        $clinic = new Clinic();
        $clinic->UpdateClinicHomeDetails($data);
    }


    public static function UpdateClinicProfileDetails($clinicdata){

        $inputdata = Input::all();

        // $mobile = $inputdata['code'] . $inputdata['Phone'];
        $mobile = $inputdata['Phone'];

        if(!empty($inputdata)){
            $dataArray['clinicid'] = $clinicdata->Ref_ID;
            $dataArray['Name'] = $inputdata['name'];
            $dataArray['communication_email'] = $inputdata['communication_email'];
            $dataArray['Clinic_Type'] = $inputdata['speciality'];
            $dataArray['image'] = $inputdata['image'];
            $dataArray['Address'] = $inputdata['address'];
            $dataArray['City'] = $inputdata['street'];
            $dataArray['State'] = $inputdata['state'];
            $dataArray['Country'] = $inputdata['country'];
            $dataArray['Postal'] = $inputdata['postal'];
            $dataArray['District'] = $inputdata['district'];
            $dataArray['MRT'] = $inputdata['MRT'];
            $dataArray['Description'] = $inputdata['description'];
            $dataArray['Phone_Code'] = $inputdata['code'];
            $dataArray['Phone'] = $mobile;
            $dataArray['Website'] = $inputdata['website'];
            $dataArray['Custom_title'] = $inputdata['titel'];
            $dataArray['Personalized_Message'] = $inputdata['message'];
            $dataArray['Lng'] = $inputdata['lng'];
            $dataArray['Lat'] = $inputdata['lat'];

            $clinicUpdate = Clinic_Library::UpdateClinicDetails($dataArray);

            // $dataArray1['userid'] = $clinicdata->UserID;
            // $dataArray1['Email'] = $inputdata['email'];

            // $emailUpdate = Auth_Library::UpdateUsers($dataArray1);
            // && $emailUpdate
            if($clinicUpdate){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


    public static function getClinicBreaks($clinicid){

        $datArray = array(
            'clinicid' => $clinicid,
            'type' => 3,
            );

        $event = new ExtraEvents();
        return $event->getClinicBreaks($datArray);
    }

    public static function getClinicBreaksByWeek($clinicid, $day){

        $datArray = array(
            'clinicid' => $clinicid,
            'type' => 3,
            'day' => strtolower($day)
            );

        $event = new ExtraEvents();
        return $event->getClinicBreaksByDay($datArray);
    }

    public static function getNewClinicBreaksByWeek($clinicid, $day){

        $datArray = array(
            'clinicid' => $clinicid,
            'type' => 3,
            'day' => strtolower($day)
            );

        $event = new ExtraEvents();
        return $event->getNewClinicBreaksByDay($datArray);
    }


    public static function addClinicBreak($clinicid){

        $input = Input::all();

        $datArray = array(
            'id' => $input['guid'],
            'clinic_id' => $clinicid,
            'doctor_id' => null,
            'type' => 3,
            'day' => $input['day'],
            'start_time' => $input['time_from'],
            'end_time' => $input['time_to'],
            );

        $event = new ExtraEvents();
        $event->insertBreak($datArray);
    }


    public static function AddClinicTimeOff($clinicID){

        $allInputs = Input::all();

        if(!empty($allInputs)){

            $dataArray['party'] = 3; // Clinic Holiday
            $dataArray['partyid'] = $clinicID;
            $dataArray['title'] = null;
            $dataArray['holidaytype'] = $allInputs['holidayType'];
            $fromholiday = date ("d-m-Y", strtotime($allInputs['dateStart']));
            $dataArray['fromholiday'] = $fromholiday;
            $toholiday = date ("d-m-Y", strtotime($allInputs['dayEnd']));
            $dataArray['toholiday'] = $toholiday;
            $dataArray['fromtime'] = $allInputs['timeStart'];
            $dataArray['totime'] = $allInputs['timeEnd'];
            $dataArray['note'] = $allInputs['note'];

            $addNewHolidays = General_Library::AddManageHolidays($dataArray);

            if($addNewHolidays){

                return 1;

            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


    public static function UpdateServiceAlldoctors($clinicdata){

        $allInputs = Input::all();

        $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
        if($findClinicDetails && !empty($allInputs)){

                $procedureChanges = FALSE;

                if($allInputs['checked']==1) {      // checkbox is checked

                    $existingProcedureList = Clinic_Library::FindAllClinicDoctors($clinicdata->Ref_ID);
                    if($existingProcedureList){
                        foreach($existingProcedureList as $procedureList){

                            $findSingleProcedure = Doctor_Library::FindSingleProcedures($findClinicDetails->ClinicID,$procedureList->DoctorID,$allInputs['procedure']);
                            if($findSingleProcedure){
                                $activeProcedure['Active'] = 1;
                                $activeProcedure['updated_at'] = time();
                                $procedureChanges = Doctor_Library::UpdateProcedure($activeProcedure,$findSingleProcedure->DoctorProcedureID);
                            }else{
                                $dataProcedure['clinicid'] = $findClinicDetails->ClinicID;
                                $dataProcedure['doctorid'] = $procedureList->DoctorID;
                                $dataProcedure['procedureid'] = $allInputs['procedure'];
                                $procedureChanges = Doctor_Library::AddDoctorProcedures($dataProcedure);
                            }
                        }
                    }

                }else {

                    $existingProcedureList = Calendar_Library::FindDoctorProcedures($allInputs['procedure'],$clinicdata->Ref_ID);
                    if($existingProcedureList){
                        foreach($existingProcedureList as $procedureList){
                            $arrayProcedure['Active'] = 0;
                            $arrayProcedure['updated_at'] = time();
                            Doctor_Library::UpdateProcedure($arrayProcedure,$procedureList->DoctorProcedureID);
                        }
                    }

                }

            if($procedureChanges){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }


    public static function updatePassword($userid){

        $allInputs = Input::all();

        $new_pass = StringHelper::encode($allInputs['new_pass']);

        $data['userid'] = $userid;
        $data['Password'] = $new_pass;

        $UpdateUsers = Auth_Library::UpdateUsers($data);

        return $UpdateUsers;

    }



}
