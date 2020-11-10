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
        Queue::push('ProcessBlockClinicAccess', [
            'message' => 'Testing Queue'
        ]);
        // $clinics = $this->options['ids'] ?? [];

        // $status = $this->options['status'];
        // $accountType = $this->options['account_type'] ?? 'employee';

        // $logs = [];
        // foreach ($clinics as $clinicId) {
        //     $result = \App\CompanyBlockClinicAccess::updateOrCreate(
        //         [
        //             'customer_id' => $this->options['customer_id'],
        //             'clinic_id'   => $clinicId,
        //             'account_type' => $this->options['account_type']
        //         ],
        //         [
        //             'customer_id' => $this->options['customer_id'],
        //             'clinic_id'   => $clinicId,
        //             'account_type' => $this->options['account_type'],
        //             'status'  => $this->options['status']
        //         ]
        //     );

        //     if (\AdminHelper::getAdminID()) {
        //         $logs[] = [
        //             'admin_id'  => \AdminHelper::getAdminID(),
        //             'type'      => 'admin_company_block_clinic_access',
        //             'data'      => serialize([
        //                 'customer_id' => $this->options['customer_id'],
        //                 'clinic_id'   => $clinicId,
        //                 'status'      => $status
        //             ])
        //         ];
        //     }
        // }
        
        // DB::table('admin_logs')->insert($logs);
        
        // if (isEqual($accountType, 'company')) {
        //     $account = DB::table('customer_link_customer_buy')
        //         ->where('customer_buy_start_id', $this->options['customer_id'])
        //         ->first();
            
        //     \App\CorporateMembers::select('user_id')
        //         ->where('removed_status', 0)
        //         ->where('corporate_id', $account->corporate_id)
        //         ->chunk(5, function ($corporate_members) use ($clinics, $accountType, $status) {
        //             dispatch(
        //                 (new ProcessBlockClinicAccess([
        //                     'customer_id' => $this->options['customer_id'],
        //                     'members' => $corporate_members,
        //                     'clinic_ids' => $clinics,
        //                     'account_type' => $accountType,
        //                     'status' => $status
        //                 ]))->onConnection('redis')
        //             );
        //     });
        // }
    }
}
