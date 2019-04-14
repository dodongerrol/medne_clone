<?php
	if( sizeof($result) > 0 ){
?>
<!-- 1 -->
@foreach($result as $key => $value)

<div class="col-md-11-5 line">
	<div class="col-md-1 no-padding">
		<h2>{{ date("j", $value->BookDate) }}</h2>
		<p>{{ date("D", $value->BookDate) }}</p>
		<p class="date">{{ date('M', $value->BookDate) }} {{ date('Y', $value->BookDate) }}</p>
	</div>
	<div class="col-md-2 time no-padding-left text-right">
		<?php 
			if($value->Status == 0) {
				$status = 'Active';
				$icon = '<i class="fa fa-check-circle blue"></i>';
			} else if($value->Status == 1) {
				$status = 'Processing';
				$icon = '<i class="fa fa-circle orange"></i>';
			} else if($value->Status == 2) {
				$status = 'Conclude';
				$icon = '<i class="fa fa-check-circle green"></i>';
			} else if($value->Status == 3) {
				$status = 'Disabled';
				$icon = '<i class="fa fa-times red"></i>';
			} else if($value->Status == 4) {
				$status = 'No Show';
				$icon = '<i class="fa fa-times red"></i>';
			}
		?>
		<h5> {{ $icon }} {{ date('g:i a', $value->StartTime) }}</h5>
		<?php  $diff = $value->EndTime - $value->StartTime; ?>
		<p>{{ round(abs($value->StartTime - $value->EndTime) / 60,2) }}mins</p>
	</div>
	<div class="col-md-5 name">
		<h5>{{ ucwords($value->client_name) }}</h5>
		<p>{{ ucwords($value->procedure_name) }}/Dr {{ ucwords($value->doctor_name) }}, ${{ $value->Price }}</p>
	</div>
	<div class="col-md-1 text-right" style="width: 20%;">
		<a tabindex="0" role="button" data-trigger="click" class="info-detail appointment-detail" data-toggle="popover" onclick="viewAppointment('{{ $value->UserAppoinmentID }}')">
		<!-- <a tabindex="0" role="button" data-trigger="click" class="info-detail appointment-detail" data-toggle="popover" onclick="test()"> -->
		<h5 class="color-black">{{ $status }}</h5>
		</a>
	</div>
</div>

@endforeach

<?php
	}else{
?>
<div class="col-md-11-5 " style="border-bottom: none;margin: 20% 0;">
	<p class="text-center" style="color: #999">No Activity To Display</p>
</div>
<?php
	}
?>


