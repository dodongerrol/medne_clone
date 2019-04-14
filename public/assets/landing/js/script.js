jQuery(document).ready(function($) {

	window.base_url = window.location.origin + '/app/';
	
	$('#contact').click(function(event) {
		
		var fname = $('#First_Name').val()
		var lname = $('#Last_Name').val()
		var company = $('#Company_Name').val()
		var email = $('#Email').val()
		var phone = $('#Phone_Number').val()
		var message = $('#Messages').val()

		var cnf = confirm("Are you sure to send an inquery?");
		if(cnf){
			$.ajax({
				url: window.base_url + 'contact',
				type: 'POST',
				data: {fname:fname, lname:lname, company:company, email:email, phone:phone, message:message },
			})
			.done(function(data) {
				$('#First_Name').val('');
				$('#Last_Name').val('');
				$('#Company_Name').val('');
				$('#Email').val('');
				$('#Phone_Number').val('');
				$('#Messages').val('');
			});
			
		}
		
	});

	$('#subscribe').click(function(event) {

		var email = $('#sub-email').val();
		var re =/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;

		console.log(email);

		if (email == '' || !re.test(email)) {
			$('#sub-email').addClass('input-error');
			return false;
		}
		 else {
		 	$('#sub-email').removeClass('input-error');
		 }

			$.ajax({
				url: window.base_url + 'subscribe',
				type: 'POST',
				data: {email:email},
			})
			.done(function(data) {

				$('#sub-email').val('');

				if(data == 1){
					// alert('Thank you! We will keep in touch.!');
		 			$('#sub-msg').css("display" ,"block");
		 			$('#sub-msg').text('Thank you! We will keep in touch.');

				}else{
					// alert(data); // display error
					$('#sub-msg').css("display" ,"block");
					$('#sub-msg').css("color" ,"#BF0A06");
		 			$('#sub-msg').text('Opps! Sorry Something Went Wrong.');
				}
				


			});

	});


	$('.scroll-now').click(function(event) {

		$('body').animate({scrollTop:$('#contact-form').position().top}, 1000);

	});


	$('#index-sign').click(function(event) {

		window.location.replace("health-professionals.html");


	});

});
