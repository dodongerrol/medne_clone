@include('common.header')

<style type="text/css">
	body{
		background: #1897D4;
	    background-image: url(https://medicloud.sg/medicloud_v2/public/assets/images/bg-effect.png);
	}
</style>

<div class="col-md-12 no-padding">
	<div class="activate-profile-wrapper">
		<div class="header">
			<img class="header-logo"  src="{{ URL::asset('assets/images/medi-widget-logo.png') }}" style="" />
			<!-- <img class="header-logo img-40"  src="medi-widget-logo.png" /> -->
		</div>
		<div class="body-success text-center" id="success-update" hidden>
			<p style="padding: 20px; font-size: 15px;">Thank you for updating your account! Please login to Medicloud App.</p>
			<p>Please click this link. <a href="http://medicloud.sg/app/user">Mednefits</a></p>
		</div>
		<div class="body">
			<div class="white-space-20"></div>
			<h4 class="text-center">Update your account to activate</h4>

			<form class="form-inline" id="user-update-form">
				<input type="hidden" name="user" id="user" value="{{ $user->UserID }}">
				<div class="form-group">
					<label>NRIC/FIN</label>
					<input type="text" class="form-control" name="nric" id="nric" value="{{ $user->NRIC }}">
				</div>
				<div class="form-group">
					<label>Your Name</label>
					<input type="text" class="form-control" name="name" id="name" value="{{ $user->Name }}">
				</div>
				<div class="form-group">
					<label>Phone</label>
					
					<div id="code-dropdown" class="col-md-2 phone-country" >
						<input type="button" class="form-control" id="phone_code" data-toggle="dropdown" value="{{ $user->PhoneCode ? $user->PhoneCode : '+65' }}">
						<ul class="dropdown-menu" id="doc-mobile-codes" style="">
						</ul>
				    </div>
					<input type="text" class="form-control phone-number" name="phone_number" id="phone_number" value="{{ $user->PhoneNo }}">
				</div>
				<div class="form-group">
					<label>Email</label>
					<input type="text" class="form-control" name="email" id="email" value="{{ $user->Email }}">
				</div>
			</form>
		</div>
		<div class="footer">
			<button class="btn btn-activate-profile" id="done-update-user-active-accout">Done</button>
		</div>
	</div>
</div>	

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$('#code-dropdown').on('shown.bs.dropdown', function () {

		    var $this = $(this);
		    // attach key listener when dropdown is shown
		    $(document).keypress(function(e){

		      // get the key that was pressed
		      var key = String.fromCharCode(e.which);
		      // look at all of the items to find a first char match
		      $this.find("li").each(function(idx,item){
		        $(item).addClass("hide"); // clear previous active item
		        $(item).removeClass("show");

		        if ($(item).text().charAt(0).toLowerCase() == key) {
		          // set the item to selected (active)
		          $(item).addClass("show");
		          $(item).removeClass("hide");
		        }
		        else{
		            $(item).addClass("hide");
		            $(item).removeClass("show");
		        }
		      });

		    });

		})

		// unbind key event when dropdown is hidden
		$('#code-dropdown').on('hide.bs.dropdown', function () {

		    var $this = $(this);

		    $this.find("li").each(function(idx,item){

		        $(item).addClass("show");
		        $(item).removeClass("hide");
		    });

		    $(document).unbind("keypress");

		})

	});	
</script>

@include('common.footer')