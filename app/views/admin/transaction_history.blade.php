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
  
<div class="admin-transac-history">

    <div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
        <span style="padding-top: 15px; font-size: large; font-weight: bold;">Transaction History</span>
    </div>

    <div class="blockUI-hide" style="min-height: 400px;width: 100%;margin-top: 45px;" hidden></div>

    <div class="blockUI">
        <div class="col-md-12 no-padding-sides" style="padding-top: 20px;padding-bottom: 25px;border-bottom: 1px solid #ccc;margin-bottom:20px;">
            <input type="text" placeholder="Enter search terms here" class="search-terms" id="search_terms">

            <button id="payment-history-range-btn" class="btn btn-default">
                <span id="payment-history-range-from">Sep 15</span> - 
                <span id="payment-history-range-to">Sep 21</span>
            </button>

            <div class="history-range-wrapper" style="left: 410px !important;">
              <a id="history-range-datepicker" href="#" style="position: absolute;top: 3px;right: 3px;"><i class="fa fa-times-circle red" style="font-size: 20px;color: red"></i></a>

                <div class="col-md-12" >
                    <input type="text" id="dateFrom" style="margin: 0px 42px;margin-bottom: 10px;">
                    <input type="text" id="dateTo" style="margin: 0px 42px;margin-bottom: 10px;">
                    <div id="history-calendar">

                    </div>     
                </div>

                <div class="col-md-12 text-right" >
                    <button id="month-payment-range" class="btn btn-info btn-sm " style="background-color: #5bc0de;border-color: #46b8da;padding: 5px 10px;font-size: 12px;margin-top: 5px;margin-left: 0;">This Month</button>

                    <button id="submit-payment-range" class="btn btn-primary btn-sm " style="background-color: #337ab7;border-color: #2e6da4;padding: 5px 10px;font-size: 12px;margin-top: 5px;margin-left: 0;">Submit</button>
                </div>
               
            </div>

            <a href="" id="payment-history-download" class="btn btn-default"><i class="glyphicon glyphicon-file"></i>  Export as .XLS</a>
        </div>

        <div class="col-md-12 no-padding" style="overflow: auto;">
          <div class="history-table-wrapper no-padding">
            <table class="table table-responsive table-history">
                <thead>
                    <tr>
                        <th>Clinic</th>
                        <th>PaymentDate</th>
                        <th>Customer</th>
                        <th>Staff</th>
                        <th>Service/Class</th>
                        <th>Initial Booking Date</th>
                        <th>Appt/Class Date</th>
                        <th>Total Amount</th>
                        <th>Collected Amount</th>
                        <th>Medi-Credit</th>
                        <th>Medicloud Transaction Fees</th>
                        <th>Payment to Clinic</th>
                        <th>Paid By Medicloud Status</th>
                    </tr>
                </thead>
                <tbody id="payment-transaction-view">
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
        onSelect: function(date) {
          if( selectedActive == 1 ){
            $( "#dateFrom" ).val(moment(date).format('MMM DD, YYYY'));
            selectedFrom = date;
            
          }else{
            $( "#dateTo" ).val(moment(date).format('MMM DD, YYYY'));
            selectedTo = date;
          }

          setTimeout(function() {
            $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');
          }, 10);
        },
        onChangeMonthYear: function (year,month,day) {
          console.log(year);
          console.log(month);
          console.log(day);
          currentMonth = month - 1;
        }
      });


      $( "#payment-history-range-from" ).text(moment($( "#history-calendar" ).datepicker('getDate')).format('MMM D'));
      $( "#payment-history-range-to" ).text(moment($( "#history-calendar" ).datepicker('getDate')).format('MMM D'));
      setTimeout(function() {
        $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');
      }, 10);
      
    }


  $( "body" ).on("click","#payment-history-range-btn",function(){
      $( ".history-range-wrapper" ).toggle();
      $('#search_terms').val('');
      initializeCalendar();
      $('#payment-transaction-view').html('');
  });

  $( "body" ).on("click","#history-range-datepicker",function(){
      $( ".history-range-wrapper" ).toggle();
  });



  $( "body" ).on("click","#month-payment-range",function(){
    var date = new Date();
    console.log(currentMonth);
    var firstDay = new Date(date.getFullYear(), currentMonth , 1);
    var lastDay = new Date(date.getFullYear(), currentMonth + 1, 0);

    // console.log(firstDay + ' ' + lastDay);

    $("#dateFrom").val( moment(firstDay).format('MMM D, YYYY') );
    $("#dateTo").val( moment(lastDay).format('MMM D, YYYY') );

  });



  $( "body" ).on("click","#submit-payment-range",function(){

    $( "#payment-history-range-from" ).text(moment(selectedFrom).format('MMM D'));
    $( "#payment-history-range-to" ).text(moment(selectedTo).format('MMM D'));
    $( ".history-range-wrapper" ).toggle();

    var start = moment(selectedFrom).format('YYYY-MM-DD');
    var end = moment(selectedTo).format('YYYY-MM-DD');

    // jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
    
    $('.blockUI-hide').show();
    $('.blockUI').hide();

    setTimeout(function(){
      $('.blockUI-hide').block({ 
          message: '<h1> '+base_loading_image+' <br /> <br /> Fetching Data..</h1>'
      });
    },10);

    $.ajax({
       url: window.base_url + 'clinic/view/payment/transaction/byDate',
       type: 'POST',
       data: { start: start, end: end, search: null }
    })
    .done(function(data){
      if(data.length == 0) {
        $('#payment-history-download').attr('disabled', true);
        $('#payment-transaction-view').html('<tr><td><h6>No data available in table</h6></td></tr>');
      } else {
        $('#payment-history-download').attr('disabled', false);
        $('#payment-transaction-view').html(data);

        $('.history-table-wrapper').css({height:'400px'});
      }
      $('#payment-history-download').attr("href", base_url + 'admin/transaction/payment/download/' + start + '/' + end)
      // $('#payment-transaction-view').html(data);

      setTimeout(function(){
        $('.blockUI-hide').hide();
        $('.blockUI').show();
        $('.blockUI-hide').unblock();

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
});
$('#search_terms').on('input', function( ){
    var search = this.value;
     $.ajax({
       url: window.base_url + 'clinic/view/payment/transaction/byDate',
       type: 'POST',
       data: { start: null, end: null, search: search }
    })
    .done(function(data){
      if(data.length == 0) {
        $('#payment-history-download').attr('disabled', true);
        $('#payment-transaction-view').html('<tr><td><h6>No data available in table</h6></td></tr>');
      } else {
        $('#payment-history-download').attr('disabled', false);
        $('#payment-transaction-view').html(data);
      }
      $('#payment-history-download').attr("href", base_url + 'admin/transaction/payment/search/download/' + search)

      var page_height = $('#payments-detail-wrapper').height()+52;
      var win_height = $(window).height()
      $('.history-table-wrapper').css({height:'400px'});
      $('.admin-transac-history').css({overflow: 'hidden'});
      if (page_height > win_height){

          $("#setting-navigation").height($('#payments-detail-wrapper').height()+52);
          $("#payments-side-list").height($('#payments-detail-wrapper').height()+52);
      }
      else{

          $("#setting-navigation").height($(window).height()-52);
          $("#payments-side-list").height($(window).height()-52);
      }

    });
 });


// FILTER DROPDOWN

$(".filterOption li a").click(function(){

  $(this).parents(".dropdown").find('.selection').text($(this).text());
  $(this).parents(".dropdown").find('.selection').val($(this).text());

});
</script>
@include('admin.footer-admin')
