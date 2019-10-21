<!DOCTYPE html>
<html><head>
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Spending Invoice Transction Lists</title>
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
				width: 200px;
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
				margin-bottom: 40px;
			}

			.invoice-content table .thead th {
		    text-align: left;
		    background: #0392CF;
		    color: #FFF;
		    border-color: #0392CF;
		    padding: 8px;
			}

			.invoice-content table .tbody td {
		    padding: 12px 8px;
		    text-align: left;
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
			<div class="header">
				<div class="item">
          <p style="margin-top:30px;margin-bottom: 15px;font-weight: 700;color: #666;">Statement for {{ $statement_start_date }} - {{ $statement_end_date }}</p>
					<p style="margin-bottom: 15px;font-weight: 700;width: 400px;color: #666;">Full Company Statement - In-Network Spending Account {{ $company }}</p>
					<p class="weight-300">{{ $statement_contact_name }}</p>
					<p class="weight-300">{{ $statement_contact_number }}</p>
					<p class="weight-300">{{ $statement_contact_email }}</p>
	    	</div>

	    	<div class="text-right item">
	    		<div style="margin-bottom: 20px;">
	    			<img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" style="width: 250px;">
	    		</div>
	    		<p class="no-margin" style="font-weight:700;color: #666;">Medicloud Pte Ltd</p>
          <p class="weight-300">7 Temasek Boulevard</p>
          <p class="weight-300">#18-02 Suntec Tower One</p>
          <p class="weight-300">038987</p>
          <p class="weight-300" style="margin-bottom: 20px;">Singapore</p>
          <p class="weight-300">+65 6254 7889</p>
          <p class="weight-300">mednefits.com</p>
	    	</div>
    	</div>

    	<div class="bill-to">
	    	<div class="item">
					<p>Total Transactions</p>
					<p style="margin-top: 5px;color: #4d8ad6;">{{ sizeOf($in_network) }}</p>
				</div>

				<div class="item">
					<p>Total Spent</p>
					<p style="margin-top: 5px;color: #4d8ad6;">{{ strtoupper($currency_type) }} {{ $statement_total_amount }}</p>
				</div>
    	</div>

			<table class="text-center" style="border-bottom: 2px solid #DCDFE0;width: 100%;">
  			<tr class="thead">
				<th>TRANSACTION #</th>
				<th>EMPLOYEE</th>
				<!-- <th>NRIC</th> -->
  				<th>Date</th>
				<th>CATEGORY</th>
				<th>ITEM/SERVICE</th>
				<th>PROVIDER</th>
				<th>TOTAL AMOUNT</th>
				@if($lite_plan)
				<th>MEDICINE & TREATMENT</th>
				<th>CONSULTATION</th>
				@endif
				<!-- <th>PAYMENT TYPE</th> -->
  			</tr>

  			<!-- LOOP HERE -->
  			@foreach($in_network as $key => $trans)
        <tr class="tbody">
				<td>{{ $trans['transaction_id'] }}</td>
				<td>{{ $trans['member'] }}</td>
				<!-- <td>{{ $trans['nric'] }}</td> -->
	          	<td>{{ $trans['date_of_transaction'] }}</td>
				<td>{{ $trans['clinic_type_name'] }}</td>
				<td>{{ $trans['clinic_type_and_service'] }}</td>
				<td>{{ $trans['clinic_name'] }}</td>
				<td>{{ $trans['currency_type'] }} {{ $trans['total_amount'] }}</td>
				@if($lite_plan)
				<td>{{ $trans['currency_type'] }} {{ $trans['treatment'] }}</td>
				<td>{{ $trans['currency_type'] }} {{ $trans['consultation'] }}</td>
				@endif
			<!-- <td>{{ $trans['payment_type'] }}</td> -->
        </tr>
        @endforeach
    	</table>

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
