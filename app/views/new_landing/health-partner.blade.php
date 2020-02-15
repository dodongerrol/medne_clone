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
	<title>Be a Health Partner — Mednefits: Insurance with Better Benefits</title>
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
		header .header-content{
			top: 208px;
		}

		.header-content p{
			width: 90% !important;
		}

		header .header-content .btn-info{
			margin: 38px auto 0 auto;
		}

		@media only screen and ( max-width: 768px ){
			#third-section{
				background-position: 50% 50% !important	;
			}
		}

		
	</style>
</head>
<body class="health-partner-page">
	<div class="body-container">
		<header class="parallax-window para-1" style="background: url('{{ URL::asset('assets/new_landing/images/landing/tiny/bigstock-Yoga-class-110092523.jpg') }}');background-size: cover;background-position: 0px 50%;">
			<div class="bg-overlap-45"></div>
			<div id="main-navbar">
				<nav class="navbar navbar-default">
				  <!-- <div class="container"> -->
				    <!-- Brand and toggle get grouped for better mobile display -->
				    <div class="navbar-header">
				      <button type="button" class="navbar-toggle collapsed" aria-expanded="false">
				        <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				      </button>
				      <a class="navbar-brand" href="/">
				      	<img src="{{ URL::asset('assets/new_landing/images/landing/logo.png') }}" class="img-responsive">
				      </a>
				    </div>

				    <!-- Collect the nav links, forms, and other content for toggling -->
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				      <ul class="nav navbar-nav navbar-right">
				        <li ><a href="/">Employers</a></li>
				        <!-- <li><a href="/individuals">individuals</a></li> -->
				        <li class="active"><a href="/health-partner">BE A HEALTH PARTNER</a></li>
				        <li><a href="http://blog.mednefits.com/">BLOG</a></li>
				        <li><a href="/our-story">OUR STORY</a></li>
				        <li><a href="/get-mednefits">CONTACT US</a></li>
				        <!-- <li><a href="/app/auth/login">LOGIN</a></li> -->	
				        <!-- <li><a href="http://blog.mednefits.com/">BLOG</a></li> -->
				        <li><a href="/app/login">LOGIN</a></li>
				        <!-- <li><a href="/try-three-months">Try 3 months free</a></li> -->
				        <!-- <li><a href="get-mednefits">GET MEDNEFITS NOW</a></li> -->
				      </ul>
				    </div><!-- /.navbar-collapse -->
				  <!-- </div> -->
				</nav>
			</div>
				
			<div class="header-content">
				<div class="col-md-8 col-md-offset-2">
					<h1 class="text-center color-white width-80">JOIN US AND BE PART OF OUR NETWORK</h1>
					<p class="text-center text-italic color-white">Our health professionals grow their business with us by attracting new customers from our corporate employees, who are looking for the best health and wellness professionals.</p>
					<div class="white-space-20 sm-hide"></div>
					<div class="white-space-20 sm-hide"></div>
					<button onclick="window.location.href='/get-mednefits'" class="btn btn-info" >JOIN US NOW</button>
				</div>
			</div>	

			<div class="header-arrow">
				<a id="scroll" href="#main-section"> 
					<!-- <i class="fa fa-angle-down color-white"></i>  -->
					<img src="{{ URL::asset('assets/new_landing/images/down-arrow-black.png') }}" class="margin-center">
					<p class="color-black ">SCROLL DOWN</p>
				</a>
			</div>
		</header>

		<section id="main-section">

			<div id="second-section" class="content_section text-center how-it-works what-mednefits-can-do">
				<div class="col-md-12 no-padding">
					<div class="col-xs-12 col-sm-12 col-md-12 heading">
						<h1 class="color-blue" style="margin-bottom: 25px;">What Mednefits Can Do For You</h1>
						<h2 class="color-blue">Be part of our healthy and happy movement.</h2>
						<p style="width: 86%;margin-bottom: 35px;">Today's employers are putting their employees well-being at the forefront thanks to Mednefits digital benefits.</p>
					</div>

					<div class="col-xs-12 col-sm-4 col-md-4 text-center do-box">
						<img src="{{ URL::asset('assets/new_landing/images/landing/can1.jpg') }}" class="img-responsive margin-center">
						<h2 class="color-blue">Expand Your Business Horizons</h2>
						<p>Gain access to Mednefits members through the digital way. Our corporate employees look for clinics, classes, seminars, plus on-demand services at your location.</p>
					</div>

					<div class="col-xs-12 col-sm-4 col-md-4 text-center do-box">
						<img src="{{ URL::asset('assets/new_landing/images/landing/can2.jpg') }}" class="img-responsive margin-center">
						<h2 class="color-blue">Tap Into Mednefits's Corporate Scheme</h2>
						<p>Our corporate employees receive member rates for health and wellness services when they join us. Offer your services to our pool of members who can now be healthy at a lower cost.</p>
					</div>

					<div class="col-xs-12 col-sm-4 col-md-4 text-center do-box">
						<img src="{{ URL::asset('assets/new_landing/images/landing/can3.jpg') }}" class="img-responsive margin-center">
						<h2 class="color-blue">Extend Your Online Presence</h2>
						<p>Online listing, booking, payments and invoicing all in Mednefits platform to take your business to the next level.</p>
					</div>
				</div>
			</div>

			<div id="third-section" class="content_section parallax-window para-2 text-center" style="background: url('{{ URL::asset('assets/new_landing/images/landing/tiny/bigstock--124460930.jpg') }}');background-size: cover;background-position: 0px 50%;height: 637px;min-height: unset;">
				<div class="bg-overlap-4"></div>
				<div class="content ">
					<h1 class="color-white" style="font-size: 48px;margin-top: 12px;margin-bottom: 63px;">QUALIFIED PARTNERS CAN USE MEDNEFITS FOR FREE</h1>
					<p class="text-center text-italic color-white">Connect to multi-billion dollar healthcare & wellness market for free with Mednefits.</p>
					<p class="text-center text-italic color-white">There are no subscription, referral, admin fees required. We only need corporate rates for our clients.</p>
				
					<button onclick="window.location.href='/get-mednefits'" class="btn btn-info" style="margin-top: 75px;">JOIN US NOW</button>
				</div>
			</div>

			<div id="fourth-section" class="content_section priority">
				<div class="col-md-12">
					<div class="col-md-12 text-center margin-bottom-70">
						<h1 class="color-blue width-90 font-52 margin-center">Our Health Partners Share Our Vision For Better Benefits</h1>
					</div>
	 
					<div class="col-md-12 text-center partners-container" style="overflow: hidden">
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Health-Partners-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Healthway-Medical-.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/SMG-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/North-East-Medical-Group.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/One-Care-Medical.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Medical-Partners-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/AcuMed-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Life-Family-Clinic.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Dr-Tan-+-Partners.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Healthwerkz-Logo.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Phoenix-Medical-Group.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/crawfurd-medical-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Hisemainn-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Tan-Teoh-Clinic.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/pinnacle-family-clinic-logo.jpg') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Accord-Medical-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/lifescan-Medical-Centre.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-iClinic.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/iDental-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/True-Dental-2.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Teeth@Tiong-Bahru-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Advanced-Dental.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Fusion-Dental.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-Dental-Studio.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/FaceDoctor.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-Wellness-Suite.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/island-orthopaedic-consultants-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Chinese-Medical-Centre-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/OrthoSports.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-Lasik-Surgery-Clinic.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/True-Chiropractic-Group.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Nexus-Surgical.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Core-Collective-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Glee-Dental-Surgery.png') }}"></div>
					</div>

					<div class="col-md-12 text-center">
						<div class="white-space-20"></div>
						<h2 class="color-blue">and still counting...</h2>
						<div class="white-space-100"></div>
					</div>

					<div class="col-md-12">
						<div class="white-space-100"></div>
						<div class="white-space-20 border-bottom"></div>
						<div class="white-space-20"></div>
					</div>		

					<div class="col-md-12 text-center">
						<h2 class="color-blue">What Our Health Professionals Say...</h2>
					</div>

					<div class="col-md-12 no-padding prof no-padding-top text-center">
						<div class="col-md-12 no-padding xs-text-center md-text-center">
							<div class="testi testi-left">
								<a href="javascript:void(0)"><img src="{{ URL::asset('assets/new_landing/images/left-arrow.png') }}" class="img-responsive"></a>
							</div>
							<img id="first-img" src="{{ URL::asset('assets/new_landing/images/landing/new_images/Dr-Chua-Testimonial-2.png') }}" class="md-margin-center">
							<img id="second-img" src="{{ URL::asset('assets/new_landing/images/landing/new_images/Eric-Testimonial-2.png') }}" class="md-margin-center" style="display: none">
							<div class="testi testi-right">
								<a href="javascript:void(0)"><img src="{{ URL::asset('assets/new_landing/images/right-arrow.png') }}" class="img-responsive"></a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</section>

		<footer id="footer">
			<div class="back-container text-center">
				<a id="scrollToTop" href="javascript:void(0)" class="back-to-top-btn text-center">
					<i class="fa fa-angle-up"></i><br>
					TOP
				</a>
			</div>
			<div class="footer-container">
				<div class="col-md-12 text-center">
					<div class="nav-wrapper">
						<ul class="nav navbar-nav">
							<li>
								<a href="http://blog.mednefits.com/news">
									NEWS
								</a>
							</li>
							<li>
								<a href="http://blog.mednefits.com/">
									BLOG
								</a>
							</li>
							<li>
								<div class="dropdown">
									<ul class="nav">
										<li><a href="/mednefits-care-plan">MEDNEFITS CARE PLAN</a></li>
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
										<li><a href="/">Employers</a></li>
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
										<li><a href="https://talenttribe.asia/companies/mednefits">CAREERS</a></li>
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
										<li><a href="/privacy">PRIVACY POLICY</a></li>
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
					<div class="col-xs-12 col-md-6 no-padding-left">
						<p class=" color-white xs-text-center" style="line-height: 28px;font-size: 19px;">Mednefits, a new kind of health benefits company using technology, data, design to make benefits simple, affordable and human. We believe in designing better healthcare experience for SMEs and our members - the kind we want for ourselves, and our loved ones.
						</p>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="white-space-20"></div>
						<div class="white-space-20"></div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-6 no-padding text-right " >
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
								<li><a href="https://www.instagram.com/mednefits/" class="btn btn-social"><i class="fa fa-instagram"></i></a></li>
							</ul>
						</div>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="white-space-50"></div>
						<div class="white-space-50"></div>
						<div class="white-space-20"></div>
					</div>
				</div>
			</div>
		</footer>

		<div class="side-nav">
			<ul class="nav">
				<li>
					<a href="#main-navbar">
						<span>health pros</span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
				<li>
					<a href="#second-section">
						<span></span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
				<li>
					<a href="#third-section">
						<span>partner access</span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
				<li>
					<a href="#fourth-section">
						<span>testimonials</span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
	
	<div class="sidemenu">
		<ul class="nav">
			<li><a href="/">Employers</a></li>
			<li class="active"><a href="/health-partner">BE A HEALTH PARTNER</a></li>
			<li><a href="http://blog.mednefits.com">BLOG</a></li>
			<li><a href="/get-mednefits">CONTACT US</a></li>
			<li><a href="/app/auth/login">LOGIN</a></li>
		</ul>
	</div>
