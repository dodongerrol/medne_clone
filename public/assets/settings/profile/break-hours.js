jQuery(document).ready( function ($) { 
    
    /*************** Module jquery Configuration *****************/
    $("[data-toggle='toggle']").bootstrapToggle('destroy');                 
    $("[data-toggle='toggle']").bootstrapToggle();
    
    $('.timepicker').timepicker({
        'timeFormat' : 'h:i A'
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
                        availableDaysKeys = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'publicHoliday'],
                        breakHoursData =  response.data;

                     for (let i = 0; i < breakHoursData.length; i++) {
                        let dayKey = availableDaysKeys.indexOf(breakHoursData[i]['day']); 
                        // Display visible all elements needed.

                        if (availableDays[dayKey] == 'monday') {
                            $('div#profile-breakHours-time-panel #profile-breakHours-'+availableDays[dayKey]+'-div #profile-breakHours-copyTimetoAllBtn').css('display', 'inline-block');
                        }
                        
                        $('div#profile-breakHours-time-panel .'+availableDays[dayKey]+'-addBreakBtn').css('display', 'none');
                        $('div#profile-breakHours-time-panel #profile-breakHours-'+availableDays[dayKey]+'-div .col-md-1.profile-breakHours-detail-lbl').css('display', 'inline-block');
                        $('div#profile-breakHours-time-panel #profile-breakHours-'+availableDays[dayKey]+'-div .toggle').css('display', 'inline-block');
                        $('div#profile-breakHours-time-panel #profile-breakHours-'+availableDays[dayKey]+'-div .timepicker').css('display', 'inline-block');
                        // Set value
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

		for (var i = 0; i < availableDays.length; i++) {console.log(availableDays[i])
            // Display visible all elements needed.
			$('div#profile-breakHours-time-panel .'+availableDays[i]+'-addBreakBtn').css('display', 'none');
			$('div#profile-breakHours-time-panel #profile-breakHours-'+availableDays[i]+'-div .col-md-1.profile-breakHours-detail-lbl').css('display', 'inline-block');
			$('div#profile-breakHours-time-panel #profile-breakHours-'+availableDays[i]+'-div .toggle').css('display', 'inline-block');
            $('div#profile-breakHours-time-panel #profile-breakHours-'+availableDays[i]+'-div .timepicker').css('display', 'inline-block');
            // Set value to time-From and time-To
			$('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(mondayTimeFrom);
			$('#profile-breakHours-'+availableDays[i]+'-div input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(mondayTimeTo);
		}

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
        
        var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'],
            operatingAvailableDaysKey = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'publicHoliday'],
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
                    providersBreakHours: breakHours
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

    // Validate Time-From and Time-to value
    $(document).on('change', 'div#profile-breakHours-time-panel .timepicker.profile-breakHours-time-to', function (time) {
		const timeselected = time.currentTarget.value,
				parentElement = this.parentElement.parentElement.id.split('-div')[0],
                fromTime = $('div#profile-breakHours-time-panel #'+parentElement+'-div .timepicker.profile-breakHours-time-from').val();
            
		if (new Date().getTime(timeselected) <= new Date().getTime(fromTime)) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selected!');
			$('div#profile-breakHours-time-panel #'+parentElement+'-div .timepicker.profile-breakHours-time-to').val('09:00 PM');
			setTimeout(function () {
				$('#config_alert_box').css('display', 'none');
				$('#config_alert_box').css('color', 'black');
			}, 1000);
		}
		
    });

    
    
    // Show break hours time
	$(document).on('click', 
                `#monday-addBreak, #tuesday-addBreak, #wednesday-addBreak,
                #thursday-addBreak, #friday-addBreak, #saturday-addBreak,
                #sunday-addBreak, #publicHoliday-addBreak`, function () {

            const parentName = this.id.split('-addBreak')[0];
                    
            
            // $('div#profile-breakHours-time-panel .'+parentName+'-addBreakBtn').css('display', 'none');
            // $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div .col-md-1.profile-breakHours-detail-lbl').css('display', 'inline-block');
            // $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div .toggle').css('display', 'inline-block');
            // $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div .toggle .profile-breakHours-chk_activate').bootstrapToggle('on');
            // $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div .timepicker').css('display', 'inline-block');

            // // For copy time to all button
            // if (parentName == 'monday') {
            //     $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div #profile-breakHours-copyTimetoAllBtn').css('display', 'inline-block');
            // }

            // Append element
            // $(`<div id="breakDiv-monday1">
            //         <div class="col-md-1" style="padding-top: 3px;">
            //             <input type="checkbox" data-toggle="toggle" data-size="mini" style="float: right;" class="monday1 profile-breakHours-chk_activate" data-onstyle="info">
            //         </div>
            //         <div class="col-md-2" style="padding-left: 10px;">
            //             <input type="button" class="timepicker profile-breakHours-time-from" value="13:00:00" style="float: right; font-size: 12px;">
            //         </div>
            //             <span class="col-md-1 text-center profile-breakHours-detail-lbl" style="padding: 0;width: 12px; padding-top: 8px;">to</span>
            //         <div class="col-md-2">
            //             <input type="button" class="timepicker profile-breakHours-time-to" value="14:00:00" style="font-size: 12px;">
            //         </div>
            //         <div>
            //             <button id="profile-breakHours-copyTimetoAllBtn" >Copy time to all</button>
            //         </div>
            //     </div>
            // `).insertAfter('div#profile-breakHours-time-panel #breakDiv-monday');
            // $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div #breakDiv-'+parentName+'1 .col-md-1.profile-breakHours-detail-lbl').css('display', 'inline-block');
            // $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div #breakDiv-'+parentName+'1 .toggle').css('display', 'inline-block');
            // $('.1.profile-breakHours-chk_activate').bootstrapToggle('on');
            // $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div #breakDiv-'+parentName+'1 .timepicker').css('display', 'inline-block');


    });

    // Toggle
    $(document).on('change', `div#profile-breakHours-time-panel .toggle`, function (element) {

            const parentName = this.firstElementChild.className.split(' ')[0];
            
            if (!$('.'+parentName+'.profile-breakHours-chk_activate').prop('checked')) {
                if (parentName == 'monday') {
                    $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div #profile-breakHours-copyTimetoAllBtn').css('display', 'none');
                }
                $('div#profile-breakHours-time-panel .'+parentName+'-addBreakBtn').css('display', 'inline-block');
                $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div .col-md-1.profile-breakHours-detail-lbl').css('display', 'none');
                $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div .toggle').css('display', 'none');
                $('div#profile-breakHours-time-panel #profile-breakHours-'+parentName+'-div .timepicker').css('display', 'none');
            }
    });

});