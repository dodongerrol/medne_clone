<!DOCTYPE html>
<html>
<head>
	<title></title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="https://raw.githubusercontent.com/simontabor/jquery-toggles/master/toggles.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

{{ HTML::script('assets/dashboard/lc_switch.js') }}
{{ HTML::style('assets/dashboard/lc_switch.css') }}

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>



<style type="text/css" media="screen">


</style>


</head>
<body>

	<div class="container">

	<br>
		<!-- <input  data-toggle="toggle" class="sat" type="checkbox"><br> -->
<div style="position: relative;">
	
		<button id="createNewStaffBtn" class="pull-left btn btn-circle" title="" data-original-title="Add New Staff">
		<i class="icon-plus"></i></button>

	<div id="addStaffPopup" class="addStaffPopup popover bottom" style="left: -124px; top:15px;display: none;">
		<div class="arrow"></div>
		<span class="popover-title" style="background-color: #f8f8f8;font-size: 15px;padding: 8px 14px; display:block;">Add New Staff</span>
		<div class="popover-content">
		<input id="staffNewName" type="text" placeholder="Staff Name">
		<input id="staffNewEmail" type="text" placeholder="Staff E-mail">
		<button id="addstaff" class="new-gray-btn new-green-btn">Add Staff</button>
		<button class="new-gray-btn cancelNewStaff">Cancel</button>
		</div>
	</div>

</div>

	</div> <!-- end of container -->

</body>
</html>




<script type="text/javascript">
	
	jQuery(document).ready(function($) {
	
		       $('#createNewStaffBtn').click(function(event) {
		       	/* Act on the event */$('#addStaffPopup').css('display', 'block');
		       });

		       $('.cancelNewStaff').click(function(event) {
		       	/* Act on the event */$('#addStaffPopup').css('display', 'none');
		       });

	});


</script>