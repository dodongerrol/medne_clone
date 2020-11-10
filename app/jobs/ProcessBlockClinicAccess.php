<?php


class ProcessBlockClinicAccess
{
    public function fire($job, $data)
    {
        $logs = [];
        $admin_id = Session::get('admin-session-id');
        $statusForHumans = isEqual($data['status'], 1) ? 'Blocked' : 'Opened';

        // First loop for members
        foreach ($data['members'] as $member) {
            // Second loop for clinic ids
            foreach ($data['clinic_ids'] as $clinicId) {
                $existed = \CompanyBlockClinicAccess::where('customer_id', $member->user_id)
                    ->where('account_type', $data['account_type'])
                    ->where('clinic_id', $clinicId)
                    ->first();
                    
                if (!$existed) {
                    $result = \CompanyBlockClinicAccess::create([
                        'customer_id' => $member->user_id,
                        'clinic_id' => $clinicId,
                        'account_type' => $data['account_type'],
                        'status' => $data['status']
                    ]);
                    SystemLogLibrary::createAdminLog([
                        'admin_id'  => $admin_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize([
                            'customer_id' => $member->user_id,
                            'clinic_id'   => $clinicId,
                            'status'      => $data['status']
                        ])
                    ]);
                } else {
                    $result = \CompanyBlockClinicAccess::where(
                        'company_block_clinic_access_id', 
                        $existed->company_block_clinic_access_id
                    )
                    ->update(['status' => $data['status']]);

                    SystemLogLibrary::createAdminLog([
                        'admin_id'  => $admin_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize([
                            'customer_id' => $member->user_id,
                            'clinic_id'   => $clinicId,
                            'status'      => $data['status']
                        ])
                    ]);
                }
            }
        }
    }
}