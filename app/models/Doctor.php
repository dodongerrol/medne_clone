<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Doctor extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'doctor';

	/**
	 * The attributes excluded from the model's JSON form.
	 * 
	 * @var array
	 */
         
         
	//protected $hidden = array('password', 'remember_token');       
        
        //Add new doctor
        public function insertDoctor ($dataArray){
                $this->Name = $dataArray['name'];
                $this->Email = $dataArray['email'];
                $this->Description = null;
                $this->Qualifications = $dataArray['qualification'];
                $this->Specialty = $dataArray['speciality'];
                $this->Availability = null;
                $this->image = 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428405297/is9qvklrjvkmts1pvq8r.png';
                $this->Phone = $dataArray['mobile'];
                $this->Emergency = $dataArray['mobile'];
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
        public function NewDoctor ($dataArray){
                $this->Name = $dataArray['name'];
                $this->Email = $dataArray['email'];
                //$this->Description = null;
                $this->Qualifications = $dataArray['qualification'];
                $this->Specialty = $dataArray['speciality'];
                //$this->Availability = null;
                $this->image = $dataArray['image'];
                $this->Code = $dataArray['code'];
                $this->Emergency_Code = $dataArray['emergency_code'];
                $this->Phone = $dataArray['phone'];
                $this->Emergency = $dataArray['emergency_phone'];
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
        
        public function updateDoctor ($dataArray){
            
            $allData = DB::table('doctor')
                ->where('DoctorID', '=', $dataArray['doctorid'])
                ->update($dataArray);
            
            return $allData;
        }
        // nhr 2016-1-29
        public function updateDoctorByGmail($dataArray){
            // dd($dataArray);
            $allData = DB::table('doctor')
                ->where('gmail', '=', $dataArray['gmail'])
                ->update($dataArray);
            
            return $allData;
        }

        public function findUniqueGmail($gmail){
            $doctorData = DB::table('doctor')
                ->where('gmail', '=', $gmail)
                ->first();
               
            return $doctorData;                
        }
        
        
        // Find a doctor details
        public function doctorDetails($value){
            $doctorData = DB::table('doctor')
                ->where('DoctorID', '=', $value)
                ->where('Active', '=', 1)    
                ->first();
                if($doctorData){
                    return $doctorData;
                }else{
                    return false;
                }    
        }

        public function ClinicDoctors($value){
            $doctorData = DB::table('doctor')
                ->where('DoctorID', '=', $value)
                ->first();
                if($doctorData){
                    return $doctorData;
                }else{
                    return false;
                }    
        }
        /* Use          :   used to find existing doctor by Email
         * Parameter    :   Doctor email
         * Return       :   Doctor details
         * ................................
         * Author       :   Rizvi
         */
        public function findDoctorByEmail($email){
            $doctorData = DB::table('doctor')
                ->where('Email', '=', $email)
                ->first();
               
            return $doctorData;                
        }

       
        
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
    //                              WEB                                   //
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//

        
    public function FindDoctor($doctorid){
        $doctorData = DB::table('doctor')
            ->where('DoctorID', '=', $doctorid)
            ->first();
                
        return $doctorData;  
    }    
    
    public function FindDoctorDetails($value){
        $doctorData = DB::table('doctor')
            ->where('DoctorID', '=', $value)
            ->where('Active', '=', 1)    
            ->first();
            return $doctorData;   
    }






    #####################################################nhr  25-04-2016 ##################################

    public function addDoctor($dataArray){

        $this->Name     = $dataArray['name'];
        $this->Email        = $dataArray['email'];
        $this->Active       = 1;
        if($this->save()){
            $insertedId = $this->id;
            return $insertedId;
        }else{
            return false;
        } 
    }

    ///main search for mobile

    public function getdoctors($search)
    {
         $doctorData = DB::table('doctor')
            ->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
            ->where('doctor.Name', 'like', "%{$search}%")
            ->where('doctor.Active', '=', 1)
            ->get();

            // DB::enableQuerylog();
            // dd(DB::getQueryLog());
            return $doctorData; 
    }

    public function getDoctorsCurrency($search, $user_id)
    {
        $wallet = DB::table('e_wallet')->where('UserID', $user_id)->first();

        if($wallet->currency_type == "myr") {
            $doctorData = DB::table('doctor')
            ->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
            ->join('clinic', 'clinic.ClinicID', '=', 'doctor_availability.ClinicID')
            ->where('doctor.Name', 'like', "%{$search}%")
            ->where('doctor.Active', '=', 1)
            ->where('clinic.currency_type', 'myr')
            ->get();
        } else {
            $doctorData = DB::table('doctor')
            ->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
            ->where('doctor.Name', 'like', "%{$search}%")
            ->where('doctor.Active', '=', 1)
            ->get();
        }
        return $doctorData; 
    }

    public function getDoctorByProcedure($key)
    {
        $doctorData = DB::table('doctor')
            ->select('doctor_procedure.ClinicID','doctor.Name','doctor.Qualifications','doctor.Specialty','doctor.Phone')
            ->join('doctor_procedure','doctor_procedure.DoctorID','=','doctor.DoctorID')
            ->join('clinic_procedure','doctor_procedure.ProcedureID','=','clinic_procedure.ProcedureID')
            ->where('clinic_procedure.Name', '=', $key)
            ->where('doctor.Active', '=', 1)
            ->groupBy('doctor.Name')    
            ->get();
            // DB::enableQuerylog();
            // dd(DB::getQueryLog());
            return $doctorData; 
    }

    public function getDoctorByType($key)
    {
        $doctorData = DB::table('doctor')
            ->select('clinic.ClinicID','doctor.Name','doctor.Qualifications','doctor.Specialty','doctor.Phone')
            ->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
            ->join('clinic','clinic.ClinicID','=','doctor_availability.ClinicID')
            ->where('clinic.Clinic_Type', '=', $key)
            ->where('doctor.Active', '=', 1)    
            ->get();
           // DB::enableQuerylog();
           //  dd(DB::getQueryLog());
            return $doctorData; 
    }

    public function getDoctorByDistrict($key)
    {
        $doctorData = DB::table('doctor')
            ->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
            ->join('clinic','clinic.ClinicID','=','doctor_availability.ClinicID')
            ->where('clinic.District', '=', $key)
            ->where('clinic.Active', '=', 1)
            ->where('doctor.Active', '=', 1)
            ->groupBy('clinic.ClinicID')  
            ->get();
           
            return $doctorData; 
    }

    public function getDoctorByMrt($key)
    {
        $doctorData = DB::table('doctor')
            ->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
            ->join('clinic','clinic.ClinicID','=','doctor_availability.ClinicID')
            ->where('clinic.Mrt', '=', $key)
            ->where('clinic.Active', '=', 1)
            ->where('doctor.Active', '=', 1)  
            ->groupBy('clinic.ClinicID')  
            ->get();
           
            return $doctorData;
    }



     public function getDoctorByPin($clinicID,$pin)
    {
        $doctorData = DB::table('doctor')
            ->join('doctor_availability','doctor_availability.DoctorID','=','doctor.DoctorID')
            ->where('doctor_availability.ClinicID', '=', $clinicID)
            ->where('doctor.pin', '=', $pin)
            // ->where('doctor.check_pin', '=', 1)
            ->where('doctor.Active', '=', 1)    
            ->get();
           
            return $doctorData; 
    }

    public function getGoogleCodeLink($code)
    {
        return Doctor::where('google_link_code', $code)->first();
    }


}
