
jQuery(document).ready(function($) {
	
  // var protocol = jQuery(location).attr('protocol');
  // var hostname = jQuery(location).attr('hostname');
  // var folderlocation = $(location).attr('pathname').split('/')[1];
  // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
  window.base_url = window.location.origin + '/app/';
// ------------ Page onload default selection -----------
	
	
	loadStaffSettingPanel();

	// loadDefualtsettingPage();

	addDoctorHolyday ();

	GetDoctorHolyday();

	UpdateDoctorHolyday ();

	DeleteDoctorHolyday ();


// ...............................popover.................................
	
	$('#btn-doctor-pop').popover({
		html: 'true',
		container: 'body',
	    title : 'Add New Doctor',
	    content : '<input type="text" class="form-control pop-input" id="pop-doctor-name" placeholder="Doctor Name" style="width: 250px; background: white !important;"><br><input type="text" class="form-control pop-input" id="pop-doctor-email" placeholder="Doctor Email" style="width: 250px; background: white !important;"><br> <button id="doctor-pop-add" class="btn pop-add-btn">Add Doctor</button> <button class="btn pop-close-btn" id="doctor-pop-cancel">Cancel</button>'
	});

// `````````````````````````````````````````````````````````````````````````````````````````````````
	$('#btn-staff-pop').popover({
		html: 'true',
		container: 'body',
	    title : 'Add New Staff',
	    content : '<input type="text" class="form-control pop-input" id="pop-staff-name" placeholder="Staff Name" style="width: 250px; background: white !important;"><br><input type="text" class="form-control pop-input" id="pop-staff-email" placeholder="Staff Email" style="width: 250px; background: white !important;"><br> <button id="staff-pop-add" class="btn pop-add-btn">Add Staff</button> <button class="btn pop-close-btn" id="staff-pop-cancel">Cancel</button>'
	});

// `````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````

	$(document).on('click', '#doctor-pop-cancel', function(event) {
		$('#btn-doctor-pop').popover('hide');
	});

	$(document).on('click', '#staff-pop-cancel', function(event) {
		$('#btn-staff-pop').popover('hide');
	});

	$(document).on('click', '#doctor-delete-cancel', function(event) {
		$('#btn-doctor-delete').popover('hide');
	});

	$(document).on('click', '#break-delete-cancel', function(event) {
		$('.break-pop').popover('hide');
	});

	$(document).on('click', '#staff-delete-cancel', function(event) {
		$('#btn-staff-delete').popover('hide');
	});
	


	$(document).on('click', '#go-to-add-service', function(event) {

		$( "#service-tab" ).trigger( "click" );

		setTimeout(function(){

			$( "#btn-service-add" ).trigger( "click" );

		},40);
		
		
	});


// ``````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````

	$(document).on('click', '#staff-pop-add', function(event) {
		var name = $('#pop-staff-name').val();
		var email = $('#pop-staff-email').val();
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
		
		if (name=='') {
			// $('#input#pop-staff-name.form-control').removeClass('pop-input');
			// $('#input#pop-staff-name.form-control').addClass('input-error');
			$('#pop-staff-name').addClass('input-error');
			$('#pop-staff-name').removeClass('pop-input');
			return false;
		} else {
			$('#pop-staff-name').removeClass('input-error');
			$('#pop-staff-name').addClass('pop-input');

		}
		
		if (email == '' || !re.test(email)) {
			$('#pop-staff-email').addClass('input-error');
			$('#pop-staff-email').removeClass('pop-input');

			return false;
		}
		 else {
		 	$('#pop-staff-email').removeClass('input-error');
			$('#pop-staff-email').addClass('pop-input');

		 }

		$.ajax({
	      url: base_url+'setting/staff/addStaff',
	      type: 'POST',
	      data:{ name:name, email:email}
	    })
	    .done(function(data) {
	    	$('#btn-staff-pop').popover('hide');
	    	$('#main-tab-staff').html(data);

	    	var text = "Staff Added Successfully !";
			$.toast({
                text: text,
                showHideTransition: 'slide',
                icon: 'success',
                // hideAfter : false,
                stack: 1,
                // bgColor : '#1667AC'
              });
	    });

	    event.stopImmediatePropagation();
    	return false;
	});

// ``````````````````````````````````````````````````````````````````````````````````````````````````````````````````````
	$(document).on('click', '#doctor-pop-add', function(event) {
		var name = $('#pop-doctor-name').val();
		var email = $('#pop-doctor-email').val();
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

		if (name=='') {
			$('#pop-doctor-name').removeClass('pop-input');
			$('#pop-doctor-name').addClass('input-error');
			return false;
		} else {
			$('#pop-doctor-name').addClass('pop-input');
			$('#pop-doctor-name').removeClass('input-error');
		}
		
		if (email == '' || !re.test(email)) {
			$('#pop-doctor-email').removeClass('pop-input');
			$('#pop-doctor-email').addClass('input-error');
			return false;
		}
		 else {
		 	$('#pop-doctor-email').addClass('pop-input');
		 	$('#pop-doctor-email').removeClass('input-error');
		 }
		// alert('');

		$.ajax({
	      url: base_url+'setting/staff/addDoctor',
	      type: 'POST',
	      data:{ name:name, email:email }
	    })
	    .done(function(data) {

	    	$('#btn-doctor-pop').popover('hide');
	    	$('#main-tab-staff').html(data);

	    	var text = "Doctor Added Successfully !";
			$.toast({
                text: text,
                showHideTransition: 'slide',
                icon: 'success',
                // hideAfter : false,
                stack: 1,
                // bgColor : '#1667AC'
              });
	    });

	    event.stopImmediatePropagation();
    	return false;
	});

// --------------------------------------------------------------------------------------------

	
	$( "#staff-doctor-list div b, #staff-list div b" ).hover(
	  function() { 
	  	$( this ).css("cursor", "pointer");
	});

// ```````````````````````````````````````````````````````````````````````````````````````````````````
	$("#staff-doctor-list div b").click(function(event) {

		var doctor_id = $(this).attr('id');
		$('#h-doctor-id').val(doctor_id);

		$.ajax({
	      url: base_url+'setting/staff/ajaxGetDoctorDetailtabPanel',
	      type: 'GET',
	    })

	    .done(function(data) {

	    	$('#detail-wrapper').html(data);
	    	loadDefualtsettingPage();

	    });

	    $("#staff-doctor-list div b").css("color", "#777676");
	    $("#staff-list div b").css("color", "#777676");
	 	$(this).css("color", "black");

	});

// ------------------------------------------------------------------

	$("#staff-list div b").click(function(event) {

		var staff_id = $(this).attr('id');
		$('#h-staff-id').val(staff_id);

		$.ajax({
	      url: base_url+'setting/staff/ajaxGetStaffDetailtabPanel',
	      type: 'post',
	      data:{staff_id:staff_id}
	    })

	    .done(function(data) {

	    	$('#detail-wrapper').html(data);
	    });

	    $("#staff-doctor-list div b").css("color", "#777676");
	    $("#staff-list div b").css("color", "#777676");
	 	$(this).css("color", "black");

	});


// ------ load staff details tab page -------

	$("#detail-wrapper").on("click","#staff-details-tab", function(){

		var doctor_id = $('#h-doctor-id').val();

		$.ajax({
	      url: base_url+'setting/staff/ajaxGetStaffDetailsTab',
	      type: 'POST',
	      data:{doctor_id:doctor_id}
	    })
	    .done(function(data) {

	    	$('#details-main-tab').html(data);

	    });

 	 });


// ------ load staff services tab page -------

	$("#detail-wrapper").on("click","#staff-service-tab", function(){

		var id = $('#h-doctor-id').val();

		$.ajax({
	      url: base_url+'setting/staff/ajaxGetStaffServicesTab',
	      type: 'POST',
	      data:{ id:id}
	    })
	    .done(function(data) {

	    	$('#service-main-tab').html(data);
	    	// alert(id);

	    });

 	 });

// ------ load staff working hours tab page -------

	$("#detail-wrapper").on("click","#staff-working_hours-tab", function(){

		var doctor_id = $('#h-doctor-id').val();
		$.ajax({
	      url: base_url+'setting/staff/ajaxGetStaffWorkingHoursTab',
	      type: 'POST',
	      data:{doctor_id:doctor_id}
	    })
	    .done(function(data) {

	    	$('#working_hours-main-tab').html(data);

	    });

 	 });

// ------ load staff breaks tab page -------

	$("#detail-wrapper").on("click","#staff-breaks-tab", function(){
		var doctor_id = $('#h-doctor-id').val();

		$.ajax({
	      url: base_url+'setting/staff/ajaxGetStaffBreaksTab',
	      type: 'POST',
	      data:{doctorid:doctor_id, doctor_id:doctor_id}
	    })
	    .done(function(data) {

	    	$('#breaks-main-tab').html(data);

	    });

 	 });

// ------ load staff time off tab page -------

	$("#detail-wrapper").on("click","#staff-time_off-tab", function(){

		var doctor_id = $('#h-doctor-id').val();

		$.ajax({
	      url: base_url+'setting/staff/ajaxGetStaffTimeOffTab',
	      type: 'POST',
	      data:{doctor_id:doctor_id}
	    })
	    .done(function(data) {

	    	$('#time_off-main-tab').html(data);
	    	$( "#day-checkbox" ).trigger( "change" );

	    });

 	 });

// ----------------------------------------------------------------------------------

	$(document).on('click', '#doc-mobile-codes li', function(event) {

  		id = $(this).attr('id');

  		$('#doc-mobile-code').text(id);
	});

	$(document).on('click', '#staff-mobile-codes li', function(event) {

  		id = $(this).attr('id');

  		$('#staff-mobile-code').text(id);
	});


// ----------------------- Upload Doctor Profile Image -------------------------------


	$(document).on('click', '.doctor-image', function(event) {

		$( "#doctor-profile-image-file" ).trigger( "click" );
		event.stopImmediatePropagation();
    	return false;
	});


	$(document).on('change', '#doctor-profile-image-file', function(event) {

		var formData = new FormData();
        formData.append('file', $('#doctor-profile-image-file')[0].files[0]);

        $('#alert_box').css('display', 'block');
		$('#alert_box').html('Please wait while your image is being uploaded...');

        $.ajax({

        	type: "POST",
	      	url: base_url+'clinic/clinic-image-upload',
	      	data: formData,
          	processData: false,
          	contentType: false,
           	enctype: 'multipart/form-data',
	    })
	    .done(function(data) {

	    	setTimeout(function(){

					if (data != 0) {

	    				$('.doctor-image').attr('src', data['img']);

	    				var text = "Profile Image Successfully Updated!";
						$.toast({
			                text: text,
			                showHideTransition: 'slide',
			                icon: 'success',
			                // hideAfter : false,
			                stack: 1,
			                // bgColor : '#1667AC'
			              });
            		}

            		$('#alert_box').css('display', 'none');

			 	}, 500);

	    });
	});


// `````````````````````````````````````update doctor details````````````````````````````````````````````````````````

	
	$(document).on('click', '#btn-doc-update', function(event) {
		
		var doctor_id = $('#h-doctor-id').val();
		var name = $('#doc-name').val();
		var qualification = $('#doc-qualification').val();
		var specialty = $('#doc-Specialty').val();
		var mobile = $('#doc-mobile').val();
		var email = $('#doc-email').val();
		var cc_email = $('#doc-cc-email').val();
		var image = $('.doctor-image').attr('src');
		var status = 'details';
		var code = $('#doc-mobile-code').text();

		var mail_valid = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
		var CCmail_valid = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
		var phone_valid = /[0-9 -()+]+$/;

		if (name=='') {

			$('#doc-name').addClass('input-error');
			return false;
		} else {

			$('#doc-name').removeClass('input-error');
		}
		
		if (mobile=='' || !phone_valid.test(mobile)) {

			$('#doc-mobile').addClass('input-error');
			return false;
		} else {

			$('#doc-mobile').removeClass('input-error');
		}
		
		if (email == '' || !mail_valid.test(email)) {

			$('#doc-email').addClass('input-error');
			return false;
		}
		 else {

		 	$('#doc-email').removeClass('input-error');
		}

		if (cc_email != '' && !CCmail_valid.test(cc_email)) {

			$('#doc-cc-email').addClass('input-error');
			return false;
		}
		else {

		 	$('#doc-cc-email').removeClass('input-error');
		}
		

		
		$.ajax({
	      url: base_url+'setting/staff/updateDoctor',
	      type: 'POST',
	      data:{doctor_id:doctor_id, name:name, qualification:qualification, specialty:specialty, mobile:mobile, code:code, email:email, cc_email:cc_email, image:image, status:status}
	    })
	    .done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');
		    	loadDefualtsettingPage();
		    	$( '#staff-doctor-list > .staff-doctor  > div > #'+doctor_id+'' ).text(name);

		    	var text = "Details Updated Successfully !";
				$.toast({
	                text: text,
	                showHideTransition: 'slide',
	                icon: 'success',
	                // hideAfter : false,
	                stack: 1,
	                // bgColor : '#1667AC'
	              });
			 }, 3000);

	    });
		
	});

