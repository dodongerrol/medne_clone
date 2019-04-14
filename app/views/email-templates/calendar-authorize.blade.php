<!DOCTYPE html>
<html>
<head>
	<title>Calendar Authorization</title>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
	<p>Please wait...</p>
	<input type="hidden" name="token" value="{{$link}}" id="google_link">

	<script type="text/javascript">
		$(document).ready(function( ){
			window.location.href = $('#google_link').val();
		});
	</script>
</body>
</html>