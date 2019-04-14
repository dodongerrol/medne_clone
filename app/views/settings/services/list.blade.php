<script type="text/javascript" src="<?php echo $server; ?>/assets/settings/services/service.js?_={{ $date->format('U') }}"></script>



<link rel="stylesheet" href="<?php echo $server; ?>/assets/settings/services/service.css?_={{ $date->format('U') }}">

  <div class="col-md-12" style="padding: 0px; padding-left: 45px;">

  <div>
    <h4 style="float: left; padding-top: 10px; padding-bottom: 20px; font-size: large; font-weight: bold;">Here is the List of all Services</h4>
    <span style="float: right; margin-top: 22px;"><button class="btn" id="btn-service-add">Add Service</button></span>
  </div>
  <br><br><br>

  <div class="service-list" id="list-service">
<?php if($services){ foreach ($services as $value) { ?>
    <div class="col-md-12 service-details" id="{{$value->ProcedureID}}">
      <span class="service-edit">
        <div class="col-xs-5 col-sm-5 col-md-5 service-details-info" style="padding: 20px 0px 20px 20px;">
          <img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="50" height="50" style="float: left;">
          <div style="display: inline-block;padding: 5px 30px;">
            <div><b>{{ $value->Name}}</b></div>
            <div style="color: #999999;">{{ $value->Description}}</div>
          </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 service-details-info" style="padding: 20px 0;">
          <span style="color: #999999;">{{ $value->Duration}} mins</span>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 service-details-info" style="padding: 20px 0;">
          <span style="color: #999999;">{{ $value->Price}}</span>
        </div>
      </span>
      <div class="col-xs-3 col-sm-3 col-md-3" style="padding: 20px 0;padding-right: 15px;text-align: right;">
        <p style="color: #999999;">Scan & Pay Page</p>
        <label class="switch" id="switch_trigger_{{ $value->ProcedureID }}">
          <input type="checkbox" {{ $value->scan_pay_show == 1 ? "checked" : "" }}>
          <span class="slider">
            <span class="off">Hide</span>
            <span class="on">Show</span>
          </span>
        </label>
      </div>
    </div>
<?php }} ?>

    <div class="col-md-12" style="border-top: 1px solid #C1C1C1;padding: 1px;"></div>

  </div>

  </div>

<style type="text/css">
  .switch {
    position: relative;
    display: inline-block;
    width: 68px;
    height: 34px;
    margin-right: 37px;
    text-align: left;
    margin-top: 10px;
    overflow: hidden;
    line-height: 34px;
    border-radius: 4px;
  }

  .switch input {display:none;}

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0px;
    right: 0;
    bottom: 0;
    background-color: #ED342B;
    -webkit-transition: .4s;
    transition: .4s;
    border-radius: 6px;
    width: 130px;
    color: #FFF;
  }

  .slider span{
    display: inline-block;
    width: 60px;
    text-align: center;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 34px;
    width: 8px;
    border-radius: 4px;
    left: 60px;
    bottom: 0;
    background-color: #FFB6B6;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked + .slider {
    background-color: #D3D3D3;
    color: #333;
    left: -60px;
  }

  input:focus + .slider {
    box-shadow: 0 0 1px #D3D3D3;
    color: #333;
    left: -60px;
  }

</style>

  <script type="text/javascript">


    // --------- Set Navigation bar height ------------------

    var page_height = $('#setting-nav-panel').height()+52;
    var win_height = $(window).height();

    // alert ('page - '+page_height+ ', window - '+win_height);

    if (page_height > win_height){

        $("#setting-navigation").height($('#setting-nav-panel').height()+52);
        // $("#profile-side-list").height($('#setting-nav-panel').height());
    }
    else{

        $("#setting-navigation").height($(window).height()-52);
        // $("#profile-side-list").height($(window).height()-52);
    }

    $('#list-service').sortable();
    $("#list-service").sortable({
        stop: function(event, ui) {
            var data = "";
            var temp = [];
            $("#list-service .service-details").each(function(i, el){
                console.log(el);
                temp.push({ id: $(el).attr("id"), position: i });
            });

            console.log(temp);
            $.ajax({
              url: base_url + 'setting/service/saveServicePosition', //<- this line is edited
              method: 'POST',
              data: {mydata: temp},
              dataType:"json",
              success: function( data )
              {
                  console.log(data);
                  // do nothing
                  // alert(data);
              }
          });
        }
    });
  </script>
{{ HTML::script('assets/js/jquery-sortable-min.js') }}
