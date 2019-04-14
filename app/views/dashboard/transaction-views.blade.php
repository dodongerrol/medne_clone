@if(sizeof($results) > 0)
<div class="col-md-12 no-padding">
	<table class="table">
		<thead>
			<tr>
				<th class="col-md-3">Payment Date</th>
				<th class="col-md-3" >Customer</th>
				<th class="col-md-3" >Total Revenue</th>
				<th class="col-md-3">Collected Revenue</th>
				<th></th>
			</tr>
		</thead>
	</table>
</div>
<div class="col-md-12 no-padding table-wrapper" >
	
	<table class="table table-hover">
		
		<tbody>
			@foreach($results as $key => $value)
			<tr>
				<?php
					$month = strtotime($value->date_of_transaction);
					$day = strtotime($value->date_of_transaction);
				?>
				<td style="width: 25%;">{{ date('M', $month) }} {{ date('d', $day) }}</td>
				<td style="width: 26%;">{{ ucwords($value->Name) }}</td>
				<td style="width: 25%;">${{ $value->procedure_cost }}</td>
				<td style="width: 24%;">${{ $value->procedure_cost - (int)$value->credit_cost }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

<div class="button-wrapper">

<div class="col-md-12 text-right">
	<a href="javascript:void(0)" id="view-trans" class="btn btn-default pull-right view-transac-history">View Transaction History</a>
</div>
<div class="col-md-12 text-right">
	<a href="javascript:void(0)" id="view-invoice" class="btn btn-default pull-right view-transac-history">View Invoice</a>
</div>
<div class="col-md-12 text-right">
	<a href="javascript:void(0)" id="view-statement" class="btn btn-default pull-right view-transac-history">View Statement of Account</a>
</div>

</div>
@else
<div class="col-md-11-5 " style="border-bottom: none;margin: 35% 0;">
	<p class="text-center" style="color: #999">No Appointments To Display</p>
</div>
@endif

<script type="text/javascript">
	$(function( ){
		 $('#view-trans').click(function( ){
		  	window.localStorage.setItem('pay-view', true);
		  	window.location.href = window.base_url + 'setting/main-setting';
		  });

		 $('#view-invoice').click(function( ){
		 	window.localStorage.setItem('invoice-view', true);
		  	window.location.href = window.base_url + 'setting/main-setting';
		 });

		  $('#view-statement').click(function( ){
		 	window.localStorage.setItem('statement-view', true);
		  	window.location.href = window.base_url + 'setting/main-setting';
		 });
	});
</script>