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
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Medicloud - CORPORATE</title>

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

    <!-- <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script> -->
    
    {{ HTML::script('assets/landing/js/jquery.min.js') }}
    {{ HTML::script('assets/landing/js/bootstrap.min.js') }}

     <!-- Bootstrap video -->
     {{ HTML::style('assets/landing/plugins/plyr-master/dist/plyr.css') }}
     {{ HTML::script('assets/landing/plugins/plyr-master/dist/plyr.js') }}

     <!-- <script type="text/javascript" src="js/script.js"></script> -->

     {{ HTML::script('assets/landing/js/script.js') }}

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

<nav class="navbar navbar-absolute navbar-web">
 <div class="container">
    <div class="navbar-header ">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <div class="icon-bar-margin"></div>
        <span class="icon-bar"></span>
        <div class="icon-bar-margin"></div>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index" >
        <img src="assets/landing/img/mc-logo.png" class="img-80 xs-img-60"  alt=""/>
      </a>
    </div>
    <div id="navbar" class="navbar-collapse collapse opacity ">
      <ul class="nav navbar-nav FF2 ">
        <li class=" nav-link "><a href="index">HOME</a></li>
        <li class=" nav-link "><a href="health-professionals"> FOR HEALTH PROFESSIONALS  </a></li>
        <li class=" nav-link margin-right active"><a href="corporate">CORPORATE</a></li>
      </ul>

    </div><!--/.nav-collapse -->
  </div>
</nav>

<nav class="navbar navbar-absolute navbar-mobile">

  <div class="container">
    <button id="show-side-menu" class="navbar-toggle" >
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <div class="icon-bar-margin"></div>
      <span class="icon-bar"></span>
      <div class="icon-bar-margin"></div>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-logo" href="index">
      <img src="assets/landing/img/mc-logo.png" class=""  alt=""/>
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
    <li class="active"><a href="corporate"> <img src="assets/landing/img/new-assets/manager.svg" class="" /> <span>CORPORATE</span> </a></li>
  </ul> 
</div>  


<div id="header3">
  <div class="col-md-12 text-center">
    <h1>Stay Tuned...</h1>

    <h4>We’ve an exciting deal for your company and employees  </h4>
    <br>
    <p>Enter your email for updates</p>
    <br>
    <br>
    <div class="form-inline">
      <input id="sub-email"  class="form-control border-radious" type="email" placeholder="Example@email.com">
      <a class="btn btn-corporate" id="subscribe">Go</a>
    </div>
  </div>
    
    
</div>

























</div><!--end of main container-fluid -->

<!-- Rangetouch to fix <input type="range"> on touch devices (see https://rangetouch.com) -->
        <script src="https://cdn.rangetouch.com/0.0.9/rangetouch.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- <script src="js/bootstrap.min.js"></script> -->
    {{ HTML::script('assets/landing/js/bootstrap.min.js') }}
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
