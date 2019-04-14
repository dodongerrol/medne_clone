jQuery(document).ready(function($) {
  // var protocol = jQuery(location).attr('protocol');
  // var hostname = jQuery(location).attr('hostname');
  // var folderlocation = $(location).attr('pathname').split('/')[1];
  // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
  window.base_url = window.location.origin + "/app/";
  // .................................delete pop over........................
  $("#btn-service-delete").popover({
    html: "true",
    title: "Are you sure ?",
    content:
      '<button id="delete-service" class="btn btn-danger">Delete</button> <button class="btn" id="service-delete-cancel" style="background: #FFFFFD; border: 1px solid #B9B8B8;">Cancel</button>'
  });

  $(document).on("click", "#service-delete-cancel", function(event) {
    $("#btn-service-delete").popover("hide");
  });

  $(document).on("change", "#service-doc-all", function(event) {
    if ($(this).is(":checked")) {
      $(".service-doc-list").prop("checked", true);
    } else {
      $(".service-doc-list").prop("checked", false);
    }
  });

  $(document).on("change", ".service-doc-list", function(event) {
    if ($(this).is(":checked")) {
    } else {
      $("#service-doc-all").prop("checked", false);
    }
  });

  $(document).on("change", ".switch input", function(event) {
    var selected_id = $(this)
      .closest(".service-details")
      .attr("id");
    var selected_value = $(this).prop("checked");

    var text = "updating..!";
    $.toast({
      text: text,
      showHideTransition: "slide",
      icon: "info",
      stack: 1
    });

    $.ajax({
      url: base_url + "clinic/update_procedure_scan_pay_status",
      type: "POST",
      data: { procedure_id: selected_id, service_status: selected_value }
    }).done(function(data) {
      console.log(data);
      if (!data.status) {
        alert(data.message);
      }else{
        var text = "saved!";
        $.toast({
          text: text,
          showHideTransition: "slide",
          icon: "success",
          stack: 1
        });
      }


    });
  });

  // .............................................................................

  $("#btn-service-add").click(function(event) {
    $.ajax({
      url: base_url + "setting/service/ajaxGetEditPage",
      type: "POST",
      data: { id: null }
    }).done(function(data) {
      $("#main-tab-service").html(data);
    });
  });

  $("#btn-service-cancel").click(function(event) {
    $.ajax({
      url: base_url + "setting/ajaxGetServicPage",
      type: "GET"
    }).done(function(data) {
      $("#main-tab-account").removeClass("active");
      $("#main-tab-staff").removeClass("active");
      $("#main-tab-service").addClass("active");
      $("#main-tab-notify").removeClass("active");

      $("#main-tab-service").html(data);
    });
  });

  $(".service-edit").click(function(event) {
    var id = $(this)
      .closest(".service-details")
      .attr("id");

    $.ajax({
      url: base_url + "setting/service/ajaxGetEditPage",
      type: "POST",
      data: { id: id }
    }).done(function(data) {
      $("#main-tab-service").html(data);
    });
  });

  $("#btn-service-save").click(function(event) {
    var id = $("#h-service_id").val();
    var name = $("#service-name").val();
    var cost = $("#service-cost").val();
    var duration = $("#service-duration").val();
    var description = $("#service-description").val();
    doctorid = [];

    x = 0;

    $(".service-doc-list").each(function() {
      if ($(this).is(":checked")) {
        doctorid[x] = this.id;
        x++;
      }
    });

    if (name == "") {
      $("#service-name").addClass("input-error");
      return false;
    } else {
      $("#service-name").removeClass("input-error");
    }

    if (cost == "") {
      $("#service-cost").addClass("input-error");
      return false;
    } else {
      $("#service-cost").removeClass("input-error");
    }

    if (duration == "") {
      $("#service-duration").addClass("input-error");
      return false;
    } else {
      $("#service-duration").removeClass("input-error");
    }

    // console.log(doctorid);
    if (id == "null") {
      if (doctorid == "") {
        alert("Select At Least One Doctor !");
        return false;
      }
    }

    //alert(id + name + cost + duration + description);
    $.ajax({
      url: base_url + "setting/service/saveServices",
      type: "POST",
      data: {
        id: id,
        name: name,
        cost: cost,
        duration: duration,
        description: description,
        doctorid: doctorid
      }
    }).done(function(data) {
      $("#alert_box").css("display", "block");
      $("#alert_box").html("Updating...");

      setTimeout(function() {
        $("#alert_box").css("display", "none");
        $("#main-tab-service").html(data);

        var text = "Services Updated !";
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
  });

  // --------------------  Add / Remove Doctors  ------------------

  $(document).on("change", "#all-doctor", function(event) {
    var service_id = $("#h-service_id").val();

    if ($(this).is(":checked")) {
      var checked = 1;

      Update_Doctor_AllService(service_id, checked);
    } else {
      var checked = 0;

      Update_Doctor_AllService(service_id, checked);
    }
  });

  $(document).on("change", ".service-doc", function(event) {
    var service_id = $("#h-service_id").val();
    var doctor_id = $(this).attr("id");

    // console.log(service_id);

    if ($(this).is(":checked")) {
      var checked = 1;

      Update_Doctor_Service(doctor_id, service_id, checked);
    } else {
      var checked = 0;

      Update_Doctor_Service(doctor_id, service_id, checked);
    }
  });

  $(document).on("change", ".service-doc-list", function(event) {
    var doctor_id = $(this).attr("id");

    if ($(this).is(":checked")) {
      $("#" + doctor_id + "-lbl").css("color", "black");
    } else {
      $("#" + doctor_id + "-lbl").css("color", "#777");
    }
  });

  $(document).on("change", "#service-doc-all", function(event) {
    var doctor_id = $(this).attr("id");

    if ($(this).is(":checked")) {
      $(".doc-lbl").css("color", "black");
    } else {
      $(".doc-lbl").css("color", "#777");
    }
  });
}); //end of services

function Update_Doctor_Service(doctor_id, service_id, checked) {
  $.ajax({
    url: base_url + "setting/staff/update-Staff-Doctor-Services",
    type: "POST",
    data: { doctorid: doctor_id, procedure: service_id, checked: checked }
  }).done(function(data) {
    $("#alert_box").css("display", "block");
    $("#alert_box").html("Updating...");
    setTimeout(function() {
      $("#alert_box").css("display", "none");

      var lable = "#" + doctor_id + '-lbl"';

      if (checked == 1) {
        $("#" + doctor_id + "-lbl").css("color", "black");
      } else {
        $("#" + doctor_id + "-lbl").css("color", "#777");
        $("#all-doctor").removeAttr("checked");
      }

      var text = "Services Updated !";
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
}

function Update_Doctor_AllService(service_id, checked) {
  $.ajax({
    url: base_url + "setting/service/update-service-Alldoctor",
    type: "POST",
    data: { procedure: service_id, checked: checked }
  }).done(function(data) {
    $("#alert_box").css("display", "block");
    $("#alert_box").html("Updating...");
    setTimeout(function() {
      $("#alert_box").css("display", "none");

      if (checked == 1) {
        $(".doc-lbl").css("color", "black");
        $(".service-doc").prop("checked", true);
      } else {
        $(".doc-lbl").css("color", "#777");
        $(".service-doc").removeAttr("checked");
      }

      var text = "Services Updated !";
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
}
