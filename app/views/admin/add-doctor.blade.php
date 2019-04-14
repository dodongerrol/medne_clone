@include('admin.header-admin')  
{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
{{ HTML::style('assets/css/bootstrap/css/bootstrap-select.css') }}
{{ HTML::style('assets/css/dataTables.bootstrap.css') }}
@include('admin.header')
<div class="row" style="margin-top: 20px;">
    <div class="col-sm-8 col-sm-offset-2">
        
        <div class="page-header">            
            <h1 style="font-size: 100% !important;"><span class="label label-default">Add Doctor</span></h1>
        </div>  

        @if ($errors->has())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $message)
                {{ $message }}<br>        
            @endforeach
        </div>
        @endif  

        <!-- FORM STARTS HERE -->
        {{ Form::open(array('url' => 'admin/clinic/insert-doctor', 'method' => 'post','files'=> true)) }}

            <div class="form-group  @if ($errors->has('clinic_name')) has-error @endif">
                <label for="clinic_name">Clinic</label>
                {{ Form::select('clinic_name', $allClinics, null, array('','class' => 'selectpicker show-tick form-control','data-live-search'=>"true")) }}
                @if ($errors->has('clinic_name')) <p class="help-block">{{ $errors->first('clinic_name') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                <label for="name">Name*</label>
                <input type="text" id="name" class="form-control" name="name">
                @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
            </div>         
    
            <div class="form-group @if ($errors->has('file')) has-error @endif">
                <label for="file">Image</label>
                <input type="file" name="file" class="form-control"> 
                @if ($errors->has('file')) <p class="help-block">{{ $errors->first('file') }}</p> @endif
            </div>
            
            <div class="form-group  @if ($errors->has('qualifications')) has-error @endif">
                <label for="qualifications">Qualifications</label>
                <input type="text" id="qualifications" class="form-control" name="qualifications">
                @if ($errors->has('qualifications')) <p class="help-block">{{ $errors->first('qualifications') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('specialty')) has-error @endif">
                <label for="specialty">Specialty</label>
                <input type="text" id="specialty" class="form-control" name="specialty">
                @if ($errors->has('specialty')) <p class="help-block">{{ $errors->first('specialty') }}</p> @endif
            </div>            

            <div class="form-group  @if ($errors->has('emergency')) has-error @endif">
                <label for="emergency">Emergency</label>
                <input type="text" id="emergency" class="form-control" name="emergency">
                @if ($errors->has('emergency')) <p class="help-block">{{ $errors->first('emergency') }}</p> @endif
            </div>

            <div class="form-group  @if ($errors->has('phone')) has-error @endif">
                <label for="phone">Phone</label>
                <input type="text" id="phone" class="form-control" name="phone">
                @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
            </div>              	
            

            <div class="form-group  @if ($errors->has('email')) has-error @endif">
                <label for="email">Email*</label>
                <input type="email" id="email" class="form-control" name="email">
                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
            </div>
           

  	       <button type="submit" class="btn btn-info  btn-sm">Save</button>
  	       {{-- <button type="reset" class="btn btn-info"></button> --}}
           {{ HTML::link('admin/clinic/all-doctors', 'Cancel','class="btn btn-info btn-sm"')}}

        {{ Form:: close() }}

    </div>
</div>
@include('admin.footer-admin')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.5/js/bootstrap-select.js"></script>