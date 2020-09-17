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
			<!-- Monday div -->
			<div class="row col-md-13" id ='profile-breakHours-monday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Monday</label>
				</div>
				<div class="col-md-3 monday-addBreakBtn" style="padding-top: 1px">
					<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#multiCollapseExample1" aria-expanded="false" aria-controls="multiCollapseExample2" id="monday-addBreak">Add Break</button>
				</div>
				<div class="row">
				<div class="col">
					<div class="collapse multi-collapse" id="multiCollapseExample1">
					<div class="card card-body">
						Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
					</div>
					</div>
				</div>
				</div>
				<!-- <div id="breakDiv-monday0">
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="monday profile-breakHours-chk_activate" data-onstyle="info">
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
				</div> -->
			</div>	
			<!-- Tuesday div -->
			<div class="row col-md-13" id ='profile-breakHours-tuesday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Tuesday</label>
				</div>
				<div class="col-md-3 tuesday-addBreakBtn" style="padding-top: 1px">
					<button id="tuesday-addBreak">Add Break</button>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="tuesday profile-breakHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Wednesday div -->
			<div class="row col-md-13" id ='profile-breakHours-wednesday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Wednesday</label>
				</div>
				<div class="col-md-3 wednesday-addBreakBtn" style="padding-top: 1px">
					<button id="wednesday-addBreak">Add Break</button>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="wednesday profile-breakHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Thursday div -->
			<div class="row col-md-13" id ='profile-breakHours-thursday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Thursday</label>
				</div>
				<div class="col-md-3 thursday-addBreakBtn" style="padding-top: 1px">
					<button id="thursday-addBreak">Add Break</button>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="thursday profile-breakHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Friday div -->
			<div class="row col-md-13" id ='profile-breakHours-friday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Friday</label>
				</div>
				<div class="col-md-3 friday-addBreakBtn" style="padding-top: 1px">
					<button id="friday-addBreak">Add Break</button>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="friday profile-breakHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Saturday div -->
			<div class="row col-md-13" id ='profile-breakHours-saturday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Saturday</label>
				</div>
				<div class="col-md-3 saturday-addBreakBtn" style="padding-top: 1px">
					<button id="saturday-addBreak">Add Break</button>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="saturday profile-breakHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
				</div>
			</div>	
			<!-- Sunday div -->
			<div class="row col-md-13" id ='profile-breakHours-sunday-div'><br>
				<div class="col-md-2" style="clear: both">
					<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Sunday</label>
				</div>
				<div class="col-md-3 sunday-addBreakBtn" style="padding-top: 1px">
					<button id="sunday-addBreak">Add Break</button>
				</div>
				<div class="col-md-1" style="padding-top: 3px;">
					<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="sunday profile-breakHours-chk_activate" data-onstyle="info">
				</div>
				<div class="col-md-2" style="padding-left: 10px;">
					<input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
				</div>
					<span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
				<div class="col-md-2">
					<input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
				</div>
			</div>
			<!-- Public Holiday div -->
			<div class="row col-md-13" id ='profile-breakHours-publicHoliday-div'><br>
					<div class="col-md-2" style="clear: both">
						<label class="profile-breakHours-detail-lbl day-name" style="padding-top: 8px;">Public Holiday</label>
					</div>
					<div class="col-md-3 publicHoliday-addBreakBtn" style="padding-top: 1px">
						<button id="publicHoliday-addBreak">Add Break</button>
					</div>
					<div class="col-md-1" style="padding-top: 3px;">
						<input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="publicHoliday profile-breakHours-chk_activate" data-onstyle="info">
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
			<div class="col-md-9" style="padding-top: 3px;" id="saveBreakHoursBtnDiv">
				<button id="profile-breakHours-savebreakHours" ><span class="glyphicon glyphicon-pencil"></span> Apply Changes</button>
			</div>	
		</div>
		