</body>
	{{ HTML::script('assets/new_landing/js/jquery.min.js') }}
	{{ HTML::script('assets/new_landing/js/bootstrap.min.js') }}
	{{ HTML::script('assets/new_landing/js/parallax.min.js') }}
	{{ HTML::script('assets/new_landing/js/main.js') }}
	<script type="text/javascript">
		// $('.para-1').parallax({
		// 	imageSrc: 'assets/new_landing/images/landing/tiny/bigstock-Yoga-class-110092523.jpg',
		// });

		// $('.para-2').parallax({
		// 	imageSrc: 'assets/new_landing/images/landing/tiny/bigstock--124460930.jpg',
		// });

		$("#scrollToTop").click(function() {
		  $("html, body").animate({ scrollTop: 0 }, "slow");
		  // return false;
		});

		$('a').click(function() {
		    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
		      var target = $(this.hash);
		      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
		      if (target.length) {
		        $('html,body').animate({
		          scrollTop: target.offset().top
		        }, 500);
		        return false;
		      }
		    }
		  });

		var ctr = 0;

		$(".testi a").click(function(){
			if( ctr == 0 ){
				$("#first-img").hide();
				$("#second-img").fadeIn('slow');
				ctr = 1;
			}else{
				$("#first-img").fadeIn('slow');
				$("#second-img").hide();
				ctr = 0;
			}
		});

		setTimeout(function() {
			if( ctr == 0 ){
				$("#first-img").hide();
				$("#second-img").fadeIn('slow');
				ctr = 1;
			}else{
				$("#first-img").fadeIn('slow');
				$("#second-img").hide();
				ctr = 0;
			}

		}, 5000);


	</script>
