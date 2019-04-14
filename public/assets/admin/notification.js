// var protocol = jQuery(location).attr('protocol');
// var hostname = jQuery(location).attr('hostname');
// var folderlocation = $(location).attr('pathname').split('/')[1];
// window.base_url = protocol+'//'+hostname+'/'+folderlocation+'/public/admin/';

////window.base_url = 'http://localhost/medicloud_web/public/admin/';
//window.base_url = 'http://medicloud.sg/medicloud_web/public/app/';


window.base_loading_image = '<img src="http://medicloud.sg/medicloud_v2/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';
window.base_url = window.location.origin + '/admin/';


jQuery("document").ready(function(){
var socket = io.connect('https://frozen-bastion-83762.herokuapp.com/')

	socket.on('clinic', function (clinic) {
		$.ajax({
	        url: base_url + 'clinic/information',
	        type: 'POST',
	        data: { clinicID: clinic }
	      })
	      .done(function(data) {
	        console.log(data);
	        $.toast({
	        	heading: 'Hello Admin!',
	            text: 'New Book Appointment has been made to ' + data[0].Name,
	            showHideTransition: 'slide',
	            icon: 'info',
	            hideAfter : 6000,
	            bgColor : '#1667AC'
	          });
	      });
	});
});
