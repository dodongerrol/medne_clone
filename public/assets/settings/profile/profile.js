jQuery(document).ready(function($) {
  // var protocol = jQuery(location).attr('protocol');
  // var hostname = jQuery(location).attr('hostname');
  // var folderlocation = $(location).attr('pathname').split('/')[1];
  // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
  window.base_url = window.location.origin + "/app/";

  // --------------------  Page onload default selection  --------------- //

  loadClinicDetails();

  GetClinicHolyday();

  addClinicHolyday();

  UpdateClinicHolyday();

  DeleteClinicHolyday();

  // --------------------------------------------------------------------

  $("#clinic-details").click(function(event) {
    $.ajax({
      url: base_url + "setting/profile/ajaxGetClinicDetailPanel",
      type: "post"
    })
    .done(function(data) {
      $('.operatingHours-div').css('display', 'none');
      $("#profile-detail-wrapper").html(data);
      $("#profile-detail-wrapper").css('display', 'inline-block');
    });

    $("#Configure-list div b").css("color", "#777676");
    $("#Integrate-list div b").css("color", "#777676");
    $(this).css("color", "black");
  });

  // --------------------------------------------------------------------

  $("#clinic-hours").click(function(event) {
    $('.clinic-detail-container').css('display', 'none');
    $('.operatingHours-div').css('display', 'inline-block');
    $('#profile-breakHours-savebreakHours').css('display', 'none');
    $('#profile-detail-wrapper').css('display', 'none');
    $("#Configure-list div b").css("color", "#777676");
    $("#Integrate-list div b").css("color", "#777676");
    $('#clinic-hours').css("color", "black");
    $('#clinic-hours-tab').click();
  });

  $("#clinic-hours-tab").click(function(event) {
    $('#profile-breakHours-savebreakHours').css('display', 'none');
  });

  // --------------------------------------------------------------------

  $("#clinic-payment-details").click(function(event) {
    $.ajax({
      url: base_url + "setting/profile/ajaxGetPaymentDetails",
      type: "post"
    })
    .done(function(data) {
      $("#profile-detail-wrapper").css('display', 'inline-block');
      $("#profile-detail-wrapper").html(data);
      getBankDetails();
      $('.operatingHours-div').css('display', 'none');
      // $( "#clinic-hours-tab" ).trigger( "click" );
    });

    $("#Configure-list div b").css("color", "#777676");
    $("#Integrate-list div b").css("color", "#777676");
    $(this).css("color", "black");
  });

  // --------------------------------------------------------------------

  $("#clinic-password").click(function(event) {
    $.ajax({
      url: base_url + "setting/profile/ajaxGetclinicPasswordPanel",
      type: "post"
    })
    .done(function(data) {
      $("#profile-detail-wrapper").css('display', 'inline-block');
      $("#profile-detail-wrapper").html(data);
      // $("#clinic-hours-tab").trigger("click");
      $('.operatingHours-div').css('display', 'none');
    });

    $("#Configure-list div b").css("color", "#777676");
    $("#Integrate-list div b").css("color", "#777676");
    $(this).css("color", "black");
  });

  // --------------------------------------------------------------------

  $("#website").click(function(event) {
    $.ajax({
      url: base_url + "setting/profile/ajaxGetWebsitePanel",
      type: "post"
    })
    .done(function(data) {
      $("#profile-detail-wrapper").css('display', 'inline-block');
      $("#profile-detail-wrapper").html(data);
      $('.operatingHours-div').css('display', 'none');
    }); 
    $("#Configure-list div b").css("color", "#777676");
    $("#Integrate-list div b").css("color", "#777676");
    $(this).css("color", "black");
  });

  $("#qr").click(function(event) {
    $.ajax({
      url: base_url + "setting/profile/ajaxGetQRPage",
      type: "post"
    })
    .done(function(data) {
      $("#profile-detail-wrapper").css('display', 'inline-block');
      $("#profile-detail-wrapper").html(data);
      $('.operatingHours-div').css('display', 'none');
    });

    $("#Configure-list div b").css("color", "#777676");
    $("#Integrate-list div b").css("color", "#777676");
    $(this).css("color", "black");
  });

  // --------------------------------------------------------------------

  $("#social").click(function(event) {
    $.ajax({
      url: base_url + "setting/profile/ajaxGetSocialPlugPanel",
      type: "post"
    })
    .done(function(data) {
      $("#profile-detail-wrapper").css('display', 'inline-block');
      $("#profile-detail-wrapper").html(data);
    });

    $("#Configure-list div b").css("color", "#777676");
    $("#Integrate-list div b").css("color", "#777676");
    $(this).css("color", "black");
  });

  // -------------------- load clinic breaks tab page --------------------

  $(document).on("click", "#clinic-breaks-tab", function(event) {
    $('.clinic-detail-container').css('display', 'none');
    $('#profile-breakHours-savebreakHours').css('display', 'inline-block');
  });

  // -------------------- load clinic time off tab page --------------------

  $(document).on("click", "#clinic-time_off-tab", function(event) {
    $.ajax({
      url: base_url + "setting/profile/ajaxGetClinicTimeOffTab",
      type: "POST"
    }).done(function(data) {
      $("#clinic-time_off-main").html(data);
      $('.clinic-breaks-main').css('display', 'none');
    });
  });

  // -------------------------------------------------------------------

  $(document).on("click", "#clinic-phone-codes li", function(event) {
    // val = $(this).text();
    id = $(this).attr("id");

    $("#clinic-phone-code").text(id);
  });

  // -------------------------------------------------------------------

  $(document).on("click", "#clinic-type-list li a", function(event) {
    val = $(this).text();
    id = $(this).attr("id");

    $("#clinic-service-name").text(val);
    $(".clinic-speciality").attr("id", id);
  });

  // -------------------------------------------------------------------

  $(document).on("click", "#clinic-MRT-list li", function(event) {
    id = $(this).attr("id");

    $("#MRT-name").text(id);
    $(".clinic-MRT").attr("id", id);
    $("#MRT-name").css("color", "#686868");
  });

  // -------------------------------------------------------------------

  $(document).on("keydown", "#clinic-Phone-code", function(c) {
    if (
      !(c.keyCode >= 96 && c.keyCode <= 105) &&
      !(c.keyCode >= 48 && c.keyCode <= 57) &&
      c.keyCode != 107 &&
      c.keyCode != 8 &&
      c.keyCode != 9
    ) {
      return false;
    }
  });

  $(document).on("keydown", "#clinic-Phone", function(c) {
    if (
      !(c.keyCode >= 96 && c.keyCode <= 105) &&
      !(c.keyCode >= 48 && c.keyCode <= 57) &&
      c.keyCode != 8 &&
      c.keyCode != 9
    ) {
      return false;
    }
  });

  // ----------------------- Update Bank Details -------------------------------

  $(document).on("click", "#update-payment-details-btn", function(event) {
    var clinic_id = $("#clinicID").val();
    var bank_name = $("#bank-name").val();
    var billing_address = $("#billing-address").val();
    var bank_type = $("#bank-type").val();
    var bank_num = $("#bank-number").val();

    if (bank_name == "") {
      $("#bank-name").addClass("input-error");
      return false;
    } else if (billing_address == "") {
      $("#billing-address").addClass("input-error");
      return false;
    }
    if (bank_type == "") {
      $("#bank-type").addClass("input-error");
      return false;
    }
    if (bank_num == "") {
      $("#bank-number").addClass("input-error");
      return false;
    }
    var invoice_status = $("#invoice_status").val();
    console.log(invoice_status);
    console.log(typeof invoice_status);

    $.ajax({
      url: base_url + "clinic/update/bank_details",
      type: "post",
      data: {
        partner_id: clinic_id,
        billing_address: billing_address,
        bank_account_type: bank_type,
        bank_account_number: bank_num,
        bank_name: bank_name
      }
    }).done(function(data) {
      if (invoice_status == 1) {
        $("#transaction-invoice").click();
      }
      $("#alert_box").css("display", "block");
      $("#alert_box").html("Updating...");

      setTimeout(function() {
        $("#alert_box").css("display", "none");
        // $( "#clinic-details" ).trigger( "click" );

        var text = "Clinic Details Updated !";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "success",
          // hideAfter : false,
          stack: 1
          // bgColor : '#1667AC'
        });
      }, 3000);
    });
  });

  // ----------------------- Update Clinic Details -------------------------------

  $(document).on("click", "#btn-clinic-detail-update", function(event) {

    var name = $("#cinic-name").val();
    var speciality = $(".clinic-speciality").attr("id");
    var address = $("#clinic-address").val();
    var street = $("#clinic-street").val();
    var state = $("#clinic-state").val();
    var country = $("#clinic-country").val();
    var postal_code = $("#clinic-postal_code").val();
    var description = $("#clinic-description").val();
    var code = $("#clinic-phone-code").text();
    var Phone = $("#clinic-Phone").val();
    var district = $("#clinic-district").val();
    var MRT = $(".clinic-MRT").attr("id");
    var email = $("#clinic-email").val();
    var website = $("#clinic-website").val();
    var titel = $("#clinic-authorize").val();
    var message = $("#clinic-Msg").val();
    var lng = $("#clinic-lng").val();
    var lat = $("#clinic-lat").val();
    var communication_email = $("#clinic-communication-email").val();
    var image = $(".clinic-image").attr("src");

    // -------------- valid input fields --------------- //

    var mail_valid = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/gim;
    var phone_valid = /[0-9 -()+]+$/;

    if (name == "") {
      $("#cinic-name").addClass("input-error");
      return false;
    } else {
      $("#cinic-name").removeClass("input-error");
    }

    if (address == "") {
      $("#clinic-address").addClass("input-error");
      return false;
    } else {
      $("#clinic-address").removeClass("input-error");
    }

    if (Phone == "" || !phone_valid.test(Phone)) {
      $("#clinic-Phone").addClass("input-error");
      return false;
    } else {
      $("#clinic-Phone").removeClass("input-error");
    }

    // if (email == "" || !mail_valid.test(email)) {
    //   $("#clinic-email").addClass("input-error");
    //   console.log('error clinic-email');
    //   return false;
    // } else {
    //   $("#clinic-email").removeClass("input-error");
    // }

    $.ajax({
      url: base_url + "setting/profile/Update-Clinic-Details",
      type: "post",
      data: {
        name: name,
        speciality: speciality,
        address: address,
        street: street,
        state: state,
        country: country,
        postal: postal_code,
        description: description,
        code: code,
        Phone: Phone,
        district: district,
        MRT: MRT,
				email: email,
				communication_email: communication_email,
        website: website,
        titel: titel,
        message: message,
        image: image,
        lng: lng,
        lat: lat
      }
    }).done(function(data) {
      $("#alert_box").css("display", "block");
      $("#alert_box").html("Updating...");

      setTimeout(function() {
        $("#alert_box").css("display", "none");
        $("#clinic-details").trigger("click");

        var text = "Clinic Details Updated !";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "success",
          // hideAfter : false,
          stack: 1
          // bgColor : '#1667AC'
        });
      }, 3000);
    });
  });

  // ----------------------- Upload Clinic Profile Image -------------------------------

  $(document).on("click", ".clinic-image", function(event) {
    $("#clinic-profile-image-file").trigger("click");
    event.stopImmediatePropagation();
    return false;
  });

  $(document).on("change", "#clinic-profile-image-file", function(event) {
    var formData = new FormData();
    formData.append("file", $("#clinic-profile-image-file")[0].files[0]);

    $("#alert_box").css("display", "block");
    $("#alert_box").html("Please wait while your image is being uploaded...");

    $.ajax({
      type: "POST",
      url: base_url + "clinic/clinic-image-upload",
      data: formData,
      processData: false,
      contentType: false,
      enctype: "multipart/form-data"
    }).done(function(data) {
      setTimeout(function() {
        if (data != 0) {
          $(".clinic-image").attr("src", data["img"]);
        }

        $("#alert_box").css("display", "none");

        var text = "Uploaded Successfully !";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "success",
          // hideAfter : false,
          stack: 1
          // bgColor : '#1667AC'
        });
      }, 500);
    });
  });

  // --------------------------  Add / Remove Clinic break slots  ------------------------------

  var mon_row = 0;
  var tue_row = 0;
  var wed_row = 0;
  var thu_row = 0;
  var fri_row = 0;
  var sat_row = 0;
  var sun_row = 0;

  $(document).on("click", ".clinic-break-btn", function(event) {
    if ($(this).attr("id") == "add-break-mon") {
      mon_row = mon_row + 1;
      var append_class = ".clinic-break-panel-mon";
      var row_num = mon_row;
      var day_name = "mon";
    } else if ($(this).attr("id") == "add-break-tue") {
      tue_row = tue_row + 1;
      var append_class = ".clinic-break-panel-tue";
      var row_num = tue_row;
      var day_name = "tue";
    } else if ($(this).attr("id") == "add-break-wed") {
      wed_row = wed_row + 1;
      var append_class = ".clinic-break-panel-wed";
      var row_num = wed_row;
      var day_name = "wed";
    } else if ($(this).attr("id") == "add-break-thu") {
      thu_row = thu_row + 1;
      var append_class = ".clinic-break-panel-thu";
      var row_num = thu_row;
      var day_name = "thu";
    } else if ($(this).attr("id") == "add-break-fri") {
      fri_row = fri_row + 1;
      var append_class = ".clinic-break-panel-fri";
      var row_num = fri_row;
      var day_name = "fri";
    } else if ($(this).attr("id") == "add-break-sat") {
      sat_row = sat_row + 1;
      var append_class = ".clinic-break-panel-sat";
      var row_num = sat_row;
      var day_name = "sat";
    } else if ($(this).attr("id") == "add-break-sun") {
      sun_row = sun_row + 1;
      var append_class = ".clinic-break-panel-sun";
      var row_num = sun_row;
      var day_name = "sun";
    }

    var S4 = (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    guid = (
      S4 +
      S4 +
      "-" +
      S4 +
      "-4" +
      S4.substr(0, 3) +
      "-" +
      S4 +
      "-" +
      S4 +
      S4 +
      S4
    ).toLowerCase();

    $(append_class).append(
      "<div id=clinic-break-" +
        day_name +
        row_num +
        " guid=" +
        guid +
        ' class="col-md-12 clinic-break" style="padding: 0;"> ' +
        '<div class="col-md-4" style="padding-top: 5px;">' +
        "<input guid=" +
        guid +
        ' class="break-timepicker clinic-break-time_from" style="float: right;" type="button" value="08:00 AM">' +
        "</div>" +
        '<span class="col-md-1 text-center" style="padding: 0; width: 12px; padding-top: 10px;">to</span>' +
        '<div class="col-md-4" style="padding-top: 5px;">' +
        "<input guid=" +
        guid +
        ' type="button" class="break-timepicker clinic-break-time_to" value="04:00 PM">' +
        "</div>" +
        "<span>" +
        "<a guid=" +
        guid +
        " id=delete-break-" +
        day_name +
        row_num +
        ' href="#"  data-toggle="popover" class="clinic-break-pop" data-placement="left" data-trigger="focus" ><span class="glyphicon glyphicon-trash" aria-hidden="true" style="padding-top: 12px; color: black;"></span></a>' +
        "</span>" +
        "</div>"
    );

    loadtimepicker();

    addClinicBreaks(day_name, "clinic-break-" + day_name + row_num);

    //      	$('#delete-break-'+ day_name + row_num).popover({

    // 	html: 'true',
    //        title : 'Are you sure ?',
    //        content : '<button guid=' + guid + ' id=' + row_num + ' class="btn btn-danger delete-break-'+ day_name +'">Delete</button> <button class="btn" id="break-delete-cancel">Cancel</button>'
    // });
  });

  // ----------------------------------- Delete Clinic Break --------------------------------------

  $(document).on("click", ".clinic-break-pop", function(event) {
    guid = $(this).attr("guid");

    var cnf = confirm("Are you sure you want to remove this break?");
    if (cnf) {
      $("#alert_box").css("display", "block");
      $("#alert_box").html("Updating...");

      $.ajax({
        url: base_url + "setting/profile/Remove-Clinic-Breaks",
        type: "POST",
        data: { id: guid }
      }).done(function(data) {
        setTimeout(function() {
          $("#alert_box").css("display", "none");
          $("#clinic-breaks-main").html(data);

          var text = "Clinic Break Deleted !";
          $.toast({
            text: text,
            showHideTransition: "slide",
            icon: "warning",
            // hideAfter : false,
            stack: 1
            // bgColor : '#1667AC'
          });
        }, 500);
      });
    }

    event.stopImmediatePropagation();
    return false;
  });

  // --------------------------------------------------------------------------------

  $(document).on("change", ".break-timepicker", function(event) {
    guid = $(this).attr("guid");
    var time_from = $(this)
      .closest(".clinic-break")
      .find(".clinic-break-time_from")
      .val();
    var time_to = $(this)
      .closest(".clinic-break")
      .find(".clinic-break-time_to")
      .val();

    $("#alert_box").css("display", "block");
    $("#alert_box").html("Updating...");

    $.ajax({
      url: base_url + "setting/profile/Update-Clinic-Breaks",
      type: "POST",
      data: { id: guid, time_from: time_from, time_to: time_to }
    }).done(function(data) {
      setTimeout(function() {
        $("#alert_box").css("display", "none");
        $("#clinic-breaks-main").html(data);

        var text = "Breaks Updated !";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "success",
          // hideAfter : false,
          stack: 1
          // bgColor : '#1667AC'
        });
      }, 500);
    });
  });

  // -------------------------------------------------------------------------------

  $(document).on("change", "#clinic-day-checkbox", function(event) {
    var Start_date = $("#clinic-custom-start-date").val();
    var End_date = $("#clinic-custom-end-date").val();
    var Start_Time = $("#clinic-custom-start-time").val();
    var End_Time = $("#clinic-custom-end-time").val();

    var day_Start_date = $("#clinic-day-start-date").val();
    var day_End_date = $("#clinic-day-end-date").val();

    if ($(this).is(":checked")) {
      $("#clinic-custom-time-off").css("display", "none");
      $("#clinic-day-time-off").css("display", "block");
      $("#clinic-day-start-date").val(Start_date);
      $("#clinic-day-end-date").val(End_date);

      $("#clinic-time-wall").html(
        "From " + day_Start_date + " to " + day_End_date
      );
    } else {
      $("#clinic-day-time-off").css("display", "none");
      $("#clinic-custom-time-off").css("display", "block");
      $("#clinic-custom-start-date").val(day_Start_date);
      $("#clinic-custom-end-date").val(day_End_date);

      $("#clinic-time-wall").html(
        "From " +
          Start_date +
          ", " +
          Start_Time +
          " to " +
          End_date +
          ", " +
          End_Time
      );
    }
  });

  // -------------------------------------------------------------------------------

  $(document).on("change", ".clinic-time-off-change", function(event) {
    var Start_date = $("#clinic-custom-start-date").val();
    var End_date = $("#clinic-custom-end-date").val();
    var Start_Time = $("#clinic-custom-start-time").val();
    var End_Time = $("#clinic-custom-end-time").val();

    var day_Start_date = $("#clinic-day-start-date").val();
    var day_End_date = $("#clinic-day-end-date").val();

    if ($("#clinic-day-checkbox").is(":checked")) {
      $("#clinic-time-wall").html(
        "From " + day_Start_date + " to " + day_End_date
      );
    } else {
      $("#clinic-time-wall").html(
        "From " +
          Start_date +
          ", " +
          Start_Time +
          " to " +
          End_date +
          ", " +
          End_Time
      );
    }
  });

  // -------------------------------------------------------------------------------

  $(document).on("click", "#clinic-password-update", function(event) {
    var old_pass = $("#old-cinic-password").val();
    var new_pass = $("#new-cinic-password").val();
    var confirm_pass = $("#confirm-cinic-password").val();

    if (old_pass == "") {
      $("#old-cinic-password").addClass("input-error");
      return false;
    } else {
      $("#old-cinic-password").removeClass("input-error");
    }

    if (new_pass == "") {
      $("#new-cinic-password").addClass("input-error");
      return false;
    } else {
      $("#new-cinic-password").removeClass("input-error");
    }

    if (confirm_pass == "") {
      $("#confirm-cinic-password").addClass("input-error");
      return false;
    } else {
      $("#confirm-cinic-password").removeClass("input-error");
    }

    if (new_pass != confirm_pass) {
      $("#new-cinic-password").addClass("input-error");
      $("#confirm-cinic-password").addClass("input-error");
      alert("PIN mismatch!");
      return false;
    } else {
      $("#new-cinic-password").removeClass("input-error");
      $("#confirm-cinic-password").removeClass("input-error");
    }

    $.ajax({
      url: base_url + "setting/profile/Update-Clinic-Password",
      type: "POST",
      data: { old_pass: old_pass, new_pass: new_pass }
    }).done(function(data) {
      if (data == 0) {
        $("#old-cinic-password").addClass("input-error");
        alert("Existing PIN mismatch!");
      } else {
        $("#alert_box").css("display", "block");
        $("#alert_box").html("Updating...");
        setTimeout(function() {
          $("#alert_box").css("display", "none");
          $("#clinic-password").trigger("click");

          var text = "Password Updated !";
          $.toast({
            text: text,
            showHideTransition: "slide",
            icon: "success",
            // hideAfter : false,
            stack: 1
            // bgColor : '#1667AC'
          });
        }, 3000);
      }
    });

    event.stopImmediatePropagation();
    return false;
  });

  

  // ===================================================================================================== //
}); // end of jQuery