// `````````````````````````````update staff````````````````````````````````````````````````````````````````

	$(document).on('click', '#btn-staff-update', function(event) {
			
		var staff_id = $('#h-staff-id').val();
		var qualification = $('#staff-qualification').val();
		var mobile = $('#staff-mobile').val();
		var code = $('#staff-mobile-code').text();
		var email = $('#staff-email').val();
		var cc_email = $('#staff-cc-email').val();
		var status = 'details';

		var mail_valid = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
		var CCmail_valid = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
		var phone_valid = /[0-9 -()+]+$/;
		
		if (mobile=='' || !phone_valid.test(mobile)) {

			$('#staff-mobile').addClass('input-error');
			return false;
		} else {

			$('#staff-mobile').removeClass('input-error');
		}
		
		if (email == '' || !mail_valid.test(email)) {

			$('#staff-email').addClass('input-error');
			return false;
		}
		 else {

		 	$('#staff-email').removeClass('input-error');
		}

		if (cc_email != '' && !CCmail_valid.test(cc_email)) {

			$('#staff-cc-email').addClass('input-error');
			return false;
		}
		else {

		 	$('#staff-cc-email').removeClass('input-error');
		}

			
			$.ajax({
		      url: base_url+'setting/staff/updateStaff',
		      type: 'POST',
		      data:{staff_id:staff_id, qualification:qualification, mobile:mobile, code:code, email:email, cc_email:cc_email, status:status}
		    })
		    .done(function(data) {

				$('#alert_box').css('display', 'block');
				$('#alert_box').html('Updating...');
				setTimeout(function(){ 
					$('#alert_box').css('display', 'none');
			    	$('#detail-wrapper').html(data);

			    	var text = "Staff Details Updated !";
					$.toast({
		                text: text,
		                showHideTransition: 'slide',
		                icon: 'success',
		                // hideAfter : false,
		                stack: 1,
		                // bgColor : '#1667AC'
		              });
				 }, 3000);

		    });
			
		});


