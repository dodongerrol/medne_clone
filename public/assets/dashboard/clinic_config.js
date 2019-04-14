jQuery(document).ready(function($) {

  	// var protocol = jQuery(location).attr('protocol');
  	// var hostname = jQuery(location).attr('hostname');
  	// var folderlocation = $(location).attr('pathname').split('/')[1];
  	// window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
// $('[data-toggle="tooltip"]').tooltip();
	window.base_url = window.location.origin + '/app/';
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

  	$("#welcome-next").click(function(){

	  	name = $('#con-clinic-name').val();
	  	speciality = $('.clinic-speciality').attr('id');
	  	mobile = $('#con-mobile').val();
	  	var phone_valid = /[0-9 -()+]+$/;
		
		if (name=='') {
			$('#con-clinic-name').addClass('con-input-error');
			return false;
		} else {
			$('#con-clinic-name').removeClass('con-input-error');
		}
		if (mobile=='' || !phone_valid.test(mobile)) {

			$('#con-mobile').addClass('con-input-error');
			return false;
		} else {

			$('#con-mobile').removeClass('con-input-error');
		}

		$('#config_alert_box').css('display', 'block');
		$('#config_alert_box').html('Updating...');

	  	$.ajax({
          url: base_url+'calendar/load-clinic-details',
          type: 'POST',
          // dataType: 'json',
          // data: { clinicname: name, speciality:speciality, mobile:mobile },
        })
        .done(function(data) {

        	$('#clinic-time-panel').html(data);

        	$('.timepicker').timepicker({
        		'timeFormat' : 'h:i A',
        	});	

        	setTimeout(function(){

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


			}, 500);

        });

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

	  	$.ajax({
          url: base_url+'calendar/load-clinic-doctor-details',
          type: 'POST',
          // dataType: 'json',
          // data: {  },
        })
        .done(function(data) {

        	$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

			$('#clinic-doctors-panel').html(data);

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

	        	$('#step-2').removeClass('active');
	      		$('#setupHours').removeClass('active');
	      		$('#step-3').addClass('active');
	      		$('#setupDoctor').addClass('active in');

	      		$('#lbl-step-2').addClass('glyphicon glyphicon-ok');
	      		$('#lbl-step-2').html('');
	      		$('#lbl-step-2').css("position", "absolute");
	      		$('#lbl-step-2').css("background", "#2AA4D8");
	      		$('#lbl-step-3').css("background", "#2AA4D8");

			}, 500);


        });

  	});


  	$(document).on('change', '#setupHours input.chk_activate', function(event) {

		// evt.stopPropagation();
		// evt.preventDefault();

      	var time_id = $(this).attr('name');
	    var status = $(this).prop('checked');
	    if (status) { status=1;} else { status=0;}

		// alert(status);

		$("#setupHours .toggle").css({'opacity': '.5'});
		$("#setupHours input").attr('disabled',true);

	    	$.ajax({
		      url: base_url+'setting/staff/updateWorkingHoursStatus',
		      type: 'POST',
		      data:{time_id:time_id, status:status}
		    })
		    .done(function(data) {

		    $('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){
   				$("#setupHours .toggle").css({'opacity': '1'});
   		    $("#setupHours input").attr('disabled',false);
				$('#config_alert_box').css('display', 'none');

			}, 500);
		    });

	});

	$(document).on('change', '#setupHours .timepicker', function(event) {

		var time_from = $(this).closest('.row').find('.time-from').val();
	    var time_to = $(this).closest('.row').find('.time-to').val();
	    var day_name = $(this).closest('.row').find('.day-name').text();

		$.ajax({
		    url: base_url+'calendar/updateClinicWorkingHours',
		    type: 'POST',
		    data:{ time_from:time_from, time_to:time_to, day_name:day_name }
		})
		.done(function(data) {

			$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

			}, 500);

		});

	});


// ---------------------------------------------------------------------------------


	$("#doctor-back").click(function(){

    	$('#step-3').removeClass('active');
      	$('#setupDoctor').removeClass('active');
      	$('#step-2').addClass('active');
      	$('#setupHours').addClass('active in');

   		$('#lbl-step-2').removeClass('glyphicon glyphicon-ok');
      	$('#lbl-step-2').html('2');
      	$('#lbl-step-3').css("background", "rgb(117, 214, 247)");

  	});

	$("#doctor-next").click(function(){

		doc = $('#doctors-count').val();

		if (doc == 0 ) {
			alert('Add At Least One Doctor !');
			return false;
		}

	  	$.ajax({
          url: base_url+'calendar/load-clinic-service-details',
          type: 'POST',
        })
        .done(function(data) {

        	$('#config_alert_box').css('display', 'block');
			$('#config_alert_box').html('Updating...');

			$('#clinic-service-panel').html(data);

        	setTimeout(function(){

				$('#config_alert_box').css('display', 'none');

	        	$('#step-3').removeClass('active');
	      		$('#setupDoctor').removeClass('active');
	      		$('#step-4').addClass('active');
	      		$('#setupService').addClass('active in');

	      		$('#lbl-step-3').addClass('glyphicon glyphicon-ok');
	      		$('#lbl-step-3').html('');
	      		$('#lbl-step-3').css("position", "absolute");
	      		$('#lbl-step-3').css("background", "#2AA4D8");
	      		$('#lbl-step-4').css("background", "#2AA4D8");

	      		$('#doc-tiptool').tooltip();
				
			}, 500);

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
      	$('#step-4').addClass('active');
      	$('#setupService').addClass('active in');

   		$('#lbl-step-4').removeClass('glyphicon glyphicon-ok');
      	$('#lbl-step-4').html('4');

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





	// $(document).on('click', '#', function(event) {

	// });



});
