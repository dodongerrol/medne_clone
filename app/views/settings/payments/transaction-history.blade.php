

<br>

<div class="container transac-history">

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

                <div class="history-range-wrapper">
                <a id="history-range-datepicker" href="#" style="position: absolute;top: 3px;right: 22px;"><i class="fa fa-times-circle red" style="font-size: 20px;"></i></a>

                    <div class="col-md-11" >
                        <input type="text" id="dateFrom">
                        <input type="text" id="dateTo" >
                        <div id="history-calendar">

                        </div>     
                    </div>

                    <div class="col-md-11 text-right" >

                        <button id="submit-payment-range" class="btn btn-primary btn-sm " style="background-color: #337ab7;border-color: #2e6da4;padding: 5px 10px;font-size: 12px;margin-top: 5px;">Submit</button>
                    </div>
                   
                </div>

            <a href="" id="payment-history-download" class="btn btn-default" disabled><i class="glyphicon glyphicon-file"></i>  Export as .XLS</a>
        </div>

        <div class="col-md-12 no-padding" style="overflow: auto;">
          <div class="history-table-wrapper no-padding" style="overflow-x: auto;overflow-y: hidden;height:50px;width: 1900px;">
            <table class="table table-responsive table-history" style="display: flex;flex-flow: column;height: 100%;width:100%;">
                <thead style="flex: 0 0 auto;width: calc(100% - 0.9em);display: table;table-layout: fixed;">
                    <tr>
                        <th class="text-center" style="font-size: 12px;">PaymentDate</th>
                        <th class="text-center" style="font-size: 12px;">Customer</th>
                        <th class="text-center" style="font-size: 12px;">Staff</th>
                        <th class="text-center" style="font-size: 12px;">Service/Class</th>
                        <th class="text-center" style="font-size: 12px;">Initial Booking Date</th>
                        <th class="text-center" style="font-size: 12px;">Appt/Class Date</th>
                        <th class="text-center" style="font-size: 12px;">Total Amount</th>
                        <th class="text-center" style="font-size: 12px;">Collected Amount</th>
                        <th class="text-center" style="font-size: 12px;">Medi-Credit</th>
                        <th class="text-center" style="font-size: 12px;">Medicloud Transaction Fees</th>
                    </tr>
                </thead>
                <tbody id="payment-transaction-view" style="flex: 1 1 auto;display: block;overflow-y: scroll;">
                    

                </tbody>
            </table>
          </div>
        </div>
    </div>

    

    
    
</div>

<script type="text/javascript">
    $(function( ){
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
              $('#payment-history-download').attr("href", base_url + 'clinic/transaction/payment/search/download/' + search)

              var page_height = $('#payments-detail-wrapper').height()+52;
              var win_height = $(window).height()
              $('.history-table-wrapper').css({height:'400px'});
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
    });
</script>