// ``````````````````````````````````set pin for doctor``````````````````````````````````````````````````````
	
	$(document).on('click', '#btn-doc-pin', function(event) {
		var doctor_id = $('#h-doctor-id').val();
		var pin = $('#doc-pin').val();
		var status = 'pin';

		var valid = /[0-9 -()+]+$/;
		
		if (pin=='' || !valid.test(pin)) {

			$('#doc-pin').addClass('input-error');
			return false;

		} else {

			if (pin.length <= 4 && pin.length >= 4){

				$('#doc-pin').removeClass('input-error');

			} else {

				$('#doc-pin').addClass('input-error');
				return false;
			}
		}


		$.ajax({
	      url: base_url+'setting/staff/updateDoctor',
	      type: 'POST',
	      data:{doctor_id:doctor_id, pin:pin, status:status}
	    })
	    .done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');
		    	loadDefualtsettingPage();

		    	var text = "Pin Updated !";
				$.toast({
	                text: text,
	                showHideTransition: 'slide',
	                icon: 'success',
	                // hideAfter : false,
	                stack: 1,
	                // bgColor : '#1667AC'
	              });
			 }, 3000);

	    });

	});

// ``````````````````````````````````set pin for staff``````````````````````````````````````````````````````
	
	$(document).on('click', '#btn-staff-pin', function(event) {
		var staff_id = $('#h-staff-id').val();
		var pin = $('#staff-pin').val();
		var status = 'pin';

		var valid = /[0-9 -()+]+$/;
		
		if (pin=='' || !valid.test(pin)) {

			$('#staff-pin').addClass('input-error');
			return false;

		} else {

			if (pin.length <= 4 && pin.length >= 4){

				$('#staff-pin').removeClass('input-error');

			} else {

				$('#staff-pin').addClass('input-error');
				return false;
			}
		}

		$.ajax({
	      url: base_url+'setting/staff/updateStaff',
	      type: 'POST',
	      data:{staff_id:staff_id, pin:pin, status:status}
	    })
	    .done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');
		    	$('#detail-wrapper').html(data);

		    	var text = "Pin Updated !";
				$.toast({
	                text: text,
	                showHideTransition: 'slide',
	                icon: 'success',
	                // hideAfter : false,
	                stack: 1,
	                // bgColor : '#1667AC'
	              });
			 }, 3000);

	    });

	});


