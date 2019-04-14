@include('admin.header-admin')
{{ HTML::style('assets/css/jquery-ui.css') }}
{{ HTML::style('assets/css/sweetalert.css') }}
{{ HTML::style('assets/css/font-awesome.min.css') }}
{{ HTML::script('assets/js/jquery-ui.js') }}
{{ HTML::script('assets/js/sweetalert.min.js') }}
{{ HTML::script('assets/js/form-validate.js') }}
{{ HTML::script('assets/js/promo_code_validate.js') }}
{{ HTML::script('assets/admin/promo_code.js') }}
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap-toggle.min.css') }}
{{ HTML::script('assets/css/bootstrap/js/bootstrap-toggle.min.js') }}
@include('admin.header')
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="col-md-10 col-md-offset-1 creditTopUp-content-wrapper">
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane fade in active">
        <div class="col-md-5 no-padding">

          <h4 class="text-center custom-content-title one">Create / Edit Promo</h4>

          <form action="" method="POST" id="promo-form">
            <div class="manualTopUp-form form-inline">
              <div class="form-group">
                <label>Code :</label>
                <input type="text" class="form-control" name="code" id="code">
              </div>
              <div class="form-group">
                <label>Amount :</label>
                <input type="number" class="form-control" name="amount" id="amount">
              </div>
              <div class="form-group">
                <label>Active :</label>
                <input type="checkbox" name="active" id="active" checked data-toggle="toggle" data-style="android" data-onstyle="primary">
              </div>
              <br>
              <div class="form-group text-right" style="width: 80%;">
                <button class="btn btn-success" id="save-promo">Done</button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-7 no-padding">
          <h4 class="text-center custom-content-title two">Promo Code List</h4>
            <!-- <div class="col-md-12">
              <input type="text" name="search" id="search" class="form-control" placeholder="Search here..." style="width: 70%; display: inline-block;">
              <button class="btn btn-success" style="display: inline-block;" id="show-all">All</button>
            </div> -->
            <table class="table table-striped table-responsive table-creditTopUp">
              <thead>
                <tr>
                  <th>Code Name</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="promo-results">
              </tbody>
            </table>
           <!--  <nav aria-label="Page navigation">
              <ul class="pagination">
                <li id="previous">
                  <a href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <li id="paginate-list"></li>
                <li id="next">
                  <a href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav> -->
        </div>
      </div>
    </div>
  </div>

@include('admin.footer-admin')
