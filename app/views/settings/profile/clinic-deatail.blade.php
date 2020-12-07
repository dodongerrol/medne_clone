
<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/country_code.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/settings/profile/clinic_MRT.js?_={{ $date->format('U') }}"></script>
<br>

<div class="clinic-detail-container">

<div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
<span style="padding-top: 15px; font-size: large; font-weight: bold;">Configure Your Medicloud Booking Page</span>
</div>

<br><br><br><br>

<div class="table-responsive">
  <table class="table">
<!--     <thead>
      <tr>
        <th>#</th>
        <th>Firstname</th>
      </tr>
    </thead> -->
    <tbody>

    <tr>
        <td><label class="profile-lbl" style="padding-top: 25px;">Logo / Photo</label></td>
        <td colspan="3">
        	<span><img id="clinic-update-image" class="clinic-image" src="{{ $clinicdetails['image'] }}" width="100" height="100" style="cursor: pointer;"></span>
           	<input type="file" id="clinic-profile-image-file" name="file" style="display: none">
        </td>
    </tr>

    <tr>
        <td><label class="profile-lbl">Clinic Name</label></td>
        <td colspan="3">
        	<input type="text" id="cinic-name" class="dropdown-btn col-sm-1" value="{{ $clinicdetails['name'] }}" placeholder="Clinic Name">
        </td>
    </tr>

    <?php
		if (!empty($clinicdetails['clinic_type'])) {

			foreach ($clinic_type as $val) {

			    if ($clinicdetails['clinic_type'] == $val->ClinicTypeID){

			    	$Speciality = $val->Name;
				}
			}
		}else{

		 	$Speciality = '';
		}?>

    <tr>
        <td><label class="profile-lbl">Speciality</label></td>
        <td colspan="3">
			<div class="dropdown" id="service-dropdown">
	    		<button class="clinic-speciality dropdown-btn dropdown-toggle" id="{{ $clinicdetails['clinic_type'] }}" type="button" data-toggle="dropdown" style="height: 42px; text-align: left; padding-left: 15px;">
	    		<span id="clinic-service-name">{{ $Speciality }}</span>&nbsp;&nbsp;&nbsp;
	  		</div>
        </td>
    </tr>

    <?php
    $MRT = '';
    $colorcode = '';
    $val ='';

    if ($clinicdetails['MRT'] != ''){

        $MRT = $clinicdetails['MRT'];
        $colorcode = '#686868';
        $val = $clinicdetails['MRT'];

    }else {

        $MRT = 'Select MRT';
        $colorcode = '#A9A9A9';
        $val = '';

    }
    ?>

    <tr>
        <td><label class="profile-lbl">MRT</label></td>
        <td colspan="3">
        	<!-- <input type="text" id="clinic-MRT" class="dropdown-btn col-sm-1" placeholder="MRT" value="{{ $clinicdetails['MRT'] }}" style=" width: 93%;"> -->
        	<div class="dropdown" id="MRT-dropdown">
	    		<button class="clinic-MRT dropdown-btn dropdown-toggle" id="{{ $val }}" type="button" data-toggle="dropdown" style="height: 42px; text-align: left; padding-left: 15px;">
	    		<span id="MRT-name" style="color: {{$colorcode}} ">{{ $MRT }}</span>&nbsp;&nbsp;&nbsp;
	    		<span class="caret" style="float: right; margin: 15px 5px 0 0;"></span></button>
	    		<ul class="dropdown-menu" role="menu" aria-labelledby="menu1" id="clinic-MRT-list" style="width: 420px; max-height: 210px; overflow-y: auto; overflow-x: hidden;">
	    		</ul>
	  		</div>
            <!-- <select class="dropdown-btn clinic-MRT" id="" name="clinic-MRT"  aria-required="true" aria-invalid="false" style="width: 95%; height: 47px;">
                <option value="{{ $val }}" selected>{{ $MRT }}</option>
            </select> -->
        </td>
    </tr>

    <tr>
        <td><label class="profile-lbl">Address</label></td>
        <td colspan="3">
        	<input type="text" id="clinic-address" class="dropdown-btn col-sm-1" placeholder="Address" value="{{ $clinicdetails['address'] }}" >
        </td>
    </tr>

    <tr>
       	<td><label class="profile-lbl">Street</label></td>
        <td>
        	<input type="text" id="clinic-street" class="dropdown-btn" value="{{ $clinicdetails['city'] }}" placeholder="Street" style="height: 32px;">
        </td>
        <td>
            <label class="profile-lbl">State</label>
        </td>
        <td>
        	<input type="text" id="clinic-state" class="dropdown-btn" value="{{ $clinicdetails['state'] }}" placeholder="State" style="height: 32px;">
        </td>
    </tr>

    <tr>
       	<td><label class="profile-lbl">District</label></td>
        <td>
        	<input type="text" id="clinic-district" class="dropdown-btn" value="{{$clinicdetails['district']}}" placeholder="District" style="height: 32px;">
        </td>
        <td><label class="profile-lbl">Postal Code</label></td>
        <td>
        	<input type="text" id="clinic-postal_code" class="dropdown-btn" value="{{ $clinicdetails['postal'] }}" placeholder="Postal&nbsp;Code" style="height: 32px;">
        </td>
    </tr>

    <tr>
       	<td><label class="profile-lbl">Country</label></td>
        <td>
        	<input type="text" id="clinic-country" class="dropdown-btn" value="{{ $clinicdetails['country'] }}" placeholder="Country" style="height: 32px;">
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td><label class="profile-lbl">Latitude</label></td>
        <td>
        	<input type="text" id="clinic-lat" class="dropdown-btn" value="{{ $clinicdetails['lat'] }}" placeholder="Latitude" style="height: 32px;">
        </td>
        <td><label class="profile-lbl">Longitude</label></td>
        <td>
        	<input type="text" id="clinic-lng" class="dropdown-btn" value="{{ $clinicdetails['lng'] }}" placeholder="longitude" style="height: 32px;">
        </td>
    </tr>

    <tr>
        <td><label class="profile-lbl">Description</label></td>
        <td colspan="3">
        	<textarea id="clinic-description" placeholder="Description" rows="4" style="border: 1px solid #d9d9d9; border-radius: 5px; padding-right: 10px; color: #686868; background: white;">{{ $clinicdetails['description'] }}</textarea>
        </td>
    </tr>

    <?php

    $phone = $clinicdetails['phone'];
    $code = $clinicdetails['code'];
    $mobileCode = $clinicdetails['code'];
    
    // if ($clinicdetails['phone_code'] != ''){
    //     $code = $clinicdetails['phone_code'];
    //     $length = strlen($code);
    //     $phone = substr($clinicdetails['phone'],$length);
    //     $mobileCode = $clinicdetails['phone_code'];
    // }else {

    //     $mobileCode = '+65';
    //     $phone = $clinicdetails['phone'];
    // }

    if($phone[0] == '+'){
        if($phone.strpos($phone, '+65') > -1){
          $phone = str_replace('+65', '', $phone);
        }
        if($phone.strpos($phone, '+60') > -1){
          $phone = str_replace('+60', '', $phone);
        }
        if($phone.strpos($phone, '+65') < 0 && $phone.strpos($phone, '+60') < 0){
          $phone = str_replace('+', '', $phone);
        }
      }else{
        $temp_code = substr($phone, 0, 2);
        echo $temp_code;
        if($temp_code == '65' || $temp_code == '60'){
          $phone = substr($phone, 2);
        }
      }
    ?>

    <tr>
        <td><label class="profile-lbl">Phone</label></td>
        <td colspan="3">

            <!-- <div class="input-group my-group" style="width: 98%; border: 1px solid #d9d9d9; border-radius: 5px; color: #686868; background: white;">
                <select id="clinic-phone-code" class="selectpicker form-control clinic-MRT" data-live-search="true" style="width: 20%; border-right: 1px solid #d9d9d9 !important; color: #686868 !important; background: white !important; font-size: 14px !important;  cursor: pointer;">
                        <option value="{{ $mobileCode }}" selected><span>Sri Lanka</span><span class="pull-right">+94</span></option>
                </select>
                <input id="clinic-Phone" type = "text" class ="form-control" placeholder = "Phone Number" value="{{ $phone }}" style="color: #686868 !important; background: white !important; font-size: 14px !important; width: 80%;">
            </div> -->


        	<div id="code-dropdown" class = "dropdown input-group input-group-lg" style="border: 1px solid #d9d9d9; border-radius: 5px; color: #686868; background: white;">
	         	<span id="clinic-phone-code" class = "input-group-addon dropdown-toggle" data-toggle="dropdown" style="border-right: 1px solid #d9d9d9 !important; color: #686868 !important; background: white; font-size: 14px !important;  cursor: pointer; width: 50px;">{{ $code }}</span>
	         	<input id="clinic-Phone" type = "text" class ="form-control" placeholder = "Phone Number" value="{{ $phone }}" style="color: #686868 !important; background: white !important; font-size: 14px !important; border-radius: 5px;">
	         	<ul class="dropdown-menu" id="clinic-phone-codes" style="width: 350px; max-height: 210px; overflow-y: auto; overflow-x: hidden;">

	         	</ul>
	      	</div>
        </td>
    </tr>
    <tr>
        <td><label class="profile-lbl">Communication Email</label></td>
        <td>
        	<input type="text" id="clinic-communication-email" class="dropdown-btn" value="{{ $clinicdetails['communication_email'] }}" placeholder="Communication Email" style="height: 32px;">
        </td>
    </tr>
    <tr>
        <td><label class="profile-lbl">Website</label></td>
        <td>
        	<input type="text" id="clinic-website" class="dropdown-btn" value="{{ $clinicdetails['website'] }}" placeholder="Website" style="height: 32px;">
        </td>
    </tr>

    <tr>
        <td><label class="profile-lbl">Personalized heading</label></td>
        <td colspan="3">
        	<input type="text" id="clinic-authorize" class="dropdown-btn col-sm-1" placeholder="Personalized heading" value="{{ $clinicdetails['custom_title'] }}" style="">
        </td>
    </tr>

    <tr>
        <td><label class="profile-lbl">Personalized Message</label></td>
        <td colspan="3">
        	<textarea id="clinic-Msg" placeholder="Personalized Message" rows="4" style="border: 1px solid #d9d9d9; border-radius: 5px; padding-right: 10px; color: #686868; background: white;">{{ $clinicdetails['message'] }}</textarea>
        </td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td colspan="3">
        	<button class="resend-email-btn staff-btn" id="btn-clinic-detail-update" style="width: 115px;">Update Changes</button>
        </td>
    </tr>


    <!-- <tr>
       	<td>1</td>
        <td>Anna</td>
        <td>1</td>
        <td>Anna</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">Anna</td>
    </tr> -->

    </tbody>
  </table>
  </div>