// ````````````````````````````````````update new pin````````````````````````````````````````````````````
	
	$(document).on('click', '#btn-doc-pin-update', function(event) {
		var doctor_id = $('#h-doctor-id').val();
		var old_pin = $('#doc-old-pin').val();
		var pin = $('#doc-newpin').val();
		var repin = $('#doc-repin').val();
		var status = 'new-pin';

		if (pin!=repin) { alert('PIN mismatch!'); return false; }

		$.ajax({
	      url: base_url+'setting/staff/updateDoctor',
	      type: 'POST',
	      data:{doctor_id:doctor_id, old_pin:old_pin, pin:pin, status:status}
	    })
	    .done(function(data) {

			if (data == 0){

				alert('Existing PIN mismatch!');
			}
			else{

				$('#alert_box').css('display', 'block');
				$('#alert_box').html('Updating...');
				setTimeout(function(){ 
					$('#alert_box').css('display', 'none');
			    	loadDefualtsettingPage();

			    	var text = "Pin Updated !";
					$.toast({
		                text: text,
		                showHideTransition: 'slide',
		                icon: 'success',
		                // hideAfter : false,
		                stack: 1,
		                // bgColor : '#1667AC'
		              });
				 }, 3000);

			}

	    });

	});

// ````````````````````````````````````update new staff pin````````````````````````````````````````````````````
	
	$(document).on('click', '#btn-staff-pin-update', function(event) {
		var staff_id = $('#h-staff-id').val();
		var old_pin = $('#staff-old-pin').val();
		var pin = $('#staff-newpin').val();
		var repin = $('#staff-repin').val();
		var status = 'new-pin';

		if (pin!=repin) { alert('PIN mismatch!'); return false; }

		$.ajax({
	      url: base_url+'setting/staff/updateStaff',
	      type: 'POST',
	      data:{staff_id:staff_id, old_pin:old_pin, pin:pin, status:status}
	    })
	    .done(function(data) {

	    	if (data == 0){

				alert('Existing PIN mismatch!');
			}
			else{

				$('#alert_box').css('display', 'block');
				$('#alert_box').html('Updating...');
				setTimeout(function(){ 
					$('#alert_box').css('display', 'none');
			    	$('#detail-wrapper').html(data);

			    	var text = "Pin Updated !";
					$.toast({
		                text: text,
		                showHideTransition: 'slide',
		                icon: 'success',
		                // hideAfter : false,
		                stack: 1,
		                // bgColor : '#1667AC'
		              });
				 }, 3000);
			}
			
	    });

	});

// ````````````````````````````````````send google calendar request````````````````````````````````````````````````````
	
	$(document).on('click', '#btn-send-Google-request', function(event) {
		var doctor_id = $('#h-doctor-id').val();
		var gmail = $('#doc-gmail').val();

		var valid = /[A-Z0-9._%+-]+\b@gmail.com\b/igm;
		
		if (gmail == '' || !valid.test(gmail)) {

			$('#doc-gmail').addClass('input-error');
			return false;
		}
		 else {

		 	$('#doc-gmail').removeClass('input-error');
		}

		$('#alert_box').css('display', 'block');
		$('#alert_box').html('Sending Request...');
		
		$.ajax({
	      url: base_url+'gcal/sendOAuthRequest',
	      type: 'POST',
	      data:{doctorid:doctor_id, gmail:gmail}
	    })
	    .done(function(data) {

			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');
		    	loadDefualtsettingPage();

		    	var text = "Google Calendar Request Sent !";
				$.toast({
	                text: text,
	                showHideTransition: 'slide',
	                icon: 'success',
	                // hideAfter : false,
	                stack: 1,
	                // bgColor : '#1667AC'
	              });
			 }, 3000);

	    });

	});


