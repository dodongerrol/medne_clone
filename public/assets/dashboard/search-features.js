jQuery(document).ready(function($) {

	window.base_url = window.location.origin + '/app/';
	window.base_loading_image = '<img src="'+ window.location.origin +'/assets/images/loading.svg" width="32" height="32" alt=""/>';

	var searchIDValue = null;
  var selectedCorporate = null;
  var bookData = null;
  var stat_price = 0;

  localStorage.setItem('corporate-selected',false);

  

    // --- SEARCH FEATURES --- //
  $(document).on('click', "#continue-search-button", function(event) {
      $("#search-booking-modal #content").hide();
      $("#search-booking-modal #content-two").fadeIn();
      $("#search-booking-modal #back-search-button").fadeIn();
      $("#search-booking-modal #book-search-button").fadeIn();
      $("#search-booking-modal #continue-search-button").hide();
      $("#search-booking-modal .modal-body ul li").removeClass('active');
      $("#search-booking-modal .modal-body ul li:nth-child(2)").addClass('active');
  });

  $(document).on('click', "#back-search-button", function(event) {
      $("#search-booking-modal #content").fadeIn();
      $("#search-booking-modal #content-two").hide();
      $("#search-booking-modal #back-search-button").hide();
      $("#search-booking-modal #book-search-button").hide();
      $("#search-booking-modal #continue-search-button").fadeIn();
      $("#search-booking-modal .modal-body ul li").removeClass('active');
      $("#search-booking-modal .modal-body ul li:nth-child(1)").addClass('active');
  });

  $(document).on('click', "#book-search-button", function(event) {
    var startTime = $( "#search-booking-modal #appointment-time-search" ).val();
    var endTime = moment(startTime, 'hh:mm A').add( $( "#search-booking-modal #block-time-Duration-search" ).val() ,'minutes').format('hh:mm A');
    // console.log(startTime);
    // console.log(endTime);

    if( startTime == '12:00 AM' ){
      $("#search-error-mess").text('No Time Selected !');
      return false;
    }

    if( $( "#search-booking-modal #selected-pro-id" ).val() ){
    	var data = {
	      clinicID: $( "#clinicID" ).val(),
	      user_id: selectedCorporate.UserID,
	      name: selectedCorporate.first_name + " " + selectedCorporate.last_name,
	      emai: selectedCorporate.email,
	      nric: selectedCorporate.NRIC,
	      duration: $( "#search-booking-modal #block-time-Duration-search" ).val(),
	      code: selectedCorporate.PhoneCode,
	      phone: selectedCorporate.PhoneNo,
	      doctorid: $( "#search-booking-modal .doctor-selection" ).attr('id'),
	      procedureid: $( "#search-booking-modal #selected-pro-id" ).val(),
	      starttime: startTime,
	      endtime: endTime,
	      bookdate: $( "#search-booking-modal #appointment-date-search" ).val(),
	      clinictimeid: 0,
        // price:  $( "#search-booking-modal #service-price-search" ).val(),
	      price:  stat_price,
	      remarks : $( "#search-booking-modal #notes" ).val()

	    }
	    // console.log(data);

	      bookSearchFunction(data);
    }else{
    	$("#search-error-mess").text('Please Select A Service Procedure !');
    }
    
  });

  $("#search-customer-feature").keydown(function (e) {
   if (e.keyCode == 13) {
      e.preventDefault();
      searchIDValue = $('#search-customer-feature').val();
      searchID( searchIDValue );
    }
    
  });

  $(document).on('click', "#search-feature-open-modal", function(event) {
      searchIDValue = $('#search-customer-feature').val();
      console.log(searchIDValue);
    // if( searchIDValue != null){
      searchID( searchIDValue );
    // }
      
      localStorage.setItem('corporate-selected',true);
  });

  getAllUsers();

  

  function getAllUsers(){
    $.ajax({

        url: base_url+'corporate/get_identification_numbers',
        type: 'GET',
        dataType: 'json',

      })
      .done(function(data) {
        // console.log(data);
        var arr = [];
        for( var i = 0; i<data.length; i++ ){
          arr.push({
            "value" : data[i].NRIC,
            "data"  : data[i] 
            });
        }

        $('#search-customer-feature').autocomplete({

          lookup: arr,
          minChars:3,

          onSelect: function (suggestion) {
            searchIDValue = suggestion.data;
            localStorage.setItem('corporate-selected',true);
            searchID( searchIDValue );
          }

        });

    });
  }

  function searchID ( data ){
      console.log(data);
      var temp = null;

      if( data && data.NRIC ){
        temp = data.NRIC;
      }else{
        temp = data;
      }

      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' </h1>'});
        $.ajax({

          url: base_url + 'corporate/search',
          type: 'POST',
          dataType: 'json',
          data: { search: temp }, 

        })
        .done(function(data) {
          // console.log(data);
          if( data[0] ){
            $.ajax({

              url: base_url + 'corporate/get_corporate/' + data[0].UserID,
              type: 'GET',
              dataType: 'json', 

            })
            .done(function(data) {
              // console.log(data);

            selectedCorporate = data;

            $("#search-booking-modal").modal('show');

            $( "#appointment-date-search" ).val( moment( new Date() ).format( 'dddd, MMMM DD YYYY' ) );
            $( "#appointment-time-search" ).val( moment( new Date() ).format( 'hh:mm A' ) );

            $("#search-booking-modal #fname").val( selectedCorporate.first_name );
            $("#search-booking-modal #lname").val( selectedCorporate.last_name );
            $("#search-booking-modal #IDnum").val( selectedCorporate.NRIC );
            $("#search-booking-modal #comp_name").val( selectedCorporate.company_name );
            jQuery.unblockUI();

            });
          }else{
            $.alert({
                title: 'Alert!',
                content: 'IC Number not found!',
                columnClass: 'col-md-4 col-md-offset-4',
                    theme: 'material',
                confirm: function(){
                    jQuery.unblockUI();
                }
            });
          }
          
        });

        
        
  }

  function bookSearchFunction ( data ){
    // console.log(data);
    $("#book-search-button").attr('disabled',true);
    jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
        $.ajax({

          url: base_url+'corporate/book',
          type: 'POST',
          dataType: 'json',
          data: data,  
        })
        .done(function(data) {
          // console.log(data);
          jQuery.unblockUI();
          $("#book-search-button").attr('disabled',false);
          if( data == 1 ){
            // console.log("Double booking not allowed");

            $("#search-error-mess").text('Double booking not allowed');
          }else if( data == 3 ){
            // console.log("Date or Time not available !");

            $("#search-error-mess").text('Date or Time not available !');
          }else{
          	
          	
            $("#search-error-mess").text('');
            var search_log_event = window.localStorage.getItem('search_log_event');
            if(search_log_event == "true") {
              getEvents();
            } else {
              getGroupEvents();
            }
            getAllUsers();
            // $(".doctor-list li a").click();

            $("#search-booking-modal").modal('hide');
            
            $("#search-customer-feature").val('');
            stat_price = 0;
          }
      });
  }