// #######################################################################################################
// #                                           Fuctions                                                  #
// #######################################################################################################

function getBankDetails() {
  $.ajax({
    url: base_url + "clinic/bank_details",
    type: "get"
  }).done(function(data) {
    console.log(data);

    $("#bank-name").val(data.details.bank_name);
    $("#billing-address").val(data.details.billing_address);
    $("#bank-type").val(data.details.company_billing_name);
    $("#bank-number").val(data.details.bank_account_number);
  });
}

function loadClinicDetails() {
  $.ajax({
    url: base_url + "setting/profile/ajaxGetClinicDetailPanel",
    type: "post"
  }).done(function(data) {
    $("#profile-detail-wrapper").html(data);
  });
}

function loadtimepicker() {
  $(".break-timepicker").timepicker({
    timeFormat: "h:i A"
  });
}

function addClinicBreaks(day, divid) {
  var time_from = $("#" + divid)
    .find(".clinic-break-time_from")
    .val();
  var time_to = $("#" + divid)
    .find(".clinic-break-time_to")
    .val();
  var guid = $("#" + divid).attr("guid");
  var day = day;

  $("#alert_box").css("display", "block");
  $("#alert_box").html("Updating...");

  $.ajax({
    url: base_url + "setting/profile/Add-Clinic-Breaks",
    type: "POST",
    data: { time_from: time_from, time_to: time_to, day: day, guid: guid }
  }).done(function(data) {
    setTimeout(function() {
      $("#clinic-breaks-main").html(data);
      $("#alert_box").css("display", "none");

      var text = "Clinic Breaks Updated !";
      $.toast({
        text: text,
        showHideTransition: "slide",
        icon: "success",
        // hideAfter : false,
        stack: 1
        // bgColor : '#1667AC'
      });
    }, 500);
  });
}

