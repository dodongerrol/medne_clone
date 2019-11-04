gl_titel = "w";
gl_clinic_pin_status = 0;
stat_price = 0;
stat_user_id = 0;
gl_date = ''
gl_stime = '';
gl_etime = '';
gl_event_id = 0;
gl_event_title = '';
co_paid = 0;
  //  pin verification types
  // 1 - new appointment;
  // 2 - resize
  // 3 - drag
  // 4 - edit
  // 5 - delete
  // 6 - conclude
  // 7 - noshow
var dayView = false;
jQuery(document).ready(function($) {

  // $('body').clearQueue();

  // var protocol = jQuery(location).attr('protocol');
  // var hostname = jQuery(location).attr('hostname');
  // var folderlocation = $(location).attr('pathname').split('/')[1];

  // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
  window.base_url = window.location.origin + '/app/';
  window.image_url = window.location.origin + '/';
  window.base_loading_image = '<img src="'+ image_url +'assets/images/loading.svg" width="32" height="32" alt=""/>';

  setAccountSetting();

  // getEvents();

  highlightCurrentDate();

  saveAppointment();

  popupValidation();

  saveBlocker();

  saveReserveBlocker();

  getAllUsers();

  deleteExtraEvent();

  DeleteAppointment();

  ConcludedAppointment();

  NoShowAppointment();

  getClinicPinStatus();

  display_new_appintment();

  getClinicDetails();
  // ........................................................../



  // ......................................................../


  $("#calendar-view-option li a").click(function(){
  val = $(this).attr('id');
  gl_titel = val;

  if (val=='d') {
    dayView = true;
    $('.fc-agendaDay-button').click();
    displayDate();
    // getEvents();
  }
  if (val=='w') {
    dayView = false;
    $('.fc-agendaWeek-button').click();
    displayDate(); }
  if (val=='m') {
    dayView = false;
    $('.fc-month-button').click();
    displayDate();
  }

  if (val=='g') {
    window.localStorage.setItem('search_log_event', false);
    view_calendar_group();
  }


    // alert(val);
    $('#calender-selection').html($(this).text());

  });

  function view_calendar_group() {
    $("#calendar_page_container").html("");
    $.ajax({
      url: base_url+'clinic/calendar-view-group',
      type: 'GET',
    })
    .done(function(data) {
      $("#calendar_page_container").html(data);

    })
  }


  $(".doctor-list li a").click(function(){
  val = $(this).text();
  id = $(this).attr('id');

    $('.doctor-selection').html(val);
    $('.doctor-selection').attr('id', id);
    getEvents();
    getDoctorProcedure();

  });


  // .............................................

  $('#btn-left').click(function(event) {
    $('.fc-prev-button').click();
    displayDate();
    getEvents();
  });

  $('#btn-right').click(function(event) {
    $('.fc-next-button').click();
    displayDate();
    getEvents();
  });

  $('#btn-today').click(function(event) {
    $('.fc-today-button').click();
    displayDate();
    getEvents();
  });

// .......................................................................

// ---------- calendar view option ------------------------

getStatusView();

$("#viewByDrop").change(function() {

    $("#viewByTab").attr("checked",false);

    $("#byDropdown").fadeIn();
    $("#byTabs").hide();

    localStorage.setItem("viewStatus","byDrop");
});

$("#viewByTab").change(function() {

    $("#viewByDrop").attr("checked",false);

    $("#byDropdown").hide();
    $("#byTabs").fadeIn();

    var selectedProvider = $(".doctor-selection").attr("id");

    $("#byTabs .doctor-list li").removeClass("active");
    $("#byTabs .doctor-list li." + selectedProvider).addClass("active");

    localStorage.setItem("viewStatus","byTab");
});

$(document).on('click', ' #provider-option', function (e) {
  e.stopPropagation();
});

function getStatusView(){

  var status = localStorage.getItem("viewStatus");
  
  if( status == "byDrop" ){
    $("#viewByTab").attr("checked",false);
    $("#viewByDrop").attr("checked",true);

    $("#byDropdown").fadeIn();
    $("#byTabs").hide();

    localStorage.setItem("viewStatus","byDrop");
  }else if( status == "byTab" ){
    $("#viewByTab").attr("checked",true);
    $("#viewByDrop").attr("checked",false);

    $("#byDropdown").hide();
    $("#byTabs").fadeIn();

    localStorage.setItem("viewStatus","byTab");
  }else{
    $("#viewByTab").attr("checked",false);
    $("#viewByDrop").attr("checked",true);

    $("#byDropdown").fadeIn();
    $("#byTabs").hide();

    localStorage.setItem("viewStatus","byDrop");
  }
}



// ---------- datepicker calendar hover function -----------

  $('#mini-calendar-view').mouseover( function() {

    $('.btn-default').css ('background' , '#73CEF4');
    $('#dp').css ('display' , 'block');

  } ).mouseout( function() {

        $('.btn-default').css ('background' , '');
        $('#dp').css ('display' , 'none');

  });


// ---------- datepicker calendar select date function ------


  $('#dp').datepicker({
    onSelect: function (argument) {
      var date = $(this).datepicker( 'getDate' );
      $('#calendar').fullCalendar('gotoDate', date);
      displayDate();
      getEvents();
    }
  });


//......................  Appointment date & Time picker calendar  .................
  getEnabledDates();
  getDisabledTimes(new Date);
  
  function getEnabledDates(){
    $.ajax({

        url: base_url+'corporate/enable-dates',
        type: 'POST',
        dataType: 'json',
        data : { clinicID : $("#clinicID").attr('id'), docID : $( "#search-booking-modal .doctor-selection" ).attr('id') }

      })
      .done(function(data) {
        availableDates = data;
    });

  }

  function getDisabledTimes(date){
    $.ajax({

          url: base_url+'corporate/disable-times',
          type: 'POST',
          dataType: 'json',
          data : { clinicID: $("#clinicID").attr('id'), docID: $( "#search-booking-modal .doctor-selection" ).attr('id'), date: moment(date).format('dddd, d MMMM YYYY') , duration: $( "#search-booking-modal #block-time-Duration-search" ).val() }

        })
        .done(function(data) {
          // console.log(data);
          // if( data[0] == 1 ){
          //   $("#appointment-time-search").attr('disabled',true);
          //   $("#appointment-time-search").css({'opacity': '.5'});
          // }else{
          //   $("#appointment-time-search").attr('disabled',false);
          //   $("#appointment-time-search").css({'opacity': '1'});
          // }
          $('#appointment-time-search').timepicker('option', 'disableTimeRanges', data[1]);
      });
  }

  $( "#appointment-date" ).datepicker({

    dateFormat : "DD, MM dd yy" ,
    minDate : 0,
    maxDate : 360,
  });

  $( "#appointment-date-search" ).datepicker({

    dateFormat : "DD, MM dd yy" ,
    minDate : 0,
    maxDate : 360,
    beforeShowDay : available,
    onSelect : function( date ){
      // console.log(date);
      getDisabledTimes(date);
    }
  });

  function available(date) {
    dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
    if ($.inArray(dmy, availableDates) != -1) {
      return [true, "","Available"];
    } else {
      return [false,"","unAvailable"];
    }
  }

  $('#appointment-time').timepicker({

      'timeFormat' : 'h:i A',
    });

  $('#appointment-time-search').timepicker({
      'minTime' : '6am',
      'maxTime' : '11.45pm',
      'timeFormat' : 'h:i A'
    });

  $( "#appointment-date-reserve" ).datepicker({

    dateFormat : "DD, MM dd yy" ,
    minDate : 0,
    maxDate : 360
  });

  $('#appointment-time-reserve').timepicker({

      'timeFormat' : 'h:i A',
    });

// ................................ drop down handling .............................

// $(document).ready(function () {
//     $('.navbar-default .navbar-nav > li.dropdown').hover(function () {
//         $('ul.dropdown-menu', this).stop(true, true).slideDown('fast');
//         $(this).addClass('open');
//         $('.open > a').css('background', 'transparent');
//     }, function () {
//         $('ul.dropdown-menu', this).stop(true, true).slideUp('fast');
//         $(this).removeClass('open');
//     });
// });



// $(document).ready(function () {
//     $('#calender_header .navbar-nav >li>.dropdown').hover(function () {
//         $('ul.dropdown-menu', this).stop(true, true).slideDown('');
//         $(this).addClass('open');
//         // $('.open > a').css('background', 'transparent');
//     }, function () {
//         $('ul.dropdown-menu', this).stop(true, true).slideUp('');
//         $(this).removeClass('open');
//     });
// });


// $('#doctor-list>li>a').click(function(event) {
//   $('#doctor-list').css('display', 'none');
//   $('#calender_header .navbar-nav >li>.dropdown').removeClass('open');
// });


// $('#calendar-view-option>li>a').click(function(event) {
//   $('#calendar-view-option').css('display', 'none');
//   $('#calendar-view-option .navbar-nav >li>.dropdown').removeClass('open');
// });


// ----------------------  Appointment Popup Menu Handling -------------------------

$('#myModal #booking .panel-body #slot-blocker-service').addClass('hide');
$('#myModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width');
$('#myModal #booking .panel-body #service-lbl').addClass('input-width');

$('#myModal #booking .panel-body #Cost-Time-duration').addClass('hide');

$('#myModal #patient .panel-body #new-customer').addClass('hide');

$('#reserveModal #booking .panel-body #slot-blocker-service').addClass('hide');
$('#reserveModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width');
$('#reserveModal #booking .panel-body #service-lbl').addClass('input-width');

$('#reserveModal #booking .panel-body #Cost-Time-duration').addClass('hide');

$('#reserveModal #patient .panel-body #new-customer').addClass('hide');



// ------- Service list on click functions --------

$("#service-list").on("click","li", function(){
  val = $('.service',this).text();
  id = $('.service',this).attr('id');

  $( "#selected-pro-id" ).val(id);

    $('.service-selection').html(val);
    $('.service-selection').attr('id', id);

    $('.blocker-time-format').html('Mins');
    $('.blocker-time-format').attr('id', 'mins');
    $('.time-format').html('Mins');
    $('.time-format').attr('id', 'mins');
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

$("#service-list-reserve").on("click","li", function(){

  val = $('.service',this).text();
  id = $('.service',this).attr('id');

    $('.service-selection').html(val);
    $('.service-selection').attr('id', id);

    $('.blocker-time-format').html('Mins');
    $('.blocker-time-format').attr('id', 'mins');
    $('.time-format').html('Mins');
    $('.time-format').attr('id', 'mins');

    $('#reserveModal #patient-tab').removeClass('hide').addClass('show');

    $('#reserveModal #myModalLabel').text("Appointment");

    $('#reserveModal #booking .panel-body #slot-blocker-service').removeClass('show').addClass('hide');
    $('#reserveModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width').addClass('input-width');
    $('#reserveModal #booking .panel-body #Cost-Time-duration').removeClass('hide').addClass('show');

    $('#blocker').removeClass('show').addClass('hide');
    $('#continue').removeClass('hide').addClass('show');


    $('#reserveModal #booking #ok-icon').addClass('glyphicon-ok');
    $('#reserveModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
    $('#reserveModal #booking #ok-icon').removeClass('arrow-color');

  //alert(id);
  getPrcedureDetails();

  });



  // ---- New customer button click function -----

  $("#patient #add-new-customer").click(function(){
    
    $('#myModal #patient .panel-body #new-customer').removeClass('hide').addClass('show');
    $('#myModal #patient .panel-body #search-panel').removeClass('show').addClass('hide');

    $('#customer-name').val('');
    $('#customer-nric').val('');
    $('#phone-code').val('');
    $('#phone-no').val('');
    $('#customer-email').val('');
    $('#customer-address').val('');
    $('#city-name').val('');
    $('#state-name').val('');
    $('#zip-code').val('');

    NRICValidation ();

  });

  // ------------- patients details panel close button functions ------------

  $('#new-customer #close').hover(function () {

        $('#close').css("cursor","pointer");
    }, function () {

    });

$('#new-customer #close').click(function(event) {

    $('#myModal #patient .panel-body #new-customer').removeClass('show').addClass('hide');
    $('#myModal #patient .panel-body #search-panel').removeClass('hide').addClass('show');

    $('#myModal #patient #search-customer').val('');
    $('#error_div2').css('display', 'none');

  });


// --------------------------------------------------------------------------

  $("#appointment-doctor-list li a").click(function(){
  val = $(this).text();
  id = $(this).attr('id');

    $('.doctor-selection').html(val);
    $('.doctor-selection').attr('id', id);
    getEvents();
    popupReset();
    getDoctorProcedure();
    getEnabledDates();

  });

  

  

  $("#select-time-Format li a").click(function(){
  val = $(this).text();
  id = $(this).attr('id');

    $('.time-format').html(val);
    $('.time-format').attr('id', id);

  });

  $("#select-blocker-time-Format li a").click(function(){
  val = $(this).text();
  id = $(this).attr('id');

    $('.blocker-time-format').html(val);
    $('.blocker-time-format').attr('id', id);

  });


  $("#myModal #phone-code-list li").click(function(){
      // console.log("FUCK");
      id = $(this).attr('id');

      $('#phone-code').text(id);
      // $('.clinic-speciality').attr('id', id);

    });

  $("#phone-code-list-reserve li").click(function(){

      id = $(this).attr('id');

      $('#phone-code-reserve').text(id);
      // $('.clinic-speciality').attr('id', id);

    });


// -------------------------------------------------------------------------------------------

$('#mobile-dropdown').on('shown.bs.dropdown', function () {

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

})

// unbind key event when dropdown is hidden
$('#mobile-dropdown').on('hide.bs.dropdown', function () {

    var $this = $(this);

    $this.find("li").each(function(idx,item){

        $(item).addClass("show");
        $(item).removeClass("hide");
    });

    $(document).unbind("keypress");

})

// .............................................................................................
var reserve_trap = 0;
var bookID = 0;

$(document).on('click', '#edit-appointment-details', function(event) {

  $("#dialog").dialog("close");

  getClinicPinStatus();

  if ( gl_clinic_pin_status==1) {
      $('#h-pin_types').val(4);
      veryfiPin();
  }else {
    popupReset();
    $('#next-appointment').hide();
    loadEditDetails();
  }

});

// .........................................................................................................................

  $(document).on('click', '#update-appointment', function(event) {

   var id = $('#h-appointment-id').val();
   // $('#next-appointment').hide();
   var doctorID        = $('.doctor-selection').attr('id');
   var procedureID     = $('.service-selection').attr('id');
   var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
   var time_format     = $('.time-format').attr('id');
   var date            = $('#appointment-date').val();
   var stime           = $('#appointment-time').val();
   // var price           = $('#service-price').val();
   // var price           = stat_price;
   // console.log(stat_price);
   var remarks         = $('#notes').val();

   var name = $('#customer-name').val();
   var nric = $('#customer-nric').val();
   var code = $('#phone-code').text();
   var phone = $('#phone-no').val();
   var email = $('#customer-email').val();
   var address = $('#customer-address').val();
   var city = $('#city-name').val();
   var statate = $('#state-name').val();
   var zip = $('#zip-code').val();

   // ................... validate user ......................
   if(!code) {
    alert('Please select phone area code.');
    return false;
   }
   var er_count = 0;
   var error = '';
   var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

   if (name=='') { error += 'Please insert name!<br>'; er_count++; }
   if (nric=='') { error += 'Please insert nric/fin/passport!<br>'; er_count++; }
   if (code=='') { error += 'Please insert code!<br>'; er_count++; }
   // if (phone=='') { error += 'Please insert phone number!<br>'; er_count++; }
   // if (email=='') { error += 'Please insert email!<br>'; er_count++; }
   if (email == '' || !re.test(email)) { error += 'Please insert valid email!<br>'; er_count++; }

   $('#error_div2').css('display', 'block');
   $('#error2').html(error);
   if (er_count==0) {$('#error_div2').css('display', 'none');} else { return false;}

   if (time_format == 'hours' ){

      duration = Math.floor( duration * 60);

    }

   $('#update-appointment').text('Processing ...');

   $.ajax({
      url: base_url+'calendar/updateAppointment',
      type: 'POST',
      // dataType: 'json',
      data: {
        user_id: stat_user_id, appointment_id:id, doctorid: doctorID, procedureid:procedureID, duration:duration, bookdate:date, starttime:stime, price: stat_price, remarks:remarks, name:name, nric:nric, code:code, phone:phone, email:email, address:address, city:city, statate:statate, zip:zip },
    })
    .done(function(data) {
      if(data.status == false) {
        $.alert({
            title: 'Alert!',
            content: data.message,
            columnClass: 'col-md-4 col-md-offset-4',
                theme: 'material',
            confirm: function(){
                
            }
        });
        $('#update-appointment').text('Update Appointment');
      } else {
        $('#myModal').modal('hide');
        $('#next-appointment').show();
        $('#update-appointment').text('Update Appointment');
        stat_price = 0;
        getEvents();
      }
      // if (data==0) {
      //   // alert('Double booking not allowed!');
      //   $.alert({
      //       title: 'Alert!',
      //       content: 'Double booking not allowed!',
      //       columnClass: 'col-md-4 col-md-offset-4',
      //           theme: 'material',
      //       confirm: function(){
                
      //       }
      //   });
      //   $('#update-appointment').text('Update Appointment');
      // }else if(data==2){
      //   // alert('Sorry! Clinic is closed.');
      //   $.alert({
      //       title: 'Alert!',
      //       content: 'Sorry! Clinic is closed!',
      //       columnClass: 'col-md-4 col-md-offset-4',
      //           theme: 'material',
      //       confirm: function(){
                
      //       }
      //   });
      //   $('#update-appointment').text('Update Appointment');
      // } else {
      //   $('#myModal').modal('hide');
      //   $('#next-appointment').show();
      //   $('#update-appointment').text('Update Appointment');
      //   stat_price = 0;
      //   getEvents();
      // }

    });


  });

// -------------------------------------------------------------

$(document).on('click', '#update-reserve', function(event) {
    // console.log("IN");
   var id = $('#h-appointment-id').val();
   var doctorID        = $('#reserveModal .doctor-selection').attr('id');
   var procedureID     = $('#reserveModal  .service-selection').attr('id');
   var duration        = $('#service-time-Duration-reserve').val();
   var time_format     = $('#reserveModal  .time-format').attr('id');
   var date            = $('#appointment-date-reserve').val();
   var stime           = $('#appointment-time-reserve').val();
   var price           = $('#service-price-reserve').val();
   var remarks         = $('#notes-reserve').val();

   var name = $('#customer-name').val();
   var nric = $('#customer-nric').val();
   var code = $('#phone-code-reserve').text();
   var phone = $('#phone-no-reserve').val();
   var email = $('#email-reserve').val();
   var address = $('#customer-address').val();
   var city = $('#city-name').val();
   var statate = $('#state-name').val();
   var zip = $('#zip-code').val();

   // if( email == null && phone == null || email == '' && phone == '' ){
    console.log("null this");
   //  $.alert({
   //      title: 'Alert!',
   //      content: 'Please input at least email or phone number!',
   //      columnClass: 'col-md-4 col-md-offset-4',
   //          theme: 'material',
   //      confirm: function(){
            
   //      }
   //  });
   //  return false;
   // }


   if (time_format == 'hours' ){

      duration = Math.floor( duration * 60);

    }
  var bookID = localStorage.getItem('bookID');
  var userID = localStorage.getItem('userID');
  // console.log(bookID);
  // console.log(userID);

   $('#update-reserve').text('Processing ...');
   $('#update-reserve').attr('disabled',true);

   $.ajax({
      url: base_url+'clinic/save-appointment-reserver',
      type: 'POST',
      // dataType: 'json',
      data: {
        userid:userID,
        doctorid: doctorID, 
        bookingid: bookID, 
        procedureid:procedureID, 
        duration:duration, 
        bookdate:date, 
        starttime:stime, 
        price:price, 
        remarks:remarks, 
        code:code, 
        phone:phone, 
        email:email, 
      },
    })
    .done(function(data) {
      if (data==0) {
        // alert('Double booking not allowed!');
        $.alert({
            title: 'Alert!',
            content: 'Double booking not allowed!',
            columnClass: 'col-md-4 col-md-offset-4',
                theme: 'material',
            confirm: function(){
                
            }
        });
        $('#update-reserve').text('Update Appointment');
        $('#update-reserve').attr('disabled',false);
      }else if(data==2){
        // alert('Sorry! Clinic is closed.');
        $.alert({
            title: 'Alert!',
            content: 'Sorry! Clinic is closed!',
            columnClass: 'col-md-4 col-md-offset-4',
                theme: 'material',
            confirm: function(){
                
            }
        });
        $('#update-reserve').text('Update Appointment');
        $('#update-reserve').attr('disabled',false);
      } else {
        $('#reserveModal').modal('hide');
      $('#update-reserve').text('Update Appointment');
      $('#update-reserve').attr('disabled',false);
      getEvents();
      }

    });


  });

// ..............................................................................................................................


$('#pin_cancel').click(function(event) {
 $('#verify_pin').dialog('close');
 $('#pinerror').css('display', 'none');
getEvents();
});


//  pin verification types
  // 1 - new appointment;
  // 2 - resize
  // 3 - drag
  // 4 - edit
  // 5 - delegte
  // 6 - conclude
  // 7 - noshow

$('#pin_confirm').click(function(event) {

  var pin = $('#pin_verification').val();
  var type = $('#h-pin_types').val();

  $.ajax({
      url: base_url+'calendar/validatePin',
      type: 'POST',
      // dataType: 'json',
      data: {
        pin:pin},
    })
    .done(function(data) {

      if(data==1) {
        $('#pinerror').css('display', 'none');
        if (type==1) {// 1 - new appointment;
            $('#verify_pin').dialog('close');
            $('#myModal').modal('show');


        } else if (type==2) {// 2 - resize
          $('#verify_pin').dialog('close');

            if (gl_event_title == 'Blocked') {
                var url = base_url+'calendar/updateOnBlockerDrag';
            } else {
                var url = base_url+'calendar/updateOnDrag';
            }

            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
              $.ajax({
                url: url,
                type: 'POST',
                // dataType: 'json',
                data: {date: gl_date,stime:gl_stime, etime:gl_etime, event_id:gl_event_id },
              })
              .done(function(data) {
                jQuery.unblockUI();
                getEvents()
              });
        }else if (type==3) {// 3 - drag
          $('#verify_pin').dialog('close');

              if (gl_event_title == 'Blocked') {
                  var url = base_url+'calendar/updateOnBlockerDrag';
              } else {
                  var url = base_url+'calendar/updateOnDrag';
              }

            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
              $.ajax({
                url: url,
                type: 'POST',
                // dataType: 'json',
                data: {date: gl_date,stime:gl_stime, etime:gl_etime, event_id:gl_event_id },
              })
              .done(function(data) {
                jQuery.unblockUI();
                getEvents()
              });
        }else if (type==4) {// 4 - edit
          $('#verify_pin').dialog('close');
          popupReset();
          loadEditDetails();
        }else if (type==5) {// 5 - delete
          var appointment_id = $('#h-appointment-id').val();
          $('#verify_pin').dialog('close');
           jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

          $.ajax({
              url: base_url + "calendar/deleteAppointmentDetails",
              type: "POST",
              dataType: 'json',
              data: { appointment_id: appointment_id},
          })
          .done(function(data) {
            if (data != 0) {

              // alert('Booking Deleted...!');
              $.alert({
                  title: 'Alert!',
                  content: 'Booking Deleted !',
                  columnClass: 'col-md-4 col-md-offset-4',
                      theme: 'material',
                  confirm: function(){
                      
                  }
              });
              $("#dialog").dialog("close");
              getEvents();

            } else {

              // alert('Someting went wrong, Please check ....')
              $.alert({
                  title: 'Alert!',
                  content: 'Someting went wrong, Please check !',
                  columnClass: 'col-md-4 col-md-offset-4',
                      theme: 'material',
                  confirm: function(){
                      
                  }
              });

            }

            jQuery.unblockUI();

          });


        }else if (type==6) {// 6 - conclude
          var appointment_id = $('#h-appointment-id').val();
          $('#verify_pin').dialog('close');
          jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

          $.ajax({
              url: base_url + "calendar/concludedAppointment",
              type: "POST",
              dataType: 'json',
              data: { appointment_id: appointment_id},
          })
          .done(function(data) {
            if (data != 0) {

              // alert('...!');
              $.alert({
                  title: 'Alert!',
                  content: 'Booking Concluded !',
                  columnClass: 'col-md-4 col-md-offset-4',
                      theme: 'material',
                  confirm: function(){
                      
                  }
              });
              $("#dialog").dialog("close");
              getEvents();

            } else {
              $.alert({
                  title: 'Alert!',
                  content: 'Someting went wrong, Please check !',
                  columnClass: 'col-md-4 col-md-offset-4',
                      theme: 'material',
                  confirm: function(){
                      
                  }
              });
            }

            jQuery.unblockUI();

          });


        }else if (type==7) {// 7 - noshow
          $('#verify_pin').dialog('close');
          var appointment_id = $('#h-appointment-id').val();
           jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

          $.ajax({
              url: base_url + "calendar/No-ShowAppointment",
              type: "POST",
              dataType: 'json',
              data: { appointment_id: appointment_id},
          })
          .done(function(data) {
            if (data != 0) {

              // alert('...!');
              $.alert({
                  title: 'Alert!',
                  content: 'Booking No Showed !',
                  columnClass: 'col-md-4 col-md-offset-4',
                      theme: 'material',
                  confirm: function(){
                      
                  }
              });
              $("#dialog").dialog("close");
              getEvents();

            } else {
              $.alert({
                  title: 'Alert!',
                  content: 'Someting went wrong, Please check !',
                  columnClass: 'col-md-4 col-md-offset-4',
                      theme: 'material',
                  confirm: function(){
                      
                  }
              });
            }

            jQuery.unblockUI();

          });

        }


      }else{
          $('#pinerror').css('display', 'block');
      }

    });




});


});
var socket = io.connect('https://frozen-bastion-83762.herokuapp.com/');
// end of jquery////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// view day
function displayDay( ) {

  // $('#calendar').fullCalendar({
		// 	defaultView: 'agendaDay',
		// 	defaultDate: '2016-01-07',
		// 	editable: true,
		// 	selectable: true,
		// 	eventLimit: true, // allow "more" link when too many events
		// 	header: {
		// 		left: 'prev,next today',
		// 		center: 'title',
		// 		right: 'agendaDay,agendaTwoDay,agendaWeek,month'
		// 	},
		// 	views: {
		// 		agendaTwoDay: {
		// 			type: 'agenda',
		// 			duration: { days: 2 },

		// 			// views that are more than a day will NOT do this behavior by default
		// 			// so, we need to explicitly enable it
		// 			groupByResource: true

		// 			//// uncomment this line to group by day FIRST with resources underneath
		// 			//groupByDateAndResource: true
		// 		}
		// 	},

		// 	//// uncomment this line to hide the all-day slot
		// 	//allDaySlot: false,

		// 	resources: [
		// 		{ id: 'a', title: 'Room A' },
		// 		{ id: 'b', title: 'Room B', eventColor: 'green' },
		// 		{ id: 'c', title: 'Room C', eventColor: 'orange' },
		// 		{ id: 'd', title: 'Room D', eventColor: 'red' }
		// 	],
		// 	events: [
		// 		{ id: '1', resourceId: 'a', start: '2016-01-06', end: '2016-01-08', title: 'event 1' },
		// 		{ id: '2', resourceId: 'a', start: '2016-01-07T09:00:00', end: '2016-01-07T14:00:00', title: 'event 2' },
		// 		{ id: '3', resourceId: 'b', start: '2016-01-07T12:00:00', end: '2016-01-08T06:00:00', title: 'event 3' },
		// 		{ id: '4', resourceId: 'c', start: '2016-01-07T07:30:00', end: '2016-01-07T09:30:00', title: 'event 4' },
		// 		{ id: '5', resourceId: 'd', start: '2016-01-07T10:00:00', end: '2016-01-07T15:00:00', title: 'event 5' }
		// 	],

		// 	select: function(start, end, jsEvent, view, resource) {
		// 		// console.log(
		// 			'select',
		// 			start.format(),
		// 			end.format(),
		// 			resource ? resource.id : '(no resource)'
		// 		);
		// 	},
		// 	dayClick: function(date, jsEvent, view, resource) {
		// 		// console.log(
		// 			'dayClick',
		// 			date.format(),
		// 			resource ? resource.id : '(no resource)'
		// 		);

		// 	}
		// });
}


function getcalendar(firstDay, defaultView, defaultDuration){



  $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      // scrollTime: "08:00:00",
      defaultView: defaultView,
      editable: true,
      firstDay: firstDay,
      slotDuration: defaultDuration,
      slotLabelInterval: '01:00:00',
      allDaySlot: false,
      // timezone: 'Asia/SingaPore',
      timezone: 'local',
      columnFormat: 'ddd, MMM DD',
      selectable: true,
      selectHelper: true,
      select: selectOnCalendar,
      editable: true,
      nowIndicator:true,
      eventRender: function(event, eventElement){
        // console.log(event);
        if (event.image) {
          eventElement.find("div.fc-content").prepend("<img src='https://mednefits.com/favicon.ico' style='display: inline-block;position: absolute;right: 0; width: 30px; height: 30px; margin: 5px;'>");
        }
      },
      selectConstraint:{
        start: '00:00',
        end: '24:00',
      },

      // minTime: 08:00:00,

      eventDrop: eventdrag,
      eventResize: eventResize,
      eventClick: showDetailsDialog,
      // eventOverlap: false,
      // slotEventOverlap: false,
      eventTextColor: 'black',
      height:'auto',
      contentHeight:'auto',

      // scrollTime: '11:00:00',

      // snapDuration:'00:05:00',


  }); // end of calendar

}

// ......................................................................................


function loadEditDetails() {

  // popupReset();

  $('#myModal #booking .panel-body #slot-blocker-service').removeClass('show');
  $('#myModal #booking .panel-body #slot-blocker-service').addClass('hide');
  $('#myModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width');
  $('#myModal #booking .panel-body #service-lbl').addClass('input-width');
  $('#myModal #booking .panel-body #Cost-Time-duration').removeClass('hide');
  $('#myModal #booking .panel-body #Cost-Time-duration').addClass('show');

  $('#myModal #patient .panel-body #new-customer').removeClass('hide');
  $('#myModal #patient .panel-body #new-customer').addClass('show');
  $('#myModal #patient .panel-body #search-panel').removeClass('show');
  $('#myModal #patient .panel-body #search-panel').addClass('hide');

  $('#reserveModal #booking .panel-body #slot-blocker-service').removeClass('show');
  $('#reserveModal #booking .panel-body #slot-blocker-service').addClass('hide');
  $('#reserveModal #booking .panel-body #service-lbl').removeClass('slot-blocker-width');
  $('#reserveModal #booking .panel-body #service-lbl').addClass('input-width');
  $('#reserveModal #booking .panel-body #Cost-Time-duration').removeClass('hide');
  $('#reserveModal #booking .panel-body #Cost-Time-duration').addClass('show');

  $('#reserveModal #patient .panel-body #new-customer').removeClass('hide');
  $('#reserveModal #patient .panel-body #new-customer').addClass('show');
  $('#reserveModal #patient .panel-body #search-panel').removeClass('show');
  $('#reserveModal #patient .panel-body #search-panel').addClass('hide');

  $('#save-appointment').addClass('hide');
  $('#update-appointment').removeClass('hide');

  $('#blocker-reserve').addClass('hide');
  $('#update-reserve').removeClass('hide');

  $('#tabs .enabledTab').addClass('active');
  $('#booking').addClass('active');
  $('#tabs .disabledTab').removeClass('active');
  $('#patient').removeClass('in active');

  var id = $('#h-appointment-id').val();
  $('.service-selection').attr('id', $('#h-procedure-id').val() );
  $('.service-selection').text( $('#appointment-service-detail').text() );
  // $('#service-price').val( $('#h-procedure-price').val() );
  stat_price = $('#appointment-cost-detail').text();
  // console.log(stat_price);
  $('#service-price-search').val( $('#h-procedure-price').val() );
  // $('#service-price-reserve').val( $('#h-procedure-price').val() );
  $('#service-time-Duration').val( $('#h-procedure-duration').val() );
  $('#service-time-Duration-reserve').val( $('#h-procedure-duration').val() );
  $('#appointment-date').val( $('#appointment-date-lbl').text() );
  $('#appointment-date-reserve').val( $('#appointment-date-lbl').text() );
  $('#appointment-time-reserve').val( $('#h-app-time').val() );
  $('#appointment-time').val( $('#h-app-time').val() );

  var phone = $('#appointment-phone-detail').text();
  var code = $('#h-cus-phone-code').val();
  var length = $('#h-cus-phone-code').val().length;
  // var length = $("#new-customer #phone-code").text().length;
  if(code.indexOf('+') > -1) {
    // console.log('has + sign');
    var phone_code = code;
  } else {
    // console.log('does not have + sign');
    var phone_code = '+' + code.replace(/\s/g,'');
  }

  var PhoneNo = phone.substring(length);

  $('#customer-name').val($('#appointment-customer-detail').text());
  $('#customer-nric').val($('#appointment-nric-detail').text());
  $('#phone-code').text(phone_code);
  $('#phone-code-reserve').text(phone_code);
  $('#phone-no').val(PhoneNo);
  $('#phone-no-reserve').val(PhoneNo);
  $('#customer-email').val($('#appointment-email-detail').text());
  $('#email-reserve').val($('#appointment-email-detail').text());
  $('#customer-address').val($('#h-cus-address').val());
  $('#city-name').val($('#h-cus-city').val())
  $('#state-name').val($('#h-cus-state').val());
  $('#zip-code').val($('#h-cus-zip').val())

  $('#notes').val($('#appointment-note-detail').text())
  $('#notes-reserve').val($('#appointment-note-detail').text())

  $('.time-format').html('Mins');
  $('.time-format').attr('id', 'mins');

  $('#myModal #booking #ok-icon').addClass('glyphicon-ok');
  $('#myModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
  $('#myModal #booking #ok-icon').removeClass('arrow-color');

  $('#reserveModal #booking #ok-icon').addClass('glyphicon-ok');
  $('#reserveModal #booking #ok-icon').removeClass('glyphicon-arrow-right');
  $('#reserveModal #booking #ok-icon').removeClass('arrow-color');

  NRICValidation ();

  if( reserve_trap == 0 ){
    $('#myModal').modal('show');
  }else{
    $('#reserveModal').modal('show');
  }
  
}


function getClinicDetails( )
{
  $.ajax({
    url: base_url + 'clinic/clinic_details/' + $('#clinicID').val(),
    type: 'GET',
  })
  .done(function(data) {
    console.log(data);
    co_paid = data.clinic_type.co_paid; 
  });
}

function setAccountSetting (){

  $.ajax({
    url: base_url + 'calendar/getClinicDetails',
    type: 'POST',
    dataType: 'json',
  })
  .done(function(data) {
    console.log(data);
    var def_day = data.first_day;
    var slot_duration = '00:'+ data.slot_duration +':00';

    if (data.default_view == 1){

      var def_view = 'agendaWeek';
      $('#calender-selection').html("Weekly");
    }
    else if (data.default_view == 2){

      var def_view = 'agendaDay';
      $('#calender-selection').html("Daily");
    }
    else if (data.default_view == 3){

      var def_view = 'month';
      $('#calender-selection').html("Monthly");
    }
    // console.log(def_day, def_view, slot_duration);
    getcalendar (def_day, def_view, slot_duration);
    displayDate();

     getEvents();
  });

}

function eventdrag(event, delta, revertFunc) {

  gl_date = moment(event.start).format('dddd, DD MMMM YYYY');
  gl_stime = moment(event.start).format('h:mm A');
  gl_etime = moment(event.end).format('h:mm A');
  gl_event_id = event.id;
  gl_event_title = event.title;

  var start = new Date(event.start);
  var end = new Date(event.end);

  var overlap = $('#calendar').fullCalendar('clientEvents', function(ev) {
      if( ev == event) {
          return false;
      }
      var estart = new Date(ev.start);
      var eend = new Date(ev.end);

      // return (
      //     ( Math.round(start) > Math.round(estart) && Math.round(start) < Math.round(eend) )
      //     ||
      //     ( Math.round(end) > Math.round(estart) && Math.round(end) < Math.round(eend) )
      //     ||
      //     ( Math.round(start) < Math.round(estart) && Math.round(end) > Math.round(eend) )
      // );

      return (Math.round(estart)/1000 < Math.round(end)/1000 && Math.round(eend) > Math.round(start));
  });

  // console.log(overlap);

  if( overlap[1] ){
    revertFunc();
  }else{
      
    
      if (gl_event_title == 'Blocked') {
          var url = base_url+'calendar/updateOnBlockerDrag';
      } else if(gl_event_title.indexOf('Concluded') !== -1) {
        revertFunc();
        return false;
      } else {
          var url = base_url+'calendar/updateOnDrag';
      }

      getClinicPinStatus();
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure about this change?',
            columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
            confirmButton: 'Yes',
            cancelButton: 'NO',
            confirm: function(){
              if ( gl_clinic_pin_status==1) {
                  $('#h-pin_types').val(3);
                  veryfiPin();
              } else {
                  jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
                    $.ajax({
                      url: url,
                      type: 'POST',
                      // dataType: 'json',
                      data: {date: gl_date,stime:gl_stime, etime:gl_etime, event_id:gl_event_id },
                    })
                    .done(function(data) {
                      jQuery.unblockUI();
                      getEvents()
                    });
              }
            },
            cancel: function(){
              revertFunc();
            }
        });
  }

    

}

