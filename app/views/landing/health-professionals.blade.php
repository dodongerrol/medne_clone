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
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Medicloud -HEALTH PROFESSIONALS</title>

    <!-- Bootstrap -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css>
    <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
    {{ HTML::style('assets/landing/css/bootstrap.min.css') }}
    <link href='https://fonts.googleapis.com/css?family=Oxygen:400,700' rel='stylesheet' type='text/css'>
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


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script> -->


     <!-- Bootstrap video -->
    <!--  <link href="plugins/plyr-master/dist/plyr.css" rel="stylesheet" type="text/css">
     <script type="text/javascript" src="plugins/plyr-master/dist/plyr.js"></script> -->
      {{ HTML::style('assets/landing/plugins/plyr-master/dist/plyr.css') }}
     {{ HTML::script('assets/landing/plugins/plyr-master/dist/plyr.js') }}
     

     {{ HTML::script('assets/landing/js/bootstrap.min.js') }}
    {{ HTML::script('assets/landing/js/script.js') }}

    


    <!-- Rangetouch to fix <input type="range"> on touch devices (see https://rangetouch.com) -->
    <!-- <script src="https://cdn.rangetouch.com/0.0.9/rangetouch.js"></script> -->

    

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

<!--main navigation-->
  <nav class="navbar navbar-absolute navbar-web" >
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
          <li class="nav-link"><a href="/">HOME</a></li>
          <li class="nav-link active2"><a href="health-professionals">FOR HEALTH PROFESSIONALS  </a></li>
          <li class="nav-link"><a href="corporate">CORPORATE</a></li>
        </ul>

        <ul class="nav navbar-nav md-navbar-right HP-nav-right">
          <li class="nav-link"><a href="/app/login">Log In </a></li>
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
    <li class="active"><a href="health-professionals"> <img src="assets/landing/img/new-assets/doctor.svg" class="" /> <span>HEALTH PROFESSIONALS</span> </a></li>
    <li><a href="corporate"> <img src="assets/landing/img/new-assets/manager.svg" class="" /> <span>CORPORATE</span> </a></li>
  </ul> 
</div>  

<div id="header2">
  <div class="header-wrapper">
    <div class="container-fluid">
      <div class="info-popbox text-center" >
        <h4 class="info-poptitle margin-center"> 
          <b>
          JOIN US <br> 
          AND BE PART  OF OUR NETWORK
          </b>
        </h4>
        <div class="blue-line" style="width: 20%"></div>
        <p class="info-popdetail margin-center">
          Our Health Professionals grow their business with us, from attracting new customers to reducing no - show. Medicloud brings healthcare  and wellness to the next level
        </p>
        <br>
        <a href="#form-contact" class="btn-custom btn-custom-primary img-90 margin-center">Get Started Now</a>
      </div>
    </div>
  </div>
</div>

<div class="section-two2">
  <div class="col-md-12 text-center">
    <h2 class="section-title">
      WHAT MEDICLOUD CAN DO FOR YOUR BUSINESS
    </h2>
  </div>

  <div class="col-xs-12 col-sm-6 col-md-6 text-center content">
      <div class="content-icon">
        <img src="assets/landing/img/icn-step1.svg" class="img-30"/> 
      </div>
      <h5 class="content-title">
        EXPAND YOUR HORIZONS
      </h5>
      <p class="content-detail">
        Gain access to Medicloud customers through the digital way. Our customers look for clinics,
        classes, workshops, plus on-demand services online, at home or at your location.
      </p>
  </div>

  <div class="col-xs-12 col-sm-6 col-md-6 text-center content">
      <div class="content-icon">
        <img src="assets/landing/img/icn-step2.svg" class="img-30"/> 
      </div>
      <h5 class="content-title">
        MAXIMISE YOUR AVAILABILITY
      </h5>
      <p class="content-detail">
        Fill last minute openings in your calender caused by cancellations and rescheduling. With Medicloud,
        we help you to increase your revenue and be there for your customers when they need you.
      </p>
  </div>

  <div class="col-xs-12 col-sm-6 col-md-6 text-center content">
      <div class="content-icon">
        <img src="assets/landing/img/icn-step3.svg" class="img-30"/> 
      </div>
      <h5 class="content-title">
        BUILD CUSTOMER LOYALTY
      </h5>
      <p class="content-detail">
        Strengthen your customers relationships by offering a premium convenience of Medicloud
        from simple booking, custom reminders to re-book function of their favourite providers.
      </p>
  </div>

  <div class="col-xs-12 col-sm-6 col-md-6 text-center content">
      <div class="content-icon">
        <img src="assets/landing/img/icn-step4.svg" class="img-30"/> 
      </div>
      <h5 class="content-title">
        TAP INTO MEDICLOUD’S DIGITAL WALLET
      </h5>
      <p class="content-detail">
        We make it easy for people to pay for your services. Our digital wallet increases customers commitment towards their appointment.
      </p>
  </div>

</div>

<div class="section-three2">
  <div class="col-md-12 text-center">
    <div class="white-space-20"></div>
    <h2 class="section-title color-white">WHAT PARTNERS HAVE ACCESS TO </h2>
  </div>

  <div id="myCarousel3" class="carousel slide" data-ride="carousel" >
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#myCarousel3" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel3" data-slide-to="1"></li>
      <li data-target="#myCarousel3" data-slide-to="2"></li>
    </ol>
    
    <div class="carousel-inner" role="listbox" style="position: relative;">
      <div class="item active">
        <img class="img-90 sm-img-80" src="assets/landing/img/user-slide1.png " alt="First slide"/>
        <div class="slider-content">
          <h3>Exclusive Marketplace</h3>
          <br>
          <p>
            Create an instant online presence. Showcase your services and expertise and use our platfrom to market your services
          </p>
          <br>
          <a href="#form-contact" class="btn btn-slider">Get Started Now</a>
        </div>
      </div>

      <div class="item">
        <img class="img-90 sm-img-80" src="assets/landing/img/user-slide3.png " alt="First slide"/>
        <div class="slider-content">
          <h3>SCHEDULING</h3>
          <br>
          <p>
            Hassle - free booking for you and your client. Never miss an opportunity again.Intergrates with google calender.
          </p>
          <br>
          <a href="#form-contact" class="btn btn-slider">Get Started Now</a>
        </div>
      </div>

      <div class="item">
        <img class="img-90 sm-img-80" src="assets/landing/img/user-slide2.png " alt="First slide"/>
        <div class="slider-content">
          <h3>INTEGRATIONS</h3>
          <br>
          <p>
            We expand our booking engine to your current website and social media page. With the booking plugin, customers can book via multiple platform
          </p>
          <br>
          <a href="#form-contact" class="btn btn-slider">Get Started Now</a>
        </div>
      </div>

    </div>

    <a class="left carousel-control" href="#myCarousel3" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel3" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
</div>

<div class="section-five ">
  <div class="section-five-wrapper block-section">
    <div class="container">
      <div class="col-md-12 text-center">
        <h2>QUALIFIED PARTNERS CAN USE MEDICLOUD FOR FREE</h2>

        <div class="white-space-20"></div>
        <div class="blue-line"></div>
        <div class="white-space-20"></div>

        <h3>Connect to multi-billion dollar healthcare & wellness market <br />
        for free with medicloud.</h3>

        <h3>There are no subscription fees - just pay $10 on each transaction.</h3>

        <div class="white-space-20"></div>
        <div class="white-space-20"></div>
        <a href="#form-contact" class="btn btn-primary btn-lg margin-center">Get Started Now</a>
        <div class="white-space-20"></div>
      </div>
    </div>
  </div>
</div>

<div class="section-six text-center">
  <div class="col-md-12">
    <h3><b>WHAT OUR HEALTH PROFESSIONALS SAY</b></h3>
    <div class="white-space-20"></div>
    <div class="white-space-20"></div>
  </div>

  <div class="col-md-12">
    <div id="myCarousel3" class="carousel slide" data-ride="carousel" >
      <!-- Indicators -->
      <!-- <ol class="carousel-indicators">
        <li data-target="#myCarousel3" data-slide-to="0" class="active"></li>
      </ol> -->
      
      <div class="carousel-inner" role="listbox" style="position: relative;">
        <div class="item active">
          <div class="col-xs-12 col-sm-3 col-md-2">
            <img src="assets/landing/img/new-assets/Screen Shot 2016-08-26 at 5.34.09 PM.png" class="img-responsive img-item" />
          </div>
          
          <div class="col-xs-12 col-sm-9 col-md-10 item-detail">
            <p>
              "Medicloud is a game changer in the medical industry. It is breathtaking how technology can change the traditional healthcare industry. Now you don't have to queue or call to visit your doctor. Medicloud online booking is the next step into digital health future."
            </p>

            <label>Eric Benghozi</label>
            <br>
            <label>Founder of Medical Partners</label>
          </div>
        </div>

      </div>

      <a class="left carousel-control" href="#myCarousel3" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel3" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </div>
</div>

<div class="section-four2">
  <div class="container">
    

    <div id="form-contact" class="col-md-12">
      <h1 class="form-title text-center">SIMPLY WRITE TO US TO GET YOUR SERVICE LISTED ON MEDICLOUD</h1>
    </div>

    <div class="col-md-12">
      <div class="white-space-20"></div>
      <div class="white-space-20"></div>
      <form>
        <div class="form-container" id="contact-form">
          
          <div class="input-field-container">

            <div class="form-group col-xs-12 col-md-4">
              <div class="input-field-title"><label for="First Name">First Name</label> </div>
              <div class="input-field-element "><input id="First_Name" class="form-control" type="text"></div>
            </div>

            <div class="form-group col-xs-12 col-md-4">
              <div class="input-field-title"><label for="Last Name">Last Name </label></div>
              <div class="input-field-element "><input  id="Last_Name" class="form-control" type="text"></div>
            </div>

            <div class="form-group col-xs-12 col-md-4">
              <div class="input-field-title"><label for="Company Name">Company Name </label>  </div>
              <div class="input-field-element "><input id="Company_Name"  class="form-control" type="text"></div>
            </div>

            <div class="form-group col-xs-12 col-md-4">
              <div class="input-field-title"><label for="Email">Email</label>  </div>
              <div class="input-field-element "><input id="Email"  class="form-control" type="email"></div>
            </div>


            <div class="form-group col-xs-12 col-md-4">
              <div class="input-field-title"><label for="Phone Number">Phone Number</label></div>
              <div class="input-field-element "><input id="Phone_Number"  class="form-control" type="text"></div>
            </div>

            <div class="form-group col-xs-12 col-md-9">
              <div class="input-field-title"><label for="Messages">Messages</label></div>
              <div class="input-field-element ">
                <textarea id="Messages" class="form-control" rows="5" cols="10" style="resize: none"></textarea>
              </div>
            </div>

            <div class="form-group col-md-12 xs-text-center">
             <a class="btn btn-form" id="contact"  href="javascript:void(0)">GET IN TOUCH NOW</a>
            </div>

          </div>
        </div>
      </form>
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
  $(function() {
    $('a[href^="#"]:not(.carousel-control)').on('click',function (e) {
        e.preventDefault();

        var target = this.hash;
        var $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 900, 'swing', function () {
            window.location.hash = target;
        });
    });


  }); 

  $("#scrollToForm").click(function(){
    $('html, body').animate({
            scrollTop: $('#form-contact').offset().top
          }, 1300);
  });

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



<!-- Google Tag Manager -->
<!-- <noscript><iframe src="//www.googletagmanager.com/ns?id=GTM-5DDJ83"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5DDJ83');</script> -->
<!-- End Google Tag Manager -->


</body>
</html>
