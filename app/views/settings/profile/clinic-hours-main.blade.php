
  <!-- tabs -->

    <div class="tabbable detail-tab" style="padding-left: 30px; padding-top: 10px;">

      <div class="row" style="border-bottom: 1px solid #ddd;">
        <ul class="nav nav-tabs">
         <li class="active tab-border"><a id="clinic-hours-tab" href="#clinic-hours-main" data-toggle="tab"><b>OPERATING-HOURS</b></a></li>
         <li class="tab-border"><a id="clinic-breaks-tab" href="#clinic-breaks-main" data-toggle="tab"><b>BREAKS</b></a></li>
         <li class="tab-border"><a id="clinic-time_off-tab" href="#clinic-time_off-main" data-toggle="tab"><b>TIME-OFF</b></a></li>
      </ul>

    </div>
      
    <div class="tab-content row">
            <div class="tab-pane active" id="clinic-hours-main">
                @include('settings.profile.clinic-business-hours')
            </div>
            <div class="tab-pane" id="clinic-breaks-main">
              @include('settings.profile.clinic-breaks')
            </div>
            <div class="tab-pane" id="clinic-time_off-main"></div>
        </div>
    </div>

  <!-- /tabs -->

<style>

  .tab-border{
    border-bottom: 1px solid #ddd;
  }
  
</style>