jQuery(document).ready(function($) {
  // var protocol = jQuery(location).attr('protocol');
  // var hostname = jQuery(location).attr('hostname');
  // var folderlocation = $(location).attr('pathname').split('/')[1];
  // window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
  window.base_url = window.location.origin + "/app/";
  // --------------------  Page onload default selection  --------------- //

  loadPaymentDetails();

  // ===================================================================================================== //
}); // end of jQuery

var invoice_selected_date;





function loadTransactionPreview() {
  $(".payments-settings").css({ color: "color: rgb(119, 118, 118);" });
  $("#transaction-history").css({ color: "color: rgb(0, 0, 0);" });

  $.ajax({
    url: base_url + "clinic/mobile/transaction/preview",
    type: "post"
  }).done(function(data) {
    $("#payments-detail-wrapper").html("");
    $("#payments-detail-wrapper").html(data);

    getTransactionList();
  });
}

function loadPaymentDetails() {
  $(".payments-settings").css({ color: "color: rgb(119, 118, 118);" });
  $("#transaction-history").css({ color: "color: rgb(0, 0, 0);" });

  $.ajax({
    url: base_url + "clinic/transaction_page_view",
    type: "get"
  }).done(function(data) {
    $("#payments-detail-wrapper").html("");
    $("#payments-detail-wrapper").html(data);

    var page_height = $("#payments-detail-wrapper").height() + 52;
    var win_height = $(window).height();
    if (page_height > win_height) {
      $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
      $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
    } else {
      $("#setting-navigation").height($(window).height() - 52);
      $("#payments-side-list").height($(window).height() - 52);
    }
  });
}

function loadPaymentStatement() {
  $(".payments-settings").css({ color: "color: rgb(119, 118, 118);" });
  $("#transaction-statement").css({ color: "color: rgb(0, 0, 0);" });

  $.ajax({
    url: base_url + "setting/payments/statement",
    type: "post"
  }).done(function(data) {
    $("#payments-detail-wrapper").html("");
    $("#payments-detail-wrapper").html(data);

    var date = new Date();

    getClinicStatementListRange(date);

    var page_height = $("#payments-detail-wrapper").height() + 52;
    var win_height = $(window).height();
    if (page_height > win_height) {
      $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
      $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
    } else {
      $("#setting-navigation").height($(window).height() - 52);
      $("#payments-side-list").height($(window).height() - 52);
    }

    $("#statement-calendar").datepicker({
      format : 'yyyy',
      minViewMode : 'years',
    });

    $(".statement-calendar-picker").click(function(e){
      $("#statement-calendar").datepicker('show');
    });

    $('#statement-calendar').datepicker()
      .on("changeDate", function(e) {
        $("#statement-calendar").datepicker('hide');
        console.log( e.date );
        invoice_selected_date = moment( e.date ).format(  );
      });

    $("#statement-calendar").datepicker('setDate', moment().format('YYYY') );
  });
}

function loadPaymentInvoice() {
  $(".payments-settings").css({ color: "color: rgb(119, 118, 118);" });
  $("#transaction-invoice").css({ color: "color: rgb(0, 0, 0);" });

  $.ajax({
    url: base_url + "setting/payments/invoice",
    type: "post"
  }).done(function(data) {
    $("#payments-detail-wrapper").html("");
    $("#payments-detail-wrapper").html(data);

    var date = new Date();

    getClinicInvoiceList(date);

    var page_height = $("#payments-detail-wrapper").height() + 52;
    var win_height = $(window).height();
    if (page_height > win_height) {
      $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
      $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
    } else {
      $("#setting-navigation").height($(window).height() - 52);
      $("#payments-side-list").height($(window).height() - 52);
    }

    $("#invoice-statement-calendar").datepicker({
      format : 'MM yyyy',
      minViewMode : 'months',
    });

    $(".statement-calendar-picker").click(function(e){
      $("#invoice-statement-calendar").datepicker('show');
    });

    $('#invoice-statement-calendar').datepicker()
      .on("changeDate", function(e) {
        $("#invoice-statement-calendar").datepicker('hide');
        console.log( e.date );
        invoice_selected_date = moment( e.date );
      });

    $("#invoice-statement-calendar").datepicker('setDate', moment().format('MMM YYYY') );
  });
}

