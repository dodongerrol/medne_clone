	<!DOCTYPE html>
<html >
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>Mednefits: Modern Employees Digital Benefits</title>
	<link rel="shortcut icon" href="assets/new_landing/images/favicon.ico" type="image/ico">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

	{{ HTML::style('assets/new_landing/css/bootstrap.min.css') }}
	{{ HTML::style('assets/new_landing/css/font-awesome.min.css') }}
	{{ HTML::style('assets/new_landing/css/style.css') }}
	{{ HTML::style('assets/css/sweetalert2.css') }}
	{{ HTML::style('assets/new_landing/css/responsive.css') }}
	{{ HTML::style('assets/css/star-rating-svg.css') }}

	<style type="text/css">
		header .header-content{
			top: 60%;
		}
	</style>
</head>
<body class="individual-page">
	<div class="body-container" style="height: auto!important;">
		<header style="background: #33A2D4!important;min-height: 0!important;">
			<!-- <div class="bg-overlap-5"></div> -->
			<div id="main-navbar">
				<nav class="navbar navbar-default">
				  <!-- <div class="container"> -->
				    <!-- Brand and toggle get grouped for better mobile display -->
				    <div class="navbar-header">
				      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				        <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				      </button>
				      <a class="navbar-brand" href="/">
				      	<img src="{{ asset('e-template-img/mednefits logo v3 (hybrid) LARGE.png')}}" class="img-responsive">
				      </a>
				    </div>

				    <!-- Collect the nav links, forms, and other content for toggling -->
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				      <ul class="nav navbar-nav navbar-right">
				        <li><a href="/">Employers</a></li>
				        <li><a href="/individuals">individuals</a></li>
				        <li><a href="/health-partner">BE A HEALTH PARTNER</a></li>
				        <li><a href="/our-story">OUR STORY</a></li>
				        <li><a href="/app/auth/login">LOGIN</a></li>
				        <!-- <li><a href="http://blog.mednefits.com/">BLOG</a></li> -->
				        <li><a href="/try-three-months">Try 3 months free</a></li>
				        <!-- <li><a href="get-mednefits">GET MEDNEFITS NOW</a></li> -->
				      </ul>
				    </div>
				  <!-- </div> -->
				</nav>
			</div>
				

			
		</header>

		<section id="main-section" >

			<div id="second-section" class="content_section how-it-works" style="min-height: 873px;background: #F2F2F2!important;">
				<div class="row">
					<div class="col-xs-8 col-sm-8 col-md-8 col-xs-offset-2 col-sm-offset-2 col-md-offset-2">
						<p style="color: #414141!important;padding: 10px;">Experience Review</p>
					</div>
				</div>
				<div class="col-xs-8 col-sm-9 col-md-8 col-xs-offset-2 col-sm-offset-2 col-md-offset-2" style="background: #FFFFFF;padding: 40px;">
					<h2 style="color: #33A2D4;"><b>Your recent health & wellness visit</b></h2>
					<p>
						<span>Appointment ID:</span>
						<br />
						<span style="color: #434343;">{{$rating->UserAppoinmentID}}</span>
					</p>
					<p>
						<span>Health Provider:</span>
						<br />
						<span style="color: #434343;">{{ucwords($rating->clinic_name)}}</span>
					</p>
					<p>
						<span>Services:</span>
						<br />
						<span style="color: #434343;">{{ucwords($rating->procedure_name)}}</span>
					</p>
					<p>
						<span>Health Partner's Address:</span>
						<br />
						<span style="color: #434343;">{{ucwords($rating->Address)}}</span>
					</p>
					<div class="row" style="margin-left: 0; margin-top: 70px;">
						<p>
							<span style="color: #404040;">Your experience rating:</span>
							<br />
							<div class="my-rating-5 my-rating-5" style="margin-top: 15px;"></div>
						</p>
					</div>
					<div class="row" style="margin-left: 0;">
						<p>
							<span style="color: #404040;">Other experience review:</span>
							<br />
							<textarea class="form-control" style="margin-top: 10px; margin: 10px -0.4125px 0px 0px;border-radius: 0px;width: 95%;height: 106px;" id="feedback"></textarea>
							<input type="hidden" name="user_id" id="user_id" value="{{$rating->UserID}}">
							<input type="hidden" name="appointment_id" id="appointment_id" value="{{$rating->UserAppoinmentID}}">
							<input type="hidden" name="clinic_id" id="clinic_id" value="{{$rating->ClinicID}}">
							<input type="hidden" name="rate_id" id="rate_id" value="{{$rate}}">
						</p>
					</div>
					<div class="row" style="margin-left: 0;margin-bottom: 30px;">
						<p style="color: #010101;">Review as <span style="text-decoration: underline">{{$rating->Email}}</span></p>
					</div>
					<div class="row" style="margin-left: 0;">
						<button class="btn btn-primary pull-right" style="border-radius: 0;background: #0392CF;width: 100px;padding: 10px;border: none;" id="submit_rating">SUBMIT</button>
						<button class="btn btn-default pull-right" style="border-radius: 0;background: #E5E5E5;color: #919191;width: 100px;padding: 10px;border: none;margin-right: 10px;">CANCEL</button>
					</div>
				</div>
			</div>

			
		</section>

		<footer id="footer" style="min-height: 490px;">
			<div class="footer-container">
				<div class="col-md-12 text-center">
					<div class="nav-wrapper">
						<ul class="nav navbar-nav">
							<li>
								<a href="http://blog.mednefits.com/">
									BLOG
								</a>
							</li>
							<li>
								<div class="dropdown">
									<ul class="nav">
										<li><a href="/">MEDNEFITS CARE PLAN</a></li>
										<li><a href="/outpatient-care">OUTPATIENT CARE</a></li>
										<li><a href="/hospital-care">HOSPITAL CARE</a></li>
										<li><a href="/bonus-credits">DIGITAL HEALTH BENEFITS</a></li>
									</ul>
								</div>
								<a>
									<span class="plus">+</span> 
									<span class="minus">-</span> 
									PRODUCTS
								</a>
								
							</li>
							<li>
								<div class="dropdown">
									<ul class="nav">
										<li><a href="/">EMPLOYERS</a></li>
										<li><a href="/individuals">INDIVIDUALS</a></li>
										<li><a href="/health-partner">HEALTH PROFESSIONALS</a></li>
									</ul>
								</div>
								<a>
									<span class="plus">+</span> 
									<span class="minus">-</span> 
									CUSTOMERS
								</a>
								
							</li>
							<li>
								<div class="dropdown">
									<ul class="nav">
										<li><a href="/our-story">OUR STORY</a></li>
										<li><a href="/get-mednefits">CONTACT US</a></li>
									</ul>
								</div>
								<a>
									<span class="plus">+</span> 
									<span class="minus">-</span> 
									COMPANY
								</a>
								
							</li>
							<li>
								<div class="dropdown">
									<ul class="nav">
										<li><a href="/provider-terms">PROVIDER TERMS AND CONDITIONS</a></li>
										<li><a href="/user-terms">MEMBER TERMS AND CONDITIONS</a></li>
										<li><a href="/privacy-policy">PRIVACY POLICY</a></li>
										<li><a href="/insurance-license">INSURANCE LICENSES AND SERVICES</a></li>
									</ul>
								</div>
								<a>
									<span class="plus">+</span> 
									<span class="minus">-</span> 
									LEGAL
								</a>
								
							</li>
						</ul>
					</div>				
				</div>

				<div class="col-md-12 no-padding border-top margin-top-30">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="white-space-20"></div>
						<div class="white-space-20"></div>
					</div>
					<div class="col-md-6 no-padding">
						<p class=" color-white  xs-text-center">Mednefits, a new kind of health benefits company using technology, data, design to make benefits simple, affordable and human. We believe better benefits is possible for every employer who cares. 
						</p>
					</div>

					<div class="col-md-6 no-padding text-right" >
						<div class="copyright">
							<p class="color-white">
								© 2020 Mednefits. All rights reserved
							</p>
						</div>
					</div>

					<div class="col-md-12 no-padding text-right" >
						<div class="social">
							<ul class="nav navbar-nav navbar-right">
								<li><a href="https://www.facebook.com/Mednefits" class="btn btn-social"><i class="fa fa-facebook"></i></a></li>
								<li><a href="https://www.linkedin.com/company/13238401" class="btn btn-social"><i class="fa fa-linkedin"></i></a></li>
								<li><a href="https://www.instagram.com/mednefits" class="btn btn-social"><i class="fa fa-instagram"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<div class="sidemenu">
		<ul class="nav">
			<li><a href="/">EMPLOYERS</a></li>
			<li><a href="/individuals">INDIVIDUALS</a></li>
			<li><a href="/health-partner">BE A HEALTH PARTNER</a></li>
			<li><a href="/our-story">OUR STORY</a></li>
			<li><a href="/app/auth/login">LOGIN</a></li>
			<li class="/try-three-months-li"><a href="try-three-months">TRY THREE MONTHS</a></li>
		</ul>
	</div>
