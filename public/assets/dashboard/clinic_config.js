jQuery(document).ready(function($) {

  	// var protocol = jQuery(location).attr('protocol');
  	// var hostname = jQuery(location).attr('hostname');
  	// var folderlocation = $(location).attr('pathname').split('/')[1];
  	// window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
// $('[data-toggle="tooltip"]').tooltip();
	window.base_url = window.location.origin + '/app/';
	window.dashboard_url = window.location.origin;
  	$("#config-clinic-type-list li a").click(function(){
	  	text = $(this).text();
	  	id = $(this).attr('id');

    	$('.clinic-speciality').val(text);
    	$('.clinic-speciality').attr('id', id);

  	});

  	$("#config-mobile-code-list li").click(function(){

	  	id = $(this).attr('id');

    	$('#con-mobile-code').text(id);
    	// $('.clinic-speciality').attr('id', id);

  	});

  	$(document).on('click', '#time-format-list li a', function(c) {

        text = $(this).text();
    	$('#con-time-format').val(text);

    });

  	$(document).on('keydown', '#con-mobile', function(c) {

        if (!(c.keyCode>=96 && c.keyCode<=105) && !(c.keyCode>=48 && c.keyCode<=57) && c.keyCode!=8 && c.keyCode!=9) {
            return false;
        }

    });

    $('#code-dropdown').on('shown.bs.dropdown', function () {
  
	    var $this = $(this);
	    // attach key listener when dropdown is shown
	    $(document).keypress(function(e){
	      
	      // get the key that was pressed
	      var key = String.fromCharCode(e.which);
	      // look at all of the items to find a first char match
	      $this.find("li").each(function(idx,item){
	        $(item).addClass("hide"); // clear previous active item
	        $(item).removeClass("show");

	        if ($(item).text().charAt(0).toLowerCase() == key) {
	          // set the item to selected (active)
	          $(item).addClass("show");
	          $(item).removeClass("hide");
	        }
	        else{
	            $(item).addClass("hide");
	            $(item).removeClass("show");
	        }
	      });
	      
	    });
	  
	});

	// unbind key event when dropdown is hidden
	$('#code-dropdown').on('hide.bs.dropdown', function () {

	    var $this = $(this);

	    $this.find("li").each(function(idx,item){

	        $(item).addClass("show");
	        $(item).removeClass("hide");
	    });
	    
	    $(document).unbind("keypress");

	});



// -------------------------------------------------------------------------


	$("#hour-back").click(function(){

    	$('#step-2').removeClass('active');
      	$('#setupHours').removeClass('active');
      	$('#step-1').addClass('active');
      	$('#setupHome').addClass('active in');

   		$('#lbl-step-1').removeClass('glyphicon glyphicon-ok');
      	$('#lbl-step-1').html('1');
      	$('#lbl-step-2').css("background", "rgb(117, 214, 247)");

  	});


	$("#hour-next").click(function(){
		
		$('#config_alert_box').css('display', 'none');
		$('#step-2').removeClass('active');
		$('#setupHours').removeClass('active');
		$('#step-3').addClass('active');
		$('#setupBreakHours').addClass('active in');
		$('#lbl-step-2').addClass('glyphicon glyphicon-ok');
		$('#lbl-step-2').html('');
		$('#lbl-step-2').css("position", "absolute");
		$('#lbl-step-2').css("background", "#2AA4D8");
		$('#lbl-step-3').css("background", "#2AA4D8");

		$('.timepicker.breakTime-from').timepicker({
			'timeFormat' : 'h:i A',
			'minTime'	 : '09:00:00',
			'maxTime'	 : '20:00:00'
		});

		$('.timepicker.breakTime-to').timepicker({
			'timeFormat' : 'h:i A',
			'minTime'	 : '09:15:00',
			'maxTime'	 : '21:00:00'
		});

  	});

// ---------------------------------------------------------------------------------


	$("#breakHour-back").click(function(){

    	$('#step-3').removeClass('active');
      	$('#setupBreakHours').removeClass('active');
      	$('#step-2').addClass('active');
      	$('#setupHours').addClass('active in');

   		$('#lbl-step-2').removeClass('glyphicon glyphicon-ok');
      	$('#lbl-step-2').html('2');
      	$('#lbl-step-3').css("background", "rgb(117, 214, 247)");

  	});

	$("#breakHour-next").click(function(){
		$('#config_alert_box').css('display', 'block');
		$('#config_alert_box').html('Updating records. Please wait...');

		// get All modal value and populate it to a array holder
		var providersPhone = $('#con-mobile').val(),
			providersName = $('#con-clinic-name').val(),
			operatingAvailableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'],
			operatingAvailableDaysKey = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'publicHoliday'],
			breakAvailableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'],
			breakAvailableDaysKey = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'publicHoliday'],
			providersOperatingHours = [],
			providersBreakHours = [];
			
		// For Operating hours
		for (let i = 0; i < operatingAvailableDays.length; i++) {
			if ($('#'+operatingAvailableDays[i]+'-div .chk_activate').prop('checked')) {
				providersOperatingHours.push({
					StartTime: $('#'+operatingAvailableDays[i]+'-div input.timepicker.time-from.ui-timepicker-input').val(),
					EndTime:  $('#'+operatingAvailableDays[i]+'-div input.timepicker.time-to.ui-timepicker-input').val(),
					[operatingAvailableDaysKey[i]]: 1,
					updated_at: new Date().getFullYear(),
					created_at: new Date().getFullYear()
				});
			}
		}

		// For Break Hours
		for (let i = 0; i < breakAvailableDays.length; i++) {
			if ($('#'+breakAvailableDays[i]+'-div .breakChk_activate').prop('checked')) {
				providersBreakHours.push({
					start_time: $('#'+breakAvailableDays[i]+'-div input.timepicker.breakTime-from.ui-timepicker-input').val(),
					end_time:  $('#'+breakAvailableDays[i]+'-div input.timepicker.breakTime-to.ui-timepicker-input').val(),
					day: breakAvailableDaysKey[i],
					type: 3,
					clinic_id: $('#clinicID').val(),
					updated_at: new Date().getFullYear(),
					created_at: new Date().getFullYear()
				});
			}
		}
		
		// Populate data
		$.ajax({
			url: base_url+'clinic/updateProvidersDetail',
			type: 'PUT',
			data: {
				providersDetails: {
					providersInfo: {
						Phone:	providersPhone,
						Name:	providersName,
						configure: 1
					},
					providersOperatingHours: providersOperatingHours,
					providersBreakHours: providersBreakHours
				},
				provider_id: $('#clinicID').val()
			}
	  	}).done(function (data) {
			$('#config_alert_box').html(data.message);
			setTimeout(function() {
				$('#config_alert_box').css('display', 'none');
				$('#step-3').removeClass('active');
				$('#setupBreakHours').removeClass('active');
				$('#step-5').addClass('active');
				$('#setupDone').addClass('active in');

				$('#lbl-step-3').addClass('glyphicon glyphicon-ok');
				$('#lbl-step-3').html('');
				$('#lbl-step-3').css("position", "absolute");
				$('#lbl-step-3').css("background", "#2AA4D8");

				$('#lbl-step-5').addClass('glyphicon glyphicon-ok');
				$('#lbl-step-5').html('');
				$('#lbl-step-5').css("position", "absolute");
				$('#lbl-step-5').css("background", "#2AA4D8");
			}, 1000);
		});
  	});


  	$(document).on('click', '#config-doc-add-btn', function(event) {

		var name = $('#con-doctor-name').val();
		var email = $('#con-doctor-email').val();
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

		if (name=='') {
			$('#con-doctor-name').addClass('con-input-error');
			return false;
		} else {
			$('#con-doctor-name').removeClass('con-input-error');
		}

		if (email == '' || !re.test(email)) {
			$('#con-doctor-email').addClass('con-input-error');
			return false;
		}
		 else {
		 	$('#con-doctor-email').removeClass('con-input-error');
		 }

		$.ajax({
	      url: base_url+'calendar/Add-clinic-doctor-details',
	      type: 'POST',
	      data:{ name:name, email:email }
	    })
	    .done(function(data) {

	    	$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');
				
				$( "#hour-next" ).trigger( "click" );
	    		$('#con-doctor-name').val('');
	    		$('#con-doctor-email').val('');

			}, 500);


	    });

	});


	$(document).on('click', '#clinic-doctors-panel .glyphicon-remove', function(event) {

		var doctor_id = $(this).attr('id');
		// alert (doctor_id);

		$.ajax({

      		url: base_url+'setting/staff/Delete-doctor',
		    type: 'POST',
		    data:{ doctorid:doctor_id }

    	})
		.done(function(data) {

			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

				$( "#hour-next" ).trigger( "click" );

			}, 500);

			
		});

	});