// ...................................................................................

function isOverlapping(event) {
    var array = $('#calendar').fullCalendar('clientEvents');
    // console.log(array);
    // console.log(event);
    for(i in array){
      if(array[i].id != event.id){
        if((Date(array[i].start) >= Date(event.end) || Date(array[i].end) <= Date(event.start))){
            return true;
        }
      }
    }
    return false;
}

function eventResize(event, delta, revertFunc) {
  var status = isOverlapping(event);
  // console.log(status);
  // if(status == true) {
  //   alert('Oooops! You are overlapping the other schedule.');
  //   revertFunc();
  //   return false;
  // }
  gl_date = moment(event.start).format('dddd, DD MMMM YYYY');
  gl_stime = moment(event.start).format('h:mm A');
  gl_etime = moment(event.end).format('h:mm A');
  gl_event_id = event.id;
  gl_event_title = event.title;
  // console.log(gl_event_title);
  if (gl_event_title == 'Blocked') {
      var url = base_url+'calendar/updateOnBlockerDrag';
  } else if(gl_event_title.indexOf('Concluded') !== -1) {
    // console.log('cannot update concluded appointment');
    revertFunc();
    return false;
  } else {
      var url = base_url+'calendar/updateOnDrag';
  }


  getClinicPinStatus();
  // alert(etime);
    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure about this change?',
        columnClass: 'col-md-4 col-md-offset-4',
        theme: 'material',
         confirmButton: 'Yes',
          cancelButton: 'NO',
        confirm: function(){
          if ( gl_clinic_pin_status==1) {
            $('#h-pin_types').val(2);
            veryfiPin();

          }else {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Updating...</h1>'});
            $.ajax({
              url: url,
              type: 'POST',
              // dataType: 'json',
              data: {date: gl_date,stime:gl_stime, etime:gl_etime, event_id:gl_event_id },
            })
            .done(function(data) {
              jQuery.unblockUI();
              getEvents()
            });

          }
        },
        cancel: function(){
          revertFunc();
        }
    });
}

