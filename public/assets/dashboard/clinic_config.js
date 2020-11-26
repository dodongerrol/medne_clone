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
			operatingAvailableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
			operatingAvailableDaysKey = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
			breakAvailableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
			breakAvailableDaysKey = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
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
					created_at: new Date().getFullYear(),
					active: 1
				});
			} else {
				providersOperatingHours.push({
					StartTime: $('#'+operatingAvailableDays[i]+'-div input.timepicker.time-from.ui-timepicker-input').val(),
					EndTime:  $('#'+operatingAvailableDays[i]+'-div input.timepicker.time-to.ui-timepicker-input').val(),
					[operatingAvailableDaysKey[i]]: 1,
					updated_at: new Date().getFullYear(),
					created_at: new Date().getFullYear(),
					active: 0
				});
			}
		}

		// For Break Hours
		for (let x = 0; x < breakAvailableDays.length; x++) {
            for (let i = 0; i < 5; i++) {
                let chkValue = $('div#setupBreakHours #'+breakAvailableDays[x]+'-mainCollapsibleDiv .row.'+breakAvailableDays[x]+i+' .profile-breakHours-chk_activate').prop('checked');
                
                if (chkValue) {
                    providersBreakHours.push({
                        start_time: $('div#setupBreakHours #'+breakAvailableDays[x]+'-mainCollapsibleDiv .row.'+breakAvailableDays[x]+i+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
                        end_time:  $('div#setupBreakHours #'+breakAvailableDays[x]+'-mainCollapsibleDiv .row.'+breakAvailableDays[x]+i+' input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(),
                        day: breakAvailableDaysKey[x]+i,
                        type: 3,
                        updated_at: new Date().getFullYear(),
						created_at: new Date().getFullYear(),
						active: 1
                    });
                } else {
					providersBreakHours.push({
                        start_time: $('div#setupBreakHours #'+breakAvailableDays[x]+'-mainCollapsibleDiv .row.'+breakAvailableDays[x]+i+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
                        end_time:  $('div#setupBreakHours #'+breakAvailableDays[x]+'-mainCollapsibleDiv .row.'+breakAvailableDays[x]+i+' input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(),
                        day: breakAvailableDaysKey[x]+i,
                        type: 3,
                        updated_at: new Date().getFullYear(),
						created_at: new Date().getFullYear(),
						active: 0
                    });
				}
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
		const 	parentElement = this.parentElement.parentElement.id.split('-div')[0],
				fromTime = $('div#setupHours #'+parentElement+'-div .timepicker.time-from').val(),
                timeselected = $('div#setupHours #'+parentElement+'-div .timepicker.time-to').val(),
                fullYear = new Date().getFullYear(),
                month = ("0" + (new Date().getMonth() + 1)).slice(-2),
                day = new Date().getDate();
			
		if (new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime() <= new Date(month+'-'+day+'-'+fullYear+' '+fromTime).getTime()) {
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

	$(document).on('change', 'div#setupHours .timepicker.time-from', function (time) {
		const 	parentElement = this.parentElement.parentElement.id.split('-div')[0],
				fromTime = $('div#setupHours #'+parentElement+'-div .timepicker.time-from').val(),
                timeselected = $('div#setupHours #'+parentElement+'-div .timepicker.time-to').val(),
                fullYear = new Date().getFullYear(),
                month = ("0" + (new Date().getMonth() + 1)).slice(-2),
                day = new Date().getDate();
				
		if (new Date(month+'-'+day+'-'+fullYear+' '+fromTime).getTime() >= new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime()) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selecteds!');
			$('div#setupHours #'+parentElement+'-div .timepicker.time-from').val('09:00 PM');
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
	  $(document).on('change', 'div#setupBreakHours .timepicker.profile-breakHours-time-to', function () {
        const   rowIndexClass = this.parentElement.parentElement.className.split('row ')[1],
                parentElement = rowIndexClass.replace(/\d+/g,''),
                fromTime = $('div#setupBreakHours #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-from').val(),
                timeselected = $('div#setupBreakHours #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-to').val(),
                fullYear = new Date().getFullYear(),
                month = ("0" + (new Date().getMonth() + 1)).slice(-2),
                day = new Date().getDate();
           
		if (new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime() <= new Date(month+'-'+day+'-'+fullYear+' '+fromTime).getTime()) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selected!');
			$('div#setupBreakHours #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-to').val('02:00 PM');
			setTimeout(function () {
				$('#config_alert_box').css('display', 'none');
				$('#config_alert_box').css('color', 'black');
			}, 1000);
		}
		
    });

    $(document).on('change', 'div#setupBreakHours .timepicker.profile-breakHours-time-from', function () {
        const   rowIndexClass = this.parentElement.parentElement.className.split('row ')[1],
                parentElement = rowIndexClass.replace(/\d+/g,''),
                fromTime = $('div#setupBreakHours #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-to').val(),
                timeselected = $('div#setupBreakHours #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-from').val(),
                fullYear = new Date().getFullYear(),
                month = ("0" + (new Date().getMonth() + 1)).slice(-2),
                day = new Date().getDate();
           
		if (new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime() >= new Date(month+'-'+day+'-'+fullYear+' '+fromTime).getTime()) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selected!');
			$('div#setupBreakHours #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-from').val('01:00 PM');
			setTimeout(function () {
				$('#config_alert_box').css('display', 'none');
				$('#config_alert_box').css('color', 'black');
			}, 1000);
		}
		
    });
    
    
    // Show hours for breaks
	$(document).on('click', 
                `#monday-addBreak, #tuesday-addBreak, #wednesday-addBreak,
                #thursday-addBreak, #friday-addBreak, #saturday-addBreak,
                #sunday-addBreak, #publicHoliday-addBreak`, function () {

            const parentName = this.id.split('-addBreak')[0];
            
            /*
                All process are in sequence.
            */
               
            // Collapse show
            if (!$('#'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'0').is(':visible')) {
                // Stage 1: Set Toggle ON
                $('#setupBreakHours .row.'+parentName+'0 .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Stage 2:  Collapse Show
                $('#setupBreakHours #'+parentName+'-div').collapse('show');

                // Stage 3: CSS, Remove element and Class changes
                $('#setupBreakHours .day-label-'+parentName+'').attr('class','day-label-'+parentName+'');
                $('#setupBreakHours .day-label-'+parentName+'').css('clear', '');
                $('#setupBreakHours .day-label-'+parentName+'').css('margin-left', '3%');
                $('#setupBreakHours div#'+parentName+'-div .card-body').css('margin-left', '25%');
                $('#setupBreakHours .'+parentName+'-addBreakBtn').remove();

                // Stage 4: Element insertion
                $('#setupBreakHours #'+parentName+'-div').after(`
                <div class="`+parentName+`-addBreakBtn">
                    <a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='`+parentName+`-addBreak'>
                        <span class="glyphicon glyphicon-plus"></span> Add Break
                    </a>
                </div>`);
                
                // Stage 5: Add Css
                $('#setupBreakHours .'+parentName+'-addBreakBtn').css('margin-left', '25%');
                $('#setupBreakHours .'+parentName+'-addBreakBtn').css('padding-top', '1%');
                $('#setupBreakHours .'+parentName+'-addBreakBtn').css('padding-bottom', '1%');

            } else if (!$('#setupBreakHours #'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'1').is(':visible')) {
                 // Set Toggle ON
                $('#setupBreakHours .row.'+parentName+'1 .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Show row visibility
                $('div#setupBreakHours .row.'+parentName+'1').css('display', 'block');

                // Disable row 0
                $('#setupBreakHours .'+parentName+'0.profile-breakHours-chk_activate').prop('disabled',  true);

            } else if (!$('#setupBreakHours #'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'2').is(':visible')) {
                 // Set Toggle ON
                $('#setupBreakHours .row.'+parentName+'2 .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Show row visibility
                $('div#setupBreakHours .row.'+parentName+'2').css('display', 'block');

            } else if (!$('#'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'3').is(':visible')) {
                 // Set Toggle ON
                $('#setupBreakHours .row.'+parentName+'3 .profile-breakHours-chk_activate').bootstrapToggle('on');

                // Show row visibility
                $('div#setupBreakHours .row.'+parentName+'3').css('display', 'block');

            } else if (!$('#setupBreakHours #'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'4').is(':visible')) {
                // Set Toggle ON
                $('#setupBreakHours .row.'+parentName+'4 .profile-breakHours-chk_activate').bootstrapToggle('on');

                // Show row visibility
                $('div#setupBreakHours .row.'+parentName+'4').css('display', 'block');

            }
            
    });

    // Toggle for breaks
    $(document).on('change', `div#setupBreakHours .toggle`, function (element) {

            const parentName = this.firstElementChild.className.split(' ')[0];
           
            if (!$('div#setupBreakHours .row.'+parentName+' .profile-breakHours-chk_activate').prop('checked')) {
                if (parentName.indexOf('0') > 0 
                    && $('div#setupBreakHours .row.'+parentName).is(':visible') 
                    && !$('div#setupBreakHours .row.'+parentName.replace(/\d+/g,'')+'1').is(':visible')) {
                
                    // Stage 1:  Collapse Show
                    $('div#setupBreakHours .row.'+parentName).css('display', 'none');
                    $('div#setupBreakHours #'+parentName.replace(/\d+/g,'')+'-div').collapse('hide');
                    $('div#setupBreakHours .row.'+parentName).css('display', 'block');
                
                    // Stage 2: CSS, Remove element and Class changes
                    $('div#setupBreakHours .'+parentName.replace(/\d+/g,'')+'-addBreakBtn').remove();
                    $('div#setupBreakHours .day-label-'+parentName.replace(/\d+/g,'')+'').attr('class','day-label-'+parentName.replace(/\d+/g,'')+' col-md-2');
                    $('div#setupBreakHours .day-label-'+parentName.replace(/\d+/g,'')+'').css('clear', 'both');

                    // Stage 3: Undo changes in button
                    $('div#setupBreakHours .day-label-'+parentName.replace(/\d+/g,'')+'').after(`
                    <div class="col-md-3 `+parentName.replace(/\d+/g,'')+`-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
                        <a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='`+parentName.replace(/\d+/g,'')+`-addBreak'>
                        <span class="glyphicon glyphicon-plus"></span>Add Break
                        </a>
                    </div>`);

                    // Stage 4: Removed Css
                    $('div#setupBreakHours .'+parentName+'-addBreakBtn').css('margin-left', '');
                    $('div#setupBreakHours .'+parentName+'-addBreakBtn').css('padding-top', '');
                    $('div#setupBreakHours .'+parentName+'-addBreakBtn').css('padding-bottom', '');
                    $('div#setupBreakHours .day-label-'+parentName.replace(/\d+/g,'')+'').css('margin-left', '');
                } else {
                    if ($('div#setupBreakHours .row.'+parentName).is(':visible')) {
                        if (parentName.indexOf('1') > 0 ) {
                            $('div#setupBreakHours .'+parentName.replace(/\d+/g,'')+'0.profile-breakHours-chk_activate').prop('disabled',  false);
                        }
                        $('div#setupBreakHours .row.'+parentName).css('display', 'none');
                    }
                }
            }
	});
	
	 /* Copy and Paste time to all days  */
	 $('div#setupBreakHours #profile-breakHours-copyTimetoAllBtn').click(function () {
       // Get number of time set on Monday
		let numberOfTimeSet = 0,
			timeArray = [];

        for (let j = 0; j < 5; j++) {
			let chkValue = $('div#setupBreakHours #monday-mainCollapsibleDiv .row.monday'+j+' .profile-breakHours-chk_activate').prop('checked');
            if (chkValue) {
				numberOfTimeSet += 1;
				timeArray.push({
                    'mondayTimeFrom': $('div#setupBreakHours #monday-mainCollapsibleDiv .monday'+j+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
                    'mondayTimeTo': $('div#setupBreakHours #monday-mainCollapsibleDiv .monday'+j+' input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val()
                });
            }
		}
		
		// Set monday Time values to other days
	
		var availableDays = ['monday','tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];
        
        for (let i = 0; i < availableDays.length; i++) {
			if (!$('div#setupBreakHours #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+'0').is(':visible')) {
				// Trigger click event
				$('div#setupBreakHours #'+availableDays[i]+'-addBreak').click();
			}

            for (let x = xStartingCnt; x < 5; x++) {
				// Display Block
				$('div#setupBreakHours #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x).css('display', 'block');

                // Set Toggle ON
                $('div#setupBreakHours #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x+' .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Set time
                $('div#setupBreakHours #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(mondayTimeFrom);
                $('div#setupBreakHours #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x+'  input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(mondayTimeTo);
            }
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
			'timeFormat' : 'h:i A'
		});

		$('.timepicker.time-to').timepicker({
			'timeFormat' : 'h:i A'
		});
	  });
});
