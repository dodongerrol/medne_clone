@include('admin.header-admin') 
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
@include('admin.header')
<div class="row" style="margin-top: 20px;">
	<div class="page-header col-md-3 col-md-offset-1">
		<h1 style="font-size: 100% !important;"><span class="label label-default">All Doctors</span></h1>
	</div>
</div>
<div class="row">       
	<div class="col-md-12">
		<table class="table table-striped table-bordered"  id="allDoctors" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Email</th>
					<th>Qualifications</th>
					<th>Specialty</th>					
					<th>image</th>
					<th>Phone</th>
					<th>Emergency</th>
					<th>Status</th>      
					<th>Action</th>
					
				</tr>	                        
			</thead>
			<tbody>											
			@foreach($resultSet as $result)
				<tr>
					<td>{{ $result->DoctorID}}</td>
					<td>{{ $result->Name }}</td>					                        
					<td>{{ $result->Email }}</td>					                        
					<td>{{ $result->Qualifications }}</td>					                        
					<td>{{ $result->Specialty }}</td>										                        
					<td>{{ HTML::image($result->image, $result->Name, array( 'width' => 50, 'height' => 50 )) }} </td>					                        
					<td>{{ $result->Phone }}</td>					                        
					<td>{{ $result->Emergency }}</td>
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
								 {{ HTML::link('admin/clinic/doctor/'.$result->DoctorID.'/edit', 'Edit','class="btn btn-sm btn-info"')}}						 
							</div>
							{{-- <div class="col-md-6">
								{{ HTML::link('#', 'Delete','class="btn btn-sm btn-danger"')}}
							</div> --}}
						</div>
					</td>    					
				</tr>                        
			@endforeach	    
			</tbody>
		</table>  	 
	</div>
</div>
@include('admin.footer-admin')