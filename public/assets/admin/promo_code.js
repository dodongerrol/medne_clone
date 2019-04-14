// var protocol = jQuery(location).attr('protocol');
// var hostname = jQuery(location).attr('hostname');
// var folderlocation = $(location).attr('pathname').split('/')[1];
// window.base_url = protocol+'//'+hostname+'/'+folderlocation+'/public/admin/';
window.base_loading_image = '<img src="http://medicloud.sg/medicloud_v2/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';
window.base_url = window.location.origin + '/admin/';

jQuery("document").ready(function(){
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

	promoCodeValidation( );
	getPromoCode( );

	// display list of promo
	function getPromoCode( ) {
		jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
		$.ajax({
	        url: base_url + 'promocode/get',
	        type: 'GET',

	        success: function (data){
	        	// console.log(data);
	        	// current_page = data.current_page;
	        	// from = data.from;
	        	// to = data.to;
	        	// last_page = data.last_page;
	        	// per_page = data.per_page;
	        	// total = data.total;
	        	// next = data.current_page + 1;

	        	$('#promo-results').empty();
	        	// $('#paginate-list').empty();
	        	// if(current_page == 1) {
	        	// 	$('#previous').hide();
	        	// }else{
	        	// 	$('#previous').show();
	        	// }

	        	// if(current_page == last_page){
	        	// 	$('#next').hide();
	        	// }else{
	        	// 	$('#next').show();
	        	// }

	        	// if( current_page < 3 ){
	        	// 	loop_page++;
	        	// }else{
	        	// 	if( current_page >= 3 ){
		        // 		loop_start_page = current_page - 1 ;
		        		
		        // 		if( ( per_page + ( current_page - 1 ) ) < ( last_page + 1 ) ){
			        		
			       //  		loop_page = current_page + (per_page - 1);

			       //  	}else{

			       //  		loop_start_page = last_page - 10;
			       //  		loop_page = last_page;
			       //  	}

			       //  	console.log(loop_page);
		        // 	}else{
		        // 		loop_start_page = 1;
		        // 	}
		        // }

		        // for(var x = loop_start_page; x <= loop_page; x++) {
	        	// 	paginateRow = '<li class="paginate-trigger" rel="' + x + '"><a href="javascript:void(0)" class="paginate-container-list" id="paginate_'+ x +'"">' + x + '</a></li>';
	        	// 	$('#paginate-list').append(paginateRow);
	        	// 	if( x == current_page ) {
	        	// 		$('.paginate-container-list').removeClass('paginate-active');
	        	// 		$('#paginate_' + x).addClass('paginate-active');
	        	// 	}
	        	// }

	        	$.each(data, function(key, value){
	        		if(value.active == 1) {
	        			var active = "Active";
	        		} else {
	        			var active = "Inactive";
	        		}
        			newRow = '<tr><td>' + value.code + '</td>';
        			newRow += '<td>' + value.amount + '</td>';
        			newRow += '<td>' + active +' </td>';
        			newRow += '<td class="td-edit"><button class="btn btn-info edit-credit"><i class="fa fa-edit"></i></button></td>';
        			newRow += '<td class="td-check" hidden><button class="btn btn-success done-edit-credit"><i class="fa fa-check"></i></button></td>';
        			newRow += '<td class="td-delete"><button class="btn btn-success delete-credit"><i class="fa fa-trash"></i></button></td>';
        			newRow += '<td hidden><button class="btn btn-danger cancel-edit-credit"><i class="fa fa-close"></i></button></td>';
        			newRow += '<td hidden>' + value.promo_code_id +'</td>';
        			newRow += '<td hidden>' + value.active +'</td>';
        			$('#promo-results').append(newRow);
	        	});
	        	jQuery.unblockUI();
	        }

   		});
	}

	// save promo
	$('#save-promo').click(function( event ){
		event.preventDefault();

		var code = $('#code').val();
		var amount = $('#amount').val();
		if($('#active').is(':checked')) {
			var active = 1;
		} else {
			var active = 0;
		}
		if( id == undefined ) {
			id = 0;
		}

		if(amount < 0)
		{
			alert('Please input amount greater than 0.');
		} else {
			dataValues = '&code='+code+'&amount='+amount+'&active='+active+'&id='+id;
			// console.log(dataValues);
			if(jQuery("form").valid() == true){
				jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
				$.ajax({
			        url: base_url + 'promocode/create',
			        type: 'POST',
			        data : dataValues,

			        success: function (data){
			        	if(data == 1) {
			        		$.toast({
					        	heading: 'Success!',
					            text: 'Promo Code created.',
					            showHideTransition: 'slide',
					            icon: 'info',
					            hideAfter : 6000,
					            bgColor : '#1667AC'
					          });
							$('#code').val('');
							$('#amount').val('');
							id = "";
							getPromoCode( );
			        	} else if(data == 2) {
			        		$.toast({
					        	heading: 'Oooops!',
					            text: 'Promo Code Name already existed.',
					            showHideTransition: 'slide',
					            icon: 'error',
					            hideAfter : 6000,
					            bgColor : '#D34332'
					          });
			        	} else if(data == 3) {
			        		$.toast({
					        	heading: 'Success!',
					            text: 'Promo Code updated.',
					            showHideTransition: 'slide',
					            icon: 'info',
					            hideAfter : 6000,
					            bgColor : '#1667AC'
					          });
			        		$('#code').val('');
							$('#amount').val('');
							id = "";
			        		getPromoCode( );
			        	} else {
			        		$.toast({
					        	heading: 'Oooops!',
					            text: 'Something went wrong. Try again later.',
					            showHideTransition: 'slide',
					            icon: 'error',
					            hideAfter : 6000,
					            bgColor : '#D34332'
					          });
			        	}

			        	jQuery.unblockUI();
			        }

		   		});
			}
		}

	});

	// edit promo
	var id
	$('body').on('click', '.edit-credit', function( e ) {
	    // alert("click");
		var code = $(this).closest("tr").find("td:nth-child(1)").text();
		var amount = $(this).closest("tr").find("td:nth-child(2)").text();
		var active = $(this).closest("tr").find("td:nth-child(8)").text();
		id = $(this).closest("tr").find("td:nth-child(8)").text();
		// console.log(id);
		// console.log(active);

		$(this).closest("tr").find(".td-edit").hide();
		$(this).closest("tr").find(".td-delete").hide();
		$(this).closest("tr").find(".td-check").fadeIn();
		$(this).closest("tr").find("td:nth-child(7)").fadeIn();

		$(".manualTopUp-form #code").val(code);
		$(".manualTopUp-form #amount").val(amount);
		if(active == '1') {
			 $('#active').bootstrapToggle('on');
		} else if(active == '0') {
			 $('#active').bootstrapToggle('off');
		}
	 });

// cancel edit
	$('body').on('click', '.cancel-edit-credit', function( e ){
		var code = $(this).closest("tr").find("td:nth-child(1) input").val();
		var amount = $(this).closest("tr").find("td:nth-child(2) input").val();
		var active = $(this).closest("tr").find("td:nth-child(3) input").val();

		$(this).closest("tr").find(".td-edit").fadeIn();
		$(this).closest("tr").find(".td-delete").fadeIn();
		$(this).closest("tr").find(".td-check").hide();
		$(this).closest("tr").find("td:nth-child(7)").hide();

		$(".manualTopUp-form #code").val("");
		$(".manualTopUp-form #amount").val("");
		 $('.manualTopUp-form #active').bootstrapToggle('on');
	});

// delete
$('body').on('click', '.delete-credit', function( e ){
		id = $(this).closest("tr").find("td:nth-child(8)").text();
		console.log(id);
		swal({   
			title: "Are you sure?",   
			text: "You will not be able to recover this promo code!",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#508DC1",   
			confirmButtonText: "Yes, delete it!",   
			closeOnConfirm: false }, 
			function(){
				jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
				$.ajax({
			        url: base_url + 'promocode/remove/' + id,
			        type: 'GET',
			        success: function (data){
			        	console.log(data);
			        	if(data == 1) {
							swal("Deleted!", "Your imaginary file has been deleted.", "success");
							getPromoCode( );
			        	} else {
			        		swal({ title: "Oooops! ",   text: "Something went wrong.",   timer: 2000, type: "error",  showConfirmButton: false });
			        	}
			        	jQuery.unblockUI();
			        }
			    });
			});
	});
});