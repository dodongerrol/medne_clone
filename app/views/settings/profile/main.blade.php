
{{ HTML::script('assets/settings/profile/profile.js') }}
{{ HTML::style('assets/settings/profile/profile.css') }}
<div id="config_alert_box">
    message goes here
</div>

<div id='cover-spin'></div>

<div class="col-md-12" style="padding: 0px;">

	<div id="profile-side-list" style="">

		<!-- Configure side list -->

		<div class="col-md-12" style="padding: 0px; padding-top: 20px; padding-bottom: 20px; border-bottom: 1px solid #D0D0D0;">
			<div class="col-md-12">
				<div class="col-md-6"><b>Configure</b></div>
			</div><br><br>

			<div id="Configure-list" class="">
				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/Clinic Details.png') }}" width="30" height="35" style="float: left;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;">
						<b id="clinic-details" class="profile-settings" style="color: black;">CLINIC DETAILS</b>
					</div>
				</div>
				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/Business Hours.png') }}" width="30" height="30" style="float: left;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;">
						<b id="clinic-hours" class="profile-settings">OPERATING HOURS</b>
					</div>
				</div>
				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/Password.png') }}" width="30" height="20" style="float: left;margin-top: 8px;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;"><b id="clinic-password" class="profile-settings">PASSWORD</b></div>
				</div>
				<div class="col-md-11 staff-doctor">
					<img src="{{ URL::asset('assets/images/Payment Details.png') }}" width="30" height="31" style="float: left;margin-top: 8px;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;"><b id="clinic-payment-details" class="profile-settings">PAYMENT DETAILS</b></div>
				</div>
			</div>

		</div>

		<!-- Integrate side list -->

		<div class="col-md-12" style="padding: 0px; padding-top: 20px; padding-bottom: 20px;">
			<div class="col-md-11">
				<div class="col-md-6"><b>Integrate</b></div>
			</div><br><br>

			<div id="Integrate-list" class="">

				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/Widget.png') }}" width="30" height="30" style="float: left;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;"><b id="website" class="profile-settings">WIDGET</b></div>
				</div>
				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/qr-code.png') }}" width="30" height="30" style="float: left;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;"><b id="qr" class="profile-settings">QR CODE</b></div>
				</div>
			</div>
		</div>

	</div>


	<div id="profile-detail-wrapper" class="detail-wrapper" style="padding: 0px;"></div>
	<div class="operatingHours-div">
			@include('settings.profile.clinic-hours-main')
	</div>
</div>