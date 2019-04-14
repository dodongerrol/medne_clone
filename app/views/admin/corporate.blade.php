@include('admin.header-admin')
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->
{{ HTML::style('assets/css/jquery-ui.css') }}
{{ HTML::style('assets/css/font-awesome.min.css') }}
{{ HTML::script('assets/js/jquery-ui.js') }}
{{ HTML::script('assets/js/form-validate.js') }}
{{ HTML::script('assets/js/corporate-validate-form.js') }}
{{ HTML::script('assets/admin/corporate.js') }}

<!-- <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
<!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css"> -->
@include('admin.header')
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="col-md-2">
    <ul class="nav nav-pills nav-stacked nav-corporate" role="tablist">
      <li class="active"><a href="#create-customer" aria-controls="create-customer" role="tab" data-toggle="tab">Create Corporate</a></li>
      <li id="customer-list-trigger"><a href="#customer-list" aria-controls="customer-list" role="tab" data-toggle="tab">Corporate List</a></li>
    </ul>
  </div>
  <div class="col-md-10 corporate-content-wrapper">
    <div class="">
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="create-customer">
          <form action="" method="POST" id="corporate-form-signup">
            <div class="corporate-form form-inline">
              <div class="form-group">
                <label>First Name :</label>
                <input type="text" class="form-control" name="first_name" id="first_name">
              </div>
              <div class="form-group">
                <label>Last Name :</label>
                <input type="text" class="form-control" name="last_name" id="last_name">
              </div>
              <div class="form-group">
                <label>Email Address :</label>
                <input type="email" class="form-control" name="email" id="email">
              </div>
              <div class="form-group">
                <label>Company Name :</label>
                <input type="text" class="form-control" name="company_name" id="company_name">
              </div>
              <div class="form-group">
                <label>Credit Top Up :</label>
                <b>$</b> <input type="number" class="form-control" min="1" max="1000" name="credit" id="credit">
              </div>
              <br>
              <div class="form-group">
                <button class="btn btn-success" id="create-company">Done</button>
              </div>
            </div>
          </form>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="customer-list">
          
        </div>
      </div>
    </div>
  </div>

  <!-- modal -->
  <div class="modal fade" id="password-check-box" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <form action="" method="POST" id="password-credit-form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Enter password to save data</h4>
        </div>
        <div class="modal-body">
          <input type="password" name="password" id="password" class="form-control" placeholder="Input Password to save data ..." required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="check-pass">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@include('admin.footer-admin')