</body>
	{{ HTML::script('assets/new_landing/js/jquery.min.js') }}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
	{{ HTML::script('assets/new_landing/js/bootstrap.min.js') }}
	{{ HTML::script('assets/js/sweetalert2.min.js') }}
	{{ HTML::script('assets/js/jquery.star-rating-svg.js') }}

	<script type="text/javascript">
		window.base_url = window.location.origin + '/app/';
	  var rating = 0;
	  var rate = $('#rate_id').val();
	  console.log(rate);
		$(".my-rating-5").starRating({
		  totalStars: 5,
		  emptyColor: 'lightgray',
		  hoverColor: '#FFE061',
		  activeColor: '#FFE061',
		  initialRating: rate,
		  strokeWidth: 0,
		  useGradient: false,
		  starSize: '40',
		  callback: function(currentRating, $el){
		    // alert('rated ' + currentRating);
		    rating = currentRating;
		    // console.log('DOM element ', $el);
		  }
		});

		$('#submit_rating').click(function( ){
			var feedback = $('#feedback').val();
			var clinic_id = $('#clinic_id').val();
			var user_id = $('#user_id').val();
			var appointment_id = $('#appointment_id').val();
			if(!rating) {
				swal(
				  'Please rate us!',
				  '',
				  'error'
				)
				return false;
			}
			$.ajax({
        url: base_url + 'save/clinic/rating',
        type: 'POST',
        data: {
	         user_id: user_id,
	         clinic_id: clinic_id,
	         rating: rating,
	         appointment_id: appointment_id,
	         feedback: feedback
	      },
	      })
				.done(function(data) {
					console.log(data);
					  swal(
						  'Thank you for your feedback!',
						  '',
						  'success'
						)
					setTimeout(function() {
						window.location.href = "/";
					}, 500);
				});
		});
		
	</script>

	<script charset="utf-8" src="https://js.hscta.net/cta/current.js"></script>
	<script type="text/javascript">
		hbspt.cta.load(2705714, '3fa248b1-c1d9-4f11-8b60-bde1ce2c6a3e', {});
		hbspt.cta.load(2705714, 'f2e9fc28-0815-40de-8886-f3aa5726cf02', {});
		hbspt.cta.load(2705714, 'ea9d5486-ed5f-48d1-a92d-9e332ab3a70f', {});
	</script>
</html>