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
				background: #F8FAFC;
			}

			.invoice-content .header{
				border-bottom: 1px solid #F1F1F1;
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
		    /*background: #eee;*/
		    /*padding: 10px 0;*/
		    padding-right: 30px;
			}

			.bill-to .right-wrapper label {
		    width: 210px;
		    text-align: right;
		    margin-right: 15px;
		    display: inline-block;
			}

			.invoice-content table{
				border-collapse: collapse;
			}

			.invoice-content table .thead th {
		    text-align: left;
		    padding: 8px;
			}

			.invoice-content table .tbody td {
        padding: 15px 8px;
        text-align: left;
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

	    	<div class="col-md-12 text-center">
	    		<h1 style="font-size: 35px !important;color: #999 !important;font-family: 'Open Sans', sans-serif !important;margin-bottom: 0px;">STATEMENT OF ACCOUNT</h1>
	    		<p style="margin-top: 5px;">(Generated on Jan 04, 2019)</p>
	    	</div>

	    	<div class="col-md-12">
	    		<div class="item">
		    		<div id="clinic-logo-container" style="text-align: left;">
	            
	            <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" style="max-width: 250px;max-height: 135px;">
	          </div>
          </div>
          <div class="item" style="text-align: right;">
		    		<p class="statement_bank_name" style="margin-bottom: 10px;font-weight: 700;">statement_bank_name</p>
		    		<p class="statement_bank_address" style="font-weight: 700;">statement_bank_address</p>
	    		</div>
	    	</div>
    	</div>

    	<div class="bill-to">
	    	<div class="item left-wrapper" >
	    		<p style="margin: 0;">BILL TO</p>
	    		<p style="margin: 0 0 10px 0;">Medicloud Private Limited</p>
	    		<p>7 Temasek Boulevard</p>
	    		<p>#18-02 Suntec Tower One</p>
	    		<p>038987</p>
	    	</div>
	    	<div class="item right-wrapper" >
	    		<p style="margin: 0;text-align: right;padding-right: 30px;">Account Summary</p>
    			<p><label>Invoiced: </label> $1230.00</p>
    			<p><label>Payments: </label> ($413.84.00)</p>
    			<p><label>Ending Balance Jan 04, 2019: </label> $816.16</p>
	    	</div>
    	</div>

    	<div style="width: 95%;margin:0 auto 20px auto;">
    		<div style="background: #EEE;padding: 15px;color: #AFAFAF !important;text-align: center;">
    			SHOWING ALL INVOICES AND PAYMENTS BETWEEN <span class="statement_start">Jan 01, 2019</span> AND <span class="statement_end">Jan 31, 2019</span>
    		</div>
    	</div>

    	<table class="table table-responsive text-center" style="border-bottom: 2px solid #DCDFE0;width: 95%;margin:0 auto;">
  			<tr class="thead">
  				<th>Date</th>
  				<th>Details</th>
  				<th style="text-align: right !important;">Amount</th>
  				<th style="text-align: right !important;">Balance</th>
  			</tr>

        <tr class="tbody">
          <td style="text-align: left !important;"><b>Jan 01, 2019</b></td>
          <td><b>Invoice #MNAL00004 (due Jan 31, 2019)</b></td>
          <td style="text-align: right !important;"><b>S$1230.00	</b></td>
          <td style="text-align: right !important;"><b>S$1230.00</b></td>
        </tr>

        <tr class="tbody">
          <td style="text-align: left !important;"><b>Jan 01, 2019</b></td>
          <td><b>Payment Invoice #MNAL00004</b></td>
          <td style="text-align: right !important;"><b>(S$413.84.00)</b></td>
          <td style="text-align: right !important;"><b>S$816.16</b></td>
        </tr>

        <tr class="tbody">
          <td style="text-align: left !important;"><b>Jan 01, 2019</b></td>
          <td><b>Invoice #MNAL00004 (due Jan 31, 2019)</b></td>
          <td style="text-align: right !important;"><b></b></td>
          <td style="text-align: right !important;"><b>S$816.16</b></td>
        </tr>

    	</table>

    	<div class="col-md-12 total text-right" style="width: 94.5%;text-align: right;position: relative;height: 135px;">
    		<div style="width: 250px;display: inline-block;position: absolute;right: 15px;top: 25px;">
    			<p style="margin-bottom: 10px;">Amount due (SGD)</p>
					<p style="margin-top: 10px;">$816.16</p>
    		</div>
    	</div>

    	<div class="col-md-12 copyright text-center">
    		<h5 style="color: #999;"><b>&copy; 2019 Mednefits. All rights reserved</b></h5>
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