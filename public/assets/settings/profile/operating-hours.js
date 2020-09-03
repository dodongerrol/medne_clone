jQuery(document).ready( function ($) { 
    
    /*************** Module jquery Configuration *****************/
    $("[data-toggle='toggle']").bootstrapToggle('destroy')                 
    $("[data-toggle='toggle']").bootstrapToggle();
    
    $('.timepicker.profile-operatingHours-time-from').timepicker({
        'timeFormat' : 'h:i A',
        'minTime'	 : '09:00:00',
        'maxTime'	 : '21:00:00'
    });

    $('.timepicker.profile-operatingHours-time-to').timepicker({
        'timeFormat' : 'h:i A',
        'minTime'	 : '09:00:00',
        'maxTime'	 : '21:00:00'
    });


    /*************** Element behavior *****************/

    // Get Provider Operating Hours
    $('#clinic-hours, #clinic-hours-tab').click(function () {
        $('#cover-spin').css('display', 'block');
        $.ajax({
            url: base_url+'clinic/getProviderOperatingHours',
            type: 'get'
            }).done(function (response) {
                $('#cover-spin').css('display', 'none');

                // Assign Data
                let availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'],
                    availableDaysKeys = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'publicHoliday'];

                for (let i = 0; i < availableDays.length; i++) {

                    // Filter Data in there are active status for specific day
                    selectedChk = response.data.filter( function(item) {
                        return item[availableDaysKeys[i]];
                    });
                    

                    // Populate Data
                    if (selectedChk.length > 0) {
                        $('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val(selectedChk[0]['StartTime']);
                        $('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val(selectedChk[0]['EndTime']);
                        $('#profile-operatingHours-'+availableDays[i]+'-div .profile-operatingHours-chk_activate').bootstrapToggle('on');
                    }
                }
        });
    });

    // Button Behavior
    $(document).on('change', `#profile-operatingHours-monday-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input, 
                                #profile-operatingHours-monday-div input.profile-operatingHours-timepicker.time-to.ui-timepicker-input`,function () {
                                    
		var mondayTimeFrom = $('#profile-operatingHours-monday-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#profile-operatingHours-monday-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val();
			
		// if already copied changes to other days
		if  (document.getElementById('profile-operatingHours-copyTimetoAllBtn').style.display == 'none') {
			// Get monday Time values
				var mondayTimeFrom = $('#profile-operatingHours-monday-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val(),
				mondayTimeTo   = $('#profile-operatingHours-monday-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val();
				
				// Set monday Time values to other days
		
			/* Set all toggle ON*/
            $('.profile-operatingHours-chk_activate').bootstrapToggle('on');
            
            // Paste monday Date-from  and Date-to to other days

			var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

			for (var i = 0; i < availableDays.length; i++) {
				$('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val(mondayTimeFrom);
				$('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val(mondayTimeTo);
			}
			
		}
		
		if (mondayTimeFrom !== '' && mondayTimeTo !== '') {
			$('#profile-operatingHours-copyTimetoAllBtn').prop('disabled', false);
		} else {
			$('#profile-operatingHours-copyTimetoAllBtn').prop('disabled', true);
		}
    });
    
    /* Copy and Paste time to all days  */
	$('#profile-operatingHours-copyTimetoAllBtn').click(function () {
		// Get monday Time values
		var mondayTimeFrom = $('#profile-operatingHours-monday-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val(),
			mondayTimeTo   = $('#profile-operatingHours-monday-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val();
		
		// Set monday Time values to other days
		/* Set all toggle ON*/
        $('.profile-operatingHours-chk_activate').bootstrapToggle('on');
        
		var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

		for (var i = 0; i < availableDays.length; i++) {
			$('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val(mondayTimeFrom);
			$('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val(mondayTimeTo);
		}

		/* Change Button text and add class */
		// $('#profile-operatingHours-copyTimetoAllBtn').css('display', 'none');
		// $('#profile-operatingHours-undoCopyTimetoAllBtn').css('display', 'block');
    });	
    
    /* Undo changes in every days */
	$('#profile-operatingHours-undoCopyTimetoAllBtn').click(function () {
        
        var availableDays = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];

		for (var i = 0; i < availableDays.length; i++) {

            /* Set all toggle OFF*/
		    $('#profile-operatingHours-'+availableDays[i]+'-div .profile-operatingHours-chk_activate').bootstrapToggle('off');
            
            // Set to default time
            $('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val('09:00 AM');
			$('#profile-operatingHours-'+availableDays[i]+'-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val('09:00 PM');
		}

		/* Change Button text and add class */
		$('#profile-operatingHours-copyTimetoAllBtn').css('display', 'block');
		$('#profile-operatingHours-undoCopyTimetoAllBtn').css('display', 'none');
    });	
    
    /* Save Operating Hours */
    $('#profile-operatingHours-saveOperatingHours').click(function () {

        $('#config_alert_box').css('display', 'block');
        $('#config_alert_box').html('Updating records. Please wait...');
        
        var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'],
            operatingAvailableDaysKey = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'publicHoliday'],
            operatingHours = [];
        
        for (let x = 0; x < availableDays.length; x++) {
            let chkValue = $('#profile-operatingHours-'+availableDays[x]+'-div .profile-operatingHours-chk_activate').prop('checked');
            if (chkValue) {
                operatingHours.push({
                    StartTime: $('#profile-operatingHours-'+availableDays[x]+'-div input.timepicker.profile-operatingHours-time-from.ui-timepicker-input').val(),
					EndTime:  $('#profile-operatingHours-'+availableDays[x]+'-div input.timepicker.profile-operatingHours-time-to.ui-timepicker-input').val(),
					[operatingAvailableDaysKey[x]]: 1,
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
                    providersOperatingHours: operatingHours
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