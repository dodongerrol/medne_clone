<br>

<div class="widget-container">

<div class="col-md-12" style="padding: 0px; padding-bottom: 15px; border-bottom: 1px solid #ccc;">
<span style="padding-top: 15px; font-size: large; font-weight: bold;">Booking Widget Integration</span>
</div>
<br><br><br>

<div class="row">
	<div class="col-md-2">
			<H5><b> Widget Code<b></H5>
	</div>	
	<div class="col-md-8">
			<textarea id="widgetlink" placeholder="" rows="6" style="border: 1px solid #d9d9d9; border-radius: 5px; padding-right: 10px; color: #686868; background: white; width: 485px;"><a href="{{url('/')}}/app/widget/{{$clinicid}}" onclick="window.open(this.href, 'newwindow', 'menubar=1,resizable=1,width=900,height=700, left=300, right=100'); return false;" title="Medicloud Clinic Widget"><img src="{{url('/')}}/assets/images/medicloudbutton.png"></a></textarea>
		</div>
	
</div>
<hr>
<div style="clear: both"></div>
<div class="row">
	<div class="col-md-2">
		&nbsp;
	</div>	
	<div class="col-md-8">
		 Copy & paste this link into your website. <br><br>
		<button class=" staff-btn" id="copy" style="width: 140px;">Copy to Clipboard</button>
	</div>
	
</div>

</div>




<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		$('#copy').click(function(event) {
			try
            {
                $('#widgetlink').select();
                document.execCommand('copy');
            }
            catch(e)
            {
                alert(e);
            }	

		});

		// --------- Set Navigation bar height ------------------

		var page_height = $('#profile-detail-wrapper').height()+52;
		var win_height = $(window).height()

		if (page_height > win_height){

		    $("#setting-navigation").height($('#profile-detail-wrapper').height()+52);
		    $("#profile-side-list").height($('#profile-detail-wrapper').height()+52);
		}
		else{

		    $("#setting-navigation").height($(window).height()-52);
		    $("#profile-side-list").height($(window).height()-52);
		}

	});
</script>