// ----------------------------------------------------------------------------------------


	$("#service-back").click(function(){

    	$('#step-4').removeClass('active');
      	$('#setupService').removeClass('active');
      	$('#step-3').addClass('active');
      	$('#setupDoctor').addClass('active in');

   		$('#lbl-step-3').removeClass('glyphicon glyphicon-ok');
      	$('#lbl-step-3').html('3');
      	$('#lbl-step-4').css("background", "rgb(117, 214, 247)");

  	});

	$("#service-next").click(function(){

		service = $('#service-count').val();

		if (service == 0 ) {
			alert('Add At Least One Service !');
			return false;
		}

		$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

				$('#step-4').removeClass('active');
	      		$('#setupService').removeClass('active');
	      		$('#step-5').addClass('active');
	      		$('#setupDone').addClass('active in');

	      		$('#lbl-step-4').addClass('glyphicon glyphicon-ok');
	      		$('#lbl-step-4').html('');
	      		$('#lbl-step-4').css("position", "absolute");
	      		$('#lbl-step-4').css("background", "#2AA4D8");

	      		$('#lbl-step-5').addClass('glyphicon glyphicon-ok');
	      		$('#lbl-step-5').html('');
	      		$('#lbl-step-5').css("position", "absolute");
	      		$('#lbl-step-5').css("background", "#2AA4D8");

			}, 500);

    		

  	});


  	$(document).on('click', '#service-add-btn-config', function(event) {

  		var name = $('#con-service-name').val();
  		var time = $('#con-service-time').val();
  		var time_format = $('#con-time-format').val();
  		var cost = $('#con-service-cost').val();
  		doctorid = [];

  		x = 0;

  		$('.service-doc-list').each(function () {

  			if($(this).is(":checked")) {

  				doctorid[x] = this.id;
  				x++;
        	}
        	
    	});

    	var valid = /[0-9 -()+]+$/;

		if (name=='') {
			$('#con-service-name').addClass('con-input-error');
			return false;
		} else {
			$('#con-service-name').removeClass('con-input-error');
		}
		if (time=='' || !valid.test(time)) {

			$('#con-service-time').addClass('con-input-error');
			return false;
		} else {

			$('#con-service-time').removeClass('con-input-error');
		}
		if (cost=='' || !valid.test(cost)) {

			$('#con-service-cost').addClass('con-input-error');
			return false;
		} else {

			$('#con-service-cost').removeClass('con-input-error');
		}
		if (doctorid=='') {
			alert('Select At Least One Doctor !')
			return false;
		}


		if (time_format == 'Hours' ){
		
			time = Math.floor( $('#con-service-time').val() * 60);

		}

    	$.ajax({
          url: base_url+'calendar/save-clinic-services',
          type: 'POST',
          // dataType: 'json',
          data: { name:name, time: time, cost:cost, doctorid:doctorid },
        })
        .done(function(data) {

        	$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

				$( "#doctor-next" ).trigger( "click" );

			}, 500);

        });

	});


	$(document).on('click', '#clinic-service-panel .glyphicon-remove', function(event) {

		var service_id = $(this).attr('id');
		// alert (service_id);

		$.ajax({

      		url: base_url+'calendar/delete-clinic-services',
		    type: 'POST',
		    data:{ id:service_id }

    	})
		.done(function(data) {

			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

				$( "#doctor-next" ).trigger( "click" );

			}, 500);

			
		});

	});



