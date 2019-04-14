jQuery(document).ready(function($) {
	var all_records = false;
	var start_date;
	var clinic_pin_status;
	  window.base_url = window.location.origin + '/app/';
	  window.base_loading_image = '<img src="'+ window.location.origin +'/assets/images/loading.svg" width="32" height="32" alt=""/>';

	  getAppointmentsCount( null, null);
  	  getAppointmentLists( );

  	  initializeDatePicker( );
  	  setRangeButtonValue( );

  	  getClinicTotalRevenue( null, null );
  	  // getClinicCredits( null, null );
  	  getClinicCollected( null, null );
  	  viewClinicTransactionHistory( );
  	  getMinimumDate( );
  	  getPinStatus( );

  	  function getPinStatus( ) {
  	  	$.ajax({
            url: base_url+'calendar/getClinicPinStatus',
            type: 'POST',
          })
          .done(function(data) {
          	console.log(data);
            clinic_pin_status = data;
          });
  	  }

	  function getAppointmentsCount(start, end) {
  		$.ajax({
  			 url: window.base_url + 'clinic/appointments/count',
  			 type: 'POST',
  			 data: { start: start, end: end }
  		})
  		.done(function(data){
  			$('#appointments').text(data);
  		});
	  }

	  function getMinimumDate( ) {
  		$.ajax({
  			 url: window.base_url + 'clinic/get/minimum/date',
  			 type: 'GET',
  		})
  		.done(function(data){
  			console.log(data);
  			start_date = data;
  		});
	  }

	  function getAppointmentLists( ) {
	  	jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
	  	$.ajax({
  			 url: window.base_url + 'clinic/appointments/list',
  			 type: 'GET',
  		})
  		.done(function(data){
  			$('#schedule-view').html(data);
  			jQuery.unblockUI();
  			$('.info-detail').popover({
				html:true,
				container:'body',
				template : '<div class="detailpopup popover" role="tooltip">' +
								'<div class="arrow"></div>' +
								'<h3 class="popover-title"></h3>' +
								'<div class="popover-content">' +
									
								'</div>'+
							'</div>',
				content : '<div class="header">' +
								'<h4>Appointment Details</h4>' +
							'</div>' +
							'<div id="content-section">' +

								'<div class="body">' +
									'<h5>--</h5>' +
									'<div class="white-space-20" ></div>' +

									'<p><label>Staff</label> <span>--</span></p>' +
									'<p><label>Services</label> <span>--</span></p>' +
									'<div class="white-space-20" ></div>' +

									'<p><label>Cost $</label> <span>--</span></p>' +
									'<p><label>Customer</label> <span>--</span></p>' +
									'<div class="white-space-20" ></div>' +

									'<p><label>Booked From</label> <span>--</span></p>' +
								'</div>' +
								'<div class="footer">' +
									'<h5><a href="javascript:void(0)" style="color:#76C9EC">Edit Appointment >></a> <a href="javascript:void(0)" class="pull-right">Delete</span></a>' +
								'</div>' +
							'</div>'

			});
  		});
	  }

	  function getClinicTotalRevenue( start, end ) {
	  	$.ajax({
  			 url: window.base_url + 'clinic/total/revenue',
  			 type: 'POST',
  			 data: { start: start, end: end }
  		})
  		.done(function(data){
  			// console.log(data);
  			$('#total_revenue').text(data);
  		});
	  }

	  function getClinicCredits( start, end ) {
	  	$.ajax({
  			 url: window.base_url + 'clinic/credits/revenue',
  			 type: 'POST',
  			 data: { start: start, end: end }
  		})
  		.done(function(data){
  			// console.log(data);
  			$('#credits').text(data);
  		});
	  }

	  function getClinicCollected( start, end ) {
	  	$.ajax({
  			 url: window.base_url + 'clinic/collected/revenue',
  			 type: 'POST',
  			 data: { start: start, end: end }
  		})
  		.done(function(data){
  			console.log(data);
  			$('#collected').text(data);
  		});
	  }

	  function viewClinicTransactionHistory( ) {
	  	$.ajax({
  			 url: window.base_url + 'clinic/view/transaction/history/limit',
  			 type: 'GET',
  		})
  		.done(function(data){
  			// console.log(data);
  			$('#transaction-view').html(data);
  		});
	  }

	// --------------------------------------- //

	var dateActive = 1;
	var dateFrom = "";
	var dateTo = "";
	var currentDate = new Date();
  	var currentMonth = currentDate.getMonth();
  	var nextMonthTrue = 0;

	$( "#dateFrom" ).focus(function(){
    	dateActive = 1;
    	$("#dateFrom").attr('style','box-shadow : inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6) !important; border: 3px solid #66afe9 !important;');
    	$("#dateTo").attr('style','');
    });

    $( "#dateTo" ).focus(function(){
    	dateActive = 2;
    	$("#dateTo").attr('style','box-shadow : inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6) !important; border: 3px solid #66afe9 !important;');
    	$("#dateFrom").attr('style','');
    });

    $('body').on('click', function (e) {
	    $('[data-toggle="popover"]').each(function () {
	        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
	            $(this).popover('hide');
	        }
	    });
	});


    // -----------SELECT RANGE OPTION----------- //

    $( "#custom-date-range" ).change(function() {
	  var option = $(this).val();
	  // console.log(option);

	  if( option == 'Today' ){
	  	$('.range').fadeIn(500);
	  	$("#dateFrom").val( $( "#summary-datepicker" ).val() );
	  	$("#dateTo").val( $( "#summary-datepicker" ).val() );
	  	nextMonthTrue = 0;
	  	all_records = false;
	  }else if( option == 'Last Week' ){
	  	$('.range').fadeIn(500);
		var mon = $( "#summary-datepicker" ).datepicker('getDate');
		mon.setDate( (mon.getDate() + 1 - (mon.getDay() || 7)) - 7 );
		var sun = new Date(mon.getTime());
		sun.setDate( (sun.getDate() + 6) );
		nextMonthTrue = 0;
		// console.log(mon + ' ' + sun);

		$("#dateFrom").val( moment(mon).format('MMM D, YYYY') );
	  	$("#dateTo").val( moment(sun).format('MMM D, YYYY') );
	  	all_records = false;
	  }else if( option == 'Last Month' ){
	  	$('.range').fadeIn(500);
	  	var date = new Date();
		var firstDay = new Date(date.getFullYear(), date.getMonth() - 1, 1);
		var lastDay = new Date(date.getFullYear(), date.getMonth(), 0);
		nextMonthTrue = 0;
		// console.log(firstDay + ' ' + lastDay);

		$("#dateFrom").val( moment(firstDay).format('MMM D, YYYY') );
	  	$("#dateTo").val( moment(lastDay).format('MMM D, YYYY') );
	  	all_records = false;
	  }else if( option == 'This Month' ){
	  	$('.range').fadeIn(500);
	  	var date = new Date();
	    console.log(currentMonth);
	    var firstDay = new Date(date.getFullYear(), currentMonth , 1);
	    var lastDay = new Date(date.getFullYear(), currentMonth + 1, 0);
	    nextMonthTrue = 1;
	    // console.log(firstDay + ' ' + lastDay);

	    $("#dateFrom").val( moment(firstDay).format('MMM D, YYYY') );
	    $("#dateTo").val( moment(lastDay).format('MMM D, YYYY') );
	    all_records = false;

	  }else if( option == 'All Records' ){
	  	$('.range').fadeOut(500);
	  	console.log("All records function goes here !");
	  	all_records = true;
	  	nextMonthTrue = 0;
	  }else{
	  	$('.range').fadeIn(500);
	  	$("#dateFrom").val("");
	  	$("#dateTo").val("");
	  	all_records = false;
	  	nextMonthTrue = 0;
	  }
	});

    //  -------- INITIALIZE DATEPICKER ------ //

    function initializeDatePicker(){

    	$("#dateFrom").attr('style','box-shadow : inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6) !important; border: 3px solid #66afe9 !important;');
    	$("#dateTo").attr('style','');

	    $( "#summary-datepicker" ).datepicker({
	    	onSelect: function(date) {
	    		if( dateActive == 2 ){
	    			$( "#dateTo" ).val(date);
	    			dateTo = date;
	    		}else{
	    			$( "#dateFrom" ).val(date);
	    			dateFrom = date;
	    		}
	    		setTimeout(function() {
		          $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');
		        }, 10);
	        },
	        onChangeMonthYear: function (year,month,day) {
	          currentMonth = month - 1;

	          if( nextMonthTrue == 1 ){
	          	var date = new Date();
	          	
			    var firstDay = new Date(date.getFullYear(), currentMonth , 1);
			    var lastDay = new Date(date.getFullYear(), currentMonth + 1, 0);

			    // console.log(firstDay + ' ' + lastDay);

			    $("#dateFrom").val( moment(firstDay).format('MMM D, YYYY') );
			    $("#dateTo").val( moment(lastDay).format('MMM D, YYYY') );
	          }
	          
	        }
	    });
    }   

    function setRangeButtonValue(){
    	var today = $( "#summary-datepicker" ).datepicker( "option", "dateFormat", "M dd, yy" ).val();

	    var date = new Date(today);
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		var edate= new Date(y, m, d+7);

		var toRange = moment(edate).format('MMM D, YYYY');

	    $(".date-from").text( today );
	    $(".date-to").text( toRange );
    }

    // --- SUBMIT FILTER -------- //

    $( "#submit-date-filter" ).click(function(){
    	var dateFrom = $("#dateFrom").val();
	  	var dateTo = $("#dateTo").val();
    	
    	$( ".summary-datepicker-wrapper" ).toggle();

	  	console.log(dateFrom);
	  	// console.log(dateTo);
	  	var start = moment(dateFrom).format('YYYY-MM-DD');
	  	var end = moment(dateTo).format('YYYY-MM-DD');
	  	jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });

	  	if(all_records == true) {
	  		$(".date-from").text( 'All' );
	    	$(".date-to").text( 'Records' );

	  		getAppointmentsCount( null, null);
		  	getAppointmentLists( );
	  	  	getClinicTotalRevenue( null, null );
	  	  	getClinicCredits( null, null );
	  	  	getClinicCollected( null, null );
	  	  	viewClinicTransactionHistory( );
	  	} else {
		  	$(".date-from").text( dateFrom );
	    	$(".date-to").text( dateTo );

		  	getAppointmentsCount(start, end);
		  	getClinicTotalRevenue(start, end);
		  	getClinicCredits(start, end);
		  	getClinicCollected(start, end);

		  	$.ajax({
  			 url: window.base_url + 'clinic/view/schedule/byDate',
  			 type: 'POST',
  			 data: { start: start, end: end }
	  		})
	  		.done(function(data){
	  			// console.log(data);
	  			$('#schedule-view').html(data);
	  		});

	  		$.ajax({
	  			 url: window.base_url + 'clinic/view/transaction/byDate',
	  			 type: 'POST',
	  			 data: { start: start, end: end }
	  		})
	  		.done(function(data){
	  			// console.log(data);
	  			$('#transaction-view').html(data);
	  		});
	  		setTimeout(function() {
	  			jQuery.unblockUI();
	  		}, 500);
	  	}
    });
});	