// function getEvents() {
//   console.log("DSFDSFsd");
//   jQuery.blockUI({message: '<h1> ' + base_loading_image + ' </h1>'});
//   var view = $('#calendar').fullCalendar('getView');
//   var start_date = moment(view.start).format("YYYY-MM-DD");
//   $.ajax({
//         url: base_url + 'get/group_events',
//         type: 'POST',
//         dataType: 'json',
//         data : { clinic_id : $("#clinicID").val(), start_date: start_date }
//       })
//       .done(function(data) {
//         $('#calendar').fullCalendar('removeEvents');
//         $('#calendar').fullCalendar( 'addEventSource', data);
//         jQuery.unblockUI();
//     });
// }

function getEvents( ) {
    var view = $('#calendar').fullCalendar('getView');
    var current_date = moment(view.start).format("DD-MM-YYYY");

    var doctorID = $('.doctor-selection').attr('id');
    jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

    $.ajax({
      url: base_url+'calendar/getevent',
      type: 'POST',
      dataType: 'json',
      data: {current_date: current_date, doctorID:doctorID},
    })
    .done(function(data) {
      // console.log(data);
      $('#calendar').fullCalendar('removeEvents');
      $('#calendar').fullCalendar( 'addEventSource', data);
      jQuery.unblockUI();
    });

}