function GetClinicHolyday() {
  $(document).on("click", ".clinic-time-off", function(event) {
    var id = $(this).attr("id");

    // console.log(id);

    $.ajax({
      url: base_url + "setting/staff/get-doctor-time-off",
      type: "POST",
      dataType: "json",
      data: { Holiday_id: id }
    }).done(function(data) {
      $("#Clinic-time-off-Modal").modal("show");
      $("#time-off-modal-title").html("Edit Time Off");

      // console.log(data.Type);

      if (data.Type == 1) {
        $("#clinic-day-checkbox").prop("checked", false);
        $("#clinic-day-time-off").css("display", "none");
        $("#clinic-custom-time-off").css("display", "block");

        $("#new-clinic-time-off").css("display", "none");
        $("#exist-clinic-time-off").css("display", "block");

        $("#h-clinic-holiday-id").val(data.Holiday_id);
        $("#clinic-custom-start-date").val(data.Start_date);
        $("#clinic-custom-end-date").val(data.End_date);
        $("#clinic-custom-start-time").val(data.Start_Time);
        $("#clinic-custom-end-time").val(data.End_Time);
        $("#clinic-time-off-note").val(data.Note);
        $("#clinic-time-wall").html(
          "From " +
            data.Start_date +
            ", " +
            data.Start_Time +
            " to " +
            data.End_date +
            ", " +
            data.End_Time
        );

        $("#clinic-day-start-date").val(data.Start_date);
        $("#clinic-day-end-date").val(data.End_date);
      } else {
        $("#clinic-day-checkbox").prop("checked", true);
        $("#clinic-day-time-off").css("display", "block");
        $("#clinic-custom-time-off").css("display", "none");

        $("#new-clinic-time-off").css("display", "none");
        $("#exist-clinic-time-off").css("display", "block");

        $("#h-clinic-holiday-id").val(data.Holiday_id);
        $("#clinic-day-start-date").val(data.Start_date);
        $("#clinic-day-end-date").val(data.End_date);
        $("#clinic-time-off-note").val(data.Note);
        $("#clinic-time-wall").html(
          "From " + data.Start_date + " to " + data.End_date
        );

        $("#custom-start-date").val(data.Start_date);
        $("#custom-end-date").val(data.End_date);
      }
    });

    event.stopImmediatePropagation();
    return false;
  });
}

