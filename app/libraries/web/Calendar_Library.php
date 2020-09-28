<?php

/**
*
*/
use Carbon\Carbon;
class Calendar_Library
{
	
	function __construct()
	{
		# code...
	}
	
	public static function test(){
		dd('doooom');
	}
	
	public static function doctorsScheduleByClinic($clinicid)
	{
		$input = Input::all();
		$start_date = $input['current_date'];
		$event_array = array();
	}
	
	public static function getDoctorListWithAppointMents($clinicid)
	{
		// $doctorLists = DB::table('clinic')->where('Ref_ID', $clinicid)->where('Active', 1)->get();
		// $clinicData = DB::table('clinic')->where('Active',1)->where('ClinicID',$clinicid)->get();
		// return $clinicData;
		$appointments = array();
		$data = DB::table('doctor')
		//  ->join('user_appoinment', 'doctor.DoctorID', '=', 'user_appoinment.DoctorID')
		->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
		->where('doctor_availability.ClinicID','=', $clinicid)
		->groupBy('doctor.DoctorID')
		->get();
		foreach ($data as $key => $ap) {
			$temp = array(
				'Doctor' => $ap->Name,
				'data'  => DB::table('user_appoinment')
				// ->select('doctor.Name as DocName', 'doctor.Email as DocEmail', 'doctor.Description as DocDescription', 'doctor.Qualifications as DocQualifications', 'doctor.Specialty as DocSpecialty', 'doctor.image as DoctorImage')
				
				// ->select('user.*')
				->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
				->join('user', 'user_appoinment.UserID', '=', 'user.userID')
				->where('user_appoinment.DoctorID', '=', $ap->DoctorID)
				->get()
			);
			array_push($appointments, $temp);
		}
		
		return $appointments;
	}
	public function getDoctorsEvents($clinicid)
	{
		$input = Input::all();
		$start_date = $input['current_date'];
	}
	
	public static function getDoctorsSchedEvents($clinicdata)
	{
		$input = Input::all();
		$doctorTimes = array();
		$clinicTimes = array();
		$event_array = array();
		$all_event = array();
		$start_date = $input['current_date'];
		$findClinicTimes = General_Library::FindAllClinicTimes(3, $clinicdata, strtotime($start_date));
		
		$doctors = DB::table('doctor')
		->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
		->where('doctor_availability.ClinicID','=', $clinicdata)
		->where('doctor.Active', '=', 1)
		->groupBy('doctor.DoctorID')
		->get();
		// return $doctors;
		foreach ($doctors as $key => $docs) {
			// echo $docs->DoctorID;
			$findDoctorTimes = General_Library::FindAllClinicTimes(2, $docs->DoctorID, strtotime($start_date));
			foreach ($findDoctorTimes as $key => $value) {
				$findDoctorTimes[$key]->doctorName = $docs->Name;
				$findDoctorTimes[$key]->DoctorID = $docs->DoctorID;
			}
			
			array_push($doctorTimes, $findDoctorTimes);
			// return $doctorTimes;
			// return $doctors;
			if ($doctorTimes) {
				foreach ($doctorTimes as $key => $dT) {
					$key_temp_doctor_id = $doctorTimes[$key][0]->DoctorID;
					foreach ($dT as $key => $val) {
						$stime = strtotime($val->StartTime);
						$etime = strtotime($val->EndTime);
						
						
						
						
						
						if(($val->Mon==1)){
							foreach ($findClinicTimes as $k) {
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
									
									
									$Mon=1;
								break;
							} else {
								$Mon='';
							}
							
						}
						
						
					}  else{
						$Mon='';
					}
					
					
					if(($val->Tue==1)){
						foreach ($findClinicTimes as $k) {
							
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
								
								$Tue=2;
							break;
						} else {
							$Tue='';
						}
						
					}
					
				}  else{
					$Tue='';
				}
				
				
				if(($val->Wed==1)){
					foreach ($findClinicTimes as $k) {
						
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
							
							$Wed=3;
						break;
					} else {
						$Wed='';
					}
					
				}
				
			}  else{
				$Wed='';
			}
			
			
			if(($val->Thu==1)){
				foreach ($findClinicTimes as $k) {
					
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
						
						$Thu=4;
					break;
				} else {
					$Thu='';
				}
				
			}
			
		}  else{
			$Thu='';
		}
		
		
		if(($val->Fri==1)){
			foreach ($findClinicTimes as $k) {
				
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
					
					$Fri=5;
				break;
			} else {
				$Fri='';
			}
			
		}
		
	}  else{
		$Fri='';
	}
	
	
	if(($val->Sat==1)){
		foreach ($findClinicTimes as $k) {
			
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
				
				$Sat=6;
			break;
		} else {
			$Sat='';
		}
		
	}
}  else{
	$Sat='';
}

if(($val->Sun==1)){
	foreach ($findClinicTimes as $k) {
		
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
			
			$Sun=0;
		break;
	} else {
		$Sun='';
	}
	
}

}  else{
	$Sun='';
}


$stime = date("H:i", $stime);
$etime = date("H:i", $etime);

$arr = array('id'=>'bg','start'=>$stime,"end"=> $etime,"dow"=> "[$Mon,$Tue,$Wed,$Thu,$Fri,$Sat,$Sun]","rendering"=> 'inverse-background') ;






array_push($event_array, $arr);
}
// insert google data Here
for ($i=0; $i < 7; $i++) {
	
	$dayOfWeek = date('d-m-Y', strtotime($start_date.' +'.$i.' day'));
	$getWeek = date('l', strtotime($dayOfWeek));//Monday
	$findWeek = StringHelper::FindWeekFromDate($dayOfWeek);//Mon
	$findDoctorAvailability = Array_Helper::DoctorArrayWithCurrentDate($key_temp_doctor_id,$findWeek, $dayOfWeek);
	
	
	if ($findDoctorAvailability['existingappointments']) {
		foreach ($findDoctorAvailability['existingappointments'] as $val) {
			
			$stime = date('Y-m-d\TH:i:s', $val->StartTime);
			$etime = date('Y-m-d\TH:i:s', $val->EndTime);
			$event_type = $val->event_type;
			$event_status = $val->Status;
			// $duration = $val->Duration; // New appointment duration
			$duration = round(abs($val->EndTime - $val->StartTime) / 60,2);
			$status = null;
			
			if ($duration <= 15){
				
				$event_titel = $val->UsrName;
				
			}elseif ($duration > 15 && $duration <= 30) {
				
				$event_titel = $val->UsrName."\n". $val->ProName;
				
			}else{
				
				$event_titel = $val->UsrName."\n". $val->ProName."\n".$val->PhoneNo ;
			}
			
			
			
			if ($event_type==1) { //google
				$title = 'Google Event';
				$backgroundColor = "#ffb2b2";
				$borderColor = "#dc8081";
			}
			elseif ($event_type==3) { //widget
				
				if ($event_status==2){ // Completed
					$backgroundColor = "#bbffb2";
					$borderColor = "#7ecf72";
					$title = $event_titel ."\n".'d';
					$status = 'Concluded';
				}
				elseif ($event_status==4){ // No Show
					$backgroundColor = "#bbffb2";
					$borderColor = "#7ecf72";
					$title = $event_titel ."\n".'No Show';
					$status = 'No Show';
				}
				else {
					$backgroundColor = "#bbffb2";
					$borderColor = "#7ecf72";
					$title = $event_titel;
					$status = 'Appointment';
				}
			}elseif ($event_type==0 && $val->MediaType==0) { //mobile
				
				if ($event_status==2){ // Concluded
					$backgroundColor = "#fef3b3";
					$borderColor = "#e0d079";
					$title = $event_titel ."\n".'Concluded';
					$status = 'Concluded';
				}
				elseif ($event_status==4){ // No Show
					$backgroundColor = "#fef3b3";
					$borderColor = "#e0d079";
					$title = $event_titel ."\n".'No Show';
					$status = 'No Show';
				}
				else {
					$backgroundColor = "#fef3b3";
					$borderColor = "#e0d079";
					$title = $event_titel;
					$status = 'Appointment';
				}
			}
			else{ // medicloud
				
				if ($event_status==2){ // Concluded
					$backgroundColor = "#b2e8ff";
					$borderColor = "#7bbdd7";
					$title = $event_titel ."\n".'Concluded';
					$status = 'Concluded';
				}
				elseif ($event_status==4){ // No Show
					$backgroundColor = "#b2e8ff";
					$borderColor = "#7bbdd7";
					$title = $event_titel ."\n".'No Show';
					$status = 'No Show';
				}
				else {
					$title = $event_titel;
					$backgroundColor = "#b2e8ff";
					$borderColor = "#7bbdd7";
					$status = 'Appointment';
					
				}
			}
			
			
			$arr = array('id'=>$val->UserAppoinmentID,'title'=>$title,'status'=>$status,'start'=>$stime,'end'=> $etime, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor );
			array_push($event_array, $arr);
		}
		
	}
	
	if($i + 1 == 7) {
		array_push($all_event, $event_array);
		$event_array = array();
	}
}
// end of insert

}

}
//  end of id

}

return $all_event;
}

public static function getEvents($clinicid)
{
	$input = Input::all();
	$start_date = $input['current_date'];
	$doctorID 	= $input['doctorID'];
	// $dayView    = $input['day_view_status'];
	$event_array = array();
	
	$doctorTime = self::getDoctorAvailablity();
	$clinicTime = self::getClinicAvailablity($clinicid);
	
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
						
						
						$Mon=1;
					break;
				} else {
					$Mon='';
				}
				
			}
			
			
		}  else{
			$Mon='';
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
					
					$Tue=2;
				break;
			} else {
				$Tue='';
			}
			
		}
		
	}  else{
		$Tue='';
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
				
				$Wed=3;
			break;
		} else {
			$Wed='';
		}
		
	}
	
}  else{
	$Wed='';
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
			
			$Thu=4;
		break;
	} else {
		$Thu='';
	}
	
}

}  else{
	$Thu='';
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
			
			$Fri=5;
		break;
	} else {
		$Fri='';
	}
	
}

}  else{
	$Fri='';
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
			
			$Sat=6;
		break;
	} else {
		$Sat='';
	}
	
}
}  else{
	$Sat='';
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
			
			$Sun=0;
		break;
	} else {
		$Sun='';
	}
	
}

}  else{
	$Sun='';
}


$stime = date("H:i", $stime);
$etime = date("H:i", $etime);

$arr = array('id'=>'bg','start'=>$stime,"end"=> $etime,"dow"=> "[$Mon,$Tue,$Wed,$Thu,$Fri,$Sat,$Sun]","rendering"=> 'inverse-background');
array_push($event_array, $arr);
}

}

// Clinic_Library::syncAppointment($start_date,$doctorID);


for ($i=0; $i < 7; $i++) {
	
	$dayOfWeek = date('d-m-Y', strtotime($start_date.' +'.$i.' day'));
	$getWeek = date('l', strtotime($dayOfWeek));//Monday
	$findWeek = StringHelper::FindWeekFromDate($dayOfWeek);//Mon
	$findDoctorAvailability = Array_Helper::DoctorArrayWithCurrentDate($doctorID,$findWeek, $dayOfWeek);
	
	// dd($findDoctorAvailability['existingappointments']);
	
	if ($findDoctorAvailability['existingappointments']) {
		// dd($findDoctorAvailability['existingappointments']);
		foreach ($findDoctorAvailability['existingappointments'] as $val) {
			$stime = date('Y-m-d\TH:i:s', $val->StartTime);
			$etime = date('Y-m-d\TH:i:s', $val->EndTime);
			$event_type = $val->event_type;
			$event_status = $val->Status;
			// $duration = $val->Duration; // New appointment duration
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
			
			
			$arr = array('id'=>$val->UserAppoinmentID,'title'=>$title,'status'=>$status,'start'=>$stime,'end'=> $etime, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor, 'type' => $type, 'user_id' => $val->UserID, 'image' => $image);
			array_push($event_array, $arr);
		}
	}
	
	
}


