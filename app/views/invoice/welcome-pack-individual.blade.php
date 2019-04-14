<!DOCTYPE html>
<html>
<head>
	<title>Welcome Pack</title>
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
</head>
<style type="text/css">
	body {
		font-family: 'Oxygen';
		background: #297ea2;
	}
</style>
<body>
	<div class="container">
		<div class="col-md-6 col-md-offset-3 text-center" style="margin-top: 100px;">
			<img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/logo.png" class="img-responsive">
			<div class="list-group">
			  <a href="/pdf/How Mednefits Works.pdf" class="list-group-item active">How Mednefits Works</a>
			  <a href="/pdf/Mednefits-u2019s Health Partners & Benefits (ind).pdf" class="list-group-item active">Mednefits Health Partners & Benefits</a>
			  <a href="https://docs.google.com/spreadsheets/d/1YtsLDjgdHu6bKkZWRGtBIdeyWhwPTnDdQGFrUsBOZ9g/pubhtml" class="list-group-item active">Health Partner List</a>
			  <a href="/get/invoice/{{$id}}" target="_blank" class="list-group-item active">Invoice</a>
			  <a href="/get/certificate/{{$id}}" target="_blank" class="list-group-item active">Certificate</a>
			  <a href="/get/receipt/{{$id}}" target="_blank" class="list-group-item active">Receipt</a>
			</div>
		</div>
	</div>
</body>
</html>