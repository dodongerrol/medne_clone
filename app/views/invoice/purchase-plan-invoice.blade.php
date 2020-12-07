<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Corporate Purchase Invoice</title>
  {{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
  {{ HTML::style('assets/css/medicloudv3.css') }}
  {{ HTML::style('assets/settings/payments/payments.css') }}

  {{ HTML::script('assets/js/jquery.min.js') }}
  {{ HTML::script('assets/js/jspdf.debug.js') }}
  {{ HTML::script('assets/js/html2canvas.min.js') }}
  {{ HTML::script('assets/js/jquery.printElement.min.js') }}
  {{ HTML::script('assets/js/printThis.js') }}
</head>
<style type="text/css">
  body{
    width: 100%;
    height: 100%;
  }
  .transac-invoice .invoice-wrapper .bill-to .right-wrapper label
  {
    width: 100%!important;
    text-align: left!important;
  }
  .transac-invoice .invoice-wrapper .bill-to .right-wrapper
  {
    background: none!important;
  }
  .right-wrapper{
    text-align: right;
  }

  .right-wrapper p{
    display: inline-block;
  }

  .right-wrapper p label span{
    display: inline-block;
    width: 135px;
    margin-left: 10px;
  }
</style>
<body>
<br>
<div class="transac-invoice" style="margin: 0 auto; padding: 0 15px;width: 60%;position: relative;">
  
    <div class="col-md-12 invoice-wrapper no-padding" id="pdf-print" style="width: 100%;margin: 0 auto;display: block;float: none;margin-bottom: 50px;">
    	<div class="header">
	    	<div class="col-md-12 text-right " style="width: 95%;">
	    		<h1 style="font-size: 35px !important;color: #000 !important;font-family: 'Open Sans', sans-serif !important;">INVOICE</h1>
	    	</div>
        
        @if($paid)
          <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/paid-text.png" style="position: absolute;left: 25%;top: 10%;">
        @endif
        
        @if($complimentary)
        <p style="position: absolute;font-size: 18px;top: 52%;left: 28%;color: #44a1ce;font-family: 'HelveticaNeueMed', sans-serif !important;">COMPLIMENTARY BY MEDNEFITS FOR 1 YEAR</p>
        @endif
	    	<div class="col-md-12 text-right" style="width: 95%;">
	    		<div id="clinic-logo-container">
             <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" class="pull-left" style="width: 250px;max-height: 135px;">
          </div>
	    		<br>
	    		<b>
	    			<span>Medicloud Pte Ltd</span>
	    			<br />
		    		<span style="font-weight: normal;">7 Temasek Boulevard</span>
		    		<br />
		    		<span style="font-weight: normal;">#18-02 Suntec Tower One</span>
		    		<br />
		    		<span style="font-weight: normal;">038987</span>
		    		<br />
		    		<span style="font-weight: normal;">Singapore</span>
		    		<p></p>
		    		<span style="font-weight: normal;">+65 3163 5403</span>
		    		<br />
		    		<span style="font-weight: normal;">mednefits.com</span>
	    		</b>
	    	</div>
    	</div>

    	<div class="bill-to">
	    	<div class="col-md-6 no-padding">
          @if($billing_contact_status == true && $billing_address_status == false)
	    		<div class="left-wrapper">
		    		<h5 style="color: #aaa;margin-bottom: 0;"><b>BILL TO</b></h5>
		    		<h5 style="margin-top: 0;margin-bottom: 0;"><b>{{$company}}</b></h5>
		    		<p>{{$first_name}} {{$last_name}}</p>
		    		<p>{{$address}}, {{$postal}}</p>
            <br />
		    		<p>{{$phone}}</p>
            <p>{{$email}}</p>
	    		</div>
          @endif	
          @if($billing_contact_status == false && $billing_address_status == true)
            <div class="left-wrapper">
            <h5 style="color: #aaa;margin-bottom: 0;"><b>BILL TO</b></h5>
            <h5 style="margin-top: 0;margin-bottom: 0;"><b>{{$company}}</b></h5>
            <p>{{$address}}, {{$postal}}</p>
            <br />
          </div>
          @endif
	    	</div>
	    	<div class="col-md-6 no-padding">
	    		<div class="right-wrapper">
            <input type="hidden" name="invoice_number" id="invoice_number" value="{{$invoice_number}}">
	    			<p><label>Invoice Number:  <span style="font-weight: normal;">{{$invoice_number}}</span></label> </p>
	    			<p><label>Invoice Date:  <span style="font-weight: normal;">{{date('F d, Y', strtotime($invoice_date))}}</span></label> </p>
	    			<p><label>Payment Due: <span style="font-weight: normal;">{{date('F d, Y', strtotime($invoice_due))}}</span></label> </p>
	    			<p style="background: #f0f0f0;padding: 5px;"><label>Amount Due (SGD): <span><b>${{$amount_due}}</b></span></label> </p>
	    		</div>
	    	</div>
    	</div>

    	<table class="table table-responsive text-center" style="border-bottom: 2px solid #DCDFE0;margin-bottom: 0">
    		<thead>
    			<tr>
    				<th style="width: 40%;text-align: left !important;padding-left: 30px;">Items</th>
    				<th>Quantity</th>
    				<th>Price</th>
    				<th>Amount</th>
    			</tr>
    		</thead>

    		<tbody id="invoice-items-table">
    			<tr>
    				<td style="text-align: left!important; ">
              @if($billing_contact_status == true && $billing_address_status == false)
    					 <p style="margin-left: 10px;margin-bottom: 0;"><b>{{$plan_type}}</b></p>
              @endif
              @if($billing_contact_status == false && $billing_address_status == true)
              <p style="margin-left: 10px;"><b>{{$company}}</b></p>
              @endif
    					<p style="margin-left: 10px;margin-bottom: 0;">No. of employees: {{$number_employess}}</p>
    					<p style="margin-left: 10px;margin-bottom: 0;">Billing Frequency: Annual</p>
              <p style="margin-left: 10px;margin-bottom: 0;">Start Date: {{date('F d, Y', strtotime($plan_start))}}</p>
    					<p style="margin-left: 10px;margin-bottom: 0;">End Date: {{date('F d, Y', strtotime($plan_end))}}</p>
    				</td>
    				<td><b>{{$number_employess}}</b></td>
    				<td><b>${{$price}}</b></td>
    				<td><b>${{$amount}}</b></td>
    			</tr>

    		</tbody>
    	</table>

    	<div class="total">
    		<div class="col-md-12 text-right" style="width: 95%;">
    			<div class="pull-right" style="width: 40%">
    				<p><label class="pull-left">Total:</label> ${{$total}}</p>
    				<div style="border-bottom: 2px solid #aaa"></div>
    				<p><label class="pull-left">Amount Due (SGD):</label> <b>${{$amount_due}}</b></p>
    			</div>
    		</div>
    	</div>

    	<div class="notes">
    		<div class="col-md-12 text-left" style="width: 95%;">
    			<p style="margin-bottom: 10px"><b>Notes</b></p>
    			<!-- <p style="margin-bottom: 0;font-size: 16px;color: #000;">Please make cheques payable to:</p>
    			<p style="margin-bottom: 0;">Medicloud Pte Ltd</p>
    			<p style="margin-bottom: 0;">7 Temasek Boulevard</p>
    			<p style="margin-bottom: 20px;">#18-02 Suntec Tower One, S038987</p> -->
          <p style="margin-bottom: 0;font-size: 16px;color: #000;">Corporate PayNow</p>
          <p style="margin-bottom: 0;">UEN: 201415681W</p>


          <p style="margin-bottom: 0;font-size: 16px;color: #000;">Or Bank Transfer to:</p>
          <p style="margin-bottom: 0;">Bank: UOB Anson Road</p>
          <p style="margin-bottom: 0;">Bank No: 7375</p>
          <p style="margin-bottom: 0;">Branch No: 057</p>
          <p style="margin-bottom: 0;">Account Name: Medicloud Pte Ltd</p>
          <p style="margin-bottom: 0;">Account No.: 3743069399</p>
          <p style="margin-bottom: 20px;">ACRA 201415681W</p>
          
          <p style="margin-bottom: 0;font-size: 12px">Please contact us for any questions related to your invoice/contract at support@mednefits.com</p>
          <p>Please send all payment advice to finance@mednefits.com</p>
    		</div>
    	</div>

    	<div class="copyright text-center">
    		<h5 style="color: #999;"><b>&copy; 2020 Mednefits. All rights reserved</b></h5>
    	</div>
    	
    </div>
</div>

<script type="text/javascript">
  var form = $('#pdf-print'),
  cache_width = form.width(),
  pdf_name = $('#invoice_number').val(),
  a4  = [ 655.28,  841.89];
  console.log(cache_width);
  getCanvas().then(function(canvas){
    var 
    img = canvas.toDataURL("image/png"),
    doc = new jsPDF({
          unit:'px', 
          format:'a4'
        });     
        doc.addImage(img, 'PNG', 0, 0);
        doc.save(pdf_name + '.pdf');
        form.width(cache_width);
  });

  function getCanvas(){
    form.width((a4[0]*1.33333) -80).css('max-width','none');
    return html2canvas(form,{
        imageTimeout:2000,
        removeContainer:true
      }); 
  }

</script>
</body>
</html>