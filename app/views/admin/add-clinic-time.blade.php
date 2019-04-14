@include('admin.header-admin') 
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/admin/bootstrap-datetimepicker.css') }}
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">        
		<div class="page-header">
			<h1 style="font-size: 100% !important;"><span class="label label-default"> Add Clinic Time</span></h1>
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
		{{ Form:: open(array('class'=> 'form-horizontal', 'id'=>'setTime')) }}		
			<!-- Text input-->
			<div class="form-group">
				<label class="col-md-3 control-label col-sm-offset-1" for="startTime">Opening Time</label>
				<div class="col-md-3">
					<div class="input-group date" id="startTime">
						<input type="text" class="form-control" required id="startTime" name="startTime" placeholder="" value=""><span class="input-group-addon"><span class="glyphicon-time glyphicon"></span>				    	
					</div>				    
					<input type="hidden" id="clinicid" class="form-control" name="clinicid" value="{{ $clinic->ClinicID }}">
					<input type="hidden" id="base_url" class="form-control" name="base_url" value="<?php echo URL::to('/'); ?>">
				</div>				    
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label col-sm-offset-1" for="endTime">Closing Time</label>
				<div class="col-md-3">
					<div class="input-group date" id="endTime">
						<input type="text" class="form-control" required id="endTime" name="endTime" placeholder="" value=""><span class="input-group-addon"><span class="glyphicon-time glyphicon"></span>				    	
					</div>
				</div>
			</div>
			<div class="form-group">						
					<div class="col-md-8 col-sm-offset-4">
					  <div class="btn-group" data-toggle="buttons">
					  	 <label class="btn btn-success status">	
							<input type="checkbox" id="monday" name="monday" value="1" autocomplete="off">Mon
						</label>
						<label class="btn btn-success status">
							<input type="checkbox" id="tuesday" name="tuesday" value="1" autocomplete="off">Tue
						</label>
						<label class="btn btn-success status">
							<input type="checkbox" id="wednesday" name="wednesday" value="1">Wed
						</label>
						<label class="btn btn-success status">
							<input type="checkbox" id="thursday" name="thursday" value="1">Thu
						</label>
						<label class="btn btn-success status">
							<input type="checkbox" id="friday" name="friday" value="1">Fri
						</label>
						<label class="btn btn-success status">
							<input type="checkbox" id="saturday" name="saturday" value="1">Sat
						</label>
						<label class="btn btn-success status">
							<input type="checkbox" id="sunday" name="sunday" value="1">Sun
						</label>
					</div>
					</div>						
			</div>	<br>
			<!-- Button -->
			<div class="form-group">
				{{-- <label class="col-md-3 control-label col-md-offset-1" for="addTime"></label> --}}
				<div class="col-md-3 col-md-offset-4">
					<button id="addTime" name="addTime" class="btn btn-primary">Add</button>
					{{ HTML::link('admin/clinic/all-clinics', 'Cancel','class="btn btn-primary btn-md"')}}
				</div>						
			</div>			
		{{ Form:: close() }}
	</div>
</div>
<br><br>
<div class="row">        
		<div class="col-md-12">
		 	<table class="table table-list-search" id="viewClinicTimeDetails">
	            <thead>
	                <tr>
	                    <th>ClinicTimeID</th>
	                    <th>StartTime</th>
	                    <th>EndTime</th>
	                    <th>Mon</th>
	                    <th>Tue</th>
	                    <th>Wed</th>
	                    <th>Thu</th>
	                    <th>Fri</th>
	                    <th>Sat</th>
	                    <th>Sun</th>
	                    <th>Active</th>
	                    <th>Action</th>
	                </tr>
	            </thead>
	            <tbody>                      
	               <?php if (!empty($clinicTime)){ ?>
	        	 	@foreach($clinicTime as $result)
					<tr>
						<td>{{ $result->ClinicTimeID}}</td>
						<td>{{ $result->StartTime }}</td>					                        
						<td>{{ $result->EndTime }}</td>								
						<td><?php 

								if ($result->Mon == 1)
								{
									echo '<span class="btn btn-success">';
								} 
							?>
						</td>
						<td><?php 

								if ($result->Tue == 1)
								{
									echo '<span class="btn btn-success">';
								} 
							?>
						</td>
						<td><?php 

								if ($result->Wed == 1)
								{
									echo '<span class="btn btn-success">';
								} 
							?>
						</td>
						<td><?php 

								if ($result->Thu == 1)
								{
									echo '<span class="btn btn-success">';
								} 
							?>
						</td>
						<td><?php 

								if ($result->Fri == 1)
								{
									echo '<span class="btn btn-success">';
								} 
							?>
						</td>
						<td><?php 

								if ($result->Sat == 1)
								{
									echo '<span class="btn btn-success">';
								} 
							?>
						</td>
						<td><?php 

								if ($result->Sun == 1)
								{
									echo '<span class="btn btn-success">';
								} 
							?>
						</td>
						<td>
							<?php 
							if ($result->Active == 1)
							   {
									echo 'Active';
									// var_dump($allData);	
							   } 
							   else
						   	   {
						   	   		echo 'Inactive';
							   }
						 	?>   					   
					   	</td>
				   		<td>
							<div class="row">
								<div class="col-md-6">
									 {{ HTML::link('admin/clinic/time/'.$result->ClinicTimeID.'/edit', 'Edit','class="btn btn-sm btn-info"')}}						 
								</div>
								{{-- <div class="col-md-6">
									{{ HTML::link('#', 'Delete','class="btn btn-sm btn-danger"')}}
								</div> --}}
							</div>
						</td>
					</tr>
					@endforeach
					<?php } ?>                        
					<tr>
						<td id="showStartTime"></td>
						<td id="showEndTime"></td>
						<td id="showMonday"></td>
						<td id="showTuesday"></td>
						<td id="showWednesday"></td>
						<td id="showThursday"></td>
						<td id="showFriday"></td>
						<td id="showSaturday"></td>
						<td id="showSunday"></td>
						<td id="active"></td>
						<td id="editLink"></td>
					</tr>                        
	                </tr>                        
	            </tbody>
	        </table> 
		</div>
		<!-- <table class="table table-list-search" id="viewStartTimeDetails">
			            <thead>
			                <tr>	                   
			                    <th>StartTime</th>
			                </tr>
			            </thead>
			            <tbody>
			            </tbody>
			        </table>  -->
	</div>
@include('admin.footer-admin')