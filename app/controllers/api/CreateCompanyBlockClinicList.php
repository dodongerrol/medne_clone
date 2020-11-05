<?php

class CreateCompanyBlockClinicList extends \BaseController 
{
    public function create()
    {
        $input = Input::all();

        if(empty($input['type']) || $input['type'] == null) {
          return array('status' => false, 'message' => 'Block access type access is required');
        }

        $region_type = ["sgd", "myr", "all_region"];

        if(!in_array($input['region'], $region_type)) {
            return array(
              'status' => false, 
              'message' => 'Region Type must be sgd or myr'
            );
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $hr_id = $result->hr_dashboard_id;
        $admin_id = Session::get('admin-session-id');
        $account_type = "company";

        $check = $customer = DB::table('customer_buy_start')
            ->where('customer_buy_start_id', $customer_id)
            ->first();

        if(!$customer) {
            return array('status' => false, 'message' => 'Customer/Company does not exist.');
        }

        if (isEqual($input['type'], 'clinic_type')) {
            if(empty($input['clinic_type_id']) || $input['clinic_type_id'] == null) {
                return array('status' => false, 'messsage' => 'Clinic Type ID is required');
            }
            if(empty($input['access_status']) || $input['access_status'] == null) {
                return array('status' => false, 'messsage' => 'Access status is required');
            }

            $clinic_ids = [];
            
            if($input['region'] == "all_region") {
                $clinic_ids = DB::table('clinic')
                    ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                    ->whereIn('clinic.Clinic_Type', $input['clinic_type_id'])
                    ->pluck('clinic.ClinicID');
            } else {
                $clinic_ids = DB::table('clinic')
                    ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                    ->where('clinic.currency_type', $input['region'])
                    ->whereIn('clinic.Clinic_Type', $input['clinic_type_id'])
                    ->pluck('clinic.ClinicID');
            }

            if (count($clinic_ids) <= 0) {
                return array('status' => false, 'messsage' => 'No clinics found!');
            } else {
                foreach ($clinic_ids as $clinic_id) {
                    $existed = \CompanyBlockClinicAccess::where(
                        'customer_id', 
                        $customer_id
                    )
                    ->where('account_type', $account_type)
                    ->where('clinic_id', $clinic_id)
                    ->first();
                    
                    if (!$existed) {
                        $result = \CompanyBlockClinicAccess::create([
                            'customer_id' => $customer_id,
                            'clinic_id' => $clinic_id,
                            'account_type' => $account_type,
                            'status' => $data['status']
                        ]);
                        SystemLogLibrary::createAdminLog([
                            'admin_id'  => $admin_id,
                            'type'      => 'admin_company_block_clinic_access',
                            'data'      => serialize([
                                'customer_id' => $customer_id,
                                'clinic_id'   => $id,
                                'status'      => $data['status']
                            ])
                        ]);
                    } else {
                        $result = \CompanyBlockClinicAccess::where('company_block_clinic_access_id', $existed->company_block_clinic_access_id)
                        ->update(['status' => $data['status']]);
    
                        SystemLogLibrary::createAdminLog([
                            'admin_id'  => $admin_id,
                            'type'      => 'admin_company_block_clinic_access',
                            'data'      => serialize([
                                'customer_id' => $customer_id,
                                'clinic_id'   => $id,
                                'status'      => $data['status']
                            ])
                        ]);
                    }

                }
                Queue::push('ProcessBlockClinicAccess', array('message' => 'This should be dispatch!'));
            }
        }
    }
}