
<br>

<div class="col-md-12" style="padding: 0px;">

	<div class="col-md-2">
		<span style="float: right;"><img alt="" src="{{ URL::asset('assets/images/ico_Profile.svg') }}" width="75" height="75"></span>
	</div>
	<div class="col-md-8" style="padding-top: 15px; padding-bottom: 15px; border-bottom: 2px solid #DEDEDE;">
		<div style="float: left; font-size: 25px;">Services You Provide :</div>
		<div style="float: right;"><button id="go-to-add-service" style="height: 30px; width: 100px; background: #1b9bd7; color: white; border: 1px solid #1b9bd7; border-radius: 3px;">Add Service</button></div>
	</div>

	<div class="row col-md-12">
		<div class="col-md-2" style="clear: both">
			<label class="detail-lbl">&nbsp;</label>
		</div>
		<div class="col-md-8" style="color: #1667AC">
			<div class="col-md-5" style="width: 55%;">

  				<?php if (count($services)==count($doctor_services)){?>
  				<div class="col-md-1" style="padding: 0px; width: 10%;">
					<input id="doctor-all-services" type="checkbox" checked>
				</div>
													  
				<div class="col-md-1" style="padding: 0px; width: 90%;">
					<label style="padding-top: 13px; color: #1667AC;"><b>Select All Services</b></label>
				</div>

  				<!-- <label style=""><input id="doctor-all-services" type="checkbox" checked ><b>Select All Services</b></label> -->
  				<?php } else {?>
  				<div class="col-md-1" style="padding: 0px; width: 10%;">
					<input id="doctor-all-services" type="checkbox">
				</div>
													  
				<div class="col-md-1" style="padding: 0px; width: 90%;">
					<label style="padding-top: 13px;"><b>Select All Services</b></label>
				</div>
  				<!-- <label><input id="doctor-all-services" type="checkbox" ><b>Select All Services</b></label> -->
  				<?php }?>

			</div>
			<div class="col-md-2" style="padding-top: 16px; width: 15%;">
        		
      		</div>
      		<div class="col-md-2" style="padding-top: 16px; width: 8%;">
        		
      		</div>
		</div>
	</div>

	

<?php if($services){

         $procedurecount =0;

				foreach ($services as $key => $value) {

					if($doctor_services){

                        foreach($doctor_services as $docvalue){

                                if($docvalue->ProcedureID==$value->ProcedureID && $procedurecount==0){ ?>
                                        
                                        <div class="row col-md-12">
											<div class="col-md-2" style="clear: both">
												<label class="detail-lbl">&nbsp;</label>
											</div>
											<div class="col-md-8" style="color: black;">
												<div class="col-md-5" style="width: 55%;">
									  				<!-- <label><input type="checkbox" value="{{ $value->ProcedureID}}" class="doctor-service-staff" checked >{{ $value->Name}}</label> -->

													<div class="col-md-1" style="padding: 0px; width:10%;">
														<input type="checkbox" value="{{ $value->ProcedureID}}" class="doctor-service-staff" checked="">
													</div>
													  
													<div class="col-md-1" style="padding: 0px; width: 90%;">
														<label style="padding-top: 13px;">{{ $value->Name}}</label>
													</div>


												</div>
												<div class="col-md-2" style="padding-top: 16px; width: 15%;">
									        		<span>{{ $value->Duration}} mins</span>
									      		</div>
									      		<div class="col-md-2" style="padding-top: 16px; width: 30%;">
									        		<span class="cost_val index<?php echo $key?>">{{ $value->Price}}</span>
									      		</div>
											</div>
										</div>
                                        
                    <?php 				$procedurecount=1;

                                }
                        }
                    }

                    if($procedurecount==0){ ?>

                    			<div class="row col-md-12">
									<div class="col-md-2" style="clear: both">
										<label class="detail-lbl">&nbsp;</label>
									</div>
									<div class="col-md-8" style="color: #999999">
										<div class="col-md-5" style="width: 55%;">
							  				<!-- <label><input  type="checkbox" value="{{ $value->ProcedureID}}" class="doctor-service-staff"/>{{ $value->Name}}</label> -->

							  				<div class="col-md-1" style="padding: 0px; width: 10%;">
												<input type="checkbox" value="{{ $value->ProcedureID}}" class="doctor-service-staff">
											</div>
													  
											<div class="col-md-1" style="padding: 0px; width: 90%;">
												<label style="padding-top: 13px;">{{ $value->Name}}</label>
											</div>

										</div>
										<div class="col-md-2" style="padding-top: 16px; width: 15%;">
							        		<span>{{ $value->Duration}} mins</span>
							      		</div>
							      		<div class="col-md-2" style="padding-top: 16px; width: 30%;">
							        		<span> {{ $value->Price}}</span>
							      		</div>
									</div>
								</div>
                              
            		<?php

        			}
                    else{
                              $procedurecount=0;
                    }

             } } ?>

</div>


<script type="text/javascript">

	// Currency condition
	// $('.sg').show();
	var currency;
	var arrLength = $('.cost_val').size();
	// console.log(currency,arrLength);
	
	for ( var i = 0; i <= arrLength - 1; i++ ) {
		var str = $('.index' + i).text();
		if (str.includes('S$')) {
			currency = $('.index' + i).text().replace('S$', 'SGD ');
			$('.index' + i ).text(currency);
		} else if (str.includes('RM')) {
			currency = $('.index' + i).text().replace('RM', 'MYR ');
			$('.index' + i ).text(currency);
		}
		
		console.log(currency,arrLength);
	}

	
	// --------- Set Navigation bar height ------------------

    var page_height = $('#detail-wrapper').height()+52;
    var win_height = $(window).height();

    // alert ('page - '+page_height+ ', window - '+win_height);

    if (page_height > win_height){

        $("#setting-navigation").height($('#detail-wrapper').height()+52);
        $(".staff-side-list").height($('#detail-wrapper').height()+52);
    }
    else{

        $("#setting-navigation").height($(window).height()-52);
        $(".staff-side-list").height($(window).height()-52);
    }

    $("#staff-doctor-list").height(($('.staff-side-list').height() / 2) -75);
	$("#staff-list").height(($('.staff-side-list').height() / 2) -75);
	
</script>