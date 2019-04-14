<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Payment View</title>
	<link rel="shortcut icon" href="{{ URL::asset('assets/new_landing/images/favicon.ico') }}" type="image/ico">
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
	{{ HTML::script('assets/js/jquery.min.js') }}
	{{ HTML::script('assets/css/bootstrap/js/bootstrap.min.js') }}
	{{ HTML::script('assets/js/sweetalert2.min.js') }}
	{{ HTML::style('assets/css/sweetalert2.css') }}
</head>
<style type="text/css">
	body {
		font-family: 'Oxygen';
		background: #297ea2;
	}
	.adjust-margin
	{
		margin-top: 0px!important;
		-webkit-transition: margin-top 0.5s;
		transition: margin-top 0.5s;
	}
</style>
<body>
	<div class="container">
		<div class="col-md-6 col-md-offset-3 text-center inner-container" style="margin-top: 100px;-webkit-transition: margin-top 0.5s;transition: margin-top 0.5s;">
			<img src="{{ URL::asset('assets/new_landing/images/landing/logo.png') }}" class="img-responsive">
			<div class="list-group" id="payment_container">
			  <a href="javascript:void(0)" target="_blank" class="list-group-item active">
			  Welcome <b>{{ ucwords($user->Name) }}</b>
			  To Clinic <b>{{ ucwords($clinic->Name) }}</b>
			  </a>
			  <input type="hidden" name="qr_code" id="clinic_id" value="{{ $clinic->ClinicID }}">
				<input type="hidden" name="qr_code" id="user_id" value="{{ $user->UserID }}">
				<div class="form-group list-group-item active">
					<h2>Procedure</h2>
					<select class="form-control" id="procedure_id">
						@foreach($procedures as $key => $procedure)
							<option value="{{$procedure->ProcedureID}}">{{ ucwords($procedure->Name) }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group list-group-item active">
					<h2>Please key in Medication Amount:</h2>
					<input type="number" name="" class="form-control" id="amount"/>
				</div>
				<div class="form-group list-group-item active">
					<button class="btn btn-lg btn-block btn-info" id="pay">
			  		Pay
			  		<span id="pay_loader" style="display: none;"><img src="{{ URL::asset('images/loading_apple.gif') }}" style="width: 20px;"></span>
			  	</button>
			  </div>
			</div>
		</div>
	</div>

	<!-- modal -->
	<div class="modal fade" id="qr_pin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog modal-sm" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Please input Pin:</h4>
	      </div>
	      <div class="modal-body">
	        <div class="form-group">
	        	<input type="password" name="amount" id="qr_pin_code" class="form-control" autofocus>	
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" id="btn-pin" class="btn btn-primary" onclick="finishPin()">
	        	Done
	        	<span></span>
	        </button>
	      </div>
	    </div>
	  </div>
	</div>

	<script type="text/javascript">
		$('#check_in').click(function( ){
			$('#check_in_loader').show();
			$(this).attr('disabled', true);
			var clinic_id = $('#clinic_id').val();
			var user_id = $('#user_id').val();

			$.ajax({
				url: window.location.origin + '/app/save/check_in',
				type: 'POST',
				data: { user_id: user_id, clinic_id: clinic_id}
			})
			.done(function(data) {
				$('#check_in_loader').hide();
				$(this).attr('disabled', false);
				$('#check_in_container').slideUp();
				$('#payment_container').fadeIn();
				$('.inner-container').addClass('adjust-margin');
			});
		});

		$('#pay').click(function( ){
			var amount = $('#amount').val();

			console.log(isNaN(amount));
			if(isNaN(amount) == true) {
				swal(
				  'Oooops!',
				  'Amount should be a number',
				  'error'
				);
				return false;
			}

			if(!amount) {
				swal(
				  'Oooops!',
				  'Please enter an amount.',
				  'error'
				);
				return false;
			}

			$('#pay_loader').show();
			$(this).attr('disabled', true);
			$('#qr_pin').modal('show');
		});

		function finishPin() {
			var user_id = $('#user_id').val();
			var pin = $('#qr_pin_code').val();
			var clinic_id = $('#clinic_id').val();
			console.log(user_id, pin);
			$('#btn-pin').attr('disabled', true);
			$.ajax({
	        url: window.location.origin + "/app/check/user_pin",
	        type: "POST",
	        dataType: 'json',
	        data: { user_id: user_id, pin: pin },
	    })
	    .done(function(data) {
	      if (data == 1) {
	      	$.ajax({
						url: window.location.origin + '/app/save/payment',
						type: 'POST',
						data: { user_id: user_id, clinic_id: clinic_id, amount: $('#amount').val(), procedure_id: $('#procedure_id').val()}
					})
					.done(function(data) {
						swal(
						  'Success!',
						  '',
						  'success'
						);
						$('#pay_loader').hide();
						$(this).attr('disabled', false);
						$('#payment_container').slideUp();
						$('#qr_pin').modal('hide');
					});
	      } else {
	        swal(
					  'Incorrect Pin!',
					  '',
					  'error'
					)
	      }
	      $('#pay_loader').hide();
				$(this).attr('disabled', false);
	      $('#btn-pin').attr('disabled', false);
	    });
		}

	</script>
</body>
</html>