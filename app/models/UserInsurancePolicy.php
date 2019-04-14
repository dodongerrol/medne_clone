<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserInsurancePolicy extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_insurance_policy';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('password', 'remember_token');
        
        //get user Insurance policy
        //parameter : user id 
        //Out put : array 
        public function getUserInsurancePolicyi($profileid){           
            $findUser = DB::table('user_insurance_policy')
                    ->where('UserID', '=', $profileid)
                    ->where('Active', '=', 1)
                     ->first();
            
            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }  
        }
        /* Use          :   Used to find users primary insurance 
         * 
         */
        public function FindUserInsurancePolicy($profileid){
                $userPolicy = DB::table('user_insurance_policy')
                    ->join('insurance_company', 'user_insurance_policy.InsuaranceCompanyID', '=', 'insurance_company.CompanyID')
                    //->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('insurance_company.CompanyID','insurance_company.Name', 'insurance_company.Image','user_insurance_policy.UserInsurancePolicyID', 'user_insurance_policy.PolicyNo','user_insurance_policy.PolicyName')
                    ->where('user_insurance_policy.UserID','=',$profileid)
                    ->where('user_insurance_policy.IsPrimary','=',1)
                    ->where('user_insurance_policy.Active','=',1)
                    ->where('insurance_company.Active','=',1)    
                    ->first();
                if($userPolicy){
                    return $userPolicy;
                }else{
                    return FALSE;
                }
        }
        public function getUserInsurancePolicy($profileid){
                $userPolicy = DB::table('user_insurance_policy')
                    ->join('insurance_company', 'user_insurance_policy.InsuaranceCompanyID', '=', 'insurance_company.CompanyID')
                    //->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select('insurance_company.CompanyID','insurance_company.Name', 'insurance_company.Image','user_insurance_policy.UserInsurancePolicyID', 'user_insurance_policy.PolicyNo','user_insurance_policy.PolicyName')
                    ->where('user_insurance_policy.UserID','=',$profileid)
                    //->where('user_insurance_policy.Active','=',1)
                    //->where('user_insurance_policy.IsPrimary','=',1)    
                    ->first();
                if($userPolicy){
                    return $userPolicy;
                }else{
                    return FALSE;
                }
        }
        
        public function updateInsurancePolicy ($dataArray){         
            $allData = DB::table('user_insurance_policy')
                ->where('UserID', '=', $dataArray['userid'])
                ->update($dataArray);
            
            return $allData;
        }
        public function UpdatePolicy ($dataArray, $policyid){         
            $allData = DB::table('user_insurance_policy')
                ->where('UserInsurancePolicyID', '=', $policyid)
                ->update($dataArray);
            
            return $allData;
        }
        
        //Add new insurance policy
        public function addInsurancePolicy ($dataArray){
            $user = new User();
                $this->UserID = $dataArray['userid'];
                $this->InsuaranceCompanyID = $dataArray['insuranceid'];
                $this->PolicyName = $dataArray['policyname'];
                $this->PolicyNo = $dataArray['policyno'];
                $this->IsPrimary = $dataArray['isprimary'];
                $this->Created_on = time();
                $this->created_at = time();
                $this->Active = 1;
                	
                if($this->save()){
                    $insertedId = $this->id;
                    return $insertedId;
                }else{
                    return false;
                }
                
        }
        
        public function FindAllInsurancePolicy($profileid){
            $userPolicy = DB::table('user_insurance_policy')
            ->join('insurance_company', 'user_insurance_policy.InsuaranceCompanyID', '=', 'insurance_company.CompanyID')
            //->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('insurance_company.CompanyID','insurance_company.Name', 'insurance_company.Image','user_insurance_policy.UserInsurancePolicyID', 'user_insurance_policy.PolicyNo','user_insurance_policy.PolicyName','user_insurance_policy.IsPrimary')
            ->where('user_insurance_policy.UserID','=',$profileid)
            ->where('user_insurance_policy.Active','=',1)        
            ->get();

            return $userPolicy;
        }
        
        
        public function FindUserPrimaryInsurance ($profileid){         
            $allData = DB::table('user_insurance_policy')
                ->where('UserID', '=', $profileid)
                ->where('IsPrimary', '=', 1)
                ->where('Active', '=', 1)    
                ->first();
            
            return $allData;
        }
        
        public function FindInsurancePolicyByID ($userid, $insurancepolicyid){         
            $allData = DB::table('user_insurance_policy')
                ->where('UserID', '=', $userid)    
                ->where('UserInsurancePolicyID', '=', $insurancepolicyid)
                ->where('Active', '=', 1)    
                ->first();
            
            return $allData;
        }
        
        public function FindInsurancePolicy ($userid, $insuranceid){         
            $allData = DB::table('user_insurance_policy')
                ->where('UserID', '=', $userid)    
                ->where('InsuaranceCompanyID', '=', $insuranceid)
                ->where('Active', '=', 1)    
                ->first();
            
            return $allData;
        }
       
}
