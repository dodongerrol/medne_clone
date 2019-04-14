<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/country_code.js?_={{ $date->format('U') }}"></script>
<br>

<div class="col-md-12 details-tab" style="padding: 0px;padding-right: 30px;">

	<div class="row col-md-12">
		<div class="col-md-3">

		<?php if(!empty($doctorDetails->image)){ ?>
				<span style="float: right; padding-right: 12px;">
					<img class="doctor-image" src="{{ $doctorDetails->image }}" width="85" height="85" style="cursor: pointer;">
				</span>

	    <?php } else { ?>

		        <span style="float: right; padding-right: 12px;">
					<img class="doctor-image" src="{{ URL::asset('assets/images/img-portfolio-place.png') }}" width="85" height="85" style="cursor: pointer;">
				</span>

	    <?php } ?>

	           	<input type="file" id="doctor-profile-image-file" name="file" style="display: none">

			<!-- <span style="float: right;">
			<img class="doctor-image" src="http://localhost/medicloud_v003/public/assets/images/img-portfolio-place.png" width="85" height="85" style="cursor: pointer;">
			</span> -->

		</div>
		<div class="col-md-8" style="padding-top: 15px;">
			<div class="col-md-12" style="padding: 0px;"><!-- <b style="font-size: large;">{{$doctorDetails->Name}}</b> -->
				<input type="text" id="doc-name" class="col-sm-1" value="{{$doctorDetails->Name}}" placeholder="Doctor Name" style=" width: 300px; font-weight: bold;">
			</div>
			<div class="col-md-12"></div>
		</div>
	</div>

	<div class="row col-md-12"><br>
		<div class="col-md-3" style="clear: both">
			<label class="detail-lbl">Qualification</label>
		</div>
		<div class="col-md-8">
			<input type="text" id="doc-qualification" class="col-sm-1" value="{{$doctorDetails->Qualifications}}" placeholder="Doctor Qualification" style=" width: 300px;">
		</div>
	</div>

	<div class="row col-md-12"><br>
		<div class="col-md-3" style="clear: both">
			<label class="detail-lbl">Designation</label>
		</div>
		<div class="col-md-8">
			<input type="text" id="doc-Specialty" class="col-sm-1" value="{{$doctorDetails->Specialty}}" placeholder="Doctor Designation" style=" width: 300px;">
		</div>
	</div>

	<?php

    $mobileCode = '';

    if ($doctorDetails->phone_code != ''){

        $mobileCode = $doctorDetails->phone_code;

    }else {

        $mobileCode = '+65';

    }
    ?>


	<div class="row col-md-12"><br>
		<div class="col-md-3" style="clear: both">
			<label class="detail-lbl">Mobile</label>
		</div>
		<div class="col-md-8">
			<!-- <input type="text" id="doc-mobile" class="col-sm-1" placeholder="Mobile" value="{{$doctorDetails->Phone}}" style=" width: 300px;"> -->
			<div id="doc-code-dropdown" class="dropdown input-group input-group-lg" style="width: 330px; border: 1px solid #d9d9d9; border-radius: 5px; color: #686868; background: white;">
	         	<span id="doc-mobile-code" class = "input-group-addon dropdown-toggle" data-toggle="dropdown" style="border-right: 1px solid #d9d9d9 !important; color: #686868 !important; background: white; font-size: 14px !important; height: 20px; cursor: pointer; width: 40px;">{{ $mobileCode }}</span>
	         	<input id="doc-mobile" type = "text" class ="form-control" placeholder = "Phone Number" value="{{$doctorDetails->Phone}}" style="color: #686868 !important; background: white !important; font-size: 14px !important; border-radius: 5px;">
	         	<ul class="dropdown-menu" id="doc-mobile-codes" style="width: 330px; max-height: 180px; overflow-y: auto; overflow-x: hidden;">
	         	
	         	</ul>
	      	</div>
		</div>
	</div>

	<div class="row col-md-12"><br>
		<div class="col-md-3" style="clear: both">
			<label class="detail-lbl">Email</label>
		</div>
		<div class="col-md-8">
			<input type="text" id="doc-email" class="dropdown-btn col-sm-1" placeholder="Example@gmail.com" value="{{$doctorDetails->Email}}" style=" width: 300px;">
		</div>
	</div>

	<div class="row col-md-12 "><br>
		<div class="col-md-3" style="clear: both">
			<label class="detail-lbl">CC Emails to</label>
		</div>
		<div class="col-md-8">
			<input type="text" id="doc-cc-email" class="dropdown-btn col-sm-1" value="{{$doctorDetails->cc_email}}" placeholder="Doctor CC E-Mail" style=" width: 300px;">
		</div>
	</div>
	<div class="row col-md-12 line-break"><br>
		<div class="col-md-3" style="clear: both">
		</div>
		<div class="col-md-8">
			<button class="resend-email-btn staff-btn" id="btn-doc-update" style="margin-left: 10px;">Update</button>
		</div>
		
	</div>

	<div class="row col-md-12 line-break " style="display: none;"><br>
		<div class="col-md-3" style="clear: both">
			<label class="detail-lbl">Doctor Login</label>
		</div>
		<div class="col-md-8" style="padding: 0px;">
			<div class="col-md-12"><input id="doctor-login-toggle" <?php {{ if($doctorDetails->check_login == 1 ){ echo "checked"; } }}?> type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info"  class="abc doc-toggel" value="{{$doctorDetails->check_login}}"></div>
			<div class="col-md-12" id="login-on">
				<span class="col-md-12" style="padding: 13px; padding-left: 0px; color: #666666;">Setup information is sent to <strong id="">{{$doctorDetails->Email}}</strong></span>
				<button class="resend-email-btn staff-btn" id="btn-doc-resend">Resend</button>
			</div>
		</div>
	</div>

	<div class="row col-md-12 line-break "><br>
		<!-- <div class="col-md-3" style="clear: both">
			<label class="detail-lbl">Requires PIN</label>
		</div>
		<div class="col-md-8 bottom-padding">
			<input id="requires-pin-toggle" <?php {{ if($doctorDetails->check_pin == 1 ){ echo "checked"; } }}?>  type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info" class="abc doc-toggel" value="{{$doctorDetails->check_pin}}">
		</div> -->

		<?php if ($doctorDetails->pin == '0000') { ?>

		<div id="set-new-pin">
			<div class="col-md-3" style="clear: both">
				<label class="detail-lbl">PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="doc-pin" class="dropdown-btn col-sm-1" placeholder="4 digit PIN" style=" width: 300px;">&nbsp;&nbsp;&nbsp;
				<button class="submit-doctor-pin staff-btn" id="btn-doc-pin">Enter</button>
			</div>
		</div>

		<?php } else {?>

		<div id="change-new-pin">
			<div class="col-md-3" style="clear: both">
				<label class="detail-lbl">Existing PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="doc-old-pin" class="dropdown-btn col-sm-1" placeholder="" style=" width: 150px;">
			</div>
			<div class="col-md-3" style="clear: both">
				<label class="detail-lbl">New PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="doc-newpin" class="dropdown-btn col-sm-1" placeholder="" style=" width: 150px;">
			</div>
			<div class="col-md-3" style="clear: both;">
				<label class="detail-lbl">Re-Enter PIN</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="password" id="doc-repin" class="dropdown-btn col-sm-1" placeholder="" style=" width: 150px;">
			</div>
			<div class="col-md-3" style="clear: both">
				<label class="detail-lbl">&nbsp;</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<button class="submit-doctor-pin staff-btn" id="btn-doc-pin-update">Update</button>
			</div>
		</div>

		<?php }?>

	</div>

	<div class="row col-md-12 line-break "><br>
		<div class="col-md-3" style="clear: both">
			<label class="detail-lbl">Google Sync</label>
		</div>
		<div class="col-md-8 bottom-padding">
			<input id="google-sync-toggle" <?php {{ if($doctorDetails->check_sync == 1 ){ echo "checked"; } }}?>  type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info" class="abc doc-toggel" value="{{$doctorDetails->check_sync}}">
		</div>

		<div id="GC-on">
			<div class="col-md-3" style="clear: both">
				<label class="detail-lbl">Gmail</label>
			</div>
			<div class="col-md-8 bottom-padding">
				<input type="text" id="doc-gmail" class="dropdown-btn col-sm-1" value="{{$doctorDetails->gmail}}" placeholder="Gmail Address" style=" width: 300px;">&nbsp;&nbsp;&nbsp;
				<?php 

					if ($doctorDetails->gmail!=null) {
						$status = "disabled='disabled'";
					}else {
						$status = "";
					}

				 ?>

				<button class="submit-doctor-pin staff-btn" <?php echo $status; ?> id="btn-send-Google-request" style="width: 100px; height: 40px;">Send Request</button>&nbsp;&nbsp;&nbsp;
				
			</div>
			<div class="col-md-12">
				<div class="col-md-3" style="clear: both">
					<label class="detail-lbl">&nbsp;</label>
				</div>
				<div class="col-md-6" style="padding: 0">
					<button  class="submit-doctor-pin staff-btn" id="btn-remove-Google-request" style="width: 140px; height: 40px;margin-left: 5px;">Remove Credentials</button>
				</div>
				
			</div>
			<div class="col-md-3" style="clear: both">
				<label class="detail-lbl">&nbsp;</label>
			</div>

			<?php 

				if ($doctorDetails->gmail!=null && $doctorDetails->token!=null) {
					$status = 'Active';
				}else if($doctorDetails->gmail!=null && $doctorDetails->token==null) {
					$status = 'Pending';
				}else {
					$status = 'InActive';
				}

			 ?>

			<div class="col-md-8 bottom-padding">
				<span><b>Status :</b><strong style="color: #CAA021;"> {{$status}}</strong></span>
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



	$("[data-toggle='toggle']").bootstrapToggle('destroy')                 
    $("[data-toggle='toggle']").bootstrapToggle();

	login_block = $("#doctor-login-toggle").prop('checked');
	// pin_block = $("#requires-pin-toggle").prop('checked');
	GC_block = $("#google-sync-toggle").prop('checked');

	toggle ();

		$('#doctor-login-toggle').change(function() {

		login_block = $("#doctor-login-toggle").prop('checked');
		toggle ();
		
		})

		$('#google-sync-toggle').change(function() {

			GC_block = $("#google-sync-toggle").prop('checked');
			toggle ();
		})


	function toggle (){

		if (login_block){
			$("#login-on").css("display", "block");
			
		}
		else{
			$("#login-on").css("display", "none");

		}

		if (GC_block){
			$("#GC-on").css("display", "block");
		}
		else{
			$("#GC-on").css("display", "none");
		}
	}

	$('#btn-doctor-delete').popover({

		html: 'true',
	    title : 'Are you sure ?',
	    content : '<button id="delete-doctor" class="btn btn-danger">Delete</button> <button class="btn" id="doctor-delete-cancel" style="background: #FFFFFD; border: 1px solid #B9B8B8;">Cancel</button>'

	});

	});

	$('#doc-code-dropdown').on('shown.bs.dropdown', function () {
  
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
$('#doc-code-dropdown').on('hide.bs.dropdown', function () {

    var $this = $(this);

    $this.find("li").each(function(idx,item){

        $(item).addClass("show");
        $(item).removeClass("hide");
    });
    
    $(document).unbind("keypress");

})

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
    	#doc-mobile-codes li:hover {
			cursor: pointer;
			background: #1997D4 !important;
    		color: white !important;
		}
	</style>

