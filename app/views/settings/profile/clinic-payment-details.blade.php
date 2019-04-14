@if($invoice_required)
{{ HTML::script('assets/settings/profile/profile.js') }}
@endif
<br>
<input type="hidden" id="clinicID" value="{{$clinicdetails['clinicid']}}">
<input type="hidden" name="invoice_status" id="invoice_status" value="{{$invoice_required}}">
<!-- {{var_dump($clinicdetails)}} -->
<div class="payment-details-container">

<div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
@if(!$invoice_required)
<span style="padding-top: 15px; font-size: large; font-weight: bold;">Enter Your Payment Details</span>
@else
<span style="padding-top: 15px; font-size: large; font-weight: bold;">Enter Your Payment Details To View Invoice</span>
@endif
</div>
<br><br><br><br>	
<!-- <hr> -->
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;text-align: right;">
		<label class="profile-lbl">Company Name</label>
	</div>	
	<div class="col-md-8">
		<input type="text" id="bank-name" class="dropdown-btn col-sm-1" style="height: 15px; width: 300px;">
	</div>
	
</div>
<br>
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;text-align: right;">
		<label class="profile-lbl">Billing Address</label>
	</div>	
	<div class="col-md-8">
		<input type="text" id="billing-address" class="dropdown-btn col-sm-1" style="height: 15px; width: 300px;">
	</div>
	
</div>
<br>
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;text-align: right;">
		<label class="profile-lbl">Bank Account Type</label>
	</div>	
	<div class="col-md-8">
		<input type="text" id="bank-type" placeholder="i.e UOB Account" class="dropdown-btn col-sm-1" style="height: 15px; width: 300px;">
	</div>
	
</div>
<br>

<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3" style="padding: 0px;text-align: right;">
		<label class="profile-lbl">Bank Account Number</label>
	</div>	
	<div class="col-md-8">
		<input type="text" id="bank-number" class="dropdown-btn col-sm-1" style="height: 15px; width: 300px;">
	</div>
	
</div>

<div style="clear: both"></div>
<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-8" style="margin-top: 20px;">
		<button id="update-payment-details-btn" class="btn btn-default btn-go" style="background: #1B9BD7 !important;border: 1px solid #1B9BD7 !important;color: #FFF !important;"> Update	</button>
	</div>
	
</div>
</div>