// ````````````````````````````````````remove google calendar request````````````````````````````````````````````````````
	
	$(document).on('click', '#btn-remove-Google-request', function(event) {
		var doctor_id = $('#h-doctor-id').val();

		$('#alert_box').css('display', 'block');
		$('#alert_box').html('Removing Credentials...');
		
		$.ajax({
	      url: base_url+'gcal/revokeToken',
	      type: 'POST',
	      data:{doctorid:doctor_id}
	    })
	    .done(function(data) {

			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');
		    	loadDefualtsettingPage();

		    	var text = "Credentials Removed !";
				$.toast({
	                text: text,
	                showHideTransition: 'slide',
	                icon: 'warning',
	                // hideAfter : false,
	                stack: 1,
	                // bgColor : '#1667AC'
	              });
			 }, 3000);

	    });

	});


// -------------------- service tab - change procedures ------------------


	$(document).on('change', '#doctor-all-services', function(event) {

		var doctor_id = $('#h-doctor-id').val();

		if($(this).is(":checked")) {

			var checked = 1;
	            
	        Update_Doctor_AllService(doctor_id,checked);

        }else{
        	
	        var checked = 0;

	        Update_Doctor_AllService(doctor_id,checked);

        }


	});

	$(document).on('change', '.doctor-service-staff', function(event) {

		var doctor_id = $('#h-doctor-id').val();
		var service_id = $(this).val();
		
		// console.log(service_id);

		if($(this).is(":checked")) {

			var checked = 1;
	            
	        Update_Doctor_Service(doctor_id,service_id,checked);

        }else{
        	
	        var checked = 0;

	        Update_Doctor_Service(doctor_id,service_id,checked);

        }

	});



// --------------------  Add / Remove Doctor break slots  --------------------------

var mon_row = 0;
var tue_row = 0;
var wed_row = 0;
var thu_row = 0;
var fri_row = 0;
var sat_row = 0;
var sun_row = 0;


	$(document).on('click', '.staff-break-btn', function(event) {

        if ($(this).attr('id') == 'add-break-mon'){

        	mon_row = mon_row + 1;
        	var append_class = '.doc-break-panel-mon';
        	var row_num = mon_row;
        	var day_name = 'mon';

        }else if ($(this).attr('id') == 'add-break-tue'){

        	tue_row = tue_row + 1;
        	var append_class = '.doc-break-panel-tue';
        	var row_num = tue_row;
        	var day_name = 'tue';

        }else if ($(this).attr('id') == 'add-break-wed'){

        	wed_row = wed_row + 1;
        	var append_class = '.doc-break-panel-wed';
        	var row_num = wed_row;
        	var day_name = 'wed';

        }else if ($(this).attr('id') == 'add-break-thu'){

        	thu_row = thu_row + 1;
        	var append_class = '.doc-break-panel-thu';
        	var row_num = thu_row;
        	var day_name = 'thu';
        	
        }else if ($(this).attr('id') == 'add-break-fri'){

        	fri_row = fri_row + 1;
        	var append_class = '.doc-break-panel-fri';
        	var row_num = fri_row;
        	var day_name = 'fri';
        	
        }else if ($(this).attr('id') == 'add-break-sat'){

        	sat_row = sat_row + 1;
        	var append_class = '.doc-break-panel-sat';
        	var row_num = sat_row;
        	var day_name = 'sat';
        	
        }else if ($(this).attr('id') == 'add-break-sun'){

        	sun_row = sun_row + 1;
        	var append_class = '.doc-break-panel-sun';
        	var row_num = sun_row;
        	var day_name = 'sun';
        	
        }

		var S4 = (((1+Math.random())*0x10000)|0).toString(16).substring(1);
		guid = (S4 + S4 + "-" + S4 + "-4" + S4.substr(0,3) + "-" + S4 + "-" + S4 + S4 + S4).toLowerCase();

            $(append_class).append('<div id=doc-break-' + day_name + row_num + ' guid=' + guid + ' class="col-md-12 doc-break" style="padding: 0;"> ' +
				'<div class="col-md-4" style="padding-top: 8px;">' +
					'<input guid=' + guid + ' class="timepicker doc-break-time_from" style="float: right;" type="button" value="12:00 PM">' +
				'</div>' +
				'<span class="col-md-1 text-center" style="padding: 0; width: 12px; padding-top: 10px;">to</span>' +
				'<div class="col-md-4" style="padding-top: 8px;">' +
					'<input guid=' + guid + ' type="button" class="timepicker doc-break-time_to" value="01:PM">' +
				'</div>' +
				'<span>' +
				'<a guid='+guid+' id=delete-break-'+ day_name + row_num + ' href="#"  data-toggle="popover" class="break-pop" data-placement="left" data-trigger="focus" ><span class="glyphicon glyphicon-trash" aria-hidden="true" style="padding-top: 12px; color: black;"></span></a>' +
				'</span>' +
				'</div>');

        	loadtimepicker ();

        	addBreak(day_name,'doc-break-' + day_name + row_num);


  //      	$('#delete-break-'+ day_name + row_num).popover({

		// 	html: 'true',
	 //        title : 'Are you sure ?',
	 //        content : '<button guid=' + guid + ' id=' + row_num + ' class="btn btn-danger delete-break-'+ day_name +'">Delete</button> <button class="btn" id="break-delete-cancel">Cancel</button>'
		// });

    });


