<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ClinicProcedures extends Eloquent {


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'clinic_procedure';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */


    //protected $hidden = array('password', 'remember_token');

        public function ClinicProcedureByID($procedureid){
            $procedures = DB::table('clinic_procedure')
                    ->where('ProcedureID', '=', $procedureid)
                    ->where('Active', '=', 1)
                     ->first();

            return $procedures;
        }



        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        //                              WEB                                   //
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//

        // rearrange service position
        public function rearrangeServicePosition($data) {
            $temp = array();
            for($x = 0; $x < sizeof($data); $x++) {
                array_push($temp,
                    DB::table('clinic_procedure')
                    ->where('ProcedureID', '=', $data[$x]['id'])
                    ->update(['Position' => $data[$x]['position']]));
            }

            return $temp;
        }

        public function AddProcedures ($dataArray){

            $this->ClinicID = $dataArray['clinicid'];
            $this->Name = $dataArray['name'];
            $this->Description = $dataArray['description'];
            $this->Duration = $dataArray['duration'];
            $this->Duration_Format = $dataArray['durationformat'];
            $this->Price = $dataArray['price'];
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

        public function UpdateProcedure ($dataArray){
            $allData = DB::table('clinic_procedure')
                ->where('ProcedureID', '=', $dataArray['procedureid'])
                ->update($dataArray);

            return $allData;
        }

        public function FindClinicProcedures($clinicid){
            $procedures = DB::table('clinic_procedure')
                    ->where('ClinicID', '=', $clinicid)
                    ->where('Active', '=', 1)
                    ->orderBy('Position', 'asc')
                    ->get();

            return $procedures;
        }
        public function GetClinicProcedure($procedureid){
            $procedures = DB::table('clinic_procedure')
                    ->where('ProcedureID', '=', $procedureid)
                    ->where('Active', '=', 1)
                     ->first();

            return $procedures;
        }

// nhr
        public function GetClinicProcedureByTime($duration){
            $procedures = DB::table('clinic_procedure')
                    ->where('Duration', '=', $duration)
                    ->where('Active', '=', 1)
                     ->first();

            return $procedures;
        }


    public function GetClinicProcedureTime(){
            $procedures = DB::table('clinic_procedure')
                    ->select('ProcedureID')
                    ->where('Active', '=', 1)
                    // ->groupBy('Duration')
                    // ->orderBy('Duration','Desc')
                    ->get();

            return $procedures;
        }
        public function ClinicProcedureBoth($procedureid){
            $procedures = DB::table('clinic_procedure')
                    ->where('ProcedureID', '=', $procedureid)
                    //->where('Active', '=', 1)
                     ->first();

            return $procedures;
        }



        // nhr main searach functions for mobile

     public function getProcedure($search)
        {
            // $Data = DB::table('clinic_procedure')
            //     ->select('ProcedureID as procedureid', 'Name as name')
            //     ->where('Name', 'like binary', "%$search%")
            //     ->where('Active', '=', 1)
            //     ->get();

             $results = DB::select("SELECT ProcedureID as procedureid , Name as name FROM medi_clinic_procedure WHERE Name like binary ?  and Active=? group By Name", array("%$search%",1));

            //  DB::enableQuerylog();
            // dd(DB::getQueryLog());
            return $results;

                // return $Data;
        }

}