function getTransactionList() {
  $(".payments-settings").css({ color: "color: rgb(119, 118, 118);" });
  $("#view-transaction-preview").css({ color: "color: rgb(0, 0, 0);" });

  $.ajax({
    url: base_url + "clinic/mobile/all_transaction/preview",
    type: "get"
  }).done(function(data) {
    console.log(data);
    var data_list = data.transaction_result;
    var data_customer = data.customer_result;

    if (data_list.length == 0) {
      $(".transaction-list").html( '<p style="margin-left:0">No Transactions</p>' );
    } else {
      for (var i = 0; i < data_list.length; i++) {
        $(".transaction-list").append( '<a class="transac-data" href="javascript:void(0)">' +
            '<input class="user_value" type="hidden" value="' + data_list[i].transaction_id + '">' +
            '<div class="transaction-wrapper">' +
            '<div class="col-md-3 no-padding">' +
              '<img src="' + data_list[i].user_image + '" class="img-responsive img-circle" style="margin: 0 auto;">' +
            '</div>' +
              '<div class="col-md-9 no-padding">' +
                '<p class="trans-name">' + data_list[i].customer + '</p>' +
                '<p class="trans-no"><label >Transaction #:</label> </p>' +
                '<p class="trans-no"><b>' + data_list[i].transaction_id + '</b> ' +
                '<span class="trans-date">' + moment( data_list[i].date_of_transaction, "DD MMM YYYY, hh:mm:A" ).format("DD-MM-YY") + '</span></p>' +
              '</div>' +
            '</div>' +
          '</a>' );
      }

      $(".transac-data:first-child").addClass("active");

      $(".transaction-details").html( '<div class="col-md-12 text-center">' +
          '<img src="' + data_customer.user_image + '" class="img-responsive img-circle" style="margin: 20px auto;width: 150px">'+
          '<br>' +
        '</div>' +

        '<div class="col-md-12">' +
          '<p>' +
            '<label>Transaction # : </label>' +
            '<span>' + data_customer.transaction_id + '</span>' +
            '<span class="pull-right detail-date">' + data_customer.date_of_transaction + '</span>' +
          '</p>' +
          '<p>' +
            '<label class="service-label">Service : </label> ' +
            '<span class="trans-service">' + data_customer.clinic_type_and_service + '</span>' +
            '<span class="pull-right detail-price">S$ <span>' + data_customer.amount + '</span></span>' + +
          '</p>' +
          '<p><label>Clinic : </label> <span>' + data_customer.clinic_name + '</span></p>' +
          '<p><label>Name : </label> <span>' + data_customer.customer + '</span></p>' +
        '</div>' );
    }
  });
}

function getTransactionData(id) {
  $.ajax({
    url: base_url + "clinic/mobile/transaction/details/" + id,
    type: "get"
  }).done(function(data) {
    console.log(data);
    var data_customer = data.customer_result;

    if (data.length == 0) {
      $(".transaction-details").html("<p>No data available</p>");
    } else {
      $(".transaction-details").html("");
      $(".transaction-details").html( '<div class="col-md-12 text-center">' +
          '<img src="' + data_customer.user_image + '" class="img-responsive img-circle" style="margin: 20px auto;width: 150px">' +
          '<br>' +
        '</div>' +
        '<div class="col-md-12">' +
          '<p>' +
            '<label>Transaction # : </label> <span>' + data_customer.transaction_id + '</span>' +
            '<span class="pull-right detail-date">' + data_customer.date_of_transaction + '</span>' +
          '</p>' +
          '<p>' +
            '<label class="service-label">Service : </label>' +
            '<span class="trans-service">' + data_customer.clinic_type_and_service + '</span>' +
            '<span class="pull-right detail-price">S$ <span>' + data_customer.amount + '</span></span>' +
          '</p>' +
          '<p>' +
            '<label>Clinic : </label> ' +
            '<span>' + data_customer.clinic_name + '</span>' +
          '</p>' +
          '<p>' +
            '<label>Name : </label>' +
            '<span>' + data_customer.customer + '</span>' +
          '</p>' +
        '</div>' );
    }
  });
}