// ................................................................................

function displayDate() {

  $.ajax({
    url: base_url + 'calendar/getClinicDetails',
    type: 'POST',
    dataType: 'json',
  })
  .done(function(data) {
    // console.log(data);
    var start_hour = data.start_hour;

    var view = $('#calendar').fullCalendar('getView');
    $('#btn-title').text(view.title);
    highlightCurrentDate();

    $(".fc-body").height($(window).height()-136);

    $('.fc-body').animate({scrollTop:$('tr[data-time="'+start_hour+'"]').position().top}, 1);

    var $myDiv = $('.scroll-div');

    // if ( $myDiv.length){
    //     $('.fc-body').animate({scrollTop:$(".scroll-div").position().top}, 1);
    //   }else{
    //       $('.fc-body').animate({scrollTop:$('tr[data-time="'+start_hour+'"]').position().top}, 1);
    //   }
  });

}

// ................................................................................

function getEvents() {
  // console.log("SiNGLE NING ANIMAS");
  var view = $('#calendar').fullCalendar('getView');
  var current_date = moment(view.start).format("DD-MM-YYYY");

  var doctorID = $('.doctor-selection').attr('id');
   // jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

    $.ajax({
      url: base_url+'calendar/getevent',
      type: 'POST',
      dataType: 'json',
      data: {current_date: current_date,doctorID:doctorID},
    })
    .done(function(data) {
      // console.log(data);
      $('#calendar').fullCalendar('removeEvents');
      // console.log(dayView);
      // if(dayView == true) {
        // displayDay();
        // $('#calendar').fullCalendar( 'addEventSource', data[0]);
        // $('#calendar').fullCalendar( 'addEventSource', data[1]);
      // } else {
        $('#calendar').fullCalendar( 'addEventSource', data);
      // }
      jQuery.unblockUI();

       getGoogleEvents();
       load_appointment_count();
      // setTimeout(function() {
      // }, 1000);

    });

}