// `````````````````````````````````````delete break````````````````````````````````````

	$(document).on('click', '.break-pop', function(event) {

		guid = $(this).attr('guid');
		doctor_id = $('#h-doctor-id').val();

		var cnf = confirm("Are you sure you want to remove this break?");
    	if(cnf){

		$('#alert_box').css('display', 'block');
		$('#alert_box').html('Updating...');

		$.ajax({
	      url: base_url+'setting/staff/removeBreak',
	      type: 'POST',
	      data:{ id:guid, doctorid:doctor_id}

	    })
		.done(function(data) {
		$('#alert_box').css('display', 'none');	
		$('#breaks-main-tab').html(data);


		});
	}

	event.stopImmediatePropagation();
	    return false;

	});

// -------------------------------------------------------------------------------


	$(document).on('change', '#day-checkbox', function(event) {

		var Start_date = $('#custom-start-date').val();
		var End_date = $('#custom-end-date').val();
		var Start_Time = $('#custom-start-time').val();
		var End_Time = $('#custom-end-time').val();

		var day_Start_date = $('#day-start-date').val();
		var day_End_date = $('#day-end-date').val();

		if($(this).is(":checked")) {

			$('#custom-time-off').css('display', 'none');
			$('#day-time-off').css('display', 'block');

			$('#time-wall').html('From ' + day_Start_date + ' to ' + day_End_date);

        }else{

        	$('#day-time-off').css('display', 'none');
        	$('#custom-time-off').css('display', 'block');

        	$('#time-wall').html('From '+ Start_date +', '+ Start_Time +' to '+ End_date +', '+ End_Time);
        	
        }

	});

// -------------------------------------------------------------------------------

	$(document).on('change', '.time-off-change', function(event) {
		
		var Start_date = $('#custom-start-date').val();
		var End_date = $('#custom-end-date').val();
		var Start_Time = $('#custom-start-time').val();
		var End_Time = $('#custom-end-time').val();

		var day_Start_date = $('#day-start-date').val();
		var day_End_date = $('#day-end-date').val();

		if($('#day-checkbox').is(":checked")) {

			$('#time-wall').html('From ' + day_Start_date + ' to ' + day_End_date);

        }else{

        	$('#time-wall').html('From '+ Start_date +', '+ Start_Time +' to '+ End_date +', '+ End_Time);
        	
        }

	});

// --------------------------------------------------------------------------------

	$(document).on('change', '.timepicker', function(event) {
		
		guid = $(this).attr('guid');
		doctor_id = $('#h-doctor-id').val();
		var time_from = $(this).closest('.doc-break').find('.doc-break-time_from').val();
		var time_to = $(this).closest('.doc-break').find('.doc-break-time_to').val();


			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');

			$.ajax({
		      url: base_url+'setting/staff/updateBreak',
		      type: 'POST',
		      data:{ id:guid, doctorid:doctor_id, time_from:time_from, time_to:time_to }

		    })
			.done(function(data) {
				$('#alert_box').css('display', 'none');	
				$('#breaks-main-tab').html(data);

				
			});

	});

// --------------------------------------------------------------------------------

	$(document).on('change', '.doc-toggel', function(event) {
		
		var doctor_id = $('#h-doctor-id').val();
		var id = $(this).attr('id');

		if (id == 'doctor-login-toggle'){

			var status = 1;
			var value = $('#doctor-login-toggle').val();
			// console.log ('log: ' + value);

		}else if (id == 'requires-pin-toggle') {

			var status = 2;
			var value = $('#requires-pin-toggle').val();
			// console.log ('pin: ' + value);

		} else if (id == 'google-sync-toggle') {

			var status = 3;
			var value = $('#google-sync-toggle').val();
			// console.log ('gl: ' + value);

		}

		$.ajax({

      		url: base_url+'setting/staff/Update-doctor-detail-toggal',
		    type: 'POST',
		    data:{ doctorid:doctor_id, status:status, value:value }

    	})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){

				$('#alert_box').css('display', 'none');

			}, 1000);

		});


	});

// --------------------------------------------------------------------------------

	$(document).on('change', '#pin-toggle', function(event) {
		
		var staff_id = $('#h-staff-id').val();
		var value = $(this).val();

		$.ajax({

      		url: base_url+'setting/staff/Update-staff-detail-toggal',
		    type: 'POST',
		    data:{ staffid:staff_id, value:value }

    	})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){

				$('#alert_box').css('display', 'none');

				var text = "Calendar View Updated !";
				$.toast({
	                text: text,
	                showHideTransition: 'slide',
	                icon: 'success',
	                // hideAfter : false,
	                stack: 1,
	                // bgColor : '#1667AC'
	              });

			}, 1000);

		});


	});

