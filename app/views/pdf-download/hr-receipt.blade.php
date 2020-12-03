<!DOCTYPE html>
<html><head>
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Receipt</title>
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

			.receipt-content{
				border: 1px solid #EEE;
				overflow: hidden;
				margin: 10px auto;
				width: 500px;
				position: relative;

			}

			.receipt-content .header{
				text-align: center; 
				border-bottom: 1px solid #e0e6ea;
				padding-bottom: 20px;
				width: 100%;
				display: inline-block;
				overflow: hidden;
				height: 320px;
			}

			.receipt-content .header p{
				margin: 0;
				font-weight: 700;
				font-size: 12px;
			}

			.receipt-content .body{
				padding: 20px 30px;
				width: 100%;
				/*display: inline-block;
				overflow: hidden;*/
				height: 240px;
			}

			.receipt-content .body p{
				margin: 0;
				font-size: 16px;
			}

			.receipt-content .amount{
				padding: 20px 20px 0 20px;
				text-align: center;
				border-top: 2px solid #e8eaeb;
    		border-bottom: 2px solid #e8eaeb;
    		width: 90%;
    		margin: 0 auto;
    		width: 100%;
				/*display: inline-block;*/
				height: 64px;
			}

			.receipt-content .method{
				padding: 20px 20px 0 20px;
				text-align: center;
				width: 100%;
				/*display: inline-block;*/
				height: 60px;
				margin: 0 auto;
			}

			.receipt-content .method p,
			.receipt-content .amount p{
				margin: 0;
				font-size: 16px;
			}
		</style>
	</head><body>
		<div class="receipt-content">
			<div class="header">
				<div style="padding-top:20px;width: 100%;height: 50px;display: block;">
					<img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" style="width: 190px;">
				</div>
				<p style="font-size: 30px;">Receipt</p>
				<p style="font-size: 14px;">Invoice #{{$invoice_number}}</p>
				<p style="font-size: 14px;color: #cbcfd1;">for {{$company}}</p>
				<p style="font-size: 14px;color: #cbcfd1;">paid on {{date('F d, Y', strtotime($paid_date))}}</p>
				<p style="color: #464d52;">Medicloud Pte Ltd 7 </p>
				<p style="color: #464d52;">Temasek Boulevard #18-02 Suntec Tower One</p>
				<p style="color: #464d52;">038987</p>
				<p style="color: #464d52;">Singapore</p> 
				<p style="color: #464d52;">Tel: +6562547889</p>
				<p style="color: #464d52;">mednefits.com</p> 
				<p style="color: #464d52;">support@mednefits.com</p>
			</div>

			<div class="body">
				<p style="margin-bottom: 10px;">Hi,</p>
				<p style="margin-bottom: 10px;">Here's your payment receipt for Invoice #{{$invoice_number}}, for ${{$amount_paid}} SGD.</p>
				<p style="margin-bottom: 10px;">You can always view your receipt online, at: https://medicloud.sg/company-benefits-dashboard#/account-and-billing/transactions</p>
				<p style="margin-bottom: 10px;">If you have any questions, please let us know.</p>
				<p style="margin-bottom: 10px;">Thanks,</p>
				<p>Your Mednefits Team</p>
				
			</div>

			<div class="amount">
				<p style="color: #464d52;"><b>Payment Amount: ${{$amount_paid}} SGD</b></p>
				@if(isset($notes))
					@if($notes && $notes != 'NULL')
					<h5>Note: {{ $notes }}</h5>
					@endif
				@endif
			</div>	

			<div class="method">
				<p style="color: #464d52;"><b>Payment Method: {{$payment_method}}</b></p>
			</div>	

			<div style="width: 100%;text-align: center;height: 150px;padding-top:50px;display: block;">
				<img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/paid.png" style="width: 25%;-ms-transform: rotate(-23deg);-webkit-transform: rotate(-23deg);transform: rotate(-23deg);">
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