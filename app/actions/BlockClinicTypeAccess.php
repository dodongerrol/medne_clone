<?php

class BlockClinicTypeAccess
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function execute()
    {
        $admin_id = Session::get('admin-session-id');

        $count = 0;

        foreach ($this->options['ids'] as $clinicId) {
            $existed = \CompanyBlockClinicAccess::where(
                    'customer_id', 
                    $this->options['customer_id']
                )
                ->where('account_type', $this->options['account_type'])
                ->where('clinic_id', $clinicId)
                ->first();

            if (!$existed) {
                $result = \CompanyBlockClinicAccess::create([
                    'customer_id' => $this->options['customer_id'],
                    'clinic_id'   => $clinicId,
                    'account_type' => $this->options['account_type'],
                    'status'  => $this->options['status']
                ]); 
                SystemLogLibrary::createAdminLog([
                    'admin_id'  => $admin_id,
                    'type'      => 'admin_company_block_clinic_access',
                    'data'      => serialize([
                        'customer_id' => $this->options['customer_id'],
                        'clinic_id'   => $clinicId,
                        'status'      => $this->options['status']
                    ])
                ]);
            } else {
                $result = \CompanyBlockClinicAccess::where('company_block_clinic_access_id',$existed->company_block_clinic_access_id)
                ->update(['status' => $this->options['status']]);

                SystemLogLibrary::createAdminLog([
                    'admin_id'  => $admin_id,
                    'type'      => 'admin_company_block_clinic_access',
                    'data'      => serialize([
                        'customer_id' => $this->options['customer_id'],
                        'clinic_id'   => $clinicId,
                        'status'      => $this->options['status']
                    ])
                ]);
            }

            $count ++;
        }

        echo "Total clinics completed: {$count} \n";
    }
}
