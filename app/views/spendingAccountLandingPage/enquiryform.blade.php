<!DOCTYPE html>
<html ng-app="enquiry">
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
  <link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/pre-loader.css?_={{ $date->format('U') }}">
  <link rel="stylesheet" href="<?php echo $server; ?>/assets/hr-dashboard/css/sweetalert.css?_={{ $date->format('U') }}">


  <style>
    *{
      box-sizing: border-box;
    }
    html, body{
      height: 100%;
      width: 100%;
      background: #FFF;
      font-family: 'Helvetica Light';
      color: #0E0E0E;
      font-size: 16px;
      font-weight: 700;
      overflow-x: hidden;
      /* overflow: hidden; */
      margin: 0;
      padding: 0;
      letter-spacing: .14px;
      line-height: 1.42857143;
      -webkit-tap-highlight-color: transparent;
      -webkit-touch-callout: none;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    a{
      color: #3192CF;
    }
    b{
      font-family: 'Helvetica Medium';
    }
    #main-content{

    }
    .logo-wrapper{
      text-align: center;
    }
    .logo-wrapper img{
      margin: 50px auto 135px auto;
      width: 210px;
    }
    .user-info-wrapper{
      max-width: 500px;
      margin: 0 auto;
    }
    .user-info-wrapper > p{
      margin-bottom: 45px;
      text-align: center;
      font-size: 20px;
    }
    .form-wrapper{
      max-width: 520px;
      margin: 0 auto;
      padding-right: 60px;
      padding-bottom: 150px;
    }
    .form-wrapper .form-box{
      display: flex;
      flex-wrap: wrap;
    }
    .form-wrapper label{
      flex: 1;
      font-size: 16px;
      margin-top: 7px;
    }
    .form-wrapper input,
    .form-wrapper textarea{
      width: 331px;
      resize: none;
      margin-bottom: 10px;
      background: #FFFFFF;
      border: 1px solid #BFBFBF;
      border-radius: 3px;
      min-height: 38px;
      font-size: 14px;
      color: #000000;
      padding: 10px;
      font-family: 'Helvetica Light';
      font-weight: 700;
    }
    .form-wrapper input{
      background: #F2F2F2;
    }
    .form-wrapper input::placeholder,
    .form-wrapper textarea::placeholder{
      color: rgba(132, 132, 132, 0.55);
      font-family: 'Helvetica Light';
      font-weight: 700;
    }
    .btn-container{
      display: flex;
    }
    .btn-container div{
      flex: 1
    }
    .btn-container button{
      width: 331px;
      height: 38px;
      background: #0392CF;
      border-radius: 4px;
      color: #FFFFFF;
      font-size: 15px;
      line-height: 18px;
      margin-top: 25px;
      border: none;
      font-weight: 700;
      font-family: 'Helvetica Light';
    }

    @media (max-width: 768px) {
      .form-wrapper {
        max-width: 520px;
        margin: 0 auto;
        padding-right: 60px;
        padding: 0 20px 150px 20px;
        /* padding-bottom: 150px; */
      }
      .form-wrapper label{
        width: 100%;
        flex: none;
        font-size: 14px;
        margin-bottom: 5px;
      }
      .form-wrapper input,
      .form-wrapper textarea{
        flex: none;
        width: 100%;
      }
      .btn-container div{
        display: none;
      }
      .btn-container button{
        width: 100%;
      }
    }
  </style>
</head>
<body enquiry-directive>
	<div id="main-content">
    <div class="logo-wrapper">
      <img src="../assets/images/mednefits_logo_latest.png" alt="">
    </div>

    <div class="user-info-wrapper">
      <p><b>Hi <span ng-bind="companyDetails.contact_name"></span>,<br>We are here to help!</b></p>
    </div>

    <div class="form-wrapper">
      
      <form>
        <div class="form-box">
          <label>Subject</label>
          <input type="text" value="Activate Spending Account" readonly>
        </div>
        <div class="form-box">
          <label>Your Enquiry</label>
          <textarea cols="30" rows="10" placeholder="Please type in your enquiry" ng-model="companyDetails.message"></textarea>
        </div>

        <div class="btn-container">
          <div></div>
          <button class="btn" ng-click="submitEnquiry(companyDetails)">Submit</button>
        </div>
      </form>
    </div>

  </div>
  
  <div class="circle-loader" hidden>
		<div class="preloader-container" style="width: auto !important;">
			<div class="white-space-50"></div>
			<div class="preloader-wrapper big active">
		    <div class="spinner-layer spinner-blue-only">
		      <div class="circle-clipper left">
		        <div class="circle"></div>
		      </div><div class="gap-patch">
		        <div class="circle"></div>
		      </div><div class="circle-clipper right">
		        <div class="circle"></div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</body>

<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/jquery.min.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/angular.min.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/js/sweetalert.min.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/hr-dashboard/process/enquiry.js?_={{ $date->format('U') }}"></script>
	

</html>
