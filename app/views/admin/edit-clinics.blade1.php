@include('admin.header-admin')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css">
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        
        <div class="page-header">
            <h1><span class="glyphicon glyphicon-plus"></span>Edit {{ $clinic->Name }}</h1>
        </div>  

        @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $message)
                {{ $message }}<br>        
            @endforeach
        </div>
        @endif  

        <!-- FORM STARTS HERE -->
         {{ Form:: open(array('url' => 'admin/clinic/'.$clinic->ClinicID.'/update','files'=> true)) }}

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                <label for="name">Name*</label>
                <input type="text" id="name" class="form-control" name="name" value="{{ $clinic->Name }}">
                <input type="hidden" id="clinicid" class="form-control" name="clinicid" value="{{ $clinic->ClinicID }}">
                {{-- <input type="hidden" id="userid" class="form-control" name="userid" value="{{ $user->UserID }}"> --}}
                @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('description')) has-error @endif">
                <label for="description">Description</label>
                <textarea id="description" class="form-control" name="description">{{ $clinic->Description }}</textarea>
                @if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('file')) has-error @endif">
                {{ HTML::image($clinic->image, $clinic->Name, array( 'width' => 50, 'height' => 50 )) }}
                <label for="file">Image</label>
                <input type="file" name="file" class="form-control"> 
                @if ($errors->has('file')) <p class="help-block">{{ $errors->first('file') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('address')) has-error @endif">
                <label for="address">Address*</label>
                <input type="address" id="address" class="form-control" name="address" value="{{ $clinic->Address }}">
                @if ($errors->has('address')) <p class="help-block">{{ $errors->first('address') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('city')) has-error @endif">
                <label for="city">City*</label>
                <input type="city" id="city" class="form-control" name="city" value="{{ $clinic->City }}">
                @if ($errors->has('city')) <p class="help-block">{{ $errors->first('city') }}</p> @endif
            </div>            

            <div class="form-group  @if ($errors->has('state')) has-error @endif">
                <label for="state">State</label>
                <input type="state" id="state" class="form-control" name="state" value="{{ $clinic->State }}">
                @if ($errors->has('state')) <p class="help-block">{{ $errors->first('state') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('country')) has-error @endif">
                <label for="country">Country*</label>
                {{-- <input type="country" id="country" class="form-control" name="country" value="{{ $clinic->Country }}"> --}}
                <select name="country" id="country" class="form-control">
                    <option value="Singapore">Singapore</option>
                </select>
                @if ($errors->has('country')) <p class="help-block">{{ $errors->first('country') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('postal')) has-error @endif">
                <label for="postal">Postal*</label>
                <input type="postal" id="postal" class="form-control" name="postal" value="{{ $clinic->Postal }}">
                @if ($errors->has('postal')) <p class="help-block">{{ $errors->first('postal') }}</p> @endif
            </div>

         	<div class="form-group  @if ($errors->has('district')) has-error @endif">
                <label for="district">District</label>
                <input type="district" id="district" class="form-control" name="district" value="{{ $clinic->District }}">
                @if ($errors->has('district')) <p class="help-block">{{ $errors->first('district') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('latitude')) has-error @endif">
                <label for="latitude">Latitude*</label>
                <input type="latitude" id="latitude" class="form-control" name="latitude" value="{{ $clinic->Lat }}">
                @if ($errors->has('latitude')) <p class="help-block">{{ $errors->first('latitude') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('longitude')) has-error @endif">
                <label for="longitude">Longitude*</label>
                <input type="longitude" id="longitude" class="form-control" name="longitude" value="{{ $clinic->Lng }}">
                @if ($errors->has('longitude')) <p class="help-block">{{ $errors->first('longitude') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('phone')) has-error @endif">
                <label for="phone">Phone*</label>
                <input type="phone" id="phone" class="form-control" name="phone" value="{{ $clinic->Phone }}">
                @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
            </div>

         	<div class="form-group  @if ($errors->has('mrt')) has-error @endif">
                <label for="mrt">MRT</label>
                <input type="mrt" id="mrt" class="form-control" name="mrt" value="{{ $clinic->MRT }}">
                @if ($errors->has('mrt')) <p class="help-block">{{ $errors->first('mrt') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('opening')) has-error @endif">
                <label for="opening">Opening*</label>
                <input type="opening" id="opening" class="form-control" name="opening" value="{{ $clinic->Opening }}">
                @if ($errors->has('opening')) <p class="help-block">{{ $errors->first('opening') }}</p> @endif
            </div>
				
         	<div class="form-group  @if ($errors->has('insurance_company')) has-error @endif">
                <label for="insurance_company">Insurance Company*</label>
             	<?php $selected = $insuranceID; ?>                           	
                {{ Form::select('insurance_company[]', $company, $selected, array('multiple'=>'multiple','class' => 'form-control')) }}
                @if ($errors->has('insurance_company')) <p class="help-block">{{ $errors->first('insurance_company') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('email')) has-error @endif">
                <label for="email">Email*</label>
                <input type="email" id="email" class="form-control" name="email" value="<?php if (!(empty($user->Email))) { echo  $user->Email;} ?>">
                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('password')) has-error @endif">
                <label for="password">Password*</label>
                <input type="password" id="password" class="form-control" name="password">
                @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('status')) has-error @endif">
                <label for="status">Status</label>
                 <select id="status" name="status" class="form-control">
			      <option value="0" <?php if ($clinic->Active == 0) echo 'selected="selected"' ?>>Deactivate</option>
			      <option value="1" <?php if ($clinic->Active == 1) echo 'selected="selected"' ?>>Activate</option>
			    </select>
                @if ($errors->has('status')) <p class="help-block">{{ $errors->first('status') }}</p> @endif
            </div>
     

        {{ Form::submit('Update', array('class' => 'btn btn-sm btn-info')) }}
        {{ HTML::link('admin/clinic/all-clinics', 'Cancel','class="btn  btn-sm btn-info"')}}

        {{ Form::close() }}

    </div>
</div>
@include('admin.footer-admin') 