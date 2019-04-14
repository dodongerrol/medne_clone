@include('admin.header-admin')
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->
{{ HTML::style('assets/css/jquery-ui.css') }}
{{ HTML::script('assets/js/jquery-ui.js') }}
<!-- <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
<script type="text/javascript">

$(function() {
    $("#startdate").datepicker({
        dateFormat: "d-m-yy"
    });
    $("#enddate").datepicker({
        dateFormat: "d-m-yy"
    });
    $("#created_startdate").datepicker({
        dateFormat: "d-m-yy"
    });
    $("#created_enddate").datepicker({
        dateFormat: "d-m-yy"
    });
  });
  </script>

{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
<!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css"> -->
@include('admin.header')
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>

	<div class="col-md-10 custom-block-section" >
    <div class="search-area">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#byId" aria-controls="byId" role="tab" data-toggle="tab">Search by Booking id</a></li>
          <li role="presentation"><a href="#byAdv" aria-controls="byAdv" role="tab" data-toggle="tab">Advance search</a></li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="byId">
            <br>
            <h4><b>Search by booking ID</b></h4>
            <br>

            <div>
                <label>Enter booking id</label>
                <input id="bookingid" type="text" name="bookingid" class="form-control" style="width: 250px;">
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="byAdv">
            <br>
            <h4><b>Custom Search</b></h4>
            <br>
            <div class="col-md-12 no-side-padding">
              <label>Date of Appoinment:</label><br>
              <div class="col-md-3 no-side-padding form-group">
                <label>Start date</label>
                <input id="startdate" type="text" name="startdate" class="form-control">
              </div>

              <div class="col-md-3 form-group">
                <label>End date</label>
                <input id="enddate" type="text" name="enddate" class="form-control">
              </div>

              <div class="col-md-3 form-group">
                <label>Select clinic</label>
                <select class="form-control" id="clinic" name="clinic" style="width: 250px">
                    <option value="">Select</option>
                    <?php// if($cliniclist){
                        //foreach($cliniclist as $clinicli){
                        //    echo '<option value="'.$clinicli->ClinicID.'">'.$clinicli->Name.'</option>';
                        //}
                    //}?>
                </select>
              </div>


            </div>

            <div class="col-md-12 no-side-padding">
              <label>Date of Appoinment Created:</label><br>

              <div class="col-md-3 no-side-padding form-group">
                <label>Start date</label>
                <input id="created_startdate" type="text" name="created_startdate" class="form-control">
              </div>

              <div class="col-md-3 form-group">
                <label>End date</label>
                <input id="created_enddate" type="text" name="created_enddate" class="form-control">
              </div>

              <div class="col-md-3 form-group">
                <label>Select Doctor</label>
                <select class="form-control" id="doctor" name="doctor" style="width: 250px">
                    <option value="">select</option>
                </select>
              </div>

            </div>
          </div>
        </div>

        <div class="btn-options">
          <br>
          <a href="#" class="btn btn-primary" name="dl_button" id="dl_generate">Download as CSV</a>
          <br>
          <br>
          <button type="button" name="search_button" id="search_booking" class="btn btn-success" style="width: 140px;">Search Now</button>
        </div>
        <br>
    </div>
  </div>
  <br>
  <br>
  <div id="results-area"></div>

@include('admin.footer-admin')
