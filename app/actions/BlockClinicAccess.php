<?php

class BlockClinicAccess
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function execute()
    {
        $logs = [];
        $admin_id = Session::get('admin-session-id');
        $statusForHumans = isEqual($this->options['status'], 1) ? 'Blocked' : 'Opened';
        
        $count = 0;
        echo $this->options['account_type']." \n";
        echo "BlockClinicAccess\n";
        // First loop for members
        foreach ($this->options['members'] as $member) {
            // Second loop for clinic ids
            foreach ($this->options['clinic_ids'] as $clinicId) {
                echo $clinicId." done for member id ".$member['user_id']." \n";
                $existed = \CompanyBlockClinicAccess::where('customer_id', $member['user_id'])
                    ->where('account_type', $this->options['account_type'])
                    ->where('clinic_id', $clinicId)
                    ->first();
                    
                if (!$existed) {
                    $result = \CompanyBlockClinicAccess::create([
                        'customer_id' => $member['user_id'],
                        'clinic_id'   => $clinicId,
                        'account_type' => $this->options['account_type'],
                        'status' => $this->options['status']
                    ]);
                    // echo $result." created \n";
                    SystemLogLibrary::createAdminLog([
                        'admin_id'  => $admin_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize([
                            'customer_id' => $member['user_id'],
                            'clinic_id'   => $clinicId,
                            'status'      => $this->options['status']
                        ])
                    ]);
                } else {
                    $result = \CompanyBlockClinicAccess::where('company_block_clinic_access_id', $existed->company_block_clinic_access_id)
                    ->update(['status' => $this->options['status']]);

                    SystemLogLibrary::createAdminLog([
                        'admin_id'  => $admin_id,
                        'type'      => 'admin_company_block_clinic_access',
                        'data'      => serialize([
                            'customer_id' => $member['user_id'],
                            'clinic_id'   => $clinicId,
                            'status'      => $this->options['status']
                        ])
                    ]);
                }
            }

            $count ++;
        }

        echo "Total members completed: {$count} \n";
    }
}
