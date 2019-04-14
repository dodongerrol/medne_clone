@include('admin.header-admin')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.6/integration/bootstrap/3/dataTables.bootstrap.css">
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        
        <div class="page-header">
            <h1><span class="glyphicon glyphicon-plus"></span>Edit {{ $doctor->Name }}</h1>
        </div>  

        @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $message)
                {{ $message }}<br>        
            @endforeach
        </div>
        @endif  

        <!-- FORM STARTS HERE -->
         {{ Form:: open(array('url' => 'admin/clinic/doctor/'.$doctor->DoctorID.'/update','files'=> true)) }}

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                <label for="name">Name*</label>
                <input type="text" id="name" class="form-control" name="name" value="{{ $doctor->Name }}">
                <input type="hidden" id="doctorid" class="form-control" name="doctorid" value="{{ $doctor->DoctorID }}">
                {{-- <input type="hidden" id="userid" class="form-control" name="userid" value="{{ $user->UserID }}"> --}}
                @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
            </div>                     
            
            <div class="form-group @if ($errors->has('file')) has-error @endif">
                {{ HTML::image($doctor->image, $doctor->Name, array( 'width' => 50, 'height' => 50 )) }}
                <label for="file">Image</label>
                <input type="file" name="file" class="form-control"> 
                @if ($errors->has('file')) <p class="help-block">{{ $errors->first('file') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('qualifications')) has-error @endif">
                <label for="qualifications">Qualifications*</label>
                <input type="qualifications" id="qualifications" class="form-control" name="qualifications" value="{{ $doctor->Qualifications }}">
                @if ($errors->has('qualifications')) <p class="help-block">{{ $errors->first('qualifications') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('specialty')) has-error @endif">
                <label for="specialty">Specialty*</label>
                <input type="specialty" id="specialty" class="form-control" name="specialty" value="{{ $doctor->Specialty }}">
                @if ($errors->has('specialty')) <p class="help-block">{{ $errors->first('specialty') }}</p> @endif
            </div>            

            <div class="form-group  @if ($errors->has('emergency')) has-error @endif">
                <label for="emergency">Emergency</label>
                <input type="emergency" id="emergency" class="form-control" name="emergency" value="{{ $doctor->Emergency }}">
                @if ($errors->has('emergency')) <p class="help-block">{{ $errors->first('emergency') }}</p> @endif
            </div>           

            <div class="form-group  @if ($errors->has('phone')) has-error @endif">
                <label for="phone">Phone*</label>
                <input type="phone" id="phone" class="form-control" name="phone" value="{{ $doctor->Phone }}">
                @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('email')) has-error @endif">
                <label for="email">Email</label>
                <input type="email" id="email" class="form-control" name="email" value="{{ $doctor->Email }}">
                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
            </div>           
            
            <div class="form-group  @if ($errors->has('password')) has-error @endif">
                <label for="password">Password</label>
                <input type="password" id="password" class="form-control" name="password">
                @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
            </div>    

            <div class="form-group  @if ($errors->has('status')) has-error @endif">
                <label for="status">Status</label>
                 <select id="status" name="status" class="form-control">
                  <option value="0" <?php if ($doctor->Active == 0) echo 'selected="selected"' ?>>Deactivate</option>
                  <option value="1" <?php if ($doctor->Active == 1) echo 'selected="selected"' ?>>Activate</option>
                </select>
                @if ($errors->has('status')) <p class="help-block">{{ $errors->first('status') }}</p> @endif
            </div>

           
     

        {{ Form::submit('Update', array('class' => 'btn btn-sm btn-info')) }}
        {{ HTML::link('admin/clinic/all-doctors', 'Cancel','class="btn  btn-sm btn-info"')}}

        {{ Form::close() }}

    </div>
</div>
@include('admin.footer-admin') 