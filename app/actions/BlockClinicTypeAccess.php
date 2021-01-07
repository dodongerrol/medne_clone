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
        echo $this->options['account_type']." \n";
        echo "BlockClinicTypeAccess\n";
        foreach ($this->options['ids'] as $clinicId) {
            $existed = \CompanyBlockClinicAccess::where(
                    'customer_id', 
                    $this->options['customer_id']
                )
                ->where('account_type', $this->options['account_type'])
                ->where('clinic_id', $clinicId)
                ->first();

            if (!$existed) {
                if($this->options['env'] == "production") {
                    $data = array(
                        'customer_id'   => $this->options['customer_id'],
                        'clinic_id'     => $clinicId,
                        'account_type'     => $this->options['account_type'],
                        'status'        => $this->options['status'],
                        'type'          => 'create'
                    );
                    $result =$result = \httpLibrary::postHttp($this->options['api_url'], $data, []);
                } else {
                    $result = \CompanyBlockClinicAccess::create([
                        'customer_id' => $this->options['customer_id'],
                        'clinic_id'   => $clinicId,
                        'account_type' => $this->options['account_type'],
                        'status'  => $this->options['status']
                    ]); 
                }

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
                if($this->options['env'] == "production") {
                    $data = array(
                        'customer_id'   => $this->options['customer_id'],
                        'clinic_id'     => $clinicId,
                        'account_type'     => $this->options['account_type'],
                        'status'        => $this->options['status'],
                        'type'          => 'update'
                    );
                    $result =$result = \httpLibrary::postHttp($this->options['api_url'], $data, []);
                } else {
                    $result = \CompanyBlockClinicAccess::where('company_block_clinic_access_id',$existed->company_block_clinic_access_id)
                    ->update(['status' => $this->options['status']]);
                }
                
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
