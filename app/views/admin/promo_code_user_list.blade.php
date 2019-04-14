<div class="corporate-list">
  <table class="table table-striped table-responsive">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email Address</th>
        <th>Wallet Balance</th>
        <th>Promo Code</th>
        <th>Credit Top Up</th>
        <th></th>
        <!-- <th></th> -->
      </tr>
    </thead>
    <tbody>
      @foreach($result as $res)
        <tr>
          <td>{{ $res->Name }}</td>
          <td>{{ $res->Email }}</td>
          <td>${{ $res->balance }}</td>
          <td>{{ $res->code }}</td>
          <td class="td-edit"><button class="btn btn-info edit-promo"><i class="fa fa-edit"></i></button></td>
          <td class="td-check" hidden><button class="btn btn-success done-edit-promo"><i class="fa fa-check"></i></button></td>
          <td hidden><button class="btn btn-danger cancel-edit-promo"><i class="fa fa-close"></i></button></td>
          <td hidden>{{ $res->wallet_id }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>