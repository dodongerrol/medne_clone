<!DOCTYPE html>
<html><head>
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Statement</title>
		<style type="text/css">
			@page { margin: 10px; }

			* {
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}

			.col-md-12 {
				width: 100%;
				position: relative;
				min-height: 1px;
				padding-left: 15px;
				padding-right: 15px;
			}
			body{
				/*background-color: #f0f0f0;*/
				margin: 0;
				font-family: 'Helvetica Light',sans-serif;
				font-size: 14px;
				line-height: 1.42857143;
			}

			.invoice-content{
				border: 1px solid #EEE;
				overflow: hidden;
				margin: 10px auto;
				width: 98%;
			}

			.invoice-content .header{
				border-bottom: 1px solid #DCDFE0;
		    padding: 20px 30px 20px 30px;
		    overflow: hidden;
			}

			.invoice-content .header .item{
				display: inline-block;
				width: 49.5%;
				vertical-align: top;
			}

			.bill-to{
				/*border-bottom: 1px solid #DCDFE0;*/
		    padding: 25px 0px 25px 30px;
		    overflow: hidden;
		    width: 100%;
		    display: inline-block;
			}

			.bill-to .item{
				display: inline-block;
				width: 49.5%;
				vertical-align: top;
			}

			.bill-to .item p{
				margin: 0;
				color: #333;
			}

			.bill-to .right-wrapper {
		    background: #eee;
		    padding: 10px 0;
		    padding-right: 30px;
			}

			.bill-to .right-wrapper label {
		    width: 180px;
		    text-align: right;
		    margin-right: 15px;
		    display: inline-block;
			}

			.invoice-content table{
				border-collapse: collapse;
			}

			.invoice-content table .thead th {
		    text-align: center;
		    background: #0392CF;
		    color: #FFF;
		    border-color: #0392CF;
		    padding: 8px;
			}

			.invoice-content table .tbody td {
		    padding: 40px 8px;
			}

			.invoice-content table .tbody td p{
		    margin: 0;
			}

			.total{
				height: 300px;
			}

			.total p label {
	      width: 145px;
			  display: inline-block;
			  margin-right: 15px;
			}

			.notes p{
				margin:0;
			}

			.copyright{
				padding-bottom: 20px;
			}
		</style>
	</head><body>
		<div class="invoice-content">
			<div class="header">
	    	<div class="col-md-12 text-right">
	    		<h1 style="font-size: 35px !important;color: #000 !important;font-family: 'Open Sans', sans-serif !important;margin-bottom: 0px;">INVOICE</h1>
	    		<p style="color: #999;font-weight: 700;margin-top: 8px;">Mednefits Wallet ( {{ $period }} )</p>
	    	</div>

	    	<div class="col-md-12">
	    		<div class="item">
		    		<div id="clinic-logo-container" style="text-align: left;">
	            <img src="{{ $clinic->image ? $clinic->image : 'https://medicloud.sg/assets/images/img-portfolio-place.png' }}" style="max-width: 250px;max-height: 135px;">
	          </div>
          </div>
          <div class="item" style="text-align: right;">
		    		<p class="clinic_name" style="margin-bottom: 10px;font-weight: 700;">{{ ucwords($clinic->Name) }}</p>
		    		<p class="clinic_name" style="margin-bottom: 10px;font-weight: 700;">{{ $clinic->billing_name ? ucwords($clinic->billing_name) : $clinic->Name }}</p>
		    		<p class="clinic_address" style="font-weight: 700;">{{ $clinic->billing_address ? ucwords($clinic->billing_address) : $clinic->Address }}</p>
	    		</div>
	    	</div>
    	</div>

    	<div class="bill-to">
	    	<div class="item left-wrapper" >
	    		<h5 style="color: #aaa;margin: 0;"><b>BILL TO</b></h5>
	    		<h5 style="margin: 0 0 10px 0;"><b>Medicloud Private Limited</b></h5>
	    		<p>7 Temasek Boulevard #18-02 Suntec Tower One</p>
	    		<p>038987</p>
	    		<p>Singapore</p>
	    	</div>
	    	<div class="item right-wrapper" >
    			<p><label>Invoice Number: </label> {{ $payment_record->invoice_number }}</p>
    			<p><label>Invoice Date: </label> {{ $invoice_record['start_date'] }}</p>
    			<p><label>Payment Due: </label> {{ $invoice_record['end_date'] }}</p>
    			<p><label>Amount Due (SGD): </label> <b>$ {{ $amount_due }}</b></p>
	    	</div>
    	</div>

    	<table class="table table-responsive text-center" style="border-bottom: 2px solid #DCDFE0;width: 100%;">
  			<tr class="thead">
  				<th style="width: 40%;text-align: left !important;padding-left: 30px;">Items</th>
  				<th>Mednefits Fee</th>
  				<th>Mednefits Credit</th>
  				<th>Total Amount</th>
  			</tr>

        <tr class="tbody">
          <td style="text-align: left !important;padding-left: 30px;">
            <p><b>Period:</b> {{ $period }}</p>
            <p><b>Total Transactions:</b> {{ $total_transaction }}</p>
            <p><b>Transactions Breakdown</b></p>
            <p>Mednefits Credit: {{ $total_credits_transactions }}</p>
            <p>Cash : {{ $total_cash_transactions }}</p>
          </td>
          <td><b>S$ {{ $total_fees }}</b></td>
          <td><b>S$ {{ $mednefits_credits }}</b></td>
          <td><b>S$ {{ $total }}</b></td>
        </tr>

    	</table>

    	<div class="col-md-12 total text-right" style="width: 94.5%;text-align: right;position: relative;height: 200px;">
    		<div style="width: 250px;display: inline-block;position: absolute;right: 15px;top: 25px;">
    			<p><label>Total:</label> ${{ $total }}</p>

					<div style="border-bottom: 1px solid #aaa;display: inline-block;width: 100%;padding-bottom: 20px;"></div>

					<p style="padding-top: 10px;"><label>Amount Due (SGD):</label> <b>${{ $amount_due }}</b></p>
    		</div>
    	</div>

    	<div class="col-md-12 notes">
  			<p style="margin-bottom: 10px"><b>Notes</b></p>
  			<p >Corporate PayNow</p>
          	<p style="margin-bottom: 20px;">UEN: 201415681W</p>
  			<p>Payment method: Bank Transfer</p>
  			<p>Payee's Name: <span class="invoice_bank_name">{{ $bank_details ? $bank_details->bank_name : 'N/a' }}</span></p>
  			<p>Account Type: <span class="invoice_account_type">{{ $bank_details ? $bank_details->bank_account_type : 'N/a' }}</span></p>
  			<p>Payee's Account Number: <span class="invoice_account_number">{{ $bank_details ? $bank_details->bank_account_number : 'N/a' }}</span></p>
    	</div>

    	<div class="col-md-12 copyright text-center">
    		<h5 style="color: #999;"><b>&copy; 2020 Mednefits. All rights reserved</b></h5>
    	</div>

		</div>
		</body></html>

