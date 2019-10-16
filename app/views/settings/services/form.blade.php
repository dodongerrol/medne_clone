{{ HTML::script('assets/settings/services/service.js') }}
{{ HTML::style('assets/settings/services/service.css') }}

<?php if ($services) { ?> <!-- display for edit -->
	<div>
	    <h4 style="float: left; margin-left: 45px; padding-top: 15px; font-size: large; font-weight: bold;">Update Service</h4>
      <div style="float: right; margin-top: 30px; margin-left: 10px;">
        <a id="btn-service-delete" href="#"  data-toggle="popover" data-placement="left" >
          <span class="glyphicon glyphicon-trash" aria-hidden="true" style="padding: 6px 10px 0 0;"></span>
        </a>
      </div>
	    <span style="float: right; margin-top: 25px;  margin-left: 10px"><button class="btn" id="btn-service-cancel">Cancel</button></span>
	    <span style="float: right; margin-top: 25px;"><button class="btn" id="btn-service-save">Update</button></span>
	</div>
  <div style="clear: both"></div>

	
	<hr>
<input type="hidden" name="" value="{{$services->ProcedureID}}" id="h-service_id">
  <div class="row">
  		<div class="col-md-6" style="color: #666666;">
  			<!-- <div class="" style="margin-left: 45px">
  				
  				<div style="display: inline; margin-left: 15px"> </div>
  			</div> -->
        <div class="row">
          <div class="col-md-4 text-right">
            <img style="margin-left: 74px" alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="60" height="60">
          </div>
          <div class="col-md-6" style="padding-top: 8px;"> 
            <input class="txt_bg" type="text" name="" id="service-name" value="{{$services->Name}}"  placeholder="Enter Service Name" style="width: 260px;">
          </div>
        </div>
  			<br><br>
  			<div class="row">
  				<div class="col-md-4 text-right" style="padding-top: 6px;">Service Cost <span class="sg">S$</span><span class="rm">RM</span></div>
  				<div class="col-md-6"> <input class="txt_bg" id="service-cost" type="text" name="" value="{{$services->Price}}"  placeholder="0" style="width: 260px;"></div>
  			</div>
  			<br>
  			<div class="row">
  				<div class="col-md-4 text-right" style="padding-top: 6px;">Service Time (mins)</div>
  				<div class="col-md-6"> <input class="txt_bg" id="service-duration" type="text" name="" value="{{$services->Duration}}"  placeholder="0" style="width: 260px;"></div>
  			</div>
  			<br>
  			<div class="row">
  				<div class="col-md-4 text-right" style="padding-top: 6px;">Service Description</div>
  				<div class="col-md-6"> <input class="txt_bg" id="service-description" type="text" name="" value="{{$services->Description}}"  placeholder="Enter Service Description" style="width: 260px;"></div>
  			</div>
  		</div>

  		<div class="col-md-6" style="color: #777;" id="service-doctor-wap">
        <span style="padding-left: 22px; color: #777;">Who can provide the Service</span>
  			<div style="width: 100%;" class="col-md-5">

        <?php if (count($doctors)==count($service_doctors)){?>

          <div style="padding: 0px; width: 6%;" class="col-md-1">
            <input type="checkbox" checked="" id="all-doctor">
          </div>          
          <div style="padding: 0px; width: 94%;" class="col-md-1">
            <label id="all-doc-lbl" style="padding-top: 13px; color: #1667AC;"><b>Select All Doctors</b></label>
          </div>

        <?php } else {?>

          <div style="padding: 0px; width: 6%;" class="col-md-1">
            <input type="checkbox" id="all-doctor">
          </div>          
          <div style="padding: 0px; width: 94%;" class="col-md-1">
            <label id="all-doc-lbl" style="padding-top: 13px; color: #1667AC;"><b>Select All Doctors</b></label>
          </div>
        <?php }?>

          <!-- <label style=""><input id="doctor-all-services" type="checkbox" checked ><b>Select All Services</b></label> -->

        </div>

        <?php if($doctors){

         $doctorcount = 0;

        foreach ($doctors as $value) {

          if ($service_doctors){

            foreach($service_doctors as $service){

              if($service->DoctorID==$value->DoctorID && $doctorcount==0){ 
        ?>

        <div style="width: 100%;" class="col-md-5">

          <div style="padding: 0px; width: 6%;" class="col-md-1">
            <input type="checkbox" id="{{$value->DoctorID}}" class="service-doc" checked="">
          </div>          
          <div style="padding: 0px; width: 94%;" class="col-md-1">
            <label id="{{$value->DoctorID}}-lbl" class="doc-lbl" style="padding-top: 13px; color: black;">{{ $value->DocName}}</label>
          </div>

        </div>

        <?php 
              $doctorcount = 1; 

            } 
          }
        }

        if ($doctorcount == 0){ ?>

          <div style="width: 100%;" class="col-md-5">

          <div style="padding: 0px; width: 6%;" class="col-md-1">
            <input type="checkbox" id="{{$value->DoctorID}}" class="service-doc">
          </div>          
          <div style="padding: 0px; width: 94%;" class="col-md-1">
            <label id="{{$value->DoctorID}}-lbl" class="doc-lbl" style="padding-top: 13px;">{{ $value->DocName}}</label>
          </div>

        </div>

        <?php }else {
          $doctorcount = 0;
        }
        ?>

        <?php } }?>

  		</div>

  </div>

<?php } else{ ?>

<div>
	    <h4 style="float: left; margin-left: 45px; padding-top: 15px; font-size: large; font-weight: bold;">Add Service</h4>
	    <span style="float: right; margin-top: 25px; margin-left: 10px"><button class="btn"  id="btn-service-cancel">Cancel</button></span>
	    <span style="float: right; margin-top: 25px;"><button class="btn" id="btn-service-save">Save</button></span>
	</div>
  <div style="clear: both"></div>
	<hr>
<input type="hidden" name="" value="null" id="h-service_id">
  <div class="row">

    <div class="col-md-6" style="color: #666666;">
        <!-- <div class="" style="margin-left: 45px">
          
          <div style="display: inline; margin-left: 15px"> </div>
        </div> -->
        <div class="row">
          <div class="col-md-4 text-right">
            <img style="margin-left: 74px" alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="60" height="60">
          </div>
          <div class="col-md-6" style="padding-top: 8px;"> 
            <input class="txt_bg" type="text" name="" id="service-name" value=""  placeholder="Enter Service Name" style="width: 260px;">
          </div>
        </div>
        <br><br>
        <div class="row">
          <div class="col-md-4 text-right" style="padding-top: 6px;">Service Cost <span class="sg">S$</span><span class="rm">RM</span></div>
          <div class="col-md-6"> <input class="txt_bg" id="service-cost" type="text" name="" value=""  placeholder="0" style="width: 260px;"></div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-4 text-right" style="padding-top: 6px;">Service Time (mins)</div>
          <div class="col-md-6"> <input class="txt_bg" id="service-duration" type="text" name="" value=""  placeholder="0" style="width: 260px;"></div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-4 text-right" style="padding-top: 6px;">Service Description</div>
          <div class="col-md-6"> <input class="txt_bg" id="service-description" type="text" name="" value=""  placeholder="Enter Service Description" style="width: 260px;"></div>
        </div>
      </div>

  		<div class="col-md-6" style="color: #777;" id="service-doctor-wap">
        <span style="padding-left: 22px; color: #777;">Who can provide the Service</span>
        <div style="width: 100%;" class="col-md-5">

          <div style="padding: 0px; width: 6%;" class="col-md-1">
            <input type="checkbox" id="service-doc-all">
          </div>          
          <div style="padding: 0px; width: 94%;" class="col-md-1">
            <label id="all-doc-lbl" style="padding-top: 13px; color: #1667AC;"><b>Select All Doctors</b></label>
          </div>

        </div>

        <?php if($doctors){

        foreach ($doctors as $value) {
        ?>

        <div style="width: 100%;" class="col-md-5">

          <div style="padding: 0px; width: 6%;" class="col-md-1">
            <input type="checkbox" id="{{$value->DoctorID}}" class="service-doc-list">
          </div>          
          <div style="padding: 0px; width: 94%;" class="col-md-1">
            <label id="{{$value->DoctorID}}-lbl" class="doc-lbl" style="padding-top: 13px; color: #777;">{{ $value->DocName}}</label>
          </div>

        </div>

        <?php } }?>

      </div>
  </div>

<?php } ?>

  <script type="text/javascript">
  	
  	jQuery(document).ready(function($) {

      
      $('.sg').show();
      $('.rm').hide();

  		$(document).on('click', '#delete-service', function(event) {
			var id = $('#h-service_id').val();
			
			$.ajax({
		      url: base_url+'setting/service/deleteServices',
		      type: 'POST',
		      data:{ id:id}
		    })
		    .done(function(data) {

          if (data == 0){

            alert('Can\'t be deleted, This Procedure Already in use !');

          }else{

            $('#alert_box').css('display', 'block');
            $('#alert_box').html('Updating...');
            
            setTimeout(function(){

              $('#alert_box').css('display', 'none');
              $('#main-tab-service').html(data);

            }, 1000);
        
          }

        });


      event.stopImmediatePropagation();
      return false;

		});

    // --------- Set Navigation bar height ------------------

    var page_height = $('#setting-nav-panel').height()+52;
    var win_height = $(window).height();

    // alert ('page - '+page_height+ ', window - '+win_height);

    if (page_height > win_height){

        $("#setting-navigation").height($('#setting-nav-panel').height()+52);
        // $("#profile-side-list").height($('#setting-nav-panel').height());
    }
    else{

        $("#setting-navigation").height($(window).height()-52);
        // $("#profile-side-list").height($(window).height()-52);
    }


  	});
  </script>