function addClinicHolyday() {
  $(document).on("click", "#Add-clinic-time-off", function(event) {
    var note = $("#clinic-time-off-note").val();

    if ($("#clinic-day-checkbox").is(":checked")) {
      var holiday_type = 0;
      var date_start = $("#clinic-day-start-date").val();
      var day_end = $("#clinic-day-end-date").val();
      var time_start = 0;
      var time_end = 0;
    } else {
      var holiday_type = 1;
      var date_start = $("#clinic-custom-start-date").val();
      var day_end = $("#clinic-custom-end-date").val();
      var time_start = $("#clinic-custom-start-time").val();
      var time_end = $("#clinic-custom-end-time").val();
    }

    $.ajax({
      url: base_url + "setting/profile/Add-Clinic-Time-Off",
      type: "POST",
      data: {
        holidayType: holiday_type,
        dateStart: date_start,
        dayEnd: day_end,
        timeStart: time_start,
        timeEnd: time_end,
        note: note
      }
    }).done(function(data) {
      $("#Clinic-time-off-Modal").modal("hide");
      $("#alert_box").css("display", "block");
      $("#alert_box").html("Updating...");

      setTimeout(function() {
        $("#alert_box").css("display", "none");
        $("#clinic-time_off-tab").trigger("click");

        var text = "Clinic Holiday Updated !";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "success",
          // hideAfter : false,
          stack: 1
          // bgColor : '#1667AC'
        });
      }, 1000);
    });

    event.stopImmediatePropagation();
    return false;
  });
}

