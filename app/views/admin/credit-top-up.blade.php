@include('admin.header-admin')
{{ HTML::style('assets/css/jquery-ui.css') }}
{{ HTML::style('assets/css/font-awesome.min.css') }}
{{ HTML::script('assets/js/jquery-ui.js') }}
{{ HTML::script('assets/js/form-validate.js') }}
{{ HTML::script('assets/js/credit-validate-form.js') }}
{{ HTML::script('assets/admin/credit-top-up.js') }}
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
@include('admin.header')
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="white-space-20"></div>
  <div class="col-md-2">
    <ul class="nav nav-pills nav-stacked nav-corporate" role="tablist">
      <li class="active"><a href="#manual-top-up" aria-controls="manual-top-up" role="tab" data-toggle="tab">Manual Top Up</a></li>
      <li id="promo-code-list-trigger"><a href="#promo-code-top-up" aria-controls="promo-code-top-up" role="tab" data-toggle="tab">Promo Code Top Up</a></li>
    </ul>
  </div>
  <div class="col-md-10 creditTopUp-content-wrapper">
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane fade in active" id="manual-top-up">
        <div class="col-md-5 no-padding">

          <h4 class="text-center custom-content-title one">Create / Edit User</h4>

          <form action="" method="POST" id="user-form">
            <div class="manualTopUp-form form-inline">
              <div class="form-group">
                <label>Name :</label>
                <input type="text" class="form-control" name="name" id="name">
              </div>
              <div class="form-group">
                <label>Email Address :</label>
                <input type="email" class="form-control" name="email" id="email">
              </div>
              <div class="form-group">
                <label>Company Name :</label>
                <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Optional...">
              </div>
              <div class="form-group">
                <label>Credit Top Up :</label>
                <b>$</b> <input type="number" class="form-control" min="0" max="1000" name="credit" id="credit">
              </div>
              <br>
              <div class="form-group text-right" style="width: 80%;">
                <button class="btn btn-success" id="done-edit-credit">Done</button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-7 no-padding">
          <h4 class="text-center custom-content-title two">Top Up User</h4>
            <div class="col-md-12">
              <input type="text" name="search" id="search" class="form-control" placeholder="Search here..." style="width: 70%; display: inline-block;">
              <button class="btn btn-success" style="display: inline-block;" id="show-all">All</button>
            </div>
            <table class="table table-striped table-responsive table-creditTopUp">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email Address</th>
                  <th>NRIC</th>
                  <th>Company Name</th>
                  <th>Credit Top Up</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="user-results">
              </tbody>
            </table>
            <nav aria-label="Page navigation">
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
            </nav>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane fade" id="promo-code-top-up">
        <div id="promo_code_result_list"></div>
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

<script type="text/javascript">
  $('#search').on('input',function( ){
    var prev = null;
    var next;
    var current_page;
    var from;
    var last_page;
    var per_page;
    var to;
    var total;
    var search = this.value;
    var newRow = "";
    dataValues = '&search='+search;
    if(search.length > 0) {
      
      $.ajax({
          url: base_url + 'search/users',
          type: 'POST',
          data: dataValues,
          success: function (data){
            current_page = data.current_page;
            from = data.from;
            to = data.to;
            last_page = data.last_page;
            per_page = data.per_page;
            total = data.total;
            next = data.current_page + 1;
            var newRow = "";
            var paginateRow = "";
            $('#user-results').empty();
            $('#paginate-list').empty();

            // if(prev == null) {
              $('#previous').hide();
              $('#next').hide();
            // }

            // for(var x = 1; x <= to; x++) {
            //   paginateRow = '<li class="paginate-trigger" rel="' + x + '"><a href="javascript:void(0)" class="paginate-container-list" id="paginate_'+ x +'"">' + x + '</a></li>';
            //   $('#paginate-list').append(paginateRow);
            //   if( x == current_page ) {
            //     $('.paginate-container-list').removeClass('paginate-active');
            //     $('#paginate_' + x).addClass('paginate-active');
            //   }
            // }

            $.each(data.data, function(key, value){
              newRow = '<tr><td>' + value.Name + '</td>';
              newRow += '<td>' + value.Email + '</td>';
              newRow += '<td>' + value.NRIC + '</td>';
              newRow += '<td></td>';
              newRow += '<td>' + value.balance +' </td>';
              newRow += '<td class="td-edit"><button class="btn btn-info edit-credit"><i class="fa fa-edit"></i></button></td>';
              newRow += '<td class="td-check" hidden><button class="btn btn-success done-edit-credit"><i class="fa fa-check"></i></button></td>';
              newRow += '<td hidden><button class="btn btn-danger cancel-edit-credit"><i class="fa fa-close"></i></button></td>';
              newRow += '<td hidden>' + value.wallet_id +'</td>';
              $('#user-results').append(newRow);
            });
          }
      });
    }
  });
</script>
@include('admin.footer-admin')
