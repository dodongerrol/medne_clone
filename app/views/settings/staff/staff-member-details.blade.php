
{{ HTML::script('assets/dashboard/country_code.js') }}

<br>

<div class="container">

<div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
<span style="float: left; padding-top: 15px; font-size: large; font-weight: bold;">Details</span>
<span style="float: right; padding-top: 15px;">
        <a id="btn-staff-delete" href="#"  data-toggle="popover" data-placement="left" data-trigger="focus"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color: black;"></span></a>
      </span>	
</div>

<br><br><br><br>
	
<div class="col-md-12" style="padding: 0px;">

	<div class="col-md-2">
		<span style="float: right;"><img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="75" height="75"></span>
	</div>
	<div class="col-md-8" style="padding-top: 15px;">
		<div class="col-md-12"><b style="font-size: large;">{{$staff[0]->name}}</b></div>
		<div class="col-md-12"></div>
	</div>

	<div class="row col-md-12"><br>
		<div class="col-md-2" style="clear: both">
			<label class="detail-lbl">Qualification</label>
		</div>
		<div class="col-md-8">
			<input type="text" id="staff-qualification" class="dropdown-btn col-sm-1" value="{{$staff[0]->qualifcation}}" placeholder="Staff Qualification" style="height: 15px; width: 300px;">
		</div>
	</div>

	<?php

    $mobileCode = '';

    if ($staff[0]->phone_code != ''){

        $mobileCode = $staff[0]->phone_code;

    }else {

        $mobileCode = '+65';

    }
    ?>


	<div class="row col-md-12"><br>
		<div class="col-md-2" style="clear: both">
			<label class="detail-lbl">Mobile</label>
		</div>
		<div class="col-md-8">
			<div id="staff-code-dropdown" class = "dropdown input-group input-group-lg" style="width: 330px; border: 1px solid #d9d9d9; border-radius: 5px; color: #686868; background: white;">
	         	<span id="staff-mobile-code" class = "input-group-addon dropdown-toggle" data-toggle="dropdown" style="border-right: 1px solid #d9d9d9 !important; color: #686868 !important; background: white; font-size: 14px !important; height: 20px; cursor: pointer; width: 40px;">{{ $mobileCode }}</span>
	         	<input id="staff-mobile" type = "text" class ="form-control" placeholder = "Phone Number" value="{{$staff[0]->phone}}" style="color: #686868 !important; background: white !important; font-size: 14px !important; border-radius: 5px;">
	         	<ul class="dropdown-menu" id="staff-mobile-codes" style="width: 330px; max-height: 180px; overflow-y: auto; overflow-x: hidden;">
	         	
	         	</ul>
	      	</div>
		</div>
	</div>

	<div class="row col-md-12"><br>
		<div class="col-md-2" style="clear: both">
			<label class="detail-lbl">Email</label>
		</div>
		<div class="col-md-8">
			<input type="text" id="staff-email" class="dropdown-btn col-sm-1" placeholder="Example@gmail.com" value="{{$staff[0]->email}}" style="height: 15px; width: 300px;">
		</div>
	</div>

	<div class="row col-md-12 "><br>
		<div class="col-md-2" style="clear: both">
			<label class="detail-lbl">CC Emails to</label>
		</div>
		<div class="col-md-8">
			<input type="text" id="staff-cc-email" class="dropdown-btn col-sm-1" value="{{$staff[0]->cc_email}}" placeholder="Staff CC E-Mail" style="height: 15px; width: 300px;">
		</div>
	</div>
	<div class="row col-md-12 line-break"><br>
		<div class="col-md-2" style="clear: both">
		</div>
		<div class="col-md-8">
		<button class="resend-email-btn staff-btn" id="btn-staff-update">Update</button>
		</div>
	</div>


	<div class="row col-md-12 line-break "><br>
		<!-- <div class="col-md-2" style="clear: both">
			<label class="detail-lbl">Requires PIN</label>
		</div>
		<div class="col-md-8 bottom-padding">
			<input id="pin-toggle"  type="checkbox" <?php {{ if($staff[0]->check_login == 1 ){ echo "checked"; } }}?> data-toggle="toggle" data-size="mini" data-onstyle="info" class="abc" value="{{$staff[0]->check_login}}">
		</div> -->

		<?php if ($staff[0]->pin_no == null) { ?>

		<div id="set-pin">
			<div class="col-md-2" style="clear: both">
				<label class="detail-lbl">PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="staff-pin" class="dropdown-btn col-sm-1" placeholder="4 digit PIN" style="height: 15px; width: 300px;">&nbsp;&nbsp;&nbsp;
				<button class="submit-doctor-pin staff-btn" id="btn-staff-pin">Enter</button>
			</div>
		</div>

		<?php } else {?>

		<div id="change-pin">
			<div class="col-md-2" style="clear: both">
				<label class="detail-lbl">Existing PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="staff-old-pin" class="dropdown-btn col-sm-1" placeholder="" style="height: 15px; width: 150px;">
			</div>
			<div class="col-md-2" style="clear: both">
				<label class="detail-lbl">New PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="staff-newpin" class="dropdown-btn col-sm-1" placeholder="" style="height: 15px; width: 150px;">
			</div>
			<div class="col-md-2" style="clear: both;">
				<label class="detail-lbl">Re-Enter PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="staff-repin" class="dropdown-btn col-sm-1" placeholder="" style="height: 15px; width: 150px;">
			</div>
			<div class="col-md-2" style="clear: both">
				<label class="detail-lbl">&nbsp;</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<button class="submit-doctor-pin staff-btn" id="btn-staff-pin-update">Update</button>
			</div>
		</div>

		<?php }?>

	</div>

