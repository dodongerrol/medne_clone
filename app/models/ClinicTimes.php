<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ClinicTimes extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic_time';

        public function AddClinicTimes ($dataArray){
            $this->ManageTimeID = $dataArray['managetimeid'];
            $this->Party = null;
            $this->ClinicID = 0;
            $this->DoctorID = 0;
            $this->StartTime = $dataArray['starttime'];
            $this->EndTime = $dataArray['endtime'];
            $this->Mon = $dataArray['wemon'];
            $this->Tue = $dataArray['wetus'];
            $this->Wed = $dataArray['wewed'];
            $this->Thu = $dataArray['wethu'];
            $this->Fri = $dataArray['wefri'];
            $this->Sat = $dataArray['wesat'];
            $this->Sun = $dataArray['wesun'];
            $this->Created_on = time();
            $this->created_at = time();
            $this->updated_at = 0;
            $this->Active = 0;

            if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }
        }
        /*
            Add doctor defualt times
        */

        public function AddDorctorTimes ($dataArray){
            $this->ManageTimeID = $dataArray['managetimeid'];
            $this->Party = null;
            $this->ClinicID = 0;
            $this->DoctorID = 0;
            $this->StartTime = $dataArray['starttime'];
            $this->EndTime = $dataArray['endtime'];
            $this->Mon = $dataArray['wemon'];
            $this->Tue = $dataArray['wetus'];
            $this->Wed = $dataArray['wewed'];
            $this->Thu = $dataArray['wethu'];
            $this->Fri = $dataArray['wefri'];
            $this->Sat = $dataArray['wesat'];
            $this->Sun = $dataArray['wesun'];
            $this->Created_on = time();
            $this->created_at = time();
            $this->updated_at = 0;
            $this->Active = $dataArray['status'];

            if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }
        }


        public function FindClinicTimesStatus($clinicid,$week){
            $allData = DB::table('clinic_time')
                ->where('ClinicID', '=', $clinicid)
                ->where('Active', '=', 1)
                ->where($week, '=', 1)
                ->get();
            return $allData;
        }

        public function FindClinicTimes($clinicid){
            $allData = DB::table('clinic_time')
                ->where('ClinicID', '=', $clinicid)
                ->where('Active', '=', 1)
                ->get();
            return $allData;
        }

        public function UpdateClinicTimes ($dataArray){
            $allData = DB::table('clinic_time')
                ->where('ClinicTimeID', '=', $dataArray['clinictimeid'])
                ->update($dataArray);

            return $allData;
        }

        public function FindClinicActivetimes($managetimeid){
            $allData = DB::table('clinic_time')
                ->where('ManageTimeID', '=', $managetimeid)
                ->where('Active', '=', 1)
                ->get();
            return $allData;
        }
// nhr
         public function FindClinicActivetimesNew($managetimeid){
            $allData = DB::table('clinic_time')
                ->where('ManageTimeID', '=', $managetimeid)
                // ->where('Active', '=', 1)
                ->get();
            return $allData;
        }


        public function FindClinicActivetimesByDay($managetimeid,$dayname){
            $allData = DB::table('clinic_time')
                ->where('ManageTimeID', '=', $managetimeid)
                ->where(''.$dayname.'', '=', 1)
                ->where('Active', '=', 1)
                ->get();
            return $allData;
        }

        public function deleteClinicActivetimes($managetimeid)
        {
            DB::table('clinic_time')
            ->where('ManageTimeID', '=', $managetimeid)
            ->delete();
        }



        // nhr......2016/5/18

        public function findCurentClinicStatus($week,$clinicid)
        {
            $allData = DB::table('clinic_time')
                        ->select('clinic_time.*')
                        ->join('manage_times', 'clinic_time.ManageTimeID', '=', 'manage_times.ManageTimeID')
                        ->where(''.$week.'', '=', 1)
                        ->where('manage_times.party', '=', 3)
                        ->where('manage_times.PartyID', '=', $clinicid)
                        ->get();

             return $allData;
        }
}
