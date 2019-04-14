@include('admin.header-admin')
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
@include('admin.header')
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->
<!-- <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
{{ HTML::style('assets/css/jquery-ui.css') }}
{{ HTML::script('assets/js/jquery-ui.js') }}
<script type="text/javascript">

$(function() {
    $("#startdate").datepicker({
        dateFormat: "yy-mm-dd"
    });
    $("#enddate").datepicker({
        dateFormat: "yy-mm-dd"
    });

  });
</script>
<!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css"> -->
<div class="row">
	<div class="page-header col-md-3 col-md-offset-1">
		<h1 style="font-size: 100% !important;"><span class="label label-default"> Signed Users</span></h1>
	</div>
</div>
<div class="row">
  <div class="col-md-10 custom-block-section">
      <div class="search-area">
          <div class="form-inline">
          <div class="form-group">
            <input class="download_option" type="radio" name="downloadhradio" checked="checked" value="1">
            <label>Advanced search</label>
          </div>
          <div class="form-group" style="margin-right:20px">
            <input class="download_option" type="radio" name="downloadhradio"  value="0" id="all_users">
            <label>All</label>
          </div>
        </div>
          <div id="download-custom">
          	<br>
            <div class="col-md-3 no-side-padding form-group">
              <label>Start date</label>
              <input id="startdate" type="text" name="startdate" class="form-control">
            </div>

            <div class="col-md-3 form-group">
              <label>End date</label>
              <input id="enddate" type="text" name="enddate" class="form-control">
            </div>
          </div>
          <br>
          <div class="col-md-12 no-side-padding">
            <button class="btn btn-success" id="search_sign_users">Search</button>
            <button class="btn btn-primary" id="dl_sign_users" style="display: none;">Download</button>
          </div>

      </div>

	</div>
</div>
<br>
<br>
<div id="download-results-area"></div>
@include('admin.footer-admin')
