<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $title }}</title>
	<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	{{ HTML::script('assets/js/jquery.min.js') }}
	{{ HTML::script('assets/js/qrcode.min.js') }}
</head>
<body>
	<input type="hidden" name="qr_code" id="qr_code" value="<?php echo $server; ?>">
	<div class="container">
		<div class="col-lg-4"></div>
		<div class="col-lg-4 text-center">
			<div id="qrcode" style="margin-top: 100px;"></div>
			<h4>Check In</h4>
		</div>
		<div class="col-lg-4"></div>
	</div>

	<script type="text/javascript">
		var code = $('#qr_code').val();
		new QRCode(document.getElementById("qrcode"), code);
	</script>
</body>
</html>