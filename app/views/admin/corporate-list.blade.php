{{ HTML::script('assets/admin/corporate.js') }}

<div class="corporate-list">
  <table class="table table-striped table-responsive">
    <thead>
      <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email Address</th>
        <th>Company Name</th>
        <th>Credit Top Up</th>
        <th></th>
        <!-- <th></th> -->
      </tr>
    </thead>
    <tbody>
      @foreach($result as $res)
        <tr>
          <td>{{ $res->first_name }}</td>
          <td>{{ $res->last_name }}</td>
          <td>{{ $res->email }}</td>
          <td>{{ $res->company_name }}</td>
          <td>${{ $res->credit }}</td>
          <td class="td-edit"><button class="btn btn-info edit-corporate"><i class="fa fa-edit"></i></button></td>
          <td class="td-check" hidden><button class="btn btn-success done-edit-corporate"><i class="fa fa-check"></i></button></td>
          <td hidden><button class="btn btn-danger cancel-edit-corporate"><i class="fa fa-close"></i></button></td>
          <td hidden>{{ $res->corporate_id }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>