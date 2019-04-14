<!DOCTYPE html>
<html>
<head>
	<title>Login HR Platform</title>
	{{ HTML::script('assets/hr-dashboard/js/jquery.min.js') }}
	<script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-8344843655918366",
        enable_page_level_ads: true
      });
    </script>
</head>
<body>
<input type="hidden" name="token" id="login_token" value="{{ $token }}">
</body>

<script type="text/javascript">
	$(document).ready(function( ){
		var token = $('#login_token').val();
		window.localStorage.setItem('token', token);
		setTimeout(function() {
			window.location.href = window.location.origin + '/company-benefits-dashboard';
		}, 500);
	})
</script>
</html>