return $event_array;
}

public static function getGoogleEvents($clinicdata){
	
	$input = Input::all();
	$start_date = $input['current_date'];
	$doctorID 	= $input['doctorID'];
	
	if($doctorID) {
		Clinic_Library::syncAppointment($start_date,$doctorID);
		
		$array = array();
		
		for ($j=0; $j < 7; $j++) {
			
			$date = date('d-m-Y', strtotime($start_date.' +'.$j.' day'));
			
			$data_events = new ExtraEvents();
			$events = $data_events->getEvents($doctorID, strtotime($date));
			$clinic_events = $data_events->findClinicBreaks(strtolower(date('D', strtotime($date))), $clinicdata->Ref_ID);
			// array_push($array, array('doctor_id' => $doctorID, 'day' => strtolower(date('D', strtotime($date)))));
			// array_push($array, $events);
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
							$arr = array('id'=>$val->id,'title'=>$title,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>true, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor);
						} else {
							$arr = array('id'=>$val->id,'title'=>$title,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>false, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor);
						}
						
						array_push($array, $arr);
					} else if ($type==2){
						$title = 'Blocked';
						$color = '#e5e5e5';
						$backgroundColor = "";
						$borderColor = "";
						if ($title=='Blocked') {
							$arr = array('id'=>$val->id,'title'=>$title,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>true, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor);
						} else {
							$arr = array('id'=>$val->id,'title'=>$title,'start'=>$stime,'end'=> $etime, 'color'=>$color, 'editable'=>false, 'backgroundColor'=>$backgroundColor, 'borderColor'=>$borderColor);
						}
						array_push($array, $arr);
					}
					
				}
			}
			
			if ($clinic_events) {
				
				foreach ($clinic_events as $val) {
					
					$stime = date("H:i", strtotime($val->start_time));
					$etime = date("H:i", strtotime($val->end_time));
					$day = $val->day;
					
					if ($day=='mon') { $day = 1;}
					if ($day=='tue') { $day = 2;}
					if ($day=='wed') { $day = 3;}
					if ($day=='thu') { $day = 4;}
					if ($day=='fri') { $day = 5;}
					if ($day=='sat') { $day = 6;}
					if ($day=='sun') { $day = 0;}
					
					if ($val->type){
						$title = '';
						$color = '#e5e5e5';
						$backgroundColor = "";
						$borderColor = "";
						
						$arr = array('id'=>$val->id,'title'=>'','start'=>$stime,'end'=> $etime, 'color'=>'#e5e5e5','dow'=> '['.$day.']', 'editable'=>false);
						array_push($array, $arr);
					}
					
				}
			}
		}
		// return $array;
		// ------------------------- load doctor breaks --------------------------------
		
		$dataArray = array('doctor_id' => $doctorID, 'type' => 3);
		$breaks = new ExtraEvents();
		$breaks = $breaks->getDoctorBreaks($dataArray);
		
		$ClinicTimes = self::getClinicAvailablity($clinicdata->Ref_ID);
		$DoctorTimes = General_Library::FindAllClinicTimesNew(2,$doctorID,strtotime(date('d-m-Y')));
		// dd($ClinicTimes);
		
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
									
									// dd($etime);
									
									$arr = array('id'=>$value->id,'title'=>'','start'=>$stime,'end'=> $etime, 'color'=>'#e5e5e5','dow'=> '['.$day.']', 'editable'=>false);
									array_push($array, $arr);
									
								}
							}
						}
						
					}
				}
			}
		}
		
		
		// ````````````````````````````````load time off````````````````````````````````````````````
		
		$hol = new ManageHolidays();
		$timeoff = $hol->FindExistingClinicHolidays(2,$doctorID);
		
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
				
				$arr = array('title' => 'Time Off'.$note, 'start' => $stime, 'end' => $etime, 'color'=>'#e5e5e5','editable' => false);
				array_push($array, $arr);
			}
			
		}

		// $clinic_timeoff = $hol->FindExistingClinicHolidays(3,$clinicdata->Ref_ID);
		// // return $clinic_timeoff;
		// foreach ($clinic_timeoff as $v) {
		// 	if((int)$v->Active == 1) {
		// 		$from_holiday = $v->From_Holiday;
		// 		$to_holiday = $v->To_Holiday;
		// 		$from_time = $v->From_Time;
		// 		$to_time = $v->To_Time;
		// 		$no_of_days = strtotime($to_holiday)- strtotime($from_holiday);
		// 		$no_of_days =  floor($no_of_days/(60*60*24))+1;
		// 		$note = "\n".$v->Note;
				
		// 		if ($v->From_Time==0) {$from_time = '06.00 AM'; $to_time = '10.00 PM';}
				
		// 		for ($i=0; $i < $no_of_days; $i++) {
					
		// 			$stime = date('Y-m-d\TH:i:s', date(strtotime("+$i day", strtotime($from_holiday.$from_time))));
		// 			$etime = date('Y-m-d\TH:i:s', date(strtotime("+$i day", strtotime($from_holiday.$to_time))));
		// 			// $etime = date('Y-m-d\TH:i:s', strtotime($from_holiday.$to_time. '+'.$i.'day'));
					
		// 			$arr = array('title' => 'Time Off'.$note, 'start' => $stime, 'end' => $etime, 'color'=>'#e5e5e5','editable' => false);
		// 			array_push($array, $arr);
		// 		}
		// 	}
			
		// }
		return $array;
	}
	
	return [];
	
}


public static function getDoctorAvailablityClinics($doctorID)
{
	$input = Input::all();
	$start_date = date('Y-m-d');//$input['current_date'];
	// $doctorID 	= $input['doctorID'];
	
	$findDoctorTimes = General_Library::FindAllClinicTimes(2,$doctorID,strtotime($start_date));
	return $findDoctorTimes;
}

