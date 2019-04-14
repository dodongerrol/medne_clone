{{ HTML::script('assets/admin/credit-top-up.js') }}
<table class="table table-striped table-responsive table-creditTopUp">
  <thead>
    <tr>
      <th>Name</th>
      <th>Email Address</th>
      <th>Company Name</th>
      <th>Credit Top Up</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Jhon2</td>
      <td>Jhon2@gmail.com</td>
      <td>medicloud2</td>
      <td>$300</td>
      <td class="td-edit"><button class="btn btn-info edit-credit"><i class="fa fa-edit"></i></button></td>
      <td class="td-check" hidden><button class="btn btn-success done-edit-credit"><i class="fa fa-check"></i></button></td>
      <td hidden><button class="btn btn-danger cancel-edit-credit"><i class="fa fa-close"></i></button></td>
    </tr>
  </tbody>
</table>  