<br>

<div class="clinic-pass-container">

<div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
<span style="padding-top: 15px; font-size: large; font-weight: bold;">Change Your Password Here</span>
</div>
<br><br><br>

<div class="row">
	<div class="col-md-3" style="padding: 0px;">
		<img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="75" height="75" style="float: right;">
	</div>	
	<div class="col-md-8" style="padding-top: 20px;">
		<b style="font-size: large;">{{ $clinicdetails['name'] }}</b>
	</div>
	
</div>
<br><br>
<!-- <hr> -->
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;">
		<label class="profile-lbl">Current Password</label>
	</div>	
	<div class="col-md-8">
		<input type="password" id="old-cinic-password" class="dropdown-btn col-sm-1" style="height: 15px; width: 300px;">
	</div>
	
</div>
<br>
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;">
		<label class="profile-lbl">New Password</label>
	</div>	
	<div class="col-md-8">
		<input type="password" id="new-cinic-password" class="dropdown-btn col-sm-1" style="height: 15px; width: 300px;">
	</div>
	
</div>
<br>
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;">
		<label class="profile-lbl">Confirm Password</label>
	</div>	
	<div class="col-md-8">
		<input type="password" id="confirm-cinic-password" class="dropdown-btn col-sm-1" style="height: 15px; width: 300px;">
	</div>
	
</div>
<br>
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;">
		<label class="profile-lbl">&nbsp;</label>
	</div>	
	<div class="col-md-8">
		<button class="resend-email-btn staff-btn" id="clinic-password-update" style="width: 125px;">Update Changes</button>
	</div>
	
</div>
<br>

</div>

<style>

.profile-lbl {
    float: right;
    padding-right: 15px;
    padding-top: 10px;
    font-size: 14px;
    color: #666666;
    text-align: right;
}

</style>


<script type="text/javascript">
	jQuery(document).ready(function($) {

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

	});
</script>