function getGoogleEvents() {
  var view = $('#calendar').fullCalendar('getView');

  var current_date = moment(view.start).format("DD-MM-YYYY");
  var doctorID = $('.doctor-selection').attr('id');
   jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br />Updating Calendar</h1>'});

    $.ajax({
      url: base_url+'calendar/getGoogleEvent',
      type: 'POST',
      dataType: 'json',
      data: {current_date: current_date,doctorID:doctorID},
    })
    .done(function(data) {

      // $('#calendar').fullCalendar('removeEvents');
      $('#calendar').fullCalendar( 'addEventSource', data);

      jQuery.unblockUI();
    });

}

// ------------------  Load available procedure for selected doctor  --------------------

function getDoctorProcedure() {

    var docID = $('.doctor-selection').attr('id');
    var clinicID = $('#clinicID').val();

    var corp = localStorage.getItem('corporate-selected');
    // console.log(corp);

    $.ajax({
    url: base_url+'calendar/getDoctorProcedure',
    type: 'POST',
    data: {docID: docID, clinicID:clinicID , corporate: corp },
    })

    .done(function(data) {
      // console.log(data);
      $('#service-list').html(data);
      
      $('.slot-block').html(data);
      $('#service-list-search').html(data);

    });
}

// --------------- Load price and duration for selected procedure ------------------

