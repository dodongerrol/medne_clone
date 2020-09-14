<?php
    /**
     * Billing & Payments embedded scripts
     */
    $billingPaymentsViews = "{$server}/assets/hr-dashboard/templates/home/companyProfile/BillingPayments";
    $billingPaymentsScripts = [
        '/contacts/api.js',
        '/contacts/controller.js'
    ];
?>

@foreach ($billingPaymentsScripts as $script)
<script type="text/javascript" src="{{ $billingPaymentsViews . $script }}?_={{ $date->format('U') }}"></script>
@endforeach