// ----------------------------------------------------------------------------------------


	$("#config-back").click(function(){

    	$('#step-5').removeClass('active');
      	$('#setupDone').removeClass('active');
      	$('#step-3').addClass('active');
      	$('#setupBreakHours').addClass('active in');

   		$('#lbl-step-3').removeClass('glyphicon glyphicon-ok');
      	$('#lbl-step-3').html('3');

      	$('#lbl-step-5').removeClass('glyphicon glyphicon-ok');
      	$('#lbl-step-5').html('5');
      	$('#lbl-step-5').css("background", "rgb(117, 214, 247)");

  	});


  	$("#config-done").click(function(){

  		name = $('#con-clinic-name').val();
	  	speciality = $('.clinic-speciality').attr('id');
	  	code = $('#con-mobile-code').text();
	  	mobile = $('#con-mobile').val();
	  	phone = code + mobile;
	  	Phonecode = code;
	  	
	  	$.ajax({
          url: base_url+'calendar/save-clinic-details',
          type: 'POST',
          // dataType: 'json',
          data: { clinicname: name, speciality:speciality, mobile:phone, Phonecode:Phonecode },
        })
        .done(function(data) {

        	$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

				window.location.replace(base_url + "clinic/appointment-home-view");

			}, 500);

        	
        });

  	});


	//  Added/Modify functionality by Stephen
	
	/****************************Operating Hours**********************************/

	$(document).on('change', 'div#setupHours .timepicker.time-to', function (time) {
		const timeselected = time.currentTarget.value,
				parentElement = this.parentElement.parentElement.id.split('-div')[0],
				fromTime = $('div#setupHours #'+parentElement+'-div .timepicker.time-from').val();
		if (new Date().getTime(timeselected) <= new Date().getTime(fromTime)) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selected!');
			$('div#setupHours #'+parentElement+'-div .timepicker.time-to').val('09:00 PM');
			setTimeout(function () {
				$('#config_alert_box').css('display', 'none');
				$('#config_alert_box').css('color', 'black');
			}, 1000);
		}
		
	});

	$(document).on('change', '#monday-div input.timepicker.time-from.ui-timepicker-input, #monday-div input.timepicker.time-to.ui-timepicker-input',function () {
		var mondayTimeFrom = $('#monday-div input.timepicker.time-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#monday-div input.timepicker.time-to.ui-timepicker-input').val();
			
		// if already copied changes to other days
		if  (document.getElementById('copyTimetoAllBtn').style.display == 'none') {
			// Get monday Time values
				var mondayTimeFrom = $('#monday-div input.timepicker.time-from.ui-timepicker-input').val(),
				mondayTimeTo   = $('#monday-div input.timepicker.time-to.ui-timepicker-input').val();
				
				// Set monday Time values to other days
		
			/* Set all toggle ON*/
			$('.chk_activate').bootstrapToggle('on');
			var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

			for (var i = 0; i < availableDays.length; i++) {
				$('#'+availableDays[i]+'-div input.timepicker.time-from.ui-timepicker-input').val(mondayTimeFrom);
				$('#'+availableDays[i]+'-div input.timepicker.time-to.ui-timepicker-input').val(mondayTimeTo);
			}
			
		}
		
		if (mondayTimeFrom !== '' && mondayTimeTo !== '') {
			$('#copyTimetoAllBtn').prop('disabled', false);
		} else {
			$('#copyTimetoAllBtn').prop('disabled', true);
		}
	});
	
	/* Copy and Paste time to all days  */
	$('#copyTimetoAllBtn').click(function () {
		// Get monday Time values
		var mondayTimeFrom = $('#monday-div input.timepicker.time-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#monday-div input.timepicker.time-to.ui-timepicker-input').val();
		
		// Set monday Time values to other days
		/* Set all toggle ON*/
		$('.chk_activate').bootstrapToggle('on');
		var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

		for (var i = 0; i < availableDays.length; i++) {
			$('#'+availableDays[i]+'-div input.timepicker.time-from.ui-timepicker-input').val(mondayTimeFrom);
			$('#'+availableDays[i]+'-div input.timepicker.time-to.ui-timepicker-input').val(mondayTimeTo);
		}
	});	

	/* Undo changes in every days */
	$('#undoCopyTimetoAllBtn').click(function () {
		/* Set all toggle OFF*/
		$('#tuesday-div .chk_activate').bootstrapToggle('off');
		$('#wednesday-div .chk_activate').bootstrapToggle('off');
		$('#thursday-div .chk_activate').bootstrapToggle('off');
		$('#friday-div .chk_activate').bootstrapToggle('off');
		$('#saturday-div .chk_activate').bootstrapToggle('off');
		$('#sunday-div .chk_activate').bootstrapToggle('off');
		$('#publicHoliday-div .chk_activate').bootstrapToggle('off');

		var availableDays = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

		for (var i = 0; i < availableDays.length; i++) {
			$('#'+availableDays[i]+'-div input.timepicker.time-from.ui-timepicker-input').val('09:00 AM');
			$('#'+availableDays[i]+'-div input.timepicker.time-to.ui-timepicker-input').val('09:00 PM');
		}

		/* Change Button text and add class */
		$('#copyTimetoAllBtn').css('display', 'block');
		$('#undoCopyTimetoAllBtn').css('display', 'none');
	});	

	/****************************Break Hours**********************************/
	// Validate Time-From and Time-to value
	$(document).on('change', 'div#setupBreakHours .breakTime-to', function (time) {
		const timeselected = time.currentTarget.value,
				parentElement = this.parentElement.parentElement.id.split('-div')[0],
				fromTime = $('div#setupBreakHours #'+parentElement+'-div .breakTime-from').val();

		if (new Date().getTime(timeselected) <= new Date().getTime(fromTime)) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selected!');
			$('div#setupBreakHours #'+parentElement+'-div .breakTime-to').val('09:00 PM');
			
			// For copy time to all button
			if (parentElement == 'monday') {
				$('div#setupBreakHours #'+parentElement+'-div #copyTimetoAllBtnBreak').css('display', 'none');
			}
		}
		
	});
	// Show break hours time
	$(document).on('click', 
						`#monday-addBreak, #tuesday-addBreak, #wednesday-addBreak,
						#thursday-addBreak, #friday-addBreak, #saturday-addBreak,
						#sunday-addBreak, #publicHoliday-addBreak`, function () {
		const parentName = this.id.split('-addBreak')[0];
							
		$('div#setupBreakHours .'+parentName+'-addBreakBtn').css('display', 'none');
		$('div#setupBreakHours #'+parentName+'-div .col-md-1.con-detail-lbl').css('display', 'inline-block');
		$('div#setupBreakHours #'+parentName+'-div .toggle').css('display', 'inline-block');
		$('div#setupBreakHours #'+parentName+'-div .toggle .breakChk_activate').bootstrapToggle('on');
		$('div#setupBreakHours #'+parentName+'-div .timepicker').css('display', 'inline-block');
		
		// For copy time to all button
		if (parentName == 'monday') {
			$('div#setupBreakHours #'+parentName+'-div #copyTimetoAllBtnBreak').css('display', 'inline-block');
		}
	});

	// Toggle
	$(document).on('change', `div#setupBreakHours .toggle`, function (element) {
		
		const parentName = this.firstElementChild.className.split(' ')[0];
		
		if (!$('.'+parentName+'.breakChk_activate').prop('checked')) {
			if (parentName == 'monday') {
				$('div#setupBreakHours #'+parentName+'-div #copyTimetoAllBtnBreak').css('display', 'none');
			}
			$('div#setupBreakHours .'+parentName+'-addBreakBtn').css('display', 'inline-block');
			$('div#setupBreakHours #'+parentName+'-div .col-md-1.con-detail-lbl').css('display', 'none');
			$('div#setupBreakHours #'+parentName+'-div .toggle').css('display', 'none');
			$('div#setupBreakHours #'+parentName+'-div .timepicker').css('display', 'none');
		}
	});

	$(document).on('change', '#monday-div input.timepicker.breakTime-from.ui-timepicker-input, #monday-div input.timepicker.breakTime-to.ui-timepicker-input',function () {
		var mondayTimeFrom = $('#monday-div input.timepicker.breakTime-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#monday-div input.timepicker.breakTime-to.ui-timepicker-input').val();

		// if already copied changes to other days
		if  (document.getElementById('copyTimetoAllBtnBreak').style.display == 'none') {
			// Get monday Time values
				var mondayTimeFrom = $('#monday-div input.timepicker.breakTime-from.ui-timepicker-input').val(),
				mondayTimeTo   = $('#monday-div input.timepicker.breakTime-to.ui-timepicker-input').val();
			
				// Set monday Time values to other days
		
			/* Set all toggle ON*/
			$('.breakChk_activate').bootstrapToggle('on');
			var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

			for (var i = 0; i < availableDays.length; i++) {
				$('#'+availableDays[i]+'-div input.timepicker.breakTime-from.ui-timepicker-input').val(mondayTimeFrom);
				$('#'+availableDays[i]+'-div input.timepicker.breakTime-to.ui-timepicker-input').val(mondayTimeTo);
			}
			
		}
		// For Button.
		
		if (mondayTimeFrom !== '' && mondayTimeTo !== '') {
			$('#copyTimetoAllBtnBreak').prop('disabled', false);
		} else {
			$('#copyTimetoAllBtnBreak').prop('disabled', true);
		}
	});

	/* Copy and Paste time to all days  */
	$('#copyTimetoAllBtnBreak').click(function () {
		// Get monday Time values
		var mondayTimeFrom = $('#monday-div input.timepicker.breakTime-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#monday-div input.timepicker.breakTime-to.ui-timepicker-input').val();
		
		/* Set monday Time values to other days */
		
		//Set all toggle ON
		$('.breakChk_activate').bootstrapToggle('on');
		var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

		for (var i = 0; i < availableDays.length; i++) {
			// Display visible all elements needed.
			$('div#setupBreakHours .'+availableDays[i]+'-addBreakBtn').css('display', 'none');
			$('div#setupBreakHours #'+availableDays[i]+'-div .col-md-1.con-detail-lbl').css('display', 'inline-block');
			$('div#setupBreakHours #'+availableDays[i]+'-div .toggle').css('display', 'inline-block');
			$('div#setupBreakHours #'+availableDays[i]+'-div .timepicker').css('display', 'inline-block');

			// Set value to time-From and time-To
			$('#'+availableDays[i]+'-div input.timepicker.breakTime-from.ui-timepicker-input').val(mondayTimeFrom);
			$('#'+availableDays[i]+'-div input.timepicker.breakTime-to.ui-timepicker-input').val(mondayTimeTo);
		}

	});	

	$("#welcome-next").click(function(){
		$('#config_alert_box').css('display', 'none');
		$('#step-1').removeClass('active');
		$('#setupHome').removeClass('active');
		$('#step-2').addClass('active');
		$('#setupHours').addClass('active in');
		$('#lbl-step-1').addClass('glyphicon glyphicon-ok');
		$('#lbl-step-1').html('');
		$('#lbl-step-1').css("position", "absolute");
		$('#lbl-step-1').css("background", "#2AA4D8");
		$('#lbl-step-2').css("background", "#2AA4D8");

		$('.timepicker.time-from').timepicker({
			'timeFormat' : 'h:i A',
			'minTime'	 : '09:00:00',
			'maxTime'	 : '20:00:00'
		});

		$('.timepicker.time-to').timepicker({
			'timeFormat' : 'h:i A',
			'minTime'	 : '09:15:00',
			'maxTime'	 : '21:00:00'
		});
	  });
});