public static function getDoctorAvailablity()
{
	$input = Input::all();
	$start_date = date('Y-m-d');//$input['current_date'];
	$doctorID 	= $input['doctorID'];
	
	$findDoctorTimes = General_Library::FindAllClinicTimes(2,$doctorID,strtotime($start_date));
	
	// foreach ($findDoctorTimes as $value) {
		// 	echo $value->StartTime;
		// }
		
		return $findDoctorTimes;
		
	}
	
	public static function getClinicAvailablity($clinicid)
	{
		$input = Input::all();
		$start_date = date('Y-m-d');//$input['current_date'];
		$doctorID   = $input['doctorID'];
		
		$findClinicTimes = General_Library::FindAllClinicTimes(3,$clinicid,strtotime($start_date));
		// foreach ($findDoctorTimes as $value) {
			//  echo $value->StartTime;
			// }
			
			return $findClinicTimes;
			
		}
		
		public static function getDoctorProcedure()
		{
			$allInputs = Input::all();
			$doctorID = $allInputs['docID'];
			$clinicID = $allInputs['clinicID'];
			
			return $doctorProcedures = Doctor_Library::FindDoctorProcedures($clinicID,$doctorID);
		}
		
		
		public static function getAllUsers(){
			
			$allInputs = Input::all();
			$profileType = $allInputs['userType'];
			
			$user = new User();
			$getUsers = $user->getAllUsers($profileType);
			$getIndividual = $user->getIndividual($allInputs['user_type'], $allInputs['access_type']);
			$array = array();
			
			if($getUsers){
				foreach ($getUsers as $val) {
					
					$id = $val->UserID;
					$Name = $val->Name;
					// $NRIC = $val->NRIC;
					$PhoneCode = $val->PhoneCode;
					$PhoneNo = $val->PhoneNo;
					$Email = $val->Email;
					$Address = $val->Address;
					$City = $val->City;
					$State = $val->State;
					$Zip = $val->Zip_Code;
					// if ($NRIC == null){
						//     $NRIC = '';
						// }
						if ($PhoneNo == null){
							$PhoneNo = '';
						}
						
						
						$arr = array('value'=>$Email, 'id'=>$id, 'Name'=>$Name, 'PhoneCode'=>$PhoneCode, 'PhoneNo'=>$PhoneNo, 'Email'=>$Email, 'Address'=>$Address, 'City'=>$City, 'State'=>$State, 'zip'=>$Zip );
						
						$arr2 = array('value'=>$PhoneNo, 'id'=>$id, 'Name'=>$Name, 'PhoneCode'=>$PhoneCode, 'PhoneNo'=>$PhoneNo, 'Email'=>$Email, 'Address'=>$Address, 'City'=>$City, 'State'=>$State, 'zip'=>$Zip );
						
						array_push($array, $arr);
						array_push($array, $arr2);
					}
					
					if($getIndividual) {
						foreach ($getIndividual as $val) {
							
							$id = $val->UserID;
							$Name = $val->Name;
							// $NRIC = $val->NRIC;
							$PhoneCode = $val->PhoneCode;
							$PhoneNo = $val->PhoneNo;
							$Email = $val->Email;
							$Address = $val->Address;
							$City = $val->City;
							$State = $val->State;
							$Zip = $val->Zip_Code;
							
							if ($PhoneNo == null){
								$PhoneNo = '';
							}
							
							
							$arr = array('value'=>$Email, 'id'=>$id, 'Name'=>$Name, 'PhoneCode'=>$PhoneCode, 'PhoneNo'=>$PhoneNo, 'Email'=>$Email, 'Address'=>$Address, 'City'=>$City, 'State'=>$State, 'zip'=>$Zip );
							
							$arr2 = array('value'=>$PhoneNo, 'id'=>$id, 'Name'=>$Name, 'PhoneCode'=>$PhoneCode, 'PhoneNo'=>$PhoneNo, 'Email'=>$Email, 'Address'=>$Address, 'City'=>$City, 'State'=>$State, 'zip'=>$Zip );
							
							array_push($array, $arr);
							array_push($array, $arr2);
						}
					}
					
					
					return $array;
				}else{
					return FALSE;
				}
			}
			
			
			
			
			public static function saveAppointment($clinicdata)
			{
				$allInputs = Input::all();
				StringHelper::Set_Default_Timezone();
				$wallet = new Wallet( );
				$procedure = new ClinicProcedures( );
				$transaction_data = new Transaction( );
				$clinic = new Clinic( );
				$duration = $allInputs['duration'];
				$stime = strtotime($allInputs['starttime']);
				$etime = $stime+($duration*60);
				
				//$currentDate = date('d-m-Y');
				$newDate = strtotime($allInputs['bookdate']);
				$bookDate = strtotime($allInputs['bookdate'].' '.$allInputs['starttime']);
				$newFormatDate = date('d-m-Y',$newDate);
				$currentDate = $newFormatDate;
				
				
				$getProcedure = $procedure->ClinicProcedureByID($allInputs['procedureid']);
				// return $getProcedure->ProcedureID;
				$userexistStatus = 0;
				$clinic->checkCoPaidAmount($clinicdata->Ref_ID);
				$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
				if($findClinicDetails && !empty($allInputs)){
					// $findPlusSign = substr($allInputs['phone'], 0, 1);
					
					// if($findPlusSign == 0){
						//     $PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
						// }else{
							//     $PhoneOnly = $allInputs['code'].$allInputs['phone'];
							// }
							$temp = explode(str_replace('+', '', $allInputs['code']), $allInputs['phone']);
							$number = $temp[sizeof($temp) - 1];
							$PhoneOnly = $allInputs['code'].$number;
							// return $PhoneOnly;
							//$findUser = Auth_Library::FindRealUser($allInputs['nric'],$allInputs['email']);
							$findUser = Auth_Library::FindUserEmail($allInputs['email']);
							if($findUser){
								//$userid = $findUser->UserID;
								$userid = $findUser;
								$userexistStatus = 1;
								
								$userData['userid'] = $userid;
								$userData['Name'] = $allInputs['name'];
								$userData['Email'] = $allInputs['email'];
								// $userData['NRIC'] = $allInputs['nric'];
								$userData['PhoneCode'] = $allInputs['code'];
								$userData['PhoneNo'] = $PhoneOnly;
								$userData['Address'] = $allInputs['address'];
								$userData['City'] = $allInputs['city'];
								$userData['State'] = $allInputs['statate'];
								$userData['Zip_Code'] = $allInputs['zip'];
								
								$user = new User();
								$user->updateUser ($userData);
								
							}else{
								
								$pw = StringHelper::get_random_password(8);
								
								$userData['name'] = $allInputs['name'];
								$userData['usertype'] = 1;
								$userData['email'] = $allInputs['email'];
								// $userData['nric'] = $allInputs['nric'];
								$userData['code'] = $allInputs['code'];
								$userData['mobile'] = $PhoneOnly;
								$userData['address'] = $allInputs['address'];
								$userData['city'] = $allInputs['city'];
								$userData['state'] = $allInputs['statate'];
								$userData['zip'] = $allInputs['zip'];
								$userData['ref_id'] = 0;
								$userData['activelink'] = null;
								$userData['status'] = 0;
								$userData['source'] = 1;//web;
								$userData['pw'] = StringHelper::encode($pw);
								$newuser = Auth_Library::AddNewUser($userData);
								if($newuser){
									$userid = $newuser;
									
									// nhr 2016/7/25
									
									// $emailDdata['emailPage']= 'email-templates.welcome';
									// $emailDdata['emailTo']= $allInputs['email'];
									// $emailDdata['pw']= $pw;
									// $emailDdata['emailName']= $allInputs['name'];
									// $emailDdata['emailSubject'] = 'Booking Confirmed';
									// EmailHelper::sendEmail($emailDdata);
									// dd($emailDdata);
									
								}else{
									return 0;
								}
							}
							
							
							
							
							$wallet_id = $wallet->getWalletId($userid);
							
							$bookingtime = $etime - $stime;
							$slottime = abs($bookingtime)/60;
							
							$starttime = date('h:i A',$stime);
							$endtime = date('h:i A',$etime);
							$startTimeNow = strtotime($currentDate.$starttime);
							$endTimeNow = strtotime($currentDate.$endtime);
							
							$starttime = date('h:i A',$stime);
							$endtime = date('h:i A',$etime);
							$startTimeNow = strtotime($currentDate.$starttime);
							$endTimeNow = strtotime($currentDate.$endtime);
							
							//check double booking
							$currentdate = date("d-m-Y",strtotime($newFormatDate));
							
							$findAppointments = General_Library_Mobile::FindTodayAppointments($allInputs['doctorid'],strtotime($currentdate));
							$findExtraEvents = General_Library_Mobile::FindTodayExtraEvents($allInputs['doctorid'],strtotime($currentdate));
							$activeAvailability=0;
							if ($findAppointments || $findExtraEvents) {
								if($findAppointments){
									foreach($findAppointments as $todayAppoint){
										if( ( $startTimeNow>=$todayAppoint->StartTime && $startTimeNow<$todayAppoint->EndTime) ||
										( $endTimeNow >$todayAppoint->StartTime && $endTimeNow<=$todayAppoint->EndTime) ||
										( $startTimeNow<= $todayAppoint->StartTime && $endTimeNow>=$todayAppoint->EndTime)){
											
											$activeAvailability=1;
										break;
										
									}
									
								}
							}
							
							if($findExtraEvents){
								foreach($findExtraEvents as $todayAppoint1){
									
									if( ( $startTimeNow>=$todayAppoint1->start_time && $startTimeNow<$todayAppoint1->end_time) ||
									( $endTimeNow >$todayAppoint1->start_time && $endTimeNow<=$todayAppoint1->end_time) ||
									( $startTimeNow<= $todayAppoint1->start_time && $endTimeNow>=$todayAppoint1->end_time)){
										
										$activeAvailability=1;
									break;
									
								}
								
							}
						}
						
						
					}
					
					
					// check clinic tome off
					
					$toff = new ManageHolidays();
					$off = $toff->FindTodayClinicTimeOFF($clinicdata->Ref_ID, $newFormatDate);
					if($off){
						$s_time = strtotime($starttime);
						$e_time = strtotime($endtime);
						foreach ($off as $v) {
							if ($v->Type==0) { //full day off
								return 2;
							} else {
								$ostime = strtotime($v->From_Time);
								$oetime = strtotime($v->To_Time);
								
								if (($s_time>=$ostime&&$s_time<$oetime) || $e_time>$ostime&&$e_time<=$oetime) {
									return 2; //block on clinic break
								}
								
							}
							
						}
						
					}
					// return 2;
					
					//chek clininc breaks
					
					$findWeek = StringHelper::FindWeekFromDate($currentdate);//Mon
					$brk = new ExtraEvents();
					$breaks = $brk->findClinicBreaks($findWeek, $clinicdata->Ref_ID);
					
					if($breaks){
						$s_time = strtotime($starttime);
						$e_time = strtotime($endtime);
						
						foreach ($breaks as $k) {
							$bstime = strtotime($k->start_time);
							$betime = strtotime($k->end_time);
							
							if (($s_time>=$bstime&&$s_time<$betime) || $e_time>$bstime&&$e_time<=$betime) {
								return 2; //block on clinic break
							}
							
							
						}
					}
					
					
					
					// if ($activeAvailability==1) {
						//       return 0;
						//   }
						
						
						
						
						$bookArray['userid'] = $userid;
						$bookArray['clinictimeid'] = 0;//allInputs['clinictimeid'];
						$bookArray['doctorid'] = $allInputs['doctorid'];
						$bookArray['procedureid'] = $allInputs['procedureid'];
						$bookArray['starttime'] = strtotime($currentDate.$starttime);
						$bookArray['endtime'] = strtotime($currentDate.$endtime);
						$bookArray['remarks'] = $allInputs['remarks'];
						$bookArray['bookdate'] = strtotime($currentDate);
						$bookArray['mediatype'] = 1;
						$bookArray['patient']=$allInputs['name'];
						$bookArray['price']=$allInputs['price'];
						$bookArray['duration']=$allInputs['duration'];
						$newBooking = General_Library::NewAppointment($bookArray);
						if($newBooking){
							// ......activity log
							// $guid = StringHelper::getGUID();
							// $data['id'] = $guid;
							// $data['pin'] = 1234;
							// $data['header'] = 'New Appointment';
							// $data['activity'] = 'placed a new Appointment';
							
							// Clinic_Library::insertActivityLog($data);
							
							$findUserDetails = Auth_Library::FindUserDetails($userid);
							$findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputs['doctorid']);
							$findClinicProcedure = General_Library::FindClinicProcedure($allInputs['procedureid']);
							//Update User Details
							if($userexistStatus==1){
								$userupdate['userid'] = $findUserDetails->UserID;
								$userupdate['Name'] = $allInputs['name'];
								$userupdate['PhoneCode'] = $allInputs['code'];
								$userupdate['PhoneNo'] = $PhoneOnly;
								
								$userupdate['Address'] = $allInputs['address'];
								$userupdate['City'] = $allInputs['city'];
								$userupdate['State'] = $allInputs['statate'];
								$userupdate['Zip_Code'] = $allInputs['zip'];
								
								Auth_Library::UpdateUsers($userupdate);
							}
							//Send SMS
							if(StringHelper::Deployment()==1){
								// if(strlen($findUserDetails->PhoneNo) > 8) {
									//     $smsMessage = "Hello ".$findUserDetails->Name." your appointment with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone." is confirmed for ".$currentDate.", ".date('h:i A',$stime).". Your appointment ID is: ".$newBooking.", thank you for using Mednefits. Get the free app at mednefits.com";
									
									//    $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
									
									//    $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $allInputs['name'], $allInputs['code'], $PhoneOnly, $smsMessage);
									// }
								}
								
								if($findClinicProcedure){
									$procedurename = $findClinicProcedure->Name;
								}else{
									$procedurename = null;
								}
								
								//Send Email User
								$formatDate = date('l, j F Y',strtotime($currentDate));
								$emailDdata['bookingid'] = $newBooking;
								$emailDdata['remarks'] = $allInputs['remarks'];
								$emailDdata['bookingTime'] = date('h:i A',$stime).' - '.date('h:i A',$etime);
								$emailDdata['bookingNo'] = 0;
								$emailDdata['bookingDate'] = $formatDate;
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
									// EmailHelper::sendEmail($emailDdata);
								}
								//copy to company
								$emailDdata['emailTo']= Config::get('config.booking_email');
								if(StringHelper::Deployment()==1){
									// EmailHelper::sendEmail($emailDdata);
								}
								//Send email to Doctor
								$emailDdata['emailPage']= 'email-templates.booking-doctor';
								$emailDdata['emailTo']= $findDoctorDetails->Email;
								if(StringHelper::Deployment()==1){
									// EmailHelper::sendEmail($emailDdata);
								}
								//Send email to Clinic
								$emailDdata['emailPage']= 'email-templates.booking';
								$emailDdata['emailTo']= $clinicdata->Email;
								
								if(StringHelper::Deployment()==1){
									// EmailHelper::sendEmail($emailDdata);
								}
								
								$event_id = Clinic_Library::insertGoogleCalenderAppointment($bookArray,$findDoctorDetails); //nhr
								$ua = new UserAppoinment();
								$ua->updateUserAppointment(array('event_type'=>0,'Gc_event_id'=>$event_id),$newBooking);
								$discount = $clinic->getClinicPercentage($getProcedure->ClinicID);
								
								if($findClinicDetails->co_paid_status == 1) {
									$co_paid_amount = $findClinicDetails->co_paid_amount;
									$co_paid_status = 1;
								} else {
									$co_paid_amount = $findClinicDetails->co_paid_amount;
									$co_paid_status = 0;
								}
								// save transaction data
								$transaction = array(
									'wallet_id'             => $wallet_id,
									'ClinicID'              => $getProcedure->ClinicID,
									'UserID'                => $userid,
									'ProcedureID'           => $allInputs['procedureid'],
									'DoctorID'              => $allInputs['doctorid'],
									'AppointmenID'          => $newBooking,
									'procedure_cost'        => $getProcedure->Price,
									'revenue'               => null,
									'debit'                 => null,
									'medi_percent'          => $discount['medi_percent'],
									'clinic_discount'       => $discount['discount'],
									'co_paid_amount'				=> $co_paid_amount,
									'co_paid_status'				=> $co_paid_status,
									'date_of_transaction'   => Carbon::now(),
									'created_at'            => Carbon::now(),
									'updated_at'            => Carbon::now()
								);
								
								return $transaction_data->createTransaction($transaction);
								
								// return $newBooking;
								
								
							}else{
								return 0;
							}
						}else{
							return 0;
						}
					}
					
					// ...................................................................................
					
					public static function updateAppointment($clinicdata)
					{
						$allInputs = Input::all();
						StringHelper::Set_Default_Timezone();
						$duration = $allInputs['duration'];
						$stime = strtotime($allInputs['starttime']);
						$etime = $stime+($duration*60);
						$user_update = FALSE;
						$booking_update = FALSE;
						//$currentDate = date('d-m-Y');
						$newDate = strtotime($allInputs['bookdate']);
						$newFormatDate = date('d-m-Y',$newDate);
						$currentDate = $newFormatDate;
						
						$userexistStatus = 0;
						$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
						// return $findClinicDetails;
						if($findClinicDetails && !empty($allInputs)){
							
							$findPlusSign = substr($allInputs['phone'], 0, 1);
							if($findPlusSign == 0){
								$PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
							}else{
								$PhoneOnly = $allInputs['code'].$allInputs['phone'];
							}
							
							// $findUser = Auth_Library::FindUserEmail($allInputs['email']);
							$findUser = Auth_Library::FindUserID($allInputs['user_id']);
							if($findUser){
								//$userid = $findUser->UserID;
								$userid = $findUser;
								$userexistStatus = 1;
								$userData['userid'] = $userid;
								$userData['Name'] = $allInputs['name'];
								$userData['Email'] = $allInputs['email'];
								// $userData['NRIC'] = $allInputs['nric'];
								$userData['PhoneCode'] = $allInputs['code'];
								$userData['PhoneNo'] = $PhoneOnly;
								$userData['Address'] = $allInputs['address'];
								$userData['City'] = $allInputs['city'];
								$userData['State'] = $allInputs['statate'];
								$userData['Zip_Code'] = $allInputs['zip'];
								
								$user = new User();
								if($user->updateUser($userData)) {
									$user_update = TRUE;
								}
							}else{
								
								$userData['name'] = $allInputs['name'];
								$userData['usertype'] = 1;
								$userData['email'] = $allInputs['email'];
								// $userData['nric'] = $allInputs['nric'];
								$userData['code'] = $allInputs['code'];
								$userData['mobile'] = $PhoneOnly;
								$userData['address'] = $allInputs['address'];
								$userData['city'] = $allInputs['city'];
								$userData['state'] = $allInputs['statate'];
								$userData['zip'] = $allInputs['zip'];
								$userData['ref_id'] = 0;
								$userData['activelink'] = null;
								$userData['status'] = 0;
								$newuser = Auth_Library::AddNewUser($userData);
								if($newuser){
									$userid = $newuser;
								}else{
									return array('status' => FALSE, 'Error to create User info.');
								}
							}
							
							
							$bookingtime = $etime - $stime;
							$slottime = abs($bookingtime)/60;
							
							$starttime = date('h:i A',$stime);
							$endtime = date('h:i A',$etime);
							$startTimeNow = strtotime($currentDate.$starttime);
							$endTimeNow = strtotime($currentDate.$endtime);
							
							$starttime = date('h:i A',$stime);
							$endtime = date('h:i A',$etime);
							$startTimeNow = strtotime($currentDate.$starttime);
							$endTimeNow = strtotime($currentDate.$endtime);
							
							
							$currentdate = date("d-m-Y",strtotime($newFormatDate));
							
							$findAppointments = General_Library_Mobile::FindTodayAppointments($allInputs['doctorid'],strtotime($currentdate));
							$findExtraEvents = General_Library_Mobile::FindTodayExtraEvents($allInputs['doctorid'],strtotime($currentdate));
							$activeAvailability=0;
							if ($findAppointments || $findExtraEvents) {
								if($findAppointments){
									foreach($findAppointments as $todayAppoint){
										
										if( (( $startTimeNow>=$todayAppoint->StartTime && $startTimeNow<$todayAppoint->EndTime) ||
										( $endTimeNow >$todayAppoint->StartTime && $endTimeNow<=$todayAppoint->EndTime) ||
										( $startTimeNow<= $todayAppoint->StartTime && $endTimeNow>=$todayAppoint->EndTime)) && $todayAppoint->UserAppoinmentID!=$allInputs['appointment_id']){
											
											$activeAvailability = 1;
										break;
										
									}
									
								}
							}
							
							if($findExtraEvents){
								foreach($findExtraEvents as $todayAppoint1){
									
									if( ( $startTimeNow>=$todayAppoint1->start_time && $startTimeNow<$todayAppoint1->end_time) ||
									( $endTimeNow >$todayAppoint1->start_time && $endTimeNow<=$todayAppoint1->end_time) ||
									( $startTimeNow<= $todayAppoint1->start_time && $endTimeNow>=$todayAppoint1->end_time)){
										
										$activeAvailability = 1;
									break;
									
								}
								
							}
						}
						
						
					}
					
					// check clinic tome off
					
					$toff = new ManageHolidays();
					$off = $toff->FindTodayClinicTimeOFF($clinicdata->Ref_ID, $newFormatDate);
					
					if($off){
						$s_time = strtotime($starttime);
						$e_time = strtotime($endtime);
						foreach ($off as $v) {
							if ($v->Type==0) { //full day off
								return 2;
							} else {
								$ostime = strtotime($v->From_Time);
								$oetime = strtotime($v->To_Time);
								
								if (($s_time>=$ostime&&$s_time<$oetime) || $e_time>$ostime&&$e_time<=$oetime) {
									// return 2; //block on clinic break
									return array('status' => FALSE, 'message' => 'Sorry! Clinic is closed!');
								}
								
							}
							
						}
						
					}
					
					//chek clininc breaks
					
					$findWeek = StringHelper::FindWeekFromDate($currentdate);//Mon
					$brk = new ExtraEvents();
					$breaks = $brk->findClinicBreaks($findWeek, $clinicdata->Ref_ID);
					
					if($breaks){
						$s_time = strtotime($starttime);
						$e_time = strtotime($endtime);
						
						foreach ($breaks as $k) {
							$bstime = strtotime($k->start_time);
							$betime = strtotime($k->end_time);
							
							if (($s_time>=$bstime&&$s_time<$betime) || $e_time>$bstime&&$e_time<=$betime) {
								// return 2; //block on clinic break
								return array('status' => FALSE, 'message' => 'Sorry! Clinic is closed!');
							}
							
							
						}
					}
					
					if ($activeAvailability==1) {
						return array('status' => FALSE, 'message' => 'Sorry, this time slot is taken by other appointment.');
					}
					
					$findUserDetails = Auth_Library::FindUserDetails($userid);
					//Update User Details
					// if($userexistStatus == 1){
						//   $userupdate['userid'] = $findUserDetails->UserID;
						//   $userupdate['Name'] = $allInputs['name'];
						//   $userupdate['PhoneCode'] = $allInputs['code'];
						//   $userupdate['PhoneNo'] = $PhoneOnly;
						//   $userupdate['Address'] = $allInputs['address'];
						//   $userupdate['City'] = $allInputs['city'];
						//   $userupdate['State'] = $allInputs['statate'];
						//   $userupdate['Zip_Code'] = $allInputs['zip'];
						//   return var_dump(Auth_Library::UpdateUsers($userupdate));
						//   // if(Auth_Library::UpdateUsers($userupdate)) {
							//   // 	$user_update = TRUE;
							//   // }
							// }
							
							
							$bookArray['userid'] = $userid;
							$bookArray['clinictimeid'] = 0;//allInputs['clinictimeid'];
							$bookArray['doctorid'] = $allInputs['doctorid'];
							$bookArray['procedureid'] = $allInputs['procedureid'];
							$bookArray['starttime'] = strtotime($currentDate.$starttime);
							$bookArray['endtime'] = strtotime($currentDate.$endtime);
							$bookArray['remarks'] = $allInputs['remarks'];
							$bookArray['bookdate'] = strtotime($currentDate);
							// $bookArray['mediatype'] = 1;
							// $bookArray['patient']=$allInputs['name'];
							$bookArray['price']=$allInputs['price'];
							$bookArray['duration']=$allInputs['duration'];
							$newBooking = General_Library::UpdateAppointment($bookArray,$allInputs['appointment_id']);
							// return var_dump($newBooking);
							$bookArray['patient']=$allInputs['name'];
							
							if($newBooking){
								$booking_update = TRUE;
								// $guid = StringHelper::getGUID();
								// $data['id'] = $guid;
								// $data['pin'] = 1234;
								// $data['header'] = 'Update Appointment';
								// $data['activity'] = 'updated an appointment';
								
								// Clinic_Library::insertActivityLog($data);
								$findDoctorDetails = Doctor_Library::FindDoctorDetails($allInputs['doctorid']);
								$findClinicProcedure = General_Library::FindClinicProcedure($allInputs['procedureid']);
								
								//Send SMS
								if(StringHelper::Deployment()==1){
									$smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is updated on ".$currentDate." from ".date('h:i A',$stime)." to ".date('h:i A',$etime).". Thank you for using Mednefits.";
									//$smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is confirmed on ".$allInputs['bookdate'].". Thank you for using medicloud.";
									$sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
									$saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
								}
								
								if($findClinicProcedure){
									$procedurename = $findClinicProcedure->Name;
								}else{
									$procedurename = null;
								}
								
								$formatDate = date('l, j F Y',strtotime($currentDate));
								$emailDdata['bookingid'] = $allInputs['appointment_id'];
								$emailDdata['remarks'] = $allInputs['remarks'];
								$emailDdata['bookingTime'] = date('h:i A',$stime).' - '.date('h:i A',$etime);
								$emailDdata['bookingNo'] = 0;
								$emailDdata['bookingDate'] = $formatDate;
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
								
								$app = new UserAppoinment();
								$value = $app->getAppointment($allInputs['appointment_id']);
								
								if($value->Gc_event_id !=null){
									$gc = new GoogleCalenderController();
									try {
										$gc->removeEvent($value->DoctorID,$value->Gc_event_id);
										
									} catch (Exception $e) {}
								}
								
								$event_id = Clinic_Library::insertGoogleCalenderAppointment($bookArray,$findDoctorDetails); //nhr
								$ua = new UserAppoinment();
								$ua->updateUserAppointment(array('event_type'=>0,'Gc_event_id'=>$event_id),$allInputs['appointment_id']);
								$check = DB::table('transaction_history')
								->where('AppointmenID', '=', $allInputs['appointment_id'])
								->count();
								if($check > 0)
								{
									$data = array(
										'DoctorID'          => $allInputs['doctorid'],
										'ProcedureID'       => $allInputs['procedureid'],
										'procedure_cost'    => $allInputs['price']
									);
									DB::table('transaction_history')->where('AppointmenID', '=', $allInputs['appointment_id'])->update($data);
								}
							}
							
							if($booking_update && $user_update) {
								return array('status' => TRUE, 'message' => 'Booking and User details updated.');
							} else if($booking_update || $user_update) {
								return array('status' => TRUE, 'message' => 'Booking and User details updated.');
							}
						} else {
							return array('status' => FALSE, 'message' => 'Something went wrong. Please contact Mednefits Team.');
						}
						
						return array('booking' => $booking_update, 'user' => $user_update);
					}
					
					// ..................................................................................................
					
					public static function saveAppointmentFromReserve($clinicdata)
					{
						$allInputs = Input::all();
						StringHelper::Set_Default_Timezone();
						$duration = $allInputs['duration'];
						$doctor_id = $allInputs['doctorid'];
						$procedure_id = $allInputs['procedureid'];
						$remarks = $allInputs['remarks'];
						$date = $allInputs['bookdate'];
						$stime = strtotime($allInputs['starttime']);
						$etime = $stime+($duration*60);
						
						// $guid = StringHelper::getGUID();
						
						$newDate = strtotime($date);
						$newFormatDate = date('d-m-Y',$newDate);
						$currentDate = $newFormatDate;
						
						$starttime = date('h:i A',$stime);
						$endtime = date('h:i A',$etime);
						$startTimeNow = strtotime($currentDate.$starttime);
						$endTimeNow = strtotime($currentDate.$endtime);
						
						// chck for double booking
						$currentdate = date("d-m-Y",strtotime($date));
						
						$findAppointments = General_Library_Mobile::FindTodayAppointments($doctor_id,strtotime($currentdate));
						$findExtraEvents = General_Library_Mobile::FindTodayExtraEvents($doctor_id,strtotime($currentdate));
						$activeAvailability=0;
						// $getProcedure = $procedure->ClinicProcedureByID($allInputs['procedureid']);
						$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
						if ($findAppointments || $findExtraEvents) {
							if($findAppointments){
								foreach($findAppointments as $todayAppoint){
									if( ( $startTimeNow>=$todayAppoint->StartTime && $startTimeNow<$todayAppoint->EndTime) ||
									( $endTimeNow >$todayAppoint->StartTime && $endTimeNow<=$todayAppoint->EndTime) ||
									( $startTimeNow<= $todayAppoint->StartTime && $endTimeNow>=$todayAppoint->EndTime)){
										
										$activeAvailability=1;
									break;
									
								}
								
							}
						}
						
						if($findExtraEvents){
							foreach($findExtraEvents as $todayAppoint1){
								
								if( ( $startTimeNow>=$todayAppoint1->start_time && $startTimeNow<$todayAppoint1->end_time) ||
								( $endTimeNow >$todayAppoint1->start_time && $endTimeNow<=$todayAppoint1->end_time) ||
								( $startTimeNow<= $todayAppoint1->start_time && $endTimeNow>=$todayAppoint1->end_time)){
									
									$activeAvailability=1;
								break;
								
							}
							
						}
					}
					
					
				}
				//chek clininc breaks and timm off
				
				$findWeek = StringHelper::FindWeekFromDate($newFormatDate);//Mon
				$brk = new ExtraEvents();
				$breaks = $brk->findClinicBreaks($findWeek, $clinicdata->Ref_ID);
				
				if($breaks){
					
					foreach ($breaks as $k) {
						$bstime = strtotime($k->start_time);
						$betime = strtotime($k->end_time);
						
						if (($stime>=$bstime&&$stime<$betime) || $etime>$bstime&&$etime<=$betime) {
							return 2; //block on clinic break
						}
						
						
					}
				}
				
				
				
				
				if ($activeAvailability==1) {
					return 0; //double booking
				} else {
					$user = new User();
					$wallet = new Wallet( );
					$procedure = new ClinicProcedures( );
					$transaction_data = new Transaction( );
					$findPlusSign = substr($allInputs['phone'], 0, 1);
					if($findPlusSign == 0){
						$PhoneOnly = $allInputs['code'].substr($allInputs['phone'], 1);
					}else{
						$PhoneOnly = $allInputs['code'].$allInputs['phone'];
					}
					$userid = $user->createUserFromReserve($allInputs['email'], $PhoneOnly, $allInputs['code'], $allInputs['name']);
					$wallet_id = $wallet->getWalletId($userid);
					// $data['event_id'] = 4;
					
					
					$bookArray['userid'] = $userid;
					$bookArray['clinictimeid'] = 0;//allInputs['clinictimeid'];
					$bookArray['doctorid'] = $doctor_id;
					$bookArray['procedureid'] = $procedure_id;
					$bookArray['starttime'] = $startTimeNow;
					$bookArray['endtime'] = $endTimeNow;
					$bookArray['remarks'] = $remarks;
					$bookArray['bookdate'] = strtotime($currentDate);
					$bookArray['mediatype'] = 4;
					$bookArray['patient'] = 'Mednefits User';
					$bookArray['price']= $allInputs['price'];
					$bookArray['duration']= $duration;
					$newBooking = General_Library::NewAppointment($bookArray);
					if($newBooking){
						$findUserDetails = $user->FindUserDetailsFromReserve($userid);
						$findDoctorDetails = Doctor_Library::FindDoctorDetails($doctor_id);
						$findClinicProcedure = General_Library::FindClinicProcedure($procedure_id);
						$getProcedure = $procedure->ClinicProcedureByID($procedure_id);
						//Send SMS
						// if(StringHelper::Deployment()==1){
							//  $smsMessage = "Hello ".$findUserDetails->Name." your appointment with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone." is confirmed for ".$currentDate.", ".date('h:i A',$startTimeNow).". Your appointment ID is: ".$newBooking.", thank you for using Mednefits. Get the free app at mednefits.com";
							
							// $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
							
							// $smsMessage_2 = "Hello ".$findUserDetails->Name.", your appointment with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone." is tomorrow at ".date('h:i A',$stime).". Thank you for using Mednefits. Get the free app at mednefits.sg ";
							
							
							// $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage_2);
							
							// $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
							
							// $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage_2);
							// }
							
							if($findClinicProcedure){
								$procedurename = $findClinicProcedure->Name;
							}else{
								$procedurename = null;
							}
							//Send Email User
							$formatDate = date('l, j F Y',strtotime($currentDate));
							$emailDdata['bookingid'] = $newBooking;
							$emailDdata['remarks'] = $remarks;
							$emailDdata['bookingTime'] = date('h:i A',$startTimeNow).' - '.date('h:i A',$endTimeNow);
							$emailDdata['bookingNo'] = 0;
							$emailDdata['bookingDate'] = $formatDate;
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
							$emailDdata['emailSubject'] = 'Booking Confirmed Via Reserve Blocker';
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
							$event_id = Clinic_Library::insertGoogleCalenderAppointment($bookArray,$findDoctorDetails); //nhr
							$ua = new UserAppoinment();
							$ua->updateUserAppointment(array('event_type'=>4,'Gc_event_id'=>$event_id),$newBooking);
							
							$clinic = new Clinic( );
							$discount = $clinic->getClinicPercentage($getProcedure->ClinicID);
							// save transaction data
							$transaction = array(
								'wallet_id'             => $wallet_id,
								'ClinicID'              => $getProcedure->ClinicID,
								'UserID'                => $userid,
								'ProcedureID'           => $allInputs['procedureid'],
								'DoctorID'              => $allInputs['doctorid'],
								'AppointmenID'          => $newBooking,
								'procedure_cost'        => $getProcedure->Price,
								'revenue'               => null,
								'debit'                 => null,
								'medi_percent'          => $discount['medi_percent'],
								'clinic_discount'       => $discount['discount'],
								'date_of_transaction'   => Carbon::now(),
								'created_at'            => Carbon::now(),
								'updated_at'            => Carbon::now()
							);
							return $transaction_data->createTransaction($transaction);
						}else{
							return 0;
						}
						
						// $events = new ExtraEvents();
						// $events->insertEvent($data);
						// return 1;
					}
				}
				
				public static function saveBlocker($clinicid)
				{
					$allInputs = Input::all();
					StringHelper::Set_Default_Timezone();
					$duration = $allInputs['duration'];
					$doctor_id = $allInputs['doctorid'];
					$remarks = $allInputs['remarks'];
					$date = $allInputs['bookdate'];
					$stime = strtotime($allInputs['starttime']);
					$etime = $stime+($duration*60);
					
					$guid = StringHelper::getGUID();
					
					$newDate = strtotime($date);
					$newFormatDate = date('d-m-Y',$newDate);
					$currentDate = $newFormatDate;
					
					$starttime = date('h:i A',$stime);
					$endtime = date('h:i A',$etime);
					$startTimeNow = strtotime($currentDate.$starttime);
					$endTimeNow = strtotime($currentDate.$endtime);
					
					// chck for double booking
					$currentdate = date("d-m-Y",strtotime($date));
					
					$findAppointments = General_Library_Mobile::FindTodayAppointments($doctor_id,strtotime($currentdate));
					$findExtraEvents = General_Library_Mobile::FindTodayExtraEvents($doctor_id,strtotime($currentdate));
					$activeAvailability=0;
					if ($findAppointments || $findExtraEvents) {
						if($findAppointments){
							foreach($findAppointments as $todayAppoint){
								if( ( $startTimeNow>=$todayAppoint->StartTime && $startTimeNow<$todayAppoint->EndTime) ||
								( $endTimeNow >$todayAppoint->StartTime && $endTimeNow<=$todayAppoint->EndTime) ||
								( $startTimeNow<= $todayAppoint->StartTime && $endTimeNow>=$todayAppoint->EndTime)){
									
									$activeAvailability=1;
								break;
								
							}
							
						}
					}
					
					if($findExtraEvents){
						foreach($findExtraEvents as $todayAppoint1){
							
							if( ( $startTimeNow>=$todayAppoint1->start_time && $startTimeNow<$todayAppoint1->end_time) ||
							( $endTimeNow >$todayAppoint1->start_time && $endTimeNow<=$todayAppoint1->end_time) ||
							( $startTimeNow<= $todayAppoint1->start_time && $endTimeNow>=$todayAppoint1->end_time)){
								
								$activeAvailability=1;
							break;
							
						}
						
					}
				}
				
				
			}
			//chek clininc breaks and timm off
			
			$findWeek = StringHelper::FindWeekFromDate($newFormatDate);//Mon
			$brk = new ExtraEvents();
			$breaks = $brk->findClinicBreaks($findWeek, $clinicid);
			
			if($breaks){
				
				foreach ($breaks as $k) {
					$bstime = strtotime($k->start_time);
					$betime = strtotime($k->end_time);
					
					if (($stime>=$bstime&&$stime<$betime) || $etime>$bstime&&$etime<=$betime) {
						return 2; //block on clinic break
					}
					
					
				}
			}
			
			
			
			
			if ($activeAvailability==1) {
				return 0; //double booking
			} else {
				
				$data['id'] = $guid;
				$data['type'] = 2; //blocker
				$data['date'] = strtotime($currentDate);
				$data['start_time'] = $startTimeNow;
				$data['end_time'] = $endTimeNow;
				$data['doctor_id'] = $doctor_id;
				$data['remarks'] = $remarks;
				$data['event_id'] = null;
				
				
				$events = new ExtraEvents();
				$events->insertEvent($data);
				return 1;
			}
		}
		
		
		public static function blockUnavailable($clinicid)
		{
			$allInputs = Input::all();
			StringHelper::Set_Default_Timezone();
			$cur_date = $allInputs['current_date'];
			$cur_time = $allInputs['time'];
			$doctorID = $allInputs['doctorID'];
			$status = 3; //not available
			
			$ctime = date("H:i", strtotime($cur_time));
			$dayOfWeek = date('d-m-Y', strtotime($cur_date));
			$selected_date_time = strtotime($dayOfWeek.$ctime);
			$cur_date_time = strtotime(date('d-m-Y H:i'));
			// return date('d-m-Y H:i');
			$current_year = date('Y');
			$selected_year = $allInputs['year'];
			$getWeek = date('l', strtotime($dayOfWeek));//Monday
			$findWeek = StringHelper::FindWeekFromDate($dayOfWeek);//Mon
			$findDoctorAvailability = Array_Helper::DoctorArrayWithCurrentDate($doctorID,$findWeek, $dayOfWeek);
			
			$doctorTime = self::getDoctorAvailablity();
			$clinicTime = self::getClinicAvailablity($clinicid);
			
			$ctime = strtotime($ctime);
			
			foreach ($doctorTime as $val) {
				$stime = date("H:i", strtotime($val->StartTime));
				$etime = date("H:i", strtotime($val->EndTime));
				
				$stime = strtotime($stime);
				$etime = strtotime($etime);
				
				
				if($val->Mon==1 && $findWeek=='Mon')  {
					
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
							
							if ($ctime>=$stime && $ctime<$etime) {
								$status = 0;
							}
						break;
					}
					
				}
				
			}else if($val->Tue==1 && $findWeek=='Tue') {
				
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
						
						if ($ctime>=$stime && $ctime<$etime) {
							$status = 0;
						}
					break;
				}
				
			}
			
		}else if($val->Wed==1 && $findWeek=='Wed') {
			
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
					
					if ($ctime>=$stime && $ctime<$etime) {
						$status = 0;
					}
				break;
			}
			
		}
		
	}else if($val->Thu==1 && $findWeek=='Thu') {
		
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
				
				if ($ctime>=$stime && $ctime<$etime) {
					$status = 0;
				}
			break;
		}
		
	}
	
}else if($val->Fri==1 && $findWeek=='Fri') {
	
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
			
			if ($ctime>=$stime && $ctime<$etime) {
				$status = 0;
			}
		break;
	}
	
}

}else if($val->Sat==1 && $findWeek=='Sat') {
	
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
			
			if ($ctime>=$stime && $ctime<$etime) {
				$status = 0;
			}
		break;
	}
	
}


}else if($val->Sun==1 && $findWeek=='Sun') {
	
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
			
			if ($ctime>=$stime && $ctime<$etime) {
				$status = 0;
			}
		break;
	}
	
}

}

}

