
{{ HTML::script('assets/settings/payments/payments.js') }}
{{ HTML::style('assets/css/font-awesome.min.css') }}
<!-- {{ HTML::script('assets/bower_components/moment/min/moment.min.js') }} -->


<div class="trans-container" style="padding: 0px;">

	<div id="payments-side-list" style="padding: 0px; background: #B0E2F9;">

		<!-- Configure side list -->

		<div class="col-md-12" style="padding: 0px; padding-top: 20px; padding-bottom: 20px; border-bottom: 1px solid #D0D0D0;">
			<div class="col-md-11">
				<div class="col-md-6"><b>Payments</b></div>
			</div><br><br>

			<div id="Configure-list" class="">
				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/history.png') }}" width="30" height="35" style="float: left;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;"><b id="transaction-history" class="payments-settings ">HISTORY</b></div>
				</div>

				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/invoice.png') }}" width="30" height="35" style="float: left;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;"><b id="transaction-invoice" class="payments-settings ">INVOICE</b></div>
				</div>

				<div class="col-md-12 staff-doctor">
					<img src="{{ URL::asset('assets/images/Statement of Account.png') }}" width="30" height="35" style="float: left;">
					<div style="padding: 10px 0 10px 10px;display: inline-block;"><b id="transaction-statement" class="payments-settings ">STATEMENT OF ACCOUNT</b></div>
				</div>

				<!-- <div class="col-md-11 staff-doctor">
					<span class="col-md-1">
					<img src="{{ URL::asset('assets/images/Statement of Account.png') }}" width="30" height="35">
					</span>
					<div class="col-md-7" style="padding-top: 9px;"><b id="view-transaction-preview" class="payments-settings ">TRANSACTION PREVIEW</b></div>
				</div> -->
			</div>

		</div>

	</div>


	<div id="payments-detail-wrapper" class="detail-wrapper" style="padding: 0px;">
		
	</div>
	
</div>

