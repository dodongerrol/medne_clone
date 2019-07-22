jQuery(document).ready(function($) {
	
	$('#send_message').click(function(event) {
		
		var name = $('#name').val();
		var code = $('#phone_code').val();
		var phone = $('#phone').val();
		var message = $('#message').val();
		var base_url = $('#h_base_url').val();

		if (name=='') { $('.error').css({'display': 'block','color': 'red'}); $('.error').html('</br>Please enter sender\'s name'); return false;}
		if (phone=='') { $('.error').css({'display': 'block','color': 'red'}); $('.error').html('</br>Please enter sender\'s phone'); return false;}
		if (message=='') { $('.error').css({'display': 'block','color': 'red'}); $('.error').html('</br>Please enter message'); return false;}

		$('.error').css('display', 'none');
		$('#send_message').text('Sending...');
		$.ajax({
			url: base_url+'/app/send_custom_sms',
			type: 'POST',
			data: {name:name, code:code, phone: phone, message:message}
		})
		.done(function(data) {
			if (data == 1) {
				$('#send_message').text('Send Message');
				$('.error').css({'display': 'block','color': 'green'});
				$('.error').html('</br>Your message sent successfully');

				var name = $('#name').val('');
				var code = $('#phone_code').val('+65');
				var phone = $('#phone').val('');
				var message = $('#message').val('');
			} else {
				$('#send_message').text('Resend');
				$('.error').css({'display': 'block','color': 'red'});
				$('.error').html('</br>There is a error sending your message');
			}
		})
		.error(function(error){
			console.log('error', error);
			$('#send_message').text('Resend');
			$('.error').css({'display': 'block','color': 'red'});
			var message = error.responseJSON.error.message.split(": ");
			$('.error').html('</br>' + message[1]);
		})
		.fail(function(fail) {
			console.log('fail', fail);
			$('#send_message').text('Resend');
			$('.error').css({'display': 'block','color': 'red'});
			var message = fail.responseJSON.error.message.split(": ");
			$('.error').html('</br>' + message[1]);
		});
		

	});



	$('#sms_link').click(function(event) {
		var name = $('#name').val('');
		var code = $('#phone_code').val('+65');
		var phone = $('#phone').val('');
		var message = $('#message').val('');
		$('.error').css('display', 'none');
		$('#send_message').text('Send Message');
	});


$('body').on('keydown', '#phone', function(c) {

        // if (String.fromCharCode(c.keyCode).replace(/[^0-9]/g, '') == '') {
        //     return false;
        // }
        if (!(c.keyCode>=96 && c.keyCode<=105) && !(c.keyCode>=48 && c.keyCode<=57) && c.keyCode!=8 && c.keyCode!=9) {
            return false;
        }

    });

});