function getClinicStatementList() {
  var data = {
    clinic_id: $("#clinicID").val()
  };

  $.ajax({
    url: base_url + "clinic/invoice_list",
    type: "post",
    data: data
  }).done(function(data) {
    if (data.length == 0) {
      $("#transaction-statement-table").html( "<tr><td><h6>No data available in table</h6></td></tr>" );
    } else {
      $("#transaction-statement-table").html(data);
    }
  });
}

function getClinicStatementListRange(date) {
  // var month = date.getMonth();
  // var day = date.getDay();
  // var year = date.getFullYear();
  // var firstDay = new Date(date.getFullYear(), month, 1);
  // var lastDay = new Date(date.getFullYear(), month + 1, 0);

  // $(".statement-payment-range .month").text( moment().month(month).format("MMM") );
  // $(".statement-payment-range .year").text( moment().year(year).format("YYYY") );

  var data = {
    clinic_id: $("#clinicID").val(),
    start: moment(invoice_selected_date).startOf('month').format("MMM D, YYYY"),
    end: moment(invoice_selected_date).endOf('month').format("MMM D, YYYY")
  };

  $.ajax({
    url: base_url + "clinic/invoice_list_by_date",
    type: "post",
    data: data
  }).done(function(data) {
    $("#transaction-statement-table").html("");
    if (data.length == 0) {
      $("#transaction-statement-table").html( "<tr><td><h6>No data available in table</h6></td></tr>" );
    } else {
      $("#transaction-statement-table").html(data);
    }
  });
}

// LEFT MENU

$("body").on("click", "#transaction-invoice", function() {
  loadPaymentInvoice();
});

$("body").on("click", "#transaction-history", function() {
  loadPaymentDetails();
});

$("body").on("click", "#transaction-statement", function() {
  loadPaymentStatement();
});

$("body").on("click", ".view-statement-button", function() {
  $("#table-statement").hide();
  $("#view-statement").fadeIn();

  var page_height = $("#payments-detail-wrapper").height() + 52;
  var win_height = $(window).height();
  if (page_height > win_height) {
    $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
    $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
  } else {
    $("#setting-navigation").height($(window).height() - 52);
    $("#payments-side-list").height($(window).height() - 52);
  }
});

$("body").on("click", "#view-transaction-preview", function() {
  loadTransactionPreview();
});

$("body").on("click", ".transac-data", function() {
  var transaction_id = $(this).children("input").val();
  console.log(transaction_id);
  getTransactionData(transaction_id);

  $(".transac-data").removeClass("active");
  $(this).addClass("active");
});

// //////////////////////

var selectedActive = 1;
var selectedFrom = "";
var selectedTo = "";

$("body").on("focus", "#dateFrom", function() {
  selectedActive = 1;
});

$("body").on("focus", "#dateTo", function() {
  selectedActive = 2;
});

function initializeCalendar() {
  $("#history-calendar").datepicker({
    onSelect: function(date) {
      if (selectedActive == 1) {
        $("#dateFrom").val(moment(date).format("MMM DD, YYYY"));
        selectedFrom = date;
      } else {
        $("#dateTo").val(moment(date).format("MMM DD, YYYY"));
        selectedTo = date;
      }

      setTimeout(function() {
        $(document).find("a.ui-state-highlight").removeClass("ui-state-highlight");
      }, 10);
    }
  });

  $("#payment-history-range-from").text( moment($("#history-calendar").datepicker("getDate")).format("MMM D") );
  $("#payment-history-range-to").text( moment($("#history-calendar").datepicker("getDate")).format("MMM D") );
  setTimeout(function() {
    $(document).find("a.ui-state-highlight").removeClass("ui-state-highlight");
  }, 10);
}