=======
<!DOCTYPE html>
<html >
<head>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='-1'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>Be a Health Partner — Mednefits: Insurance with Better Benefits</title>
	<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">

	{{ HTML::style('assets/new_landing/css/bootstrap.min.css') }}
	{{ HTML::style('assets/new_landing/css/font-awesome.min.css') }}
	{{ HTML::style('assets/new_landing/css/style.css') }}
	{{ HTML::style('assets/new_landing/css/responsive.css') }}
	<meta property="og:site_name" content="Mednefits: Insurance with Better Benefits"/>
	<meta property="og:title" content="Employers"/>
	<meta property="og:url" content="https://mednefits.com/"/>
	<meta property="og:type" content="website"/>
	<meta property="og:image" content="http://static1.squarespace.com/static/57e8a69c9f7456dca3d4a91d/t/5809c3fdf5e231ac26a0dfc8/1477035006737/mednefits-logo-v3-%28hybrid%29-Medium.png?format=1000w"/>
	<meta property="og:image:width" content="1000"/>
	<meta property="og:image:height" content="241"/>
	<meta itemprop="name" content="Employers"/>
	<meta itemprop="url" content="https://mednefits.com/"/>
	<meta itemprop="thumbnailUrl" content="http://static1.squarespace.com/static/57e8a69c9f7456dca3d4a91d/t/5809c3fdf5e231ac26a0dfc8/1477035006737/mednefits-logo-v3-%28hybrid%29-Medium.png?format=1000w"/>
	<link rel="image_src" href="http://static1.squarespace.com/static/57e8a69c9f7456dca3d4a91d/t/5809c3fdf5e231ac26a0dfc8/1477035006737/mednefits-logo-v3-%28hybrid%29-Medium.png?format=1000w" />
	<meta itemprop="image" content="http://static1.squarespace.com/static/57e8a69c9f7456dca3d4a91d/t/5809c3fdf5e231ac26a0dfc8/1477035006737/mednefits-logo-v3-%28hybrid%29-Medium.png?format=1000w"/>
	<meta name="twitter:title" content="Employers"/>
	<meta name="twitter:image" content="http://static1.squarespace.com/static/57e8a69c9f7456dca3d4a91d/t/5809c3fdf5e231ac26a0dfc8/1477035006737/mednefits-logo-v3-%28hybrid%29-Medium.png?format=1000w"/>
	<meta name="twitter:url" content="https://mednefits.com/"/>
	<meta name="twitter:card" content="summary"/>
	<meta name="description" content="We make health benefits simple, affordable and human to the small 
	businesses and individuals" />

	<style type="text/css">
		header .header-content{
			top: 35%;
		}

		@media only screen and ( max-width: 768px ){
			#third-section{
				background-position: 50% 50% !important	;
			}
		}

		
	</style>
