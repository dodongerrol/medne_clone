<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $title }}</title>
	<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	{{ HTML::script('assets/js/jquery.min.js') }}
	{{ HTML::script('assets/js/qrcode.min.js') }}

	<style type="text/css">
		img{
			display: inline-block !important;
		}
	</style>
</head>
<body>
	<input type="hidden" name="qr_code" id="qr_code" value="<?php echo $server; ?>">
	<div class="container">
		<div class="col-lg-4 col-lg-offset-4 text-center" style="position: relative;">
			<img src="{{ URL::asset('images/mednefits_logo.png') }}" style="width: 30px;position: relative;top: 240px;">
			<div id="qrcode" style="margin-top: 100px;"></div>
			<h4>{{ ucwords($clinic->Name) }}</h4>
		</div>
	</div>

	<script type="text/javascript">
		var code = $('#qr_code').val();
		new QRCode(document.getElementById("qrcode"), code);
	</script>
</body>
</html>