</div>
</div>


<script>

	jQuery(document).ready(function($) {

	// --------- Set Navigation bar height ------------------

    var page_height = $('#detail-wrapper').height()+52;
    var win_height = $(window).height();

    // alert ('page - '+page_height+ ', window - '+win_height);

    if (page_height > win_height){

        $("#setting-navigation").height($('#detail-wrapper').height()+52);
        $(".staff-side-list").height($('#detail-wrapper').height()+52);
    }
    else{

        $("#setting-navigation").height($(window).height()-52);
        $(".staff-side-list").height($(window).height()-52);
    }

    $("#staff-doctor-list").height(($('.staff-side-list').height() / 2) -75);
	$("#staff-list").height(($('.staff-side-list').height() / 2) -75);

	$('#btn-staff-delete').popover({

		html: 'true',
	    title : 'Are you sure ?',
	    content : '<button id="delete-staff" class="btn btn-danger">Delete</button> <button class="btn" id="staff-delete-cancel" style="background: #FFFFFD; border: 1px solid #B9B8B8;">Cancel</button>'

	});


	$('#staff-code-dropdown').on('shown.bs.dropdown', function () {
  
    var $this = $(this);

    $(document).keypress(function(e){

      var key = String.fromCharCode(e.which);

	      $this.find("li").each(function(idx,item){

	        $(item).addClass("hide");
	        $(item).removeClass("show");

	        if ($(item).text().charAt(0).toLowerCase() == key) {

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

	$('#staff-code-dropdown').on('hide.bs.dropdown', function () {

	    var $this = $(this);

	    $this.find("li").each(function(idx,item){

	        $(item).addClass("show");
	        $(item).removeClass("hide");
	    });
	    
	    $(document).unbind("keypress");

	})

	});

</script>


<style>
		.toggle.btn {
			min-width: 40px;
			min-height: 25px; 
		}
		.btn-info {
    		background-image: -webkit-linear-gradient(top,#1b9bd7 0,#1b9bd7 100%);
    	}
    	.btn-info:focus, .btn-info:hover {
    		background-color: #1b9bd7;
    		    background-position: 0px;
    	}
    	#staff-mobile-codes li:hover {
			cursor: pointer;
			background: #1997D4 !important;
    		color: white !important;
		}

</style>

