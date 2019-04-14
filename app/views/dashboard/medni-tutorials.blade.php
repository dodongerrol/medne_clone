@include('common.home_header')

<style type="text/css">
	.help-li .help-text{
		display: inline-block;
    border-bottom: 2px solid #fff;
	}

	.need-help-page-container,
	.need-help-page-container *{
		box-sizing: border-box !important;
	}

	.need-help-page-container{
    overflow: hidden;
    padding: 60px 50px;
	}

	.need-help-page-container .exp-box{
    width: 310px;
    text-align: left;
    display: inline-block;
    border: 1px solid #d2d2d2;
    border-radius: 10px;
    overflow: hidden;
    height: 475px;
    margin-top: 45px;
	}

	.need-help-page-container .exp-box .img-wrapper img{
    width: 100%;
	}

	.head-title{
		border-bottom: 2px solid #e9e9e9;
    padding-bottom: 20px;
    margin: 0;
    font-size: 18px;
	}

	.need-help-page-container .exp-box .box-content{
		padding: 25px 30px;
	}

	.need-help-page-container .exp-box .box-content .content-text{
		height: 125px;
	}

	.need-help-page-container .exp-box .box-content p{
		margin: 0;
		color: #999;
		font-size: 13px;
	}

	.need-help-page-container .exp-box .box-content p.exp-title{
		margin: 0;
		color: #000;
		font-size: 15px;
		margin-bottom: 15px;
	}

	.need-help-page-container .exp-box .box-content a{
		font-size: 13px;
		font-weight: 700;
	}

	.need-help-page-container .exp-box .box-content a i{
		margin-left: 10px;
	}

	.medni-video-container { 
		position: relative; 
		padding-bottom: 56.25%; 
		height: 0; 
		overflow: hidden; 
		max-width: 100%; 
		height: auto; 
	} 

	.medni-video-container iframe, 
	.medni-video-container object, 
	.medni-video-container embed { 
		position: absolute; 
		top: 0; 
		left: 0; 
		width: 100%; 
		height: 100%; 
	}
</style>


<div id="main" class="need-help-page-container" >
	<div class="col-md-10 col-md-offset-1">
		<p class="head-title">Mednefits Health Partner Experience</p>
	</div>

	<div class="col-md-8 col-md-offset-2" >
		<div class="col-md-6 text-center">
			
				<div class="exp-box" >
					<div class="img-wrapper">
						<img src="{{ URL::asset('assets/images/Step-by-Step-Guide.png') }}">
					</div>
					<div class="box-content">
						<div class="content-text">
							<p class="exp-title">Step by Step Guide</p>
							<p>Learn the process of how Mednefits member utilise your service. From verification, submit claim, to end of month reporting.</p>
						</div>
						<div class="white-space-20"></div>
						<a href="/pdf/Mednefits Tutorial for Health Partners (GP).pdf" target="_blank"><span>Download PDF for GP Clinic <i class="fa fa-chevron-right"></i></span></a>
						<p>&nbsp;</p>
						<a href="/pdf/Mednefits Tutorial for Health Partners (Dental).pdf" target="_blank"><span>Download PDF for Dental Clinic <i class="fa fa-chevron-right"></i></span></a>
					</div>
				</div>	
			
		</div>

		<div class="col-md-6 text-center">
			<a id="watch-video" href="javascript:void(0)">
				<div class="exp-box" >
					<div class="img-wrapper">
						<img src="{{ URL::asset('assets/images/Platform Demo Video.png') }}">
					</div>
					<div class="box-content">
						<div class="content-text">
							<p class="exp-title">Platform Demo Video</p>
							<p>Navigating Mednefits Health Partner platform is simple. See how.</p>
						</div>
						<div class="white-space-20"></div>
						<span>Watch now <i class="fa fa-chevron-right"></i></span>
					</div>
				</div>	
			</a>
		</div>
	</div>
</div>

<div id="video" class="need-help-page-container" hidden>
	<div class="col-md-10 col-md-offset-1">
		<p class="head-title" style="border-bottom: none !important">Mednefits Health Partner Experience <a id="back-video" href="javascript:void(0)" class="pull-right"><i class="fa fa-times" style="font-size: 20px;color: #000;"></i></a></p>
	</div>

	<div class="col-md-10 col-md-offset-1 text-center">
		<div class="medni-video-container">
			<iframe src="https://player.vimeo.com/video/251270919" 
				frameborder="0"
				webkitallowfullscreen
				mozallowfullscreen
				allowfullscreen></iframe>
		</div>	
	</div>
</div>


<script type="text/javascript">
	$( '#watch-video' ).click(function() {
		$( '#video' ).show();
		$( '#main' ).hide();
	});

	$( '#back-video' ).click(function() {
		$( '#video' ).hide();
		$( '#main' ).show();
	});

</script>
@include('common.footer')