</head>
<body class="health-partner-page">
	<div class="body-container">
		<header class="parallax-window para-1" style="background: url('{{ URL::asset('assets/new_landing/images/landing/tiny/bigstock-Yoga-class-110092523.jpg') }}');background-size: cover;background-position: 0px 50%;">
			<div class="bg-overlap-5"></div>
			<div id="main-navbar">
				<nav class="navbar navbar-default">
				  <!-- <div class="container"> -->
				    <!-- Brand and toggle get grouped for better mobile display -->
				    <div class="navbar-header">
				      <button type="button" class="navbar-toggle collapsed" aria-expanded="false">
				        <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				      </button>
				      <a class="navbar-brand" href="/">
				      	<img src="{{ URL::asset('assets/new_landing/images/landing/logo.png') }}" class="img-responsive">
				      </a>
				    </div>

				    <!-- Collect the nav links, forms, and other content for toggling -->
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				      <ul class="nav navbar-nav navbar-right">
				        <li ><a href="/">Employers</a></li>
				        <!-- <li><a href="/individuals">individuals</a></li> -->
				        <li class="active"><a href="/health-partner">BE A HEALTH PARTNER</a></li>
				        <li><a href="http://blog.mednefits.com/">BLOG</a></li>
				        <li><a href="/our-story">OUR STORY</a></li>
				        <li><a href="/get-mednefits">CONTACT US</a></li>
				        <!-- <li><a href="/app/auth/login">LOGIN</a></li> -->	
				        <!-- <li><a href="http://blog.mednefits.com/">BLOG</a></li> -->
				        <li><a href="/app/auth/login">HEALTH PARTNER'S LOGIN</a></li>
				        <!-- <li><a href="/try-three-months">Try 3 months free</a></li> -->
				        <!-- <li><a href="get-mednefits">GET MEDNEFITS NOW</a></li> -->
				      </ul>
				    </div><!-- /.navbar-collapse -->
				  <!-- </div> -->
				</nav>
			</div>
				
			<div class="header-content">
				<div class="col-md-8 col-md-offset-2">
					<h1 class="text-center color-white width-80">JOIN US AND BE PART OF OUR NETWORK</h1>
					<p class="text-center text-italic color-white">Our health professionals grow their business with us by attracting new customers from our corporate employees, who are looking for the best health and wellness professionals.</p>
					<div class="white-space-20 sm-hide"></div>
					<div class="white-space-20 sm-hide"></div>
					<button onclick="window.location.href='/get-mednefits'" class="btn btn-info">JOIN US NOW</button>
				</div>
			</div>	

			<div class="header-arrow">
				<a id="scroll" href="#main-section"> 
					<!-- <i class="fa fa-angle-down color-white"></i>  -->
					<img src="{{ URL::asset('assets/new_landing/images/down-arrow-black.png') }}" class="margin-center">
					<p class="color-black ">SCROLL DOWN</p>
				</a>
			</div>
		</header>

		<section id="main-section">

			<div id="second-section" class="content_section text-center how-it-works">
				<div class="col-md-10 col-md-offset-1">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<h1 class="color-blue">What Mednefits Can Do For You</h1>
						<h2 class="color-blue">Be part of our healthy and happy movement.</h2>
						<p class="width-90">Today's employers are putting their employees well-being at the forefront thanks to Mednefits digital benefits.</p>
					</div>

					<div class="col-xs-12 col-sm-4 col-md-4 text-center">
						<img src="{{ URL::asset('assets/new_landing/images/landing/can1.jpg') }}" class="img-responsive margin-center">
						<h2 class="color-blue">Expand Your Business Horizons</h2>
						<p>Gain access to Mednefits customers through the digital way. Our corporate employees look for clinics, classes,  seminars, plus on-demand services at your location.</p>
					</div>

					<div class="col-xs-12 col-sm-4 col-md-4 text-center">
						<img src="{{ URL::asset('assets/new_landing/images/landing/can2.jpg') }}" class="img-responsive margin-center">
						<h2 class="color-blue">Tap Into Mednefits's Copayment Scheme</h2>
						<p>Our corporate employees receive member rates for health and wellness services when they join us. Offer your services to our pool of customers who can now be healthy at a lower cost.</p>
					</div>

					<div class="col-xs-12 col-sm-4 col-md-4 text-center">
						<img src="{{ URL::asset('assets/new_landing/images/landing/can3.jpg') }}" class="img-responsive margin-center">
						<h2 class="color-blue">Extend Your Online Presence</h2>
						<p>Online listing, booking, payments and invoicing all in Mednefits platform to take your business to the next level.</p>
					</div>
				</div>
			</div>

			<div id="third-section" class="content_section parallax-window para-2 text-center" style="background: url('{{ URL::asset('assets/new_landing/images/landing/tiny/bigstock--124460930.jpg') }}');background-size: cover;background-position: 0px 50%;">
				<div class="bg-overlap-4"></div>
				<div class="content ">
					<h1 class="color-white ">QUALIFIED PARTNERS CAN USE MEDNEFITS FOR FREE</h1>
					<p class="text-center text-italic color-white">Connect to multi-billion dollar healthcare & wellness market for free with Mednefits.</p>
					<p class="text-center text-italic color-white">There are no subscription, referral, admin fees required. We only need corporate rates for our clients.</p>
				
					<button onclick="window.location.href='/get-mednefits'" class="btn btn-info">JOIN US NOW</button>
				</div>
			</div>

			<div id="fourth-section" class="content_section priority">
				<div class="col-md-10 col-md-offset-1">
					<div class="col-md-12  text-center margin-bottom-70">
						<h1 class="color-blue width-90 font-52 margin-center">Our Health Partners Share Our Vision For Better Benefits</h1>
					</div>
	 
					<div class="col-md-12 text-center partners-container" style="overflow: hidden">
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Medical-Partners-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Drs+Chua+&+Partners.jpg') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/SMG-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/North-East-Medical-Group.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/One-Care-Medical.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Life-Family-Clinic.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Dr-Tan-+-Partners.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Healthwerkz-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Phoenix-Medical-Group.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/crawfurd-medical-2.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Hisemainn-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Tan-Teoh-Clinic.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/pinnacle-family-clinic-logo.jpg') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Accord-Medical-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/lifescan-Medical-Centre.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Vienna-Medical.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-iClinic.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/iDental-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/True-Dental-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Teeth@Tiong-Bahru-2.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Shuang-Dentistry-Logo.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Advanced-Dental.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Fusion-Dental.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-Dental-Studio.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Only-Aesthetics-2.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/FaceDoctor.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-Wellness-Suite.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/island-orthopaedic-consultants-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Chinese-Medical-Centre-2.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/Preciouz-Kare.png') }}"></div>

						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/OrthoSports.png') }}"></div>
						<div class="img-wrapper"><img src="{{ URL::asset('assets/new_landing/images/landing/new_images/The-Lasik-Surgery-Clinic.png') }}"></div>
					</div>

					<div class="col-md-12 text-center">
						<div class="white-space-20"></div>
						<h2 class="color-blue">and still counting...</h2>
						<div class="white-space-100"></div>
					</div>

					<div class="col-md-12">
						<div class="white-space-100"></div>
						<div class="white-space-20 border-bottom"></div>
						<div class="white-space-20"></div>
					</div>		

					<div class="col-md-12 text-center">
						<h2 class="color-blue">What Our Health Professionals Say...</h2>
					</div>

					<div class="col-md-12 no-padding prof no-padding-top text-center">
						<div class="col-md-12 no-padding xs-text-center md-text-center">
							<div class="testi testi-left">
								<a href="javascript:void(0)"><img src="{{ URL::asset('assets/new_landing/images/left-arrow.png') }}" class="img-responsive"></a>
							</div>
							<img id="first-img" src="{{ URL::asset('assets/new_landing/images/landing/new_images/Dr-Chua-Testimonial-2.png') }}" class="md-margin-center">
							<img id="second-img" src="{{ URL::asset('assets/new_landing/images/landing/new_images/Eric-Testimonial-2.png') }}" class="md-margin-center" style="display: none">
							<div class="testi testi-right">
								<a href="javascript:void(0)"><img src="{{ URL::asset('assets/new_landing/images/right-arrow.png') }}" class="img-responsive"></a>
							</div>
						</div>
						<!-- <div class="col-md-10">
							<p class="text-italic xs-text-center" style="font-size: 18px;line-height: 1.3em;font-weight: 400;padding: 0 20px">“ Mednefits is a game changer in the medical industry. It is breathtaking how technology can change the traditional healthcare industry. Now patient don’t have to queue or call to visit their doctor. Not only do Mednefits assist in convenience booking, they help by bringing patients’ healthcare cost down. Mednefits is the next step into digital health future.”</p>
							<p class="text-right xs-text-center">
								— Eric Benghozi, Founder of Medical Partners
							</p>
						</div> -->
					</div>

					<!-- <div class="col-md-12 no-padding prof">
						<div class="col-md-2 xs-text-center">
							<img src="assets/new_landing/images/landing/avatar2.png" class="img-responsive xs-inline-block xs-margin-btm-20 md-margin-center">
						</div>
						<div class="col-md-10">
							<p class="text-italic xs-text-center md-text-center" style="font-size: 18px;line-height: 1.3em;font-weight: 400;padding: 0 20px">“Word of mouth and obviously referrals are a huge part of the business, but leveraging on technology to enhance my practice’s visibility has been very beneficial for me. Mednefits is definitely the platform to be on.”</p>
							<p class="text-right xs-text-center">
								— Dr Kevin Chua, Drs Chua & Partners (AV) Pte Ltd
							</p>
						</div>
					</div> -->

					

				</div>
			</div>
		</section>

		<footer id="footer">
			<div class="back-container text-center">
				<a id="scrollToTop" href="javascript:void(0)" class="back-to-top-btn text-center">
					<i class="fa fa-angle-up"></i><br>
					TOP
				</a>
			</div>
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
										<li><a href="/mednefits-care-plan">MEDNEFITS CARE PLAN</a></li>
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
										<li><a href="/">Employers</a></li>
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
										<li><a href="/privacy">PRIVACY POLICY</a></li>
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
						<p class=" color-white">Mednefits, a new kind of health benefits company using technology, data, design to make benefits simple, affordable and human. We believe in designing better healthcare experience for SMEs and individuals - the kind we want for ourselves, and our loved ones.
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
								<li><a href="https://www.instagram.com/mednefits/" class="btn btn-social"><i class="fa fa-instagram"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</footer>

		<div class="side-nav">
			<ul class="nav">
				<li>
					<a href="#main-navbar">
						<span>health pros</span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
				<li>
					<a href="#second-section">
						<span></span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
				<li>
					<a href="#third-section">
						<span>partner access</span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
				<li>
					<a href="#fourth-section">
						<span>testimonials</span>
						<i class="fa fa-circle"></i>
						<i class="fa fa-circle-o"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
	
	<div class="sidemenu">
		<ul class="nav">
			<li ><a href="/">Employers</a></li>
			<li><a href="/individuals">INDIVIDUALS</a></li>
			<li class="active"><a href="/health-partner">BE A HEALTH PARTNER</a></li>
			<li><a href="/our-story">OUR STORY</a></li>
			<li><a href="/app/auth/login">LOGIN</a></li>
			<li class="try-three-months-li"><a href="/try-three-months">TRY 3 MONTHS FREE</a></li>
		</ul>
	</div>