function getPrcedureDetails() {

  var clinicID = $('#clinicID').val();
  var procedureID = $('.service-selection').attr('id');

  $.ajax({
      url: base_url+'calendar/load-procedure-details',
      type: 'POST',
      dataType: 'json',
      data: {procedureID: procedureID, clinicID:clinicID, corporate : localStorage.getItem('corporate-selected')  },
    })
    .done(function(data) {
      // console.log(data);
      // alert(data.Price);

      $("#service-time-Duration").val(data.Duration);
      $("#service-time-Duration-reserve").val(data.Duration);
      $("#service-price").val(data.Price);
      stat_price = data.Price;
      $("#service-price-search").val(data.Price);
      var currency_type = localStorage.getItem('currency_type');
      console.log(currency_type);
      var str_price = data.Price;
      var new_price = str_price.replace('S$', currency_type + ' ');
      $("#service-price-reserve").val(new_price);

    });

}


// ............... highlight Current Date in calendar Header .................


function highlightCurrentDate(){

  var d = new Date();

  var month = d.getMonth()+1;
  var day = d.getDate();

  var output = d.getFullYear() + '-' + ((''+month).length<2 ? '0' : '') + month + '-' +((''+day).length<2 ? '0' : '') + day;
  // alert(output);

  $("th[data-date*="+output+"]").addClass("header-date");
}

// ...............................................................................

function selectOnCalendar (start, end) {
    // console.log(start);
    var docID = $('.doctor-selection').attr('id');
   var duration = moment.duration(end.diff(start));
    var Minutes = duration.asMinutes();

   var date_display = moment(start).format('dddd, MMMM DD');
   var date = moment(start).format('dddd, MMMM DD YYYY');
   var time = moment(start).format('h:mm A');
   $('#h-duration').val(Minutes);
   $('#appointment-date').val(date);
   $('#appointment-time').val(time);

   $('#appointment-date-reserve').val(date);
   $('#appointment-time-reserve').val(time);

   console.log("THIS" , $('#appointment-date').val(date));

  getClinicPinStatus();
   popupReset();

   // $.ajax({
   //    url: base_url+'calendar/blockUnavailable',
   //    type: 'POST',
   //    // dataType: 'json',
   //    data: {
   //      current_date: date, 'time': time, 'doctorID':docID, year: year},
   //  })
   //  .done(function(data) {
   //    // alert(data);
   //    $('#next-appointment').show();
   //    if (data==0) {

   //      if ( gl_clinic_pin_status==1) {
   //        $('#h-pin_types').val(1);
   //        veryfiPin();
   //      } else {
   //        $('#myModal').modal('show');
   //      }

   //      //$('#myModal').modal('show');

   //    } else if(data==1) {
   //      alert('Doctor holiday, Please use another date!');
   //    }else if(data==2) {
   //      // alert('Back date not allowed, Please use another date!');
   //      dialog = $( "#error-messages" ).dialog({

   //        modal: true,
   //        draggable: false,
   //        resizable: false,
   //        // position: ['center', 'top'],
   //        show: 'blind',
   //        hide: 'blind',
   //        width: 500,
   //        dialogClass: 'dialog-cal-error',

   //      });

   //      $( ".dialog-cal-error .ui-dialog-titlebar-close" ).html( '<i class="glyphicon glyphicon-remove"></i>' );
   //      $('#cal-error').text('Back date not allowed, please select an available time !');


   //    }else {

   //      dialog = $( "#error-messages" ).dialog({

   //        modal: true,
   //        draggable: false,
   //        resizable: false,
   //        // position: ['center', 'top'],
   //        show: 'blind',
   //        hide: 'blind',
   //        width: 570,
   //        dialogClass: 'dialog-cal-error',

   //      });

   //      $( ".dialog-cal-error .ui-dialog-titlebar-close" ).html( '<i class="glyphicon glyphicon-remove"></i>' );
   //      $('#cal-error').text('Doctor is not available, please select an available time (marked in blue)');
   //    }

   //  });
      $.ajax({
          url: base_url + 'check/book_date_resource',
          type: 'POST',
          dataType: 'json',
          data : { doctor_id : docID, start_date: moment(start._d).format('YYYY-MM-DD h:mm:ss a') }
        })
        .done(function(data) {
          if( data.status == 200 ){
            if ( gl_clinic_pin_status==1) {
              $('#h-pin_types').val(1);
              veryfiPin();
            } else {
              $('#myModal').modal('show');
            }
          } else {
            $.toast({
                  text: data.message,
                  showHideTransition: 'slide',
                  icon: 'error',
                  hideAfter : 5000,
                  stack: 1,
                  position : 'bottom-left' 
                  // bgColor : '#1667AC'
              });
          }
          
          jQuery.unblockUI();
      });

};


// ......................................................................................

function saveAppointment() {
  $(document).on('click', '#save-appointment', function(event) {

   var doctorID        = $('.doctor-selection').attr('id');
   var procedureID     = $('.service-selection').attr('id');
   var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
   var time_format     = $('.time-format').attr('id');
   var date            = $('#appointment-date').val();
   var stime           = $('#appointment-time').val();
   // var price           = $('#service-price').val();
   var price           = stat_price;
   var remarks         = $('#notes-single').val();
   // console.log(remarks);
   // console.log("FCJCJ");

   var name = $('#customer-name').val();
   var nric = $('#customer-nric').val();
   var code = $('#phone-code').text();
   var phone = $('#phone-no').val();
   var email = $('#customer-email').val();
   var address = $('#customer-address').val();
   var city = $('#city-name').val();
   var statate = $('#state-name').val();
   var zip = $('#zip-code').val();

   // ................... validate user ......................

   var er_count = 0;
   var error = '';
   var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;

   if (name=='') { error += 'Please insert name!<br>'; er_count++; }
   if (nric=='') { error += 'Please insert nric/fin/passport!<br>'; er_count++; }
   if (code=='') { error += 'Please insert country code!<br>'; er_count++; }
   if (phone=='') { error += 'Please insert phone number!<br>'; er_count++; }
   // if (email=='') { error += 'Please insert email!<br>'; er_count++; }
   if (email == '' || !re.test(email)) { error += 'Please insert valid email!<br>'; er_count++; }
   if(!code) {
    alert('Please select the phone area code.');
    return false;
   }
   $('#error_div2').css('display', 'block');
   $('#error2').html(error);
   if (er_count==0) {$('#error_div2').css('display', 'none');} else { return false;}

   if (time_format == 'hours' ){

      duration = Math.floor( duration * 60);

    }


   $.confirm({
        title: 'Confirm!',
        content: 'Are you sure you want to make this Appointment?',
        columnClass: 'col-md-4 col-md-offset-4',
        theme: 'material',
        confirmButton: 'Yes',
        cancelButton: 'NO',
        confirm: function(){
          $('.save-btn').text('Processing ...');
          $('.save-btn').attr('disabled', true);
           $.ajax({
              url: base_url+'calendar/saveAppointment',
              type: 'POST',
              // dataType: 'json',
              data: {
                doctorid: doctorID, procedureid:procedureID, duration:duration, bookdate:date, starttime:stime, price:price, remarks:remarks, name:name, nric:nric, code:code, phone:phone, email:email, address:address, city:city, statate:statate, zip:zip },
            })
            .done(function(data) {
              $('.save-btn').attr('disabled', false);
              if (data==0) {
                alert('Double booking not allowed');
                $('.save-btn').text('Save Appointment');
              }else if(data==2){
                alert('Sorry! Clinic is closed.');
                $('.save-btn').text('Save Appointment');
              } else {
                $( "#new-customer" ).removeClass('hide');
                $( "#new-customer" ).addClass('show');

                $( "#check-save" ).addClass('hide');
                $( "#check-save" ).removeClass('show');

                $('#myModal').modal('hide');
                $('.save-btn').text('Save Appointment');
                stat_price = 0;
                getEvents();
                getAllUsers();

              }
            });
        },
        cancel: function(){
        }
    });

  });
}

