<nav class="navbar navbar-fixed-top admin-navbar">
  <div class="container-fluid">
    <div class="navbar-header">
      <!-- <a class="navbar-brand" href="#">MediCloud</a> -->
      <img alt="Brand" src="{{ asset('assets/images/ico_ MediCloud-Logo.svg') }}" height="45px">
    </div>
    <ul class="nav navbar-nav admin-nav-custom" style="margin-left: 20px;">
      <!-- <li class="{{Request::path() == 'admin/clinic/all-clinics' ? 'active' : '';}}">{{ HTML::link('admin/clinic/all-clinics', 'Manage Clinic')}}</li>
      <li class="{{Request::path() == 'admin/clinic/search_booking' ? 'active' : '';}}">{{ HTML::link('admin/clinic/search_booking', 'Search Booking')}}</li>
      <li class="{{Request::path() == 'admin/clinic/new-clinic' ? 'active' : '';}}">{{ HTML::link('admin/clinic/new-clinic', 'Add New Clinic')}}</li>
      <li class="{{Request::path() == 'admin/clinic/new-doctor' ? 'active' : '';}}">{{ HTML::link('admin/clinic/new-doctor', 'Add New Doctor')}}</li> 
      <li class="{{Request::path() == 'admin/clinic/all-doctors' ? 'active' : '';}}">{{ HTML::link('admin/clinic/all-doctors', 'All Doctors')}}</li>
      <li class="{{Request::path() == 'admin/user/signed-users' ? 'active' : '';}}">{{ HTML::link('admin/user/signed-users', 'Signed Users')}}</li>
      <li class="{{Request::path() == 'admin/corporate' ? 'active' : '';}}">{{ HTML::link('admin/corporate', 'Corporate')}}</li> -->
      <!-- <li class="{{Request::path() == 'admin/top/up/credit' ? 'active' : '';}}">{{ HTML::link('admin/top/up/credit', 'Credit Top Up')}}</li>
      <li class="{{Request::path() == 'admin/promocode' ? 'active' : '';}}">{{ HTML::link('admin/promocode', 'Promo Code')}}</li> -->

      <li class="dropdown">
        <a href="javascript:void(0)" class="dropbtn">Clinic</a>
        <ul class="dropdown-content">
          <li class="{{Request::path() == 'admin/clinic/all-clinics' ? 'active' : '';}}">{{ HTML::link('admin/clinic/all-clinics', 'Manage Clinic')}}</li>
          <li class="{{Request::path() == 'admin/clinic/new-clinic' ? 'active' : '';}}">{{ HTML::link('admin/clinic/new-clinic', 'Add New Clinic')}}</li>
        </ul>
      </li> 

      <li class="dropdown">
        <a href="javascript:void(0)" class="dropbtn">Doctor</a>
        <ul class="dropdown-content">
          <li class="{{Request::path() == 'admin/clinic/new-doctor' ? 'active' : '';}}">{{ HTML::link('admin/clinic/new-doctor', 'Add New Doctor')}}</li> 
          <li class="{{Request::path() == 'admin/clinic/all-doctors' ? 'active' : '';}}">{{ HTML::link('admin/clinic/all-doctors', 'All Doctors')}}</li>
        </ul>
      </li> 

      <li class="dropdown">
        <a href="javascript:void(0)" class="dropbtn">User</a>
        <ul class="dropdown-content">
          <li class="{{Request::path() == 'admin/user/signed-users' ? 'active' : '';}}">{{ HTML::link('admin/user/signed-users', 'Signed Users')}}</li>
        <li class="{{Request::path() == 'admin/corporate' ? 'active' : '';}}">{{ HTML::link('admin/corporate', 'Corporate')}}</li>
        </ul>
      </li> 

      <li class="dropdown">
        <a href="javascript:void(0)" class="dropbtn">Credit</a>
        <ul class="dropdown-content">
          <li class="{{Request::path() == 'admin/top/up/credit' ? 'active' : '';}}">{{ HTML::link('admin/top/up/credit', 'Credit Top Up')}}</li>
      <li class="{{Request::path() == 'admin/promocode' ? 'active' : '';}}">{{ HTML::link('admin/promocode', 'Promo Code')}}</li>
        </ul>
      </li> 

      <li class="dropdown">
        <a href="javascript:void(0)" class="dropbtn">Booking</a>
        <ul class="dropdown-content">
          <li class="{{Request::path() == 'admin/clinic/search_booking' ? 'active' : '';}}">{{ HTML::link('admin/clinic/search_booking', 'Search Booking')}}</li>
        </ul>
      </li> 

      <li class="dropdown">
        <a href="javascript:void(0)" class="dropbtn">Transaction</a>
        <ul class="dropdown-content">
          <li class="{{Request::path() == 'admin/clinic/transaction_history' ? 'active' : '';}}">{{ HTML::link('admin/clinic/transaction_history', 'Transaction History')}}</li>
          <li class="{{Request::path() == 'admin/clinic/credit_payments' ? 'active' : '';}}">{{ HTML::link('admin/clinic/credit_payments', 'Invoice Payments')}}</li>
        </ul>
      </li> 


    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li>{{ HTML::link('admin/auth/logout', 'Logout')}}</li>
    </ul>
  </div>
</nav>