</body>
	{{ HTML::script('assets/new_landing/js/jquery.min.js') }}
	{{ HTML::script('assets/new_landing/js/bootstrap.min.js') }}
	{{ HTML::script('assets/new_landing/js/parallax.min.js') }}
	{{ HTML::script('assets/new_landing/js/main.js') }}
	<script type="text/javascript">
		// $('.para-1').parallax({
		// 	imageSrc: 'assets/new_landing/images/landing/tiny/bigstock-Yoga-class-110092523.jpg',
		// });

		// $('.para-2').parallax({
		// 	imageSrc: 'assets/new_landing/images/landing/tiny/bigstock--124460930.jpg',
		// });

		$("#scrollToTop").click(function() {
		  $("html, body").animate({ scrollTop: 0 }, "slow");
		  // return false;
		});

		$('a').click(function() {
		    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
		      var target = $(this.hash);
		      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
		      if (target.length) {
		        $('html,body').animate({
		          scrollTop: target.offset().top
		        }, 500);
		        return false;
		      }
		    }
		  });

		var ctr = 0;

		$(".testi a").click(function(){
			if( ctr == 0 ){
				$("#first-img").hide();
				$("#second-img").fadeIn('slow');
				ctr = 1;
			}else{
				$("#first-img").fadeIn('slow');
				$("#second-img").hide();
				ctr = 0;
			}
		});

		setTimeout(function() {
			if( ctr == 0 ){
				$("#first-img").hide();
				$("#second-img").fadeIn('slow');
				ctr = 1;
			}else{
				$("#first-img").fadeIn('slow');
				$("#second-img").hide();
				ctr = 0;
			}

		}, 5000);


	</script>
</html>