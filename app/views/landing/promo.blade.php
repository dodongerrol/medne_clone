<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-78188906-2"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-78188906-2');
  </script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Medicloud - promo</title>

    <!-- Bootstrap -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
    {{ HTML::style('assets/landing/css/bootstrap.min.css') }}
    <link href='https://fonts.googleapis.com/css?family=Oxygen:400,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Raleway:300|Source+Sans+Pro" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-8344843655918366",
        enable_page_level_ads: true
      });
    </script>
    <!-- <link href="css/medicloud.css" rel="stylesheet" type="text/css">
    <link href="css/customized.css" rel="stylesheet" type="text/css">
    <link href="css/overwrite.css" rel="stylesheet" type="text/css">
    <link href="css/responsive.css" rel="stylesheet" type="text/css"> -->

    <link rel="shortcut icon" href="{{ asset('assets/landing/img/favi.ico') }}" type="image/ico">

    {{ HTML::style('assets/landing/css/medicloud.css') }}
    {{ HTML::style('assets/landing/css/customized.css') }}
    {{ HTML::style('assets/landing/css/overwrite.css') }}
    {{ HTML::style('assets/landing/css/responsive.css') }}

    <!-- <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script> -->

    {{ HTML::script('assets/landing/js/jquery.min.js') }}
    {{ HTML::script('assets/landing/js/bootstrap.min.js') }}
    

     <!-- Bootstrap video -->
     {{ HTML::style('assets/landing/plugins/plyr-master/dist/plyr.css') }}
     {{ HTML::script('assets/landing/plugins/plyr-master/dist/plyr.js') }}


      <!-- Facebook Pixel Code -->
      <script>
      !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
      n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
      document,'script','https://connect.facebook.net/en_US/fbevents.js');

      fbq('init', '300800066938054');
      fbq('track', "ViewContent");</script>
      <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=300800066938054&ev=ViewContent&noscript=1"
      /></noscript>
      <!-- End Facebook Pixel Code -->
      <!-- Google Analytics -->
      <!--script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-78307319-2', 'auto');
        ga('send', 'pageview');

       </script-->
</head>
<body>

<nav class="navbar navbar-absolute navbar-others navbar-web" >
    <div class="container">
      <div class="navbar-header " >
        <button type="button " class="navbar-toggle collapsed HP-icon" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <div class="icon-bar-margin"></div>
          <span class="icon-bar"></span>
          <div class="icon-bar-margin"></div>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index" >
          <img src="assets/landing/img/mc-logov2.png" class="img-80 xs-img-60"  alt=""/>
        </a>
      </div>
      <div id="navbar" class="navbar-collapse collapse HP-navs">
        <ul class="nav navbar-nav">
          <li class="nav-link"><a href="index">HOME</a></li>
          <li class="nav-link"><a href="health-professionals">FOR HEALTH PROFESSIONALS  </a></li>
          <li class="nav-link"><a href="corporate">CORPORATE</a></li>
        </ul>

        <ul class="nav navbar-nav md-navbar-right HP-nav-right">
          <li class="nav-link"><a href="https://medicloud.sg/medicloud_v2/public/app/auth/login">Log In </a></li>
          <li class=""><button id="scrollToForm" class="btn-custom btn-custom-primary">Sign Up</button></li>
        </ul>

      </div><!--/.nav-collapse -->
    </div>
  </nav>

<nav class="navbar navbar-absolute navbar-mobile">

  <div class="container">
    <button id="show-side-menu" class="navbar-toggle HP-icon" >
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <div class="icon-bar-margin"></div>
      <span class="icon-bar"></span>
      <div class="icon-bar-margin"></div>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-logo" href="index">
      <img src="assets/landing/img/mc-logov2.png" class=""  alt=""/>
    </a>
  </div>
</nav>

<div class="body-bg-overlay bg-hide"></div>

<div class="side-menu side-menu-hide"> 
  <div class="bg-overlay"></div>
  <div class="side-menu-header"> 

    <a href="#" id="hide-side-menu" class="" style="z-index: 10">
      <img src="assets/landing/img/new-assets/remove.svg" class="pull-right close-side-menu"  alt=""/>
    </a>
  </div>
  <ul class="nav mobile-side-menu-nav">
    <li><a href="index"> <img src="assets/landing/img/new-assets/house.svg" class="" /> <span>HOME</span> </a></li>
    <li><a href="health-professionals"> <img src="assets/landing/img/new-assets/doctor.svg" class="" /> <span>HEALTH PROFESSIONALS</span> </a></li>
    <li><a href="corporate"> <img src="assets/landing/img/new-assets/manager.svg" class="" /> <span>CORPORATE</span> </a></li>
  </ul> 
