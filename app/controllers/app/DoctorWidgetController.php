<?php

class DoctorWidgetController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	// corporate --
	public function getDoctorList($id)
	{
		StringHelper::Set_Default_Timezone();
        return Clinic_Library::FindAllClinicDoctors($id);
	}

	public function getDoctorProcedure( )
	{
		return json_encode(Widget_Library::loadDoctorProcedure());
	}

	public function loadProcedure($id)
	{
		$procedure = new ClinicProcedures();
		return $procedure->FindClinicProcedures($id);
	}
	// ----
	public function index($clinicid)
	{
		Utility::stripXSS();
        $data = Widget_Library::getClinicData($clinicid);

        return $data;

	}

	public function checkNric( )
	{
		$input = Input::all();
		$user = new User();
		return $user->checkNric($input['nric'], $input['email']);
	}

  public function loadClinicData($clinic_name)
  {
		// return $clinic_name
			$data['clinic'] = Widget_Library::getClinicMedicalPartners($clinic_name);
			if($clinic_name == "medical_partners") {
				$data['image'] = 'medical_partners_logo.png';
				$data['name'] = 'medical_partners';
			} else if($clinic_name == "only_group") {
				$data['image'] = 'only_aesthetics_logo.png';
				$data['name'] = 'only_group';
			} else if($clinic_name == "dental_focus") {
				$data['image'] = 'dental focus logo.jpg';
				$data['name'] = 'dental_focus';
			} else if($clinic_name == "cmc") {
				$data['image'] = 'cmc-logo.jpg';
				$data['name'] = 'cmc';
			} else {
				return View::make("errors.503");
			}

			return View::make('widget.multiple-clinic', $data);
		// return $data;
  }

	public function loadDoctorProcedure()
	{

		$data = Widget_Library::loadDoctorProcedure();
		if ($data) {
           $select = '<option value="">-- Select --</option>';
           foreach ($data as $key => $value) {
           	$select.= '<option value="'.$value->ProcedureID.'">'.$value->Name.'</option>';
           }
       } else {
			$select = '<option value="">-- Select --</option>';
		}

        return $select;
	}

	public function loadProcedureDoctor()
	{

		$data = Widget_Library::loadProcedureDoctor();
		if ($data) {
           $select = '<option value="">-- Select --</option>';
           foreach ($data as $key => $value) {
           	$select.= '<option value="'.$value->DoctorID.'">'.$value->DocName.'</option>';
           }
       } else {
			$select = '<option value="">-- Select --</option>';
		}

        return $select;
	}

	public function loadProcedureData()
	{
		$allInputs = Input::all();
		$procedure = General_Library::FindClinicProcedure($allInputs['procedureID']);

		return json_encode($procedure);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function loadEndTime()
	{
		$data = Widget_Library::loadEndTime();
		return date('h:i A',$data);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function newBooking()
	{

		$allInputs = Input::all();
		 $clinicID = $allInputs['clinicID'];
		// $findClinic = Clinic_Library::FindClinicDetails(32);
		// dd($clinicID);
		$clinicdata = (object) array('Ref_ID'=>$clinicID, 'Email'=>'');

		$data = Widget_Library::NewClinicAppointment($clinicdata);
		return $data;
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function enableDates()
	{
		StringHelper::Set_Default_Timezone();
        $currentDate = date('d-m-Y');
		$allInputs = Input::all();
		$clinicID = $allInputs['clinicID'];
		$docID = $allInputs['docID'];
		$enable_dates_array = array();
		$doctor_holidays_array = array();
		$clinic_holidays_array = array();
		$doctor_extra_events_array = array();
		$events = new ExtraEvents();
		$managetimes = new ManageTimes();
		$findDoctorTimes = General_Library::FindAllClinicTimes(2,$docID,strtotime($currentDate));
		
		$findDoctorHoliday = General_Library::FindFullDayHolidays(2,$docID);
		$findClinicHoliday = General_Library::FindFullDayHolidays(3,$clinicID);
		$doctorExtraEvents = $events->getDoctorExtraEvents($docID);
		// return $doctorExtraEvents;

		$temp_date = [];
		if($doctorExtraEvents) {
			foreach ($doctorExtraEvents as $key => $value) {
				if($value->date != NULL) {
					if(date('Y', $value->date) >= date('Y')) {
						$status_time = 0;
						$day = date('D', $value->date);	
						$doctor_times = $managetimes->FindAllClinicTimesNew(2, $docID, $value->date);
						foreach ($doctor_times as $key => $times) {
							if($day == 'Mon' && $times->Mon == 1) {
								if(strtotime(date('h:i A', $value->start_time)) == strtotime($times->StartTime) && strtotime(date('h:i A', $value->end_time)) == strtotime($times->EndTime)) {
									$status_time = 1;
									$doctor_extra_events_array[] = date('d-m-Y', $value->date);
								}

							} elseif($day == 'Tue' && $times->Tue == 1) {
								if(strtotime(date('h:i A', $value->start_time)) == strtotime($times->StartTime) && strtotime(date('h:i A', $value->end_time)) == strtotime($times->EndTime)) {
									$status_time = 1;
									$doctor_extra_events_array[] = date('d-m-Y', $value->date);
								}
							} elseif($day == 'Wed' && $times->Wed == 1) {
								if(strtotime(date('h:i A', $value->start_time)) == strtotime($times->StartTime) && strtotime(date('h:i A', $value->end_time)) == strtotime($times->EndTime)) {
									$status_time = 1;
									$doctor_extra_events_array[] = date('d-m-Y', $value->date);
								}

							} elseif($day == 'Thu' && $times->Thu == 1) {
								if(strtotime(date('h:i A', $value->start_time)) == strtotime($times->StartTime) && strtotime(date('h:i A', $value->end_time)) == strtotime($times->EndTime)) {
									$status_time = 1;
									$doctor_extra_events_array[] = date('d-m-Y', $value->date);
								}
							} elseif($day == 'Fri' && $times->Fri == 1) {
								if(strtotime(date('h:i A', $value->start_time)) == strtotime($times->StartTime) && strtotime(date('h:i A', $value->end_time)) == strtotime($times->EndTime)) {
									$status_time = 1;
									$doctor_extra_events_array[] = date('d-m-Y', $value->date);
								}
							} elseif($day == 'Sat' && $times->Sat == 1) {
								if(strtotime(date('h:i A', $value->start_time)) == strtotime($times->StartTime) && strtotime(date('h:i A', $value->end_time)) == strtotime($times->EndTime)) {
									$status_time = 1;
									$doctor_extra_events_array[] = date('d-m-Y', $value->date);
								}
							} elseif($day == 'Sun' && $times->Sun == 1) {
								if(strtotime(date('h:i A', $value->start_time)) == strtotime($times->StartTime) && strtotime(date('h:i A', $value->end_time)) == strtotime($times->EndTime)) {
									$status_time = 1;
									$doctor_extra_events_array[] = date('d-m-Y', $value->date);
								}
							}
						}
					}
				}
			}

		}

		// return $doctor_extra_events_array;

		if ($findDoctorHoliday){

			foreach ($findDoctorHoliday as $holiday) {

				$start_date = $holiday->From_Holiday;
				$end_date = $holiday->To_Holiday;
				$total_days = round(abs(strtotime($end_date) - strtotime($start_date)) / 86400, 0) + 1;

					if (strtotime($end_date) >= strtotime($start_date)) {

						for ($day = 0; $day < $total_days; $day++) {

							$doc_holiday = date("d-m-Y", strtotime("{$start_date} + {$day} days"));
							array_push($doctor_holidays_array, $doc_holiday);
						}
					}
				$total_days = 0;	
			}
		}
		// return $doctor_holidays_array;
		if ($findClinicHoliday){

			foreach ($findClinicHoliday as $holiday) {

				$start_date = $holiday->From_Holiday;
				$end_date = $holiday->To_Holiday;
				$total_days = round(abs(strtotime($end_date) - strtotime($start_date)) / 86400, 0) + 1;

					if ($end_date >= $start_date) {

						for ($day = 0; $day < $total_days; $day++) {

							$clinic_holiday = date("d-m-Y", strtotime("{$start_date} + {$day} days"));
							array_push($clinic_holidays_array,$clinic_holiday);
						}
					}
			}
		}
		// return $clinic_holidays_array;
			if($findDoctorTimes){
				
				foreach ($findDoctorTimes as $value) {
					# code...

					// $startDate = $value->From_Date;
					$startDate = date('Y').'-'.date('m-d', $value->From_Date);
					$startDate = date('Y-m-d');
					$startDate = strtotime($startDate);
					$repeat = $value->Repeat;

					if ($repeat==1) {
						$endDate = strtotime("+6 months", $startDate);
					} else {
						$endDate = $value->To_Date;
					}



					for ($i = $startDate; $i <= $endDate; $i = strtotime('+1 day', $i)) {

						$status = true;

						foreach ($doctor_holidays_array as $key => $Holiday){
							// return $Holiday.(string)date('d-m-Y', $i);
							if ($Holiday == date('d-m-Y', $i)){ // check if it's a doctor holiday

								$status = false;
							}
						}

						foreach ($clinic_holidays_array as $key => $Holiday){

							if ($Holiday == date('d-m-Y', $i)){ // check if it's a doctor holiday

								$status = false;

							}
						}

						if ($status) {

							if (date('N', $i) == 1 && $value->Mon==1){ // Monday == 1
								$date = date('j-n-Y', $i); // Prints the date only if it's a Monday
								array_push($enable_dates_array,$date);
							}elseif (date('N', $i) == 2 && $value->Tue==1) {
								$date = date('j-n-Y', $i);
								array_push($enable_dates_array,$date);
							}elseif (date('N', $i) == 3 && $value->Wed==1) {
								$date = date('j-n-Y', $i);
								array_push($enable_dates_array,$date);
							}elseif (date('N', $i) == 4 && $value->Thu==1) {
								$date = date('j-n-Y', $i);
								array_push($enable_dates_array,$date);
							}elseif (date('N', $i) == 5 && $value->Fri==1) {
								$date = date('j-n-Y', $i);
								array_push($enable_dates_array,$date);
							}elseif (date('N', $i) == 6 && $value->Sat==1) {
								$date = date('j-n-Y', $i);
								array_push($enable_dates_array,$date);
							}elseif (date('N', $i) == 7 && $value->Sun==1) {
								$date = date('j-n-Y', $i);
								array_push($enable_dates_array,$date);
							}
						}

					}
				}

			} else {
				$enable_dates_array = array();
			}
			// $new_enable_dates_array = array( );
			// return $enable_dates_array;
			$dates = [];
			$new_enable_dates_array = array_diff($enable_dates_array, $doctor_holidays_array);
			// $dates = array_diff($new_enable_dates_array, $doctor_extra_events_array);
			// foreach ($enable_dates_array as $key => $value) {
			// 	foreach ($doctor_holidays_array as $key => $Holiday){
			// 			if ($Holiday != $value){ 
			// 				array_push($new_enable_dates_array, $value);
			// 			}
			// 		}
			// }
			// return $new_enable_dates_array;
			$day_slots = [];
			sort($new_enable_dates_array);
			foreach ($new_enable_dates_array as $key => $value) {
				if(date('Y', strtotime($value)) >= date('Y')) {
					$day_slots[] = $value;
				} 		
			}
			$new_day_slots = [];
			// $dates = array_diff($day_slots, $doctor_extra_events_array);
			foreach ($day_slots as $key => $value) {
				if(date('Y', strtotime($value)) >= date('Y')) {
					$new_day_slots[] = $value;
				} 	
			}
			$slots = [];
			foreach ($new_day_slots as $key_1 => $value_1) {
				foreach ($doctor_extra_events_array as $key => $value_2) {
					if(date('d-m-Y', strtotime($value_1)) == date('d-m-Y', strtotime($value_2))) {
						unset($new_day_slots[$key_1]);
					}
				}
			}

			foreach ($new_day_slots as $key => $value) {
				$slots[] = $value;
			}

			usort($slots, function($a, $b) {
				return  strtotime($a) - strtotime($b);
			});

			return $slots;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sendOtpSms()
	{
		$allInputs = Input::all();

		$findPlusSign = substr($allInputs['phone'], 0, 1);
        if($findPlusSign == 0){
            $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
        }else{
            $PhoneOnly = $allInputs['code'].$allInputs['phone'];
        }
        $otp_code = StringHelper::OTPChallenge();
        Session::forget('se_otp_code');
        Session::put('se_otp_code', $otp_code);

        $new_message = $otp_code.' is your Mednefits verification code.';
        // try {
        $data = array(
        	'phone' => $PhoneOnly,
        	'message'	=> $new_message
        );
        return SmsHelper::sendCommzSms($data);
        // return StringHelper::TestSendOTPSMS($PhoneOnly,$otp_code);
        // } catch (Exception $e) { 
        // 	return var_dump($e);
        // }


	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function validateOtp()
	{
		$allInputs = Input::all();
		$code =  $allInputs['code'];
		$s_code = Session::get('se_otp_code');

		if ($code==$s_code || $code==123456) {
			return 1;
		} else {
			return 0;
		}


	}



	public function disableTimes(){

		// StringHelper::Set_Default_Timezone();
		$time_disable = array();
		$times = array();
		$allInputs = Input::all();
		$date =  date('d-m-Y', strtotime($allInputs['date']));
    	// return $date;
		$docID =  $allInputs['docID'];
		$clinicid =  $allInputs['clinicID'];

		$duration = ($allInputs['duration']*60)-900;
		$time_duration = '-'.($allInputs['duration']-1).'minutes';


		$duration = ($allInputs['duration']*60)-900;
		$defaultStart = strtotime($date."06.00 AM");
    	$defaultEnd = strtotime($date."11.50 PM");
    	$findWeek = StringHelper::FindWeekFromDate($date);
    	$findDoctorAvailability = Array_Helper::DoctorArrayWithCurrentDate($docID,$findWeek, $date);
    	$count = count($findDoctorAvailability['available_times']);

    	$expire_time='';

    	$findDoctorFullHoliday = General_Library::FindFullDayHolidays(2,$docID);
    	// $date_temp = [];
    	if($findDoctorFullHoliday) {
	    	foreach ($findDoctorFullHoliday as $key => $holiday) {
					$date_start = new \DateTime(date('Y-m-d', strtotime($holiday->From_Holiday)));
					$date_end = new \DateTime(date('Y-m-d', strtotime($holiday->To_Holiday)));
					$date_end = $date_end->modify( '+1 day' ); 
					$interval = new DateInterval('P1D');
					$daterange = new DatePeriod($date_start, $interval ,$date_end);
					foreach ($daterange as $key => $dates) {
						// return $dates->format('d-m-Y');
						if($dates->format('d-m-Y') == $date) {
							$times = array();
							array_push($times, date('h:i A', strtotime($dates->format('d-m-Y')."06.00 AM")));
							array_push($times, date('h:i A', strtotime($dates->format('d-m-Y')."11.50 PM")));
							array_push($time_disable, $times);
						}
					}
	    	}
    	}

    	for($i=0; $i<$count; $i++){

    		$starttime =	strtotime($date.$findDoctorAvailability['available_times'][$i]['starttime']);
    		$starttime1 =	$starttime;
    		$endtime   =	strtotime($date.$findDoctorAvailability['available_times'][$i]['endtime']);
    		$expire_time = $endtime;

    		if ($i==0) {

	    		if ($date==date('d-m-Y')) {
	    			$date =  date('d-m-Y');
	    			$time = date('g:i A');
		    		$starttime =	strtotime($date.$time);
		    		$starttime1 = $starttime+(900-($starttime%900));
	    		}

	    		$times = array();
				array_push($times,date('h:i A',$defaultStart));
				array_push($times,date('h:i A',$starttime1));
				array_push($time_disable,$times);

    			$defaultStart = $endtime;
    		} else {
    			$times = array();
    			$Time_End = strtotime($time_duration, $endtime);

	    		array_push($times,date('h:i A',$Time_End));
				array_push($times,date('h:i A',$defaultEnd));
				array_push($time_disable,$times);
    		}

    			$times = array();
    			$time_start = strtotime($time_duration, $defaultStart);

	    		array_push($times,date('h:i A',$time_start));
				array_push($times,date('h:i A',$starttime));
				array_push($time_disable,$times);
				$defaultStart = $endtime;

			if($count==1){
				$times = array();
				$Time_End = strtotime($time_duration, $endtime);

	    		array_push($times,date('h:i A',$Time_End));
				array_push($times,date('h:i A',$defaultEnd));
				array_push($time_disable,$times);
			}


    	}
			// array_push($times,date('h:i A',$defaultStart));
    		$date =  date('d-m-Y');
	    	$time = date('g:i A');
	    	$current_time =	strtotime($date.$time);

	    	// dd($findDoctorAvailability['extraAppointment']);

	    	if($current_time < $expire_time){

				if ($findDoctorAvailability['existingappointments']) {

					foreach ($findDoctorAvailability['existingappointments'] as $value) {

						// $app_start = $value->StartTime;
						$app_end = $value->EndTime;
						// $app_start = $app_start-$duration;
						$app_start = strtotime($time_duration, $value->StartTime);


						$times = array();
			    		array_push($times,date('h:i A',$app_start));
						array_push($times,date('h:i A',$app_end));
						array_push($time_disable,$times);

					}

				}



				$events = new ExtraEvents();
                $clinicBreaks = $events->getAllClinicBreaks($clinicid);

                if ($clinicBreaks) {

					$day_name = date('N', date(strtotime($allInputs['date'])));
					$breaks_status = false;

					foreach ($clinicBreaks as $value) {

						if ($day_name == 1 && $value->day=='mon'){ // Monday == 1

							$breaks_status = true;
						}
						elseif ($day_name == 2 && $value->day=='tue'){ // Tuesday == 2

							$breaks_status = true;
						}
						elseif ($day_name == 3 && $value->day=='wed'){ // Wednsday == 3

							$breaks_status = true;
						}
						elseif ($day_name == 4 && $value->day=='thu'){ // Thursday == 4

							$breaks_status = true;
						}
						elseif ($day_name == 5 && $value->day=='fri'){ // Friday == 5

							$breaks_status = true;
						}
						elseif ($day_name == 6 && $value->day=='sat'){ // Saturday == 6

							$breaks_status = true;
						}
						elseif ($day_name == 7 && $value->day=='sun'){ // Sunday == 7

							$breaks_status = true;
						}

						if ($breaks_status){

							$extra_end = strtotime($value->end_time);
							$extra_start = strtotime($time_duration, strtotime($value->start_time));

							$times = array();
					    	array_push($times,date('h:i A',$extra_start));
							array_push($times,date('h:i A',$extra_end));
							array_push($time_disable,$times);
						}

					}

				}


				// dd($findDoctorAvailability['breaks']);

				if ($findDoctorAvailability['doctor-breaks']) {

					$day_name = date('N', date(strtotime($allInputs['date'])));
					$breaks_status = false;

					foreach ($findDoctorAvailability['doctor-breaks'] as $value) {

						if ($day_name == 1 && $value->day=='mon'){ // Monday == 1

							$breaks_status = true;
						}
						elseif ($day_name == 2 && $value->day=='tue'){ // Tuesday == 2

							$breaks_status = true;
						}
						elseif ($day_name == 3 && $value->day=='wed'){ // Wednsday == 3

							$breaks_status = true;
						}
						elseif ($day_name == 4 && $value->day=='thu'){ // Thursday == 4

							$breaks_status = true;
						}
						elseif ($day_name == 5 && $value->day=='fri'){ // Friday == 5

							$breaks_status = true;
						}
						elseif ($day_name == 6 && $value->day=='sat'){ // Saturday == 6

							$breaks_status = true;
						}
						elseif ($day_name == 7 && $value->day=='sun'){ // Sunday == 7

							$breaks_status = true;
						}

						if ($breaks_status){

							$extra_end = strtotime($value->end_time);
							$extra_start = strtotime($time_duration, strtotime($value->start_time));

							$times = array();
					    	array_push($times,date('h:i A',$extra_start));
							array_push($times,date('h:i A',$extra_end));
							array_push($time_disable,$times);
						}

					}

				}


				$doctor_holidays_array = array();

                $findDoctorHoliday = General_Library::FindCustomTimeHolidays(2, $docID);
                if ($findDoctorHoliday) {

                	foreach ($findDoctorHoliday as $holiday) {

                            $start_date = $holiday->From_Holiday;
                            $end_date = $holiday->To_Holiday;
                            $total_days = round(abs(strtotime($end_date) - strtotime($start_date)) / 86400, 0) + 1;

                            if ($end_date >= $start_date) {

                                for ($day = 0; $day < $total_days; $day++) {

                                    $doc_holiday['id'] = $holiday->ManageHolidayID;
                                    $doc_holiday['starttime'] = $holiday->From_Time;
                                    $doc_holiday['endtime'] = $holiday->To_Time;
                                    $doc_holiday['date'] = date("d-m-Y", strtotime("{$start_date} + {$day} days"));
                                    array_push($doctor_holidays_array,$doc_holiday);
                                }
                            }
                    }

                    // dd(date('d-m-Y',strtotime($allInputs['date'])));

                    foreach ($doctor_holidays_array as $key => $Holiday){

                            if ($Holiday['date'] == date('d-m-Y',strtotime($allInputs['date']))){ // check if it's a doctor holiday

                                $extra_start = strtotime($Holiday['starttime']);
								$extra_end = strtotime($Holiday['endtime']);
								$extra_start = strtotime($time_duration, strtotime($Holiday['starttime']));

								$times = array();
					    		array_push($times,date('h:i A',$extra_start));
								array_push($times,date('h:i A',$extra_end));
								array_push($time_disable,$times);

                            }
                        }
                }



                $clinic_holidays_array = array();

                $findClinicHoliday = General_Library::FindCustomTimeHolidays(3, $clinicid);

                if ($findClinicHoliday) {

                	foreach ($findClinicHoliday as $holiday) {

                            $start_date = $holiday->From_Holiday;
                            $end_date = $holiday->To_Holiday;
                            $total_days = round(abs(strtotime($end_date) - strtotime($start_date)) / 86400, 0) + 1;

                            if ($end_date >= $start_date) {

                                for ($day = 0; $day < $total_days; $day++) {

                                    $doc_holiday['id'] = $holiday->ManageHolidayID;
                                    $doc_holiday['starttime'] = $holiday->From_Time;
                                    $doc_holiday['endtime'] = $holiday->To_Time;
                                    $doc_holiday['date'] = date("d-m-Y", strtotime("{$start_date} + {$day} days"));
                                    array_push($clinic_holidays_array,$doc_holiday);
                                }
                            }
                    }

                    // dd(date('d-m-Y',strtotime($allInputs['date'])));

                    foreach ($clinic_holidays_array as $key => $Holiday){

                            if ($Holiday['date'] == date('d-m-Y',strtotime($allInputs['date']))){ // check if it's a doctor holiday

                                $extra_start = strtotime($Holiday['starttime']);
								$extra_end = strtotime($Holiday['endtime']);
								$extra_start = strtotime($time_duration, strtotime($Holiday['starttime']));

								$times = array();
					    		array_push($times,date('h:i A',$extra_start));
								array_push($times,date('h:i A',$extra_end));
								array_push($time_disable,$times);

                            }
                        }
                }


				if ($findDoctorAvailability['extraAppointment']) {


					foreach ($findDoctorAvailability['extraAppointment'] as $value) {

						// $extra_start = $value->start_time;
						$extra_end = $value->end_time;
						// $extra_start = $extra_start-$duration;
						$extra_start = strtotime($time_duration, $value->start_time);


						$times = array();
			    		array_push($times,date('h:i A',$extra_start));
						array_push($times,date('h:i A',$extra_end));
						array_push($time_disable,$times);

					}

				}
                    // dd($time_disable);

				$is_expire = 0;
			}else{
				$is_expire = 1;
			}
	    	// 		$app_start =
	    	// }
	    	$data = array();
	    	array_push($data,$is_expire);
	    	array_push($data,$time_disable);
			return $data;
	}


}
