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
                        let dayKey = availableDaysKeys.indexOf(breakHoursData[i]['day'].replace(/\d+/g, '')); 
                        // Trigger button click event
                        if (!$('#'+availableDays[dayKey]+'-div').is(':visible')) {
                            $('#'+availableDays[dayKey]+'-addBreak').click();    
                        }

                        // Show row
                         $('div#profile-breakHours-time-panel #'+availableDays[dayKey]+'-mainCollapsibleDiv .row.'+availableDays[dayKey]+breakHoursData[i]['day'].toString().match(/[0-9]/g)[0]+'').css('display', 'block');

                        // Set Toggle ON
                         $('div#profile-breakHours-time-panel #'+availableDays[dayKey]+'-mainCollapsibleDiv .row.'+availableDays[dayKey]+breakHoursData[i]['day'].toString().match(/[0-9]/g)[0]+' .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                        // Populate Time from and to
                        $('div#profile-breakHours-time-panel #'+availableDays[dayKey]+'-mainCollapsibleDiv .row.'+availableDays[dayKey]+breakHoursData[i]['day'].toString().match(/[0-9]/g)[0]+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(breakHoursData[i]['start_time']);
                        $('div#profile-breakHours-time-panel #'+availableDays[dayKey]+'-mainCollapsibleDiv .row.'+availableDays[dayKey]+breakHoursData[i]['day'].toString().match(/[0-9]/g)[0]+'  input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(breakHoursData[i]['end_time']);

                        

                        
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
    /* Copy and Paste time to all days  */
	$('#profile-breakHours-copyTimetoAllBtn').click(function () {
        // Get Parent Element
        const parentElementClass =  this.parentElement.parentElement.className.split(' ').join('.');
        
        // Get number of time set on Monday
        let numberOfTimeSet = 0,
            timeArray = [];

        for (let j = 0; j < 5; j++) {
           const checked =  $('div#profile-breakHours-time-panel #monday-mainCollapsibleDiv .row.monday'+j+' .profile-breakHours-chk_activate').prop('checked');
            if (checked) {
                numberOfTimeSet += 1;
                timeArray.push({
                    'mondayTimeFrom': $('div#profile-breakHours-time-panel #monday-mainCollapsibleDiv .monday'+j+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
                    'mondayTimeTo': $('div#profile-breakHours-time-panel #monday-mainCollapsibleDiv .monday'+j+' input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val()
                });
            }
        }
		
		// Set monday Time values to other days
		var availableDays = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'];
        
        for (let i = 0; i < availableDays.length; i++) {
        console.log(numberOfTimeSet)
            // Trigger click event
            $('#'+availableDays[i]+'-addBreak').click();

            for (let x = 0; x < numberOfTimeSet; x++) {
                // Display Block
				$('div#profile-breakHours-time-panel #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x).css('display', 'block');
                
                // Set Toggle ON
                $('div#profile-breakHours-time-panel #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x+' .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Set time
                $('div#profile-breakHours-time-panel #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(timeArray[x]['mondayTimeFrom']);
                $('div#profile-breakHours-time-panel #'+availableDays[i]+'-mainCollapsibleDiv .row.'+availableDays[i]+x+'  input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(timeArray[x]['mondayTimeTo']);
            }
        }
    });	
    
    /* Save Break Hours */
    $('#profile-breakHours-savebreakHours').click(function () {

        $('#config_alert_box').css('display', 'block');
        $('#config_alert_box').html('Updating records. Please wait...');
        
        var availableDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'publicHoliday'],
            operatingAvailableDaysKey = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'publicHoliday'],
            breakHours = [];
        
        for (let x = 0; x < availableDays.length; x++) {
            for (let i = 0; i < 5; i++) {
                let chkValue = $('div#profile-breakHours-time-panel #'+availableDays[x]+'-mainCollapsibleDiv .row.'+availableDays[x]+i+' .profile-breakHours-chk_activate').prop('checked');
                
                if (chkValue) {
                    breakHours.push({
                        start_time: $('div#profile-breakHours-time-panel #'+availableDays[x]+'-mainCollapsibleDiv .row.'+availableDays[x]+i+' input.timepicker.profile-breakHours-time-from.ui-timepicker-input').val(),
                        end_time:  $('div#profile-breakHours-time-panel #'+availableDays[x]+'-mainCollapsibleDiv .row.'+availableDays[x]+i+' input.timepicker.profile-breakHours-time-to.ui-timepicker-input').val(),
                        day: operatingAvailableDaysKey[x]+i,
                        type: 3,
                        updated_at: new Date().getFullYear(),
                        created_at: new Date().getFullYear()
                    });
                }
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
    $(document).on('change', 'div#profile-breakHours-time-panel .timepicker.profile-breakHours-time-to', function () {
        const   rowIndexClass = this.parentElement.parentElement.className.split('row ')[1],
                parentElement = rowIndexClass.replace(/\d+/g,''),
                fromTime = $('div#profile-breakHours-time-panel #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-from').val(),
                timeselected = $('div#profile-breakHours-time-panel #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-to').val(),
                fullYear = new Date().getFullYear(),
                month = ("0" + (new Date().getMonth() + 1)).slice(-2),
                day = new Date().getDate(),
                allowedSameTime = new Date(month+'-'+day+'-'+fullYear+' 12:00 AM').getTime();
           
		if ( !(allowedSameTime == new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime() && allowedSameTime == new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime())
            && (new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime() <= new Date(month+'-'+day+'-'+fullYear+' '+fromTime).getTime())) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selected!');
			$('div#profile-breakHours-time-panel #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-to').val('02:00 PM');
			setTimeout(function () {
				$('#config_alert_box').css('display', 'none');
				$('#config_alert_box').css('color', 'black');
			}, 1000);
		}
		
    });

    $(document).on('change', 'div#profile-breakHours-time-panel .timepicker.profile-breakHours-time-from', function () {
        const   rowIndexClass = this.parentElement.parentElement.className.split('row ')[1],
                parentElement = rowIndexClass.replace(/\d+/g,''),
                fromTime = $('div#profile-breakHours-time-panel #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-to').val(),
                timeselected = $('div#profile-breakHours-time-panel #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-from').val(),
                fullYear = new Date().getFullYear(),
                month = ("0" + (new Date().getMonth() + 1)).slice(-2),
                day = new Date().getDate(),
                allowedSameTime = new Date(month+'-'+day+'-'+fullYear+' 12:00 AM').getTime();
           
		if (!(allowedSameTime == new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime() && allowedSameTime == new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime())
            && (new Date(month+'-'+day+'-'+fullYear+' '+timeselected).getTime() >= new Date(month+'-'+day+'-'+fullYear+' '+fromTime).getTime())) {
			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').css('color', 'red');
			$('#config_alert_box').html('Invalid time selected!');
			$('div#profile-breakHours-time-panel #'+parentElement+'-div .row.'+rowIndexClass+' .timepicker.profile-breakHours-time-from').val('01:00 PM');
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
                $('.row.'+parentName+'0 .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Stage 2:  Collapse Show
                $('#'+parentName+'-div').collapse('show');

                // Stage 3: CSS, Remove element and Class changes
                $('.day-label-'+parentName+'').attr('class','day-label-'+parentName+'');
                $('.day-label-'+parentName+'').css('clear', '');
                $('.day-label-'+parentName+'').css('margin-left', '2%');
                $('div#'+parentName+'-div .card-body').css('margin-left', '18%');
                $('.'+parentName+'-addBreakBtn').remove();

                // Stage 4: Element insertion
                $('#'+parentName+'-div').after(`
                <div class="`+parentName+`-addBreakBtn">
                    <a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='`+parentName+`-addBreak'>
                        <span class="glyphicon glyphicon-plus"></span> Add Break
                    </a>
                </div>`);
                
                // Stage 5: Add Css
                $('.'+parentName+'-addBreakBtn').css('margin-left', '18%');
                $('.'+parentName+'-addBreakBtn').css('padding-top', '1%');
                $('.'+parentName+'-addBreakBtn').css('padding-bottom', '1%');

            } else if (!$('#'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'1').is(':visible')) {
                 // Set Toggle ON
                $('.row.'+parentName+'1 .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Show row visibility
                $('div#profile-breakHours-time-panel .row.'+parentName+'1').css('display', 'block');

                // Disable row 0
                $('.'+parentName+'0.profile-breakHours-chk_activate').prop('disabled',  true);

            } else if (!$('#'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'2').is(':visible')) {
                 // Set Toggle ON
                $('.row.'+parentName+'2 .profile-breakHours-chk_activate').bootstrapToggle('on');
                
                // Show row visibility
                $('div#profile-breakHours-time-panel .row.'+parentName+'2').css('display', 'block');

            } else if (!$('#'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'3').is(':visible')) {
                 // Set Toggle ON
                $('.row.'+parentName+'3 .profile-breakHours-chk_activate').bootstrapToggle('on');

                // Show row visibility
                $('div#profile-breakHours-time-panel .row.'+parentName+'3').css('display', 'block');

            } else if (!$('#'+parentName+'-mainCollapsibleDiv .card-body .row.'+parentName+'4').is(':visible')) {
                // Set Toggle ON
                $('.row.'+parentName+'4 .profile-breakHours-chk_activate').bootstrapToggle('on');

                // Show row visibility
                $('div#profile-breakHours-time-panel .row.'+parentName+'4').css('display', 'block');

            }
            
    });

    // Toggle for breaks
    $(document).on('change', `div#profile-breakHours-time-panel .toggle`, function (element) {

            const parentName = this.firstElementChild.className.split(' ')[0];
           
            if (!$('.row.'+parentName+' .profile-breakHours-chk_activate').prop('checked')) {
                if (parentName.indexOf('0') > 0 
                    && $('div#profile-breakHours-time-panel .row.'+parentName).is(':visible') 
                    && !$('div#profile-breakHours-time-panel .row.'+parentName.replace(/\d+/g,'')+'1').is(':visible')) {
                
                    // Stage 1:  Collapse Show
                    $('div#profile-breakHours-time-panel .row.'+parentName).css('display', 'none');
                    $('#'+parentName.replace(/\d+/g,'')+'-div').collapse('hide');
                    $('div#profile-breakHours-time-panel .row.'+parentName).css('display', 'block');
                
                    // Stage 2: CSS, Remove element and Class changes
                    $('.'+parentName.replace(/\d+/g,'')+'-addBreakBtn').remove();
                    $('.day-label-'+parentName.replace(/\d+/g,'')+'').attr('class','day-label-'+parentName.replace(/\d+/g,'')+' col-md-2');
                    $('.day-label-'+parentName.replace(/\d+/g,'')+'').css('clear', 'both');

                    // Stage 3: Undo changes in button
                    $('.day-label-'+parentName.replace(/\d+/g,'')+'').after(`
                    <div class="col-md-3 `+parentName.replace(/\d+/g,'')+`-addBreakBtn" style="margin-top:1%;margin-bottom: 1%;">
                        <a class="btn btn-primary" data-toggle="collapse" role="button" aria-expanded="false" id='`+parentName.replace(/\d+/g,'')+`-addBreak'>
                        <span class="glyphicon glyphicon-plus"></span>Add Break
                        </a>
                    </div>`);

                    // Stage 4: Removed Css
                    $('.'+parentName+'-addBreakBtn').css('margin-left', '');
                    $('.'+parentName+'-addBreakBtn').css('padding-top', '');
                    $('.'+parentName+'-addBreakBtn').css('padding-bottom', '');
                    $('.day-label-'+parentName.replace(/\d+/g,'')+'').css('margin-left', '');
                } else {
                    if ($('div#profile-breakHours-time-panel .row.'+parentName).is(':visible')) {
                        if (parentName.indexOf('1') > 0 ) {
                            $('.'+parentName.replace(/\d+/g,'')+'0.profile-breakHours-chk_activate').prop('disabled',  false);
                        }
                        $('div#profile-breakHours-time-panel .row.'+parentName).css('display', 'none');
                    }
                }
            }
    });

});