function saveReserveBlocker() {
  $('#reserveModal').on('hidden.bs.modal', function (e) {
    // do something...
    $('#update-reserve').addClass('hide');
    $('#blocker-reserve').removeClass('hide');

    $('#reserveModal .doctor-selection').attr('id');
    $('#reserveModal .service').attr('id');

    $('#reserveModal #service-price-reserve').val(null);
    $('#service-time-Duration-reserve').val(null);
    $('#reserveModal .time-format').attr('id');
    
    $('#notes-reserve').val(null);
     
    $('#email-reserve').val(null);
    $('#phone-no-reserve').val(null);

    $('#phone-code-reserve').text();
  })

  

  $(document).on('click', '#blocker-reserve', function(event) {

   var doctorID        = $('#reserveModal .doctor-selection').attr('id');
   var proID           = $('#reserveModal .service-selection').attr('id');

   var price           = $('#reserveModal #service-price-reserve').val();
   var duration        = $('#service-time-Duration-reserve').val();
   var time_format     = $('#reserveModal .time-format').attr('id');

   var stime           = $('#appointment-time-reserve').val();
   var date            = $('#appointment-date-reserve').val();
   var remarks         = $('#notes-reserve').val();
   
   var email         = $('#email-reserve').val();
   var phone         = $('#phone-no-reserve').val();
   if(phone) {
    var code         = $('#phone-code-reserve').text();
   } else {
    var code = '';
   }
   var name = $('#name-reserve').val();
   // var remarks         = $('#notes-reserve').val();
   // console.log(proID);
   if(!proID){
    // console.log("null this");
    $.alert({
        title: 'Alert!',
        content: 'Please Select a  Service.',
        columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
        confirm: function(){
            
        }
    });
    return false;
   }

   if(!name){
    // console.log("null this");
    $.alert({
        title: 'Alert!',
        content: 'Please put a name for the reserver.',
        columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
        confirm: function(){
            
        }
    });
    return false;
   }

   if(!price) {
      $.alert({
        title: 'Alert!',
        content: 'Please put a price of the service.',
        columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
        confirm: function(){
            
        }
    });
      return false;
   }

   if(!duration) {
      $.alert({
        title: 'Alert!',
        content: 'Please put a duration of the service.',
        columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
        confirm: function(){
            
        }
    });
      return false;
   }

  if (time_format == 'hours' ){
    duration = Math.floor( duration * 60);
  }

   $.confirm({
        title: 'Confirm!',
        content: 'Are you sure you want to add Reserve Blocker?',
        columnClass: 'col-md-4 col-md-offset-4',
        theme: 'material',
         confirmButton: 'Yes',
          cancelButton: 'NO',
        confirm: function(){
          $('#blocker-reserve').text('Processing ...');
          $('#blocker-reserve').attr('disabled',true);
          $.ajax({
             url: base_url+'clinic/save-appointment-reserver',
             type: 'POST',
             // dataType: 'json',
             data: {
               doctorid: doctorID, 
               procedureid: proID, 
               duration:duration, 
               bookdate:date, 
               starttime:stime,
               remarks:remarks,
               price:price,
               email:email,
               phone:phone,
               code:code,
               name: name
             },
           })
           .done(function(data) {
              // console.log(data);
              if (data==0) {
                alert('Double booking not allowed');
                $('#blocker-reserve').text('Save Blocker');
                $('#blocker-reserve').attr('disabled',false);
              }else if(data==2){
                alert('Sorry! Clinic is closed.');
                $('#blocker-reserve').text('Save Blocker');
                $('#blocker-reserve').attr('disabled',false);
              } else {
                $('#reserveModal').modal('hide');
              $('#blocker-reserve').text('Save Blocker');
              $('#blocker-reserve').attr('disabled',false);
              getEvents();

              }


           });
        },
        cancel: function(){
        }
    });

  

  });

}

function saveBlocker() {

  $(document).on('click', '#blocker', function(event) {

   var doctorID        = $('.doctor-selection').attr('id');
   var duration        = $('#block-time-Duration').val();
   var time_format     = $('.blocker-time-format').attr('id');
   var stime           = $('#appointment-time').val();
   var date            = $('#appointment-date').val();
   var remarks         = $('#notes').val();

   var er_count = 0;
   var error = '';


   if (duration=='') {error += 'Please insert a duration!<br>'; er_count++;}
   if (date=='') {error += 'Please select a date!<br>'; er_count++;}
   if (stime=='') {error += 'Please select a time!<br>'; er_count++;}
   $('#error_div1').css('display', 'block');
   $('#error1').html(error);
   if (er_count==0) {$('#error_div1').css('display', 'none');} else { return false;}

   if (time_format == 'hours' ){
      duration = Math.floor( duration * 60);
    }

    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure you want to add Blocker?',
        columnClass: 'col-md-4 col-md-offset-4',
        theme: 'material',
         confirmButton: 'Yes',
          cancelButton: 'NO',
        confirm: function(){
          $('#blocker-reserve').text('Processing ...');
          $.ajax({
             url: base_url+'calendar/saveBlocker',
             type: 'POST',
             // dataType: 'json',
             data: {
               doctorid: doctorID, duration:duration, bookdate:date, starttime:stime,remarks:remarks},
           })
           .done(function(data) {

              if (data==0) {
                alert('Double booking not allowed');
                $('#blocker-reserve').text('Save Blocker');
              }else if(data==2){
                alert('Sorry! Clinic is closed.');
                $('#blocker-reserve').text('Save Blocker');
              } else {
                $('#myModal').modal('hide');
              $('#blocker-reserve').text('Save Blocker');
              getEvents();

              }


           });
        },
        cancel: function(){
        }
    });

  });

}


function popupValidation(){

  $(document).on('click', '#continue', function(event) {

   var doctorID        = $('.doctor-selection').attr('id');
   var procedureID     = $('.service-selection').attr('id');
   var duration        = (procedureID==0)? $('#block-time-Duration').val() : $('#service-time-Duration').val();
   var date            = $('#appointment-date').val();
   var stime            = $('#appointment-time').val();
   // var price           = $('#service-price').val();
   var price           = stat_price;
   // console.log(price);
   var remarks         = $('#notes').val();
   var default_time    = $('#h-datetime').val();

   var name = $('#customer-name').val();
   var nric = $('#customer-nric').val();
   var code = $('#phone-code').val();
   var phone = $('#phone-no').val();
   var email = $('#customer-email').val();
   var address = $('#customer-address').val();
   var city = $('#city-name').val();
   var statate = $('#state-name').val();
   var zip = $('#zip-code').val();

   var er_count = 0;
   var error = '';


   if (procedureID=='') {error += 'Please select a procedure!<br>'; er_count++;}
   if (duration=='') {error += 'Please insert a duration!<br>'; er_count++;}
   // if (price=='' && procedureID!=0) {error += 'Please insert a price!<br>'; er_count++;}
   if (date=='') {error += 'Please select a date!<br>'; er_count++;}
   if (stime=='') {error += 'Please select a time!<br>'; er_count++;}

   $('#error_div1').css('display', 'block');
   $('#error1').html(error);
   if (er_count==0) {
      $('#error_div1').css('display', 'none');
      $('#tabs .enabledTab').removeClass('active');
      $('#booking').removeClass('active');
      $('#tabs .disabledTab').addClass('active');
      $('#patient').addClass('in active');
    }

  });


  // $(document).on('keydown', '#phone-code', function(c) {

  //       if (!(c.keyCode>=96 && c.keyCode<=105) && !(c.keyCode>=48 && c.keyCode<=57) && c.keyCode!=107 && c.keyCode!=8 && c.keyCode!=9) {
  //           return false;
  //       }

  //   });

  $(document).on('keydown', '#phone-no', function(c) {

        // if (String.fromCharCode(c.keyCode).replace(/[^0-9]/g, '') == '') {
        //     return false;
        // }
        if (!(c.keyCode>=96 && c.keyCode<=105) && !(c.keyCode>=48 && c.keyCode<=57) && c.keyCode!=8 && c.keyCode!=9) {
            return false;
        }

    });

  $(document).on('keyup', '#customer-nric', function(c) {

        var NRIC = $('#customer-nric').val();
        var validate = /^[STFG]\d{7}[A-Z]$/igm;

        if (validate.test(NRIC)) {

          $('#nric-valid-icon').addClass('glyphicon-ok');

        }else {

          $('#nric-valid-icon').removeClass('glyphicon-ok');

        }

  });

}



