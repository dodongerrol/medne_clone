<?php

class CreateCompanyBlockClinicList extends \BaseController 
{
    const COMPANY = 'company';
    const EMPLOYEE = 'employee';

    const SGD = 'sgd';
    const MYR = 'myr';
    const ALL_REGION = 'all_region';

    const CHUNK = 50;

    public function create()
    {
        $input = Input::all();

        $response = [
            'status' => false,
            'message' => ''
        ];

        $account_types = [self::COMPANY, self::EMPLOYEE];
        $region_types = [self::SGD, self::MYR, self::ALL_REGION];

        if (!Input::has('type') || is_null(Input::get('type'))) {
            $response['message'] = 'Block access type access is required';

            return $response;
        }

        if (!in_array(Input::get('region'), $region_types)) {
            $response['message'] = 'Block account type access must be company or employee';

            return $response;
        }

        $result = StringHelper::getJwtHrSession();
        $customer_id = $result->customer_buy_start_id;
        $hr_id = $result->hr_dashboard_id;
        $admin_id = Session::get('admin-session-id');

        $check = $customer = DB::table('customer_buy_start')
            ->where('customer_buy_start_id', $customer_id)
            ->first();

        if(!$customer) {
            return array('status' => false, 'message' => 'Customer/Company does not exist.');
        }

        /**
         * Clinic Type actions
         */
        if (isEqual(Input::get('type'), 'clinic_type')) {
            if (
                !Input::has('clinic_type_id') || 
                isEqual(Input::get('clinic_type_id'), null)
            ) {
                $response['message'] = 'Clinic Type ID is required';

                return $response;
            }

            $status_codes = [0, 1];

            if (
                Input::has('access_status') && 
                in_array(Input::get('access_status'), $status_codes)
            ) {
                $response['message'] = 'Access Status should only be 1 or 0';
            }

            $clinic_ids = [];

            if(Input::get('region') == "all_region") {
                $clinic_ids = DB::table('clinic')
                    ->select('clinic.ClinicID as clinic_id')
                    ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                    ->whereIn('clinic.Clinic_Type', Input::get('clinic_type_id'))
                    ->get();
            } else {
                $clinic_ids = DB::table('clinic')
                    ->select('clinic.ClinicID as clinic_id')
                    ->join('clinic_types', 'clinic_types.ClinicTypeID', '=', 'clinic.Clinic_Type')
                    ->where('clinic.currency_type', Input::get('region'))
                    ->whereIn('clinic.Clinic_Type', Input::get('clinic_type_id'))
                    ->get();
            }

            $chunkClinics = array_column($clinic_ids, 'clinic_id');

            foreach (array_chunk($chunkClinics, self::CHUNK) as $clinics) {
                Queue::push('ProcessBlockClinicTypeAccess', [
                    'customer_id' => $customer_id,
                    'ids' => $clinics,
                    'account_type' => 'company',
                    'status' => Input::get('status')
                ]);
            }

            // if (isEqual(Input::get('type'), 'company')) {
                $account = DB::table('customer_link_customer_buy')
                    ->where('customer_buy_start_id', $customer_id)
                    ->first();
    
                \CorporateMembers::select('user_id')
                    ->where('removed_status', 0)
                    ->where('corporate_id', $account->corporate_id)
                    ->chunk(self::CHUNK, function ($members) use ($customer_id, $chunkClinics)   {
                        // Queue::push('ProcessBlockClinicAccess', [
                        //     'customer_id' => $customer_id,
                        //     'members' => $members, 
                        //     'clinic_ids' => Input::get('clinic_type_id'),
                        //     'account_type' => 'employee',
                        //     'status' => Input::get('status')
                        // ]);
                        foreach (array_chunk($chunkClinics, self::CHUNK) as $clinics) {
                            Queue::push('ProcessBlockClinicAccess', [
                                'customer_id' => $customer_id,
                                'members' => $members, 
                                'clinic_ids' => $clinics,
                                'account_type' => 'employee',
                                'status' => Input::get('status')
                            ]);
                        }
                    });
            // }

            $response['status'] = true;
            $response['message'] = 'Clinic Block Lists updated for this Company. Please be inform that it will not refect instantly due to high number of clinics that are being block both company and employee side.';

            return $response;
        }

        if (isNotEqual(Input::get('type'), 'clinic_type')) {
            foreach (array_chunk(Input::get('clinic_type_id'), self::CHUNK) as $clinics) {
                $blocker = new \BlockClinicTypeAccess([
                    'customer_id' => $customer_id,
                    'ids' => $clinics,
                    'account_type' => Input::get('account_type'),
                    'status' => Input::get('status')
                ]);

                $blocker->execute();

                // if (isEqual(Input::get('type'), 'company')) {
                    $account = DB::table('customer_link_customer_buy')
                        ->where('customer_buy_start_id', $customer_id)
                        ->first();
        
                    \CorporateMembers::select('user_id')
                        ->where('removed_status', 0)
                        ->where('corporate_id', $account->corporate_id)
                        ->chunk(self::CHUNK, function ($members) use ($customer_id, $clinics) {
                            $blocker = new \BlockClinicAccess([
                                'customer_id' => $customer_id,
                                'members' => $members, 
                                'clinic_ids' => $clinics,
                                'account_type' => 'employee',
                                'status' => Input::get('status')
                            ]);
                            $blocker->execute();
                        });
                // }    
            }

            $response['status'] = true;
            $response['message'] = 'Clinic Block Lists updated for this Company. Please be inform that it will not refect instantly due to high number of clinics that are being block both company and employee side.';
            return $response;
        }
    }
}