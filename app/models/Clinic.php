<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Clinic extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'clinic';
    public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        
        
	//protected $hidden = array('password', 'remember_token');

    public function updateClinic($id, $data) {
        return Clinic::where('ClinicID', $id)->update($data);
    }

    public function resetClinic( )
    {
        return DB::table('clinic')->update(['medicloud_transaction_fees' => 0]);
    }

        public function search ($search){
            $clinicData = DB::table('clinic')    
                    ->select('clinic.ClinicID','clinic.Name as CLName','clinic.Clinic_Type','clinic.Description','clinic.Custom_title','clinic.Website','clinic.image as CLImage','clinic.Address as CLAddress','clinic.State as CLState','clinic.City as CLCity','clinic.Postal as CLPostal','clinic.Phone','clinic.Lat as CLLat','clinic.Lng as CLLng','clinic.Clinic_Price','clinic.Favourite', 'clinic.Phone_Code')
                    ->where('Active', '=', 1)
                    //->where('Name', 'LIKE', "%{$search}%")
                    //->orWhere('District', 'LIKE', "%{$search}%")
                    //->orWhere('MRT', 'LIKE', "%{$search}%")
                    
                    ->where(function ($clinicData) use ($search) {
                        $clinicData->where('Name', 'LIKE', "%{$search}%")
                        ->orWhere('District', 'LIKE', "%{$search}%")
                        ->orWhere('MRT', 'LIKE', "%{$search}%");
                    })
                    
                    
                    
                    ->get();
                    
                    /*
                    ->where('Name', 'LIKE', "%{$search}%")
                    ->where('Active', '=', 1)
                    ->orWhere(function($query)
                    {
                        $query->where('District', 'LIKE', "%{$search}%");
                             // ->where('Active', '=', 1);
                    })
                    */
                    
             
            if($clinicData){
                return $clinicData;
            }else{
                return false;
            } 
        }
        
        
        public function ClinicDetails($value){
            $clinicData = DB::table('clinic')
                ->where('ClinicID', '=', $value)
                ->where('Active', '=', 1)    
                ->first();
            
                if($clinicData){
                    return $clinicData;
                }else{
                    return false;
                }    
        }

        public function ClinicDetail($id)
        {
            return Clinic::where('ClinicID', $id)->first();
        }
        
        public function Nearby1($lat,$lng,$radius,$getType){       
            $clinicData = DB::table('clinic')
                ->join('clinic_types_detail', 'clinic.ClinicID', '=', 'clinic_types_detail.ClinicID')
                ->join('clinic_types', 'clinic_types_detail.ClinicTypeID', '=', 'clinic_types.ClinicTypeID')    
                ->select('clinic.ClinicID','clinic.Name','clinic.Address','clinic.image','clinic.Lat','clinic.Lng','clinic.Phone','clinic.Opening','clinic.Clinic_Price',
                        'clinic_types.ClinicTypeID','clinic_types.Name as ClinicType',
                    DB::raw("
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("clinic.Active","=",1)    
                ->where("clinic_types.Active","=",1)  
                ->where("clinic_types_detail.Active","=",1)
                ->where("clinic_types_detail.ClinicTypeID","=",$getType)                      
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->get();
    
            return $clinicData;            
        }
        public function Nearby_bkp($lat,$lng,$radius,$getType){       
            $clinicData = DB::table('clinic')
                ->join('clinic_types_detail', 'clinic.ClinicID', '=', 'clinic_types_detail.ClinicID')
                ->join('clinic_types', 'clinic_types_detail.ClinicTypeID', '=', 'clinic_types.ClinicTypeID')    
                ->select('clinic.ClinicID','clinic.Name as CLName','clinic.Clinic_Type','clinic.Description','clinic.Custom_title','clinic.Website','clinic.image as CLImage','clinic.Address as CLAddress','clinic.State as CLState','clinic.City as CLCity','clinic.Postal as CLPostal','clinic.Phone','clinic.Lat as CLLat','clinic.Lng as CLLng','clinic.Clinic_Price',
                        'clinic_types.ClinicTypeID','clinic_types.Name as ClinicType',
                    DB::raw("
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("clinic.Active","=",1)    
                ->where("clinic_types.Active","=",1)  
                ->where("clinic_types_detail.Active","=",1)
                ->where("clinic_types_detail.ClinicTypeID","=",$getType)                      
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->get();
    
            return $clinicData;            
        }
        
        public function newestNearby($lat,$lng,$radius,$getType,$page)
        {
            $offset = 10 * ($page - 1);
            $clinic_types = new ClinicTypes();
            $clinic_ids = [];
            $nearby_results = [];
            array_push($clinic_ids, (int)$getType);
            $results = $clinic_types->getSubClinics($getType);
            if(sizeof($results) > 0) {
                foreach ($results as $key => $value) {
                    array_push($clinic_ids, $value->ClinicTypeID);
                }
            }

            $clinicData = DB::table('clinic')
                ->join('clinic_types', 'clinic.Clinic_type', '=', 'clinic_types.ClinicTypeID')    
                ->select('clinic.ClinicID','clinic.Name as CLName','clinic.Clinic_Type','clinic.Description','clinic.Custom_title','clinic.Website','clinic.image as CLImage','clinic.Address as CLAddress','clinic.State as CLState','clinic.City as CLCity','clinic.Postal as CLPostal','clinic.Phone','clinic.Lat as CLLat','clinic.Lng as CLLng','clinic.Clinic_Price',
                        'clinic_types.ClinicTypeID','clinic_types.Name as ClinicType','clinic.position',
                    DB::raw("
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("clinic.Active","=",1)    
                ->where("clinic_types.Active","=",1)                 
                ->whereIn("clinic_types.ClinicTypeID", $clinic_ids)  
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->get();

            $perPage = 10;
            $totalItems = count($clinicData);
            $totalPages = ceil($totalItems / $perPage);

            if ($page > $totalPages or $page < 1) {
                $page = 1;
            }

            $offset = ($page * $perPage) - $perPage;

            $clinics = array_slice($clinicData, $offset, $perPage);

            $clinics = Paginator::make($clinics, $totalItems, $perPage);
            return $clinics;
        }

        public function NewNearby($lat,$lng,$radius,$getType,$page)
        {  
            // return $page;
            $offset = 10 * ($page - 1);
            $clinic_types = new ClinicTypes();
            $clinic_ids = [];
            $nearby_results = [];
            array_push($clinic_ids, (int)$getType);
            $results = $clinic_types->getSubClinics($getType);
            if(sizeof($results) > 0) {
                foreach ($results as $key => $value) {
                    array_push($clinic_ids, $value->ClinicTypeID);
                }
            }
            $clinicData = DB::table('clinic')
                ->join('clinic_types', 'clinic.Clinic_type', '=', 'clinic_types.ClinicTypeID')    
                ->select(DB::raw("
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("clinic.Active","=",1)    
                ->where("clinic_types.Active","=",1)                
                ->whereIn("clinic_types.ClinicTypeID", $clinic_ids)  
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->get();

            $clinicDataReal = DB::table('clinic')
                ->join('clinic_types', 'clinic.Clinic_type', '=', 'clinic_types.ClinicTypeID')    
                ->select('clinic.ClinicID','clinic.Name as CLName','clinic.Clinic_Type','clinic.Description','clinic.Custom_title','clinic.Website','clinic.image as CLImage','clinic.Address as CLAddress','clinic.State as CLState','clinic.City as CLCity','clinic.Postal as CLPostal','clinic.Phone','clinic.Lat as CLLat','clinic.Lng as CLLng','clinic.Clinic_Price',
                        'clinic_types.ClinicTypeID','clinic_types.Name as ClinicType','clinic.position',
                    DB::raw("
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("clinic.Active","=",1)    
                ->where("clinic_types.Active","=",1)                
                ->whereIn("clinic_types.ClinicTypeID", $clinic_ids)  
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->skip($offset)
                ->take(10)
                ->get();

            $clinicDataReal = ClinicHelper::removeBlockClinicsFromPaginate($clinicDataReal);
            $paginator = Paginator::make($clinicDataReal, sizeof($clinicData), 10);
            // make pagination
            return $paginator;            
        }    
        
        public function Nearby($lat,$lng,$radius,$getType)
        {   
            $clinic_types = new ClinicTypes();
            $clinic_ids = [];
            $nearby_results = [];
            array_push($clinic_ids, (int)$getType);
            $results = $clinic_types->getSubClinics($getType);
            if(sizeof($results) > 0) {
                foreach ($results as $key => $value) {
                    array_push($clinic_ids, $value->ClinicTypeID);
                }
            }

            $clinicData = DB::table('clinic')
                ->join('clinic_types', 'clinic.Clinic_Type', '=', 'clinic_types.ClinicTypeID')    
                ->select(
                    'clinic.ClinicID','clinic.Name as CLName','clinic.Clinic_Type','clinic.Description','clinic.Custom_title','clinic.Website','clinic.image as CLImage','clinic.Address as CLAddress','clinic.State as CLState','clinic.City as CLCity','clinic.Postal as CLPostal','clinic.Phone','clinic.Lat as CLLat','clinic.Lng as CLLng','clinic.Clinic_Price',
                        'clinic_types.ClinicTypeID','clinic_types.Name as ClinicType',
                    DB::raw("
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("clinic.Active","=",1)    
                ->where("clinic_types.Active","=",1)  
                // ->where("clinic_types.ClinicTypeID","=",$getType)
                ->whereIn("clinic_types.ClinicTypeID", $clinic_ids)                   
                ->having("distance", "<", $radius)
                // ->orderBy("distance","ASC")
                // ->orderBy("clinic.position","DESC")
                // ->orderBy("clinic.position","ASC")
                ->orderBy("distance","ASC")
                ->get();
            return $clinicData;            
        }   
        public function Nearby_Old($lat,$lng,$radius){
            
            $clinicData = DB::table('clinic')
                ->select(
                    DB::raw("*,
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("Active","=",1)    
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->get();
            
           if($clinicData){
               return $clinicData;
           }else{
               return false;
           }            
        }
        
        public function PanelClinicNearby($lat,$lng,$radius,$insuranceid){
            
            $clinicData = DB::table('clinic')
                ->join('clinic_insurence_company', 'clinic.ClinicID', '=', 'clinic_insurence_company.ClinicID')
                //->join('insurance_company', 'clinic_insurence_company.InsuranceID', '=', 'insurance_company.CompanyID')      
                //->select('clinic.ClinicID','clinic.Name') 
                ->select(
                    DB::raw("*,
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance") )
                //->select('clinic.ClinicID')                    
                ->where("clinic.Active","=",1)
                ->where("clinic_insurence_company.Active","=",1)  
                ->where("clinic_insurence_company.InsuranceID","=",$insuranceid)                      
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->get();
            
             //print_r($clinicData);
            return $clinicData;           
        }
        
        
        /*public function Nearby($lat,$lng,$radius){
            
            $clinicData = DB::table('clinic')
                ->select(
                    DB::raw("*,
                            (3959 * acos( cos( radians({$lat}) ) *
                              cos( radians( lat ) )
                              * cos( radians( lng ) - radians({$lng})
                              ) + sin( radians({$lat}) ) *
                              sin( radians( lat ) ) )
                            ) AS distance"))
                ->where("Active","=",1)    
                ->having("distance", "<", $radius)
                ->orderBy("distance","ASC")
                ->get();
            
           if($clinicData){
               return $clinicData;
           }else{
               return false;
           }            
        }*/

     public function FindClinicProfile($clinicid){
         $clinicData = DB::table('clinic')
                ->join('user', 'clinic.ClinicID', '=', 'user.Ref_ID')
                ->select('clinic.ClinicID','clinic.Name as CLName','clinic.Clinic_Type','clinic.Description','clinic.Custom_title','clinic.Website','clinic.image as CLImage','clinic.Address as CLAddress','clinic.State as CLState','clinic.City as CLCity','clinic.Postal as CLPostal','clinic.Phone','clinic.Phone_Code','clinic.Lat as CLLat','clinic.Lng as CLLng','clinic.Clinic_Price','clinic.Favourite','user.UserType','user.Email','user.Password','clinic.Personalized_Message', 'clinic.currency_type', 'clinic.co_paid_status', 'clinic.gst_percent', 'clinic.co_paid_amount', 'clinic.consultation_fees', 'clinic.consultation_gst_status')  
                // ->where("clinic.Active",1)
                ->where("user.Active",1)
                ->where("clinic.ClinicID","=",$clinicid)                  
                ->first();

            if($clinicData) {
                return $clinicData;
            } else {
                return FALSE;
            }
     }

     

     ///////////////////////////////////////////////nhr ///////////////////////////////////////////////////////


     public function findTypeClinic($typeid)
     {
        $clinicData = DB::table('clinic')
                      ->where('Clinic_Type','=',$typeid)
                      ->where('Active', '=', 1)
                      ->get();

        return $clinicData;              
     }


     // nhr main searach functions for mobile

     public function getDistrict($search)
        {
            $Data = DB::table('clinic')
                ->select('District as district')
                ->where('District', 'like', "%$search%")
                ->where('Active', '=', 1) 
                ->groupBy('District')   
                ->get();

                return $Data;
        }


        public function getMrt($search)
        {
            $Data = DB::table('clinic')
                ->select('MRT as mrt')
                ->where('MRT', 'like', "%$search%")
                ->where('Active', '=', 1) 
                ->groupBy('MRT')   
                ->get();

                return $Data;
        }

        public function getClinics($search)
        {
            $Data = DB::table('clinic')
                ->where('Name', 'like', "%$search%")
                ->where('Active', '=', 1) 
                ->groupBy('Name')   
                ->get();

                return $Data;
        }

        public function getClinicsByType($key)
        {
            $Data = DB::table('clinic')
                ->where('Clinic_Type', '=', $key)
                ->where('Name', '!=', '')
                ->where('Active', '=', 1) 
                ->groupBy('Name')   
                ->get();

                return $Data;
        }

        public function getClinicsByProcedure($key)
        {
            $Data = DB::table('clinic')
                ->select('clinic.ClinicID', 'clinic.Name', 'clinic.Address', 'clinic.Postal', 'clinic.District', 'clinic.Country','clinic.Phone')
                ->join('clinic_procedure','clinic_procedure.ClinicID','=','clinic.ClinicID')
                ->where('clinic_procedure.Name', '=', $key)
                ->where('clinic.Active', '=', 1)    
                ->groupBy('clinic.Name')    
                ->get();

                return $Data;
        }

        public function getClinicsByDistrict($key)
        {
            $Data = DB::table('clinic')
                ->where('District', '=', $key)
                ->where('Active', '=', 1)   
                ->get();

                return $Data;
        }

        public function getClinicsByMrt($key)
        {
            $Data = DB::table('clinic')
                ->where('MRT', '=', $key)
                ->where('Active', '=', 1)   
                ->get();

                return $Data;
        }


public function getFavouriteClinics($userID)
{
    $Data = DB::table('clinic')
                ->join('clinic_user_favourite','clinic_user_favourite.clinic_id','=','clinic.ClinicID')
                ->where('clinic_user_favourite.user_id', '=', $userID)
                ->where('clinic_user_favourite.favourite', '=', 1)    
                ->get();

                return $Data;
}
     //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        //                              WEB                                   //
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//
        
        
        
        
        public function FindClinicDetails($value){
            $clinicData = DB::table('clinic')
                ->where('ClinicID', '=', $value)
                ->where('Active', '=', 1)    
                ->first();
   
            return $clinicData;    
        }

         public function getClinicName($value){
            $clinicData = DB::table('clinic')
                ->where('ClinicID', '=', $value)
                // ->where('Active', '=', 1)  
                // ->select('Name')  
                ->first();
   
            $data = explode(' ', $clinicData->Name);
            return $data;
        }
        
        public function UpdateClinicDetails($dataArray){
            unset($dataArray['speciality']);
            
            $allData = DB::table('clinic')
                ->where('ClinicID', '=', $dataArray['clinicid'])
                ->update($dataArray);
            
            return $allData;
        }

         public function UpdateClinicHomeDetails($dataArray){

            // $findClinicType = \DB::table('clinic_types')->where('ClinicTypeID', $dataArray['Clinic_Type'])->first();

            // if($findClinicType->discount_type == "fixed") {
            //     $dataArray['co_paid_status'] = 1;
            //     $dataArray['discount'] = '$'.$findClinicType->clinic_discount;
            //     $dataArray['co_paid_amount'] = $findClinicType->discount_amount;
            // } else {
            //     $dataArray['co_paid_status'] = 0;
            //     $dataArray['medicloud_transaction_fees'] = $findClinicType->discount_amount;
            //     $dataArray['discount'] = $findClinicType->clinic_discount.'%';
            // }

            $allData = DB::table('clinic')
                ->where('ClinicID', '=', $dataArray['clinicid'])
                ->update($dataArray);
            
            return $allData;
        }

        public function getClinicPercentage($id)
        {
            $data = Clinic::where('ClinicID', '=', $id)->first();
            $medi_percent = $data->medicloud_transaction_fees / 100;
            
            return array(
                'medi_percent' => $medi_percent,
                'discount'     => $data->discount 
            );    
        }

        public function updateGP($id)
        {
            return Clinic::where('ClinicID', $id)->where('co_paid_status', 0)->where('co_paid_amount', 0)->update(['co_paid_status' => 1, 'co_paid_amount' => 13, 'medicloud_transaction_fees' => 0]);
        }

        public function updatePercent($id)
        {
            return Clinic::where('ClinicID', $id)->update(['co_paid_status' => 0, 'co_paid_amount' => 0, 'medicloud_transaction_fees' => 0]);
        }

        public function checkCoPaidAmount($id)
        {
            $clinic = DB::table('clinic')->where('ClinicID', $id)->first();
            $type = DB::table('clinic_types')->where('ClinicTypeID', $clinic->Clinic_Type)->first();

            if($clinic->co_paid_status == 1 && $clinic->co_paid_amount == 0) {
                Clinic::where('ClinicID', $id)->update(['co_paid_amount' => $type->discount_amount]);
            }
        }

        public function updateClinicInfo($data, $clinic_id) {
            // Update user table first
            DB::table('user')
                ->where('Ref_ID', $clinic_id)
                ->where('UserType', 3)
                ->update(array( 'Name' => $data['Name'] ));
            
            // Update Clinic Table.
           return Clinic::where('ClinicID', $clinic_id)
                    ->update($data);
        }

        public function updateOperatingHours($data, $clinic_id) {
            // Get manage time id
            $manageTime = DB::table('manage_times')
                            ->where('PartyID', $clinic_id)
                            ->first();
            
            // Delete existing time record
            DB::table('clinic_time')
                ->where('ManageTimeID', $manageTime->ManageTimeID)
                ->delete();
            
            // Insert new record
            for ($i = 0; $i < count($data); $i++) { 
                // Assign Active Value
                $active = $data[$i]['active'];
                // Remove active key
                unset( $data[$i]['active'] );
                // Deconstruct Data
                $newData = array_merge($data[$i], array( 'ManageTimeID' => $manageTime->ManageTimeID, 'ClinicID' => $clinic_id, 'Active' => $active, 'Created_on' => time()));
                 
                // Insert New data to clinic_time table
                DB::table('clinic_time')
                        ->insert( $newData );
            }
            return true;
        }

        public function updateBreakHours($data, $clinic_id) {
            // Delete existing time record
            DB::table('extra_events')
                ->where('clinic_id', $clinic_id)
                ->delete();
            
            // update manage events
            for ($x = 0; $x < count($data); $x++) {
                if ($data[$x]['active'] == true) {
                    // Remove active key
                    unset( $data[$x]['active'] );
                    
                    $guid = StringHelper::getGUID();
                    if (!isset($data[$x]['clinic_id'])) {
                        $newData = array_merge($data[$x], array( 'id' => $guid, 'clinic_id' => $clinic_id));
                    } else {
                        $newData = array_merge($data[$x], array( 'id' => $guid));    
                    }
                    
                    DB::table('extra_events')
                        ->insert( $newData );

                    $guid = StringHelper::getGUID();
                    if (!isset($data[$x]['clinic_id'])) {
                        $newData = array_merge($data[$x], array( 'id' => $guid, 'clinic_id' => $clinic_id));
                    } else {
                        $newData = array_merge($data[$x], array( 'id' => $guid));    
                    }
                    
                    DB::table('extra_events')
                        ->insert( $newData );
                }
            }
            
            return "Providers break hours successfully updated.";
        }

        public function getProviderOperatingHour($clinic_id) {
            return DB::table('clinic_time')
                        ->where('ClinicID', $clinic_id)
                        ->where('Active', 1)
                        ->get();
        }
       
        public function getProviderBreakHours($clinic_id) {
            return DB::table('extra_events')
                        ->where('clinic_id', $clinic_id)
                        ->get();
        }

}