</div>  

<div id="promo-header">
  <div class="header-wrapper">
    
  </div>
</div>

<div class="promo promo-section-one block-section padding-ups-50 text-center">
  <div class="col-md-12 text-center">
    <h1 class="color-white">Medicloud Exclusive Privilege</h1>
    <div class="white-space-20"></div>
  </div>


  <div class="col-xs-12 col-sm-6 col-md-6 xs-text-center md-text-right">
    <div class="app-btn"> 
      <a href="https://itunes.apple.com/us/app/medicloud/id972694931?mt=8">
        <img src="assets/landing/img/app-store.svg" class="img-40 xs-img-60"/>
      </a>
     </div>
     <div class="white-space-20"></div>
  </div>


  <div class="col-xs-12 col-sm-6 col-md-6 xs-text-center md-text-left">
    <div class="app-btn">
      <a href="https://play.google.com/store/apps/details?id=com.sg.medicloud&hl=en">
        <img src="assets/landing/img/google-play.svg" class="img-40 xs-img-60" />
      </a>
     </div>
     <div class="white-space-20"></div>
  </div>
</div>

<div class="promo  promo-section-two block-section text-center">
  <div class="container">
    <div class="col-md-12">
       <h3>Medicloud offer you exclusive privileges for all your medical and dental needs. </h3>
       <div class="white-space-20"></div>
       <div class="white-space-20"></div>
       <div class="white-space-20"></div>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-4">
      <img src="assets/landing/img/arrow.png" class="img-responsive"  alt=""/>
      <br>
      <h2>Convenience</h2>
      <div class="white-space-20"></div>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-4 ">
      <img src="assets/landing/img/time.png" class="img-responsive"  alt=""/>
      <br>
      <h2>Real time availability</h2>
      <div class="white-space-20"></div>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-4">
      <img src="assets/landing/img/Saving.png" class="img-responsive"  alt=""/>
      <br>
      <h2>Cost saving</h2>
      <div class="white-space-20"></div>
    </div>
  </div>
</div>

<div class="promo  promo-section-three block-section-md">
  <div class="container">
    <div class="col-md-12">
      <h3>How to get your $10 savings !</h3>
    </div>

    <div class="col-md-12">
      <div class="terms-list">
        <ol>
        <li>Download the Medicloud app from the App Store or Google Play and register your account. If you already have the Medicloud app, please ensure you have updated to the latest version.</li>
        <li>Search by speciality, clinic name, doctor’s name from the search bar, or the speciality tap on the bottom-left hand corner.</li>
        <li>Select the procedure you would like to book.</li>
        <li>Select a doctor and hit “BOOK NOW” button.</li>
        <li>Choose the available date or time you would like your appointment to be.</li>
        <li>Once confirmed, key in OTP code for verification purposes.</li>
        <li>On the day of appointment, show the clinic personnel your appointment record in Medicloud app (under Menu > Appointments), to enjoy $10 saving off your bill.</li>
        </ol>
      </div>
    </div>

    <div class="col-md-12">
      <h3>Terms & Conditions</h3>
    </div>

    <div class="col-md-12">
      <div class="terms-list">
        <ul>
          <li>Medicloud Special means 10 dollars savings of all procedures booked via Medicloud’s mobile app.</li>
          <li>Medicloud means Medicloud Pte Ltd.</li>
          <li>Medicloud Special is valid till 30 September 2016 </li>
          <li>The Medicloud Special of 10 dollars savings are co-paid by Medicloud Pte Ltd.</li>
          <li>All prices shown in Medicloud’s mobile app are the original price set by Medicloud’s providers, and subject to GST.</li>
          <li>The 10 dollars savings will be applied for each realised transaction when payment is made in the clinic.</li>
          <li>The 10 dollars savings will be applied to the final bill (before GST if any) upon completion of the procedures held in the clinic.</li>
          <li>Medicloud Special excludes appointment using insurance card.</li>
          <li>Medicloud Special is subject to Medicloud’s providers availability and confirmation.</li>
          <li>All payments are solely made to and handled by Medicloud’s providers.</li>
          <li>Medicloud Special is mutually exclusive and does not work in conjunction with other existing medicloud promotion.</li>
          <li>Medicloud shall not at any time be responsible or held liable for any loss, injury, damage, or harm suffered by or in connection with the products and/or services provided by this parties.</li>
          <li>Medicloud and Medicloud’s providers reserve the right at their absolute discretion to terminate or amend the promotion or vary, delete or add to any of these terms and conditions from time to time without notice. </li>
        </ul>
      </div>
    </div>
  </div>
