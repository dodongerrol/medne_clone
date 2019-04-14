	var gl_enableDays = [];
var gl_disableTimes = [[]];
var gl_duration = 0;
var gl_price = 0;

jQuery(document).ready(function($) {

	// var protocol = jQuery(location).attr('protocol');
    // var hostname = jQuery(location).attr('hostname');
    // var folderlocation = $(location).attr('pathname').split('/')[1];
    // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
    window.base_url = window.location.origin + '/app/';

	$( "#date" ).datepicker({
		 dateFormat : "DD, d MM yy" ,
	  showButtonPanel: true,
	  minDate : 0,
	  maxDate : 360,
	  // beforeShowDay: enableAll,
	 

	});



	$(document).on("change", "#nric", function () {

	});

	$(document).on("change", "#date", function () {
		$('#time').attr('disabled', true);
        var date = $(this).val();
        var docID = $('#doctor').val();
		var clinicID = $('#h-clinicID').val();
		var duration = gl_duration;

		$.ajax({
			url: base_url+'widget/disable-times',
			type: 'POST',
			data: {docID: docID, clinicID:clinicID, date: moment(date).format('YYYY-MM-DD'), duration:duration },
		})
		.done(function(disableTimes) {
			console.log(disableTimes);
			$('#time').attr('disabled', false);
			if(disableTimes[0]==1) {
				$('#alert').css('display', 'block');
			}else {
				$('#alert').css('display', 'none');
			}

	        $('#time').timepicker('option', 'disableTimeRanges', disableTimes[1]);
			$('ui-timepicker-list li').css('color', 'red');
			$('#time').val('');
			$('#lbl_time').val('');

		});

    })

// ......................................................................................../


	$('#time').timepicker({
		'timeFormat' : 'h:i A',
		'minTime' : '6am',
		'maxTime' : '11.45pm',
	    // 'disableTimeRanges': [
	    //     ['1am', '2am'],
	    // ],
	   
	});

	$('#time').on('change', function(){

	    var time = $(this).val();
	    // var duration = 30;

	    $.ajax({
			url: base_url+'widget/load-end-time',
			type: 'POST',
			data: {time: time,duration:gl_duration},
		})
		.done(function(data) {
			$('#lbl_time').val(data);
		});
	    
	    
	 });


	// $('#time').focus(function(event) {
	// 	// $('.ui-timepicker-wrapper ul.ui-timepicker-list li.ui-timepicker-disabled').css('display', 'none');
	// });
	
// /............................................................................................


	$('#doctor').change(function(event) {
		// var docID = $(this).val();
		// var clinicID = $('#h-clinicID').val();
		// $.ajax({
		// 	url: base_url+'widget/load-doctor-procedure',
		// 	type: 'POST',
		// 	data: {docID: docID, clinicID:clinicID },
		// })
		// .done(function(data) {
		// 	 $('#procedure').html(data);
		// });

		enableDays ();

		$('#date').val('');
		$('#time').val('');
		$('#lbl_time').val('');
	});

	// /............................................................................................


	$('#procedure').change(function(event) {
		// alert('procedure');
		var procedureID = $(this).val();
		var clinicID = $('#h-clinicID').val();
		$('#date').val('');
		$('#time').val('');
		$('#lbl_time').val('');

		laodProcedureDoctors(procedureID);
		$.ajax({
			url: base_url+'widget/load-procedure-data',
			type: 'POST',
			dataType: 'json',
			data: {procedureID: procedureID, clinicID:clinicID },
		})
		.done(function(data) {
			// alert(data.Price);
    	gl_duration = data.Duration;
    	gl_price = data.Price;

		});
		

	});

	$(document).on('click', '#doc-mobile-codes li', function(event) {

  		id = $(this).attr('id');
  		$('#phone_code').val(id);
	});

// ..............................................................................

	
		$("#form-1").validate({
                rules: {
                    doctor: "required",
                    procedure: "required",
                    date: "required",
                    time: "required",
                },
                messages: {
                    doctor: "Please Select the Doctor",
                    procedure: "Please Select the Procedure",
                    date: "Please Select the Date",
                    time: "Please Select the Time",
                },
                submitHandler: function(form) {
                    //form.submit();
                    // $('#btn-book').text('Book');
                    // $('#screen1').css('display', 'none');
                    // $('#screen3').css('display', 'none');
                    // $('#screen2').css('display', 'block');

                  $('#booking-tab').removeClass('active');
			      $('#booking').removeClass('active');
			      $('#patient-tab').addClass('active');
			      $('#patient').addClass('in active');

                }
            });
	
// ................................................................................................../

		$("#form-2").validate({
                rules: {
                    
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        number: true,
                        minlength:6
                    },
                    phone_code: {
                        // number: true,
                        required: true,
                        minlength:2
                    },
                    nric: "required",
                    name: "required",
                },
                messages: {
                    nric: "Please insert nric",
                    phone: "Please insert phone number",
                    phone_code: "Please insert phone code",
                    name: "Please insert name",
                    email: {
                        required: "Please insert email",
                    },
                },    
                submitHandler: function(form) {
                    // form.submit();
                    
                    var nric = $('#nric').val();
                    var email = $('#email').val();
					console.log(nric);
					// $('#btn-next-2').text("Wait ...");
					// $.ajax({
					// 	url: base_url + 'widget/check_nric',
					// 	type: 'POST',
					// 	data: { nric: nric, email: email },
					// })
					// .done(function(response) {
					// 	console.log(response);
					// 	if(response == 0) {
                    		viewDetails();
					// 	} else if(response == 1) {
					// 		$('#btn-next-2').text("Next");
					// 		alert('Sorry, NRIC is already taken. Make sure you have the correct NRIC associated by your email.');
					// 	} 
					// });
                }
            });


		// ................................................................................................................./

		$('#btn-confirm').click(function(event) {
			var condition = $('#chk-condition').is(":checked");
			var otp_status = $('#h-otp-status').val();
			
			if (!condition) {
				alert('You must agree with terms and conditions');
				return false;
			};
			if (otp_status==0) {
			 	alert('OTP code mismatch');
			 	return false;
			 };

			var phone 			= $('#phone').val();	 		
			var code 			= $('#phone_code').val();
			var email 			= $('#email').val();
			var name 			= $('#name').val();
			var nric 			= $('#nric').val();
			var duration 		= gl_duration; 
			var endtime 		= $('#lbl_time').val();
			var starttime 		= $('#time').val();
			var doctorid 		= $('#doctor').val();
			var procedureid 	= $('#procedure').val();
			var bookdate 		= $('#date').val();
			var remarks 		= $('#remarks').val();
			var clinictimeid 	= 0;
			var clinicID 		= $('#h-clinicID').val();
			var price 			= $('#sc3-price').text();
			
			var cnf = confirm('Please confirm booking');
			if (cnf) {
				$('#btn-confirm').text('Wait ...');
			    $('#btn-confirm').attr('disabled', 'disabled');
				$.ajax({
					url: base_url+'widget/new-widget-booking',
					type: 'POST',
					data: {phone:phone, code:code, email:email, name:name, nric:nric, duration:duration, endtime:endtime, starttime: starttime, doctorid:doctorid, procedureid:procedureid, bookdate:bookdate, clinictimeid:clinictimeid, remarks:remarks, clinicID:clinicID, price:price},
				})
				.done(function(data) {
					// console.log(data);

					$.ajax({
						url: 'https://frozen-bastion-83762.herokuapp.com/api/send/clinic/booking/notification',
						type: 'POST',
						data: { clinicId: clinicID }
					});
					
			    	$('#btn-confirm').text('Booking Completed');
                    // $('#screen3').css('display', 'none');
                    // $('#screen4').css('display', 'block');

                    $('#confirm-tab').removeClass('active');
			      	$('#confirm').removeClass('active');
			      	$('#done-tab').addClass('active');
			      	$('#done').addClass('in active');

				});	
			};

		});


// ....................................................................................../

	$('#btn-cancel1').click(function(event) {
		$('#form-1').trigger("reset");
		$('#lbl_time').val('');
		$('#time').val('');
		return false;
	});

	$('#btn-cancel2').click(function(event) { 
		
		// $('#screen2').css('display', 'none');
		// $('#screen3').css('display', 'none');
  //       $('#screen1').css('display', 'block');
  		$('#patient-tab').removeClass('active');
      	$('#patient').removeClass('active');
      	$('#booking-tab').addClass('active');
      	$('#booking').addClass('in active');
  		return false;
	});

	$('#btn-cancel3').click(function(event) {
		$('#btn-book').text('Resend OTP code');
		$('#btn-book').removeAttr('disabled')
		// $('#screen3').css('display', 'none');
		// $('#screen1').css('display', 'none');
  //       $('#screen2').css('display', 'block');
  		$('#confirm-tab').removeClass('active');
      	$('#confirm').removeClass('active');
      	$('#patient-tab').addClass('active');
      	$('#patient').addClass('in active');
        $('#h-otp-status').val(0);
	});

	// ..................................................................

	$('#code').blur(function(event) {
		code = $(this).val();
		$.ajax({
			url: base_url+'widget/validate-otp',
			type: 'POST',
			data: {code:code}
		})
		.done(function(data) {
	    	$('#h-otp-status').val(data);
	    	if (data==1) {
	    		$('#lbl_otp_code_msg').html('OTP matched').css('color', 'green');
	    	} else{
	    		$('#lbl_otp_code_msg').html('OTP mismatched').css('color', 'red');
	    	};
	    	

		});
	});
		

$('#resend_otp').click(function(event) {
	resendOtpSms();
$(this).text('Sending ...');
	setTimeout(function() {
	   
		$('#lbl_otp_code_msg').html("We have sent a SMS again");
	    $('#resend_otp').text('Resend Code');
    }, 5000);

});



$('#resend_otp').click(function(event) {
	resendOtpSms();
$(this).text('Sending ...');
	setTimeout(function() {
	   
		$('#lbl_otp_code_msg').html("We have sent a SMS again");
	    $('#resend_otp').text('Resend Code');
    }, 5000);

});


// $('body').on('keydown', '#phone_code', function(c) {
		
//         if (!(c.keyCode>=96 && c.keyCode<=105) && !(c.keyCode>=48 && c.keyCode<=57) && c.keyCode!=107 && c.keyCode!=8 && c.keyCode!=9) {
//             return false;
//         }

//     });
	
	$('body').on('keydown', '#phone', function(c) {

        // if (String.fromCharCode(c.keyCode).replace(/[^0-9]/g, '') == '') {
        //     return false;
        // }
        if (!(c.keyCode>=96 && c.keyCode<=105) && !(c.keyCode>=48 && c.keyCode<=57) && c.keyCode!=8 && c.keyCode!=9) {
            return false;
        }

    });

}); //end of ready///////////////////////////////////////////////////////////////////////////////////////////////////