// ---------------------------- Delete Doctor ---------------------------------

	$(document).on('click', '#delete-doctor', function(event) {
		
		var doctor_id = $('#h-doctor-id').val();

		$.ajax({

      		url: base_url+'setting/staff/Delete-doctor',
		    type: 'POST',
		    data:{ doctorid:doctor_id }

    	})
		.done(function(data) {

			// console.log(data);

			if (data == 1){

				$('#alert_box').css('display', 'block');
				$('#alert_box').html('Updating...');
				
				setTimeout(function(){

					$('#alert_box').css('display', 'none');
					$( "#staff-tab" ).trigger( "click" );

				}, 1000);

			}else{

				alert('Can\'t be deleted, This Doctor Already in use !');
			}

		});

		event.stopImmediatePropagation();
	    return false;


	});

// ----------------------------- Delete Staff -------------------------------------

	$(document).on('click', '#delete-staff', function(event) {
		
		var staff_id = $('#h-staff-id').val();

		$.ajax({

      		url: base_url+'setting/staff/Delete-staff',
		    type: 'POST',
		    data:{ staffid:staff_id }

    	})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){

				$('#alert_box').css('display', 'none');
				$( "#staff-tab" ).trigger( "click" );

			}, 1000);

		});

		event.stopImmediatePropagation();
	    return false;

	});



// ======================================================================================================================== //

}); // end of jQuery


// ##################################################################################################################
// #                             		Fuctions                                                                    # 
// ##################################################################################################################


function loadtimepicker (){

	$('.timepicker').timepicker({

		'timeFormat' : 'h:i A',
	});
}



function loadStaffSettingPanel (){

	$.ajax({
	      url: base_url+'setting/staff/ajaxGetDoctorDetailtabPanel',
	      type: 'GET',
	    })

	.done(function(data) {

	    	$('#detail-wrapper').html(data);
	    	$( "#staff-details-tab" ).trigger( "click" );

	});
}

// ````````````````````````````````````````````````````````````````````````````````````````````````

function loadDefualtsettingPage(){

	var doctor_id = $('#h-doctor-id').val();

	$.ajax({
	      url: base_url+'setting/staff/ajaxGetStaffDetailsTab',
	      type: 'POST',
	      data:{doctor_id:doctor_id}
	    })
	    .done(function(data) {

	    	$('#details-main-tab').html(data);

	    });
}

// `````````````````````````````````````````````````````````````````````````````````````````````````````````````````

function Update_Doctor_Service(doctor_id,service_id,checked) {

		$.ajax({
		      url: base_url+'setting/staff/update-Staff-Doctor-Services',
		      type: 'POST',
		      data:{ doctorid:doctor_id , procedure:service_id, checked:checked }

		    })
		    .done(function(data) {

		    	$('#alert_box').css('display', 'block');
				$('#alert_box').html('Updating...');
				setTimeout(function(){ 
					$('#alert_box').css('display', 'none');
			    	$( "#staff-service-tab" ).trigger( "click" );

			    	var text = "Services Updated !";
					$.toast({
		                text: text,
		                showHideTransition: 'slide',
		                icon: 'success',
		                // hideAfter : false,
		                stack: 1,
		                // bgColor : '#1667AC'
		              });
				 }, 1000);

		    });
}


function Update_Doctor_AllService(doctor_id,checked) {

		$.ajax({
		      url: base_url+'setting/staff/update-Staff-Doctor-AllServices',
		      type: 'POST',
		      data:{ doctorid:doctor_id , checked:checked }

		    })
		    .done(function(data) {

		    	$('#alert_box').css('display', 'block');
				$('#alert_box').html('Updating...');
				setTimeout(function(){ 
					$('#alert_box').css('display', 'none');
			    	$( "#staff-service-tab" ).trigger( "click" );

			    	var text = "Services Updated !";
					$.toast({
		                text: text,
		                showHideTransition: 'slide',
		                icon: 'success',
		                // hideAfter : false,
		                stack: 1,
		                // bgColor : '#1667AC'
		              });
				 }, 1000);

		    });
}



function addBreak(day,divid) {


	var time_from = $('#'+divid).find('.doc-break-time_from').val();
	var time_to = $('#'+divid).find('.doc-break-time_to').val();
	var guid = $('#'+divid).attr('guid');
	var day = day;
	var doctor_id = $('#h-doctor-id').val();

	$('#alert_box').css('display', 'block');
	$('#alert_box').html('Updating...');

	$.ajax({
      url: base_url+'setting/staff/addBreak',
      type: 'POST',
      data:{ doctorid:doctor_id, time_from:time_from, time_to:time_to, day:day, guid:guid }

    })
	.done(function(data) {
		$('#breaks-main-tab').html(data);
		$('#alert_box').css('display', 'none');

		var text = "Breaks Updated !";
		$.toast({
            text: text,
            showHideTransition: 'slide',
            icon: 'success',
            // hideAfter : false,
            stack: 1,
            // bgColor : '#1667AC'
          });
	});

}


function guid() {
	var S4 = (((1+Math.random())*0x10000)|0).toString(16).substring(1);

	guid = (S4 + S4 + "-" + S4 + "-4" + S4.substr(0,3) + "-" + S4 + "-" + S4 + S4 + S4).toLowerCase();
	return guid;
}