</div>



<div class="footer">
  <div class="container-fluid">
    <div class="col-xs-12 col-sm-6 col-md-6 ">
      <div class="col-md-12 no-padding">
        <p class="footer-title"><span>About</span></p>
      <p>
      Medicloud is a trusted healthcare and wellness marketplace <br />
      for health professionals to list their services and customers  <br />
      to book appointments online or from a mobile phone. <br />

      With Medicloud platform, health professionals will be able to  <br />
      connect with and provide a rich personalized service to their   <br />
      customers.
      </p>
      </div>

      <div class="col-md-12 no-padding footer-copyright xs-hide">
      <p style="margin-bottom: 0">© 2015 Medicloud Pte. Ltd. All rights reserved</p>
      <a href="terms">Terms of Use</a> | <a href="privacy-policy">Privacy Policy </a> 
      </div>
      
    </div>

    <div class="col-xs-6 col-sm-3 col-md-3 ">
      <p class="footer-title"><span>Learn More</span></p>
      <ul class="nav">
        <li><a href="health-professionals">Health Professionals</a></li>
        <li><a href="corporate">Corporate</a></li>
        <li><a href="promo">Promotions</a></li>
        <li><a href="privacy-policy">Privacy</a></li>
        <li><a href="terms">Terms</a></li>
      </ul>
    </div>
    
    <div class="col-xs-6 col-sm-3 col-md-3 ">
      <div class="col-md-12 no-padding footer-social xs-hide">
        <p class="footer-title"><span>Connect</span></p>
          <div class="social">
            <a href="https://www.facebook.com/medicloudsg/">
              <img src="assets/landing/img/new-assets/facebook-logo.svg" width="30" height="30"  alt=""/>
            </a>
          </div>
          <div class="social">
            <a href="https://www.instagram.com/medicloud/?hl=en">
              <img src="assets/landing/img/new-assets/instagram-symbol.svg" width="30" height="30"  alt=""/>
            </a>
          </div>
          <div class="social">
            <a href="http://bit.ly/1Uh2Q2s">
              <img src="assets/landing/img/new-assets/linkedin-sign.svg" width="30" height="30"  alt=""/>
             </a>
          </div>
          <div class="white-space-20"></div>
      </div>

      <div class="col-md-12 no-padding">
        <p class="footer-title"><span>Contact</span></p>
    <p>
    1 Temasek Boulevard #18-02<br />
    Suntec Tower One<br />
    Singapore 038987<br /><br />

    (65) 6635 8374<br />
    <a class="f1" href="mailto:info@medicloud.sg">info@medicloud.sg</a> 
    </p>
      </div>
      
    </div>

    <div class="col-xs-12 col-sm-3 col-md-2 footer-social sm-text-right xs-text-center xs-show">
       <div class="social">
      <a href="https://www.facebook.com/medicloudsg/">
        <img src="assets/landing/img/new-assets/facebook-logo.svg" width="30" height="30"  alt=""/>
      </a>
    </div>
    <div class="social">
      <a href="https://www.instagram.com/medicloud/?hl=en">
        <img src="assets/landing/img/new-assets/instagram-symbol.svg" width="30" height="30"  alt=""/>
      </a>
    </div>
    <div class="social">
      <a href="http://bit.ly/1Uh2Q2s">
        <img src="assets/landing/img/new-assets/linkedin-sign.svg" width="30" height="30"  alt=""/>
       </a>
    </div>
    </div>

    <div class="col-xs-12 col-sm-9 col-md-9 footer-copyright xs-text-center xs-dl-app-text-show xs-show">
      <p style="margin-bottom: 0">© 2015 Medicloud Pte. Ltd. All rights reserved</p>
      <a href="terms">Terms of Use</a> | <a href="privacy-policy">Privacy Policy </a> 
    </div>
  </div>
</div>
  <script type="text/javascript">
    $( "#show-side-menu" ).click(function(){
      $( ".side-menu" ).removeClass("side-menu-hide");
      $( ".side-menu" ).addClass("side-menu-show");

      $( ".body-bg-overlay" ).removeClass("bg-hide");
      $( ".body-bg-overlay" ).addClass("bg-show");
    });

    $( "#hide-side-menu" ).click(function(){
      $( ".side-menu" ).addClass("side-menu-hide");
      $( ".side-menu" ).removeClass("side-menu-show");

      $( ".body-bg-overlay" ).addClass("bg-hide");
      $( ".body-bg-overlay" ).removeClass("bg-show");
    });
    
  </script>

</body>
</html>
