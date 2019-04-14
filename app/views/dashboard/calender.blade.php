@include('common.home_header')
<style type="text/css">
	div.modal-content {
	    top: 25px !important;
	}
</style>
<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/calender_index.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/search-features.js?_={{ $date->format('U') }}"></script>

<script src="/assets/js/OneSignalSDK.js" async='async'></script>
<script>
  var OneSignal = window.OneSignal || [];

  jQuery.ajax({   
    type: "GET",
    url : window.location.origin + "/config/notification",
    success : function(data){
       console.log(data);
		    OneSignal.push(["init", {
		      appId: data,
		      autoRegister: true, // Set to true to automatically prompt visitors 
		      httpPermissionRequest: {
		        enable: true
		      },
		      notifyButton: {
		          enable: false /* Set to false to hide */
		      }
		    }]);
			OneSignal.push(["sendTag", "clinicid", "{{$clincID}}"])
       
    }
  }); 

</script>

<div id="calendar_page_container">
	
</div>

@include('common.footer')

