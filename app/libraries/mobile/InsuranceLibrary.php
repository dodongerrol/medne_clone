<?php

class InsuranceLibrary{
    
    public static function FindClinicInsurance($clinicid){
        $clinicInsurance = new ClinicInsurenceCompany();
            $panelInsurance = $clinicInsurance->FindClinicInsuranceCompnay($clinicid);
            if($panelInsurance){
                return $panelInsurance;
            }else{
                return FALSE;
            }
    }
    
    public static function AllInsuranceCompany(){
        $insuranceCompany = new InsuranceCompany();
        $returnObject = new stdClass();

        $findAllCompany = $insuranceCompany->findInsuranceCompany();
        if($findAllCompany){
            foreach($findAllCompany as $inCom){
                $dataArray['insurance_id'] = $inCom->CompanyID;
                $dataArray['name'] = $inCom->Name;
                //$dataArray['image_url'] = URL::to('/assets/'.$inCom->Image);
                $dataArray['image_url'] = $inCom->Image;
                $allArray[] = $dataArray;
            }

            $returnObject->status = TRUE;
            $returnObject->data = $allArray;
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject; 
    }
    
    public static function AddInsurancePolicy($findUserID){
        $userinsurancepolicy = new UserInsurancePolicy();
        $allInputdata = Input::all();
        $returnObject = new stdClass();
        if(!empty($allInputdata['insurance_id']) && !empty($allInputdata['policy_name']) && !empty($allInputdata['policy_no'])){
            $dataArray['userid']= $findUserID;
            $dataArray['insuranceid']= Input::get ('insurance_id');
            $dataArray['policyname']= Input::get ('policy_name');
            $dataArray['policyno']= Input::get ('policy_no');
            $dataArray['isprimary']= Input::get ('is_primary');

            $insertID = $userinsurancepolicy->addInsurancePolicy($dataArray);
            if($insertID){
                $findAllInsurancePolicy = self::AllUserInsurancePolicy($findUserID);
                if($findAllInsurancePolicy){
                    foreach($findAllInsurancePolicy as $insPolicy){
                        if($insertID != $insPolicy->UserInsurancePolicyID){
                            $updateArray['IsPrimary']= 0;
                            $updateArray['updated_at'] = time();
                            self::UpdatePolicy($updateArray,$insPolicy->UserInsurancePolicyID);
                        }
                    }
                }
                $returnObject->status = TRUE;
                $returnObject->data['record_id'] = $insertID;
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("Tryagain");
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        return $returnObject;  
    }
    
    public static function FindAllInsurancePolicy($findUserID){
        $returnObject = new stdClass();
        $allPolicy = self::AllUserInsurancePolicy($findUserID);
        if($allPolicy){
            foreach($allPolicy as $policy){
                //$dataArray['insurance_id'] = $policy->CompanyID;
                $dataArray['insurance_id'] = $policy->UserInsurancePolicyID;
                $dataArray['name'] = $policy->Name;
                //$dataArray['image_url'] = URL::to('/assets/'.$policy->Image);
                $dataArray['image_url'] = $policy->Image;
                $dataArray['policy_no'] = $policy->PolicyNo;
                $dataArray['policy_name'] = $policy->PolicyName;
                if($policy->IsPrimary==1){
                    $dataArray['is_primary'] = TRUE;
                }else{
                    $dataArray['is_primary'] = FALSE;
                }
                //$dataArray['is_primary'] = $policy->IsPrimary;
                $allArray[] = $dataArray;
            }
            $returnObject->status = TRUE;
            $returnObject->data = $allArray;
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("NoRecords");
        }
        return $returnObject;
    }
    
    /* Use          :   Used to find users primary insurance 
     * Access       :   Public (no direct access allowed)
     * Parameter    :   User id
     */
    public static function FindUserPrimaryInsurance($userid){
        $userpolicy = new UserInsurancePolicy();
        $findUserPolicy = $userpolicy->FindUserPrimaryInsurance($userid);
        if($findUserPolicy){
            return $findUserPolicy;
        }else{
            return FALSE;
        }
    }
    
    public static function FindInsuranceCompany($insurance){
        $insurancecompany = new InsuranceCompany();
        $findCompany = $insurancecompany->InsuranceCompanyByID($insurance);
        if($findCompany){
            return $findCompany;
        }else{
            return FALSE;
        }
    }
    
    public static function DeleteInsurancePolicy($userid){
        $returnObject = new stdClass();
        if(!empty(Input::get ('insurance_id'))){ 
            //$findPolicy = self::FindUserPolicy($userid, Input::get ('insurance_id'));
            $findPolicy = self::FindUserPolicyByID($userid, Input::get ('insurance_id'));
            
            if($findPolicy){ 
                $updateArray['Active'] = 0;
                $updateArray['updated_at'] = time();
                $updated = self::UpdatePolicy($updateArray,$findPolicy->UserInsurancePolicyID);
                if($updated){
                    $returnObject->status = TRUE;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Update");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        return $returnObject;
    }
    
    public static function ChangePrimaryPolicy($userid){
        $returnObject = new stdClass();
        if(!empty(Input::get ('insurance_id'))){ 
            //$findPolicy = self::FindUserPolicy($userid, Input::get ('insurance_id'));
            $findPolicy = self::FindUserPolicyByID($userid, Input::get ('insurance_id'));
            if($findPolicy){ 
                $findPrimary = self::FindUserPrimaryInsurance($userid);
                if($findPrimary){
                    $updatePriArray['IsPrimary'] = 0;
                    $updatePriArray['updated_at'] = time();
                    self::UpdatePolicy($updatePriArray,$findPrimary->UserInsurancePolicyID);
                }
                $updateArray['IsPrimary'] = 1;
                $updateArray['updated_at'] = time();
                $updated = self::UpdatePolicy($updateArray,$findPolicy->UserInsurancePolicyID);
                if($updated){
                    //$returnObject = self::FindAllInsurancePolicy($userid);
                    $returnObject->status = TRUE;
                }else{
                    $returnObject->status = FALSE;
                    $returnObject->message = StringHelper::errorMessage("Update");
                }
            }else{
                $returnObject->status = FALSE;
                $returnObject->message = StringHelper::errorMessage("NoRecords");
            }
        }else{
            $returnObject->status = FALSE;
            $returnObject->message = StringHelper::errorMessage("EmptyValues");
        }
        return $returnObject;
    }
    
    public static function FindUserPolicyByID($userid, $insurancepolicyid){ 
        $insurancepolicy = new UserInsurancePolicy();
        $findPolicy = $insurancepolicy->FindInsurancePolicyByID($userid, $insurancepolicyid);
        if($findPolicy){ 
            return $findPolicy;
        }else{
            return FALSE;
        }
    }
    
    public static function FindUserPolicy($userid, $insuranceid){ 
        $insurancepolicy = new UserInsurancePolicy();
        $findPolicy = $insurancepolicy->FindInsurancePolicy($userid, $insuranceid);
        if($findPolicy){ 
            return $findPolicy;
        }else{
            return FALSE;
        }
    }
    
    public static function UpdatePolicy($updateArray,$policyid){
        $insurancepolicy = new UserInsurancePolicy();
        $updated = $insurancepolicy->UpdatePolicy($updateArray,$policyid);
        if($updated){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public static function AllUserInsurancePolicy($findUserID){
        $userinsurancepolicy = new UserInsurancePolicy();
        $allPolicy = $userinsurancepolicy->FindAllInsurancePolicy($findUserID);
        if($allPolicy){
            return $allPolicy;
        }else{
            return FALSE;
        }
    }
    public static function FindAnnotation(){
        $insuranceCompany = new InsuranceCompany();
        $findAnnotation = $insuranceCompany->FindAnnotation("Other");
        if($findAnnotation){
            return $findAnnotation;
        }else{
            return FALSE;
        }
    }
    public static function FindUserInsurancePolicy($findUserID){
        $userinsurancepolicy = new UserInsurancePolicy();
        $allPolicy = $userinsurancepolicy->FindUserInsurancePolicy($findUserID);
        if($allPolicy){
            return $allPolicy;
        }else{
            return FALSE;
        }
    }
}


