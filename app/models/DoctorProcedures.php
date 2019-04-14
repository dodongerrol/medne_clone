<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DoctorProcedures extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor_procedure';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        
	//protected $hidden = array('password', 'remember_token');
        
        public function FindDoctorsByProcedure($procedureid, $clinicid){
            $procedures = DB::table('doctor_procedure')
                ->join('doctor', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                ->join('clinic', 'clinic.ClinicID', '=', 'doctor_procedure.ClinicID')
                //->join('clinic_procedure', 'doctor_procedure.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                ->select('doctor_procedure.DoctorProcedureID','doctor_procedure.ProcedureID',
                   'doctor.DoctorID','doctor.Name as DocName','doctor.Qualifications','doctor.Specialty','doctor.image as DocImage','doctor.Phone as DocPhone', 'doctor.Email', 'doctor.phone_code as DocPhoneCode', 'clinic.Phone as CliPhone', 'clinic.Phone_Code as CliPhoneCode')
                ->where('doctor_procedure.ProcedureID', '=', $procedureid)
                ->where('doctor_procedure.ClinicID', '=', $clinicid)
                ->where('doctor_procedure.Active', '=', 1)
                ->where('doctor.Active', '=', 1) 
                ->get();
            
            return $procedures;
        }
        public function DoctorsProcedureList($doctorid){
            $procedures = DB::table('doctor_procedure')
                ->join('clinic_procedure', 'doctor_procedure.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                ->select('doctor_procedure.DoctorProcedureID',
                   'clinic_procedure.ProcedureID','clinic_procedure.ClinicID','clinic_procedure.Name','clinic_procedure.Duration','clinic_procedure.Duration_Format','clinic_procedure.Price')

                ->where('doctor_procedure.DoctorID', '=', $doctorid)
                ->where('clinic_procedure.Active', '=', 1)
                ->where('doctor_procedure.Active', '=', 1)
                ->get();
            
            return $procedures;
        }
        public function FindClinicDoctorProcedures($doctorid,$procedureid){
            $procedures = DB::table('doctor_procedure')
                    ->join('clinic_procedure', 'doctor_procedure.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                    ->select('doctor_procedure.DoctorProcedureID',
                       'clinic_procedure.ProcedureID','clinic_procedure.Name','clinic_procedure.Duration','clinic_procedure.Price','clinic_procedure.Duration_Format')
                    ->where('doctor_procedure.ProcedureID', '=', $procedureid)
                    //->where('doctor_procedure.ClinicID', '=', $clinicid)
                    ->where('doctor_procedure.DoctorID', '=', $doctorid)
                    ->where('doctor_procedure.Active', '=', 1)
                    ->where('clinic_procedure.Active', '=', 1)
                     ->first();
            
            return $procedures;
        }

         public function compareDoctorProcedures($doctorid,$procedureid){
            $procedures = DB::table('doctor_procedure')
                    ->join('clinic_procedure', 'doctor_procedure.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                    ->select('doctor_procedure.DoctorProcedureID',
                       'clinic_procedure.ProcedureID','clinic_procedure.Name','clinic_procedure.Duration','clinic_procedure.Price','clinic_procedure.Duration_Format')
                    ->where('doctor_procedure.ProcedureID', '=', $procedureid)
                    //->where('doctor_procedure.ClinicID', '=', $clinicid)
                    ->where('doctor_procedure.DoctorID', '=', $doctorid)
                    ->where('doctor_procedure.Active', '=', 1)
                    ->where('clinic_procedure.Active', '=', 1)
                     ->count();
            
            return $procedures;
        }
        
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        //                              WEB                                   //
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        
        
        
        public function AddDoctorProcedures ($dataArray){
           
            $this->ProcedureID = $dataArray['procedureid'];
            $this->ClinicID = $dataArray['clinicid'];    
            $this->DoctorID = $dataArray['doctorid'];
            $this->Created_on = time();
            $this->created_at = time();
            $this->updated_at = 0;
            $this->Active = 1; 
			
            if($this->save()){
                $insertedId = $this->id; 
                return $insertedId;
            }else{
                return false;
            }     
        }
        public function FindDoctorProcedures($clinicid,$doctorid){
            $procedures = DB::table('doctor_procedure')
                    ->join('clinic_procedure', 'doctor_procedure.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                    ->select('doctor_procedure.DoctorProcedureID',
                       'clinic_procedure.ProcedureID','clinic_procedure.Name','clinic_procedure.Duration','clinic_procedure.Price','clinic_procedure.Duration_Format')
                    ->where('doctor_procedure.ClinicID', '=', $clinicid)
                    ->where('doctor_procedure.DoctorID', '=', $doctorid)
                    ->where('doctor_procedure.Active', '=', 1)
                    ->where('clinic_procedure.Active', '=', 1)
                     ->get();
            
            return $procedures;
        }
        
        
        public function UpdateProcedure($dataArray,$docprocedureid){
            $allData = DB::table('doctor_procedure')
                ->where('DoctorProcedureID', '=', $docprocedureid)
                ->update($dataArray);
            
            return $allData;
        }
        public function FindSingleProcedures($clinicid,$doctorid,$procedureid){
            $procedures = DB::table('doctor_procedure')
                    ->join('clinic_procedure', 'doctor_procedure.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                    ->select('doctor_procedure.DoctorProcedureID',
                       'clinic_procedure.ProcedureID','clinic_procedure.Name','clinic_procedure.Duration','clinic_procedure.Price','clinic_procedure.Duration_Format')
                    ->where('doctor_procedure.ProcedureID', '=', $procedureid)
                    ->where('doctor_procedure.ClinicID', '=', $clinicid)
                    ->where('doctor_procedure.DoctorID', '=', $doctorid)
                    // ->where('doctor_procedure.Active', '=', 0)
                    ->where('clinic_procedure.Active', '=', 1)
                    ->first();
            
            return $procedures;
        }
        
        public function FindClinicFromProcedure($doctorid,$procedureid){
            $procedures = DB::table('doctor_procedure')
                ->join('clinic', 'doctor_procedure.ClinicID', '=', 'clinic.ClinicID')
                ->join('doctor', 'doctor_procedure.DoctorID', '=', 'doctor.DoctorID')
                ->join('clinic_procedure', 'doctor_procedure.ProcedureID', '=', 'clinic_procedure.ProcedureID')    
                ->select('clinic.ClinicID','clinic.Name as CliName','clinic.Phone as CliPhone','clinic.Address as CliAddress',
                   'doctor.DoctorID','doctor.Name as DocName','doctor.Email as DocEmail','doctor.Specialty','doctor.Qualifications',
                        'clinic_procedure.Name as ProName')

                ->where('doctor_procedure.DoctorID', '=', $doctorid)
                ->where('doctor_procedure.ProcedureID', '=', $procedureid)
                ->where('doctor_procedure.Active', '=', 1)
                ->where('clinic.Active', '=', 1)    
                ->where('doctor.Active', '=', 1)    
                ->first();
            
            return $procedures;
        }
        
//        public function UpdateProcedure ($dataArray){         
//            $allData = DB::table('clinic_procedure')
//                ->where('ProcedureID', '=', $dataArray['procedureid'])
//                ->update($dataArray);
//            
//            return $allData;
//        }
//        
//        
//        public function GetClinicProcedure($procedureid){
//            $procedures = DB::table('clinic_procedure')
//                    ->where('ProcedureID', '=', $procedureid)
//                    ->where('Active', '=', 1)
//                     ->first();
//            
//            return $procedures;
//        }
       

}
