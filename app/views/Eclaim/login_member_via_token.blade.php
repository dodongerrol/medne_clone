<!DOCTYPE html>
<html>
<head>
	<title>Login Member Platform</title>
	{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
</head>
<body>
<input type="hidden" name="token" id="login_token" value="{{ $token }}">
<p>Please wait....</p>
</body>

<script type="text/javascript">
	$(document).ready(function( ){
		var token = $('#login_token').val();
		window.localStorage.setItem('token_member', token);
		setTimeout(function() {
			window.location.href = window.location.origin + '/member-portal/#/home';
		}, 500);
	})
</script>
</html>