function addDoctorHolyday (){

	$(document).on('click', '#Add-doctor-time-off', function(event) {

		var doctor_id = $('#h-doctor-id').val();
		var note = $('#time-off-note').val();

		if($('#day-checkbox').is(":checked")) {

			var holiday_type = 0;
			var date_start = $("#day-start-date").val();
			var day_end = $("#day-end-date").val();
        	var time_start = 0;
        	var time_end = 0;
        }else {

        	var holiday_type = 1;
			var date_start = $("#custom-start-date").val();
			var day_end = $("#custom-end-date").val();
        	var time_start = $("#custom-start-time").val();
        	var time_end = $("#custom-end-time").val();
        }

        $.ajax({

      		url: base_url+'setting/staff/Add-doctor-time-off',
		    type: 'POST',
		    data:{ doctorid:doctor_id, holidayType:holiday_type, dateStart:date_start, dayEnd:day_end, timeStart:time_start, timeEnd:time_end, note:note }

    	})
		.done(function(data) {

			$('#Time-off-Modal').modal('hide');
			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){ 

				$('#alert_box').css('display', 'none');
			   	$( "#staff-time_off-tab" ).trigger( "click" );

			}, 1000);

		});
		event.stopImmediatePropagation();
	    return false;

	});

}


function GetDoctorHolyday (){

	$(document).on('click', '.doctor-time-off', function(event) {

		var id = $(this).attr('id');

		// console.log(id);
		
        $.ajax({

      		url: base_url+'setting/staff/get-doctor-time-off',
		    type: 'POST',
		    dataType: 'json',
		    data:{ Holiday_id:id }

    	})
		.done(function(data) {

			$('#Time-off-Modal').modal('show');
			$('#time-off-modal-title').html('Edit Time Off');

			// console.log(data.Type);

			if (data.Type == 1 ){

				$('#day-checkbox').prop('checked', false);
				$('#day-time-off').css('display', 'none');
        		$('#custom-time-off').css('display', 'block');

				$('#new-time-off').css('display', 'none');
				$('#exist-time-off').css('display', 'block');

				$('#h-doctor-holiday-id').val(data.Holiday_id);
				$('#custom-start-date').val(data.Start_date);
				$('#custom-end-date').val(data.End_date);
				$('#custom-start-time').val(data.Start_Time);
				$('#custom-end-time').val(data.End_Time);
				$('#time-off-note').val(data.Note);
				$('#time-wall').html('From '+ data.Start_date +', '+ data.Start_Time +' to '+ data.End_date +', '+ data.End_Time);

				$('#day-start-date').val(data.Start_date);
				$('#day-end-date').val(data.End_date);


			}else{

				$('#day-checkbox').prop('checked', true);
				$('#day-time-off').css('display', 'block');
        		$('#custom-time-off').css('display', 'none');

				$('#new-time-off').css('display', 'none');
				$('#exist-time-off').css('display', 'block');

				$('#h-doctor-holiday-id').val(data.Holiday_id);
				$('#day-start-date').val(data.Start_date);
				$('#day-end-date').val(data.End_date);
				$('#time-off-note').val(data.Note);
				$('#time-wall').html('From ' + data.Start_date + ' to ' + data.End_date);

				$('#custom-start-date').val(data.Start_date);
				$('#custom-end-date').val(data.End_date);

			}

		});

		event.stopImmediatePropagation();
	    return false;

	});

}


function UpdateDoctorHolyday (){

	$(document).on('click', '#update-doctor-time-off', function(event) {

		var doctor_id = $('#h-doctor-id').val();
		var Holiday_id = $('#h-doctor-holiday-id').val();
		var note = $('#time-off-note').val();

		if($('#day-checkbox').is(":checked")) {

			var holiday_type = 0;
			var date_start = $("#day-start-date").val();
			var day_end = $("#day-end-date").val();
        	var time_start = 0;
        	var time_end = 0;
        }else {

        	var holiday_type = 1;
			var date_start = $("#custom-start-date").val();
			var day_end = $("#custom-end-date").val();
        	var time_start = $("#custom-start-time").val();
        	var time_end = $("#custom-end-time").val();
        }

        $.ajax({

      		url: base_url+'setting/staff/Update-doctor-time-off',
		    type: 'POST',
		    data:{ doctorid:doctor_id, holidayid:Holiday_id, holidayType:holiday_type, dateStart:date_start, dayEnd:day_end, timeStart:time_start, timeEnd:time_end, note:note }

    	})
		.done(function(data) {

			$('#Time-off-Modal').modal('hide');
			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){

				$('#alert_box').css('display', 'none');
			   	$( "#staff-time_off-tab" ).trigger( "click" );

			}, 1000);

		});
		
		event.stopImmediatePropagation();
	    return false;
	});

}


function DeleteDoctorHolyday (){

	$(document).on('click', '#delete-doctor-time-off', function(event) {

		var Holiday_id = $('#h-doctor-holiday-id').val();

        $.ajax({

      		url: base_url+'setting/staff/Delete-doctor-time-off',
		    type: 'POST',
		    data:{ holidayid:Holiday_id }

    	})
		.done(function(data) {

			$('#Time-off-Modal').modal('hide');
			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){

				$('#alert_box').css('display', 'none');
			   	$( "#staff-time_off-tab" ).trigger( "click" );

			}, 1000);

		});

		event.stopImmediatePropagation();
	    return false;

	});

}