function viewDetails () {

	$('#btn-next-2').text("Wait ...");


	var doctor = $('#doctor option:selected').text();
	var procedure = $('#procedure option:selected').text();
	var date = $('#date').val();
	var ftime = $('#time').val();
	var ttime = $('#lbl_time').val();
	var datetime = date+'<br>'+ftime+' - '+ttime;
	var remarks = $('#remarks').val();
	var nric = $('#nric').val();
	var email = $('#email').val();
	var phone_code = $('#phone_code').val();
	var phone = $('#phone').val();
	var emailphone = email+'<br>'+phone_code+' '+phone;
	// var price = $('#price').val('SGD 400');
	var name = $('#name').val();

	$('#sc3-doctor').html(doctor);
	$('#sc3-procedure').html(procedure);
	$('#sc3-datetime').html(datetime);
	$('#sc3-notes').html(remarks);
	$('#sc3-nric').html(nric);
	$('#sc3-name').html(name);
	$('#sc3-emailphone').html(emailphone);
	$('#sc3-price').html(gl_price);

	$('#btn-book').text('Sending OTP code ...');
	$('#btn-book').attr('disabled', 'disabled');
    sendOtpSms(phone_code,phone);

    setTimeout(function() {
		$('#lbl_otp_code_msg').html("We have sent a SMS");
	    // $('#screen3').css('display', 'block');
	    // $('#screen2').css('display', 'none');
	    // $('#screen1').css('display', 'none');
	    $('#patient-tab').removeClass('active');
	    $('#patient').removeClass('active');
	    $('#confirm-tab').addClass('active');
	    $('#confirm').addClass('in active');
	    $('#btn-next-2').text("Next");
	    $('#lbl_otp_code_msg').css("color","#333");

    }, 3000);
	
}