function popupReset() {

  $('.service-selection').attr('id', '');
  $('.service-selection').html('Select a service');
  $('#block-time-Duration').val('');
  $('#service-time-Duration').val('');
  $('#Cost-Time-duration').removeClass('show').addClass('hide');
  $('#notes').val('');

  $('#slot-blocker-service').removeClass('show').addClass('hide');
  $('#service-lbl').removeClass('slot-blocker-width').addClass('input-width');

  $('#customer-name').val('');
  $('#customer-nric').val('');
  $('#phone-code').val('');
  $('#phone-no').val('');
  $('#customer-email').val('');
  $('#customer-address').val('');
  $('#city-name').val('');
  $('#state-name').val('');
  $('#zip-code').val('');
  $('#search-customer').val('');

  $('#tabs .enabledTab').addClass('active');
  $('#booking').addClass('active');
  $('#tabs .disabledTab').removeClass('active');
  $('#patient').removeClass('in active');

  $('#search-panel').addClass('show')
  $('#new-customer').removeClass('show').addClass('hide');
  $('#blocker').removeClass('show').addClass('hide');
  $('#continue').removeClass('hide').addClass('show');

  $('#save-appointment').addClass('hide');
  // $('#save-appointment').removeClass('hide');
  $('#update-appointment').addClass('hide');

  $('#myModal #booking #ok-icon').removeClass('glyphicon-ok');
  $('#myModal #booking #ok-icon').addClass('glyphicon-arrow-right');
  $('#myModal #booking #ok-icon').addClass('arrow-color');

  $('#myModal #patient-tab').removeClass('hide').addClass('show');

  $('#reserveModal #booking #ok-icon').removeClass('glyphicon-ok');
  $('#reserveModal #booking #ok-icon').addClass('glyphicon-arrow-right');
  $('#reserveModal #booking #ok-icon').addClass('arrow-color');

  $('#reserveModal #patient-tab').removeClass('hide').addClass('show');

  $('#error_div2').css('display', 'none');
  $('#error_div1').css('display', 'none');

  $(document).on('click', '#next-appointment', function(event) {
    var a = $("#myModal .doctor-selection").text();
    var b = $("#customer-nric").val();
    var c = $("#myModal .service-selection").text();
    var d = $("#customer-name").val();
    var e = $("#appointment-date").val();
    var f = $("#appointment-time").val();
    var g = $("#customer-email").val();
    var h = $("#customer-phone").val();
    var i = $("#notes-single").val();
    var j = $("#service-price").val();
    // console.log(i);

    $("#doctor-confirm").text(a);
    $("#nric-confirm").text(b);
    $("#procedure-confirm").text(c);
    $("#name-confirm").text(d);
    $("#date-confirm").text(e);
    $("#time-confirm").text(f);
    $("#email-confirm").text(g);
    $("#phone-confirm").text(h);
    $("#notes-confirm").text(i);
    $("#price-confirm").text(stat_price);

    $( "#new-customer" ).removeClass('show');
    $( "#new-customer" ).addClass('hide');

    $( "#check-save" ).addClass('show');
    $( "#check-save" ).removeClass('hide');
  });

  $(document).on('click', '#back-appointment', function(event) {

    $( "#new-customer" ).removeClass('hide');
    $( "#new-customer" ).addClass('show');

    $( "#check-save" ).addClass('hide');
    $( "#check-save" ).removeClass('show');
  });
}


function getAllUsers() {
    $.ajax({  

      url: base_url+'calendar/load-users',
      type: 'POST',
      dataType: 'json',
      data: { userType: 1, user_type: 5, access_type: 1 }, // 1 for normal user type

    })
    .done(function(data) {
    $('#myModal #search-customer').autocomplete({

    lookup: data,
    minChars:5,

    onSelect: function (suggestion) {

      $('#myModal #patient .panel-body #new-customer').removeClass('hide');
      $('#myModal #patient .panel-body #new-customer').addClass('show');

      $('#myModal #patient .panel-body #search-panel').removeClass('show');
      $('#myModal #patient .panel-body #search-panel').addClass('hide');
      // if(suggestion.PhoneCode.indexOf('+') > -1) {
        console.log('has + sign');
      //   var phone_code = suggestion.PhoneCode;
      // } else {
        console.log('does not have + sign');
      //   var phone_code = '+' + suggestion.PhoneCode.replace(/\s/g,'');
      // }
      // $('#new-customer #customer-name').attr('id', '');
      $('#new-customer #customer-name').val(suggestion.Name);
      // $('#new-customer #customer-nric').val(suggestion.NRIC);
      $('#new-customer #phone-code').text(suggestion.PhoneCode);

      var length = $("#new-customer #phone-code").text().length;
      console.log(length);

      var phone = suggestion.PhoneNo;
      var PhoneNo = phone.substring(length);

      $('#new-customer #phone-no').val(PhoneNo);
      $('#new-customer #customer-email').val(suggestion.Email);
      $('#new-customer #customer-address').val(suggestion.Address);
      $('#new-customer #city-name').val(suggestion.City);
      $('#new-customer #state-name').val(suggestion.State);
      $('#new-customer #zip-code').val(suggestion.zip);

      // NRICValidation ();

    }

    });
  });

}

// ......................................................................../

function showDetailsDialog(calEvent) {
  // console.log(calEvent);
  var event_id = calEvent.id;
  var event_title = calEvent.title;
  var status = calEvent.status;

  if(calEvent.type == 4){
    reserve_trap = 1;
    bookID = calEvent.id;

    localStorage.setItem('bookID', bookID);
    localStorage.setItem('userID', calEvent.user_id);
  } else{
    reserve_trap = 0;
  }

  if (event_title=='Blocked') {

      $.ajax({
        url: base_url+'calendar/getExtraEventDetails',
        type: 'POST',
        dataType: 'json',
        data: {
        appointment_id: event_id},

      })
      .done(function(data) {

        $('#blocker-id').val(data.event_id);
        $('#bocker-doctor-detail').html(data.note);
        // $('#blocker-date-lbl').html(data.date);
        $('#blocker-time-lbl').html(data.description);



        dialog = $( "#bocker-dialog" ).dialog({

          modal: true,
          draggable: false,
          resizable: false,
          // position: ['center', 'top'],
          show: 'blind',
          hide: 'blind',
          width: 400,
          dialogClass: 'ui-dialog-osx',

        });

        $( ".ui-dialog-titlebar-close" ).html( '<i class="glyphicon glyphicon-remove"></i>' );

      });


  }else if(event_title=='Google Event'){

      // alert('Google Event');

  }else if(event_title=='Breaks'){

      // alert('Breaks');

  }else if(event_title=='Time Off'){

      // alert('Time Off');

  }else if(event_title=='On a Break'){

      // alert('On a Break');

  }else {

      setTimeout(function() {
        $.ajax({
          url: base_url+'calendar/getAppointmentDetails',
          type: 'POST',
          dataType: 'json',
          data: {
            appointment_id: event_id},
        })
        .done(function(data) {
          // console.log(data);
          $('#h-appointment-id').val(data.appointment_id);
          $('#h-doctor-id').val(data.doctor_id);
          $('#h-procedure-id').val(data.procedure_id);
          $('#h-procedure-duration').val(data.duration);
          // $('#h-procedure-price').val(data.cost);
          stat_price = data.cost;
          stat_user_id = data.user_id;
          // console.log(stat_price);
          $('#h-cus-city').val(data.city);
          $('#h-cus-zip').val(data.zip);
          $('#h-cus-state').val(data.state);
          $('#h-cus-address').val(data.address);
          $('#h-app-time').val(data.time1);
          $('#h-cus-phone-code').val(data.phoneCode);


          $('#appointment-doctor-detail').html(data.doctor);
          $('#appointment-service-detail').html(data.procedure);
          $('#appointment-cost-detail').html(data.cost);
          $('#appointment-customer-detail').html(data.customer);
          $('#appointment-nric-detail').html(data.nric);
          $('#appointment-email-detail').html(data.email);
          $('#appointment-phone-detail').html(data.phone);
          $('#appointment-date-lbl').html(data.date);
          $('#appointment-time-lbl').html(data.time);

          if (data.note != ''){
            $('#Appoit-note').html('<td class="col-sm-2" style="vertical-align: top;">Note</td><td class="col-sm-8" id="appointment-note-detail">'+data.note+'</td>');
          }
          else{
            $('#Appoit-note').html('');
          }


          if (status=='Concluded' || status=='No Show') {

            $('.hide-buttons').addClass('hide');
          }
          else{
            $('.hide-buttons').removeClass('hide');
          }



          dialog = $( "#dialog" ).dialog({

            modal: true,
            draggable: false,
            resizable: false,
            // position: ['center', 'top'],
            show: 'blind',
            hide: 'blind',
            width: 400,
            dialogClass: 'ui-dialog-osx',

                });

          $( ".ui-dialog-titlebar-close" ).html( '<i class="glyphicon glyphicon-remove"></i>' );

        });
      }, 100);

  }


}

// -------------- Delete Blocker Event -------------------


function deleteExtraEvent() {

  $(document).on('click', '#bocker-delete', function(event) {

    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure you want to delete this Event ?',
        columnClass: 'col-md-4 col-md-offset-4',
        theme: 'material',
         confirmButton: 'Yes',
          cancelButton: 'NO',
        confirm: function(){
          var event_id = $('#blocker-id').val();

            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

            $.ajax({
                url: base_url+'calendar/deleteBlockerDetails',
                type: 'POST',
                dataType: 'json',
                data: { Event_id: event_id},
              })
              .done(function(data) {

              });

            jQuery.unblockUI();
            $("#bocker-dialog").dialog("close");
            getEvents();
        },
        cancel: function(){
        }
    });



  });

}



function DeleteAppointment() {

  getClinicPinStatus();
    jQuery("#delete-appointment-details").click(function () {

        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure you want to Delete this Appointment ?',
            columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
             confirmButton: 'Yes',
              cancelButton: 'NO',
            confirm: function(){
              if (gl_clinic_pin_status==1) {
                $("#dialog").dialog("close");
                  $('#h-pin_types').val(5);
                  veryfiPin();
              } else {

                  var appointment_id = $('#h-appointment-id').val();

                   jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

                  $.ajax({
                      url: base_url + "calendar/deleteAppointmentDetails",
                      type: "POST",
                      dataType: 'json',
                      data: { appointment_id: appointment_id},
                  })
                  .done(function(data) {
                    if (data != 0) {

                      alert('Booking Deleted...!');
                      $("#dialog").dialog("close");
                      getEvents();

                    } else {

                      alert('Someting went wrong, Please check ....')
                    }

                    jQuery.unblockUI();

                  });

              }
            },
            cancel: function(){
            }
        });


    });
}

