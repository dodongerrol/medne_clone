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

<br>
<input type="hidden" id="clinicID" value="{{$clinicdetails['clinicid']}}">
<div class="container transac-statement">

    <div class="col-md-12" style="padding: 0px; padding-bottom: 40px;">
        <span style="padding-top: 15px; font-size: large; font-weight: bold;">View Statement of Account</span>
    </div>

    <div class="statement-options" style="padding: 0px; padding-bottom: 20px; border-bottom: 1px solid #ccc;">
    	<div class="statement-calendar-picker">
    		<div class="icon-wrapper">
    			<i class="fa fa-calendar"></i>
    		</div>
    		<input id="statement-calendar" type="text" ng-model="statement_monthyear" readonly>
    		<div class="icon-wrapper">
    			<i class="fa fa-caret-down"></i>
    		</div>
    	</div>
    	<button id="statement-date-go-btn" class="btn">Go</button>
        <!-- <a href="javascript:void(0)" id="payment-history-download" class="btn btn-default btn-export" disabled><i class="glyphicon glyphicon-file"></i>  Export as .PDF</a> -->
    </div>

    
    <div id="table-statement" class="col-md-12" style="padding-top: 30px;">
		<ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#all" aria-controls="all" role="tab" data-toggle="tab" style="border-top: 3px solid #029EB6;border-radius: 0;">All Invoices</a></li>
		  </ul>

		  <!-- Tab panes -->
		  <div class="tab-content">
		    <div role="tabpanel" class="tab-pane active" id="all">
		    	<table class="table table-responsive" style="margin-top: 20px;">
		    		<thead>
		    			<tr>
		    				<th>Status</th>
		    				<th>Date</th>
		    				<th>Number</th>
		    				<th>Customer</th>
		    				<th>Total</th>
		    				<th>Amount Due</th>
		    				<th>Actions</th>
		    			</tr>
		    		</thead>

		    		<tbody id="transaction-statement-table">
		    			<!-- <tr>
		    				<td>
		    					<div class="status paid" hidden>
		    						PAID
		    					</div>

		    					<div class="status unpaid" >
		    						UNPAID
		    					</div>
		    				</td>
		    				<td> Feb 01, 2017 </td>
		    				<td> MNTO00001 </td>
		    				<td> Mednefits Pte Ltd </td>
		    				<td> $18.00 </td>
		    				<td> $0.00 </td>
		    				<td>
		    					<a class="view-statement-button" href="javascript:void(0)">View </a>
		    					<div class="btn-group">
								  <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    <span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu">
								    <li><a href="#">Export as PDF</a></li>
								    <li><a href="#">Print</a></li>
								  </ul>
								</div>
		    				</td>
		    			</tr> -->

		    		</tbody>
		    	</table>
		    </div>
		  </div>
    </div>

    <div id="view-statement" class="col-md-12" style="padding:0;padding-top: 30px" hidden>
    	<div class="col-md-12 invoice-wrapper" id="pdf-print">
    		<div class="col-md-12 text-right">
    			<a id="back-button" href="javascript:void(0)" style="color: #000 !important;margin-right: 15px;"> <i class="fa fa-times"></i></a>
    		</div>
	    	<div class="header">
		    	<div class="col-md-12 text-center " style="margin-bottom: 40px;">
		    		<h1 style="font-size: 22px !important;color: #999 !important;font-family: 'Open Sans', sans-serif !important;"><b>STATEMENT OF ACCOUNT</b></h1>
		    		<p>(Generated on <span class="statement_created_at">N/a</span>)</p>
		    	</div>
		    	<!--  -->
		    	<div class="col-md-12 text-right">
		    		<div id="clinic-logo-container">
		    			<img src="{{ URL::asset('assets/images/img-portfolio-place.png') }}" class="pull-left" style="max-width: 250px;max-height: 135px;border: 1px solid #F1F1F1;">
		    		</div>
		    		<br>
		    		<b>
		    		<p class="statement_bank_name" style="margin-bottom: 10px">N/a</p>
		    		<p class="statement_bank_address" style="width: 215px;display: inline-block;">N/a</p>
		    		</b>
		    	</div>
	    	</div>

	    	<div class="bill-to">
		    	<div class="col-md-6 no-padding">
		    		<div class="left-wrapper">
			    		<h5 style="margin-bottom: 0;">BILL TO</h5>
			    		<h5 style="margin-top: 0;"><b>Medicloud Private Limited</b></h5>
			    		<p>7 Temasek Boulevard</p> 
			    		<p>#18-02 Suntec Tower One</p>
			    		<p>038987</p>
		    		</div>	
		    	</div>
		    	<div class="col-md-6 no-padding">
		    		<div class="right-wrapper">
		    			<h5><b>Account Summary</b></h5>
		    			<br>
		    			<p>Invoiced: <label> $<span class="statement_amount_due">0</span>  </label></p>
		    			<p>Payments: <label> ($<span class="statement_amount_paid">0</span>)  </label></p>
		    			<p>Ending Balance <span class="statement_created_at">N/a</span>: <label> $<span class="statement_amount_total">0</span></label></p>
		    		</div>
		    	</div>
	    	</div>

	    	<div class="col-md-12 no-padding" style="margin-bottom: 20px;">
	    		<div class="section-wrapper text-center">
	    			SHOWING ALL INVOICES AND PAYMENTS BETWEEN <span class="statement_start">FEB 01, 2017</span> AND <span class="statement_end">FEB 28, 2017</span>
	    		</div>
	    	</div>

	    	<table class="table table-responsive text-center" style="border-bottom: 1px solid #F1F1F1">
	    		<thead>
	    			<tr>
	    				<th style="width: 20%;text-align: left !important;padding-left: 30px;">Date</th>
	    				<th style="width: 40%;text-align: left !important;">Details</th>
	    				<th style="text-align: right;">Amount</th>
	    				<th style="text-align: right;">Balance</th>
	    			</tr>
	    		</thead>

	    		<tbody>
	    			<tr>
	    				<td style="text-align: left !important;padding-left: 30px; ">
	    					<span class="statement_start">Feb 01,2017</span>
	    				</td>
	    				<td style="text-align: left;"><b>Invoice #<span class="statement_invoice_number">MNCP00002</span></b> (due <span class="statement_end">FEB 28, 2017</span>)</td>
	    				<td style="text-align: right;">$<span class="statement_amount_due">0</span></td>
	    				<td style="text-align: right;">$<span class="statement_amount_due">0</span></td>
	    			</tr>
	    			<tr>
	    				<td style="text-align: left !important;padding-left: 30px; ">
	    					<span class="statement_end">Feb 28,2017</span>
	    				</td>
	    				<td style="text-align: left;">Payment <b>Invoice #<span class="statement_invoice_number">MNCP00002</span></b></td>
	    				<td style="text-align: right;">($<span class="statement_amount_paid">0</span>)</td>
	    				<td style="text-align: right;">$<span class="statement_amount_total">0</span></td>
	    			</tr>
	    			<tr>
	    				<td style="text-align: left !important;padding-left: 30px; ">
	    					<span class="statement_created_at">Mar 03,2017</span>
	    				</td>
	    				<td style="text-align: left;">Ending Balance</td>
	    				<td style="text-align: right;"></td>
	    				<td style="text-align: right;">$<span class="statement_amount_total">0</span></td>
	    			</tr>

	    		</tbody>
	    	</table>

	    	<div class="total">
	    		<div class="col-md-12 text-right">
	    			<h4>Amount due (SGD)</h4>
	    			<h4><b>$<span class="statement_amount_total">0</span></b></h4>
	    		</div>
	    	</div>
	    	
	    </div>
    </div>
    
    
</div>

<script type="text/javascript">
	
	$( "#back-button" ).click(function(){
		$( "#view-statement" ).hide();
		$( "#table-statement" ).fadeIn();
	});

	window.localStorage.setItem('statement-view', false);
</script>




