// var protocol = jQuery(location).attr('protocol');
// var hostname = jQuery(location).attr('hostname');
// var folderlocation = $(location).attr('pathname').split('/')[1];
// window.base_url = protocol+'//'+hostname+'/'+folderlocation+'/public/admin/';
window.base_loading_image = '<img src="http://medicloud.sg/medicloud_v2/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';
window.base_url = window.location.origin + '/admin/';

jQuery("document").ready(function(){

	creditSignUpValidation();
	var prev = null;
	var next;
	var current_page;
	var from;
	var last_page;
	var per_page;
	var to;
	var total;

	var loop_page = 10;
	var loop_start_page = 1;

	$(document).on('click', 'li.paginate-trigger', function( e ) {
	   goToPage($(this).attr("rel"));
	});
	$(document).on('click', '#next a', function( e ) {
	   goToPage(current_page+1);
	});
	$(document).on('click', '#previous a', function( e ) {
	   goToPage(current_page-1);
	});
	$(document).on('click', '#show-all', function( e ) {
		$('#search').val('');
		$('#paginate-list').empty();
	   	loadUsers();
	});
	// $(document).on('keypress', 'input#search', function (e) {
	//    var search = $('input#search').val();
	//    console.log(search);
	// });

	// function getSearch( e ) {
	// 	console.log( e);
	// }

	function goToPage( page ) {
		// prev = page;
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		$.ajax({
	        url: base_url + 'mobile/users?page=' + page,
	        type: 'GET',
	        success: function (data){
	        	console.log(data);
	        	current_page = data.current_page;
	        	from = data.from;
	        	to = data.to;
	        	last_page = data.last_page;
	        	per_page = data.per_page;
	        	total = data.total;
	        	next = data.current_page + 1;
	        	var newRow = "";
	        	var paginateRow = "";
	        	$('#user-results').empty();
	        	$('#paginate-list').empty();
	        	if(current_page == 1) {
	        		$('#previous').hide();
	        	}else{
	        		$('#previous').show();
	        	}

	        	if(current_page == last_page){
	        		$('#next').hide();
	        	}else{
	        		$('#next').show();
	        	}

	        	if( current_page < 3 ){
	        		loop_page++;
	        	}else{
	        		if( current_page >= 3 ){
		        		loop_start_page = current_page - 1 ;
		        		
		        		if( ( per_page + ( current_page - 1 ) ) < ( last_page + 1 ) ){
			        		
			        		loop_page = current_page + (per_page - 1);

			        	}else{

			        		loop_start_page = last_page - 10;
			        		loop_page = last_page;
			        	}

			        	console.log(loop_page);
		        	}else{
		        		loop_start_page = 1;
		        	}
		        }	        	

	        	for(var x = loop_start_page; x <= loop_page; x++) {
	        		paginateRow = '<li class="paginate-trigger" rel="' + x + '"><a href="javascript:void(0)" class="paginate-container-list" id="paginate_'+ x +'"">' + x + '</a></li>';
	        		$('#paginate-list').append(paginateRow);
	        		if( x == current_page ) {
	        			$('.paginate-container-list').removeClass('paginate-active');
	        			$('#paginate_' + x).addClass('paginate-active');
	        		}
	        	}

	        	$.each(data.data, function(key, value){
        			newRow = '<tr><td>' + value.Name + '</td>';
        			newRow += '<td>' + value.Email + '</td>';
        			newRow += '<td></td>';
        			newRow += '<td>' + value.balance +' </td>';
        			newRow += '<td class="td-edit"><button class="btn btn-info edit-credit"><i class="fa fa-edit"></i></button></td>';
        			newRow += '<td class="td-check" hidden><button class="btn btn-success done-edit-credit"><i class="fa fa-check"></i></button></td>';
        			newRow += '<td hidden><button class="btn btn-danger cancel-edit-credit"><i class="fa fa-close"></i></button></td>';
        			newRow += '<td hidden>' + value.wallet_id +'</td>';
        			$('#user-results').append(newRow);
	        	});
	        	jQuery.unblockUI();
		 	}
		 });
	}

	function loadUsers( ) {
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		$.ajax({
	        url: base_url + 'mobile/users',
	        type: 'GET',
	        success: function (data){
	        	current_page = data.current_page;
	        	from = data.from;
	        	to = data.to;
	        	last_page = data.last_page;
	        	per_page = data.per_page;
	        	total = data.total;
	        	next = data.current_page + 1;
	        	var newRow = "";
	        	var paginateRow = "";

	        	$('#previous').hide();
	        	$('#next').show();

	        	for(var x = 1; x <= per_page; x++) {
	        		paginateRow = '<li class="paginate-trigger" rel="' + x + '"><a href="javascript:void(0)" class="paginate-container-list" id="paginate_'+ x +'"">' + x + '</a></li>';
	        		$('#paginate-list').append(paginateRow);
	        		if( x == current_page ) {
	        			$('.paginate-container-list').removeClass('paginate-active');
	        			$('#paginate_' + x).addClass('paginate-active');
	        		}
	        	}

	        	$('#user-results').empty();
	        	console.log(data.data);
	        	$.each(data.data, function(key, value){
        			newRow = '<tr><td>' + value.Name + '</td>';
        			newRow += '<td>' + value.Email + '</td>';
        			newRow += '<td>' + value.NRIC + '</td>';
        			newRow += '<td></td>';
        			newRow += '<td>' + value.balance +' </td>';
        			newRow += '<td class="td-edit"><button class="btn btn-info edit-credit"><i class="fa fa-edit"></i></button></td>';
        			newRow += '<td class="td-check" hidden><button class="btn btn-success done-edit-credit"><i class="fa fa-check"></i></button></td>';
        			newRow += '<td hidden><button class="btn btn-danger cancel-edit-credit"><i class="fa fa-close"></i></button></td>';
        			newRow += '<td hidden>' + value.wallet_id +'</td>';
        			$('#user-results').append(newRow);
	        	});
	        	jQuery.unblockUI();
		 	}
		 });
	}

	loadUsers();
	var credit_top_up = false;
	var promocode_top_up = false;
	var id
	$('body').on('click', '.edit-credit', function( e ) {
	    // alert("click");
		var name = $(this).closest("tr").find("td:nth-child(1)").text();
		var email = $(this).closest("tr").find("td:nth-child(2)").text();
		var company_name = $(this).closest("tr").find("td:nth-child(3)").text();
		var credit = ($(this).closest("tr").find("td:nth-child(5)").text()).replace(/[_\W]+/g, "");
		id = $(this).closest("tr").find("td:nth-child(9)").text();
		console.log(id);

		$(this).closest("tr").find(".td-edit").hide();
		$(this).closest("tr").find(".td-check").fadeIn();
		$(this).closest("tr").find("td:nth-child(7)").fadeIn();

		$(".manualTopUp-form #name").val(name);
		$(".manualTopUp-form #email").val(email);
		$(".manualTopUp-form #company_name").val(company_name);
		$(".manualTopUp-form #credit").val(credit);
	 });

	$('#done-edit-credit').click(function(event){
		event.preventDefault();
		
		
		if(jQuery("form").valid() == true){
			credit_top_up = true;
			promocode_top_up = false;
			$('#password-check-box').modal('show');
		}

		
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
	        		if(credit_top_up == true) {
						saveUser( );
	        		} else if(promocode_top_up == true) {
	        			updatePromoCode( );
	        		}
	        		$('#password-check-box').modal('hide');
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

	function saveUser( ) {
		var done = $(this);
		var name = $(".manualTopUp-form #name").val();
		var email = $(".manualTopUp-form #email").val();
		var company_name = $(".manualTopUp-form #company_name").val();
		var credit = $(".manualTopUp-form #credit").val();
		if( id == undefined ) {
			id = 0;
		}
		console.log(name, email, company_name, credit, id);
		dataValues = '&name='+name+'&email='+email+'&company_name='+company_name+'&credit='+credit+'&wallet_id='+id;
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		
		$.ajax({
        url: base_url + 'update/top/up',
        type: 'POST',
        data : dataValues,

        success: function (data){
        	console.log(data);
        	if(data == 1) {
				$.toast({
		        	heading: 'Success!',
		            text: 'User Top Up Updated.',
		            showHideTransition: 'slide',
		            icon: 'info',
		            hideAfter : 6000,
		            bgColor : '#1667AC'
		          });
				id = "";
 				loadUsers();
        	} else if(data == 2) {
        		$.toast({
		        	heading: 'Success!',
		            text: 'User Top Up Updated.',
		            showHideTransition: 'slide',
		            icon: 'info',
		            hideAfter : 6000,
		            bgColor : '#1667AC'
		          });
				id = "";
 				loadUsers();
 			} else if( data == 3 ) {
        		$.toast({
		        	heading: 'Oooops!',
		            text: 'Email Address already taken. Please use a different email address.',
		            showHideTransition: 'slide',
		            icon: 'error',
		            hideAfter : 6000,
		            bgColor : '#D34332'
		          });
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
 			$('#password-check-box').modal('hide');
        	jQuery.unblockUI();
			done.closest("tr").find(".td-edit").fadeIn();
			done.closest("tr").find(".td-check").hide();
			done.closest("tr").find("td:nth-child(9)").hide();

			done.closest("tr").find("td:nth-child(1)").html( name );
			done.closest("tr").find("td:nth-child(3)").html( email );
			done.closest("tr").find("td:nth-child(4)").html( company_name );
			done.closest("tr").find("td:nth-child(5)").html( "$" + credit );

			$(".manualTopUp-form #name").val("");
			$(".manualTopUp-form #email").val("");
			$(".manualTopUp-form #company_name").val("");
			$(".manualTopUp-form #credit").val("");
        	
        }
      });
	}


	function updatePromoCode( )
	{
		console.log('update');
	}

	$('body').on('click', '.cancel-edit-credit', function( e ){
		var name = $(this).closest("tr").find("td:nth-child(1) input").val();
		var email = $(this).closest("tr").find("td:nth-child(3) input").val();
		var company_name = $(this).closest("tr").find("td:nth-child(4) input").val();
		var credit = $(this).closest("tr").find("td:nth-child(5) input").val();

		$(this).closest("tr").find(".td-edit").fadeIn();
		$(this).closest("tr").find(".td-check").hide();
		$(this).closest("tr").find("td:nth-child(7)").hide();

		$(".manualTopUp-form #name").val("");
		$(".manualTopUp-form #email").val("");
		$(".manualTopUp-form #company_name").val("");
		$(".manualTopUp-form #credit").val("");

	});


	// promo list user
	$('#promo-code-list-trigger').click(function( ){
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		$.ajax({
	        url: base_url + 'promocode/list/user',
	        type: 'GET',

	        success: function (data){
	        	$('#promo-code-top-up').html(data);
	        	jQuery.unblockUI();
	        }
	    });
	});


	$('body').on('click', '.edit-promo', function( e ){
		console.log('sulod bes');
		var balance_temp = $(this).closest("tr").find("td:nth-child(3)").text();
		var balance = balance_temp.replace('$', '');
		$(this).closest("tr").find(".td-edit").hide();
		$(this).closest("tr").find(".td-check").fadeIn();
		$(this).closest("tr").find("td:nth-child(7)").fadeIn();
		$(this).closest("tr").find("td:nth-child(3)").html( "<input type='number' value='" + parseInt(balance) + "' class='form-control'>" );

	});

	$('body').on('click', '.cancel-edit-promo', function( e ){
		var balance = $(this).closest("tr").find("td:nth-child(3) input").val();
		$(this).closest("tr").find(".td-edit").fadeIn();
		$(this).closest("tr").find(".td-check").hide();
		$(this).closest("tr").find("td:nth-child(7)").hide();
		$(this).closest("tr").find("td:nth-child(3)").html( '$' + balance.toString() );
	});

	$('body').on('click', '.done-edit-promo', function( e ){
		credit_top_up = false;
		promocode_top_up = true;
		$('#password-check-box').modal('show');
	});
});