function ConcludedAppointment() {

getClinicPinStatus();
var credit_use_status;
var appointment_id;
var transaction_id;
var transaction;
var total;
var wallet_use;
var summary;
var amount_bill;
    jQuery("#concluded-appointment").click(function () {

        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure you want to Conclude this Appointment ?',
            columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
             confirmButton: 'Yes',
              cancelButton: 'NO',
            confirm: function(){
              if (gl_clinic_pin_status==1) {
                $("#dialog").dialog("close");
                  $('#h-pin_types').val(6);
                  veryfiPin();
              } else {

                    appointment_id = $('#h-appointment-id').val();

                      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
                      // calendar/concludedAppointment
                      $.ajax({
                          url: base_url + "clinic/appointment/transaction",
                          type: "POST",
                          dataType: 'json',
                          data: { appointment_id: appointment_id},
                      })
                      .done(function(data) {
                        // console.log(data);

                        if(data == 0) {
                          alert('Booking Concluded...!');
                          $("#dialog").dialog("close");
                          getEvents();
                        } else if(data != 0) {
                          $(".appointment-details").hide();
                          // $('#user-credit-balance').text(data.deducted);
                          if(co_paid == 1) {
                            $(".balance-co-paid").fadeIn();
                          } else {
                            $(".balance").fadeIn();
                          }
                          // $('#user-procedure-cost').text(data.procedure_cost);
                          transaction_id = data.transaction.transaction_id;
                          if(parseInt(data.transaction.balance) == 0) {
                            credit_use_status = 0
                          } else {
                            credit_use_status = 1;
                          }

                          // if(data.status == true) {
                          // } else if(data.status == false) {
                            // amount_bill = data.transaction.procedure_cost;
                            // $('#calc-bill').click();
                          // }
                        } else {
                          alert('Someting went wrong, Please check ....')
                        }

                        jQuery.unblockUI();

                      });

              }
            },
            cancel: function(){
            }
        });

    });


    // jQuery("#concluded-appointment").click(function () {
    //   $(".appointment-details").hide();
    //   $(".balance").fadeIn();
        
    // });

    jQuery("#calc-bill-co-paid").click(function () {
      if($('#bill_amount_co_paid').val() > 1) {
        amount_bill = $('#bill_amount_co_paid').val();
        console.log($('#bill_amount_co_paid').length);
      } else {
        alert('Please enter Total Medication Bill.');
        return false;
      }

      console.log('yearh');
      $.ajax({
            url: base_url + "clinic/transaction_co_paid/calculate",
            type: "POST",
            dataType: 'json',
            data: { id: transaction_id, amount: amount_bill},
      })
      .done(function(data) {
        // console.log(data);
        if(data) {
          transaction = data;
          // total = data.total;
          // wallet_use = data.wallet_use;
          // summary = data.summary;
          $(".balance-co-paid").hide();
          $(".summary-receipt-co-paid").fadeIn();
          $(".ui-dialog").css({ top: '-100px' });  
          $(".cancel1").hide();
          $(".cancel2").show();
          $('#client_name_co_paid').text(data.name);
          $('#nric_co_paid').text(data.nric);
          $('#procedure_co_paid').text(data.procedure);
          $('#date_co_paid').text(data.date);
          $('#time_co_paid').text(data.time);
          $('#total_amount_co_paid').text(data.total_amount);
          $('#final_bill_co_paid').text(data.total_bill);
          $('#deducted_co_paid').text(data.medi_credit);
          // $('#clinic_discount').text(data.clinic_discount);
          // $('#mednefits_discount').text(data.medi_percent); 
        }

        jQuery.unblockUI();
      });

    });

    jQuery("#calc-bill").click(function () {

      if($('#bill_amount_single').val() > 1) {
        amount_bill = $('#bill_amount_single').val();
        console.log($('#bill_amount_single').length);
      } else {
        console.log('ayya');
        alert('Please enter Total Bill.');
        return false;
      }

      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        // console.log(amount_bill);
      $.ajax({
            url: base_url + "clinic/transaction/calculate",
            type: "POST",
            dataType: 'json',
            data: { id: transaction_id, amount: amount_bill, credit_use_status: credit_use_status},
      })
      .done(function(data) {
        // console.log(data);
        if(data) {
          transaction = data;
          // total = data.total;
          // wallet_use = data.wallet_use;
          // summary = data.summary;
          $(".balance").hide();
          $(".summary-receipt").fadeIn();
          $(".ui-dialog").css({ top: '-100px' });  
          $(".cancel1").hide();
          $(".cancel2").show();
          $('#client_name').text(data.name);
          $('#nric').text(data.nric);
          $('#procedure').text(data.procedure);
          $('#date').text(data.date);
          $('#time').text(data.time);
          $('#total_amount').text(data.total_amount);
          $('#final_bill').text(data.total_bill);
          $('#deducted').text(data.medi_credit);
          $('#clinic_discount').text(data.clinic_discount);
          $('#mednefits_discount').text(data.medi_percent); 
        }

        jQuery.unblockUI();
      });
    });
    
    jQuery("#finish-transaction-co-paid").click(function () {
      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
       $.ajax({
            url: base_url + "clinic/transaction/finish",
            type: "POST",
            dataType: 'json',
            data: { 
              transaction_id: transaction.transaction_id,
              total_amount: transaction.total_amount,
              user_id: transaction.UserID,
              final_bill: transaction.total_bill,
              wallet_id: transaction.wallet_id,
              appointment_id: transaction.appointment_id,
              credit: transaction.credit,
              name: transaction.name,
              nric: transaction.nric,
              procedure: transaction.procedure,
              date: transaction.date,
              time: transaction.time,
              // total_amount: summary.total_amount,
              // credit_use_status: wallet_use,
              email: transaction.email,
              credit_deducted: transaction.medi_credit,
              // final_bill: total.final_bill,
              doctorid: transaction.DoctorID,
              price: transaction.total_amount,
              procedureid: transaction.ProcedureID
            },
        })
        .done(function(data) {
          // console.log(data);
          $('#bill_amount_co_paid').val("");
          $(".appointment-details").fadeIn();
          $(".balance-co-paid").hide();
          $(".summary-receipt-co-paid").hide();

          $(".ui-dialog").css({ top: '-9px' })
          $(".cancel1").show();
          $(".cancel2").hide();


          alert('Booking Concluded...!');
          $("#dialog").dialog("close");
          getEvents();
          jQuery.unblockUI();
        });

    });

    jQuery("#finish-transaction").click(function () {
      jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
       $.ajax({
            url: base_url + "clinic/transaction/finish",
            type: "POST",
            dataType: 'json',
            data: { 
              transaction_id: transaction.transaction_id,
              total_amount: transaction.total_amount,
              user_id: transaction.UserID,
              final_bill: transaction.total_bill,
              wallet_id: transaction.wallet_id,
              appointment_id: transaction.appointment_id,
              credit: transaction.credit,
              name: transaction.name,
              nric: transaction.nric,
              procedure: transaction.procedure,
              date: transaction.date,
              time: transaction.time,
              // total_amount: summary.total_amount,
              // credit_use_status: wallet_use,
              email: transaction.email,
              credit_deducted: transaction.medi_credit,
              // final_bill: total.final_bill,
              doctorid: transaction.DoctorID,
              price: transaction.total_amount,
              procedureid: transaction.ProcedureID
            },
        })
        .done(function(data) {
          // console.log(data);
          $('#bill_amount').val("");
          $(".appointment-details").fadeIn();
          $(".balance").hide();
          $(".summary-receipt").hide();

          $(".ui-dialog").css({ top: '-9px' })
          $(".cancel1").show();
          $(".cancel2").hide();


          alert('Booking Concluded...!');
          $("#dialog").dialog("close");
          getEvents();
          jQuery.unblockUI();
        });

    });

    jQuery(".calc-cancel").click(function () {
      $(".appointment-details").fadeIn();
      $(".balance").hide();
      $(".summary-receipt").hide();

      $(".ui-dialog").css({ top: '-9px' })
      $(".cancel1").show();
      $(".cancel2").hide();
    });
}

function NoShowAppointment() {

  getClinicPinStatus();
    jQuery("#no-show-appointment-details").click(function () {

          $.confirm({
            title: 'Confirm!',
            content: 'Are you sure you want to No Show this Appointment ?',
            columnClass: 'col-md-4 col-md-offset-4',
            theme: 'material',
             confirmButton: 'Yes',
              cancelButton: 'NO',
            confirm: function(){
              
              if (gl_clinic_pin_status==1) {
                $("#dialog").dialog("close");
                  $('#h-pin_types').val(7);
                  veryfiPin();
              } else {

                  var appointment_id = $('#h-appointment-id').val();
                 jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});

                $.ajax({
                    url: base_url + "calendar/No-ShowAppointment",
                    type: "POST",
                    dataType: 'json',
                    data: { appointment_id: appointment_id},
                })
                .done(function(data) {
                  if (data != 0) {

                    alert('Booking No Showed...!');
                    $("#dialog").dialog("close");
                    getEvents();

                  } else {

                    alert('Someting went wrong, Please check ....')
                  }

                  jQuery.unblockUI();

                });

              }
            },
            cancel: function(){
            }
        });


    });
}


function veryfiPin() {
  $('#pin_verification').val('');
  dialog = $( "#verify_pin" ).dialog({

          modal: true,
          draggable: false,
          resizable: false,
          // position: ['center', 'top'],
          show: 'blind',
          hide: 'blind',
          width: 500,
          dialogClass: 'ui-dialog-pin',

        });
}

function NRICValidation (){

    var NRIC = $('#customer-nric').val();
    var validate = /^[STFG]\d{7}[A-Z]$/igm;

      if (validate.test(NRIC)) {

        $('#nric-valid-icon').addClass('glyphicon-ok');

      }else {

        $('#nric-valid-icon').removeClass('glyphicon-ok');

      }

}



function getClinicPinStatus() {

    $.ajax({
            url: base_url+'calendar/getClinicPinStatus',
            type: 'POST',
          })
          .done(function(data) {
            gl_clinic_pin_status = data;
          });


    // return status;
}




// nhr 2016-07-13 rel time update calendar

function load_appointment_count() {
       $.ajax({
            url: base_url+'calendar/loadAppointmentCount',
            type: 'POST',
          })
          .done(function(data) {
            $('#appCount').val(data);
          })
}

socket.on('clinic', function (clinic) {
  var clinicID = $('#clinicID').val();
    if(clinic == clinicID) {
      display_new_appintment();
    }
  });

function display_new_appintment() {
     // setInterval(function(){
       $.ajax({
            url: base_url+'calendar/loadAppointmentCount',
            type: 'POST',
          })
          .done(function(data) {
            var org_count = $('#appCount').val();
            var new_count = data;
            var diff = new_count - org_count;
            var text = diff + " New Appointments added, <a href='"+base_url+"clinic/appointment-home-view'>click here to update </a>";
            if (diff > 0) {
              $.toast({
                text: text,
                showHideTransition: 'slide',
                icon: 'info',
                hideAfter : false,
                stack: 1,
                bgColor : '#1667AC'
              });
              // $('#new_Appointment_notification').css('display', 'block');
              // $('#notify_text').html(diff+' New Appointments added, click here to update')
            }
          })


    // }, 30000);

}
