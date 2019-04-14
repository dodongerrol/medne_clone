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
<div class="transac-invoice" style="margin: 0 auto; padding: 0 15px;width: 60%;">
    <div class="col-md-12 invoice-wrapper no-padding" id="pdf-print" style="width: 100%;margin: 0 auto;display: block;float: none;margin-bottom: 50px;">
    	<div class="header">
	    	<div class="col-md-12 text-right " style="width: 95%;">
	    		<h1 style="font-size: 35px !important;color: #000 !important;font-family: 'Open Sans', sans-serif !important;">CERTIFICATE</h1>
	    	</div>
        @if($complimentary)
        <p style="position: absolute;font-size: 18px;top: 68%;left: 30%;color: #95d5f5;font-family: 'HelveticaNeueMed', sans-serif !important;-ms-transform: rotate(-30deg);-webkit-transform: rotate(-30deg);transform: rotate(-30deg);">COMPLIMENTARY BY MEDNEFITS FOR 1 YEAR</p>
        @endif
	    	<div class="col-md-12 text-right" style="width: 95%;">
	    		<div id="clinic-logo-container">
             <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/mednefits-care-cert.png" class="pull-left" style="max-width: 50%;">
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
		    		<span style="font-weight: normal;">+65 6254 7889</span>
		    		<br />
		    		<span style="font-weight: normal;">mednefits.com</span>
	    		</b>
	    	</div>
    	</div>

    	<div class="bill-to">
	    	<div class="col-md-6 no-padding">
          @if($billing_contact_status == true && $billing_address_status == false)
	    		<div class="left-wrapper">
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
	    		<div style="margin-left: 30%;">
            <input type="hidden" name="invoice_number" id="invoice_number" value="{{$invoice_number}}">
	    			<p><label>CERTIFCATE NUMBER:  <span style="font-weight: normal;">{{$invoice_number}}</span></label> </p>
	    		</div>
	    	</div>
    	</div>

    	<table class="table table-responsive text-center" style="border-bottom: 2px solid #DCDFE0">
    		<thead>
    			<tr>
    				<th style="width: 40%;text-align: left !important;padding-left: 30px;">Items</th>
    				<th><!-- Quantity --></th>
    				<th><!-- Price --></th>
    				<th><!-- Amount --></th>
    			</tr>
    		</thead>

    		<tbody id="invoice-items-table">
    			<tr>
    				<td style="text-align: left!important;">
              @if($billing_contact_status == true && $billing_address_status == false)
    					 <p style="margin-left: 10px;margin-bottom: 0;"><b style="font-family: 'HelveticaNeueMed', sans-serif !important;">{{$plan_type}}</b></p>
              @endif
              @if($billing_contact_status == false && $billing_address_status == true)
              <p style="margin-left: 10px;"><b style="font-family: 'HelveticaNeueMed', sans-serif !important;">{{$company}}</b></p>
              @endif
    					<p style="margin-left: 10px;margin-bottom: 0;">No. of employees: {{$number_employess}} Full Time</p>
    					<p style="margin-left: 10px;margin-bottom: 0;">Billing Frequency: Annual</p>
              <p style="margin-left: 10px;margin-bottom: 0;">Next Billing Date: {{date('d F Y', strtotime($next_billing))}}</p>
              <p style="margin-left: 10px;margin-bottom: 0;">Start Date: {{date('d F Y', strtotime($plan_start))}}</p>
    					<p style="margin-left: 10px;margin-bottom: 0;">End Date: {{date('d F Y', strtotime($plan_end))}}</p>
    				</td>
    				<td><!-- <b>{{$number_employess}}</b> --></td>
    				<td><!-- <b>${{$price}}</b> --></td>
    				<td><!-- <b>${{$amount}}</b> --></td>
    			</tr>
          <tr>
            <td style="text-align: left!important;">
              <p style="margin-left: 10px;margin-bottom: 0;"><b style="font-family: 'HelveticaNeueMed', sans-serif !important;">Benefits Coverage</b></p>
            </td>
          </tr>
          <tr>
              <td style="text-align: left!important;">
                <p style="margin-left: 10px;margin-bottom: 0;">Health Screening: 1 Complementary basic health screening for each employee.
                </p>
                <p></p>
                <p style="margin-left: 10px;margin-bottom: 0;">Outpatient GP: 100% consultation covered, employees only need to pay medicine.
                </p>
                <p></p>
                <p style="margin-left: 10px;margin-bottom: 0;">Dental Care: Up to 30% off selected dental services.
                </p>
                <p style="margin-left: 10px;margin-bottom: 0;">Health Specialist: Up to 60% off specialist consultation.</p>
                <p></p>
                <p style="margin-left: 10px;margin-bottom: 0;">TCM: 100% consultation covered, employees only need to pay medicine and treatment.</p>
              </td>
              <td></td>
              <td></td>
              <td><img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/mednefits-e-chop.png" style="width: 130px;"></td>
          </tr>

    		</tbody>
    	</table>
      
    	<div class="copyright text-center">
    		<h5 style="color: #999;"><b>&copy; 2019 Mednefits. All rights reserved</b></h5>
    	</div>
    	
    </div>
</div>

<script type="text/javascript">
  var form = $('#pdf-print'),
  cache_width = form.width(),
  cache_height = form.height(),
  pdf_name = $('#invoice_number').val(),
  a4  = [ 595.28, cache_height];  // for a4 size paper width and height
  console.log(cache_height);
  getCanvas().then(function(canvas){
    var 
    img = canvas.toDataURL("image/png"),
    doc = new jsPDF({
      unit:'px', 
      format:'a4'
    });     
    doc.addImage(img, 'PNG', 20, 20);
    doc.save(pdf_name + '.pdf');
    form.width(cache_width);
  });

  function getCanvas(){
    form.width((a4[0]*1.33333) -80).css('max-width','none');
    return html2canvas(form,{
        imageTimeout: 1000,
        removeContainer:true
      }); 
  }

</script>
</body>
</html>