function sendOtpSms (code,phone) {
	
	$.ajax({
		url: base_url+'widget/send-otp-sms',
		type: 'POST',
		// dataType: 'json',
		data: {code:code,phone:phone},
	})
	.done(function(data) {
		//$('#lbl_otp_code_msg').html('OTP mismatched').css('color', 'black');
    	

	});
}
	

function resendOtpSms () {
	
	var code = $('#phone_code').val();
	var phone = $('#phone').val();

	$.ajax({
		url: base_url+'widget/send-otp-sms',
		type: 'POST',
		// dataType: 'json',
		data: {code:code,phone:phone},
	})
	.done(function(data) {
		//$('#lbl_otp_code_msg').html('OTP mismatched').css('color', 'black');
    	

	});
}



function resendOtpSms () {
	
	var code = $('#phone_code').val();
	var phone = $('#phone').val();

	$.ajax({
		url: base_url+'widget/send-otp-sms',
		type: 'POST',
		// dataType: 'json',
		data: {code:code,phone:phone},
	})
	.done(function(data) {
		//$('#lbl_otp_code_msg').html('OTP mismatched').css('color', 'black');
    	

	});
}



function enableAll(date) {
	
    var sdate = $.datepicker.formatDate( 'd-m-yy', date)
    if($.inArray(sdate, gl_enableDays) != -1) {
        return [true];
    }
    return [false];

}

function enableDays () {
	var docID = $('#doctor').val();
	var clinicID = $('#h-clinicID').val();
	$('#date').attr('disabled', true);
	$.ajax({
		url: base_url+'widget/enable-dates',
		type: 'POST',
		data: {docID: docID, clinicID:clinicID },
	})
	.done(function(data) {
		gl_enableDays = data;
		$('#date').datepicker('option', 'beforeShowDay', enableAll);
		$('#date').attr('disabled', false);
	});
}
//...

function laodProcedureDoctors(procedureID) {
		var procedureID = procedureID;
		var clinicID = $('#h-clinicID').val();

		$.ajax({
			url: base_url+'widget/load-procedure-doctor',
			type: 'POST',
			data: {procedureID: procedureID, clinicID:clinicID },
		})
		.done(function(data) {
			 $('#doctor').html(data);

		});
}
