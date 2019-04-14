jQuery(document).ready(function($) {
	
  // var protocol = jQuery(location).attr('protocol');
  // var hostname = jQuery(location).attr('hostname');
  // var folderlocation = $(location).attr('pathname').split('/')[1];
  // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
  window.base_url = window.location.origin + '/app/';
  window.base_loading_image = '<img src="http://medicloud.sg/medicloud_v2/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';
  	// console.log(window.localStorage.getItem('pay-view'));

  	console.log(window.location);
// --- onload default selection ---

function redirects(){
	if(window.localStorage.getItem('pay-view') == "true") {
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		setTimeout(function() {
			$('#payments-tab').click();
  		// window.localStorage.setItem('pay-view', false);
		}, 500);
	} else if(window.localStorage.getItem('invoice-view') == "true") {
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		setTimeout(function() {
			$('#payments-tab').click();
			setTimeout(function() {
				$('#transaction-invoice').click();
  			// window.localStorage.setItem('invoice-view', false);
			}, 1000);
		}, 500);
	} else if(window.localStorage.getItem('statement-view') == "true") {
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		setTimeout(function() {
			$('#payments-tab').click();
			setTimeout(function() {
				$('#transaction-statement').click();
  			// window.localStorage.setItem('statement-view', false);
			}, 1000);
		}, 500);
	}

	if( window.location.search == "?transaction=mobile" ){
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		setTimeout(function() {
			$('#payments-tab').click();
			setTimeout(function() {
				$('#view-transaction-preview').click();
  			window.localStorage.setItem('view-transaction-preview', false);
			}, 1000);
		}, 500);
	}
}

	$.ajax({
	      url: base_url+'setting/ajaxGetAccountPage',
	      type: 'GET',
	    })
	    .done(function(data) {

	    	$('#main-tab-account').addClass('active');
	    	$('#main-tab-staff').removeClass('active');
	    	$('#main-tab-service').removeClass('active');
	    	$('#main-tab-notify').removeClass('active');
	    	$('#main-tab-profile').removeClass('active');
	    	$('#main-tab-payments').removeClass('active');

	    	$('#main-tab-account').html(data);

	    	redirects();
	    });


	// $(".nav-tabs").height($(window).height()-52);

	$(document).ready(function () {
	    $('.navbar-default .navbar-nav > li.dropdown').hover(function () {
	        $('ul.dropdown-menu', this).stop(true, true).slideDown('fast');
	        $(this).addClass('open');
	        $('.open > a').css('background', 'transparent');
	    }, function () {
	        $('ul.dropdown-menu', this).stop(true, true).slideUp('fast');
	        $(this).removeClass('open');
	    });
	});

// .........................................................................

	$('#account-tab').click(function(event) {

		$.ajax({
	      url: base_url+'setting/ajaxGetAccountPage',
	      type: 'GET',
	    })
	    .done(function(data) {

	    	$('#main-tab-account').addClass('active');
	    	$('#main-tab-staff').removeClass('active');
	    	$('#main-tab-service').removeClass('active');
	    	$('#main-tab-notify').removeClass('active');
	    	$('#main-tab-payments').removeClass('active');
	    	$('#main-tab-profile').removeClass('active');

	    	$('#main-tab-account').html(
	    		data);
	    });

	});


	$('#staff-tab').click(function(event) {
		
		$.ajax({
	      url: base_url+'setting/ajaxGetStaffPage',
	      type: 'GET',
	    })
	    .done(function(data) {

	    	$('#main-tab-account').removeClass('active');
	    	$('#main-tab-staff').addClass('active');
	    	$('#main-tab-service').removeClass('active');
	    	$('#main-tab-notify').removeClass('active');
	    	$('#main-tab-profile').removeClass('active');
	    	$('#main-tab-payments').removeClass('active');

	    	$('#main-tab-staff').html(data);
	    });

	});


	$('#service-tab').click(function(event) {
		
		$.ajax({
	      url: base_url+'setting/ajaxGetServicPage',
	      type: 'GET',
	    })
	    .done(function(data) {

	    	$('#main-tab-account').removeClass('active');
	    	$('#main-tab-staff').removeClass('active');
	    	$('#main-tab-service').addClass('active');
	    	$('#main-tab-notify').removeClass('active');
	    	$('#main-tab-profile').removeClass('active');
	    	$('#main-tab-payments').removeClass('active');

	    	$('#main-tab-service').html(data);
	    });

	});


	$('#notify-tab').click(function(event) {
		
		$.ajax({
	      url: base_url+'setting/ajaxGetNotifyPage',
	      type: 'GET',
	    })
	    .done(function(data) {

	    	$('#main-tab-account').removeClass('active');
	    	$('#main-tab-staff').removeClass('active');
	    	$('#main-tab-service').removeClass('active');
	    	$('#main-tab-notify').addClass('active');
	    	$('#main-tab-profile').removeClass('active');
	    	$('#main-tab-payments').removeClass('active');

	    	$('#main-tab-notify').html(data);
	    });

	});


	$('#profile-tab').click(function(event) {
		
		$.ajax({
	      url: base_url+'setting/ajaxGetProfilePage',
	      type: 'GET',
	    })
	    .done(function(data) {

	    	$('#main-tab-account').removeClass('active');
	    	$('#main-tab-staff').removeClass('active');
	    	$('#main-tab-service').removeClass('active');
	    	$('#main-tab-notify').removeClass('active');
	    	$('#main-tab-payments').removeClass('active');
	    	$('#main-tab-profile').addClass('active');

	    	$('#main-tab-profile').html(data);
	    });

	});

	$('#payments-tab').click(function(event) {
		
		$.ajax({
	      url: base_url+'setting/ajaxGetPaymentPage',
	      type: 'GET',
	    })
	    .done(function(data) {

	    	$('#main-tab-account').removeClass('active');
	    	$('#main-tab-staff').removeClass('active');
	    	$('#main-tab-service').removeClass('active');
	    	$('#main-tab-notify').removeClass('active');
	    	$('#main-tab-profile').removeClass('active');
	    	$('#main-tab-payments').addClass('active');

	    	$('#main-tab-payments').html(data);
	    	jQuery.unblockUI();
	    });

	});


});	