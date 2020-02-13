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
				position: relative;
			}

			.invoice-content .header{
				border-bottom: 1px solid #DCDFE0;
		    padding: 20px 30px 20px 30px;
		    overflow: hidden;
		    z-index: 10;
			}

			.invoice-content .header .item{
				display: inline-block;
				width: 49.5%;
				vertical-align: top;
			}

			.invoice-content .header .item p{
				margin: 0;
			}

			.bill-to{
				/*border-bottom: 1px solid #DCDFE0;*/
		    padding: 12px 0px 12px 30px;
		    overflow: hidden;
		    width: 100%;
		    display: inline-block;
		    z-index: 10;
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
		    /*background: #eee;*/	
		    padding: 10px 0;
		    padding-right: 30px;
			}

			.bill-to .right-wrapper p {
		    margin-left: 40px;
		    margin-bottom: 10px;
			}

			.bill-to .right-wrapper label {
		    width: 140px;
		    text-align: right;
		    margin-right: 15px;
		    display: inline-block;
			}

			.invoice-content table{
				border-collapse: collapse;
				z-index: 10;
			}

			.invoice-content table .thead th {
		    text-align: center;
		    background: #0392CF;
		    color: #FFF;
		    border-color: #0392CF;
		    padding: 8px;
			}

			.invoice-content table .tbody td {
		    padding: 12px 8px;
			}

			.invoice-content table .tbody td p{
		    margin: 0;
		    font-size: 12px;
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
				font-size: 12px;
			}

			.copyright{
				padding-bottom: 20px;
			}
		</style>
	</head><body>
		<div class="invoice-content">
				<!-- <img src="{{ URL::asset('images/paid-text.png') }}" style="position: absolute;left: 25%;top: 2%;z-index: 1;width: 350px;">
				<p style="position: absolute;font-size: 18px;top: 54%;left: 26%;color: #44a1ce;font-weight: 700;">COMPLIMENTARY BY MEDNEFITS FOR 1 YEAR</p> -->
			<div class="header">
				<div class="item">
          <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" style="width: 250px;margin-top: 65px;">
	    	</div>

	    	<div class="text-right item">
	    		<h1 style="font-size: 35px !important;color: #000 !important;font-family: 'Open Sans', sans-serif !important;margin-bottom: 0px;">CANCELLATION</h1>
	    		<p style="font-weight: 700;margin-top: 10px;">Medicloud Pte Ltd </p>
	    		<p>7 Temasek Boulevard</p>
	    		<p>#18-02 Suntec Tower One</p>
	    		<p>038987</p>
	    		<p style="margin-top: 10px;">Singapore</p>
	    		<p>+65 6254 7889</p>
	    		<p>mednefits.com</p>
	    	</div>
    	</div>

    	<div class="bill-to">
	    	<div class="item left-wrapper" >
	    		<p>{{ $billing_info['company'] }}</p>
					<p>{{ $billing_info['first_name'] }} {{ $billing_info['last_name'] }}</p>
					<p>{{ $billing_info['address'] }},</p>
					<p>{{ $billing_info['postal'] }}</p>
					<p>Singapore</p>

					<p style="margin-top: 10px;">{{ $billing_info['phone'] }}</p>
					<p>{{ $billing_info['email'] }}</p>
	    	</div>
	    	<div class="item right-wrapper" >
    			<p><label>Cancellation Number: </label> {{ $cancellation_number }}</p>
    			<p><label>Cancellation Date: </label> {{ $cancellation_date }}</p>
    			<p style="background: #eee;margin-top: 20px;"><label>Amount Due ({{ $currency_type }}): </label> <b>{{ $amount_due }}</b></p>
	    	</div>
    	</div>

    	<table class="table table-responsive text-center" style="border-bottom: 2px solid #DCDFE0;width: 100%;">
  			<tr class="thead">
  				<th style="width: 40%;text-align: left !important;padding-left: 30px;">Items</th>
  				<th>Quantity</th>
  				<th>Price</th>
  				<th>Amount</th>
  			</tr>
  			@foreach($users as $key => $user)
        <tr class="tbody">
          <td style="text-align: left !important;padding-left: 30px;">
          	@if($user['has_no_user'] == false)
          	<p><b>Cancellation - {{ $user['name'] }}</b></p>
          	<p>Member: {{ $user['name'] }}</p>
          	<!-- <p>NRIC: {{ $user['nric'] }}</p> -->
          	@endif
          	<p>Period of use: {{ $user['period_of_used'] }}</p>
          	<p>Refund: 70% of unused period {{ $user['period_of_unused'] }}</p>
          </td>
          <td><b>1</b></td>
          <td><b>{{ $currency_type }} ({{ $user['after_amount'] }})</b></td>
          <td><b>{{ $currency_type }} ({{ $user['after_amount'] }})</b></td>
        </tr>
        @endforeach
    	</table>

    	<div class="col-md-12 total text-right" style="width: 90.5%;text-align: right;position: relative;height: 150px;">
    		<div style="width: 250px;display: inline-block;position: absolute;right: 10px;top: 15px;">
    			<p style="margin-bottom: 5px;margin-top: 10px;"><label>Total:</label> {{ $currency_type }} {{ $total_refund }}</p>

					<div style="border-bottom: 1px solid #aaa;display: inline-block;width: 100%;padding-bottom: 10px;"></div>

					<p style="margin-top: 5px;"><label>Amount Due ({{ $currency_type }}):</label> <b>{{ $amount_due }}</b></p>
    		</div>
    	</div>

    	<div class="col-md-12 notes">
  			<p><b>Notes</b></p>
  			<p>Cancellation refund will be made to the company by cheque in 10 - 14 working days.</p>
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