function initializeStatementCalendar() {
  var thisYear = new Date().getFullYear()-5;
  var start = new Date("1/1/" + thisYear);
  var minDate = new Date(start);
  console.log(minDate);

  // $("#statement-calendar").datepicker({
  //   changeMonth: true,
  //   changeYear: true,
  //   minDate: minDate,
  //   onChangeMonthYear: function(year, month, obj) {
  //     console.log(obj);

  //     $(".statement-payment-range .month").text( moment().month(month - 1).format("MMM") );
  //     $(".statement-payment-range .year").text( year );

  //     invoice_selected_date = moment().month(month - 1).format("MMMM") + " " + obj.selectedDay + ", " + year;
  //     invoice_selected_date = new Date(invoice_selected_date);

  //     console.log(invoice_selected_date);
  //   }
  // });
}

$("body").on("click", "#payment-history-range-btn", function() {
  $(".history-range-wrapper").toggle();
  $("#search_terms").val("");
  initializeCalendar();
  $("#payment-transaction-view").html("");
});

$("body").on("click", "#statement-payment-range-btn", function() {
  $(".statement-wrapper").toggle();
  initializeStatementCalendar();
});

$("body").on("click", "#history-range-datepicker", function() {
  $(".history-range-wrapper").toggle();
});

$("body").on("click", "#statement-datepicker", function() {
  $(".statement-wrapper").toggle();
});

$("body").on("click", "#submit-payment-range", function() {
  $("#payment-history-range-from").text(moment(selectedFrom).format("MMM D"));
  $("#payment-history-range-to").text(moment(selectedTo).format("MMM D"));
  $(".history-range-wrapper").toggle();

  var start = moment(selectedFrom).format("YYYY-MM-DD");
  var end = moment(selectedTo).format("YYYY-MM-DD");

  $(".blockUI-hide").show();
  $(".blockUI").hide();

  setTimeout(function() {
    $(".blockUI-hide").block({
      message: "<h1> " + base_loading_image + " <br /> <br /> Fetching Data..</h1>"
    });
  }, 10);

  $.ajax({
    url: window.base_url + "clinic/view/payment/transaction/byDate",
    type: "POST",
    data: { start: start, end: end, search: null }
  }).done(function(data) {
    if (data.length == 0) {
      $("#payment-history-download").attr("disabled", true);
      $("#payment-transaction-view").html( "<tr><td><h6>No data available in table</h6></td></tr>" );
    } else {
      $("#payment-history-download").attr("disabled", false);
      $("#payment-transaction-view").html(data);

      $(".history-table-wrapper").css({ height: "400px" });
    }
    $("#payment-history-download").attr( "href", base_url + "clinic/transaction/payment/download/" + start + "/" + end );

    setTimeout(function() {
      $(".blockUI-hide").hide();
      $(".blockUI").show();
      $(".blockUI-hide").unblock();

      var page_height = $("#payments-detail-wrapper").height() + 52;
      var win_height = $(window).height();

      if (page_height > win_height) {
        $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
        $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
      } else {
        $("#setting-navigation").height($(window).height() - 52);
        $("#payments-side-list").height($(window).height() - 52);
      }
    }, 100);
  });
});

// --------- Set Navigation bar height ------------------

var page_height = $("#payments-detail-wrapper").height() + 52;
var win_height = $(window).height();

if (page_height > win_height) {
  $("#setting-navigation").height($("#payments-detail-wrapper").height() + 52);
  $("#payments-side-list").height($("#payments-detail-wrapper").height() + 52);
} else {
  $("#setting-navigation").height($(window).height() - 52);
  $("#payments-side-list").height($(window).height() - 52);
}

// GET INVOICE LIST

