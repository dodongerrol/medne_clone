<!DOCTYPE html>
<html >
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>Try it free for 3 months — Mednefits: Insurance with Better Benefits</title>
	<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
	<script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-8344843655918366",
        enable_page_level_ads: true
      });
    </script>
	{{ HTML::style('assets/new_landing/css/bootstrap.min.css') }}
	{{ HTML::style('assets/new_landing/css/font-awesome.min.css') }}
	{{ HTML::style('assets/new_landing/css/style.css') }}
	{{ HTML::style('assets/new_landing/css/responsive.css') }}
	
	<style type="text/css">
		
	</style>
</head>
<body>
	<div class="body-container try-three-container">
		<header class="parallax-window para-1" style="background-image: url('{{ URL::asset('assets/new_landing/images/landing/new_images/bigstock--161019443.jpg') }}');background-size: cover;background-position: 0px 50%;">
			
			<div class="bg-overlay-blue"></div>
			<div class="header-content three-months" style="height: 100%;" >
				<div class="col-md-12">
					<img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Marketing+bochure+header-01.png') }}" class="margin-top-50 margin-bottom-100" style="width: 400px;margin: 0 auto;display: inherit;">
				</div>
				<div class="col-md-12 text-center margin-top-30">
					<h1 class="color-white header-title-custom">Show your team you care.</h1>
					<p class="color-white margin-bottom-50">Mednefits Care for SMEs</p>
					<button id="open-try-form" class="btn btn-try-three">TRY IT FREE FOR 3 MONTHS</button>
				</div>
				<div class="col-md-12 text-center" style="position: absolute;bottom: 50px;width: 100%">
					<div class="social try-three-social" style="margin: 0 auto;display: inline-block;">
						<ul class="nav navbar-nav">
							<li><a href="https://www.facebook.com/Mednefits" class="btn btn-social"><i class="fa fa-facebook"></i></a></li>
							<li><a href="https://www.linkedin.com/company/13238401" class="btn btn-social"><i class="fa fa-linkedin"></i></a></li>
							<li><a href="https://www.instagram.com/mednefits/" class="btn btn-social"><i class="fa fa-instagram"></i></a></li>
						</ul>
					</div>
				</div>
			</div>	
			
		</header>

		<div class="try-form-container" hidden>
			<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
				<div class="col-md-12">
					<h3>Mednefits Care (3 months free) <a id="close-try-form" href="javascript:void(0)" class="pull-right"><i class="fa fa-times"></i></a></h3>
					<br>
					<h3 style="display: none;" id="show-message">Thank you! We will contact you shortly for the next step.</h3>
					<div class="white-space-20"></div>
				</div>
				<div id="show-result">
					<div class="form-group col-xs-6 col-sm-6 col-md-6 no-padding-right">
						<label>Name *</label>
						<input id="fname" type="text" class="form-control" name="">
						<p>First Name</p>
					</div>
					<div class="form-group col-xs-6 col-sm-6 col-md-6">
						<label>&nbsp;</label>
						<input id="lname" type="text" class="form-control" name="">
						<p>Last Name</p>
					</div>

					<div class="form-group col-xs-12 col-sm-12 col-md-12">
						<label>Work Email *</label>
						<input id="email" type="email" class="form-control" name="">
					</div>

					<div class="form-group col-xs-12 col-sm-12 col-md-12">
						<label>Company Name *</label>
						<input id="company" type="text" class="form-control" name="">
					</div>

					<div class="form-group col-xs-12 col-sm-12 col-md-12">
						<label>Employee Size *</label>
						<input id="employee" type="text" class="form-control" name="">
					</div>

					<div class="form-group col-xs-12 col-sm-12 col-md-12">
						<span class="no-margin">*</span>
						<div class="checkbox">
						    <label>
						      <input type="checkbox"> I agree to Mednefits's Terms and Conditions and Privacy Policy
						    </label>
						  </div>
					</div>

					<div class="form-group col-xs-12 col-sm-12 col-md-12">
						<button id="send-email" class="btn btn-try-three">SUBMIT</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="sidemenu">
		<ul class="nav">
			<li class="active"><a href="/">Employers</a></li>
			<li><a href="/individuals">INDIVIDUALS</a></li>
			<li><a href="/health-partner">BE A HEALTH PARTNER</a></li>
			<li><a href="/our-story">OUR STORY</a></li>
			<li><a href="/app/auth/login">LOGIN</a></li>
			<li class="try-three-months-li"><a href="/try-three-months">TRY 3 MONTHS FREE</a></li>
		</ul>
	</div>

</body>
	{{ HTML::script('assets/new_landing/js/jquery.min.js') }}
	{{ HTML::script('assets/new_landing/js/bootstrap.min.js') }}
</html>

<script type="text/javascript">
	window.base_url = window.location.origin + '/';
	$("#open-try-form").click(function () {
		$(".header-content").hide();
		$(".try-form-container").fadeIn();
	});

	$("#close-try-form").click(function () {
		$(".header-content").fadeIn();
		$(".try-form-container").hide();
	});

	$("#send-email").click(function(){
		$('#send-email').text('SUBMITTING...');
		var data = {
			fname : $("#fname").val(),
			lname : $("#lname").val(),
			email : $("#email").val(),
			company : $("#company").val(),
			employee : $("#employee").val(),
		}

		console.log(data);

		$.ajax({
			url: base_url + "send/try-three-months", 
		 	data: data,
      type: 'POST',
    });
    $('#send-email').text('SUBMIT');
		$('#show-result').fadeOut();
		$('#show-message').fadeIn();

	});
</script>