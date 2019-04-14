<!DOCTYPE html>
<html>
<head>
	<title>Receipt</title>
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	{{ HTML::script('assets/js/jquery.min.js') }}
  {{ HTML::script('assets/js/jspdf.debug.js') }}
  {{ HTML::script('assets/js/html2canvas.min.js') }}
  {{ HTML::script('assets/js/jquery.printElement.min.js') }}
  {{ HTML::script('assets/js/printThis.js') }}
</head>
<style type="text/css">
	body
	{
		font-family: 'Helvetica';
	}
</style>
<body>
	<div class="container">
		<div id="pdf-print" class="col-md-5 col-md-offset-4" style="border: 1px solid #e0e6ea;box-shadow: 0px 1px 1px #e0e6ea;margin-top: 10px;margin-bottom: 10px;">
			<div class="row text-center" style="padding: 50px;padding-bottom: 10px;">
				<img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits_Logo_(BLUE).png" style="max-width: 200px;max-height: 130px;">
			</div>
			<div class="row text-center">
				<h1><b>Payment Receipt</b></h1>
				<h4 style="margin: 0;"><b>Invoice #{{$invoice_number}}</b></h4>
				<input type="hidden" name="invoice_number" id="invoice_number" value="{{$invoice_number}}">
				<h4 style="margin: 0;color: #cbcfd1;">for <span>{{$company}}</span></h4>
				<h4 style="margin: 0;color: #cbcfd1;"><span>paid on {{date('F d, Y', strtotime($paid_date))}}</span></h4>
			</div>
			<div class="row text-center" style="border-bottom: 1px solid #e0e6ea;">
				<p></p>
				<span style="width: 30%;display: inline-block;color: #464d52;line-height: 16px;"><b>Medicloud Pte Ltd 7 Temasek Boulevard #18-02 Suntec Tower One</b></span>
				<br />
				<span style="color: #464d52;line-height: 16px;"><b> 038987 <br /> Singapore</b></span>
				<br />
				<span style="color: #464d52;line-height: 16px;"><b>Tel: +6562547889</b></span>
				<br />
				<span style="color: #464d52;line-height: 16px;"><b>mednefits.com</b></span>
				<br />
				<span style="color: #464d52;line-height: 16px;"><b>filbert@mednefits.com</b></span>
				<p style="margin-bottom: 10px;"></p>
			</div>
			<div class="row">
				<div style="padding: 30px;">
					<span style="color: #464d52;font-size: 17px;">Hi,</span>
					<p></p>
					<span style="color: #464d52;font-size: 17px;">Here's your payment receipt for Invoice #{{$invoice_number}}, for ${{$amount_paid}} SGD.</span>
					<p></p>
					<span style="color: #464d52;font-size: 17px;">You can always view your receipt online, at:</span>
					<p></p>
					<p></p>
					<span style="color: #464d52;font-size: 17px;">If you have any questions, please let us know.</span>
					<p></p>
					<span style="color: #464d52;font-size: 17px;">Thanks,</span>
					<br />
					<span style="color: black;font-size: 18px;">Your Mednefits Team</span>
				</div>
				<div style="border-top: 2px solid #e8eaeb;border-bottom: 2px solid #e8eaeb;padding: 10px;width: 90%;margin: 0 auto;" class="text-center">
					<h4 style="color: #444b50;">Payment Amount: <b>${{$amount_paid}} SGD</b></h4>
				</div>
				<div class="row text-center">
					<span style="font-size: 17px;display: inline-block;padding: 10px;font-weight: bold;color: #464d52;">PAYMENT METHOD: {{$payment_method}}</span>
					<p></p>
					<img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/paid.png" class="img-responsive" style="width: 25%;display: inline-block;-ms-transform: rotate(-23deg);-webkit-transform: rotate(-23deg);transform: rotate(-23deg);margin-bottom: 70px;margin-top: 46px;">
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">
  var form = $('#pdf-print'),
  cache_width = form.width(),
  pdf_name = $('#invoice_number').val(),
  a4  = [ 595.28,  841.89];  // for a4 size paper width and height
  console.log(cache_width);
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
        imageTimeout:2000,
        removeContainer:true
      }); 
  }

</script>
</body>
</html>