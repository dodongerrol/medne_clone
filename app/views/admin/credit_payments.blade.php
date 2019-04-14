@include('admin.header-admin')
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->
{{ HTML::style('assets/css/jquery-ui.css') }}
{{ HTML::script('assets/js/jquery-ui.js') }}
<!-- <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->

{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
<!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css"> -->
@include('admin.header')
 
<style type="text/css">
  

.ui-datepicker-calendar {
      display: none;
  }​

  .ui-datepicker-title{
    height: 30px !important;
  }​

  .ui-datepicker{
    padding: 20px 0;
  }


  .ui-menu {
      list-style:none;
      padding: 2px;
      margin: 0;
      margin-left: -5px;
      display:block;
      float: left;
      width:300px;
      border: 1px solid #999 !important;
      padding-left: 0 !important;
  }
  .ui-menu .ui-menu {
      margin-top: -3px;
  }
  .ui-menu .ui-menu-item {
      margin:0;
      padding: 0;
      zoom: 1;
      float: left;
      clear: left;
      width: 100%;
  }
  .ui-menu .ui-menu-item {
      text-decoration:none;
      display:block;
      padding:.2em .4em;
      line-height:1.5;
      font-size: 12px !important;
      color: #333 !important;
  }


</style>

<div class="admin-transac-history">

    <div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
        <span style="padding-top: 15px; font-size: large; font-weight: bold;">Invoice History</span>
    </div>

    <div class="blockUI-hide" style="min-height: 400px;width: 100%;margin-top: 45px;" hidden></div>

    <div class="blockUI">
        <div class="col-md-12 no-padding-sides" style="padding-top: 20px;padding-bottom: 25px;border-bottom: 1px solid #ccc;margin-bottom:20px;">
            <input type="hidden"  id="clinic_ID">
            <input type="text" placeholder="Enter Clinic name here" class="search-terms" id="search_terms">

            <div class="dropdown" style="display: inline-block;">
              <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="margin-left: 20px;min-width: 185px;">
                <b>Filter :</b> <span class="selection">All</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu filterOption" aria-labelledby="dropdownMenu1" style="left: 28px;">
                <li><a href="#">All</a></li>
                <li><a href="#">View By Payment to Clinic</a></li>
                <li><a href="#">View By Payment to MediCloud</a></li>
                <li role="separator" class="divider"></li>
              </ul>
            </div>

            <button id="payment-history-range-btn" class="btn btn-default" style="margin-left: 20px">
                <span id="payment-history-range-from">Sep 15 2016</span> - 
                <span id="payment-history-range-to">Sep 21 2016</span>
            </button>

            <div class="history-range-wrapper" style="left: 315px !important;padding-top:15px;">
                <a id="history-range-datepicker" href="#" style="position: absolute;top: -2px;right: 3px;"><i class="fa fa-times-circle red" style="font-size: 14px;color: gray"></i></a>
                <div class="col-md-12" >
                    <input type="hidden" id="dateFrom" style="margin: 0px 42px;margin-bottom: 10px;">
                    <input type="hidden" id="dateTo" style="margin: 0px 42px;margin-bottom: 10px;">
                    <div id="history-calendar">

                    </div>     
                </div>
               
            </div>

            <button id="submit-payment-range" class="btn btn-primary btn-sm " style="background-color: #337ab7;border-color: #2e6da4;padding: 10px;font-size: 12px;margin-left: 20px;">Submit</button>
            <button id="submit-update-checkbox" class="btn btn-success btn-sm " style="background-color: #5cb85c;border-color: #4cae4c;padding: 10px;font-size: 12px;margin-left: 20px;display: none" >Update selected to PAID</button>

            <a href="" id="payment-history-download" class="btn btn-default" style="margin-left: 0;float: right;"><i class="glyphicon glyphicon-file"></i>  Export as .XLS</a>
        </div>

        <div class="col-md-12 no-padding" style="height: auto;overflow: auto;">
          <div class="history-table-wrapper no-padding">
            <table class="table table-responsive table-history">
                <thead>
                    <tr>
                        <th style="width: 75px"> <input type="checkbox" id="checkAllBox" style="margin-right: 10px"> All</th>
                        <th>Clinic</th>
                        <th>PaymentDate</th>
                        <th>Customer</th>
                        <th>Staff</th>
                        <th>Service/Class</th>
                        <th>Initial Booking Date</th>
                        <th>Appt/Class Date</th>
                        <th>Total Bill</th>
                        <th>Collected</th>
                        <th>Medi-Credit Deducted</th>
                        <th>Transaction Fee</th>
                        <th>Payment to Clinic</th>
                        <th>Total Revenue (Clinic)</th>
                        <th>Paid Status</th>
                    </tr>
                </thead>
                <tbody id="payment-transaction-view">
                  <tr>
                        <td style="text-align: left"><b>0 Results</b></td>
                  </tr>
                </tbody>
            </table>
          </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  var selectedActive = 1;
  var selectedFrom = "";
  var selectedTo = "";

  var currentDate = new Date();
  var currentMonth = currentDate.getMonth();



  $( "body" ).on("focus","#dateFrom",function(){
    selectedActive = 1;
  });

  $( "body" ).on("focus","#dateTo",function(){
    selectedActive = 2;
  });

  function initializeCalendar (){

      $( "#history-calendar" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'MM yy',
        onChangeMonthYear : function(year,month){
          console.log(year);
          console.log(month);

          var date = new Date();

          var firstDay = new Date(year, month - 1, 1);
          var lastDay = new Date(year, month, 0);


          $("#payment-history-range-from").text( moment(firstDay).format('MMM D, YYYY') );
          $("#payment-history-range-to").text( moment(lastDay).format('MMM D, YYYY') );

          selectedFrom = moment(firstDay).format('MMM D, YYYY');
          selectedTo = moment(lastDay).format('MMM D, YYYY');
        }
      });


      
      setTimeout(function() {
        $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');
      }, 10);
      
    }


  $( "body" ).on("click","#payment-history-range-btn",function(){
      $( ".history-range-wrapper" ).toggle();
      // $('#search_terms').val('');
      initializeCalendar();
      // $('#payment-transaction-view').html('');
  });

  $( "body" ).on("click","#history-range-datepicker",function(){
      $( ".history-range-wrapper" ).toggle();
  });


  $( "body" ).on("click","#submit-payment-range",function(){
    $( ".history-range-wrapper" ).hide();

    var filter = $(".selection").text();

    var start = moment(selectedFrom).format('YYYY-MM-DD');
    var end = moment(selectedTo).format('YYYY-MM-DD');
    var filterOption;
    var clinicID = $("#clinic_ID").val();

    if( filter == "All" ){
      filterOption = 0;
    }else if( filter == "View By Payment to Clinic" ){
      filterOption = 1;
    }else if( filter == "View By Payment to MediCloud" ){
      filterOption = 2;
    }


    // console.log(start, end, filterOption, clinicID);
    if(!clinicID) {
      alert('Please choose a clinic');
    } else {
      jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
      
      $('.blockUI-hide').show();
      $('.blockUI').hide();

      $.ajax({
         url: window.base_url + 'clinic/view/payment/transaction/',
         type: 'POST',
         data: { start: start, end: end, filter: filterOption, clinicID: clinicID }
      })
      .done(function(data){
        console.log(data.length);
        if(data.length == 0) {
          $('#payment-history-download').attr('disabled', true);
          $('#payment-transaction-view').html('<tr><td style="text-align: left;"><h6>No data available in table</h6></td></tr>');
        } else {
          $('#payment-history-download').attr('disabled', false);
          $('#payment-transaction-view').html(data);

          $('.history-table-wrapper').css({height:'auto'});
          $('.admin-transac-history').css({overflow: 'hidden'});
        }
        $('#payment-history-download').attr("href", base_url + 'admin/transaction/payment/invoice/download/' + start + '/' + end + '/' + filterOption + '/' + clinicID );

        setTimeout(function(){
          $('.blockUI-hide').hide();
          $('.blockUI').show();
          $('.blockUI-hide').unblock();
          jQuery.unblockUI();
          var page_height = $('#payments-detail-wrapper').height()+52;
          var win_height = $(window).height()

          if (page_height > win_height){

              $("#setting-navigation").height($('#payments-detail-wrapper').height()+52);
              $("#payments-side-list").height($('#payments-detail-wrapper').height()+52);
          }
          else{

              $("#setting-navigation").height($(window).height()-52);
              $("#payments-side-list").height($(window).height()-52);
          }
        },100); 
        

        

      });

    }
});

