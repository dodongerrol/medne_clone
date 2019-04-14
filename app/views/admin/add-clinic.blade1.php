@include('admin.header-admin') 
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css">
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        
        <div class="page-header">
            <h1><span class="glyphicon glyphicon-plus"></span> Add Clinic</h1>
        </div>  

        @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $message)
                {{ $message }}<br>        
            @endforeach
        </div>
        @endif  

        <!-- FORM STARTS HERE -->
        {{ Form:: open(array('url' => 'admin/clinic/insert-clinic','files'=> true)) }}

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                <label for="name">Name*</label>
                <input type="text" id="name" class="form-control" name="name">
                @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('description')) has-error @endif">
                <label for="description">Description</label>
                <textarea id="description" class="form-control" name="description"></textarea>
                @if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('file')) has-error @endif">
                <label for="file">Image</label>
                <input type="file" name="file" class="form-control"> 
                @if ($errors->has('file')) <p class="help-block">{{ $errors->first('file') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('address')) has-error @endif">
                <label for="address">Address*</label>
                <input type="text" id="address" class="form-control" name="address">
                @if ($errors->has('address')) <p class="help-block">{{ $errors->first('address') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('city')) has-error @endif">
                <label for="city">City*</label>
                <input type="text" id="city" class="form-control" name="city">
                @if ($errors->has('city')) <p class="help-block">{{ $errors->first('city') }}</p> @endif
            </div>            

            <div class="form-group  @if ($errors->has('state')) has-error @endif">
                <label for="state">State</label>
                <input type="text" id="state" class="form-control" name="state">
                @if ($errors->has('state')) <p class="help-block">{{ $errors->first('state') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('country')) has-error @endif">
                <label for="country">Country*</label>
                {{-- <input type="text" id="country" class="form-control" name="country"> --}}
                <select name="country" id="country" class="form-control">
                    <option value="Singapore">Singapore</option>
                </select>
                @if ($errors->has('country')) <p class="help-block">{{ $errors->first('country') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('postal')) has-error @endif">
                <label for="postal">Postal*</label>
                <input type="text" id="postal" class="form-control" name="postal">
                @if ($errors->has('postal')) <p class="help-block">{{ $errors->first('postal') }}</p> @endif
            </div>

         	<div class="form-group  @if ($errors->has('district')) has-error @endif">
                <label for="district">District</label>
                <input type="text" id="district" class="form-control" name="district">
                @if ($errors->has('district')) <p class="help-block">{{ $errors->first('district') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('latitude')) has-error @endif">
                <label for="latitude">Latitude*</label>
                <input type="text" id="latitude" class="form-control" name="latitude">
                @if ($errors->has('latitude')) <p class="help-block">{{ $errors->first('latitude') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('longitude')) has-error @endif">
                <label for="longitude">Longitude*</label>
                <input type="text" id="longitude" class="form-control" name="longitude">
                @if ($errors->has('longitude')) <p class="help-block">{{ $errors->first('longitude') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('phone')) has-error @endif">
                <label for="phone">Phone*</label>
                <input type="text" id="phone" class="form-control" name="phone">
                @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
            </div>

         	<div class="form-group  @if ($errors->has('mrt')) has-error @endif">
                <label for="mrt">MRT</label>
                <input type="text" id="mrt" class="form-control" name="mrt">
                @if ($errors->has('mrt')) <p class="help-block">{{ $errors->first('mrt') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('opening')) has-error @endif">
                <label for="opening">Opening*</label>
                <input type="text" id="opening" class="form-control" name="opening">
                @if ($errors->has('opening')) <p class="help-block">{{ $errors->first('opening') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('insurance_company')) has-error @endif">
                <label for="insurance_company">Insurance Company</label>
                {{ Form::select('insurance_company[]', $company, null, array('multiple'=>'multiple','class' => 'form-control')) }}
                @if ($errors->has('insurance_company')) <p class="help-block">{{ $errors->first('insurance_company') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('email')) has-error @endif">
                <label for="email">Email</label>
                <input type="email" id="email" class="form-control" name="email">
                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('password')) has-error @endif">
                <label for="password">Password</label>
                <input type="password" id="password" class="form-control" name="password">
                @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
            </div>

  	       <button type="submit" class="btn btn-info  btn-sm">Save</button>
  	       {{-- <button type="reset" class="btn btn-info"></button> --}}
           {{ HTML::link('admin/clinic/all-clinics', 'Cancel','class="btn btn-info btn-sm"')}}

        {{ Form:: close() }}

    </div>
</div>
@include('admin.footer-admin')