function getGroupEvents() {
  jQuery.blockUI({message: '<h1> ' + base_loading_image + ' </h1>'});
  var view = $('#calendar').fullCalendar('getView');
  var start_date = moment(view.start).format("YYYY-MM-DD");
  $.ajax({
        url: base_url + 'get/group_events',
        type: 'POST',
        dataType: 'json',
        data : { clinic_id : $("#clinicID").val(), start_date: start_date }
      })
      .done(function(data) {
        // console.log(data);
        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar( 'addEventSource', data);
        jQuery.unblockUI();
    });

}


  $(document).on('hidden.bs.modal', "#search-booking-modal", function(event) {
      searchIDValue = null;
      // console.log(searchIDValue);
      $("#search-booking-modal #content").fadeIn();
      $("#search-booking-modal #content-two").hide();
      $("#search-booking-modal #back-search-button").hide();
      $("#search-booking-modal #book-search-button").hide();
      $("#search-booking-modal #continue-search-button").fadeIn();
      $("#search-booking-modal .modal-body ul li").removeClass('active');
      $("#search-booking-modal .modal-body ul li:nth-child(1)").addClass('active');

      $("#search-booking-modal .service-selection").text("Select a Service");
      $( "#search-booking-modal #selected-pro-id" ).val('');
      $("#search-customer-feature").val('');

      localStorage.setItem('corporate-selected',false);
      // console.log( localStorage.getItem('corporate-selected') );
      getDoctorProcedure();
  });

  function getDoctorProcedure() {

      var clinicID = $('#clinicID').val();
      var doc_id = $('.doctor-selection').attr('id');
      var corp = localStorage.getItem('corporate-selected');
      // console.log(corp);
      $.ajax({
      url: base_url+'calendar/getDoctorProcedure',
      type: 'POST',
      data: {docID: doc_id, clinicID:clinicID, corporate:corp },
      })

      .done(function(data) {
        // console.log(data);
      $('#service-list').html(data);
      $('#service-list-search').html(data);
      $('.slot-block').html(data);

      });
  }

  $(document).on('click', "#service-list-search li", function(event) {

  val = $('.service',this).text();
  id = $('.service',this).attr('id');

  // console.log(val);
  // console.log(id);

  $( "#search-booking-modal #selected-pro-id" ).val(id);

    $('#search-booking-modal .service-selection').html(val);
    $('#search-booking-modal .service-selection').attr('id', id);

    $('#search-booking-modal .blocker-time-format').html('Mins');
    $('#search-booking-modal .blocker-time-format').attr('id', 'mins');
    $('#search-booking-modal .time-format').html('Mins');
    $('#search-booking-modal .time-format').attr('id', 'mins');
    $( "#search-booking-modal #block-time-Duration-search" ).val( $('#selected-duration',this).text());
    if (id == '0') {

          $('#myModal #patient-tab').removeClass('show').addClass('hide');

          $('#myModal #myModalLabel').text("Blocker");

          $('#myModal #booking .panel-body #service-lbl').removeClass('input-width').addClass('slot-blocker-width');
          $('#myModal #booking .panel-body #slot-blocker-service').removeClass('hide').addClass('show');
          $('#myModal #booking .panel-body #Cost-Time-duration').removeClass('show').addClass('hide');

          $('#blocker').removeClass('hide').addClass('show');
          $('#continue').removeClass('show').addClass('hide');
          $('#block-time-Duration').val($('#h-duration').val());
          
    }

    else{
          $('#myModal #patient-tab').removeClass('hide').addClass('show');

          $('#myModal #myModalLabel').text("Appointment");

          $('#myModal #booking .panel-body #slot-blocker-service').removeClass('show').addClass('hide');
          $('#myModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width').addClass('input-width');
          $('#myModal #booking .panel-body #Cost-Time-duration').removeClass('hide').addClass('show');

          $('#blocker').removeClass('show').addClass('hide');
          $('#continue').removeClass('hide').addClass('show');

    }

    $('#myModal #booking #ok-icon').addClass('glyphicon-ok');
    $('#myModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
    $('#myModal #booking #ok-icon').removeClass('arrow-color');

  //alert(id);
  getPrcedureDetails();

  });

  function getPrcedureDetails() {

    var clinicID = $('#clinicID').val();
    var procedureID = $('#search-booking-modal .service-selection').attr('id');

    // console.log(clinicID);
    // console.log(procedureID);
    // console.log('this', localStorage.getItem('corporate-selected'));

    $.ajax({
        url: base_url+'calendar/load-procedure-details',
        type: 'POST',
        dataType: 'json',
        data: {procedureID: procedureID, clinicID:clinicID , corporate : localStorage.getItem('corporate-selected') },
      })
      .done(function(data) {
        // console.log(data);
        if( data ){
          $("#service-time-Duration").val(data.Duration);
          $("#service-time-Duration-reserve").val(data.Duration);
          $("#service-price").val(data.Price);
          stat_price = data.Price;
          $("#service-price-search").val(data.Price);
          $("#service-price-reserve").val(data.Price);
        }
        

      });

  }

});	
