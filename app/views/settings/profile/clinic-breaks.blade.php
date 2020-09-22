{{ HTML::script('assets/settings/profile/break-hours.js') }}
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
	<div style="float: left; font-size: 22px;">Break for you clinic :</div>
</div>

<br><br><br><br>


<div id="profile-breakHours-time-panel">
	<div id="monday-mainCollapsibleDiv" class="row">
		<div class="day-label-monday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Monday</label>
		</div>
		<div class="col-md-3 monday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='monday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="monday-div">
			<div class="card card-body">
				<div class="row monday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="monday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
					<div>
						<button id="profile-breakHours-copyTimetoAllBtn" >Copy time to all</button>
					</div>
				</div>
				<div class="row monday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="monday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row monday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="monday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row monday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="monday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row monday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="monday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="tuesday-mainCollapsibleDiv" class="row">
		<div class="day-label-tuesday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Tuesday</label>
		</div>
		<div class="col-md-3 tuesday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='tuesday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="tuesday-div">
			<div class="card card-body">
				<div class="row tuesday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="tuesday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row tuesday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="tuesday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row tuesday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="tuesday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row tuesday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="tuesday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row tuesday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="tuesday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="wednesday-mainCollapsibleDiv" class="row">
		<div class="day-label-wednesday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Wednesday</label>
		</div>
		<div class="col-md-3 wednesday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='wednesday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="wednesday-div">
			<div class="card card-body">
				<div class="row wednesday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="wednesday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row wednesday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="wednesday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row wednesday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="wednesday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row wednesday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="wednesday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row wednesday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="wednesday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="thursday-mainCollapsibleDiv" class="row">
		<div class="day-label-thursday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Thursday</label>
		</div>
		<div class="col-md-3 thursday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='thursday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="thursday-div">
			<div class="card card-body">
				<div class="row thursday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="thursday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row thursday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="thursday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row thursday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="thursday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row thursday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="thursday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row thursday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="thursday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="friday-mainCollapsibleDiv" class="row">
		<div class="day-label-friday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Friday</label>
		</div>
		<div class="col-md-3 friday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='friday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="friday-div">
			<div class="card card-body">
				<div class="row friday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="friday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row friday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="friday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row friday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="friday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row friday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="friday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row friday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="friday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="saturday-mainCollapsibleDiv" class="row">
		<div class="day-label-saturday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Saturday</label>
		</div>
		<div class="col-md-3 saturday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='saturday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="saturday-div">
			<div class="card card-body">
				<div class="row saturday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="saturday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row saturday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="saturday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row saturday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="saturday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row saturday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="saturday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row saturday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="saturday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="sunday-mainCollapsibleDiv" class="row">
		<div class="day-label-sunday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Sunday</label>
		</div>
		<div class="col-md-3 sunday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='sunday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="sunday-div">
			<div class="card card-body">
				<div class="row sunday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="sunday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row sunday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="sunday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row sunday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="sunday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row sunday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="sunday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row sunday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="sunday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="publicHoliday-mainCollapsibleDiv" class="row">
		<div class="day-label-publicHoliday col-md-2" style="clear: both">
			<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Public Holiday</label>
		</div>
		<div class="col-md-3 publicHoliday-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
			<a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='publicHoliday-addBreak'>
			<span class="glyphicon glyphicon-plus"></span>Add Break
			</a>
		</div>
		<div class="collapse" id="publicHoliday-div">
			<div class="card card-body">
				<div class="row publicHoliday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="publicHoliday0 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row publicHoliday1">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="publicHoliday1 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row publicHoliday2">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="publicHoliday2 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row publicHoliday3">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="publicHoliday3 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
				<div class="row publicHoliday4">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="publicHoliday4 profile-breakHours-chk_activate" data-onstyle="info">
					</div>
					<div class="col-md-2" style="padding-left: 10px;">
						<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
					</div>
						<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
					<div class="col-md-2">
						<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-9" style="padding-top: 3px;" id="saveBreakHoursBtnDiv">
		<button id="profile-breakHours-savebreakHours" ><span class="glyphicon glyphicon-pencil"></span> Apply Changes</button>
	</div>	
</div>