// FILTER DROPDOWN

$(".filterOption li a").click(function(){

  $(this).parents(".dropdown").find('.selection').text($(this).text());
  $(this).parents(".dropdown").find('.selection').val($(this).text());

});

var input = [];
var selectedID = [];

$('#search_terms').on('input', function( ){
    var search = this.value;
     $.ajax({
       url: window.base_url + 'admin/search',
       type: 'POST',
       data: { search: search }
    })
    .done(function(data){
      // console.log(data);

      input = [];
      selectedID = [];

      $.each(data, function (index) {
        input.push(data[index].CLName);
        selectedID.push(data[index].ClinicID);

      });

      $( "#search_terms" ).autocomplete({
          source: function(request, resolve) {
              // fetch new values with request.term
              resolve(input);
          },
          select : function( event, ui ){

            var index = $.inArray( ui.item.value, input );

            $("#clinic_ID").val(selectedID[index]);
          }
      });

    });
 });
 

 function setDateToday(){
    var date = new Date();

    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    selectedFrom = moment(firstDay).format('MMM D, YYYY');
    selectedTo = moment(lastDay).format('MMM D, YYYY');


    $("#payment-history-range-from").text( moment(firstDay).format('MMM D, YYYY') );
    $("#payment-history-range-to").text( moment(lastDay).format('MMM D, YYYY') );
  }

  setTimeout(function(){
    setDateToday();
  },100);


  var transactionArr = [];

  $("#checkAllBox").change(function(){
    if(this.checked) {
        $(".checkOneBox").prop("checked",true);

        transactionArr = [];

        $("#payment-transaction-view .checkOneBox:checked").each(function(){
          transactionArr.push( $(this).val() );
        });

        $("#submit-update-checkbox").fadeIn();
    }else{
      $(".checkOneBox").prop("checked",false);

      transactionArr = [];

      $("#payment-transaction-view .checkOneBox:checked").each(function(){
        transactionArr.push( $(this).val() );
      });

      $("#submit-update-checkbox").hide();
    }

    console.log(transactionArr);
  });  

  $("body").on("change",".checkOneBox",function(){
    var id = $(this).val();

    if(this.checked) {
      $("#submit-update-checkbox").show();
      transactionArr.push(id);
      
    }else{
      transactionArr = $.grep( transactionArr , function(value) {
        return value != id;
      });
      
      var a = $("input[type='checkbox'].checkOneBox");
      if(a.filter(":checked").length == 0){
          $("#submit-update-checkbox").hide();
          $("#checkAllBox").prop("checked",false);
      }

    }

    console.log(transactionArr);
    
  });

  $("#submit-update-checkbox").click(function(){
    console.log(transactionArr);
    $.ajax({
       url: window.base_url + 'update/transaction/paid',
       type: 'POST',
       data: { id: transactionArr }
    })
    .done(function(data){
      console.log(data);
      $('#submit-payment-range').click();
    });
  });

</script>



@include('admin.footer-admin')