$("body").on("click", "#invoice-date-go-btn", function() {
  if (invoice_selected_date != null) {
    getClinicInvoiceList(invoice_selected_date);
  } else {
    var date = new Date();
    getClinicInvoiceList(date);
  }
});
 

function getClinicInvoiceList(date) {

  $(".main-loader").show();
  $(".statement-wrapper").hide();
  $("#invoice-download-as-pdf").hide();
  // var month = date.getMonth();
  // var day = date.getDay();
  // var year = date.getFullYear();
  // var firstDay = new Date(date.getFullYear(), month, 1);
  // var lastDay = new Date(date.getFullYear(), month + 1, 0);

  // $(".statement-payment-range .month").text( moment().month(month).format("MMM") );
  // $(".statement-payment-range .year").text( moment().year(year).format("YYYY") );

  var data = {
    clinic_id: $("#clinicID").val(),
    start_date: moment(date).startOf('month').format("MMM D, YYYY"),
    end_date: moment(date).endOf('month').format("MMM D, YYYY")
  };

  // console.log(data);

  $.ajax({
    url: base_url + "clinic/invoice",
    type: "post",
    data: data
  }).done(function(data) {
    console.log(data);

    $('.currencyType').text(data.currency_type);
    // console.log($('.currencyType').text(currencyType));

    
    if (data.status == 400) {
      $("#pdf-print").hide();
      $("#error-log").show();
      $(".error-log-status").text(data.message);
    } else {
      $("#invoice-id").val(data.invoice_record.invoice_id);
      $("#invoice-download-as-pdf").show();
      $("#pdf-print").show();
      $("#error-log").hide();
      $("#invoice-items-table").html("");
      if (data) {
        // $(".invoice_day_start").text( data.start_date );
        // $(".invoice_day_end").text( data.end_date );
        // $(".invoice_month").text( moment( data.start_date ).format("MMM") );
        // $(".invoice_year").text( moment( data.start_date ).format("YYYY") );
        $(".medni_wallet_period").text( data.period );

        for( var i = 0; i < data.transaction_lists.length; i++ ){
          $("#invoice-items-table").append('<tr>' +
            '<td style="text-align: left !important;">' + data.transaction_lists[i].transaction_id + ' ' +  data.transaction_lists[i].customer +'</td>' +
            '<td><b><span style="text-transform: uppercase">' + data.transaction_lists[i].currency_type + '</span> '+ data.transaction_lists[i].mednefits_fee + '</b></td>' +
            '<td><b><span style="text-transform: uppercase">' + data.transaction_lists[i].currency_type + '</span> ' + data.transaction_lists[i].mednefits_credits + '</b></td>' +
            '<td><b><span style="text-transform: uppercase">' + data.transaction_lists[i].currency_type + '</span> ' + data.transaction_lists[i].total + '</b></td>' +
          '</tr>');
        }

        $("#invoice-items-table").append('<tr>' +
            '<td colspan="3" style="text-align:right;border: none !important;"><b>Total Amount Due <span style="display: inline-block">(SGD)</span><span style="display: none">(MYR)</span>:</b></td>' +
            '<td style="border: none !important;"><b><span style="text-transform: uppercase">' + data.currency_type + '</span> ' + data.total + '</b></td>' +
          '</tr>');

        if (data.clinic) {
          if (data.clinic.image != "") {
            $("#clinic-logo-container img").attr("src", data.clinic.image);
          }
        }

        $(".clinic_name").text(data.clinic.Name);
        $(".billing_name").text(data.billing_name);
        $(".clinic_address").text(data.clinic.Address);
        $(".billing_address").text(data.billing_address);
        $(".period_date").text(data.period);
        $(".invoice_amount_due").text(data.amount_due);
        
        $(".total_transactions").text(data.total_transaction);
        $(".credit_transactions").text(data.total_credits_transactions);
        $(".cash_creditcard_transactions").text(data.total_cash_transactions);

        if (data.payment_record) {
          $(".invoice_number").text(data.payment_record.invoice_number);
        }

        if (data.invoice_record) {
          $(".invoice_first_day").text( moment(data.invoice_record.start_date).format("MMMM DD, YYYY") );
          $(".invoice_last_day").text( moment(data.invoice_record.end_date).format("MMMM DD, YYYY") );
        }

        if (data.bank_details) {
          $(".invoice_bank_name").text(data.bank_details.bank_name);
          $(".invoice_account_type").text(data.bank_details.bank_account_type);
          $(".invoice_account_number").text( data.bank_details.bank_account_number );
        }

        $(".invoice_due_date").text( moment(data.invoice_due).format("MMMM DD, YYYY") );

        setTimeout(function() {
          var page_height = $("#payments-detail-wrapper").height() + 52;
          var win_height = $(window).height();

          if (page_height > win_height) {
            $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
            $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
          } else {
            $("#setting-navigation").height($(window).height() - 52);
            $("#payments-side-list").height($(window).height() - 52);
          }
        }, 300);
      }
    }
    $(".main-loader").hide();
  });
}

