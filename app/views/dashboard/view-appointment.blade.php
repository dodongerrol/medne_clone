<div class="body">
	<input id="h-doctor-id" type="hidden" value="{{ $result[0]->DoctorID }}">
	<input id="h-doctor-name" type="hidden" value="{{ $result[0]->doctor_name }}">
	<input id="h-procedure-id" type="hidden" value="{{ $result[0]->ProcedureID }}">
	<input id="appointment-date-lbl" type="hidden" value="{{ date('l, F d, Y', $result[0]->BookDate) }}">
	<input id="appointment-service-detail" type="hidden" value="{{ $result[0]->procedure_name }}">
	<input id="h-procedure-duration" type="hidden" value="{{ $result[0]->Duration}}">
	<input id="h-procedure-price" type="hidden" value="{{ $result[0]->Price }}">
	<input id="h-cus-city" type="hidden" value="{{ $result[0]->City }}">
	<input id="h-cus-zip" type="hidden" value="{{ $result[0]->Zip_Code }}">
	<input id="h-cus-state" type="hidden" value="{{ $result[0]->State }}">
	<input id="h-cus-address" type="hidden" value="{{ $result[0]->Address }}">
	<input id="h-app-time" type="hidden" value="{{ date('g:i A', $result[0]->StartTime) }}">
	<input id="h-cus-phone-code" type="hidden" value="{{ $result[0]->PhoneCode }}">
	<input id="appointment-note-detail" type="hidden" value="{{ $result[0]->Remarks }}">
	<input id="h-time" type="hidden" value="{{ date('g:i a', $result[0]->StartTime). ' - ' .date('g:i a', $result[0]->EndTime); }}">
	<input type="hidden" value="{{ $result[0]->client_name }}" id="appointment-customer-detail">
	<input type="hidden" value="{{ $result[0]->NRIC }}" id="appointment-nric-detail">
	<input type="hidden" value="{{ $result[0]->PhoneNo }}" id="appointment-phone-detail">
	<input type="hidden" value="{{ $result[0]->client_email }}" id="appointment-email-detail">
	<input type="hidden" value="{{ $result[0]->UserID }}" id="userid">
	<h5>{{ date("D", $result[0]->BookDate) }}, {{ date('M', $result[0]->BookDate) }}, {{ date('g:i a', $result[0]->StartTime) }} -{{ date('g:i a', $result[0]->EndTime) }}</h5>
	<div class="white-space-20" ></div>

	<p><label>Staff</label> <span>Dr {{ ucwords($result[0]->doctor_name) }}</span></p>
	<p><label>Services</label> <span>{{ ucwords($result[0]->procedure_name) }}</span></p>
	<div class="white-space-20" ></div>

	<p><label>Cost </label> <span>${{ $result[0]->Price }}</span></p>
	<p><label>Customer</label> <span>{{ ucwords($result[0]->client_name) }}</span></p>
	<div class="white-space-20" ></div>
	<?php 
		if($result[0]->MediaType == 0) {
			$media = 'Mobile App';
		} else if($result[0]->MediaType == 1) {
			$media = 'Web';
		}
		?>
	<p><label>Booked From</label> <span>{{ $media }}</span></p>
</div>
<div class="footer">
	<h5>
	@if($result[0]->Status == 0 || $result[0]->Status == 1)
		<a href="javascript:void(0)" style="color:#76C9EC" onclick="editUserAppointmentment({{ $result[0]->UserAppoinmentID }}, {{ $result[0]->ClinicID }}, {{ $result[0]->DoctorID }})">Edit Appointment >></a>
	@else
		<a href="">&nbsp;</a>
	@endif
		<a href="javascript:void(0)" class="pull-right">Delete</span></a>
	</h5>
</div>
