<!-- dev -->
	{{ HTML::script('assets/userWeb/js/jquery.min.js') }}
	{{ HTML::script('assets/userWeb/js/moment.min.js') }}
	{{ HTML::script('assets/userWeb/js/moment-range.min.js') }}
	{{ HTML::script('assets/userWeb/js/fullcalendar.js') }}
	{{ HTML::script('assets/userWeb/js/bootstrap.min.js') }}
	{{ HTML::script('assets/userWeb/js/angular.min.js') }}
	{{ HTML::script('assets/userWeb/js/unsavedChanges.js') }}
	{{ HTML::script('assets/userWeb/js/bootstrap-material-datetimepicker.js') }}
	{{ HTML::script('assets/userWeb/js/angular-animate.min.js') }}
	{{ HTML::script('assets/userWeb/js/ng-file-upload-shim.js') }}
	{{ HTML::script('assets/userWeb/js/ng-file-upload.min.js') }}
	{{ HTML::script('assets/userWeb/js/angular-ui-router.min.js') }}
	{{ HTML::script('assets/userWeb/js/main.js') }}
	{{ HTML::script('assets/userWeb/js/ng-image-appear.js') }}
	{{ HTML::script('assets/userWeb/js/loading-bar.min.js') }}
	{{ HTML::script('assets/userWeb/js/jquery-confirm.min.js') }}
	{{ HTML::script('assets/userWeb/js/intlTelInput.min.js') }}
	{{ HTML::script('assets/userWeb/js/utils.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/controllers/mainCtrl.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/authService.js?_={{ $date->format('U') }}">
	</script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/benefitService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/favouriteService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/appointmentService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/walletService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/profileService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/services/clinicService.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/app.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/calendar.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/home.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/benefits.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/appointments.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/favourites.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/profile.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/wallet.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/clinic-maps.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/appointment-create.js?_={{ $date->format('U') }}"></script>
	<script type="text/javascript" src="<?php echo $server; ?>/assets/userWeb/process/directives/ecommerce.js?_={{ $date->format('U') }}"></script>

	{{ HTML::style('assets/userWeb/css/bootstrap.min.css') }}
	{{ HTML::style('assets/userWeb/css/bootstrap-material-datetimepicker.css') }}
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/userWeb/css/style.css?_={{ $date->format('U') }}">
	{{ HTML::style('assets/userWeb/css/font-awesome.min.css') }}
	{{ HTML::style('assets/userWeb/css/animations.css') }}
	{{ HTML::style('assets/userWeb/css/fullcalendar.css') }}
	{{ HTML::style('assets/userWeb/css/loading-bar.min.css') }}
	<link rel="stylesheet" href="<?php echo $server; ?>/assets/userWeb/css/responsive.css?_={{ $date->format('U') }}">

	<td colspan="3">
			<div class="dropdown" id="service-dropdown">
	    		<button class="clinic-speciality dropdown-btn dropdown-toggle" id="{{ $clinicdetails['clinic_type'] }}" type="button" data-toggle="dropdown" style="height: 42px; width: 94%; text-align: left; padding-left: 15px;">
	    		<span id="clinic-service-name">{{ $Speciality }}</span>&nbsp;&nbsp;&nbsp;
	    		<span class="caret" style="float: right; margin: 15px 5px 0 0;"></span></button>
	    		<ul class="dropdown-menu" role="menu" aria-labelledby="menu1" id="clinic-type-list" style="width: 420px; max-height: 210px; overflow-y: auto; overflow-x: hidden;">
	      			<?php foreach ($clinic_type as $val) { ?>
					        <li role="presentation"><a href="#" id="{{ $val->ClinicTypeID }}">{{ $val->Name }}</a></li>
					    <?php } ?>
	    		</ul>
	  		</div>
        </td>

				     <!-- <a href="{{$activeLink}}">Click Here to Reset </a> -->





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