function UpdateClinicHolyday() {
  $(document).on("click", "#update-clinic-time-off", function(event) {
    var Holiday_id = $("#h-clinic-holiday-id").val();
    var note = $("#clinic-time-off-note").val();

    if ($("#clinic-day-checkbox").is(":checked")) {
      var holiday_type = 0;
      var date_start = $("#clinic-day-start-date").val();
      var day_end = $("#clinic-day-end-date").val();
      var time_start = 0;
      var time_end = 0;
    } else {
      var holiday_type = 1;
      var date_start = $("#clinic-custom-start-date").val();
      var day_end = $("#clinic-custom-end-date").val();
      var time_start = $("#clinic-custom-start-time").val();
      var time_end = $("#clinic-custom-end-time").val();
    }

    $.ajax({
      url: base_url + "setting/staff/Update-doctor-time-off",
      type: "POST",
      data: {
        holidayid: Holiday_id,
        holidayType: holiday_type,
        dateStart: date_start,
        dayEnd: day_end,
        timeStart: time_start,
        timeEnd: time_end,
        note: note
      }
    }).done(function(data) {
      $("#Clinic-time-off-Modal").modal("hide");
      $("#alert_box").css("display", "block");
      $("#alert_box").html("Updating...");

      setTimeout(function() {
        $("#alert_box").css("display", "none");
        $("#clinic-time_off-tab").trigger("click");

        var text = "Clinic Holiday Updated !";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "success",
          // hideAfter : false,
          stack: 1
          // bgColor : '#1667AC'
        });
      }, 1000);
    });

    event.stopImmediatePropagation();
    return false;
  });
}

function DeleteClinicHolyday() {
  $(document).on("click", "#delete-clinic-time-off", function(event) {
    var Holiday_id = $("#h-clinic-holiday-id").val();

    $.ajax({
      url: base_url + "setting/staff/Delete-doctor-time-off",
      type: "POST",
      data: { holidayid: Holiday_id }
    }).done(function(data) {
      $("#Clinic-time-off-Modal").modal("hide");
      $("#alert_box").css("display", "block");
      $("#alert_box").html("Updating...");

      setTimeout(function() {
        $("#alert_box").css("display", "none");
        $("#clinic-time_off-tab").trigger("click");

        var text = "Clinic Holiday Deleted !";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "warning",
          // hideAfter : false,
          stack: 1
          // bgColor : '#1667AC'
        });
      }, 1000);
    });

    event.stopImmediatePropagation();
    return false;
  });
}
