jQuery(document).ready(function($) {
	
  // var protocol = jQuery(location).attr('protocol');
  // var hostname = jQuery(location).attr('hostname');
  // var folderlocation = $(location).attr('pathname').split('/')[1];
  // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
  window.base_url = window.location.origin + '/app/';


	$(".cal-type").click(function(){

  		id = $(this).attr('id');

		$.ajax({
		    url: base_url+'setting/account/update-calendar-config',
		    type: 'POST',
		    data:{cal_type:id, status:1} // status 1 - update Calendar type
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
			}, 3000);

		});

	});


	$("#day-list li a").click(function(){

  		day = $(this).text();
  		id = $(this).attr('id');

  		$('.day-section').val(day);
    	$('.day-section').attr('id', id);

		$.ajax({
		    url: base_url+'setting/account/update-calendar-config',
		    type: 'POST',
		    data:{cal_day:id, status:2} // status 2 - update Calendar day
		})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');

				var text = "Start of Week Updated !";
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


  	$("#duration-list li a").click(function(){

  		time = $(this).text();
  		id = $(this).attr('id');

    	$('.duration-section').val(time);
    	$('.duration-section').attr('id', id);

    	$.ajax({
		    url: base_url+'setting/account/update-calendar-config',
		    type: 'POST',
		    data:{cal_duration:id, status:3} // status 3 - update Calendar slot duration
		})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');

				var text = "Calendar Time Unit Updated !";
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


  	$(".hour-section").change(function(){

  		start_hour = $(this).val();

    	$.ajax({
		    url: base_url+'setting/account/update-calendar-config',
		    type: 'POST',
		    data:{start_hour:start_hour, status:4} // status 4 - update Calendar starting hour
		})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){ 
				$('#alert_box').css('display', 'none');

				var text = "Calendar Start Hour Updated !";
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


  	$("#clinic-pin-toggle").change(function(){

  		var value = $(this).val();
		$.ajax({

      		url: base_url+'setting/account/update-calendar-config',
		    type: 'POST',
		    data:{ pin_val:value, status:5 } // status 4 - update clinic pin status

    	})
		.done(function(data) {

			$('#alert_box').css('display', 'block');
			$('#alert_box').html('Updating...');
			
			setTimeout(function(){	

				$('#alert_box').css('display', 'none');

				var text = "PIN Verification Updated !";
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


// ===================================================================================================== //

}); // end of jQuery


// #######################################################################################################
// #                                           Fuctions                                                  # 
// #######################################################################################################
