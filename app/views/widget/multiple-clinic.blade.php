<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
	<title> Multiple Clinic </title>
	<link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico">
	<!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
	{{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
	@if($name == 'only_group')
	<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','https://connect.facebook.net/en_US/fbevents.js');

		fbq('init', '300800066938054');
		fbq('track', 'OnlyGroup');
	</script>
		<noscript>
			<img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=300800066938054&ev=ViewContent&noscript=1"
		/>
	</noscript>
	@endif
	<style type="text/css">

			html,body{
					height: 100%;
					background: #eee;
			}

			.widget-body{
				width: 700px;
				overflow: hidden;
				margin: 30px auto;
				background: #FFF;
				box-shadow: 0 5px 15px rgba(0,0,0,.5);
			}

			.appointment{
					min-height: 100%;
					/*overflow: hidden;*/
					/*margin-bottom: 20px;*/

			}

			.appointment .logo{
					padding: 20px 0;
			}

			.appointment p {
					line-height: 1;
					font-size: 15px;
			}

			.appointment h4{
				 font-size: 15px;
			}

			.appointment label {
					margin-bottom: 0;
			}

			.appointment .btn {
					color: #FFF;
					font-weight: 700;
					font-size: 14px;
					border-radius: 10px;
					margin-right: 10px;
					margin-bottom: 15px;
					width: 175px;
			}

			.appointment .col-md-12{
					overflow: hidden;
			}

			.appointment .languages{
					font-size: 15px;
			}

			.appointment .languages a.active{
					font-weight: 600;
			}

			.appointment .languages a:hover{
					text-decoration: none;
					color: #000;
			}

			.row-footer{
					/*position: absolute;
					width: 100%;
					bottom: 0;*/
			}

			.appointment-footer{
					margin-top: 40px;
					padding: 15px 25px;
					background: #104158 ;
			}

			.btn-sky-blue{
					background: #73CDF1 ;
					padding: 6px 0px;
					/* padding-left: 0px; */
					padding-right: 4px;
			}
			.btn-sky-pink{
					background: #FC9DA1 ;
					padding: 6px 12px;
			}
			.border-bottom{
					border-bottom:2px solid #E5E5E5 ;
			}

			.img-15{
					width: 15%;
			}

			.img-20{
					width: 20%;
			}

			.img-30{
					width: 30%;
			}

			.no-padding{
					padding: 0 ;
			}

			.block-section-xs{
					padding: 5px 0;
			}

			.block-section-sm{
					padding: 15px 0;
			}

			.padding-side-15{
					padding-left: 15px;
					padding-right: 15px;
			}

			.color-black{
					color: #333;
			}

			.text-right{
					text-align: right;
			}

		 #map {
					width: 100%;
					height: 500px;
			}

			.btn-close{
				 position: relative;
					top: -5px;
					right: -5px;
					border-radius: 50%;
					background: #111 !important;
					color: #FFF;
					padding: 2px 6px !important;
			}

			@media only screen and (min-width: 900px){
					.appointment .btn{
							padding-top: 10px;
							padding-bottom: 10px;
					}
			}

			@media only screen and (min-width: 1257px){
					.appointment .btn{
							width: 260px;
					}

					.appointment p{
						 line-height: 1.6;
						letter-spacing: 2px;
					}
			}

			@media only screen and (max-width: 900px){
					.appointment .btn{
						width: 100%;
					}
			}

			@media only screen and (max-width: 780px){
					.widget-body{
						width: auto;
						margin: 10px;
					}
			}

			@media only screen and (max-width: 550px){
				 	.widget-body{
						width: auto;
						margin: 10px;
					}
					.appointment .btn{
							font-size: 8px;
							width: 100%;
							border-radius: 6px;
							margin-bottom: 10px;
					}

					.appointment p{
							font-size: 11px;
							margin-bottom: 8px;
					}

					.appointment h4{
						 font-size: 9px;
					}

					.appointment .languages{
							font-size: 9px;
					}
			}

			@media only screen and (max-width:350px){
				.appointment h4{
					 font-size: 8px;
				}

				.appointment .languages{
						font-size: 8px;
				}

				.appointment .btn{
						font-size: 7px;
				}

				.appointment p{
						font-size: 8px;
				}
			}
    </style>
</head>
<body>
	<div class="widget-body">

		<div class="container-fluid appointment">
			<div class="col-md-12 header block-section-xs padding-side-15 border-bottom">
				<img src="{{ asset('assets/images/') }}/{{ $image }}" class="img-responsive logo" style="width: 90px!important;">
        <h4>
  				<label>Make an Appointment via Mednefits</label>
  			</h4>
      </div>
			@if(sizeof($clinic) > 0)
				@foreach($clinic as $key)
				<div class="col-md-12 block-section-sm border-bottom">
					<div class="col-xs-6 col-sm-7 col-md-6 ">
						<p><label> {{ $key->Name }} - {{ $key->City }}</label></p>
						<p>{{ $key->Address }}</p>
						@if(sizeof($key->City) != 1)
							<p>{{ $key->City }}, {{ $key->Postal }}</p>
						@endif
					</div>
					<div class="col-xs-6 col-sm-5 col-md-6 text-right">
						<a href="https://medicloud.sg/app/widget/{{ $key->ClinicID}}" class="btn btn-sky-blue" style="background-color: #0392CF!important;"> 
						<img src="{{ asset('assets/images/mednefits logo v3 (white-box) LARGE.png') }}"  style="width: 11%;height: auto;margin-right: 5px;" />
						<span>Book via Mednefits</span> </a>
						<button class="btn btn-sky-pink" onClick="showMap({{ $key->Lat }}, {{ $key->Lng }})" data-toggle="modal" data-target="#directionModal"> Get Directions </button>

					</div>
				</div>
				@endforeach
			@endif
			@if(sizeof($clinic) == 0)
			<div class="col-md-12 block-section-sm border-bottom" style="text-align: center;">
				<div class="col-xs-6 col-sm-9 col-md-8">
					<h3>This Clinic does not have a branch right now.</d3>
				</div>
			</div>
			@endif


		</div>

		<div class="footer">
		<div class="col-md-12 appointment-footer" style="overflow: hidden;">
			<img src="{{asset('assets/images/Mednefits Logo V2.svg')}}" class="img-30">
		</div>
	</div>

	</div>
	<!-- Modal -->
	<div class="modal fade" id="directionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-body">
	        <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <div id="map"></div>
	      </div>
	    </div>
	  </div>
	</div>

</body>
	<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/3.1.0/jquery.min.js"></script> -->
	{{ HTML::script('assets/js/jquery-1.11.1.js') }}
	{{ HTML::script('assets/css/bootstrap/js/bootstrap.min.js') }}
	<!-- <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
	<script src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCUEHlCS0ge0Urb_WjZW8xRzunI3q2iAIE"></script>
	<script>
		function showMap(lat, long){
      console.log(lat);
      console.log(long);
			console.log("in");

        var myLatLng = {lat: lat, lng: long};

            setTimeout(function(){

                var mapDiv = document.getElementById('map');
                var map = new google.maps.Map(mapDiv, {
                    center: myLatLng,
                    zoom: 18
                });

 
                var marker = new google.maps.Marker({
                    position: myLatLng,
                    animation: google.maps.Animation.DROP,
                    map: map,
                    title: 'Hello World!'
                  });

            },300);
		}

    </script>
</html>
