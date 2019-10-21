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
				margin: 0;
				font-family: 'Helvetica Light',sans-serif;
				font-size: 16px;
				line-height: 1.42857143;
			}

			.transac-history-invoice{
				padding: 0 10px;
				width: 100%;
				overflow: hidden;
				display: inline-block;
			}

			.transac-history-invoice .inv-header{
				border-bottom: 2px solid #ddd;
		    display: inline-block;
		    width: 100%;
		    padding: 20px 10px 30px 10px;
			}

			.transac-history-invoice .inv-header .item{
				display: inline-block;
				width: 45%;
				overflow: hidden;
				padding: 0 15px;
			}

			.transac-history-invoice .inv-header .item p{
				margin: 0;
				font-size: 13px;
			}

			.transac-history-invoice .stats-box{
				padding: 20px 25px 30px 25px;
				display: inline-block;
		    width: 100%;
			}

			.transac-history-invoice .stats-box .item{
				display: inline-block;
		    width: 130px;
			}

			.transac-history-invoice .stats-box .item p{
				font-size: 13px;
				margin: 0 0 5px 0;
			}

			.stats-box .color-blue {
		    color: #0190CD;
			}

			.trans-invoice-tbl{
				border-collapse: collapse;
			}

			.trans-invoice-tbl .thead th {
		    padding: 10px;
		    color: #fff;
		    font-size: 10px;
		    border: none !important;
		    background: #1793CF;
		    text-align: left;
			}

			.trans-invoice-tbl .tbody td {
		    padding: 10px;
		    color: #666;
		    font-size: 12px;
		    border-top: none;
		    border-bottom: none;
		    padding: 25px 10px;
			}

			.trans-invoice-tbl .tbody:nth-child(odd) td {
		    background: #f1f1f1;
			}

			.trans-invoice-tbl .thead th:first-child, 
			.trans-invoice-tbl .tbody td:first-child {
			    padding-left: 20px;
			}

			.label-success {
			    background-color: #5cb85c;
			}
			.label {
			    display: inline;
			    padding: .2em .6em .3em;
			    font-size: 75%;
			    font-weight: bold;
			    line-height: 1;
			    color: #fff;
			    text-align: center;
			    white-space: nowrap;
			    vertical-align: baseline;
			    border-radius: .25em;
			}

			.label-custom{
				padding: 6px;
		    width: 60px;
		    color: #FFF;
		    border-radius: 5px;
		    margin: 0;
		    height: 12px;
		    display: inline-block;
		    line-height: 12px;
		    /*margin: 0 auto;*/
		    font-style: normal;
			}

		</style>
	</head><body>
		<div class="transac-history-invoice">
			<div class="col-md-12 inv-header">
				<div class="item">
					<div class="white-space-10"></div>
					<p class="weight-700 font-medium color-black3">Transaction for <span>{{ $period }}</span></p>
					<div class="white-space-10"></div>
					<div class="white-space-5"></div>
					<p class="weight-700 font-medium color-black3">Health Partner Transaction History</p>
					<p class="weight-700 font-medium color-black3">{{ $clinic_details['clinic_name'] }}</p>
					<div class="white-space-10"></div>
					<div class="white-space-5"></div>
					<p class="">{{ $clinic_details['address'] }}</p>
					<p class="">{{ $clinic_details['state'] }}</p>
					<p class="">{{ $clinic_details['country'] }} {{ $clinic_details['postal'] }}</p>
					<p class="">{{ $clinic_details['phone'] }}</p>
					<p class="">{{ $clinic_details['email'] }}</p>
				</div>
				<div class="item text-right">
					<img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" style="width: 150px;">
					<div class="white-space-10"></div>
					<div class="white-space-5"></div>
					<p class="weight-700 font-medium color-black3">Medicloud Pte Ltd</p>
					<p class="">7 Temasek Boulevard</p>
					<p class="">#18-02 Suntec Tower one</p>
					<p class="">038987</p>
					<p class="">Singapore</p>
					<div class="white-space-10"></div>
					<div class="white-space-5"></div>
					<p class="">+65 6254 7889</p>
					<p class="">mednefits.com</p>
				</div>
			</div>
			<br>
			<div class="col-md-12 stats-box">
				<div class="item">
					<p class="">Total Transactions</p>
					<p class="color-blue total-trans-num">{{ $total_transactions }}</p>
				</div>
				<div class="item">
					<p class="">Mednefits Wallet</p>
					<p class="color-blue">S$ <span class="medni-wallet-num">{{ $mednefits_wallet }}</span></p>
				</div>
			</div>

			<table class="table trans-invoice-tbl" style="width: 100%;">
				<tr class="thead">
					<th>DATE</th>
					<th>TRANSACTION ID</th>
					<th>NAME</th>
					<!-- <th>NRIC</th> -->
					<th>SERVICE/S</th>
					<th>MEDNEFITS FEE</th>
					<th>MEDNEFITS CREDIT</th>
					<th>CASH</th>
				</tr>

				<!-- LOOP HERE -->
				@foreach($transactions as $key => $trans)
				<tr class="tbody">
					<td>{{ $trans['date_of_transaction'] }}</td>
					<td>{{ $trans['transaction_id'] }} </td>
					<td>
						{{ $trans['user_name'] }}
						<br />
						@if($trans['deleted'])
						<label class="label label-success label-custom">{{ $trans['transaction_status'] }}</label>
						@endif
					</td>
					<!-- <td>{{ $trans['NRIC'] }}</td> -->
					<td>{{ $trans['procedure_name'] }}</td>
					<td>S$ {{ $trans['mednefits_fee'] }}</td>
					<td>S$ {{ $trans['mednefits_credits'] }}</td>
					<td>S$ {{ $trans['cash'] }}</td>
				</tr>
				@endforeach
			</table>
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

	.color-black3 {
    color: #555;
	}
</style>