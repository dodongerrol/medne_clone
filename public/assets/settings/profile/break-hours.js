jQuery(document).ready( function ($) { 
    
    /*************** Module jquery Configuration *****************/
    $("[data-toggle='toggle']").bootstrapToggle('destroy')                 
    $("[data-toggle='toggle']").bootstrapToggle();
    
    $('.timepicker.profile-breakHours-time-from').timepicker({
        'timeFormat' : 'h:i A',
        'minTime'	 : '09:00:00',
        'maxTime'	 : '21:00:00'
    });

    $('.timepicker.profile-breakHours-time-to').timepicker({
        'timeFormat' : 'h:i A',
        'minTime'	 : '09:00:00',
        'maxTime'	 : '21:00:00'
    });


    /*************** Element behavior *****************/

    // Get Provider Operating Hours
    $('#clinic-breaks-tab').click(function () {
        $('#cover-spin').css('display', 'block');
        $.ajax({
            url: base_url+'clinic/getProviderBreakHours',
            type: 'get'
            }).done(function (response) {
                $('#cover-spin').css('display', 'none');

                if (response.data.length > 0) {
                     // Assign Data
                     let availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'],
                        availableDaysKeys = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
                        breakHoursData =  response.data;

                     for (let i = 0; i < breakHoursData.length; i++) {
                        let dayKey = availableDaysKeys.indexOf(breakHoursData[i]['day']); 
                        
                        $('#profile-breakHours-'+availableDays[dayKey]+'-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(breakHoursData[i]['start_time']);
                        $('#profile-breakHours-'+availableDays[dayKey]+'-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(breakHoursData[i]['end_time']);
                        $('#profile-breakHours-'+availableDays[dayKey]+'-div .profile-breakHours-chk_activate').bootstrapToggle('on');
                     }
                 } else {
                    $('#config_alert_box').css('display', 'block');
                    $('#config_alert_box').html('No record found.');

                    setTimeout(function() {
                        $('#config_alert_box').css('display', 'none');
                    }, 1000);
                 }
                

                
        });
    });

    // Button Behavior
    $(document).on('change', `#profile-breakHours-monday-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input, 
                                #profile-breakHours-monday-div input.profile-breakHours-timepicker.time-to.ui-timepicker-input`,function () {
                                    
		var mondayTimeFrom = $('#profile-breakHours-monday-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#profile-breakHours-monday-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val();
			
		// if already copied changes to other days
		if  (document.getElementById('profile-breakHours-copyTimetoAllBtn').style.display == 'none') {
			// Get monday Time values
				var mondayTimeFrom = $('#profile-breakHours-monday-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
				mondayTimeTo   = $('#profile-breakHours-monday-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val();
				
				// Set monday Time values to other days
		
			/* Set all toggle ON*/
            $('.profile-breakHours-chk_activate').bootstrapToggle('on');
            
            // Paste monday Date-from  and Date-to to other days

			var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

			for (var i = 0; i < availableDays.length; i++) {
				$('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(mondayTimeFrom);
				$('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(mondayTimeTo);
			}
			
		}
		
		if (mondayTimeFrom !== '' && mondayTimeTo !== '') {
			$('#profile-breakHours-copyTimetoAllBtn').prop('disabled', false);
		} else {
			$('#profile-breakHours-copyTimetoAllBtn').prop('disabled', true);
		}
    });
    
    /* Copy and Paste time to all days  */
	$('#profile-breakHours-copyTimetoAllBtn').click(function () {
		// Get monday Time values
		var mondayTimeFrom = $('#profile-breakHours-monday-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#profile-breakHours-monday-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val();
		
		// Set monday Time values to other days
		/* Set all toggle ON*/
        $('.profile-breakHours-chk_activate').bootstrapToggle('on');
        
		var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

		for (var i = 0; i < availableDays.length; i++) {
			$('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(mondayTimeFrom);
			$('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(mondayTimeTo);
		}

		/* Change Button text and add class */
		// $('#profile-breakHours-copyTimetoAllBtn').css('display', 'none');
		// $('#profile-breakHours-undoCopyTimetoAllBtn').css('display', 'block');
    });	
    
    /* Undo changes in every days */
	$('#profile-breakHours-undoCopyTimetoAllBtn').click(function () {
        
        var availableDays = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

		for (var i = 0; i < availableDays.length; i++) {

            /* Set all toggle OFF*/
		    $('#profile-breakHours-'+availableDays[i]+'-div .profile-breakHours-chk_activate').bootstrapToggle('off');
            
            // Set to default time
            $('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val('09:00 AM');
			$('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val('09:00 PM');
		}

		/* Change Button text and add class */
		$('#profile-breakHours-copyTimetoAllBtn').css('display', 'block');
		$('#profile-breakHours-undoCopyTimetoAllBtn').css('display', 'none');
    });	
    
    /* Save Operating Hours */
    $('#profile-breakHours-savebreakHours').click(function () {

        $('#config_alert_box').css('display', 'block');
        $('#config_alert_box').html('Updating records. Please wait...');
        
        var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            operatingAvailableDaysKey = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
            breakHours = [];
        
        for (let x = 0; x < availableDays.length; x++) {
            let chkValue = $('#profile-breakHours-'+availableDays[x]+'-div .profile-breakHours-chk_activate').prop('checked');
            if (chkValue) {
                breakHours.push({
                  	start_time: $('#profile-breakHours-'+availableDays[x]+'-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
					end_time:  $('#profile-breakHours-'+availableDays[x]+'-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(),
					day: operatingAvailableDaysKey[x],
					type: 3,
					updated_at: new Date().getFullYear(),
					created_at: new Date().getFullYear()
                });
            }
        }

        $.ajax({
            url: base_url+'clinic/updateProvidersDetail',
            type: 'PUT',
            data: {
                providersDetails: {
                    providersbreakHours: breakHours
                }
            }
            }).done(function (data) {
                $('#config_alert_box').html(data.message);

                // Set timeout
                setTimeout(function() {
                    $('#config_alert_box').css('display', 'none');
                }, 1000);
          });
        
    });

});