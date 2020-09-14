<?php
    /**
     * Locations & departments embedded scripts
     */
    $locationDepartmentsViews = "{$server}/assets/hr-dashboard/templates/home/companyProfile/locationsDepartments";
    $locationDepartmentsScripts = [
        '/employee-allocation/api.js',
        '/employee-allocation/controller.js',
        '/locations/api.js',
        '/locations/controller.js',
        '/departments/api.js',
        '/departments/controller.js'
    ];
?>

@foreach ($locationDepartmentsScripts as $script)
<script type="text/javascript" src="{{ $locationDepartmentsViews . $script }}?_={{ $date->format('U') }}"></script>
@endforeach