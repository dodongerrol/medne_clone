<!DOCTYPE html>
<html><head>
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Spendig Invoice Out-Of-Network Transactions</title>
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
				font-size: 16px;
				line-height: 1.42857143;
			}

			.statement-invoice .header{
				background: #0086D3;
    		padding: 5px 30px;
			}

			.statement-invoice table{
				margin: 15px auto;
    		width: 98%;
    		border-collapse: collapse;
			}

			.statement-invoice table .thead{
				border-bottom: 1px solid #d0d0d0;
			}

			.statement-invoice table .thead th {
		    background: #C6E7F7;
		    color: #555;
		    border: none;
		    padding: 10px 10px 5px 10px;
		    font-size: 12px;
		    text-align: left;
			}

			.statement-invoice table .tbody td {
		    color: #888;
		    border: none;
		    padding: 12px 10px;
		    font-weight: 700;
		    vertical-align: middle;
		    border-bottom: 1px solid #eae7e7;
		    font-size: 14px;
			}

			.statement-invoice .status-box {
			    padding: 6px;
			    width: 80px;
			    color: #FFF;
			    border-radius: 5px;
			    margin: 0;
			    height: 12px;
			    display: inline-block;
			    line-height: 12px;
			    margin: 0 auto;
			    font-style: normal;
			}

			.statement-invoice .status-box.pending{
			    background: #909090;
			}

			.statement-invoice .status-box.approved{
			    background: #2fa235;
			}

			.statement-invoice .status-box.rejected{
			    background: rgba(255,58,62,0.66);
			}
		</style>
	</head><body>
		<div class="statement-invoice">
			<div class="header">
				<h4 class="color-white weight-700" style="margin: 5px 0;font-size: 18px;">Statement for <span>{{ $statement }}</span></h4>
			</div>
			<table>
				<tr class="thead">
					<th></th>
					<th>APPROVED DATE</th>
					<th>CLAIM DATE</th>
					<th>ITEM/SERVICE</th>
					<th>PROVIDER</th>
					<th>TOTAL AMOUNT</th>
					<th>EMPLOYEE</th>
				</tr>

				<!-- ======== TBODY : LOOP HERE ======= -->
				@foreach($transaction_details as $key => $data)
				<tr class="tbody">
					<td class="text-center">
						<i class="status-box approved" >Approved</i>
					</td>
					<td>{{ $data['approved_date'] }}</td>
					<td>{{ $data['claim_date'] }}</td>
					<td>{{ $data['service'] }}</td>
					<td>{{ $data['merchant'] }}</td>
					<td>S$ {{ $data['amount'] }}</td>
					<td>{{ $data['member'] }}</td>
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
</style>