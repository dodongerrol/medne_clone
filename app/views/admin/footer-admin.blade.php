<!--<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
<!-- <script src="//cdn.datatables.net/1.10.6/js/jquery.dataTables.min.js"></script> -->
<!-- <script src="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.js"></script> -->
<!-- <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script> -->
{{ HTML::script('assets/js/jquery.dataTables.min.js') }}
{{ HTML::script('assets/js/dataTables.bootstrap.js') }}
{{ HTML::script('assets/css/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('assets/admin/moment-with-locales.js') }}   
{{ HTML::script('assets/admin/bootstrap-datetimepicker.js') }}   
<script>
	var oTable = null;

	$(document).ready(function() {
		
    	var base_url = jQuery('#base_url').val();

    	oTable = $('#allClinics').DataTable({
			"ajax": "get-all-clinics",
			oLanguage: {
        	sProcessing: "<div style='width:100%;' class='text-center'><img src='http://www.dlib.si/images/loading.gif'></div>"
		    },
		    processing : true
		});	

		$('#allDoctors').DataTable({
			
		});	

		$('#startTime').datetimepicker({
			format: 'LT',
			useCurrent: true
		});

		$('#endTime').datetimepicker({
			format: 'LT',
			useCurrent: true
		});


	    $( "#setTime" ).submit(function( event )
	    {			
    	  	event.preventDefault();
    	  	// Create on Date
    	  	var date = new Date();
			var today =  + (date.getFullYear())+'-' + (date.getMonth() + 1) + '-' + date.getDate();

    	  	var formData = {
    	  		clinicID		  : jQuery('#clinicid').val(), 
    	  		createdOn		  : today, 
				clinicStartDate   : jQuery('#startTime').find("input").val(),
				clinicEndDate     : jQuery('#endTime').find("input").val(),				
				clinicDateMon     : jQuery('#monday').prop('checked') ? '1' : '0',				
				clinicDateTue     : jQuery('#tuesday').prop('checked') ? '1' : '0',				
				clinicDateWed     : jQuery('#wednesday').prop('checked') ? '1' : '0',				
				clinicDateThu     : jQuery('#thursday').prop('checked') ? '1' : '0',				
				clinicDateFri     : jQuery('#friday').prop('checked') ? '1' : '0',				
				clinicDateSat     : jQuery('#saturday').prop('checked') ? '1' : '0',				
				clinicDateSun     : jQuery('#sunday').prop('checked') ? '1' : '0',				
				submitMode	      : "ajax"
			};

			// create data sent url
			dataSentUrl = base_url +'/admin/clinic/insert-time';
			timeEditUrl = base_url +'/admin/clinic/time/';

			//Ajax funtion
			jQuery.ajax({
				type			: 'POST',
				url				: dataSentUrl,
				dataType		: 'json',
				data			: formData,
				success         : function (response, status)
				{	
					if(response.status == "error")
					{
						// console.log('fail');
						$('.unsuccessMsagesSetClinicTime').addClass('alert-danger');
					  	$('.unsuccessMsagesSetClinicTime').removeClass('hide');
					  	$('.unsuccessMsagesSetClinicTime').slideDown();
					  	$('.unsuccessMsagesSetClinicTime span').html(response.msg);
					  	$(document).scrollTop(0);
					  	$('.unsuccessMsagesSetClinicTime').delay(4000).slideUp();	
					}
					else
					{					  
					  	// console.log(response.startTime);				    
				 	  	$('.successMsagesSetClinicTime').addClass('alert-success');
					  	$('.successMsagesSetClinicTime').removeClass('hide');
					  	$('.successMsagesSetClinicTime').slideDown();
					  	$('.successMsagesSetClinicTime span').html(response.msg);
					  	$(document).scrollTop(0);
					  	$('.successMsagesSetClinicTime').delay(4000).slideUp();	


				  		var $tr = $('<tr/>');
					    $tr.append($('<td/>').html(response.clinicTimeId));
					    $tr.append($('<td/>').html(response.startTime));
					    $tr.append($('<td/>').html(response.endTime));
					    if(response.monday == 1){					    
					    	$tr.append($('<td/>').html('<span class="btn btn-success">'));
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    if(response.tuesday == 1){					    
					    	$tr.append($('<td/>').html('<span class="btn btn-success">'));
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    if(response.wednesday == 1){					    
					    	$tr.append($('<td/>').html('<span class="btn btn-success">'));
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    if(response.thursday == 1){					    
					    	$tr.append($('<td/>').html('<span class="btn btn-success">'));
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    if(response.friday == 1){					    
					    	$tr.append($('<td/>').html('<span class="btn btn-success">'));
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    if(response.saturday == 1){					    
					    	$tr.append($('<td/>').html('<span class="btn btn-success">'));
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    if(response.sunday == 1){					    
					    	$tr.append($('<td/>').html('<span class="btn btn-success">'));
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    if(response.active == 1){
					    	$tr.append($('<td/>').html('Active'));				    	
					    }
					    else{
					    	$tr.append($('<td/>').html(''));
					    }
					    	$tr.append($('<td/>').html('<a href="'+timeEditUrl+response.clinicTimeId+'/edit" class="btn btn-sm btn-info">Edit</a>'));
					    $('#viewClinicTimeDetails tr:last').before($tr);
					  	
					  	// clear values
					  	jQuery('#startTime').find("input").val("");
  						jQuery('#endTime').find("input").val("");
  						jQuery('input:checkbox').removeAttr('checked');
  						jQuery('input:checkbox').removeAttr('checked');
  						jQuery('.status').removeClass('active');
					}
				},
				
			 	error: function(data){
			        // var errors = data.responseJSON;
			        console.log(data.responseText);
			        $('.errorMsages').addClass('alert-danger');
			        $('.errorMsages').removeClass('hide');
			        $('<div class="errorMsages"></div>').slideDown();
			        $('.errorMsages span').html(data.responseText);
			        $(document).scrollTop(0);
				  	$('.errorMsages').delay(4000).slideUp();

			        // Render the errors with js ...
		      	}
			});  			
    	});
		
	});
</script>
</body>
</html>