<?php
	class calendarLibrary {

		public static function getGroupResource($input)
		{
			$doctor_resource = [];
			$doctors = new CalendarController();
			$doctors = json_decode($doctors->getClinicDoctors($input['clinic_id']));
			foreach ($doctors as $key => $doc) {
        		$temp = array(
        			'id'		=> $doc->DoctorID,
        			'title'		=> $doc->DocName
        		);
        		array_push($doctor_resource, $temp);
        	}

        	return $doctor_resource;
		}

		public static function getGroupEvents($input)
		{
			$doctor_events = [];
			$all_events = [];
			$doctors = new CalendarController();
        	$doctors = json_decode($doctors->getClinicDoctors($input['clinic_id']));
        	foreach ($doctors as $key => $doc) {
        		$temp = array(
        			'id'		=> $doc->DoctorID,
        			'title'		=> $doc->DocName
        		);

        		$doctor_event = self::getAppointment($doc->DoctorID, $input['clinic_id'], $input['start_date']);
        		// return $doctor_event;
        		if($doctor_event) {
        			array_push($doctor_events, $doctor_event);
        		}

        	}

        	foreach ($doctor_events as $key => $events) {
        		foreach ($events as $key => $event) {
        			$all_events[] = $event;
        		}
        	}

        	return $all_events;
		}

		public static function getAppointment($doc_id, $clinic_id, $start_date)
		{
			$doctorTime = self::getDoctorAvailablity($doc_id);
			$clinicTime = self::getClinicAvailablity($clinic_id);
			// return $doctorTime;
			$mon = 0;
			$tue = 0;
			$wed = 0;
			$thu = 0;
			$fri = 0;
			$sat = 0;
			$sun = 0;
			$event_array = [];
			if ($doctorTime) {
	            foreach ($doctorTime as $val) {
	                $stime = strtotime($val->StartTime);
	                $etime = strtotime($val->EndTime);
	                if(($val->Mon==1)){
	                    foreach ($clinicTime as $k) {
	                        $cstime = strtotime($k->StartTime);
	                        $cetime = strtotime($k->EndTime);
	                        if ($k->Mon==1) {
	                                if ($cstime<=$stime && $cetime>=$etime) {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                } else if($cstime>=$stime && $cetime<=$etime){
	                                    $stime = $cstime;
	                                    $etime = $cetime;
	                                } else if($cstime<=$stime && $cetime<=$etime){
	                                    $stime = $stime;
	                                    $etime = $cetime;
	                                }else if($stime<=$cstime && $etime<=$cetime){
	                                    $stime = $cstime;
	                                    $etime = $etime;
	                                } else {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                }
	                           $day=1;
	                           // break;
	                        } else {
	                           // $day='';
	                        }
	                    }
	                    $mon = 1;
	                } else{
	                    // $day='';
	                }
	                if(($val->Tue==1)){
	                    foreach ($clinicTime as $k) {
	                        $cstime = strtotime($k->StartTime);
	                        $cetime = strtotime($k->EndTime);
	                        if ($k->Tue==1) {
	                            if ($cstime<=$stime && $cetime>=$etime) {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                } else if($cstime>=$stime && $cetime<=$etime){
	                                    $stime = $cstime;
	                                    $etime = $cetime;
	                                } else if($cstime<=$stime && $cetime<=$etime){
	                                    $stime = $stime;
	                                    $etime = $cetime;
	                                }else if($stime<=$cstime && $etime<=$cetime){
	                                    $stime = $cstime;
	                                    $etime = $etime;
	                                } else {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                }

	                           $day=2;
	                           // break;
	                        } else {
	                           // $day='';
	                        }
	                    }
	                    $tue = 1;
	                }  else{
	                   // $day='';
	                }
	                if(($val->Wed==1)){
	                    foreach ($clinicTime as $k) {
	                        $cstime = strtotime($k->StartTime);
	                        $cetime = strtotime($k->EndTime);
	                        if ($k->Wed==1) {
	                            if ($cstime<=$stime && $cetime>=$etime) {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                } else if($cstime>=$stime && $cetime<=$etime){
	                                    $stime = $cstime;
	                                    $etime = $cetime;
	                                } else if($cstime<=$stime && $cetime<=$etime){
	                                    $stime = $stime;
	                                    $etime = $cetime;
	                                }else if($stime<=$cstime && $etime<=$cetime){
	                                    $stime = $cstime;
	                                    $etime = $etime;
	                                } else {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                }
	                           $day=3;
	                           // break;
	                        } else {
	                           // $day='';
	                        }
	                    }
	                    $wed = 1;
	                }  else{
	                    // $day='';
	                }
	                if(($val->Thu==1)){
	                    foreach ($clinicTime as $k) {
	                        $cstime = strtotime($k->StartTime);
	                        $cetime = strtotime($k->EndTime);
	                        if ($k->Thu==1) {
	                            if ($cstime<=$stime && $cetime>=$etime) {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                } else if($cstime>=$stime && $cetime<=$etime){
	                                    $stime = $cstime;
	                                    $etime = $cetime;
	                                } else if($cstime<=$stime && $cetime<=$etime){
	                                    $stime = $stime;
	                                    $etime = $cetime;
	                                }else if($stime<=$cstime && $etime<=$cetime){
	                                    $stime = $cstime;
	                                    $etime = $etime;
	                                } else {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                }
	                           $day=4;
	                           // break;
	                        } else {
	                           // $day='';
	                        }
	                    }
	                    $thu = 1;
	                }  else{
	                   // $day='';
	                }
	                if(($val->Fri==1)){
	                    foreach ($clinicTime as $k) {
	                        $cstime = strtotime($k->StartTime);
	                        $cetime = strtotime($k->EndTime);
	                        if ($k->Fri==1) {
	                            if ($cstime<=$stime && $cetime>=$etime) {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                } else if($cstime>=$stime && $cetime<=$etime){
	                                    $stime = $cstime;
	                                    $etime = $cetime;
	                                } else if($cstime<=$stime && $cetime<=$etime){
	                                    $stime = $stime;
	                                    $etime = $cetime;
	                                }else if($stime<=$cstime && $etime<=$cetime){
	                                    $stime = $cstime;
	                                    $etime = $etime;
	                                } else {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                }
	                           $day=5;
	                           // break;
	                        } else {
	                           // $day='';
	                        }
	                    }
	                    $fri = 1;
	                }  else{
	                    // $day='';
	                }
	                if(($val->Sat==1)){
	                    foreach ($clinicTime as $k) {
	                        $cstime = strtotime($k->StartTime);
	                        $cetime = strtotime($k->EndTime);
	                        if ($k->Sat==1) {
	                            if ($cstime<=$stime && $cetime>=$etime) {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                } else if($cstime>=$stime && $cetime<=$etime){
	                                    $stime = $cstime;
	                                    $etime = $cetime;
	                                } else if($cstime<=$stime && $cetime<=$etime){
	                                    $stime = $stime;
	                                    $etime = $cetime;
	                                }else if($stime<=$cstime && $etime<=$cetime){
	                                    $stime = $cstime;
	                                    $etime = $etime;
	                                } else {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                }
	                           $day=6;
	                           // break;
	                        } else {
	                           // $day='';
	                        }
	                    }
	                    $sat = 1;
	                }  else{
	                    // $day='';
	                }
	                if(($val->Sun==1)){
	                    foreach ($clinicTime as $k) {
	                        $cstime = strtotime($k->StartTime);
	                        $cetime = strtotime($k->EndTime);
	                        if ($k->Sun==1) {
	                            if ($cstime<=$stime && $cetime>=$etime) {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                } else if($cstime>=$stime && $cetime<=$etime){
	                                    $stime = $cstime;
	                                    $etime = $cetime;
	                                } else if($cstime<=$stime && $cetime<=$etime){
	                                    $stime = $stime;
	                                    $etime = $cetime;
	                                }else if($stime<=$cstime && $etime<=$cetime){
	                                    $stime = $cstime;
	                                    $etime = $etime;
	                                } else {
	                                    $stime = $stime;
	                                    $etime = $etime;
	                                }
	                           $day=0;
	                           // break;
	                        } else {
	                           // $day='';
	                        }
	                    }
	                    $sun = 1;
	                }  else{
	                   // $day='';
	                }
	                $arr = array(
	                		'id'			=> 'bg_'.$doc_id,
	                		'doctor_id'		=> $doc_id,
	                		'type'			=> 'bg',
	                		'resourceId'	=> $doc_id,
	                		'start'			=> date("H:i", $stime),
	                		"end"			=> date("H:i", $etime),
	                		"dow"			=> "[".$day."]",
	                		"rendering"		=> 'inverse-background'
	                );
	                // return $arr;
	                array_push($event_array, $arr);
	            }

	            // appointments
			 	for ($i=0; $i < 7; $i++) {
					$dayOfWeek = date('d-m-Y', strtotime($start_date.' +'.$i.' day'));
		    		$getWeek = date('l', strtotime($dayOfWeek));//Monday
		    		$findWeek = \StringHelper::FindWeekFromDate($dayOfWeek);//Mon
		    		$findDoctorAvailability = \Array_Helper::DoctorArrayWithCurrentDate($doc_id,$findWeek, $dayOfWeek);
		    		// array_push($event_array,$findDoctorAvailability['existingappointments']);
					if ($findDoctorAvailability['existingappointments']) {
						foreach ($findDoctorAvailability['existingappointments'] as $val) {
							$stime = date('Y-m-d\TH:i:s', $val->StartTime);
							$etime = date('Y-m-d\TH:i:s', $val->EndTime);
							$event_type = $val->event_type;
		                    $event_status = $val->Status;
		                    $duration = round(abs($val->EndTime - $val->StartTime) / 60,2);
		                    $status = null;
		                    if ($duration <= 15){
		                        $event_titel = $val->UsrName;
		                    }elseif ($duration > 15 && $duration <= 30) {
		                        $event_titel = $val->UsrName."\n". $val->ProName;
		                    }else{
		                        $event_titel = $val->UsrName."\n". $val->ProName."\n".$val->PhoneNo ;
		                    }
		                    if($val->UserType == 5) {
		                    	$image = true;
		                    } else {
		                    	$image = false;
		                    }
							if ($event_type==1) { //google
								$title = 'Google Event';
	                            $backgroundColor = "#ffb2b2";
	                            $borderColor = "#dc8081";
	                            $type = 0;
							}
	                        elseif ($event_type==3) { //widget
	                            if ($event_status==2){ // Completed
	                                $backgroundColor = "#bbffb2";
	                                $borderColor = "#7ecf72";
	                                $title = $event_titel ."\n".'Claimed';
	                                $status = 'Concluded';
	                                $type = 0;
	                            }
	                            elseif ($event_status==4){ // No Show
	                                $backgroundColor = "#bbffb2";
	                                $borderColor = "#7ecf72";
	                                $title = $event_titel ."\n".'No Show';
	                                $status = 'No Show';
	                                $type = 0;
	                            }
	                            else {
	                                $backgroundColor = "#bbffb2";
	                                $borderColor = "#7ecf72";
	                                $title = $event_titel;
	                                $status = 'Appointment';
	                                $type = 0;
	                            }
							} elseif ($event_type == 4) { // reserver
								if ($event_status == 2){ // Completed
	                                $backgroundColor = "#73CEF4";
	                                $borderColor = "#7ecf72";
	                                $title = $event_titel ."\n".'Claimed';
	                                $status = 'Concluded';
	                                $type = 4;
	                            }
	                            elseif ($event_status==4){ // No Show
	                                $backgroundColor = "#73CEF4";
	                                $borderColor = "#7ecf72";
	                                $title = $event_titel ."\n".'No Show';
	                                $status = 'No Show';
	                                $type = 4;
	                            }
	                            else {
	                                $backgroundColor = "#73CEF4";
	                                $borderColor = "#7ecf72";
	                                $title = $event_titel;
	                                $status = 'Appointment';
	                                $type = 4;
	                            }
							}
							elseif ($event_type==0 && $val->MediaType==0) { //mobile
	                            if ($event_status==2){ // Concluded
	                                $backgroundColor = "#fef3b3";
	                                $borderColor = "#e0d079";
	                                $title = $event_titel ."\n".'Claimed';
	                                $status = 'Concluded';
	                                $type = 0;
	                            }
	                            elseif ($event_status==4){ // No Show
	                                $backgroundColor = "#fef3b3";
	                                $borderColor = "#e0d079";
	                                $title = $event_titel ."\n".'No Show';
	                                $status = 'No Show';
	                                $type = 0;
	                            }
	                            else {
	                                $backgroundColor = "#fef3b3";
	                                $borderColor = "#e0d079";
	                                $title = $event_titel;
	                                $status = 'Appointment';
	                                $type = 0;
	                            }
	                        }
	                        else{ // medicloud
	                            if ($event_status==2){ // Concluded
	                                $backgroundColor = "#b2e8ff";
	                                $borderColor = "#7bbdd7";
	                                $title = $event_titel ."\n".'Claimed';
	                                $status = 'Concluded';
	                                $type = 0;
	                            }
	                            elseif ($event_status==4){ // No Show
	                                $backgroundColor = "#b2e8ff";
	                                $borderColor = "#7bbdd7";
	                                $title = $event_titel ."\n".'No Show';
	                                $status = 'No Show';
	                                $type = 0;
	                            }
	                            else {
	                                $title = $event_titel;
	                                $backgroundColor = "#b2e8ff";
	                                $borderColor = "#7bbdd7";
	                                $status = 'Appointment';
	                                $type = 0;
	                            }
							}

							$arr = array('id'=>$val->UserAppoinmentID, 'resourceId'=>$doc_id,'title'=>$title,'status'=>$status,'start'=>$stime,'end'=> $etime, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor, 'type' => $type, 'user_id' => $val->UserID, 'image' => $image, 'doctor_id' => $doc_id, 'appointment_id' => $val->UserAppoinmentID, 'ProcedureID' => $val->ProcedureID);
							array_push($event_array, $arr);
						}
					}
				}

				// google events
				\Clinic_Library::syncAppointment($start_date, $doc_id);
				// $array = array();
					for ($j=0; $j < 7; $j++) {
		            $date = date('d-m-Y', strtotime($start_date.' +'.$j.' day'));
					$events = new ExtraEvents();
		            $events = $events->getEvents($doc_id, strtotime($date));
		            if ($events) {
			            foreach ($events as $val) {
			            	$stime = date('Y-m-d\TH:i:s', $val->start_time);
							$etime = date('Y-m-d\TH:i:s', $val->end_time);
							$type = $val->type;
							if ($type==1 && !strpos($val->remarks, "New Mednefits Appointment") && !strpos($val->remarks, "New Medicloud Appointment")) {
		                        $title = 'Google Event';
		                        $backgroundColor = "#f6d6d5";
		                        $borderColor = "#e4b0af";
		                        $color = '';
		                        if ($title=='Blocked') {
			                        $arr = array('id'=>$val->id,'title'=>$title,'resourceId'=> $doc_id,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>true, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor);
			                    } else {
			                        $arr = array('id'=>$val->id,'title'=>$title,'resourceId'=> $doc_id,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>false, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor);
			                    }
								array_push($event_array, $arr);
							} else if ($type==2){
								$title = 'Blocked';
								$color = '#e5e5e5';
		                        $backgroundColor = "";
		                        $borderColor = "";
		                        if ($title=='Blocked') {
			                        $arr = array('id'=>$val->id,'title'=>$title,'resourceId'=> $doc_id,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>true, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor);
			                    } else {
			                        $arr = array('id'=>$val->id,'title'=>$title,'resourceId'=> $doc_id,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>false, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor, 'doctor_id' => $doc_id);
			                    }
			                    array_push($event_array, $arr);
							}
			            }
		            }
				}

				// breaks
				$dataArray = array('doctor_id' => $doc_id, 'type' => 3);
				$breaks = new ExtraEvents();
				$breaks = $breaks->getDoctorBreaks($dataArray);
				$ClinicTimes = self::getClinicAvailablity($clinic_id);
				$DoctorTimes = General_Library::FindAllClinicTimesNew(2, $doc_id, strtotime(date('d-m-Y')));
				 foreach ($breaks as $value) {
		            $stime = date("H:i", strtotime($value->start_time));
		            $etime = date("H:i", strtotime($value->end_time));
		            $day = $value->day;
		            if ($day=='mon') { $day = 1;}
		            if ($day=='tue') { $day = 2;}
		            if ($day=='wed') { $day = 3;}
		            if ($day=='thu') { $day = 4;}
		            if ($day=='fri') { $day = 5;}
		            if ($day=='sat') { $day = 6;}
		            if ($day=='sun') { $day = 0;}
		            foreach ($ClinicTimes as $clinic) {
		                if($clinic->Mon==1){ $clinicday = 1;}
		                if($clinic->Tue==1){ $clinicday = 2;}
		                if($clinic->Wed==1){ $clinicday = 3;}
		                if($clinic->Thu==1){ $clinicday = 4;}
		                if($clinic->Fri==1){ $clinicday = 5;}
		                if($clinic->Sat==1){ $clinicday = 6;}
		                if($clinic->Sun==1){ $clinicday = 0;}
		                if ($day == $clinicday){
		                    if ($clinic->Active == 1) { // check whether clinic is open
		                        foreach ($DoctorTimes as $doctor) {
		                            if($doctor->Mon==1){ $doctorday = 1;}
		                            if($doctor->Tue==1){ $doctorday = 2;}
		                            if($doctor->Wed==1){ $doctorday = 3;}
		                            if($doctor->Thu==1){ $doctorday = 4;}
		                            if($doctor->Fri==1){ $doctorday = 5;}
		                            if($doctor->Sat==1){ $doctorday = 6;}
		                            if($doctor->Sun==1){ $doctorday = 0;}
		                            if ($day == $doctorday){
		                                if ($doctor->Active == 1) { // check whether doctor is available
		                                     $arr = array('id'=>$value->id,'title'=>'', 'resourceId'=> $doc_id, 'start'=>$stime,'end'=> $etime, 'color'=>'#e5e5e5','dow'=> '['.$day.']', 'editable'=>false, 'doctor_id' => $doc_id);
		                                    array_push($event_array, $arr);
		                                }
		                            }
		                        }

		                    }
		                }
		            }
		        }

				// time off
				$hol = new ManageHolidays();
				$timeoff = $hol->FindExistingClinicHolidays(2, $doc_id);

				 foreach ($timeoff as $v) {
		            $from_holiday = $v->From_Holiday;
		            $to_holiday = $v->To_Holiday;
		            $from_time = $v->From_Time;
		            $to_time = $v->To_Time;
		            $no_of_days = strtotime($to_holiday)- strtotime($from_holiday);
		            $no_of_days =  floor($no_of_days/(60*60*24))+1;
		            $note = "\n".$v->Note;

		            if ($v->From_Time==0) {$from_time = '06.00 AM'; $to_time = '10.00 PM';}

		            for ($i=0; $i < $no_of_days; $i++) {

		                $stime = date('Y-m-d\TH:i:s', date(strtotime("+$i day", strtotime($from_holiday.$from_time))));
		                $etime = date('Y-m-d\TH:i:s', date(strtotime("+$i day", strtotime($from_holiday.$to_time))));
		                // $etime = date('Y-m-d\TH:i:s', strtotime($from_holiday.$to_time. '+'.$i.'day'));

		                $arr = array('title' => 'Time Off'.$note, 'resourceId'=> $doc_id, 'start' => $stime, 'end' => $etime, 'color'=>'#e5e5e5','editable' => false, 'doctor_id' => $doc_id);
		                array_push($event_array, $arr);
		            }

		        }

	        // return $mon.date('D', strtotime($start_date));
	        // return date('D', strtotime($start_date));
	        if($mon == 0 && date('D', strtotime($start_date)) == "Mon") {
	        	$s = strtotime('00:00');
				 		$e = strtotime('24:00');
				 		$arr = array(
	          		'id'			=> 'bg_'.$doc_id,
	          		'doctor_id'		=> $doc_id,
	          		'type'			=> 'bg',
	          		'resourceId'	=> $doc_id,
	          		'start'			=> '00:0',
	          		"end"			=> '24:00',
	          		// "dow"			=> "[$day]",
	          		'editable'		=>	false,
								'color'			=>	'#e5e5e5',
	          		'status_doctor' => 0
	          );
	          array_push($event_array, $arr);
	        } 

	        if($tue == 0 && date('D', strtotime($start_date)) == "Tue") {
	        	$s = strtotime('00:00');
				 		$e = strtotime('24:00');
				 		$arr = array(
            		'id'			=> 'bg_'.$doc_id,
            		'doctor_id'		=> $doc_id,
            		'type'			=> 'bg',
            		'resourceId'	=> $doc_id,
            		'start'			=> '00:0',
            		"end"			=> '24:00',
            		// "dow"			=> "[$day]",
            		'editable'		=>	false,
								'color'			=>	'#e5e5e5',
            		'status_doctor' => 0
            );
            array_push($event_array, $arr);
	        } 

	        if($wed == 0 && date('D', strtotime($start_date)) == "Wed") {
	        	$s = strtotime('00:00');
				 		$e = strtotime('24:00');
				 		$arr = array(
	                		'id'			=> 'bg_'.$doc_id,
	                		'doctor_id'		=> $doc_id,
	                		'type'			=> 'bg',
	                		'resourceId'	=> $doc_id,
	                		'start'			=> '00:0',
	                		"end"			=> '24:00',
	                		// "dow"			=> "[$day]",
	                		'editable'		=>	false,
											'color'			=>	'#e5e5e5',
	                		'status_doctor' => 0
	                );
              array_push($event_array, $arr);
	        } 

	        if($thu == 0 && date('D', strtotime($start_date)) == "Thu") {
	        	$s = strtotime('00:00');
				 		$e = strtotime('24:00');
				 		$arr = array(
	              		'id'			=> 'bg_'.$doc_id,
	              		'doctor_id'		=> $doc_id,
	              		'type'			=> 'bg',
	              		'resourceId'	=> $doc_id,
	              		'start'			=> '00:0',
	              		"end"			=> '24:00',
	              		// "dow"			=> "[$day]",
	              		'editable'		=>	false,
										'color'			=>	'#e5e5e5',
	              		'status_doctor' => 0
	              );
            array_push($event_array, $arr);
	        }

	        if($fri == 0 && date('D', strtotime($start_date)) == "Fri") {
	        	$s = strtotime('00:00');
				 		$e = strtotime('24:00');
				 		$arr = array(
		            		'id'			=> 'bg_'.$doc_id,
		            		'doctor_id'		=> $doc_id,
		            		'type'			=> 'bg',
		            		'resourceId'	=> $doc_id,
		            		'start'			=> '00:0',
		            		"end"			=> '24:00',
		            		// "dow"			=> "[$day]",
		            		'editable'		=>	false,
										'color'			=>	'#e5e5e5',
		            		'status_doctor' => 0
		            );
		            array_push($event_array, $arr);
	        }

	        if($sat == 0 && date('D', strtotime($start_date)) == "Sat") {
	        	$s = strtotime('00:00');
				 		$e = strtotime('24:00');
				 		$arr = array(
	            		'id'			=> 'bg_'.$doc_id,
	            		'doctor_id'		=> $doc_id,
	            		'type'			=> 'bg',
	            		'resourceId'	=> $doc_id,
	            		'start'			=> '00:0',
	            		"end"			=> '24:00',
	            		// "dow"			=> "[$day]",
	            		'editable'		=>	false,
										'color'			=>	'#e5e5e5',
	            		'status_doctor' => 0
	            		);
	            		array_push($event_array, $arr);
	        }

	        if($sun == 0 && date('D', strtotime($start_date)) == "Sun") {
	        	$s = strtotime('00:00');
				 		$e = strtotime('24:00');
				 		$arr = array(
		          		'id'			=> 'bg_'.$doc_id,
		          		'doctor_id'		=> $doc_id,
		          		'type'			=> 'bg',
		          		'resourceId'	=> $doc_id,
		          		'start'			=> '00:0',
		          		"end"			=> '24:00',
		          		// "dow"			=> "[$day]",
		          		'editable'		=>	false,
									'color'			=>	'#e5e5e5',
		          		'status_doctor' => 0
		          	);
	          		array_push($event_array, $arr);
	        }
		 	} else {
		 		// $event_array['status']	= 'false';
		 		$s = strtotime('00:00');
		 		$e = strtotime('24:00');
		 		$arr = array(
          		'id'			=> 'bg_'.$doc_id,
          		'doctor_id'		=> $doc_id,
          		'type'			=> 'bg',
          		'resourceId'	=> $doc_id,
          		'start'			=> '00:0',
          		"end"			=> '24:00',
          		// "dow"			=> "[$day]",
          		'editable'		=>	false,
							'color'			=>	'#e5e5e5',
          		// "rendering"		=> 'inverse-background',
          		'status_doctor' => 0
          );
          // return $arr;
          array_push($event_array, $arr);
		 	}

		 	return $event_array;
		}

		public static function getDoctorAvailablity($doc_id)
		{
			$start_date = date('Y-m-d');//$input['current_date'];

			$findDoctorTimes = General_Library::FindAllClinicTimes(2, $doc_id, strtotime($start_date));

			return $findDoctorTimes;

		}

		public static function getClinicAvailablity($clinic_id)
	    {
	        $start_date = date('Y-m-d');//$input['current_date'];
	        $findClinicTimes = General_Library::FindAllClinicTimes(3,$clinic_id,strtotime($start_date));
	        return $findClinicTimes;

	    }

		public static function checkBookDateResource($data)
		{

			// check date
			if(strtotime($data['start_date']) < strtotime(date('Y-m-d h:i A'))) {
	            return array(
	               'status'    => 400,
	               'message'   => 'Date or Time not available'
	            );
	        }

	        // check doctor schedules

	        $findDoctorTimes = General_Library::FindAllClinicTimes(2, $data['doctor_id'], strtotime($data['start_date']));
	        $date = date('D', strtotime($data['start_date']));
	        // return $date;
	        $start = strtotime(date('h:i A', strtotime($data['start_date'])));
	        // return $findDoctorTimes;
	        $status = 0;
	        if($findDoctorTimes) {
				foreach ($findDoctorTimes as $key => $time) {
					$start_temp = strtotime(date('h:i A', strtotime($time->StartTime)));
		        	$end_temp = strtotime(date('h:i A', strtotime($time->EndTime)));
					if ($date=='Mon' && $time->Mon == 1) { 
						if($start_temp <= $start && $end_temp >= $start ) {
		            		$status = 1;
		            	} else {
		            		$status = 0;
		            	}
		            	break;
					} elseif ($date=='Tue' && $time->Tue == 1) { 
		            	if($start_temp <= $start && $end_temp >= $start ) {
		            		$status = 1;
		            	} else {
		            		$status = 0;
		            	}
		            	break;
		            } elseif ($date=='Wed' && $time->Wed == 1) { 
		            	if($start_temp <= $start && $end_temp >= $start ) {
		            		$status = 1;
		            	} else {
		            		$status = 0;
		            	}
		            	break;
		            } elseif($date=='Thu' && $time->Thu == 1) { 
		            	if($start_temp <= $start && $end_temp >= $start ) {
		            		$status = 1;
		            	} else {
		            		$status = 0;
		            	}
		            	break;
		            }elseif ($date=='Fri' && $time->Fri == 1) { 
		            	if($start_temp <= $start && $end_temp >= $start ) {
		            		$status = 1;
		            	} else {
		            		$status = 0;
		            	}
		            	break;
		            }elseif ($date=='Sat' && $time->Sat == 1) { 
		            	if($start_temp <= $start && $end_temp >= $start ) {
		            		$status = 1;
		            	} else {
		            		$status = 0;
		            	}
		            	break;
		            }elseif ($date=='Sun' && $time->Sun == 1) { 
		            	if($start_temp <= $start && $end_temp >= $start ) {
		            		$status = 1;
		            	} else {
		            		$status = 0;
		            	}
		            	break;
		            }
				}
	        } else {
	        	$status = 3;
	        }
			if($status == 1) {
				return array(
					'status'	=> 200,
					'message'	=> 'Time is within doctor schedule.'
				);
			} elseif($status == 0) {
				return array(
					'status'	=> 400,
					'message'	=> 'Slot not available.'
				);
			} else {
				return array(
					'status'	=> 400,
					'message'	=> 'Doctor does not have a schedule.'
				);
			}
		}

		public static function rescheduleAppointmentCheck($data)
		{
			$doctor_procedure = new DoctorProcedures();
			$procedure = new ClinicProcedures();
			$doctor_one = $doctor_procedure->compareDoctorProcedures($data['doctor_resource_old'], $data['procedure_id']);
			$doctor_two = $doctor_procedure->compareDoctorProcedures($data['doctor_resource_new'], $data['procedure_id']);
			$procedure_result = $procedure->ClinicProcedureByID($data['procedure_id']);
			if($doctor_one == $doctor_two) {
				return array(
					'status'	=> 200,
					'message'	=> 'Can transfer.'
				);				
			}

			return array(
				'status'	=> 400,
				'message'	=> 'Cannot reschedule appointment. Doctor that you are trying to appoint the book does not have the procedure from the previous doctor ('. $procedure_result->Name.')'
			);
		}

		public static function rescheduleAppointment($inputs)
		{
			StringHelper::Set_Default_Timezone();

	        $date = $inputs['date'];
	        $stime = $inputs['stime'];
	        $etime = $inputs['etime'];
	        $doc_id = $inputs['doctor_id'];
	        $date = date('d-m-Y',strtotime($inputs['date']));

	        $starttime = date('h:i A',strtotime($stime));
	        $endtime = date('h:i A',strtotime($etime));
	        $startTimeNow = strtotime($date.$starttime);
	        $endTimeNow = strtotime($date.$endtime);

	        $duration = ($endTimeNow-$startTimeNow)/60;
	        $data['BookDate'] = strtotime($date);
	        $data['StartTime'] = $startTimeNow;
	        $data['EndTime'] = $endTimeNow;
	        $data['Duration'] = $duration;
	        $data['DoctorID'] = (int)$doc_id;
	        $booking = General_Library::UpdateAppointment($data, $inputs['event_id']);
	        $user = new User();
	       	$clinicdata = $user->UserProfileByRef($inputs['clinic_id']);
	        // return $clinic->Email;
	        // return $booking;
	        if ($booking) {
	            # code...
	            $findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
	            $findUserAppointment = General_Library::FindUserAppointment($inputs['event_id']);
	            $findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
	            $findDoctorDetails = Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);

	            if(StringHelper::Deployment()==1){
	                $smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is updated on ".$date." from ".date('h:i A',strtotime($stime))." to ".date('h:i A',strtotime($etime)).". Thank you for using Mednefits.";
	                //$smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is confirmed on ".$inputs['bookdate'].". Thank you for using medicloud.";
	               $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
	               $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
	            }

	            $findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
                if($findClinicProcedure){
                    $procedurename = $findClinicProcedure->Name;
                }else{
                    $procedurename = null;
                }

	            $formatDate = date('l, j F Y',strtotime(date('d-m-y')));
	            $emailDdata['bookingid'] = $inputs['event_id'];
	            $emailDdata['remarks'] = $findUserAppointment->Remarks;
	            $emailDdata['bookingTime'] = date('h:i A',strtotime($stime)).' - '.date('h:i A',strtotime($etime));
	            $emailDdata['bookingNo'] = 0;
	            $emailDdata['bookingDate'] = $date;
	            $emailDdata['doctorName'] = $findDoctorDetails->Name;
	            $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
	            $emailDdata['clinicName'] = $findClinicDetails->Name;
	            $emailDdata['clinicPhoneCode'] = $findClinicDetails->Phone_Code;
	            $emailDdata['clinicPhone'] = $findClinicDetails->Phone;
	            $emailDdata['clinicAddress'] = $findClinicDetails->Address;
	            $emailDdata['clinicProcedure'] = $procedurename;
	            $emailDdata['emailName']= $findUserDetails->Name;
	            $emailDdata['emailPhone']= $findUserDetails->PhoneNo;
	            $emailDdata['emailPage']= 'email-templates.booking';
	            $emailDdata['emailTo']= $findUserDetails->Email;
	            $emailDdata['emailSubject'] = 'Booking Confirmed';

	            if(StringHelper::Deployment()==1){
	                EmailHelper::sendEmail($emailDdata);
	            }
	            //copy to company
	            $emailDdata['emailTo']= Config::get('config.booking_email');
	            if(StringHelper::Deployment()==1){
	                EmailHelper::sendEmail($emailDdata);
	            }
	            //Send email to Doctor
	            $emailDdata['emailPage']= 'email-templates.booking-doctor';
	            $emailDdata['emailTo']= $findDoctorDetails->Email;
	            if(StringHelper::Deployment()==1){
	                EmailHelper::sendEmail($emailDdata);
	            }
	            //Send email to Clinic
	            $emailDdata['emailPage']= 'email-templates.booking';
	            $emailDdata['emailTo']= $clinicdata->Email;
	            if(StringHelper::Deployment()==1){
	                EmailHelper::sendEmail($emailDdata);
	            }
	            $transaction = new Transaction( );
	            $transaction->checkAppointmentUpdateTransaction($clinicdata->Ref_ID, $findUserAppointment->UserID, $findUserAppointment->UserAppoinmentID, $findUserAppointment->ProcedureID, $findUserAppointment->DoctorID);
	            return 1;
	        }else{
	            return 0;
	        }

		}
	}

?>