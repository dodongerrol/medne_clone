<!DOCTYPE html>
<html ng-app="app">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-78188906-2');
	</script>
	<!-- Facebook Pixel Code -->
	
	<script>!function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments) }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s) }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '165152804138364'); fbq('track', 'PageView');</script>
	<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=165152804138364&ev=PageView(44 B)https://www.facebook.com/tr?id=165152804138364&ev=PageView&noscript=1" /></noscript>
	<!-- End Facebook Pixel Code -->
	<!-- <base href="/company-benefits-dashboard/"></base> -->
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Mednefits: Modern Employees Digital Benefits</title>
  <link rel="shortcut icon" href="<?php echo $server; ?>/assets/new_landing/images/favicon.ico" type="image/ico">
  
  <link rel="stylesheet" href="<?php echo $server; ?>/assets/css/reset.css?_={{ $date->format('U') }}">
  <link rel="stylesheet" href="<?php echo $server; ?>/assets/css/fonts.css?_={{ $date->format('U') }}">
  <link rel="stylesheet" href="<?php echo $server; ?>/assets/css/spendingAccountLandingPage.css?_={{ $date->format('U') }}">

</head>
<body>
	<div id="main-content">
		<div class="header-container">
			<div class="header-wrapper">
				<div class="logo-wrapper">
					<img src="../assets/images/mednefits_logo_latest.png" alt="">
				</div>

				<div class="banner-text-wrapper">
					<p><b>What is my Spending Account?</b></p>
					<p>Your spending account enables you to enjoy the full benefits of the Mednefits platform with <b>cashless payments</b> at Mednefits panel providers and <b>e-claim submissions</b> at non-panel providers</p>
					<p>What this means for your team:</p>

				</div>
			</div>

			<div class="body-wrapper">
				<section id="one">
					<div class="row-box">
						<div class="col-box md-flex-2 flex-3">
							<div class="img-wrapper">
								<img src="../images/spendingImages/image_1.png" alt="">
							</div>
						</div>
						<div class="col-box">
							<p class="description-p">Simply allocate credits to enjoy a cashless experience.</p>
						</div>
					</div>
				</section>

				<section id="two">
					<div class="row-box">
						<div class="col-box">
							<p class="description-p">Capture real-time data on employee benefits usage. </p>
						</div>
						<div class="col-box md-flex-2 flex-3">
							<div class="img-wrapper">
								<img src="../images/spendingImages/image_2.png" alt="">
							</div>
						</div>
					</div>
				</section>

				<section id="three">
					<div class="row-box">
						<div class="col-box md-flex-2 flex-3">
							<div class="img-wrapper">
								<img src="../images/spendingImages/image_3.png" alt="">
							</div>
						</div>
						<div class="col-box">
							<p class="description-p">Scan receipts to store and process non-panel claims digitally. </p>
						</div>
					</div>
				</section>
			</div>

			<section class="how-it-works-wrapper">
				<p><b>How it works?</b></p>
				<div class="steps-wrapper">
					<div class="row-box">
						<div class="col-box">
							<div class="number-wrapper one">
								<div class="num">1</div>
							</div>
							<div class="step-description">
								<p>Mail us your completed <a href="https://mednefits.s3-ap-southeast-1.amazonaws.com/pdf/GIRO+Application+Form+(new).pdf" target="_blank" >GIRO form</a> to the address specified in the form.</p>
							</div>
						</div>

						<div class="col-box">
							<div class="number-wrapper two">
								<div class="num">2</div>
							</div>
							<div class="step-description">
								<p>Spending Account activated</p>
							</div>
						</div>

						<div class="col-box">
							<div class="number-wrapper three">
								<div class="num">3</div>
							</div>
							<div class="step-description">
								<p>Distribute allocations to employees.</p>
							</div>
						</div>

						<div class="col-box">
							<div class="number-wrapper four">
								<div class="num">4</div>
							</div>
							<div class="step-description">
								<p>Receive a consolidated monthly bill payment confirmation.</p>
							</div>
						</div>

						<div class="col-box">
							<div class="number-wrapper five">
								<div class="num">5</div>
							</div>
							<div class="step-description">
								<p>Payments processed by GIRO.</p>
							</div>
						</div>
					</div>
				</div>
				
			</section>

			<div class="footer-wrapper">
				<p>If you wish to open a Spending Account, please contact us<br>through <a href="/enquiry-form" target="_blank" >this form</a>.</p>
			</div>

		</div>
	</div>
</body>

	

</html>
