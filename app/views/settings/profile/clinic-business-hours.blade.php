{{ HTML::script('assets/settings/profile/operating-hours.js') }}
<br>

<div class="row" style="padding-bottom: 30px; padding-top: 15px; border-bottom: 1px solid #ddd; margin-left: 5px;">
	<div class="col-md-2">
		<label style="font-weight: bold !important;">Time Zone</label>
	</div>
	<div class="col-md-8">
		<span style="font-size: 15px; color: #777;">Your Time Zone is set to <u style="cursor: pointer; color: #666666;">SINGAPORE (UTC+08:00)</u>.</span>
	</div>
</div>

<br><br>

<div class="col-md-12">
	<div style="float: left; font-size: 22px;">Clinic Opening Times :</div>
</div>

<br><br><br><br>


<div id="profile-operatingHours-time-panel">
			<!-- Monday div -->
			<div class="row col-md-13" id ='profile-operatingHours-monday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Monday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
				<div>
					<button id="profile-operatingHours-copyTimetoAllBtn" >Copy time to all</button>
					<button id="profile-operatingHours-undoCopyTimetoAllBtn">Undo changes</button>
				</div>
				
			</div>	
			<!-- Tuesday div -->
			<div class="row col-md-13" id ='profile-operatingHours-tuesday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Tuesday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Wednesday div -->
			<div class="row col-md-13" id ='profile-operatingHours-wednesday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Wednesday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Thursday div -->
			<div class="row col-md-13" id ='profile-operatingHours-thursday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Thursday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Friday div -->
			<div class="row col-md-13" id ='profile-operatingHours-friday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Friday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Saturday div -->
			<div class="row col-md-13" id ='profile-operatingHours-saturday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Saturday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Sunday div -->
			<div class="row col-md-13" id ='profile-operatingHours-sunday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Sunday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Public Holiday div -->
			<!-- <div class="row col-md-13" id ='profile-operatingHours-publicHoliday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-operatingHours-detail-lbl day-name" style="padding-top: 8px;">Public Holiday</label>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="profile-operatingHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-operatingHours-time-from" value="09:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-operatingHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-operatingHours-time-to" value="21:00:00" style="font-size: 12px;">
				</div>
			</div>		 -->
		</div>
		<div class="col-md-9" style="padding-top: 3px;" id="saveOperatingBtnDiv">
			<button id="profile-operatingHours-saveOperatingHours" ><span class="glyphicon glyphicon-pencil"></span> Apply Changes</button>
		</div>