</div>

<style type="text/css">

	.profile-lbl {
		float: right;
	    padding-right: 15px;
	    padding-top: 10px;
	    font-size: 14px;
	    color: #666666;
	    text-align: right;
	}
	#clinic-phone-codes li:hover, #clinic-MRT-list li:hover, #clinic-type-list li a:hover, .clinic-MRT option:hover {
		cursor: pointer;
		background: #1997D4 !important;
    	color: white !important;
	}
    select > option:hover{ background-color: red; }
</style>

<script type="text/javascript">

    // --------- Set Navigation bar height ------------------

    var page_height = $('#profile-detail-wrapper').height()+52;
    var win_height = $(window).height()

    if (page_height > win_height){

        $("#setting-navigation").height($('#profile-detail-wrapper').height()+52);
        $("#profile-side-list").height($('#profile-detail-wrapper').height()+52);
    }
    else{

        $("#setting-navigation").height($(window).height()-52);
        $("#profile-side-list").height($(window).height()-52);
    }


$('#service-dropdown').on('shown.bs.dropdown', function () {

    var $this = $(this);
    // attach key listener when dropdown is shown
    $(document).keypress(function(e){

      // get the key that was pressed
      var key = String.fromCharCode(e.which);
      // look at all of the items to find a first char match
      $this.find("li").each(function(idx,item){
        $(item).addClass("hide"); // clear previous active item
        $(item).removeClass("show");

        if ($(item).text().charAt(0).toLowerCase() == key) {
          // set the item to selected (active)
          $(item).addClass("show");
          $(item).removeClass("hide");
        }
        else{
            $(item).addClass("hide");
            $(item).removeClass("show");
        }
      });

    });

})

