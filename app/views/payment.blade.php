<!DOCTYPE html>
<html ng-app="app">
<head>
	<title>Payment Sample</title>
	{{ HTML::script('assets/js/jquery.min.js') }}
	<script src="https://js.stripe.com/v3/"></script>
</head>
<!-- <script type="text/javascript">

	$.getScript( "https://js.braintreegateway.com/v2/braintree.js", function() {
		jQuery.ajax({
			url: window.location.origin + '/payment/token',
			type: "get",
			success: function(data) {
				braintree.setup(data, 'dropin', { container: 'dropin-container' })
			}
		});
    });

</script> -->
<!-- <style type="text/css">
	label.heading {
		font-weight: 600;
	}
	.payment-form {
		width: 300px;
		margin-left: auto;
		margin-right: auto;
		padding: 10px;
		border: 1px solid #333 solid;
	}
</style> -->
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

  <script type="text/javascript">
  	var stripe = Stripe('pk_test_whm2wdt8IMnLKOuuZEtCe8u9');
   var elements = stripe.elements();
    Stripe.setPublishableKey('pk_test_whm2wdt8IMnLKOuuZEtCe8u9');
    
    function onSubmitDo () {
      
      Stripe.card.createToken( document.getElementById('payment-form'), myStripeResponseHandler );
          
      return false;
      
    };
    function myStripeResponseHandler ( status, response ) {
      
      console.log( status );
      console.log( response );
    
      if ( response.error ) {
        document.getElementById('payment-error').innerHTML = response.error.message;
      } else {
        var tokenInput = document.createElement("input");
        tokenInput.type = "hidden";
        tokenInput.name = "stripeToken";
        tokenInput.value = response.id;
        var paymentForm = document.getElementById('payment-form');
        paymentForm.appendChild(tokenInput);
        paymentForm.submit();
      }
      
   };
      
</script>
<body style="text-align: center; margin-top: 100px" payment>
<!-- action="/payment/pay" -->
	<!-- <form action="insert/corporate_credit_payment" method="post" class="payment-form" id="form-payment">
		<label for="corporate_buy_start_id" class="heading">ID</label>
		<input type="text" name="corporate_buy_start_id" id="corporate_buy_start_id">
		<br />
		<div id="dropin-container"></div>
		<button id="submit">Pay</button>
	</form> -->
	<!-- <form action="/payment/pay" method="post">
	  <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
	          data-key="pk_test_whm2wdt8IMnLKOuuZEtCe8u9"
	          data-locale="auto">     
	   	</script>
	</form> -->
	<form action="/payment/pay" method="POST" id="payment-form" onsubmit="return onSubmitDo()">
 
        Cardholder Name
        <input type="text" size="20" data-stripe="name" name="cardName" />

        Card Number
        <input type="text" size="20" data-stripe="number" name="number" />
 				<input type="hidden" name="corporate_buy_start_id" value="2">
        CVC
        <input type="text" size="4" data-stripe="cvc"/>

        Expiration (MM/YYYY)
        <input type="text" size="2" data-stripe="exp-month" name="exp-month" />
        <input type="text" size="4" data-stripe="exp-year" name="exp-year" />
       
        Email Address
        <input type="text" size = "25" name="emailAddress" />
    
        <button type="submit">Pay $15 with Stripe</button>

  </form>

  <span style='color: red' id='payment-error'></span>
</body>
</html>