// GET STATEMENT LOST

$("body").on("click", "#statement-date-go-btn", function() {
  console.log(invoice_selected_date);

  getClinicStatementListRange(invoice_selected_date);
});

// STATEMENT OF ACCOUNT VIEW

function viewStatement(payment_id, opt) {
  $.ajax({
    url: base_url + "clinic/statement/" + payment_id,
    type: "get"
  }).done(function(data) {
    console.log(data);

    if (data) {
      // CLINIC
      if (data.clinic) {
        if (data.clinic.image != "") {
          $("#clinic-logo-container img").attr("src", data.clinic.image);
        }
      }

      if (data.bank_details != null) {
        if (data.bank_details.bank_name != "") {
          $(".statement_bank_name").text(data.bank_details.bank_name);
        }

        if (data.bank_details.billing_address != "") {
          $(".statement_bank_address").text(data.bank_details.billing_address);
        }
      }

      // STATEMENT
      if (data.statement != null) {
        $(".statement_created_at").text(
          moment(data.statement.created_at).format("MMM DD, YYYY")
        );
      }

      // PAYMENT RECORD
      if (data.payment_record != null) {
        $(".statement_amount_paid").text(data.payment_record.amount_paid);
        $(".statement_invoice_number").text(data.payment_record.invoice_number);
        $(".statement_amount_total").text(data.ending_balance);
      }

      $(".payment_date").text( moment( data.payment_record.payment_date ).format('MMMM DD, YYYY') );
      $(".transfer_number").text(data.payment_record.transfer_referrence_number);

      // DUE DATE
      // if (data.due_date != null) {
        $(".statement_start").text(moment(data.invoice_record.start_date).format("MMM DD, YYYY"));
        $(".statement_end").text(moment(data.invoice_record.end_date).format("MMM DD, YYYY"));
      // }

      $(".statement_amount_due").text(data.total);

      if (opt == "print") {
        setTimeout(function() {
          $("#pdf-print").printElement({
            pageTitle: data.clinic.Name + " - " + data.payment_record.invoice_number + " ( " + data.period + " )"
          });
        }, 500);
      } else if ("export-pdf") {
        window.open( base_url + "clinic/print_statement/" + payment_id );





        // var file = document.getElementById("pdf-print");
        // var file_name = data.payment_record.invoice_number + " (" + $(".statement_start").text() + " - " + $(".statement_end").text() + " )";
        // html2canvas(file, {
        //   onrendered: function(canvas) {
        //     //! MAKE YOUR PDF
        //     var pdf = new jsPDF("p", "pt", "letter");

        //     for (var i = 0; i <= file.clientHeight / 980; i++) {
        //       //! This is all just html2canvas stuff
        //       var srcImg = canvas;
        //       var sX = 0;
        //       var sY = 980 * i; // start 980 pixels down for every new page
        //       var sWidth = 900;
        //       var sHeight = 1000;
        //       var dX = 0;
        //       var dY = 0;
        //       var dWidth = 1130;
        //       var dHeight = 1250;

        //       window.onePageCanvas = document.createElement("canvas");
        //       onePageCanvas.setAttribute("width", 1130);
        //       onePageCanvas.setAttribute("height", 1250);
        //       var ctx = onePageCanvas.getContext("2d");
        //       // details on this usage of this function:
        //       // https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API/Tutorial/Using_images#Slicing
        //       ctx.drawImage( srcImg, sX, sY, sWidth, sHeight, dX, dY, dWidth, dHeight );

        //       // document.body.appendChild(canvas);
        //       var canvasDataURL = onePageCanvas.toDataURL("image/png", 2.0);

        //       var width = onePageCanvas.width;
        //       var height = onePageCanvas.clientHeight;

        //       //! If we're on anything other than the first page,
        //       // add another page
        //       if (i > 0) {
        //         pdf.addPage(612, 791); //8.5" x 11" in pts (in*72)
        //       }
        //       //! now we declare that we're working on that page
        //       pdf.setPage(i + 1);
        //       //! now we add content to that page!
        //       pdf.addImage( canvasDataURL, "PNG", 20, 40, width * 0.62, height * 0.62 );
        //       // console.log(canvasDataURL);
        //     }
        //     //! after the for loop is finished running, we save the pdf.
        //     // pdf.save(file_name + '.pdf');
        //     window.open(pdf.output("bloburl"), "_blank");
        //     // console.log(pdf);
        //     // console.log(status);
        //   }
        // });
      }
    }
  });
}