// dd($status);

// if($findDoctorAvailability==null){
	//     $status = 1 ;// holyday
	// }
	
	//  if ($selected_date_time < $currentdate_1_year && $cur_date_time > $selected_date_time ) {
		//     // $status = 2; //backdate
		// 	$status = 'backdate';
		// } else {
			// 	$status = 'yep';
			// }
			if($selected_year == $current_year) {
				if ($cur_date_time > $selected_date_time ) {
					$status = 2; //backdate
				} 
			} else if($cur_date_time > $selected_date_time && $selected_year > $current_year) {
				$status = 0; //backdate
			}
			// dd($cur_date_time. '--'.$selected_date_time );
			return $status;
		}
		
		
		
		public static function getAppointmentDetails()
		{
			$allInputs = Input::all();
			$appointment_id = $allInputs['appointment_id'];
			
			$app = new UserAppoinment();
			$data = $app->getAppointment($appointment_id);
			// dd($data);
			$user = new User();
			$data1 = $user->getUserProfile($data->UserID);
			
			$proc = new ClinicProcedures();
			$data2 = $proc->ClinicProcedureByID($data->ProcedureID);
			
			$doc = new Doctor();
			$data3 = $doc->ClinicDoctors($data->DoctorID);
			
			
			$arr['appointment_id'] = $data->UserAppoinmentID;
			$arr['user_id'] = $data->UserID;
			$arr['procedure'] = $data2->Name;
			$arr['procedure_id'] = $data->ProcedureID;
			$arr['doctor_id'] = $data3->DoctorID;
			$arr['doctor'] = $data3->Name;
			$arr['cost'] = $data->Price;
			$arr['duration'] = $data->Duration;
			$arr['customer'] = $data1->Name;
			$arr['nric'] = $data1->NRIC;
			$arr['email'] = $data1->Email;
			$arr['phone'] = $data1->PhoneNo;
			$arr['phoneCode'] = $data1->PhoneCode;
			$arr['address'] = $data1->Address;
			$arr['city'] = $data1->City;
			$arr['state'] = $data1->State;
			$arr['zip'] = $data1->Zip_Code;
			$arr['date'] = date('l, F d, Y',$data->BookDate);
			$arr['time'] = date('g:i a',$data->StartTime). ' - ' .date('g:i a',$data->EndTime);
			$arr['time1'] = date('g:i A',$data->StartTime);
			$arr['note'] = $data->Remarks;
			
			
			
			
			return $arr;
			
		}
		
		/* ----- Used to get extra event -----  */
		
		public static function getExtraEventDetails()
		{
			$allInputs = Input::all();
			$appointment_id = $allInputs['appointment_id'];
			
			$app = new ExtraEvents();
			$data = $app->getExtraEvents($appointment_id);
			
			
			$doc = new Doctor();
			$data1 = $doc->ClinicDoctors($data->doctor_id);
			
			
			$arr['event_id'] = $data->id;
			// $arr['doctor'] = $data1->Name;
			// $arr['date'] = date('l, F d, Y',$data->date);
			// $arr['time'] = date('g:i a',$data->start_time). ' - ' .date('g:i a',$data->end_time);
			
			$arr['description'] = $data1->Name.' (Doctor) '.date('l, F d, Y',$data->date).' '. date('g:i a',$data->start_time). ' - ' .date('g:i a',$data->end_time);
			if ($data->remarks=='') {
				$arr['note'] = '';
			}else{
				$arr['note'] = 'Note : '. $data->remarks;
			}
			
			
			// dd($arr);
			return $arr;
			
		}
		
		/* ----- Used to Delete event blocker ------  */
		
		public static function deleteBlockerDetails()
		{
			$input = Input::all();
			$Event_id = $input['Event_id'];
			
			$event = new ExtraEvents();
			$deleteEvent = $event->removeExtraEvents($Event_id);
			
		}
		
		/* ----- Used to Delete appointment ------  */
		
		public static function deleteAppointmentDetails($clinicdata){
			$allInputs = Input::all();
			StringHelper::Set_Default_Timezone();
			$currentDate = date('d-m-Y');
			
			$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
			$findUserAppointment = General_Library::FindUserAppointment($allInputs['appointment_id']);
			$book_status = $findUserAppointment->Status;
			if($findClinicDetails && $findUserAppointment){
				$bookArray['Active'] = 0;
				$bookArray['Status'] = 3;
				
				$updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
				if($updateAppointment){
					$findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
					$findDoctorDetails =    Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
					$findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
					//Send SMS
					if($book_status != 2) {
						if(StringHelper::Deployment()==1){
							// $smsMessage = "Hello ".$findUserDetails->Name." we are sorry to see you cancelled your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.". Please feel free to get in touch with us on www.medicloud.sg and let us know if we can be of any assistance.";
							
							// $smsMessage = "Hello ".$findUserDetails->Name." we are sorry to see you cancelled your appointment with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone.".  Please feel free to get in touch with us on happiness@mednefits.com and let us know if we can be of any assistance.";
							
							// $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
							// $saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
						}
					}
					if($findClinicProcedure){
						$procedurename = $findClinicProcedure->Name;
					}else{
						$procedurename = null;
					}
					//Send Email
					// $formatDate = date('l, j F Y',strtotime($findUserAppointment->BookDate));
					// $formatDate = date('l, j F Y',$findUserAppointment->BookDate);
					// $emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
					// $emailDdata['remarks'] = $findUserAppointment->Remarks;
					// $emailDdata['bookingTime'] = date('h:i A',$findUserAppointment->StartTime).' - '.date('h:i A',$findUserAppointment->EndTime);
					// $emailDdata['bookingNo'] = 0;
					// $emailDdata['bookingDate'] = $formatDate;
					// $emailDdata['doctorName'] = $findDoctorDetails->Name;
					// $emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
					// $emailDdata['clinicName'] = $findClinicDetails->Name;
					// $emailDdata['clinicAddress'] = $findClinicDetails->Address;
					// $emailDdata['clinicProcedure'] = $procedurename;
					
					// $emailDdata['emailName']= $findUserDetails->Name;
					// $emailDdata['emailPage']= 'email-templates.booking-cancel';
					// $emailDdata['emailTo']= $findUserDetails->Email;
					// $emailDdata['emailSubject'] = 'Your booking has been cancelled!';
					// EmailHelper::sendEmail($emailDdata);
					$googleCalender = new GoogleCalenderController();
					try {
						$googleCalender->removeEvent($findUserAppointment->DoctorID,$findUserAppointment->Gc_event_id);
						
					} catch (Exception $e) {}
					
					return 1;
				}else{
					return 0;
				}
			}else{
				return 0;
			}
		}
		
		
		/* ----- Used to conclude appointment ------- */
		
		public static function concludedAppointment($clinicdata){
			$allInputs = Input::all();
			StringHelper::Set_Default_Timezone();
			$currentDate = date('d-m-Y');
			
			$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
			$findUserAppointment = General_Library::FindUserAppointment($allInputs['appointment_id']);
			if($findClinicDetails && $findUserAppointment){
				$bookArray['Active'] = 1;
				$bookArray['Status'] = 2;
				
				$updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
				if($updateAppointment){
					$findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
					if($findClinicProcedure){
						$procedurename = $findClinicProcedure->Name;
					}else{
						$procedurename = null;
					}
					$findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
					$findDoctorDetails = Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
					
					// Send SMS
					if(StringHelper::Deployment()==1){
						$smsMessage = "Hello ".$findUserDetails->Name.", thank you for using Mednefits, it was a pleasure serving you. Please feel free to get in touch with us on mednefits.com and share your experience. Get the free app at mednefits.com";
						$sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
						$saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
					}
					
					// Email to User
					$formatDate = date('l, j F Y',$findUserAppointment->BookDate);
					$emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
					$emailDdata['remarks'] = $findUserAppointment->Remarks;
					$emailDdata['bookingTime'] = date('h:i A',$findUserAppointment->StartTime).' - '.date('h:i A',$findUserAppointment->EndTime);
					$emailDdata['bookingNo'] = 0;
					$emailDdata['bookingDate'] = $formatDate;
					$emailDdata['doctorName'] = $findDoctorDetails->Name;
					$emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
					$emailDdata['clinicName'] = $findClinicDetails->Name;
					$emailDdata['clinicPhoneCode'] = $findClinicDetails->Phone_Code;
					$emailDdata['clinicPhone'] = $findClinicDetails->Phone;
					$emailDdata['clinicAddress'] = $findClinicDetails->Address;
					$emailDdata['clinicProcedure'] = $procedurename;
					$emailDdata['emailName']= $findUserDetails->Name;
					$emailDdata['emailPage']= 'email-templates.booking-conclude';
					$emailDdata['emailTo']= $findUserDetails->Email;
					$emailDdata['emailSubject'] = 'Your booking is concluded!';
					if(StringHelper::Deployment()==1){
						EmailHelper::sendEmail($emailDdata);
					}
					
					return 1;
				}else{
					return 0;
				}
			}else{
				return 0;
			}
			
		}
		
		public static function concludedAppointmentFromClaimTransaction($clinicdata, $allInputs, $appointment_id){
			$hostName = $_SERVER['HTTP_HOST'];
			$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
			$server = $protocol.$hostName;
			// $allInputs = Input::all();
			StringHelper::Set_Default_Timezone();
			$currentDate = date('d-m-Y');
			
			$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
			$findUserAppointment = General_Library::FindUserAppointment($appointment_id);
			if($findClinicDetails && $findUserAppointment){
				$bookArray['Active'] = 1;
				$bookArray['Status'] = 2;
				
				$updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
				if($updateAppointment){
					$findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
					if($findClinicProcedure){
						$procedurename = $findClinicProcedure->Name;
					}else{
						$procedurename = null;
					}
					$findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
					$findDoctorDetails = Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
					
					// Send SMS
					if(StringHelper::Deployment()==1){
						// $smsMessage = "Hello ".$findUserDetails->Name.", thank you for using Mednefits, it was a pleasure serving you. Please feel free to get in touch with us on mednefits.com and share your experience. Get the free app at mednefits.com";
						$smsMessage = "Hi ".$findUserDetails->Name.", thank you for using Mednefits, it was a pleasure serving you. We hope you had a great health care experience from us. Please feel free to get in touch with us on happiness@mednefits.com and share your experience.";
						$sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
						$saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
					}
					
					// Email to User
					$formatDate = date('l, j F Y',$findUserAppointment->BookDate);
					$emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
					$emailDdata['remarks'] = $findUserAppointment->Remarks;
					$emailDdata['bookingTime'] = date('h:i A',$findUserAppointment->StartTime).' - '.date('h:i A',$findUserAppointment->EndTime);
					$emailDdata['bookingNo'] = 0;
					$emailDdata['bookingDate'] = $formatDate;
					$emailDdata['doctorName'] = $findDoctorDetails ? $findDoctorDetails->Name : '';
					$emailDdata['doctorSpeciality'] = $findDoctorDetails ? $findDoctorDetails->Specialty : '';
					$emailDdata['clinicName'] = $findClinicDetails->Name;
					$emailDdata['clinicPhoneCode'] = $findClinicDetails->Phone_Code;
					$emailDdata['clinicPhone'] = $findClinicDetails->Phone;
					$emailDdata['clinicAddress'] = $findClinicDetails->Address;
					$emailDdata['clinicProcedure'] = $procedurename;
					$emailDdata['emailName']= $findUserDetails->Name;
					$emailDdata['emailPage']= 'email-templates.e-receipt';
					// $emailDdata['emailPage']= 'email-templates.new-conclude';
					$emailDdata['emailTo']= $findUserDetails->Email;
					$emailDdata['emailSubject'] = 'Hope you had a great health care experience!';
					$emailDdata['server']	= $server.'/conclude/page/'.$findUserAppointment->UserAppoinmentID;
					// check if first time to book
					$appointment_class = new UserAppoinment();
					$result_check = $appointment_class->checkUserFirstTimeBook($findUserAppointment->UserID);
					
					if(StringHelper::Deployment()==1){
						EmailHelper::sendEmail($emailDdata);
						if($result_check == 1) {
							$emailDdata['emailName']= $findUserDetails->Name;
							$emailDdata['emailSubject'] = "We'd love to hear from you!";
							$emailDdata['emailPage']= 'email-templates.survey';
							$emailDdata['emailTo']= $findUserDetails->Email;
							EmailHelper::sendEmail($emailDdata);
						}
					}
					
					return 1;
				}else{
					return 0;
				}
			}else{
				return 0;
			}
		}
		
		public static function concludedAppointmentFromTransaction($clinicdata){
			$hostName = $_SERVER['HTTP_HOST'];
			$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
			$server = $protocol.$hostName;
			$allInputs = Input::all();
			StringHelper::Set_Default_Timezone();
			$currentDate = date('d-m-Y');
			
			$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
			$findUserAppointment = General_Library::FindUserAppointment($allInputs['appointment_id']);
			if($findClinicDetails && $findUserAppointment){
				$bookArray['Active'] = 1;
				$bookArray['Status'] = 2;
				
				$updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
				if($updateAppointment){
					$findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
					if($findClinicProcedure){
						$procedurename = $findClinicProcedure->Name;
					}else{
						$procedurename = null;
					}
					$findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
					$findDoctorDetails = Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
					
					// Send SMS
					if(StringHelper::Deployment()==1){
						// $smsMessage = "Hello ".$findUserDetails->Name.", thank you for using Mednefits, it was a pleasure serving you. Please feel free to get in touch with us on mednefits.com and share your experience. Get the free app at mednefits.com";
						$smsMessage = "Hi ".$findUserDetails->Name.", thank you for using Mednefits, it was a pleasure serving you. We hope you had a great health care experience from us. Please feel free to get in touch with us on happiness@mednefits.com and share your experience.";
						$sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
						$saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
					}
					
					// Email to User
					$formatDate = date('l, j F Y',$findUserAppointment->BookDate);
					$emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
					$emailDdata['remarks'] = $findUserAppointment->Remarks;
					$emailDdata['bookingTime'] = date('h:i A',$findUserAppointment->StartTime).' - '.date('h:i A',$findUserAppointment->EndTime);
					$emailDdata['bookingNo'] = 0;
					$emailDdata['bookingDate'] = $formatDate;
					$emailDdata['doctorName'] = $findDoctorDetails->Name;
					$emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
					$emailDdata['clinicName'] = $findClinicDetails->Name;
					$emailDdata['clinicPhoneCode'] = $findClinicDetails->Phone_Code;
					$emailDdata['clinicPhone'] = $findClinicDetails->Phone;
					$emailDdata['clinicAddress'] = $findClinicDetails->Address;
					$emailDdata['clinicProcedure'] = $procedurename;
					$emailDdata['emailName']= $findUserDetails->Name;
					$emailDdata['emailPage']= 'email-templates.e-receipt';
					// $emailDdata['emailPage']= 'email-templates.new-conclude';
					$emailDdata['emailTo']= $findUserDetails->Email;
					$emailDdata['emailSubject'] = 'Hope you had a great health care experience!';
					$emailDdata['server']	= $server.'/conclude/page/'.$findUserAppointment->UserAppoinmentID;
					// ereceipt
					$emailDdata['nric'] = $allInputs['nric'];
					$emailDdata['procedure'] = $allInputs['procedure'];
					$emailDdata['date'] = $allInputs['date'];
					$emailDdata['time'] = $allInputs['time'];
					$emailDdata['total_amount'] = $allInputs['total_amount'];
					$emailDdata['deducted'] = $allInputs['credit_deducted'];
					$emailDdata['final_bill'] = $allInputs['final_bill'];
					// check if first time to book
					$appointment_class = new UserAppoinment();
					// $result_check = $appointment_class->checkUserFirstTimeBook($findUserAppointment->UserID);
					
					if(StringHelper::Deployment()==1){
						EmailHelper::sendEmail($emailDdata);
						//         if($result_check == 1) {
							//         	$emailDdata['emailName']= $findUserDetails->Name;
							//         	$emailDdata['emailSubject'] = "We'd love to hear from you!";
							// 	$emailDdata['emailPage']= 'email-templates.survey';
							// 	$emailDdata['emailTo']= $findUserDetails->Email;
							// 	EmailHelper::sendEmail($emailDdata);
							// }
						}
						
						return 1;
					}else{
						return 0;
					}
				}else{
					return 0;
				}
			}
			
			/* ----- Used to No show appointment ------- */
			
			public static function NoShowAppointment($clinicdata){
				$allInputs = Input::all();
				StringHelper::Set_Default_Timezone();
				$currentDate = date('d-m-Y');
				
				$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
				$findUserAppointment = General_Library::FindUserAppointment($allInputs['appointment_id']);
				if($findClinicDetails && $findUserAppointment){
					$bookArray['Active'] = 1;
					$bookArray['Status'] = 4;
					
					$updateAppointment = General_Library::UpdateAppointment($bookArray,$findUserAppointment->UserAppoinmentID);
					if($updateAppointment){
						$findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
						if($findClinicProcedure){
							$procedurename = $findClinicProcedure->Name;
						}else{
							$procedurename = null;
						}
						$findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
						$findDoctorDetails = Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
						
						//Send SMS
						if(StringHelper::Deployment()==1){
							// $smsMessage = "Hello ".$findUserDetails->Name.", thank you for using medicloud, it was a pleasure serving you. We hope that the medicloud booking service was seamless, please feel free to get in touch with us on www.medicloud.sg and share your experience.";
							
							$smsMessage = "Hello ".$findUserDetails->Name.", we are sorry that you had missed your appointment with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name.", ".$findClinicDetails->Address.", Ph:".$findClinicDetails->Phone.".Please feel free to get in touch with us on happiness@mednefits.com and let us know if we can be of any assistance.";
							
							$sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
							$saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
						}
						
						//Email to User
						$formatDate = date('l, j F Y',$findUserAppointment->BookDate);
						$emailDdata['bookingid'] = $findUserAppointment->UserAppoinmentID;
						$emailDdata['remarks'] = $findUserAppointment->Remarks;
						$emailDdata['bookingTime'] = date('h:i A',$findUserAppointment->StartTime).' - '.date('h:i A',$findUserAppointment->EndTime);
						$emailDdata['bookingNo'] = 0;
						$emailDdata['bookingDate'] = $formatDate;
						$emailDdata['doctorName'] = $findDoctorDetails->Name;
						$emailDdata['doctorSpeciality'] = $findDoctorDetails->Specialty;
						$emailDdata['clinicName'] = $findClinicDetails->Name;
						$emailDdata['clinicAddress'] = $findClinicDetails->Address;
						$emailDdata['clinicProcedure'] = $procedurename;
						$emailDdata['emailName']= $findUserDetails->Name;
						$emailDdata['emailPage']= 'email-templates.booking-conclude';
						$emailDdata['emailTo']= $findUserDetails->Email;
						$emailDdata['emailSubject'] = 'Your booking is No Showed!';
						// EmailHelper::sendEmail($emailDdata);
						
						return 1;
					}else{
						return 0;
					}
				}else{
					return 0;
				}
				
			}
			
			// ..................................................................................................................................nhr
			
			
			public static function updateOnDrag($clinicdata)
			{
				$allInputs = Input::all();
				StringHelper::Set_Default_Timezone();
				$data = [];
				$date = $allInputs['date'];
				$stime = $allInputs['stime'];
				$etime = $allInputs['etime'];
				
				$date = date('d-m-Y',strtotime($allInputs['date']));
				
				$starttime = date('h:i A',strtotime($stime));
				$endtime = date('h:i A',strtotime($etime));
				$startTimeNow = strtotime($date.$starttime);
				$endTimeNow = strtotime($date.$endtime);
				
				$duration = ($endTimeNow-$startTimeNow)/60;
				$data['BookDate'] = strtotime($date);
				$data['StartTime'] = $startTimeNow;
				$data['EndTime'] = $endTimeNow;
				$data['Duration'] = $duration;
				if(isset($allInputs['doctor_id'])) {
					// $doctor_id = $allInputs['doctor_id'];
					$data["DoctorID"] = $allInputs['doctor_id'];
					// return $data["DoctorID"];
				}
				$booking = General_Library::UpdateAppointment($data,$allInputs['event_id']);
				if ($booking) {
					# code...
					$findClinicDetails = Clinic_Library::FindClinicDetails($clinicdata->Ref_ID);
					$findUserAppointment = General_Library::FindUserAppointment($allInputs['event_id']);
					$findUserDetails = Auth_Library::FindUserDetails($findUserAppointment->UserID);
					$findDoctorDetails = Doctor_Library::FindDoctorDetails($findUserAppointment->DoctorID);
					
					if(StringHelper::Deployment()==1){
						$smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is updated on ".$date." from ".date('h:i A',strtotime($stime))." to ".date('h:i A',strtotime($etime)).". Thank you for using Mednefits.";
						//$smsMessage = "Hello ".$findUserDetails->Name." your booking with ".$findDoctorDetails->Name." at ".$findClinicDetails->Name." is confirmed on ".$allInputs['bookdate'].". Thank you for using medicloud.";
						if(strlen($findUserDetails->PhoneNo) > 8) {
							// $sendSMS = StringHelper::SendOTPSMS($findUserDetails->PhoneNo,$smsMessage);
							$findPlusSign = substr($findUserDetails->PhoneNo, 0, 1);
			        if($findPlusSign == 0){
			            $PhoneOnly = $findUserDetails->PhoneCode.substr($findUserDetails->PhoneNo, 1);
			        }else{
			            $PhoneOnly = $findUserDetails->PhoneCode.$findUserDetails->PhoneNo;
			        }
							$data = array(
			        	'phone' => $PhoneOnly,
			        	'message'	=> $smsMessage
			        );
			        $sendSMS = SmsHelper::sendCommzSms($data);
							$saveSMS = StringHelper::saveSMSMLogs($clinicdata->Ref_ID, $findUserDetails->Name, $findUserDetails->PhoneCode, $findUserDetails->PhoneNo, $smsMessage);
						}
					}
					
					$findClinicProcedure = General_Library::FindClinicProcedure($findUserAppointment->ProcedureID);
					if($findClinicProcedure){
						$procedurename = $findClinicProcedure->Name;
					}else{
						$procedurename = null;
					}
					
					$formatDate = date('l, j F Y',strtotime(date('d-m-y')));
					$emailDdata['bookingid'] = $allInputs['event_id'];
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
					// return $booking;
					return $booking;
				}else{
					return 0;
				}
				
				
				
			}
			
			
			public static function updateOnBlockerDrag($clinicdata)
			{
				$allInputs = Input::all();
				StringHelper::Set_Default_Timezone();
				
				$date = $allInputs['date'];
				$stime = $allInputs['stime'];
				$etime = $allInputs['etime'];
				
				$date = date('d-m-Y',strtotime($allInputs['date']));
				
				$starttime = date('h:i A',strtotime($stime));
				$endtime = date('h:i A',strtotime($etime));
				$startTimeNow = strtotime($date.$starttime);
				$endTimeNow = strtotime($date.$endtime);
				
				
				$data['id'] = $allInputs['event_id'];
				$data['date'] = strtotime($date);
				$data['start_time'] = $startTimeNow;
				$data['end_time'] = $endTimeNow;
				
				$e = new ExtraEvents();
				$e->updateBreak($data);
				
				
			}
			
			// ...................................... config window functions ..................................................../
			
			
			
			public static function saveClinicDetails($clinicid){
				
				$input = Input::all();
				
				$data['clinicid'] = $clinicid;
				$data['Name'] = $input['clinicname'];
				if (isset($input['speciality'])) {
					$data['Clinic_Type'] = $input['speciality'];
				}
				$data['Phone'] = $input['mobile'];
				$data['Phone_Code'] = $input['Phonecode'];
				$data['active'] = 1;
				$data['configure'] = 1;
				$clinic = new Clinic();
				$clinic->UpdateClinicDetails($data);
			}
			
			
			// ------------------------------- Add Clinic working hours ------------------------------
			
			
			public static function addClinicManageTimeSlots($clinicid){
				
				$manageTime = new ManageTimes();
				$manageTimes = $manageTime->findClinicManageTime($clinicid);
				
				// add manage time
				if ($manageTimes) {
					$manage_time_id = $manageTimes[0]->ManageTimeID;
				} else {
					
					$data['party'] = 3;
					$data['partyid'] = $clinicid;
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
			
			
			
			public static function updateClinicWorkingHours($ClinicID){
				
				$input = Input::all();
				
				$time_to = $input['time_to'];
				$time_from = $input['time_from'];
				$day_name = $input['day_name'];
				$day_name = substr($day_name, 0, 3);
				
				$manageTime = new ManageTimes();
				$manageTimes = $manageTime->findClinicManageTime($ClinicID);
				
				$manage_time_id = $manageTimes[0]->ManageTimeID;
				$clinicTime = new ClinicTimes();
				$clinicTimes = $clinicTime->FindClinicActivetimesByDay($manage_time_id,$day_name);
				
				if ($clinicTimes) {
					
					$dataArray = array('clinictimeid' => $clinicTimes[0]->ClinicTimeID, 'StartTime'=>$time_from, 'Endtime'=>$time_to);
					
					$clinicTime->UpdateClinicTimes($dataArray);
				}
				
				
			}
			
			
			
			public static function FindDoctorProcedures($procedureid,$clinicid){
				$clinicprocedure = new DoctorProcedures();
				$findProcedures = $clinicprocedure->FindDoctorsByProcedure($procedureid,$clinicid);
				if($findProcedures){
					return $findProcedures;
				}else{
					return FALSE;
				}
			}
			
			
			public static function saveClinicService($clinicID)
			{
				$input = Input::all();
				
				$dataArray = array(
					'clinicid'=> $clinicID,
					'name'=> $input['name'],
					'description'=> "",
					'duration'=> $input['time'],
					'durationformat'=> "mins",
					'price'=> $input['cost'] );
					
					$procedure = new ClinicProcedures();
					$procedure->AddProcedures ($dataArray);
					
					return $procedure;
					
				}
				
				
				public static function addDoctorService($clinicID,$procedure){
					
					$input = Input::all();
					
					foreach ($input['doctorid'] as $key => $value) {
						
						$dataProcedure['clinicid'] = $clinicID;
						$dataProcedure['procedureid'] = $procedure;
						$dataProcedure['doctorid'] = $value;
						
						$addProcedure = Doctor_Library::AddDoctorProcedures($dataProcedure);
					}
					
					return $addProcedure;
					
				}
				
				public static function DeleteClinicService($clinicID){
					
					$input = Input::all();
					$id = $input['id'];
					
					$dataArray = array(
						'procedureid'=> $id,
						'active'=> 0
					);
					
					$proc = new ClinicProcedures();
					$proc->UpdateProcedure ($dataArray);
					
					if ( $proc){
						return 1;
					}
					else {
						return 0;
					}
				}
				
				
				public static function getClinicDetails($clinicid)
				{
					$clinic = new Admin_Clinic();
					$data = $clinic->getClinicdata($clinicid);
					// dd($data);
					// return $data;
					$arr['default_view'] = $data[0]->Calendar_type;
					$arr['first_day'] = $data[0]->Calendar_day;
					$arr['slot_duration'] = $data[0]->Calendar_duration;
					$arr['start_hour'] = date("H:i:s", strtotime($data[0]->Calendar_Start_Hour));
					
					return $arr;
					
				}
				
				
				public static function validatePin($Ref_ID)
				{
					$input = Input::all();
					$pin = $input['pin'];
					$staff = new Staff();
					$staffData = $staff->getStaffByPin($Ref_ID, $pin);
					
					$doc = new Doctor();
					$docData = $doc->getDoctorByPin($Ref_ID, $pin);
					
					
					
					if ($staffData || $docData) {
						return 1;
					} else {
						return 0;
					}
					
					
					
				}
				
				//get clinic pin status
				public static function getClinicPinStatus($clinicid)
				{
					$c = new Clinic();
					$data = $c->ClinicDetails($clinicid);
					
					return $data->Require_pin;
				}
				
				// ``````````````````````````````add working hours in tab load````````````````````````````````````````````
				
				public static function addDoctorManageTimes($clinicid,$doctorid)
				{
					$input = Input::all();
					$doctor_id = $doctorid;
					
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
					$doctorTime = $clinicTime->FindClinicActivetimesNew($manage_time_id);
					// dd(count($doctorTime));
					
					if (!$doctorTime) {
						
						$clinicTimes = General_Library::FindAllClinicTimesNew(3,$clinicid,strtotime(date('d-m-Y')));
						// dd($clinicTimes);
						
						if ($clinicTimes) {
							
							foreach ($clinicTimes as $value) {
								
								if($value->Mon==1){
									
									$data['managetimeid'] = $manage_time_id;
									$data['starttime'] = $value->StartTime;
									$data['endtime'] = $value->EndTime;
									$data['wemon'] = 1;
									$data['wetus'] = 0;
									$data['wewed'] = 0;
									$data['wethu'] = 0;
									$data['wefri'] = 0;
									$data['wesat'] = 0;
									$data['wesun'] = 0;
									$data['status'] = $value->Active;
									$clinicTime = new ClinicTimes();
									$clinicTime->AddDorctorTimes ($data);
								}
								
								elseif($value->Tue==1){
									
									$data1['managetimeid'] = $manage_time_id;
									$data1['starttime'] = $value->StartTime;
									$data1['endtime'] = $value->EndTime;
									$data1['wemon'] = 0;
									$data1['wetus'] = 1;
									$data1['wewed'] = 0;
									$data1['wethu'] = 0;
									$data1['wefri'] = 0;
									$data1['wesat'] = 0;
									$data1['wesun'] = 0;
									$data1['status'] = $value->Active;
									$clinicTime = new ClinicTimes();
									$clinicTime->AddDorctorTimes ($data1);
								}
								
								elseif($value->Wed==1){
									
									$data2['managetimeid'] = $manage_time_id;
									$data2['starttime'] = $value->StartTime;
									$data2['endtime'] = $value->EndTime;
									$data2['wemon'] = 0;
									$data2['wetus'] = 0;
									$data2['wewed'] = 1;
									$data2['wethu'] = 0;
									$data2['wefri'] = 0;
									$data2['wesat'] = 0;
									$data2['wesun'] = 0;
									$data2['status'] = $value->Active;
									$clinicTime = new ClinicTimes();
									$clinicTime->AddDorctorTimes ($data2);
								}
								
								elseif($value->Thu==1){
									
									$data3['managetimeid'] = $manage_time_id;
									$data3['starttime'] = $value->StartTime;
									$data3['endtime'] = $value->EndTime;
									$data3['wemon'] = 0;
									$data3['wetus'] = 0;
									$data3['wewed'] = 0;
									$data3['wethu'] = 1;
									$data3['wefri'] = 0;
									$data3['wesat'] = 0;
									$data3['wesun'] = 0;
									$data3['status'] = $value->Active;
									$clinicTime = new ClinicTimes();
									$clinicTime->AddDorctorTimes ($data3);
								}
								
								elseif($value->Fri==1){
									
									$data4['managetimeid'] = $manage_time_id;
									$data4['starttime'] = $value->StartTime;
									$data4['endtime'] = $value->EndTime;
									$data4['wemon'] = 0;
									$data4['wetus'] = 0;
									$data4['wewed'] = 0;
									$data4['wethu'] = 0;
									$data4['wefri'] = 1;
									$data4['wesat'] = 0;
									$data4['wesun'] = 0;
									$data4['status'] = $value->Active;
									$clinicTime = new ClinicTimes();
									$clinicTime->AddDorctorTimes ($data4);
								}
								
								elseif($value->Sat==1){
									
									$data5['managetimeid'] = $manage_time_id;
									$data5['starttime'] = $value->StartTime;
									$data5['endtime'] = $value->EndTime;
									$data5['wemon'] = 0;
									$data5['wetus'] = 0;
									$data5['wewed'] = 0;
									$data5['wethu'] = 0;
									$data5['wefri'] = 0;
									$data5['wesat'] = 1;
									$data5['wesun'] = 0;
									$data5['status'] = $value->Active;
									$clinicTime = new ClinicTimes();
									$clinicTime->AddDorctorTimes ($data5);
								}
								
								elseif($value->Sun==1){
									
									$data6['managetimeid'] = $manage_time_id;
									$data6['starttime'] = $value->StartTime;
									$data6['endtime'] = $value->EndTime;
									$data6['wemon'] = 0;
									$data6['wetus'] = 0;
									$data6['wewed'] = 0;
									$data6['wethu'] = 0;
									$data6['wefri'] = 0;
									$data6['wesat'] = 0;
									$data6['wesun'] = 1;
									$data6['status'] = $value->Active;
									$clinicTime = new ClinicTimes();
									$clinicTime->AddDorctorTimes ($data6);
								}
								
								
							}
						}
						
						
						
						
						
						
					}
					
					
				}
				
				
			} //end of class
			