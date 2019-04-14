 // 	gl_titel = "w";
	// gl_clinic_pin_status = 0;

 //  	gl_date = ''
 //  	gl_stime = '';
 //  	gl_etime = '';
 //  	gl_event_id = 0;
 //  	gl_event_title = '';
stat_price = 0;
doctor_name = '';
stat_doctor_id = '';
stat_user_id = '';
co_paid = 0;
jQuery(document).ready(function($) {


	$('body').clearQueue();
	window.base_url = window.location.origin + '/app/';
  window.image_url = window.location.origin + '/';
 	window.base_loading_image = '<img src="'+ image_url +'assets/images/loading.svg" width="32" height="32" alt=""/>';

 	var doctorSelectedID = null;

 	// getCalendar();
 	setAccountSetting();
 	highlightCurrentDate();
 	setAccountSetting();
 	getEventsGroup();
 	popupReset();
 	getAllUsers();
 	getClinicPinStatus();
 	DeleteAppointment();
 	saveAppointmentGroup();
 	NoShowAppointment();
 	ConcludedAppointment();
 	saveBlocker();
 	deleteExtraEvent();
 	getClinicDetails();

 	$('#mini-calendar-view').mouseover( function() {
    $('.btn-default').css ('background' , '#73CEF4');
	    $('#dp').css ('display' , 'block');
	} ).mouseout( function() {
	    $('.btn-default').css ('background' , '');
	    $('#dp').css ('display' , 'none');
	});

	$('#btn-left').click(function(event) {
	    $('.fc-prev-button').click();
	    displayDate();
	    getEventsGroup();
	});

	$('#btn-right').click(function(event) {
	    $('.fc-next-button').click();
	    displayDate();
	    getEventsGroup();
	});

	$('#btn-today').click(function(event) {
	    $('.fc-today-button').click();
	    displayDate();
	    getEventsGroup();
  	});

 	$('#dp').datepicker({
    	onSelect: function (argument) {
	      	var date = $(this).datepicker( 'getDate' );
	      	$('#calendar').fullCalendar('gotoDate', date);
	      	displayDate();
      		getEventsGroup();
    	}
  	});

  getEnabledDates();
	getDisabledTimes(new Date);

	function getEnabledDates(){
		$.ajax({

		    url: base_url+'corporate/enable-dates',
		    type: 'POST',
		    dataType: 'json',
		    data : { clinicID : $("#clinicID").attr('id'), docID : $( "#search-booking-modal .doctor-selection" ).attr('id') }

		  })
		  .done(function(data) {
		    availableDates = data;
		});

	}

	function getDisabledTimes(date){
		$.ajax({

		      url: base_url+'corporate/disable-times',
		      type: 'POST',
		      dataType: 'json',
		      data : { clinicID: $("#clinicID").attr('id'), docID: $( "#search-booking-modal .doctor-selection" ).attr('id'), date: moment(date).format('dddd, d MMMM YYYY') , duration: $( "#search-booking-modal #block-time-Duration-search" ).val() }

		    })
		    .done(function(data) {
		      $('#appointment-time-search').timepicker('option', 'disableTimeRanges', data[1]);
		  });
	}

  	function available(date) {
	    dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
	    if ($.inArray(dmy, availableDates) != -1) {
	      return [true, "","Available"];
	    } else {
	      return [false,"","unAvailable"];
	    }
	  }

	

  	$( "#appointment-date-search" ).datepicker({

	    dateFormat : "DD, MM dd yy" ,
	    minDate : 0,
	    maxDate : 360,
	    beforeShowDay : available,
	    onSelect : function( date ){
	      // console.log(date);
	      getDisabledTimes(date);
	    }
	});

	$('#appointment-time-search').timepicker({
      'minTime' : '6am',
      'maxTime' : '11.45pm',
      'timeFormat' : 'h:i A'
    });

  	$( "#appointment-date" ).datepicker({
		dateFormat : "DD, MM dd yy" ,
		minDate : 0,
		maxDate : 360,
	});

	$('#appointment-time').timepicker({
      'timeFormat' : 'h:i A',
    });

    $( "#appointment-date-reserve" ).datepicker({

	    dateFormat : "DD, MM dd yy" ,
	    minDate : 0,
	    maxDate : 360
	});

	$('#appointment-time-reserve').timepicker({
	  'timeFormat' : 'h:i A',
    });

 	$("#calendar-view-option li a").click(function(){
		val = $(this).attr('id');
		gl_titel = val;

		if (val == 'd' || val == 'w' || val == 'm') {
			window.localStorage.setItem('search_log_event', true);
		view_calendar_single();
		}

		if (val == 'g') {
			getCalendar();
			getEventsGroup();
		}
	    $('#calender-selection').html($(this).text());
	});

 	$("#service-list").on("click","li", function(){
		val = $('.service',this).text();
		id = $('.service',this).attr('id');

		var reserve_id = $(this).attr('id');

		// console.log(reserve_id);
		$( "#selected-pro-id" ).val(id);

		$('.service-selection').html(val);
		$('.service-selection').attr('id', id);

		$('.blocker-time-format').html('Mins');
		$('.blocker-time-format').attr('id', 'mins');
		$('.time-format').html('Mins');
		$('.time-format').attr('id', 'mins');
		$( "#search-booking-modal #block-time-Duration-search" ).val( $('#selected-duration',this).text());

		if (id == '0') {

			$('#myModal #patient-tab').removeClass('show').addClass('hide');

			$('#myModal #myModalLabel').text("Blocker");

			$('#myModal #booking .panel-body #service-lbl').removeClass('input-width').addClass('slot-blocker-width');
			$('#myModal #booking .panel-body #slot-blocker-service').removeClass('hide').addClass('show');
			$('#myModal #booking .panel-body #Cost-Time-duration').removeClass('show').addClass('hide');

			$('#blocker').removeClass('hide').addClass('show');
			$('#continue').removeClass('show').addClass('hide');
			$('#block-time-Duration').val($('#h-duration').val());
		      
		}else if(reserve_id == 'reserve'){
			$('#myModal').modal('hide');

			setTimeout(function(){
				loadReserveModal();
				$('.service-selection').html("Select a Service");
			},400);
		    
		}else{
			$('#myModal #patient-tab').removeClass('hide').addClass('show');

			$('#myModal #myModalLabel').text("Appointment");

			$('#myModal #booking .panel-body #slot-blocker-service').removeClass('show').addClass('hide');
			$('#myModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width').addClass('input-width');
			$('#myModal #booking .panel-body #Cost-Time-duration').removeClass('hide').addClass('show');

			$('#blocker').removeClass('show').addClass('hide');
			$('#continue').removeClass('hide').addClass('show');

		}

		if( reserve_id != 'reserve' ){
			$('#myModal #booking #ok-icon').addClass('glyphicon-ok');
			$('#myModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
			$('#myModal #booking #ok-icon').removeClass('arrow-color');
		}else{
			$('#reserveModal #booking #ok-icon').removeClass('glyphicon-ok');
			$('#reserveModal #booking #ok-icon').addClass('glyphicon-arrow-right');
			$('#reserveModal #booking #ok-icon').addClass('arrow-color');
		}
		

		getProcedureDetails();

	});

	$("#service-list-reserve").on("click","li", function(){

	  val = $('.service',this).text();
	  id = $('.service',this).attr('id');

	    $('.service-selection').html(val);
	    $('.service-selection').attr('id', id);

	    $('.blocker-time-format').html('Mins');
	    $('.blocker-time-format').attr('id', 'mins');
	    $('.time-format').html('Mins');
	    $('.time-format').attr('id', 'mins');

	    $('#reserveModal #patient-tab').removeClass('hide').addClass('show');

	    $('#reserveModal #myModalLabel').text("Appointment");

	    $('#reserveModal #booking .panel-body #slot-blocker-service').removeClass('show').addClass('hide');
	    $('#reserveModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width').addClass('input-width');
	    $('#reserveModal #booking .panel-body #Cost-Time-duration').removeClass('hide').addClass('show');

	    $('#blocker').removeClass('show').addClass('hide');
	    $('#continue').removeClass('hide').addClass('show');


	    $('#reserveModal #booking #ok-icon').addClass('glyphicon-ok');
	    $('#reserveModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
	    $('#reserveModal #booking #ok-icon').removeClass('arrow-color');

	  //alert(id);
	  getProcedureDetails();

	 });

 	$('#pin_cancel').click(function(event) {
		$('#verify_pin').dialog('close');
		$('#pinerror').css('display', 'none');
		getEventsGroup();
	});


	$('#pin_confirm').click(function(event) {

	  var pin = $('#pin_verification').val();
	  var type = $('#h-pin_types').val();

	  $.ajax({
	      url: base_url+'calendar/validatePin',
	      type: 'POST',
	      // dataType: 'json',
	      data: {
	        pin:pin},
	    })
	    .done(function(data) {
		      if(data==1) {
		        $('#pinerror').css('display', 'none');
		        if (type==1) {// 1 - new appointment;
		            $('#verify_pin').dialog('close');
		            $('#myModal').modal('show');

		        } else if (type==2) {// 2 - resize
		          $('#verify_pin').dialog('close');
		            if (gl_event_title == 'Blocked') {
		                var url = base_url+'calendar/updateOnBlockerDrag';
		            } else {
		                var url = base_url+'calendar/updateOnDrag';
		            }
		            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
		              $.ajax({
		                url: url,
		                type: 'POST',
		                // dataType: 'json',
		                data: {date: gl_date,stime:gl_stime, etime:gl_etime, event_id:gl_event_id },
		              })
		              .done(function(data) {
		                jQuery.unblockUI();
		                getEventsGroup()
		              });
		        }else if (type==3) {// 3 - drag
		          $('#verify_pin').dialog('close');

		              if (gl_event_title == 'Blocked') {
		                  var url = base_url+'calendar/updateOnBlockerDrag';
		              } else {
		                  var url = base_url+'calendar/updateOnDrag';
		              }
		            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
		              $.ajax({
		                url: url,
		                type: 'POST',
		                // dataType: 'json',
		                data: {date: gl_date,stime:gl_stime, etime:gl_etime, event_id:gl_event_id },
		              })
		              .done(function(data) {
		                jQuery.unblockUI();
		                getEventsGroup()
		              });
		        }else if (type==4) {// 4 - edit
		          $('#verify_pin').dialog('close');
		          popupReset();
		          loadEditDetails();
		        }else if (type==5) {// 5 - delete
		          var appointment_id = $('#h-appointment-id').val();
		          $('#verify_pin').dialog('close');
		           jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

		          $.ajax({
		              url: base_url + "calendar/deleteAppointmentDetails",
		              type: "POST",
		              dataType: 'json',
		              data: { appointment_id: appointment_id},
		          })
		          .done(function(data) {
		            if (data != 0) {

		              // alert('Booking Deleted...!');
		              $.alert({
		                  title: 'Alert!',
		                  content: 'Booking Deleted !',
		                  columnClass: 'col-md-4 col-md-offset-4',
		                      theme: 'material',
		                  confirm: function(){
		                      
		                  }
		              });
		              $("#dialog").dialog("close");
		              getEventsGroup();

		            } else {

		              // alert('Someting went wrong, Please check ....')
		              $.alert({
		                  title: 'Alert!',
		                  content: 'Someting went wrong, Please check !',
		                  columnClass: 'col-md-4 col-md-offset-4',
		                      theme: 'material',
		                  confirm: function(){
		                      
		                  }
		              });
		            }
		            jQuery.unblockUI();

		          });
		        }else if (type==6) {// 6 - conclude
		          var appointment_id = $('#h-appointment-id').val();
		          $('#verify_pin').dialog('close');
		          jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
		          $.ajax({
		              url: base_url + "calendar/concludedAppointment",
		              type: "POST",
		              dataType: 'json',
		              data: { appointment_id: appointment_id},
		          })
		          .done(function(data) {
		            if (data != 0) {

		              // alert('...!');
		              $.alert({
		                  title: 'Alert!',
		                  content: 'Booking Concluded !',
		                  columnClass: 'col-md-4 col-md-offset-4',
		                      theme: 'material',
		                  confirm: function(){
		                      
		                  }
		              });
		              $("#dialog").dialog("close");
		              getEventsGroup();

		            } else {
		              $.alert({
		                  title: 'Alert!',
		                  content: 'Someting went wrong, Please check !',
		                  columnClass: 'col-md-4 col-md-offset-4',
		                      theme: 'material',
		                  confirm: function(){
		                      
		                  }
		              });
		            }
		            jQuery.unblockUI();

		          });
		        }else if (type==7) {// 7 - noshow
		          $('#verify_pin').dialog('close');
		          var appointment_id = $('#h-appointment-id').val();
		           jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
		          $.ajax({
		              url: base_url + "calendar/No-ShowAppointment",
		              type: "POST",
		              dataType: 'json',
		              data: { appointment_id: appointment_id},
		          })
		          .done(function(data) {
		            if (data != 0) {

		              // alert('...!');
		              $.alert({
		                  title: 'Alert!',
		                  content: 'Booking No Showed !',
		                  columnClass: 'col-md-4 col-md-offset-4',
		                      theme: 'material',
		                  confirm: function(){
		                      
		                  }
		              });
		              $("#dialog").dialog("close");
		              getEventsGroup();

		            } else {
		              $.alert({
		                  title: 'Alert!',
		                  content: 'Someting went wrong, Please check !',
		                  columnClass: 'col-md-4 col-md-offset-4',
		                      theme: 'material',
		                  confirm: function(){
		                      
		                  }
		              });
		            }
		            jQuery.unblockUI();
		          });
		        }
		      }else{
		          $('#pinerror').css('display', 'block');
		      }

		});
	});

 	 $("#patient #add-new-customer").click(function(){
	    
	    $('#myModal #patient .panel-body #new-customer').removeClass('hide').addClass('show');
	    $('#myModal #patient .panel-body #search-panel').removeClass('show').addClass('hide');

	    $('#customer-name').val('');
	    $('#customer-nric').val('');
	    $('#phone-code').val('');
	    $('#phone-no').val('');
	    $('#customer-email').val('');
	    $('#customer-address').val('');
	    $('#city-name').val('');
	    $('#state-name').val('');
	    $('#zip-code').val('');

	    NRICValidation ();

	  });

	$("#appointment-doctor-list li a").click(function(){
		val = $(this).text();
		id = $(this).attr('id');

		$('.doctor-selection').html(val);
		$('.doctor-selection').attr('id', id);
		getEventsGroup();
		popupReset();
		getDoctorProcedure();
		getEnabledDates();

	});

	$(document).on('click', '#continue', function(event) {

		var doctorID        = $('.doctor-selection').attr('id');
		var procedureID     = $('.service-selection').attr('id');
		var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
		var date            = $('#appointment-date').val();
		var stime           = $('#appointment-time').val();
		// var price           = $('#service-price').val();
		// stat_price           = $('#service-price').val();
		// console.log(stat_price);
		var remarks         = $('#notes').val();
		var default_time    = $('#h-datetime').val();

		var name = $('#customer-name').val();
		var nric = $('#customer-nric').val();
		var code = $('#phone-code').val();
		var phone = $('#phone-no').val();
		var email = $('#customer-email').val();
		var address = $('#customer-address').val();
		var city = $('#city-name').val();
		var statate = $('#state-name').val();
		var zip = $('#zip-code').val();

		var er_count = 0;
		var error = '';

		if (procedureID=='') {error += 'Please select a procedure!<br>'; er_count++;}
		if (duration=='') {error += 'Please insert a duration!<br>'; er_count++;}
		// if (price=='' && procedureID!=0) {error += 'Please insert a price!<br>'; er_count++;}
		if (date=='') {error += 'Please select a date!<br>'; er_count++;}
		if (stime=='') {error += 'Please select a time!<br>'; er_count++;}

		$('#error_div1').css('display', 'block');
		$('#error1').html(error);

		if (er_count==0) {
			$('#error_div1').css('display', 'none');
			$('#tabs .enabledTab').removeClass('active');
			$('#booking').removeClass('active');
			$('#tabs .disabledTab').addClass('active');
			$('#patient').addClass('in active');
		}

	});

	$(document).on('click', '#back-appointment', function(event) {
    	$( "#new-customer" ).removeClass('hide');
    	$( "#new-customer" ).addClass('show');
    	$( "#check-save" ).addClass('hide');
    	$( "#check-save" ).removeClass('show');
  	});

	$(document).on('click', '#next-appointment', function(event) {
	    var a = $(".doctor-selection").text();
	    // console.log(a);
	    var b = $("#customer-nric").val();
	    var c = $("#myModal .service-selection").text();
	    var d = $("#customer-name").val();
	    var e = $("#appointment-date").val();
	    var f = $("#appointment-time").val();
	    var g = $("#customer-email").val();
	    var h = $("#customer-phone").val();
	    var i = $("#notes-group").val();
	    var j = $("#service-price").val();

	    $("#doctor-confirm").text(doctor_name);
	    $("#nric-confirm").text(b);
	    $("#procedure-confirm").text(c);
	    $("#name-confirm").text(d);
	    $("#date-confirm").text(e);
	    $("#time-confirm").text(f);
	    $("#email-confirm").text(g);
	    $("#phone-confirm").text(h);
	    $("#notes-confirm").text(i);
	    // $("#price-confirm").text(j);
	    $("#price-confirm").text(stat_price);

	    $( "#new-customer" ).removeClass('show');
	    $( "#new-customer" ).addClass('hide');

	    $( "#check-save" ).addClass('show');
	    $( "#check-save" ).removeClass('hide');
  });

  	$(document).on('click', '#update-appointment-group', function(event) {

	   var id = $('#h-appointment-id').val();
	   // $('#next-appointment').hide();
	   // var doctorID        = $('.doctor-selection').attr('id');
	   var doctorID        = stat_doctor_id;
	   var procedureID     = $('.service-selection').attr('id');
	   var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
	   var time_format     = $('.time-format').attr('id');
	   var date            = $('#appointment-date').val();
	   var stime           = $('#appointment-time').val();
	   // var price           = $('#service-price').val();
	   var price           = stat_price;
	   var remarks         = $('#notes').val();

	   var name = $('#customer-name').val();
	   var nric = $('#customer-nric').val();
	   var code = $('#phone-code').text();
	   var phone = $('#phone-no').val();
	   var email = $('#customer-email').val();
	   var address = $('#customer-address').val();
	   var city = $('#city-name').val();
	   var statate = $('#state-name').val();
	   var zip = $('#zip-code').val();

	   // ................... validate user ......................
	   if(!code) {
	    alert('Please select phone are code.');
	    return false;
	   }
	   var er_count = 0;
	   var error = '';
	   var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

	   if (name=='') { error += 'Please insert name!<br>'; er_count++; }
	   if (nric=='') { error += 'Please insert nric/fin/passport!<br>'; er_count++; }
	   if (code=='') { error += 'Please insert code!<br>'; er_count++; }
	   // if (phone=='') { error += 'Please insert phone number!<br>'; er_count++; }
	   // if (email=='') { error += 'Please insert email!<br>'; er_count++; }
	   if (email == '' || !re.test(email)) { error += 'Please insert valid email!<br>'; er_count++; }

	   $('#error_div2').css('display', 'block');
	   $('#error2').html(error);
	   if (er_count==0) {$('#error_div2').css('display', 'none');} else { return false;}

	   if (time_format == 'hours' ){

	      duration = Math.floor( duration * 60);

	    }

	   $('#update-appointment-group').text('Processing ...');

	   $.ajax({
	      url: base_url+'calendar/updateAppointment',
	      type: 'POST',
	      // dataType: 'json',
	      data: {
	        user_id: stat_user_id, appointment_id:id, doctorid: doctorID, procedureid:procedureID, duration:duration, bookdate:date, starttime:stime, price: stat_price, remarks:remarks, name:name, nric:nric, code:code, phone:phone, email:email, address:address, city:city, statate:statate, zip:zip },
	    })
	    .done(function(data) {
	    	if(data.status == false) {
	        $.alert({
	            title: 'Alert!',
	            content: data.message,
	            columnClass: 'col-md-4 col-md-offset-4',
	                theme: 'material',
	            confirm: function(){
	                
	            }
	        });
	        $('#update-appointment-group').text('Update Appointment');
	      } else {
	         $('#myModal').modal('hide');
		      $('#update-appointment-group').text('Update Appointment');
		      $('#next-appointment').show();
		      getEventsGroup();
	      }
	      // if (data==0) {
	      //   // alert('Double booking not allowed!');
	      //   $.alert({
	      //       title: 'Alert!',
	      //       content: 'Double booking not allowed!',
	      //       columnClass: 'col-md-4 col-md-offset-4',
	      //           theme: 'material',
	      //       confirm: function(){
	                
	      //       }
	      //   });
	      //   $('#update-appointment-group').text('Update Appointment');
	      // }else if(data==2){
	      //   // alert('Sorry! Clinic is closed.');
	      //   $.alert({
	      //       title: 'Alert!',
	      //       content: 'Sorry! Clinic is closed!',
	      //       columnClass: 'col-md-4 col-md-offset-4',
	      //           theme: 'material',
	      //       confirm: function(){
	                
	      //       }
	      //   });
	      //   $('#update-appointment-group').text('Update Appointment');
	      // } else {
	      //   $('#myModal').modal('hide');
		     //  $('#update-appointment-group').text('Update Appointment');
		     //  $('#next-appointment').show();
		     //  getEventsGroup();
	      // }

	    });

	});

	// .............................................................................................
	var reserve_trap = 0;
	var bookID = 0;

	$(document).on('click', '#edit-appointment-details-group', function(event) {
		event.stopPropagation();
	  $("#dialog").dialog("close");

	  getClinicPinStatus();

	  if ( gl_clinic_pin_status==1) {
	      $('#h-pin_types').val(4);
	      veryfiPin();
	  }else {
	    popupReset();
	    $('#next-appointment').hide();
	    loadEditDetails();
	  }

	});

	// ............... RESERVE BLOCKER ................... //

	$(document).on('click', '#blocker-reserve-group', function(event) {

	   var doctorID        = $('#reserveModal .doctor-selection').attr('id');
	   var proID           = $('#reserveModal .service-selection').attr('id');

	   var price           = $('#reserveModal #service-price-reserve').val();
	   var duration        = $('#service-time-Duration-reserve').val();
	   var time_format     = $('#reserveModal .time-format').attr('id');

	   var stime           = $('#appointment-time-reserve').val();
	   var date            = $('#appointment-date-reserve').val();
	   var remarks         = $('#notes-reserve').val();
	   
	   var email         = $('#email-reserve').val();
	   var phone         = $('#phone-no-reserve').val();

	   if(phone) {
	    var code         = $('#phone-code-reserve').text();
	   } else {
	    var code = '';
	   }

	   var name = $('#name-reserve').val();
	   // var remarks         = $('#notes-reserve').val();
	   // console.log(proID);

	   if(!proID){
	    // console.log("null this");
	    $.alert({
	        title: 'Alert!',
	        content: 'Please Select a  Service.',
	        columnClass: 'col-md-4 col-md-offset-4',
	            theme: 'material',
	        confirm: function(){
	            
	        }
	    });
	    return false;
	   }

	   if(!name){
	    // console.log("null this");
	    $.alert({
	        title: 'Alert!',
	        content: 'Please put a name for the reserver.',
	        columnClass: 'col-md-4 col-md-offset-4',
	            theme: 'material',
	        confirm: function(){
	            
	        }
	    });
	    return false;
	   }

	   if(!price) {
	      $.alert({
	        title: 'Alert!',
	        content: 'Please put a price of the service.',
	        columnClass: 'col-md-4 col-md-offset-4',
	            theme: 'material',
	        confirm: function(){
	            
	        }
	    });
	      return false;
	   }

	   if(!duration) {
	      $.alert({
	        title: 'Alert!',
	        content: 'Please put a duration of the service.',
	        columnClass: 'col-md-4 col-md-offset-4',
	            theme: 'material',
	        confirm: function(){
	            
	        }
	    });
	      return false;
	   }

	  if (time_format == 'hours' ){
	    duration = Math.floor( duration * 60);
	  }

	   $.confirm({
	        title: 'Confirm!',
	        content: 'Are you sure you want to add Reserve Blocker?',
	        columnClass: 'col-md-4 col-md-offset-4',
	        theme: 'material',
	         confirmButton: 'Yes',
	          cancelButton: 'NO',
	        confirm: function(){
	          $('#blocker-reserve-group').text('Processing ...');
	          $('#blocker-reserve-group').attr('disabled',true);
	          $.ajax({
	             url: base_url+'clinic/save-appointment-reserver',
	             type: 'POST',
	             // dataType: 'json',
	             data: {
	               doctorid: doctorID, 
	               procedureid: proID, 
	               duration:duration, 
	               bookdate:date, 
	               starttime:stime,
	               remarks:remarks,
	               price:price,
	               email:email,
	               phone:phone,
	               code:code,
	               name: name
	             },
	           })
	           .done(function(data) {
	              // console.log(data);
	              if (data==0) {
	                alert('Double booking not allowed');
	                $('#blocker-reserve-group').text('Save Blocker');
	                $('#blocker-reserve-group').attr('disabled',false);
	              }else if(data==2){
	                alert('Sorry! Clinic is closed.');
	                $('#blocker-reserve-group').text('Save Blocker');
	                $('#blocker-reserve-group').attr('disabled',false);
	              } else {
	                $('#reserveModal').modal('hide');
	              $('#blocker-reserve-group').text('Save Blocker');
	              $('#blocker-reserve-group').attr('disabled',false);
	              getEventsGroup();

	              }


	           });
	        },
	        cancel: function(){
	        }
	    });

	  

	  });

 	
	// -------------------------------------------------------------

	$(document).on('click', '#update-reserve', function(event) {
	    // console.log("IN");
	   var id = $('#h-appointment-id').val();
	   var doctorID        = $('#reserveModal .doctor-selection').attr('id');
	   var procedureID     = $('#reserveModal  .service-selection').attr('id');
	   var duration        = $('#service-time-Duration-reserve').val();
	   var time_format     = $('#reserveModal  .time-format').attr('id');
	   var date            = $('#appointment-date-reserve').val();
	   var stime           = $('#appointment-time-reserve').val();
	   var price           = $('#service-price-reserve').val();
	   var remarks         = $('#notes-reserve').val();

	   var name = $('#customer-name').val();
	   var nric = $('#customer-nric').val();
	   var code = $('#phone-code-reserve').text();
	   var phone = $('#phone-no-reserve').val();
	   var email = $('#email-reserve').val();
	   var address = $('#customer-address').val();
	   var city = $('#city-name').val();
	   var statate = $('#state-name').val();
	   var zip = $('#zip-code').val();

	   if (time_format == 'hours' ){

	      duration = Math.floor( duration * 60);

	    }
	  var bookID = localStorage.getItem('bookID');
	  var userID = localStorage.getItem('userID');
	  // console.log(bookID);
	  // console.log(userID);

	   $('#update-reserve').text('Processing ...');
	   $('#update-reserve').attr('disabled',true);

	   $.ajax({
	      url: base_url+'clinic/save-appointment-reserver',
	      type: 'POST',
	      // dataType: 'json',
	      data: {
	        userid:userID,
	        doctorid: doctorID, 
	        bookingid: bookID, 
	        procedureid:procedureID, 
	        duration:duration, 
	        bookdate:date, 
	        starttime:stime, 
	        price:price, 
	        remarks:remarks, 
	        code:code, 
	        phone:phone, 
	        email:email, 
	      },
	    })
	    .done(function(data) {
	      if (data==0) {
	        // alert('Double booking not allowed!');
	        $.alert({
	            title: 'Alert!',
	            content: 'Double booking not allowed!',
	            columnClass: 'col-md-4 col-md-offset-4',
	                theme: 'material',
	            confirm: function(){
	                
	            }
	        });
	        $('#update-reserve').text('Update Appointment');
	        $('#update-reserve').attr('disabled',false);
	      }else if(data==2){
	        // alert('Sorry! Clinic is closed.');
	        $.alert({
	            title: 'Alert!',
	            content: 'Sorry! Clinic is closed!',
	            columnClass: 'col-md-4 col-md-offset-4',
	                theme: 'material',
	            confirm: function(){
	                
	            }
	        });
	        $('#update-reserve').text('Update Appointment');
	        $('#update-reserve').attr('disabled',false);
	      } else {
	        $('#reserveModal').modal('hide');
	      $('#update-reserve').text('Update Appointment');
	      $('#update-reserve').attr('disabled',false);
	      getEventsGroup();
	      }

	    });


	});

 	// call functions

 	function view_calendar_single() {
	    $("#calendar_page_container").html("");
	    $.ajax({
	      url: base_url+'clinic/calendar-view-single',
	      type: 'GET',
	    })
	    .done(function(data) {
	      $("#calendar_page_container").html(data);

	    })
	}

 	function displayDate() {
		$.ajax({
			url: base_url + 'calendar/getClinicDetails',
			type: 'POST',
			dataType: 'json',
		})
		.done(function(data) {
			// console.log(data);
			var start_hour = data.start_hour;
			var view = $('#calendar').fullCalendar('getView');
			$('#btn-title').text(view.title);
			highlightCurrentDate();
			$(".fc-body").height($(window).height()-136);
			$('.fc-body').animate({scrollTop:$('tr[data-time="'+start_hour+'"]').position().top}, 1);
			// var $myDiv = $('.scroll-div');
		});

	}

 	function highlightCurrentDate(){
		var d = new Date();
		var month = d.getMonth()+1;
		var day = d.getDate();
		var output = d.getFullYear() + '-' + ((''+month).length<2 ? '0' : '') + month + '-' +((''+day).length<2 ? '0' : '') + day;
		$("th[data-date*="+output+"]").addClass("header-date");
	}

 	function setAccountSetting(){
	  $.ajax({
	    url: base_url + 'calendar/getClinicDetails',
	    type: 'POST',
	    dataType: 'json',
	  })
	  .done(function(data) {
	    var def_day = data.first_day;
	    var slot_duration = '00:'+ data.slot_duration +':00';

	    // if (data.default_view == 1){

	    //   var def_view = 'agendaWeek';
	    //   $('#calender-selection').html("Weekly");
	    // }
	    // else if (data.default_view == 2){

	    //   var def_view = 'agendaDay';
	    //   $('#calender-selection').html("Daily");
	    // }
	    // else if (data.default_view == 3){

	    //   var def_view = 'month';
	    //   $('#calender-selection').html("Monthly");
	    // }

	    var def_view = 'agendaDay';
	    $('#calender-selection').html("By Group");
	    // console.log(def_day, def_view, slot_duration);
	    getCalendar(def_day, def_view, slot_duration);
	    displayDate();
	  });

	}

	function getClinicDetails( )
	{
	  $.ajax({
	    url: base_url + 'clinic/clinic_details/' + $('#clinicID').val(),
	    type: 'GET',
	  })
	  .done(function(data) {
	    console.log(data);
	    co_paid = data.clinic_type.co_paid; 
	  });
	}

 	function getResource(callback) {
 		$.ajax({
	        url: base_url + 'get/group_resources',
	        type: 'POST',
	        dataType: 'json',
	        data : { clinic_id : $("#clinicID").val() }
      	})
      	.done(function(data) {
        	return callback(data);
    	});
 	}

 	function getEventsGroup() {
 		// console.log("GROUP NING ANIMAS");
 		jQuery.blockUI({message: '<h1> ' + base_loading_image + ' </h1>'});
 		var view = $('#calendar').fullCalendar('getView');
 		var start_date = moment(view.start).format("YYYY-MM-DD");
 		$.ajax({
	        url: base_url + 'get/group_events',
	        type: 'POST',
	        dataType: 'json',
	        data : { clinic_id : $("#clinicID").val(), start_date: start_date }
      	})
      	.done(function(data) {
      		// console.log(data);
        	$('#calendar').fullCalendar('removeEvents');
        	$('#calendar').fullCalendar( 'addEventSource', data);
        	jQuery.unblockUI();
    	});
 	}
 	var old_resource, new_resource;
 	function getCalendar(firstDay, defaultView, defaultDuration) {
		$('#calendar').fullCalendar({
		    header: {
					left: 'prev,next today',
					center: 'title',
					right: 'agendaDay,agendaWeek'
			},
			views: {
				agendaTwoDay: {
					type: 'agenda',
					duration: { days: 7 },
					groupByResource: true
				}
			},
	      	// scrollTime: "08:00:00",
	      	defaultView: 'agendaDay',
	      	editable: true,
	      	firstDay: firstDay,
	      	slotDuration: defaultDuration,
	      	slotLabelInterval: '01:00:00',
	      	allDaySlot: false,
	      	timezone: 'local',
			ignoreTimezone: false,
	      	columnFormat: 'ddd, MMM DD',
	      	selectable: true,
	      	selectHelper: true,
	      	select: selectOnCalendar,
	      	editable: true,
	      	nowIndicator:true,
	      	resources: getResource,
	      	longPressDelay: 1000,
	      	eventResize: eventResize,
		  	// events: getEventsGroup,
	      	eventRender: function(event, eventElement){
	        	if (event.image) {
	          		eventElement.find("div.fc-content").prepend("<img src='https://mednefits.com/favicon.ico' style='display: inline-block;position: absolute;right: 0; width: 30px; height: 30px; margin: 5px;'>");
	        	}

	        	if(event.status_doctor == 0) {
	        		eventElement.find(".fc-time span").addClass('display-none');
	        	}
	      	},
	      	selectConstraint:{
	        	start: '00:00',
	        	end: '24:00',
	      	},

	      	// minTime: 08:00:00,

	      	eventDrop: eventdrag,
	      	eventDragStart: function(event) {
			    var myResource = $('#calendar').fullCalendar('getResourceById', event.resourceId);
			    old_resource = myResource.id; // the variable of your resource 
			},
	      	// eventResize: eventResize,
	      	eventClick: showDetailsDialog,
	      	// eventOverlap: false,
	      	// slotEventOverlap: false,
	      	eventTextColor: 'black',
	      	height:'auto',
	      	contentHeight:'auto',

	      // scrollTime: '11:00:00',

	      // snapDuration:'00:05:00',


	  }); // end of calendar
	}

	function eventResize(event, delta, revertFunc) {
	  gl_date = moment(event.start).format('dddd, DD MMMM YYYY');
	  gl_stime = moment(event.start).format('h:mm A');
	  gl_etime = moment(event.end).format('h:mm A');
	  gl_event_id = event.id;
	  gl_event_title = event.title;
	  // console.log(gl_event_title);
	  if (gl_event_title == 'Blocked') {
	      var url = base_url+'calendar/updateOnBlockerDrag';
	  } else if(gl_event_title.indexOf('Concluded') !== -1) {
	    // console.log('cannot update concluded appointment');
	    revertFunc();
	    return false;
	  } else {
	      var url = base_url+'calendar/updateOnDrag';
	  }
	  getClinicPinStatus();
	  // alert(etime);
	    $.confirm({
	        title: 'Confirm!',
	        content: 'Are you sure about this change?',
	        columnClass: 'col-md-4 col-md-offset-4',
	        theme: 'material',
	         confirmButton: 'Yes',
	          cancelButton: 'NO',
	        confirm: function(){
	          if ( gl_clinic_pin_status==1) {
	            $('#h-pin_types').val(2);
	            veryfiPin();

	          }else {
	            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
	            $.ajax({
	              url: url,
	              type: 'POST',
	              // dataType: 'json',
	              data: {date: gl_date,stime:gl_stime, etime:gl_etime, event_id:gl_event_id },
	            })
	            .done(function(data) {
	              jQuery.unblockUI();
	              getEventsGroup()
	            });

	          }
	        },
	        cancel: function(){
	          revertFunc();
	        }
	    });
	}

	function showDetailsDialog(calEvent) {
	  	// console.log(calEvent);

		var event_id = calEvent.id;
		var event_title = calEvent.title;
		var status = calEvent.status;

		if(calEvent.type == 4){
			reserve_trap = 1;
			bookID = calEvent.id;

			localStorage.setItem('bookID', bookID);
			localStorage.setItem('userID', calEvent.user_id);
		} else{
			reserve_trap = 0;
		}

		if (event_title=='Blocked') {

			$.ajax({
				url: base_url+'calendar/getExtraEventDetails',
				type: 'POST',
				dataType: 'json',
				data: {
				appointment_id: event_id},

			})
			.done(function(data) {

				$('#blocker-id').val(data.event_id);
				$('#bocker-doctor-detail').html(data.note);
				// $('#blocker-date-lbl').html(data.date);
				$('#blocker-time-lbl').html(data.description);

				dialog = $( "#bocker-dialog" ).dialog({

					modal: true,
					draggable: false,
					resizable: false,
					// position: ['center', 'top'],
					show: 'blind',
					hide: 'blind',
					width: 400,
					dialogClass: 'ui-dialog-osx',

				});

				$( ".ui-dialog-titlebar-close" ).html( '<i class="glyphicon glyphicon-remove"></i>' );

			});

		}else {

			setTimeout(function() {
			  $.ajax({
			    url: base_url+'calendar/getAppointmentDetails',
			    type: 'POST',
			    dataType: 'json',
			    data: {
			    appointment_id: event_id},
			  })
			  .done(function(data) {
			  	// console.log(data);
			    $('#h-appointment-id').val(data.appointment_id);
			    $('#h-doctor-id').val(data.doctor_id);
			    stat_doctor_id = data.doctor_id;
			    stat_user_id = data.user_id;
			    $('#h-procedure-id').val(data.procedure_id);
			    $('#h-procedure-duration').val(data.duration);
			    $('#h-procedure-price').val(data.cost);
			    stat_price = data.cost;
			    $('#h-cus-city').val(data.city);
			    $('#h-cus-zip').val(data.zip);
			    $('#h-cus-state').val(data.state);
			    $('#h-cus-address').val(data.address);
			    $('#h-app-time').val(data.time1);
			    $('#h-cus-phone-code').val(data.phoneCode);


			    $('#appointment-doctor-detail').html(data.doctor);
			    $('#appointment-service-detail').html(data.procedure);
			    $('#appointment-cost-detail').html(data.cost);
			    $('#appointment-customer-detail').html(data.customer);
			    $('#appointment-nric-detail').html(data.nric);
			    $('#appointment-email-detail').html(data.email);
			    $('#appointment-phone-detail').html(data.phone);
			    $('#appointment-date-lbl').html(data.date);
			    $('#appointment-time-lbl').html(data.time);

			    if (data.note != ''){
			      $('#Appoit-note').html('<td class="col-sm-2" style="vertical-align: top;">Note</td><td class="col-sm-8" id="appointment-note-detail">'+data.note+'</td>');
			    }
			    else{
			      $('#Appoit-note').html('');
			    }


			    if (status=='Concluded' || status=='No Show') {

			      $('.hide-buttons').addClass('hide');
			    }
			    else{
			      $('.hide-buttons').removeClass('hide');
			    }

			    dialog = $( "#dialog" ).dialog({

			      modal: true,
			      draggable: false,
			      resizable: false,
			      // position: ['center', 'top'],
			      show: 'blind',
			      hide: 'blind',
			      width: 400,
			      dialogClass: 'ui-dialog-osx',

			          });

			    $( ".ui-dialog-titlebar-close" ).html( '<i class="glyphicon glyphicon-remove"></i>' );

			  });
			}, 100);


		}
	}

	function eventdrag(event, delta, revertFunc, jsEvent, ui, view) {
		// console.log(event);
		var new_resource = $('#calendar').fullCalendar('getResourceById', event.resourceId);
      	console.log(myResource.id);
      	console.log(old_resource);
		jQuery.blockUI({message: '<h1> ' + base_loading_image + ' </h1>'});
		$.ajax({
	        url: base_url + 'reschedule_check/resource',
	        type: 'POST',
	        dataType: 'json',
	        data : { doctor_resource_old: parseInt(old_resource), doctor_resource_new: new_resource.id, procedure_id: event.ProcedureID }
      	})
      	.done(function(data) {
        	if(data.status == 200) {
        		gl_date = moment(event.start).format('dddd, DD MMMM YYYY');
				gl_stime = moment(event.start).format('h:mm A');
				gl_etime = moment(event.end).format('h:mm A');
				gl_event_id = event.id;
				gl_event_title = event.title;

				if (gl_event_title == 'Blocked') {
				    var url = base_url+'calendar/updateOnBlockerDrag';
			  	} else if(gl_event_title.indexOf('Concluded') !== -1) {
				    revertFunc();
				    jQuery.unblockUI();
				    $.toast({
		                text: 'Cannot reschedule appointment. Appointment already concluded.',
		                showHideTransition: 'slide',
		                icon: 'error',
		                hideAfter : 10000,
		                stack: 1,
		                position : 'bottom-left' 
		                // bgColor : '#1667AC'
	              	});
			    	return false;
			  	} else {
			  	getClinicPinStatus();
			      	var url = base_url + 'calendar/updateOnDrag';
			      	$.confirm({
				        title: 'Confirm!',
				        content: 'Are you sure about this change?',
				        columnClass: 'col-md-4 col-md-offset-4',
				        theme: 'material',
				        confirmButton: 'Yes',
				        ancelButton: 'NO',
				        confirm: function(){
				            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
				            $.ajax({
				              url: url,
				              type: 'POST',
				              dataType: 'json',
				              data: {date: gl_date, stime:gl_stime, etime:gl_etime, event_id:gl_event_id, doctor_id: parseInt(event.resourceId) },
				            })
				            .done(function(data) {
				              getEventsGroup()
				            });
				        },
				        cancel: function(){
				          revertFunc();
				        }
				    });
			  	}
        	} else {
        		$.toast({
	                text: data.message,
	                showHideTransition: 'slide',
	                icon: 'error',
	                hideAfter : 10000,
	                stack: 1,
	                position : 'bottom-left' 
	                // bgColor : '#1667AC'
              	});
        		revertFunc();
        	}
        	jQuery.unblockUI();
    	});
	}

	function selectOnCalendar(start, end, jsEvent, view, resource) {
		popupReset();
		var date = moment(start).format('dddd, MMMM DD YYYY');
		var time = moment(start).format('h:mm A');
		$('#appointment-date').val(date);
		$('#appointment-time').val(time);

		$('#appointment-date-reserve').val(date);
   		$('#appointment-time-reserve').val(time);

		doctorSelectedID = resource.id;
		$( ".doctor-selection" ).text(resource.title);
		doctor_name = resource.title;
		$('.doctor-selection').attr('id', resource.id);
		jQuery.blockUI({message: '<h1> ' + base_loading_image + ' </h1>'});
 		$.ajax({
	        url: base_url + 'check/book_date_resource',
	        type: 'POST',
	        dataType: 'json',
	        data : { doctor_id : resource.id, start_date: moment(start._d).format('YYYY-MM-DD h:mm:ss a') }
      	})
      	.done(function(data) {
        	if( data.status == 200 ){
        		$("#myModal").modal('show');
        		getDoctorProcedure();
        	} else {
        		$.toast({
	                text: data.message,
	                showHideTransition: 'slide',
	                icon: 'error',
	                hideAfter : 5000,
	                stack: 1,
	                position : 'bottom-left' 
	                // bgColor : '#1667AC'
              });
        	}
        	
        	jQuery.unblockUI();
    	});
	}

	function getDoctorProcedure() {

	    var clinicID = $('#clinicID').val();
	    var doc_id = $('.doctor-selection').attr('id');
	    var corp = localStorage.getItem('corporate-selected');
	    // console.log(corp);
	    $.ajax({
	    url: base_url+'calendar/getDoctorProcedure',
	    type: 'POST',
	    data: {docID: doc_id, clinicID:clinicID, corporate:corp },
	    })

	    .done(function(data) {
	    	// console.log(data);
			$('#service-list').html(data);
			$('#service-list-search').html(data);
			$('.slot-block').html(data);

	    });
	}

	function popupReset() {

		$('.service-selection').attr('id', '');
		$('.service-selection').html('Select a service');
		$('#block-time-Duration').val('');
		$('#service-time-Duration').val('');
		$('#Cost-Time-duration').removeClass('show').addClass('hide');
		$('#notes').val('');

		$('#slot-blocker-service').removeClass('show').addClass('hide');
		$('#service-lbl').removeClass('slot-blocker-width').addClass('input-width');

		$('#customer-name').val('');
		$('#customer-nric').val('');
		$('#phone-code').val('');
		$('#phone-no').val('');
		$('#customer-email').val('');
		$('#customer-address').val('');
		$('#city-name').val('');
		$('#state-name').val('');
		$('#zip-code').val('');
		$('#search-customer').val('');

		$('#tabs .enabledTab').addClass('active');
		$('#booking').addClass('active');
		$('#tabs .disabledTab').removeClass('active');
		$('#patient').removeClass('in active');

		$('#search-panel').addClass('show')
		$('#new-customer').removeClass('show').addClass('hide');
		$('#blocker').removeClass('show').addClass('hide');
		$('#continue').removeClass('hide').addClass('show');

		$('#save-appointment-group').addClass('hide');
		// $('#save-appointment').removeClass('hide');
		$('#update-appointment-group').addClass('hide');

		$('#myModal #booking #ok-icon').removeClass('glyphicon-ok');
		$('#myModal #booking #ok-icon').addClass('glyphicon-arrow-right');
		$('#myModal #booking #ok-icon').addClass('arrow-color');
	}

	function getAllUsers() {

		$.ajax({
			url: base_url+'calendar/load-users',
			type: 'POST',
			dataType: 'json',
			data: { userType: 1, user_type: 5, access_type: 1 },
		})
		.done(function(data) {
		
			$('#myModal #search-customer').autocomplete({
				lookup: data,
				minChars:5,
				onSelect: function (suggestion) {

				      $('#myModal #patient .panel-body #search-panel').removeClass('show');
				      $('#myModal #patient .panel-body #search-panel').addClass('hide');

					$('#myModal #patient .panel-body #new-customer').removeClass('hide');
					$('#myModal #patient .panel-body #new-customer').addClass('show');

					$('#myModal #patient .panel-body #search-panel').removeClass('show');
					$('#myModal #patient .panel-body #search-panel').addClass('hide');
					$('#new-customer #customer-name').val(suggestion.Name);
					$('#new-customer #customer-nric').val(suggestion.NRIC);
					$('#new-customer #phone-code').text(suggestion.PhoneCode);

					var length = $("#new-customer #phone-code").text().length;
					// console.log(length);

					var phone = suggestion.PhoneNo;
					var PhoneNo = phone.substring(length);

					$('#new-customer #phone-no').val(PhoneNo);
					$('#new-customer #customer-email').val(suggestion.Email);
					$('#new-customer #customer-address').val(suggestion.Address);
					$('#new-customer #city-name').val(suggestion.City);
					$('#new-customer #state-name').val(suggestion.State);
					$('#new-customer #zip-code').val(suggestion.zip);

					NRICValidation ();

				}
			});
		});

	}

	function getProcedureDetails() {

	  var clinicID = $('#clinicID').val();
	  var procedureID = $('.service-selection').attr('id');

	  $.ajax({
	      url: base_url+'calendar/load-procedure-details',
	      type: 'POST',
	      dataType: 'json',
	      data: {procedureID: procedureID, clinicID:clinicID },
	    })
	    .done(function(data) {
	      // alert(data.Price);

	      $("#service-time-Duration").val(data.Duration);
	      $("#service-time-Duration-reserve").val(data.Duration);
	      $("#service-price").val(data.Price);
	      stat_price = data.Price;
	      // console.log(stat_price);
	      $("#service-price-search").val(data.Price);
	      $("#service-price-reserve").val(data.Price);

	    });

	}

	function NRICValidation (){
	    var NRIC = $('#customer-nric').val();
	    var validate = /^[STFG]\d{7}[A-Z]$/igm;

		if (validate.test(NRIC)) {
			$('#nric-valid-icon').addClass('glyphicon-ok');
		}else {
			$('#nric-valid-icon').removeClass('glyphicon-ok');
		}

	}
	function getClinicPinStatus() {
	    $.ajax({
        	url: base_url+'calendar/getClinicPinStatus',
        	type: 'POST',
      	})
      	.done(function(data) {
        	gl_clinic_pin_status = data;
      	});
	}

	function DeleteAppointment() {

	  getClinicPinStatus();
	    jQuery("#delete-appointment-details").click(function () {

	        $.confirm({
	            title: 'Confirm!',
	            content: 'Are you sure you want to Delete this Appointment ?',
	            columnClass: 'col-md-4 col-md-offset-4',
	            theme: 'material',
	             confirmButton: 'Yes',
	              cancelButton: 'NO',
	            confirm: function(){
	              if (gl_clinic_pin_status == 1) {
	                $("#dialog").dialog("close");
	                  $('#h-pin_types').val(5);
	                  veryfiPin();
	              } else {

	                  var appointment_id = $('#h-appointment-id').val();

	                   jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

	                  $.ajax({
	                      url: base_url + "calendar/deleteAppointmentDetails",
	                      type: "POST",
	                      dataType: 'json',
	                      data: { appointment_id: appointment_id},
	                  })
	                  .done(function(data) {
	                    if (data != 0) {

	                      alert('Booking Deleted...!');
	                      $("#dialog").dialog("close");
	                      getEventsGroup();

	                    } else {

	                      alert('Someting went wrong, Please check ....')
	                    }

	                    jQuery.unblockUI();

	                  });

	              }
	            },
	            cancel: function(){
	            }
	        });


	    });
	}

	function veryfiPin() {
	  $('#pin_verification').val('');
	  	dialog = $( "#verify_pin" ).dialog({
          	modal: true,
          	draggable: false,
          	resizable: false,
         	 // position: ['center', 'top'],
          	show: 'blind',
          	hide: 'blind',
          	width: 500,
          	dialogClass: 'ui-dialog-pin',
        });
	}

	function saveAppointmentGroup() {
  	   $(document).on('click', '#myModal #save-appointment-group', function(event) {

		   var doctorID        = $('.doctor-selection').attr('id');
		   var procedureID     = $('.service-selection').attr('id');
		   var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
		   var time_format     = $('.time-format').attr('id');
		   var date            = $('#appointment-date').val();
		   var stime           = $('#appointment-time').val();
		   // var price           = $('#service-price').val();
		   var remarks         = $('#notes').val();

		   var name = $('#customer-name').val();
		   var nric = $('#customer-nric').val();
		   var code = $('#phone-code').text();
		   var phone = $('#phone-no').val();
		   var email = $('#customer-email').val();
		   var address = $('#customer-address').val();
		   var city = $('#city-name').val();
		   var statate = $('#state-name').val();
		   var zip = $('#zip-code').val();

		   // ................... validate user ......................

		   var er_count = 0;
		   var error = '';
		   var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

		   if (name=='') { error += 'Please insert name!<br>'; er_count++; }
		   if (nric=='') { error += 'Please insert nric/fin/passport!<br>'; er_count++; }
		   if (code=='') { error += 'Please insert country code!<br>'; er_count++; }
		   if (phone=='') { error += 'Please insert phone number!<br>'; er_count++; }
		   // if (email=='') { error += 'Please insert email!<br>'; er_count++; }
		   if (email == '' || !re.test(email)) { error += 'Please insert valid email!<br>'; er_count++; }
		   if(!code) {
		    alert('Please select the phone area code.');
		    return false;
		   }
		   $('#error_div2').css('display', 'block');
		   $('#error2').html(error);
		   if (er_count==0) {$('#error_div2').css('display', 'none');} else { return false;}

		   if (time_format == 'hours' ){

		      duration = Math.floor( duration * 60);

		    }


		   $.confirm({
		        title: 'Confirm!',
		        content: 'Are you sure you want to make this Appointment?',
		        columnClass: 'col-md-4 col-md-offset-4',
		        theme: 'material',
		        confirmButton: 'Yes',
		        cancelButton: 'NO',
		        confirm: function(){
		          $('.save-btn').text('Processing ...');
		          $('.save-btn').attr('disabled', true);
		           $.ajax({
		              url: base_url+'calendar/saveAppointment',
		              type: 'POST',
		              // dataType: 'json',
		              data: {
		                doctorid: doctorID, procedureid:procedureID, duration:duration, bookdate:date, starttime:stime, price: stat_price, remarks:remarks, name:name, nric:nric, code:code, phone:phone, email:email, address:address, city:city, statate:statate, zip:zip },
		            })
		            .done(function(data) {
		              $('.save-btn').attr('disabled', false);
		              if (data==0) {
		                alert('Double booking not allowed');
		                $('.save-btn').text('Save Appointment');
		              }else if(data==2){
		                alert('Sorry! Clinic is closed.');
		                $('.save-btn').text('Save Appointment');
		              } else {
		                $( "#new-customer" ).removeClass('hide');
		                $( "#new-customer" ).addClass('show');

		                $( "#check-save" ).addClass('hide');
		                $( "#check-save" ).removeClass('show');

		                $('#myModal').modal('hide');
		                $('.save-btn').text('Save Appointment');
		                stat_price = 0;
		                getEventsGroup();
		                getAllUsers();

		              }
		            });
		        },
		        cancel: function(){
		        }
	    });

	  });
	}

	function NoShowAppointment() {

	  getClinicPinStatus();
	    jQuery("#no-show-appointment-details").click(function () {

	          $.confirm({
	            title: 'Confirm!',
	            content: 'Are you sure you want to No Show this Appointment ?',
	            columnClass: 'col-md-4 col-md-offset-4',
	            theme: 'material',
	             confirmButton: 'Yes',
	              cancelButton: 'NO',
	            confirm: function(){
	              
	              if (gl_clinic_pin_status==1) {
	                $("#dialog").dialog("close");
	                  $('#h-pin_types').val(7);
	                  veryfiPin();
	              } else {

	                  var appointment_id = $('#h-appointment-id').val();
	                 jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

	                $.ajax({
	                    url: base_url + "calendar/No-ShowAppointment",
	                    type: "POST",
	                    dataType: 'json',
	                    data: { appointment_id: appointment_id},
	                })
	                .done(function(data) {
	                  if (data != 0) {

	                    alert('Booking No Showed...!');
	                    $("#dialog").dialog("close");
	                    getEventsGroup();

	                  } else {

	                    alert('Someting went wrong, Please check ....')
	                  }

	                  jQuery.unblockUI();

	                });

	              }
	            },
	            cancel: function(){
	            }
	        });


	    });
	}

	function ConcludedAppointment() {

		getClinicPinStatus();
		var credit_use_status;
		var appointment_id;
		var transaction_id;
		var transaction;
		var total;
		var wallet_use;
		var summary;
		var amount_bill;
	    
	    jQuery("#concluded-appointment-group").click(function () {

	        $.confirm({
	            title: 'Confirm!',
	            content: 'Are you sure you want to Conclude this Appointment ?',
	            columnClass: 'col-md-4 col-md-offset-4',
	            theme: 'material',
	            confirmButton: 'Yes',
	            cancelButton: 'NO',
	            confirm: function(){
	              if (gl_clinic_pin_status==1) {
	                $("#dialog").dialog("close");
									$('#h-pin_types').val(6);
									veryfiPin();
	              } else {

	                    appointment_id = $('#h-appointment-id').val();

	                      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
	                      // calendar/concludedAppointment
	                      $.ajax({
	                          url: base_url + "clinic/appointment/transaction",
	                          type: "POST",
	                          dataType: 'json',
	                          data: { appointment_id: appointment_id},
	                      })
	                      .done(function(data) {
	                        // console.log(data);
	                        console.log(co_paid);
	                        if(data == 0) {
	                          alert('Booking Concluded...!');
	                          $("#dialog").dialog("close");
	                          getEventsGroup();
	                        } else if(data != 0) {
	                          $(".appointment-details").hide();
	                          // $(".balance").fadeIn();
		                        if(co_paid == 1) {
	                            $(".balance-co-paid-group").fadeIn();
	                          } else {
	                            $(".balance").fadeIn();
	                          }
	                          transaction_id = data.transaction.transaction_id;
	                          if(parseInt(data.transaction.balance) == 0) {
	                            credit_use_status = 0
	                          } else {
	                            credit_use_status = 1;
	                          }
	                        } else {
	                          alert('Someting went wrong, Please check ....')
	                        }
	                        jQuery.unblockUI();

	                      });

	              }
	            },
	            cancel: function(){
	            }
	        });

	    });

	    jQuery("#calc-bill").click(function () {

	       amount_bill = $('#bill_amount').val();

	       jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
	       $.ajax({
	            url: base_url + "clinic/transaction/calculate",
	            type: "POST",
	            dataType: 'json',
	            data: { id: transaction_id, amount: amount_bill, credit_use_status: credit_use_status},
	        })
	        .done(function(data) {
	          // console.log(data);
	          if(data) {
	            transaction = data;
	            // total = data.total;
	            // wallet_use = data.wallet_use;
	            // summary = data.summary;
	            $(".balance").hide();
	            $(".summary-receipt").fadeIn();
	            $(".ui-dialog").css({ top: '-100px' });  
	            $(".cancel1").hide();
	            $(".cancel2").show();
	            $('#client_name').text(data.name);
	            $('#nric').text(data.nric);
	            $('#procedure').text(data.procedure);
	            $('#date').text(data.date);
	            $('#time').text(data.time);
	            $('#total_amount').text(data.total_amount);
	            $('#final_bill').text(data.total_bill);
	            $('#deducted').text(data.medi_credit);
	            $('#clinic_discount').text(data.clinic_discount);
	            $('#mednefits_discount').text(data.medi_percent); 
	          }

	          jQuery.unblockUI();
	        });
	    });

	    jQuery("#finish-transaction").click(function () {
	      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
	       $.ajax({
	            url: base_url + "clinic/transaction/finish",
	            type: "POST",
	            dataType: 'json',
	            data: { 
	              transaction_id: transaction.transaction_id,
	              total_amount: transaction.total_amount,
	              user_id: transaction.UserID,
	              final_bill: transaction.total_bill,
	              wallet_id: transaction.wallet_id,
	              appointment_id: transaction.appointment_id,
	              credit: transaction.credit,
	              name: transaction.name,
	              nric: transaction.nric,
	              procedure: transaction.procedure,
	              date: transaction.date,
	              time: transaction.time,
	              // total_amount: summary.total_amount,
	              // credit_use_status: wallet_use,
	              email: transaction.email,
	              credit_deducted: transaction.medi_credit,
	              // final_bill: total.final_bill,
	              doctorid: transaction.DoctorID,
	              price: transaction.total_amount,
	              procedureid: transaction.ProcedureID
	            },
	        })
	        .done(function(data) {
	          // console.log(data);
	          $('#bill_amount').val("");
	          $(".appointment-details").fadeIn();
	          $(".balance").hide();
	          $(".summary-receipt").hide();

	          $(".ui-dialog").css({ top: '-9px' })
	          $(".cancel1").show();
	          $(".cancel2").hide();


	          alert('Booking Concluded...!');
	          $("#dialog").dialog("close");
	          getEventsGroup();
	          jQuery.unblockUI();
	        });

	    });

	    jQuery("#calc-bill-co-paid-group").click(function () {
	      if($('#bill_amount_co_paid-group').val() > 1) {
	        amount_bill = $('#bill_amount_co_paid-group').val();
	        console.log($('#bill_amount_co_paid-group').length);
	      } else {
	        alert('Please enter Total Medication Bill.');
	        return false;
	      }

	      console.log('yearh');
	      $.ajax({
	            url: base_url + "clinic/transaction_co_paid/calculate",
	            type: "POST",
	            dataType: 'json',
	            data: { id: transaction_id, amount: amount_bill},
	      })
	      .done(function(data) {
	        // console.log(data);
	        if(data) {
	          transaction = data;
	          // total = data.total;
	          // wallet_use = data.wallet_use;
	          // summary = data.summary;
	          $(".balance-co-paid-group").hide();
	          $(".summary-receipt-co-paid-group").fadeIn();
	          $(".ui-dialog").css({ top: '-100px' });  
	          $(".cancel1").hide();
	          $(".cancel2").show();
	          $('#client_name_co_paid').text(data.name);
	          $('#nric_co_paid').text(data.nric);
	          $('#procedure_co_paid').text(data.procedure);
	          $('#date_co_paid').text(data.date);
	          $('#time_co_paid').text(data.time);
	          $('#total_amount_co_paid').text(data.total_amount);
	          $('#final_bill_co_paid').text(data.total_bill);
	          $('#deducted_co_paid').text(data.medi_credit);
	          // $('#clinic_discount').text(data.clinic_discount);
	          // $('#mednefits_discount').text(data.medi_percent); 
	        }

	        jQuery.unblockUI();
	      });

	    });


	    jQuery("#finish-transaction-co-paid-group").click(function () {
	      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
	       $.ajax({
	            url: base_url + "clinic/transaction/finish",
	            type: "POST",
	            dataType: 'json',
	            data: { 
	              transaction_id: transaction.transaction_id,
	              total_amount: transaction.total_amount,
	              user_id: transaction.UserID,
	              final_bill: transaction.total_bill,
	              wallet_id: transaction.wallet_id,
	              appointment_id: transaction.appointment_id,
	              credit: transaction.credit,
	              name: transaction.name,
	              nric: transaction.nric,
	              procedure: transaction.procedure,
	              date: transaction.date,
	              time: transaction.time,
	              // total_amount: summary.total_amount,
	              // credit_use_status: wallet_use,
	              email: transaction.email,
	              credit_deducted: transaction.medi_credit,
	              // final_bill: total.final_bill,
	              doctorid: transaction.DoctorID,
	              price: transaction.total_amount,
	              procedureid: transaction.ProcedureID
	            },
	        })
	        .done(function(data) {
	          // console.log(data);
	          $('#bill_amount_co_paid-group').val("");
	          $(".appointment-details").fadeIn();
	          $(".balance-co-paid-group").hide();
	          $(".summary-receipt-co-paid-group").hide();

	          $(".ui-dialog").css({ top: '-9px' })
	          $(".cancel1").show();
	          $(".cancel2").hide();


	          alert('Booking Concluded...!');
	          $("#dialog").dialog("close");
	          getEventsGroup();
	          jQuery.unblockUI();
	        });

	    });

	    jQuery(".calc-cancel").click(function () {
	      $(".appointment-details").fadeIn();
	      $(".balance").hide();
	      $(".summary-receipt").hide();

	      $(".ui-dialog").css({ top: '-9px' })
	      $(".cancel1").show();
	      $(".cancel2").hide();
	    });

	}

	function loadEditDetails() {

	  // popupReset();

	  $('#myModal #booking .panel-body #slot-blocker-service').removeClass('show');
	  $('#myModal #booking .panel-body #slot-blocker-service').addClass('hide');
	  $('#myModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width');
	  $('#myModal #booking .panel-body #service-lbl').addClass('input-width');
	  $('#myModal #booking .panel-body #Cost-Time-duration').removeClass('hide');
	  $('#myModal #booking .panel-body #Cost-Time-duration').addClass('show');

	  $('#myModal #patient .panel-body #new-customer').removeClass('hide');
	  $('#myModal #patient .panel-body #new-customer').addClass('show');
	  $('#myModal #patient .panel-body #search-panel').removeClass('show');
	  $('#myModal #patient .panel-body #search-panel').addClass('hide');

	  $('#reserveModal #booking .panel-body #slot-blocker-service').removeClass('show');
	  $('#reserveModal #booking .panel-body #slot-blocker-service').addClass('hide');
	  $('#reserveModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width');
	  $('#reserveModal #booking .panel-body #service-lbl').addClass('input-width');
	  $('#reserveModal #booking .panel-body #Cost-Time-duration').removeClass('hide');
	  $('#reserveModal #booking .panel-body #Cost-Time-duration').addClass('show');

	  $('#reserveModal #patient .panel-body #new-customer').removeClass('hide');
	  $('#reserveModal #patient .panel-body #new-customer').addClass('show');
	  $('#reserveModal #patient .panel-body #search-panel').removeClass('show');
	  $('#reserveModal #patient .panel-body #search-panel').addClass('hide');

	  $('#save-appointment-group').addClass('hide');
	  $('#update-appointment-group').removeClass('hide');

	  $('#blocker-reserve-group').addClass('hide');
	  $('#update-reserve').removeClass('hide');

	  $('#tabs .enabledTab').addClass('active');
	  $('#booking').addClass('active');
	  $('#tabs .disabledTab').removeClass('active');
	  $('#patient').removeClass('in active');

	  var id = $('#h-appointment-id').val();
	  $('.service-selection').attr('id', $('#h-procedure-id').val() );
	  $('.service-selection').text( $('#appointment-service-detail').text() );
	  // $('#service-price').val( $('#h-procedure-price').val() );
	  $('#service-price-search').val( $('#h-procedure-price').val() );
	  $('#service-price-reserve').val( $('#h-procedure-price').val() );
	  $('#service-time-Duration').val( $('#h-procedure-duration').val() );
	  $('#service-time-Duration-reserve').val( $('#h-procedure-duration').val() );
	  $('#appointment-date').val( $('#appointment-date-lbl').text() );
	  $('#appointment-date-reserve').val( $('#appointment-date-lbl').text() );
	  $('#appointment-time-reserve').val( $('#h-app-time').val() );
	  $('#appointment-time').val( $('#h-app-time').val() );

	  var phone = $('#appointment-phone-detail').text();
	  var code = $('#h-cus-phone-code').val();
	  var length = $('#h-cus-phone-code').val().length;
	  // var length = $("#new-customer #phone-code").text().length;
	  if(code.indexOf('+') > -1) {
	    // console.log('has + sign');
	    var phone_code = code;
	  } else {
	    // console.log('does not have + sign');
	    var phone_code = '+' + code.replace(/\s/g,'');
	  }
	  // console.log(length);

	  var PhoneNo = phone.substring(length);

	  $('#customer-name').val($('#appointment-customer-detail').text());
	  $('#name-reserve').val($('#appointment-customer-detail').text());
	  $('#customer-nric').val($('#appointment-nric-detail').text());
	  $('#phone-code').text(phone_code);
	  $('#phone-code-reserve').text(phone_code);
	  $('#phone-no').val(PhoneNo);
	  $('#phone-no-reserve').val(PhoneNo);
	  $('#customer-email').val($('#appointment-email-detail').text());
	  $('#email-reserve').val($('#appointment-email-detail').text());
	  $('#customer-address').val($('#h-cus-address').val());
	  $('#city-name').val($('#h-cus-city').val());
	  $('#state-name').val($('#h-cus-state').val());
	  $('#zip-code').val($('#h-cus-zip').val());

	  $('#notes').val($('#appointment-note-detail').text());
	  $('#notes-reserve').val($('#appointment-note-detail').text());

	  $('.time-format').html('Mins');
	  $('.time-format').attr('id', 'mins');

	  $('#myModal #booking #ok-icon').addClass('glyphicon-ok');
	  $('#myModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
	  $('#myModal #booking #ok-icon').removeClass('arrow-color');

	  $('#reserveModal #booking #ok-icon').addClass('glyphicon-ok');
	  $('#reserveModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
	  $('#reserveModal #booking #ok-icon').removeClass('arrow-color');

	  NRICValidation ();

	  if( reserve_trap == 0 ){
	    $('#myModal').modal('show');
	  }else{
	    $('#reserveModal').modal('show');
	  }
	  
	}

	function loadReserveModal(){
		$('#reserveModal').modal('show');
	}

	function saveBlocker() {

	  $(document).on('click', '#blocker', function(event) {

	   var doctorID        = $('.doctor-selection').attr('id');
	   var duration        = $('#block-time-Duration').val();
	   var time_format     = $('.blocker-time-format').attr('id');
	   var stime           = $('#appointment-time').val();
	   var date            = $('#appointment-date').val();
	   var remarks         = $('#notes').val();

	   var er_count = 0;
	   var error = '';


	   if (duration=='') {error += 'Please insert a duration!<br>'; er_count++;}
	   if (date=='') {error += 'Please select a date!<br>'; er_count++;}
	   if (stime=='') {error += 'Please select a time!<br>'; er_count++;}
	   $('#error_div1').css('display', 'block');
	   $('#error1').html(error);
	   if (er_count==0) {$('#error_div1').css('display', 'none');} else { return false;}

	   if (time_format == 'hours' ){
	      duration = Math.floor( duration * 60);
	    }

	    $.confirm({
	        title: 'Confirm!',
	        content: 'Are you sure you want to add Blocker?',
	        columnClass: 'col-md-4 col-md-offset-4',
	        theme: 'material',
	         confirmButton: 'Yes',
	          cancelButton: 'NO',
	        confirm: function(){
	          $('#blocker-reserve-group').text('Processing ...');
	          $.ajax({
	             url: base_url+'calendar/saveBlocker',
	             type: 'POST',
	             // dataType: 'json',
	             data: {
	               doctorid: doctorID, duration:duration, bookdate:date, starttime:stime,remarks:remarks},
	           })
	           .done(function(data) {

	              if (data==0) {
	                alert('Double booking not allowed');
	                $('#blocker-reserve-group').text('Save Blocker');
	              }else if(data==2){
	                alert('Sorry! Clinic is closed.');
	                $('#blocker-reserve-group').text('Save Blocker');
	              } else {
	                $('#myModal').modal('hide');
	              $('#blocker-reserve-group').text('Save Blocker');
	              getEventsGroup();

	              }


	           });
	        },
	        cancel: function(){
	        }
	    });

	  });

	}

	function deleteExtraEvent() {

	  $(document).on('click', '#bocker-delete', function(event) {

	    $.confirm({
	        title: 'Confirm!',
	        content: 'Are you sure you want to delete this Event ?',
	        columnClass: 'col-md-4 col-md-offset-4',
	        theme: 'material',
	         confirmButton: 'Yes',
	          cancelButton: 'NO',
	        confirm: function(){
	          var event_id = $('#blocker-id').val();

	            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

	            $.ajax({
	                url: base_url+'calendar/deleteBlockerDetails',
	                type: 'POST',
	                dataType: 'json',
	                data: { Event_id: event_id},
	              })
	              .done(function(data) {

	              });

	            jQuery.unblockUI();
	            $("#bocker-dialog").dialog("close");
	            getEventsGroup();
	        },
	        cancel: function(){
	        }
	    });
	  });
	}

	
});