// unbind key event when dropdown is hidden
$('#service-dropdown').on('hide.bs.dropdown', function () {

    var $this = $(this);

    $this.find("li").each(function(idx,item){

        $(item).addClass("show");
        $(item).removeClass("hide");
    });

    $(document).unbind("keypress");

})


$('#MRT-dropdown').on('shown.bs.dropdown', function () {

    var $this = $(this);
    // attach key listener when dropdown is shown
    $(document).keypress(function(e){

      // get the key that was pressed
      var key = String.fromCharCode(e.which);
      // look at all of the items to find a first char match
      $this.find("li").each(function(idx,item){
        $(item).addClass("hide"); // clear previous active item
        $(item).removeClass("show");

        if ($(item).text().charAt(0).toLowerCase() == key) {
          // set the item to selected (active)
          $(item).addClass("show");
          $(item).removeClass("hide");
        }
        else{
            $(item).addClass("hide");
            $(item).removeClass("show");
        }
      });

    });

})

// unbind key event when dropdown is hidden
$('#MRT-dropdown').on('hide.bs.dropdown', function () {

    var $this = $(this);

    $this.find("li").each(function(idx,item){

        $(item).addClass("show");
        $(item).removeClass("hide");
    });

    $(document).unbind("keypress");

})

$('#code-dropdown').on('shown.bs.dropdown', function () {

    var $this = $(this);
    // attach key listener when dropdown is shown
    $(document).keypress(function(e){

      // get the key that was pressed
      var key = String.fromCharCode(e.which);
      // look at all of the items to find a first char match
      $this.find("li").each(function(idx,item){
        $(item).addClass("hide"); // clear previous active item
        $(item).removeClass("show");

        if ($(item).text().charAt(0).toLowerCase() == key) {
          // set the item to selected (active)
          $(item).addClass("show");
          $(item).removeClass("hide");
        }
        else{
            $(item).addClass("hide");
            $(item).removeClass("show");
        }
      });

    });

})

// unbind key event when dropdown is hidden
$('#code-dropdown').on('hide.bs.dropdown', function () {

    var $this = $(this);

    $this.find("li").each(function(idx,item){

        $(item).addClass("show");
        $(item).removeClass("hide");
    });

    $(document).unbind("keypress");

})



</script>