$("body").on("click", ".view-statement-button", function() {
  var payment_id = $(this).closest("tr").find("td:nth-child(1)").text();
  console.log(payment_id);
  viewStatement(payment_id, null);
});

// GENERATE PDF

var form = $(".invoice-wrapper"),
  cache_width = form.width(),
  a4 = [595.28, 841.89]; 

$("body").on("click", "#invoice-download-as-pdf", function() {
  var id = $("#invoice-id").val();
  window.location.href = window.location.origin + "/app/clinic/invoice_download/" + id;
});

//create pdf
function createPDF() {
  getCanvas().then(function(canvas) {
    var img = canvas.toDataURL("image/png"),
      doc = new jsPDF({
        unit: "px",
        format: "a4"
      });
    doc.addImage(img, "JPEG", 20, 20);
    doc.save("thisthis.pdf");
    form.width(cache_width);
  });
}

// create canvas object
function getCanvas() {
  form.width(a4[0] * 1.33333 - 80).css("max-width", "none");
  return html2canvas(form, {
    imageTimeout: 2000,
    removeContainer: true
  });
}

// PRINT DIV

$("body").on("click", ".invoice-print", function() {
  var payment_id = $(this).closest("tr").find("td:nth-child(1)").text();
  console.log(payment_id);

  $("#table-statement").hide();
  $("#view-statement").fadeIn();

  var page_height = $("#payments-detail-wrapper").height() + 52;
  var win_height = $(window).height();
  if (page_height > win_height) {
    $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
    $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
  } else {
    $("#setting-navigation").height($(window).height() - 52);
    $("#payments-side-list").height($(window).height() - 52);
  }

  viewStatement(payment_id, "print");
});

// EXPORT DIV
$("body").on("click", ".export-pdf", function() {
  var payment_id = $(this).closest("tr").find("td:nth-child(1)").text();
  console.log(payment_id);

  $("#table-statement").hide();
  $("#view-statement").fadeIn();

  var page_height = $("#payments-detail-wrapper").height() + 52;
  var win_height = $(window).height();
  if (page_height > win_height) {
    $("#setting-navigation").height( $("#payments-detail-wrapper").height() + 52 );
    $("#payments-side-list").height( $("#payments-detail-wrapper").height() + 52 );
  } else {
    $("#setting-navigation").height($(window).height() - 52);
    $("#payments-side-list").height($(window).height() - 52);
  }

  viewStatement(payment_id, "export-pdf");
});