<style type="text/css">
	p {
		display: block;
		-webkit-margin-before: 1em;
		-webkit-margin-after: 1em;
		-webkit-margin-start: 0px;
		-webkit-margin-end: 0px;
	}
	.pull-right{
		position: absolute;
		right: 0;
		top: 0;
	}

	.text-right {
		text-align: right;
	}
	.text-center {
		text-align: center;
	}
	.no-padding{
		padding: 0;
	}
	.color-gray {
		color: #777;
	}
	.color-black3 {
		color: #555 !important;
	}
	.color-blue-custom2 {
		color: #009EC8 !important;
	}
	.font-medium2 {
		font-family: 'HelveticaNeueMed', sans-serif !important;
	}
	.line-height-1 {
		line-height: 1.3;
	}
	.no-margin-top {
		margin-top: 0 !important;
	}
	.no-margin{
		margin: 0 !important;
	}
	.weight-700{
		font-weight: 700;
	}
	.font-20 {
		font-size: 20px !important;
	}
	.font-14{
		font-size: 14px;
	}
	.font-16{
		font-size: 16px;
	}
	.white-space-10{
		height: 10px;
		width: 100%;
	}
	.white-space-20{
		height: 20px;
		width: 100%;
	}

	.color-white{
		color: #FFF;
	}
</style>
