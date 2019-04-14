@include('admin.header-admin')
<!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
<!-- <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css"> -->
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
@include('admin.header')
	<div class="white-space-20"></div>
	<div class="white-space-20"></div>
	<div class="white-space-20"></div>
	<div class="white-space-20"></div>

<div class="col-md-12">
	<table class="table table-striped table-bordered"  id="allClinics" >
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Image</th>
				<th>City</th>
				<th>Country</th>
				<th>Lat</th>
				<th>Lng</th>
				<th>Phone</th>
				<th>Opening</th>
				<th>Doctors Active Count</th>
				<th>Doctors Inactive Count</th>
				<th>Medicloud Transaction Fees</th>
				<th>Status</th>
				<th>Action</th>
				<th>Time</th>
			</tr>
		</thead>
	</table>
</div>
@include('admin.footer-admin')
