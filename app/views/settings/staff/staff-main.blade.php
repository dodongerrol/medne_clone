
<script type="text/javascript" src="<?php echo $server; ?>/assets/settings/staff/staff.js?_={{ $date->format('U') }}"></script>
<div class="staff-container" style="">

	<div class="staff-side-list" >

		<!-- Doctor side list -->
		<input type="hidden" id="h-doctor-id" value="{{ $doctors[0]->DoctorID }}" >
		<input type="hidden" id="h-staff-id" value="0" >

		<div class="col-md-12" style="padding: 0px; padding-top: 20px; padding-bottom: 20px; border-bottom: 1px solid #D0D0D0;">
			<div class="col-md-12">
				<b>{{count($doctors)}} Doctors</b>
				<span style="padding-left: 40px;float: right;">
					<a id="btn-doctor-pop" href="#"  data-toggle="popover" data-placement="bottom" >
						<img src="{{ URL::asset('assets/images/ico_add new.svg') }}" width="25" height="25">
					</a>
				</span>
			</div>

			<div id="staff-doctor-list" class="scoll-panel" style="font-size: 12px;">
			<?php 
				if($doctors){
				foreach ($doctors as $value) { 
				
				$c = ($value->DoctorID==$doctors[0]->DoctorID)? 'black':'#777676';

				?>
				<div class="col-md-12 staff-doctor">
					<span style="float: left;"><img alt="$doctors[0]->DoctorID" src="{{ URL::asset('assets/images/doctor.png') }}" style="width: 40px;height: 40px"></span>
					<div style="padding-top: 15px;padding-left: 15px;display: inline-block;"><b style="color: {{$c}};" id="{{ $value->DoctorID }}">{{ $value->DocName }}</b></div>
				</div>

			<?php } } ?>	
			</div>

		</div>

		<!-- staff side list -->

	

	</div>


	<div id="detail-wrapper" class="detail-wrapper" style="padding: 0px;">
		
	</div>
	
</div>