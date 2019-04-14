@include('admin.header-admin') 
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/admin/bootstrap-datetimepicker.css') }}
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">        
		<div class="page-header">
			<h1 style="font-size: 100% !important;"><span class="label label-default"> Edit Clinic Time</span></h1>
		</div> 
	</div>    
</div>
<div class="row">	
	<div class="col-md-5 col-md-offset-3">
		<!-- successMsagesSetClinicTime alert --> 
 		<div class="alert alert-success successMsagesSetClinicTime hide popupmsg">
        	<span style="margin-left: 26%;"></span>
    	</div>

        <!-- unsuccessMsagesSetClinicTime alert --> 
        <div class="alert alert-danger unsuccessMsagesSetClinicTime hide popupmsg">                
            <span style="margin-left: 32%;"></span>
        </div>
 	</div>	
	<div class="col-md-12">		 	
		<?php foreach ($clinicTime as $key => $value):?>
		{{ Form:: open(array('class'=> 'form-horizontal','url' => 'admin/clinic/time/'.$value->ClinicTimeID.'/update')) }}		
			<!-- Text input-->
			<div class="form-group">
				<label class="col-md-3 control-label col-sm-offset-1" for="startTime">Opening Time</label>
				<div class="col-md-3">
					<div class="input-group date" id="startTime">
						<input type="text" class="form-control" required id="startTime" name="startTime" placeholder="" value="<?php echo $value->StartTime;?>"><span class="input-group-addon"><span class="glyphicon-time glyphicon"></span>				    	
					</div>				    
					<input type="hidden" id="clinicid" class="form-control" name="clinicid" value="{{ $value->ClinicID }}">
					<input type="hidden" id="clinictimeid" class="form-control" name="clinictimeid" value="{{ $value->ClinicTimeID }}">
					<!-- <input type="hidden" id="base_url" class="form-control" name="base_url" value="<?php echo URL::to('/'); ?>"> -->
				</div>				    
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label col-sm-offset-1" for="endTime">Closing Time</label>
				<div class="col-md-3">
					<div class="input-group date" id="endTime">
						<input type="text" class="form-control" required id="endTime" name="endTime" placeholder="" value="<?php echo $value->EndTime;?>"><span class="input-group-addon"><span class="glyphicon-time glyphicon"></span>						
					</div>
				</div>
			</div>
			<div class="form-group">						
				<div class="col-md-8 col-sm-offset-4">
				  	<div class="btn-group" data-toggle="buttons">
			  	 		<label class="btn btn-success <?php if($value->Mon == 1){ echo 'active';}?>">	
							<input type="checkbox" id="monday" name="monday" value="1" <?php echo ($value->Mon == 1 ? 'checked' : '');?> autocomplete="off">Mon
						</label>
						<label class="btn btn-success <?php if($value->Tue == 1){ echo 'active';}?>">
							<input type="checkbox" id="tuesday" name="tuesday" value="1" <?php echo ($value->Tue == 1 ? 'checked' : '');?> autocomplete="off">Tue
						</label>
						<label class="btn btn-success <?php if($value->Wed == 1){ echo 'active';}?>">
							<input type="checkbox" id="wednesday" name="wednesday" value="1" <?php echo ($value->Wed == 1 ? 'checked' : '');?> autocomplete="off">Wed
						</label>
						<label class="btn btn-success <?php if($value->Thu == 1){ echo 'active';}?>">
							<input type="checkbox" id="thursday" name="thursday" value="1" <?php echo ($value->Thu == 1 ? 'checked' : '');?> autocomplete="off">Thu
						</label>
						<label class="btn btn-success <?php if($value->Fri == 1){ echo 'active';}?>">
							<input type="checkbox" id="friday" name="friday" value="1" <?php echo ($value->Fri == 1 ? 'checked' : '');?> autocomplete="off">Fri
						</label>
						<label class="btn btn-success <?php if($value->Sat == 1){ echo 'active';}?>">
							<input type="checkbox" id="saturday" name="saturday" value="1" <?php echo ($value->Sat == 1 ? 'checked' : '');?> autocomplete="off">Sat
						</label>
						<label class="btn btn-success <?php if($value->Sun == 1){ echo 'active';}?>">
							<input type="checkbox" id="sunday" name="sunday" value="1" <?php echo ($value->Sun == 1 ? 'checked' : '');?> autocomplete="off">Sun
						</label>
					</div>
				</div>						
			</div>
			<div class="form-group">
				<div class="col-md-3 col-md-offset-4">					
					<select name="status" id="status" class="form-control">						
					 	<option value="0" <?php if ($value->Active == 0) echo 'selected="selected"' ?>>Deactivate</option>
                  		<option value="1" <?php if ($value->Active == 1) echo 'selected="selected"' ?>>Activate</option>
					</select>
				</div>
			</div>
			<br>
			<!-- Button -->
			<div class="form-group">
				{{-- <label class="col-md-3 control-label col-md-offset-1" for="addTime"></label> --}}
				<div class="col-md-3 col-md-offset-4">
					<button type="submit" class="btn btn-primary">Update</button>
					{{ HTML::link('admin/clinic/'.$value->ClinicID.'/new-time', 'Cancel','class="btn btn-primary btn-md"')}}
				</div>						
			</div>	
		{{ Form:: close() }}
		<?php endforeach; ?>					
	</div>
</div>
@include('admin.footer-admin')