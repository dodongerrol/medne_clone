
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.debug.js"></script> -->
<style type="text/css">
    #statement-calendar .ui-datepicker-calendar{
        display: none;
    }

    .statement-wrapper {
        width: 300px;
        padding: 15px 0;
        position: absolute;
        top: 40px;
        left: 0px;
        border-radius: 5px;
        display: none;
        box-shadow: none;
        border: 1px solid #ccc;
        background: #FFF;
        z-index: 10;
    }
</style>
<div id="editor"></div>
<br>
<div class="main-loader" hidden>
	<div class="circle-loader">
		<div class="preloader-container">
			<div class="preloader-wrapper big active">
			<div class="spinner-layer spinner-blue-only">
				<div class="circle-clipper left">
				<div class="circle"></div>
				</div><div class="gap-patch">
				<div class="circle"></div>
				</div><div class="circle-clipper right">
				<div class="circle"></div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="clinicID" value="{{$clinicdetails['clinicid']}}">
<div class="transac-invoice">

    <div class="col-md-12" style="padding: 0px; padding-bottom: 20px;">
        <span style="padding-top: 15px; font-size: large; font-weight: bold;">View Invoice</span>
    </div>

    <!-- <div class="col-md-12" style="padding: 0px; padding-bottom: 20px; border-bottom: 1px solid #ccc;margin: 20px 0;">
		<div class="btn-group">
		  <button type="button" class="btn btn-default btn-date-text">
		  	<span class="statement-payment-range"><span class="month">Jan</span> <span class="year">2017</span></span>
		  </button>
		  <button id="statement-payment-range-btn" type="button" class="btn btn-default" >
		    <span class="caret"></span>
		    <span class="sr-only">Toggle Dropdown</span>
		  </button>
		</div>

            <div class="statement-wrapper">
                <a id="statement-datepicker" href="#" style="position: absolute;top: 3px;right: 17px;"><i class="fa fa-times-circle red" style="font-size: 15px;"></i></a>

                <div id="statement-calendar">

                </div>
            </div>

        <button id="invoice-date-go-btn" class="btn btn-default btn-go">Go</button>

        <a href="javascript:void(0)" id="invoice-download-as-pdf" class="btn btn-default btn-export"><i class="glyphicon glyphicon-file" hidden></i>  Export as .PDF</a>
    </div> -->

    <div class="statement-options" style="padding: 0px; padding-bottom: 40px;">
        <div class="statement-calendar-picker" style="width: 180px;">
            <div class="icon-wrapper">
                <i class="fa fa-calendar"></i>
            </div>
            <input id="invoice-statement-calendar" type="text" ng-model="statement_monthyear" readonly>
            <div class="icon-wrapper">
                <i class="fa fa-caret-down"></i>
            </div>
        </div>
        <button id="invoice-date-go-btn" class="btn">Go</button>
        <a href="javascript:void(0)" id="invoice-download-as-pdf" class="btn btn-default btn-export"><i class="glyphicon glyphicon-file" hidden></i>  Export as .PDF</a>
    </div>

    <div class="col-md-12 invoice-wrapper no-padding" id="error-log" hidden>
        <h3 class="error-log-status" style="text-align: center;
    margin-bottom: 15px;"></h3>
    </div>
    <div class="col-md-12 invoice-wrapper no-padding" id="pdf-print" hidden>
        <input type="hidden" id="invoice-id">
        <div class="header">

            <div class="col-md-12 text-right" style="padding: 0;">
                <div id="clinic-logo-container">
                    <img src="{{ URL::asset('assets/images/img-portfolio-place.png') }}" class="pull-left" style="max-width: 250px;max-height: 135px;border: 1px solid #F1F1F1;margin-top: 30px;" crossOrigin="Anonymous">
                </div>
                <b>
                    <h1 style="font-size: 35px !important;color: #000 !important;font-family: 'Open Sans', sans-serif !important;">INVOICE</h1>
                    <p style="color: #999;font-weight: 700;margin-bottom: 20px;">Mednefits Wallet ( <span class="medni_wallet_period"></span> )</p>
                    <p><label style="color: #555;margin-right: 10px;">Clinic Name:</label> <span class="clinic_name"></span></p>
                    <p><label style="color: #555;margin-right: 10px;">Billing/Payable Name(Bank):</label> <span class="billing_name"></span></p>
                    <p><label style="color: #555;margin-right: 10px;">Clinic Address:</label> <span class="clinic_address" ></span></p>
                    <p><label style="color: #555;margin-right: 10px;">Billing Address:</label> <span class="billing_address" ></span></p>
                </b>
            </div>
        </div>

        <div class="bill-to">
            <div class="col-md-6 no-padding" style="width: 48%">
                <div class="left-wrapper">
                    <h5 style="color: #aaa;margin-bottom: 0;"><b>BILL TO</b></h5>
                    <h5 style="margin-top: 0;"><b>Medicloud Private Limited</b></h5>
                    <p>7 Temasek Boulevard #18-02 Suntec Tower One</p>
                    <p>038987</p>
                    <p>Singapore</p>
                </div>
            </div>
            <div class="col-md-6 no-padding" style="width: 52%">
                <div class="right-wrapper">
                    <p><label>Invoice Number: </label> <span class="invoice_number"></span></p>
                    <!-- <p><label>Invoice Date: </label> <span class="invoice_first_day"></span></p>
                    <p><label>Payment Due: </label> <span class="invoice_due_date"></span></p> -->
                    <p><label>Period Date: </label> <span class="period_date">1 Dec - 31 Dec 2018</span></p>
                    <p>
                        <label>Amount Due 
                          (<span class="currencyType" style="text-transform: uppercase;"></span>): 
                        </label> 
                        <b>
                          <span class="currencyType" style="text-transform: uppercase;"></span>
                          <span class="invoice_amount_due"></span></b></p>
                </div>
            </div>
        </div>

        <div class="description">
            <p><label  style="text-decoration: underline;">Descriptions:</label></p>
            <p><label>Period: </label> <span class="period_date">01 Sep</span></p>
            <p><label>Total Transactions: </label> <span class="total_transactions">5</span></p>
            <p><label>Mednefits Credit Transactions: </label> <span class="credit_transactions">2</span></p>
            <p style="margin-bottom: 10px;"><label>Cash/Creditcard Transactions: </label> <span class="cash_creditcard_transactions">3</span></p>
            <p><label>Transactions Break down</label></p>
        </div>

        <table class="table table-responsive text-center" style="width: 91%;margin: 0px auto 30px auto;">
            <thead>
                <tr>
                    <th style="width: 40%;text-align: left !important;">Items</th>
                    <th>Mednefits Fee</th>
                    <th>Mednefits Credit</th>
                    <th>Total Amount</th>
                </tr>
            </thead>

            <tbody id="invoice-items-table">
                <!-- <tr>
                    <td style="text-align: left !important; ">
                        <p><b>Polishing and Scaling</b></p>
                        <p>Customer: Filbert</p>
                        <p>Payment Date: 10 Jan 2017</p>
                        <p>Co-payment: 10% of SGD$130</p>
                    </td>
                    <td><b>1</b></td>
                    <td><b>$13.00</b></td>
                    <td><b>$13.00</b></td>
                </tr> -->

                <!-- <tr>
                    <td style="text-align: left !important; ">
                        <p><b style="font-family: 'Helvetica Medium';">Period:</b> 1 Jan - 31 Jan 2018</p>
                        <p><b style="font-family: 'Helvetica Medium';">Total Transactions:</b> 20</p>
                        <p><b style="font-family: 'Helvetica Medium';">Transactions Breakdown</b></p>
                        <p>Mednefits Credit: 15</p>
                        <p>Cash         : 5</p>
                    </td>
                    <td><b>S$ 278.20</b></td>
                    <td><b>S$ 150.00</b></td>
                    <td><b>S$ 248.20</b></td>
                </tr> -->

            </tbody>
        </table>

       <!--  <div class="notes">
            <div class="col-md-12 text-left" style="width: 95%;">
                <p style="margin-bottom: 10px"><b>Notes</b></p>
                <p>Payment method: Bank Transfer/Cheque</p>
                <p>Payee's Name: <span class="invoice_bank_name">N/a</span></p>
                <p>Account Type: <span class="invoice_account_type">N/a</span></p>
                <p>Payee's Account Number: <span class="invoice_account_number">132-66713-3</span></p>
            </div>
        </div> -->

        <div class="copyright text-center">
            <h5 style="color: #999;"><b>&copy; 2020 Mednefits. All rights reserved</b></h5>
        </div>

    </div>



</div>

<script type="text/javascript">
    window.localStorage.setItem('invoice-view', false);
</script>
