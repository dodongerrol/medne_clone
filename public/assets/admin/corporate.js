// var protocol = jQuery(location).attr('protocol');
// var hostname = jQuery(location).attr('hostname');
// var folderlocation = $(location).attr('pathname').split('/')[1];
// window.base_url = protocol+'//'+hostname+'/'+folderlocation+'/public/admin/';
window.base_loading_image = '<img src="http://medicloud.sg/medicloud_v2/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';
window.base_url = window.location.origin + '/admin/';

jQuery("document").ready(function(){

	corporateSignUpValidation();

	$('#customer-list-trigger').click(function( ){
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		$.ajax({
	        url: base_url + 'corporate/list',
	        type: 'GET',
	      })
	      .done(function(data) {
	        // console.log(data);s
	        jQuery('#customer-list').html(data);
	       	jQuery.unblockUI();
	      });
	});


	$('#check-pass').click(function(event){
		event.preventDefault();
		var pass = $('#password').val();
		var btn = $('#check-pass').text();
		$('#check-pass').text('Checking');
		$('#check-pass').attr('disabled', true);

		dataValues = '&password='+pass;

		$.ajax({
	        url: base_url + 'checkPass',
	        type: 'POST',
	        data : dataValues,

	        success: function (data){
	        	console.log(data);
	        	$('#check-pass').text(btn);
				$('#check-pass').attr('disabled', false);
	        	if(data == 1) {
					SaveCorporate( );
	        	} else {
	        		$.toast({
		        		heading: 'Oooops!',
		            	text: 'Password did not match.',
		            	showHideTransition: 'slide',
		            	icon: 'error',
		            	hideAfter : 6000,
		            	bgColor : '#D34332'
		          });
	        	}
	        }
		 });
	});

	function SaveCorporate( ) {
		var fname = $('#first_name').val();
		var lname = $('#last_name').val();
		var email = $('#email').val();
		var company_name = $('#company_name').val();
		var credit = $('#credit').val();

		dataValues = '&fname='+fname+'&lname='+lname+'&email='+email+'&company_name='+company_name+'&credit='+credit;
		
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		$.ajax({
	        url: base_url + 'corporate/create',
	        type: 'POST',
	        data : dataValues,

	        success: function (data){
	        	// console.log(data);
	        	if(data != "false") {
	        		jQuery.unblockUI();
	        		fname = $('#first_name').val('');
					lname = $('#last_name').val('');
					email = $('#email').val('');
					company_name = $('#company_name').val('');
					credit = $('#credit').val('');
					$.toast({
			        	heading: 'Success!',
			            text: 'New Company Created. Company sended an email notification.',
			            showHideTransition: 'slide',
			            icon: 'info',
			            hideAfter : 6000,
			            bgColor : '#1667AC'
			          });
	        	} else {
	        		jQuery.unblockUI();
	        		$.toast({
			        	heading: 'Oooops!',
			            text: 'Email Address must be unique to all.',
			            showHideTransition: 'slide',
			            icon: 'error',
			            hideAfter : 6000,
			            bgColor : '#D34332'
			          });
	        	}

	        	$('#password-check-box').modal('hide');
	        }
	      });
	}

	$('#create-company').click(function(event){
		event.preventDefault();

		if(jQuery("form").valid() == true){
			// check password before save the data
			$('#password-check-box').modal('show');
		};
	});
	var id
	$('.edit-corporate').click(function( e ){
		var first_name = $(this).closest("tr").find("td:nth-child(1)").text();
		var last_name = $(this).closest("tr").find("td:nth-child(2)").text();
		var email = $(this).closest("tr").find("td:nth-child(3)").text();
		var company_name = $(this).closest("tr").find("td:nth-child(4)").text();
		var credit = ($(this).closest("tr").find("td:nth-child(5)").text()).replace(/[_\W]+/g, "");
		id = $(this).closest("tr").find("td:nth-child(9)").text();
		console.log(id);
		$(this).closest("tr").find(".td-edit").hide();
		$(this).closest("tr").find(".td-check").fadeIn();
		$(this).closest("tr").find("td:nth-child(8)").fadeIn();
		$(this).closest("tr").find("td:nth-child(1)").html( "<input type='text' value='" + first_name + "' class='form-control'>" );
		$(this).closest("tr").find("td:nth-child(2)").html( "<input type='text' value='" + last_name + "' class='form-control'>" );
		$(this).closest("tr").find("td:nth-child(3)").html( "<input type='email' value='" + email + "' class='form-control'>" );
		$(this).closest("tr").find("td:nth-child(4)").html( "<input type='text' value='" + company_name + "' class='form-control'>" );
		$(this).closest("tr").find("td:nth-child(5)").html( "$ <input type='number' value='" + credit + "' class='form-control' style='width: 70%;display: inline-block;'>" );
	});

	$('.done-edit-corporate').click(function( ){
		var done = $(this);
		var first_name = $(this).closest("tr").find("td:nth-child(1) input").val();
		var last_name = $(this).closest("tr").find("td:nth-child(2) input").val();
		var email = $(this).closest("tr").find("td:nth-child(3) input").val();
		var company_name = $(this).closest("tr").find("td:nth-child(4) input").val();
		var credit = $(this).closest("tr").find("td:nth-child(5) input").val();

		console.log(first_name, last_name, email, company_name, credit, id);
		dataValues = '&fname='+first_name+'&lname='+last_name+'&email='+email+'&company_name='+company_name+'&credit='+credit+'&id='+id;
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
			$.ajax({
		        url: base_url + 'corporate/update',
		        type: 'POST',
		        data : dataValues,

		        success: function (data){
		        	console.log(data);
		        	if(data == 1 ) {
						$.toast({
				        	heading: 'Success!',
				            text: 'Company Updated.',
				            showHideTransition: 'slide',
				            icon: 'info',
				            hideAfter : 6000,
				            bgColor : '#1667AC'
				          });
						id = "";
		        	} else {
		        		$.toast({
				        	heading: 'Oooops!',
				            text: 'Something went wrong. Try again laer.',
				            showHideTransition: 'slide',
				            icon: 'error',
				            hideAfter : 6000,
				            bgColor : '#D34332'
				          });
		        	}

		        	jQuery.unblockUI();
					done.closest("tr").find(".td-edit").fadeIn();
					done.closest("tr").find(".td-check").hide();
					done.closest("tr").find("td:nth-child(8)").hide();

					done.closest("tr").find("td:nth-child(1)").html( first_name );
					done.closest("tr").find("td:nth-child(2)").html( last_name );
					done.closest("tr").find("td:nth-child(3)").html( email );
					done.closest("tr").find("td:nth-child(4)").html( company_name );
					done.closest("tr").find("td:nth-child(5)").html( "$" + credit );
		        	
		        }
		      });

		
	});

	$('.cancel-edit-corporate').click(function( ){
		var first_name = $(this).closest("tr").find("td:nth-child(1) input").val();
		var last_name = $(this).closest("tr").find("td:nth-child(2) input").val();
		var email = $(this).closest("tr").find("td:nth-child(3) input").val();
		var company_name = $(this).closest("tr").find("td:nth-child(4) input").val();
		var credit = $(this).closest("tr").find("td:nth-child(5) input").val();

		$(this).closest("tr").find(".td-edit").fadeIn();
		$(this).closest("tr").find(".td-check").hide();
		$(this).closest("tr").find("td:nth-child(8)").hide();

		$(this).closest("tr").find("td:nth-child(1)").html( first_name );
		$(this).closest("tr").find("td:nth-child(2)").html( last_name );
		$(this).closest("tr").find("td:nth-child(3)").html( email );
		$(this).closest("tr").find("td:nth-child(4)").html( company_name );
		$(this).closest("tr").find("td:nth-child(5)").html( "$" + credit );

	});
});