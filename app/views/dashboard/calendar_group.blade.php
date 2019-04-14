<style type="text/css">
	.fc-today
	{
		background: transparent!important;
	}
</style>
<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/group_calendar.js?_={{ $date->format('U') }}"></script>
<script type="text/javascript" src="<?php echo $server; ?>/assets/dashboard/country_code.js?_={{ $date->format('U') }}"></script>
<input type="hidden" id="clinicID" value="{{$clincID}}">
<div id="calender_header">
	<div class="header-list">
		<ul class="nav navbar-nav">
		
	    <li>
	        <div class="dropdown" style="margin-top: 2px;">
	        <span>&nbsp;</span>
	          <span  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="calender-selection">By Group</span><span class="caret"></span>
	          <ul class="dropdown-menu " id="calendar-view-option">
	            <li><a href="#" id="w">Weekly</a></li>
	            <li><a href="#" id="d">Daily</a></li>
	            <li><a href="#" id="m">Monthly</a></li>
	            <li><a href="#" id="g">By Group</a></li>
	          </ul>
	        </div>
	    </li>    
	    </ul> 
	</div>

	<div class="header-dates">
		<div id="mini-calendar-view" style="z-index: 2; position: fixed;">
  			<button type="button" class="btn btn-default" id="btn-today">Today</button>
			<div id="datepicker-button" class="btn-group" role="group" aria-label="...">
	  			<button type="button" class="btn btn-default" id="btn-left"><img src="{{ URL::asset('assets/images/ico_left arrow.svg') }}" alt="">
	  			<button type="button" class="btn btn-default" id="btn-title"></button>
	  			<button type="button" class="btn btn-default" id="btn-right"><img src="{{ URL::asset('assets/images/ico_right arrow.svg') }}" alt=""></button>
			</div>
			<div id="dp" style="display: none;"></div>
		</div>
	</div>

	<div class="legend" style="position: absolute;margin: 16px;margin-left: 69%;margin-right: 0;">
		<div class="blue" style="display: inline; margin-right: 30px">
			<i class="fa fa-circle" style="color:#33b5e5;background: #C5EDFF;width: 15px;text-align: center;"></i>&nbsp; Clinic
		</div>
		<div class="green" style="display: inline; margin-right: 30px">
			<i class="fa fa-circle" style="color:#00C851;background: #CBFFC5;width: 15px;text-align: center;"></i>&nbsp; Widget
		</div>
		<div class="yellow" style="display: inline; margin-right: 30px"	>
			<i class="fa fa-circle" style="color:#ffbb33;background: #FEF6C5;width: 15px;text-align: center;"></i>&nbsp; Mednefits App
		</div>
	</div>

	<a href="" data-toggle="modal" data-target="#info-modal" class="pull-right" style="margin: 15px 35px 15px 0;"> <img src="{{ URL::asset('assets/images/info.png') }}"> </a>

	<div class="header_tool pull-right" style="margin: 15px 5px;">
		<!-- <a href="" title="datepicker-button"><img src="{{ URL::asset('assets/images/ico_add.svg') }}" alt="" width="20px" height="20px"></a>
		<a href="" title=""><img src="{{ URL::asset('assets/images/ico_Notification.svg') }}" alt=""></a> -->
		<!-- <a href="javascript:void(0)" title=""><img src="{{ URL::asset('assets/images/ico_Settings.svg') }}" alt=""></a> -->

		<div class="dropdown" style="margin-top: 2px;">
        <span>&nbsp;</span>
          <span class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="{{ URL::asset('assets/images/ico_Settings.svg') }}"></span>
          <!-- <span class="caret"></span> -->
          <ul class="dropdown-menu " style="" id="provider-option">
          	<li class="dropdown-header">Providers</li>
            <li><a href="javascript:void(0)" > <input type="checkbox" name="viewOption" id="viewByDrop" checked="checked"> View as Dropdown</a></li>
            <li><a href="javascript:void(0)" > <input type="checkbox" name="viewOption" id="viewByTab"> View as Tabs</a></li>
            <li role="separator" class="divider"></li>
          </ul>
        </div>

        
	</div>
</div>

<div id="calendar"></div>


<!-- ..................modal pop up...... -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel" style="padding-left: 35px;">Appointment</h4>
		</div>

		<div class="modal-body" style="font-size: 12px;">	

			<ul class="nav nav-tabs" id="tabs" style="padding-bottom: 5px;">
				<li class="active enabledTab"><a data-toggle="tab" href="#booking">BOOKING</a></li>
				<li id="patient-tab" class="disabledTab"><a data-toggle="tab" href="#patient">PATIENT</a></li>
			</ul>

			<!-- Details tab contents -->

			<div class="tab-content">
			    <div id="booking" class="tab-pane fade in active">

			      	<div class="panel panel-default" style="margin: 0px;">
			            <div class="panel-body">

						  	<!-- Doctor list dropdown -->

						  	<div class="form-group">
							    <label class="col-sm-2 details-label" style="padding: 0px; padding-left: 33px;">Doctor</label>
							    <div class="col-sm-8">
							    	<label class="dropdown-btn input-width" style="height: 38px; cursor: pointer;"><i class="glyphicon glyphicon-ok" style="color: #1b9bd7"></i>&nbsp;&nbsp;<span class="doctor-selection" id="">Doctor Name</span><div class="ext-right"></div></label>
							    		<ul class="dropdown-menu dropdown-btn ul-width" id="appointment-doctor-list">
									        <li><a href="#" id="">
									        <span><img src="{{ URL::asset('assets/images/ico_Profile.svg') }}" alt="">&nbsp;&nbsp;</span></a></li>
										</ul>
							    </div>
						    </div>
						    <br><br><br>

						    <!-- slot blocker service list dropdown -->
						    <div class="form-group" id="abc">
							    <label class="col-sm-2 details-label" style="padding: 0px; padding-left: 33px;">Service</label>

							    <input type="hidden" name="" value="" id="h-duration">
							    <div class="col-sm-3">
							    	<label class="dropdown-btn slot-blocker-width" data-toggle="dropdown" id="service-lbl" style="height: 38px; cursor: pointer;">
							    	<i id="ok-icon" class="glyphicon" style="color: #1b9bd7"></i>&nbsp;&nbsp;<span class="service-selection" id="">Select a service</span><div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div></label>
							    		<ul class="dropdown-menu ul-width" id="service-list" style="width: 392px; max-height: 258px; overflow-y: auto; overflow-x: hidden;">

									        <!-- <li><a href="#">
									        <span class="service" id="0">Slot Blocker</span> 
									        <span class="ext-right">Custom</span></a>
									        </li> -->

									        <li id="" style="padding: 5px 15px 5px 15px; color: #555555;">
												<span class="service" id="0">Slot Blocker</span>
												<span class="pull-right">Custom</span>
											</li>

											<li id="reserve" style="padding: 5px 15px 5px 15px; color: #555555;" >
												<span>Apppointment wihout SMS Notification</span>
												<span class="pull-right">Custom</span>
											</li>

									        <li class="divider"></li>
										</ul>
							    </div>

							    <div id="slot-blocker-service" hidden>
							    	<label class="col-sm-1 details-label">Duration</label>
							    	<input type="text" id="block-time-Duration" class="dropdown-btn col-sm-1" style="height: 15px;">
							    	<!-- <label class="col-sm-1 details-label" style="padding-left: 10px">Mins</label> -->
							    	<div class="col-sm-1" style="padding: 0px; padding-left: 9px;">
							    		<label class="dropdown-btn" data-toggle="dropdown" id="" style="height: 38px; cursor: pointer; width: 33px;"><span class="blocker-time-format" id="min">Mins</span></label>
							    		<ul class="dropdown-menu" id="select-blocker-time-Format" style="min-width: 85px;">
									        <li><a href="#" id="mins">Mins</a></li>
									        <li><a href="#" id="hours">Hours</a></li>
										</ul>
							    	</div>
							    </div>

						    </div>
						    <br><br><br>

						    <!-- Cost & Time duration -->

						    <div id="Cost-Time-duration" hidden>
							    <div class="form-group">
								    <!-- <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Price</label>

								    <div class="col-sm-2" >
								     	<input type="text" id="service-price" class="dropdown-btn" style="height: 35px; width: 90px;">
								    </div> -->
								    <label class="col-sm-1 details-label" style="margin-left: 17px;">Duration</label>
								    <input type="text" id="service-time-Duration" class="dropdown-btn col-sm-1" style="height: 15px; width: 55px; margin-left: 48px;">
								    <div class="col-sm-2" style="padding: 0px; padding-left: 9px;">
								    	<label class="dropdown-btn" data-toggle="dropdown" id="" style="height: 38px; cursor: pointer;"><span class="time-format" id="min">Mins</span><div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div></label>
								    		<ul class="dropdown-menu" id="select-time-Format">
										        <li><a href="#" id="mins">Mins</a></li>
										        <li><a href="#" id="hours">Hours</a></li>
											</ul>
								    </div>
							    </div>
							    <br><br><br>
						    </div>

						    <!-- Date & Time pickers -->

						    <div class="form-group">
							    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Day</label>

							    <div class="col-sm-4">
							    	<input type="text" id="appointment-date" class="dropdown-btn" style="height: 30px; width: 180px; cursor: pointer;">
							    </div>
							    <label class="col-sm-1 details-label" style="padding-right: 0px;">Time</label>
							    <div class="col-sm-2" style="padding: 0px; padding-left: 5px;">
									<div class="right-inner-addon">
									    <i class="glyphicon glyphicon-chevron-down" style="padding: 10px 0px;"></i>
									    <input type="search" class="dropdown-btn" id="appointment-time" style="width: 65px; height: 35px; cursor: pointer;" />
									</div>
							    </div>
						    </div>
						    <br><br><br>


						    <!-- Notes -->

						    <div class="form-group">
							    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Notes</label>

							    <div class="col-sm-8">
							    	<input type="text" id="notes-group" class="dropdown-btn input-width" placeholder="Notes / Instructions" style="height: 27px;">
							    </div>
						    </div>

						    <div class="form-group" style="display: none" id="error_div1">
							    <label class="details-label col-sm-2"></label>
							    <div class="col-sm-8 alert alert-danger" id="error1" style="margin: 0px; margin-top: 12px;">
							    </div>
						    </div>

						    <div style="clear: both;">
						    </div>
						    <br><br>

						    <div class="form-group">
							    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">&nbsp;</label>

							    <div class="col-sm-8">
							    	<button type="button" class="btn btn-update font-type-Montserrat col-sm-3" id="continue">Continue</button>
							    	<button type="button" class=" hide btn btn-update font-type-Montserrat col-sm-3" id="blocker">Save Blocker</button>
							    </div>
						    </div>
						    <br>

	            		</div>
	          		</div>
	    		</div>


			    <!-- Patient tab contents -->

			    <div id="patient" class="tab-pane fade">
		    
				    <div class="panel panel-default" style="margin: 0px;">
				    	<div class="panel-body" style="text-align: center; padding-bottom: 30px;">
					    	<br>
					    	<!-- search text box-->
					    	<div id="search-panel" >
							    <div class="col-xs-10" style="padding-left: 30px">
								    <div class="right-inner-addon">
								        <i class="glyphicon glyphicon-search"></i>
								        <input type="search" class="dropdown-btn" id="search-customer" placeholder="Search By NRIC or Phone Number" />
								    </div>
								</div>
								<br><br><br>
								<hr><br>

								<div class="col-xs-10" style="padding-left: 205px;">
								<button type="button" id="add-new-customer" class="btn btn-update font-type-Montserrat" style="height: 26px;"><i class="glyphicon glyphicon-plus" style="font-size: 11px;"></i> New Patient</button>
								</div>
								<br><br>
							</div>

							<div id="new-customer">
								<div >
									<br>
									<div class="ext-right" style="padding-right: 15px;"><span id="close"><i class="glyphicon glyphicon-remove" style="color: #999999;"></i></span>
									</div>
									<br>

									<!-- Customer Name -->
									<div class="form-group">
									    <label class="details-label col-sm-2"><span><img src="{{ URL::asset('assets/images/ico_Profile.svg') }}" alt="" width="50" height="50"></span></label>

									    <div class="col-sm-8">
									    	<input type="text" id="customer-name" class="dropdown-btn input-width" placeholder="Name" style="height: 27px;">
									    </div>
								    </div>
								    <br><br><br>

									<!-- NRIC -->
								    <div class="form-group">
									    <label class="details-label col-sm-2">&nbsp;</label>

									    <div class="col-sm-8">
									    	<div class="right-inner-addon">
									        	<i class="glyphicon glyphicon-ok" id="nric-valid-icon"></i>
							  					<input type="text" id="customer-nric" class="dropdown-btn input-width" placeholder="NRIC / Fin / Passport" style="height: 27px; width: 335px;">
									    	</div>
									    </div>
								    </div><br><br><br>

									<!-- Telephone -->
									<div class="form-group">
									    <label class="details-label col-sm-2">&nbsp;</label>
									    <div class="col-md-8">
							  				<div id="mobile-dropdown" class="btn-group" style="border: 1px solid #d9d9d9; border-radius: 5px; display: block; width: 375px;">
							    				<button type="button" id="phone-code" class="btn dropdown-toggle" data-toggle="dropdown" style="height: 28px; font-size: 12px; color: #686868; background: #F4F4F4; border-right: 1px solid #d9d9d9; width: 35px; text-align: left;">+65</button>
							    				<input type="text" id="phone-no" class="dropdown-btn " value="" placeholder="Mobile Number" style="height: 28px; width: 293px; font-size: 12px; border: 0px;">
							    				<ul class="dropdown-menu" id="phone-code-list" style="width: 375px; max-height: 150px; overflow-y: auto; overflow-x: hidden;">

												</ul>
							  				</div>
										</div>
								    </div>
								    <br><br><br>

									<!-- Email -->
									<div class="form-group">
									    <label class="details-label col-sm-2">&nbsp;</label>

									    <div class="col-sm-8">
									    	<input type="text" id="customer-email" class="dropdown-btn input-width" placeholder="Email" style="height: 27px;">
									    </div>
								    </div>
								    <br><br><br>

									<!-- Address -->
									<div class="form-group">
									    <label class="details-label col-sm-2">&nbsp;</label>

									    <div class="col-sm-8">
									    	<input type="text" id="customer-address" class="dropdown-btn input-width" placeholder="Address" style="height: 27px;">
									    </div>
								    </div>
								    <br><br><br>

									<!-- City / State / Zip -->
									<div class="form-group">
								    	<label class="details-label col-sm-2">&nbsp;</label>

									    <div class="col-sm-3">
									    	<input type="text" id="city-name" class="dropdown-btn" placeholder="City" style="height: 32px; width: 130px;">
									    </div>
									    <div class="col-sm-2">
									    	<input type="text" id="state-name" class="dropdown-btn" placeholder="State" style="height: 33px; width: 90px;">
									    </div>
									    <div class="col-sm-2">
									    	<input type="text" id="zip-code" class="dropdown-btn" placeholder="Zip" style="height: 33px; width: 60px;">
									    </div>
								    </div><br><br>
								    <div style="clear: both"></div>

								    <div class="form-group" style="display: none" id="error_div2">
									    <label class="details-label col-sm-2"></label>
									    <div class="col-sm-8 alert alert-danger text-left" id="error2" style="margin-top: 12px">
											
									    </div>
								    </div>

								    <div style="clear: both"></div>
							    </div>
							    <br><br>

							    <div class="form-group">
								    <label class="details-label col-sm-2">&nbsp;</label>
								    <div class="col-sm-8"><button type="button" id="next-appointment" class="btn btn-update font-type-Montserrat ext-left">Next</button></div>
								    <div class="col-sm-8"><button type="button" id="save-appointment-group" class="btn btn-update font-type-Montserrat ext-left" hidden>Save Appointment</button></div>
								    <div class="col-sm-8"><button type="button" id="update-appointment-group" class="btn btn-update font-type-Montserrat ext-left hide">Update Appointment</button></div>
							    </div>
							    <br>
							</div>

							<div id="check-save" class="hide" style="width: 100%;overflow: hidden;text-align: left;">
								<h4 class="text-center" style="font-size: 20px;border-bottom: 1px solid #BBB;padding-bottom: 20px;margin-bottom: 30px;">Confirm Appointment :</h4>
								<div class="form-group" style="margin-left: 40px;">
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">Doctor</label><br>
										<span id="doctor-confirm"></span>
									</div>
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">NRIC</label><br>
										<span id="nric-confirm"></span>
									</div>
								</div>

								<div class="form-group" style="margin-left: 40px;">
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">Procedure</label><br>
										<span id="procedure-confirm"></span>
									</div>
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">Name</label><br>
										<span id="name-confirm"></span>
									</div>
								</div>

								<div class="form-group" style="margin-left: 40px;">
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">Date & Time</label><br>
										<span id="date-confirm"></span><br>
										<span id="time-confirm"></span>
									</div>
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">Email & Phone</label><br>
										<span id="email-confirm"></span><br>
										<span id="phone-confirm"></span>
									</div>
								</div>

								<div class="form-group" style="margin-left: 40px;">
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">Notes</label><br>
										<span id="notes-confirm"></span>
									</div>
									<div class="col-md-5" style="height: 70px;margin-bottom: 20px;">
										<label style="font-size: 14px;font-weight: 700 !important;color: #999;">Price</label><br>
										<span id="price-confirm"></span>
									</div>
								</div>
								<div class="col-sm-12" style="margin-top: 0px;text-align: center;width: 95%;">
									<button id="back-appointment" class="btn btn-update font-type-Montserrat" style="float: none;">Back</button>
									<button type="button" id="save-appointment-group" class="btn btn-update font-type-Montserrat save-btn" style="float: none;">Save Appointment</button>
								</div>
							</div>
				    	</div>
				    </div>
			    </div>


	  		</div>

      	</div>

    </div>
  </div>
</div>



<!-- ......................... dialog box for view appointment detais ............................. -->

<div id="dialog" title="Appointment Details" style="padding: 0px; display: none;">
	<div style="overflow: hidden;">
		<input id="h-appointment-id" type="hidden" value="">
		<input id="h-doctor-id" type="hidden" value="">
		<input id="h-procedure-id" type="hidden" value="">
		<input id="h-procedure-duration" type="hidden" value="">
		<input id="h-procedure-price" type="hidden" value="">
		<input id="h-cus-city" type="hidden" value="">
		<input id="h-cus-zip" type="hidden" value="">
		<input id="h-cus-state" type="hidden" value="">
		<input id="h-cus-address" type="hidden" value="">
		<input id="h-app-time" type="hidden" value="">
		<input id="h-cus-phone-code" type="hidden" value="">

		<div class="panel panel-default appointment-details">
		  	<div class="panel-body col-sm-11" style="border-bottom: 2px solid #DDDDDD; background: white !important; border-left: 0px !important; border-right: 0px !important;">
			  	<div style="margin-bottom: 70px; font-size: 15px; color: #332F2F;">
			    	<span class="col-sm-11" style="font-size: 14px;"><b id="appointment-date-lbl"></b></span>
			    	<span class="col-sm-11" id="appointment-time-lbl" style="font-size: 13px;"></span>
			    </div>

			    <input type="hidden" id="appointment-id">

			    <table class="appointment-det-container" style="font-size: 13px;">
					<tr>
					    <td class="col-sm-2">Doctor</td>
					    <td class="col-sm-8" id="appointment-doctor-detail"></td>
					</tr>
					<tr>
					    <td class="col-sm-2">Service</td>
					    <td class="col-sm-8" id="appointment-service-detail"></td>
					</tr>
					<tr>
					    <td class="col-sm-2">Cost</td>
					    <td class="col-sm-8" id="appointment-cost-detail"></td>
					</tr>
					<tr>
					    <td class="col-sm-2">Customer</td>
					    <td class="col-sm-8" id="appointment-customer-detail"></td>
					</tr>
					<tr>
					    <td class="col-sm-2">NRIC</td>
					    <td class="col-sm-8" id="appointment-nric-detail"></td>
					</tr>
					<tr>
					    <td class="col-sm-2">Email</td>
					    <td class="col-sm-8" id="appointment-email-detail"></td>
					</tr>
					<tr>
					    <td class="col-sm-2">Phone</td>
					    <td class="col-sm-8" id="appointment-phone-detail"></td>
					</tr>

					<tr id="Appoit-note">
					</tr>

				</table>
		  	</div>
	  	</div>
	  	<div class="appointment-details" style="padding-top: 10px; padding-bottom: 10px; text-align: center; background: white !important; clear: both;">
	    	<button type="button" id="edit-appointment-details-group" class="edit-appointment appt-details-btn hide-buttons" style="font-size: small;">Edit Details </button>
	    	<!-- <button type="button" id="concluded-appointment-details" class="appt-details-btn hide-buttons" style="font-size: small;">Concluded</button> -->
	    	<button type="button" id="concluded-appointment-group" class="appt-details-btn hide-buttons" style="font-size: small;">Claim</button>

	    	<button type="button" id="no-show-appointment-details" class="appt-details-btn hide-buttons" style="width: 70px; font-size: small;">No Show</button>
	    	<button type="button" id="delete-appointment-details" class="appt-details-btn" style="font-size: small; color: #333; border: 1px solid #BFB6B6; background: #f5f5f5; width: 60px;">Delete</button>
	        
	    </div>

	    <div class="balance" hidden>
	    	<div class="col-md-10">
	    		<p style="font-size: 10px;color: #555555;">* Upon finishing the billing will conclude the appointment.</p>
		    	<!-- <h5 style="color: #555;font-weight: 700">Medi Credit: <span id="user-credit-balance"></span></h5> -->
		    </div>

		    <div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

		    <div class="col-md-5">
		    	<input type="number" class="form-control" name="bill_amount" id="bill_amount" placeholder="Please enter Total Bill" style="font-size: 12px;height: 30px !important;">
		    </div>
		    <div class="col-md-4" style="margin-bottom: 20px;padding-left: 0;width: 44.333333%;">
		    	<button id="calc-bill" class="btn btn-primary" style="height: 29px;font-size: 12px;padding: 0px 12px;position: relative;top: -1px;">Calculate Bill</button>
		    	<button id="" class="btn btn-warning cancel1 calc-cancel" style="height: 29px;font-size: 12px;padding: 0px 12px;background-color: #f0ad4e;border-color: #eea236;">Cancel</button>
		    </div> 

	    </div>

	    <div class="balance-co-paid-group" hidden>
	    	<div class="col-md-10">
	    		<p style="font-size: 10px;color: #555555;">* Upon finishing the billing will conclude the appointment.</p>
		    	<!-- <h5 style="color: #555;font-weight: 700">Medi Credit: <span id="user-credit-balance"></span></h5> -->
		    </div>

		    <div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

		    <div class="col-md-5">
		    	<input type="number" class="form-control" name="bill_amount_co_paid" id="bill_amount_co_paid-group" placeholder="Total Medication Bill" style="font-size: 12px;height: 30px !important;">
		    </div>
		    <div class="col-md-4" style="margin-bottom: 20px;padding-left: 0;width: 44.333333%;">
		    	<button id="calc-bill-co-paid-group" class="btn btn-primary" style="height: 29px;font-size: 12px;padding: 0px 12px;position: relative;top: -1px;">Calculate Bill</button>
		    	<button id="" class="btn btn-warning cancel1 calc-cancel" style="height: 29px;font-size: 12px;padding: 0px 12px;background-color: #f0ad4e;border-color: #eea236;">Cancel</button>
		    </div> 

	    </div>

	    <div class="summary-receipt" style="margin-left: 20px;" hidden>
	    	<div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

	    	<div class="col-md-10">
		    	<h5 style="color: #555;font-weight: 700">Receipt Summary</h5>
		    </div>

		    <div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

		    <div class="col-md-11">
		    	<h5 style="color: #555;"><label>Name :</label>       	  <span id="client_name"></span></h5>
		    	<h5 style="color: #555;"><label>NRIC :</label> 		      <span id="nric"></span></h5>
		    	<h5 style="color: #555;"><label>Procedure :</label> 	  <span id="procedure"></span></h5>
		    	<h5 style="color: #555;"><label>Date :</label> 			  <span id="date"></span></h5>
		    	<h5 style="color: #555;"><label>Time :</label> 			  <span id="time"></span></h5>
		    	<h5 style="color: #555;"><label>Clinic Discount :</label> 			  <span id="clinic_discount"></span></h5>
		    	<h5 style="color: #555;"><label>Mednefits Discount :</label> 			  <span id="mednefits_discount"></span></h5>
		    	<h5 style="color: #555;"><label>Total Amount :</label>    <span id="total_amount"></span></h5>
		    	<h5 style="color: #555;"><label>Medi Credit :</label> -<span id="deducted"></span></h5>
		    </div>

		    <div class="col-md-11 text-right">
		    	<h5 style="color: #555;"><span style="font-weight: 700">Final Bill</span> : $<span id="final_bill"></span></h5>
		    </div>

		    <div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

		    <div class="col-md-11 text-right" style="margin-bottom: 20px">
		    	<button id="" class="btn btn-warning cancel2 calc-cancel" style="height: 29px;font-size: 12px;padding: 0px 12px;background-color: #f0ad4e;border-color: #eea236;">Back</button>
		    	<button class="btn btn-success" style="height: 29px;font-size: 12px;padding: 0px 12px;position: relative;top: -1px;background-color: #5cb85c;border-color: #4cae4c;" id="finish-transaction">Finish</button>
		    </div>
	    </div>

	    <div class="summary-receipt-co-paid-group" style="margin-left: 20px;" hidden>
	    	<div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

	    	<div class="col-md-10">
		    	<h5 style="color: #555;font-weight: 700">Receipt Summary</h5>
		    </div>

		    <div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

		    <div class="col-md-11">
		    	<h5 style="color: #555;"><label>Name :</label>       	  <span id="client_name_co_paid"></span></h5>
		    	<h5 style="color: #555;"><label>NRIC :</label> 		      <span id="nric_co_paid"></span></h5>
		    	<h5 style="color: #555;"><label>Procedure :</label> 	  <span id="procedure_co_paid"></span></h5>
		    	<h5 style="color: #555;"><label>Date :</label> 			  <span id="date_co_paid"></span></h5>
		    	<h5 style="color: #555;"><label>Time :</label> 			  <span id="time_co_paid"></span></h5>
		    	<!-- <h5 style="color: #555;"><label>Clinic Discount :</label> 			  <span id="clinic_discount"></span></h5> -->
		    	<!-- <h5 style="color: #555;"><label>Mednefits Discount :</label> 			  <span id="mednefits_discount"></span></h5> -->
		    	<h5 style="color: #555;"><label>Total Medication Amount :</label>    <span id="total_amount_co_paid"></span></h5>
		    	<h5 style="color: #555;"><label>Medi Credit :</label> -<span id="deducted_co_paid"></span></h5>
		    </div>

		    <div class="col-md-11 text-right">
		    	<h5 style="color: #555;"><span style="font-weight: 700">Final Medication Bill</span> : $<span id="final_bill_co_paid"></span></h5>
		    </div>

		    <div class="col-md-11">
		    	<div class="white-space-20"></div>
		    </div>

		    <div class="col-md-11 text-right" style="margin-bottom: 20px">
		    	<button id="" class="btn btn-warning cancel2 calc-cancel" style="height: 29px;font-size: 12px;padding: 0px 12px;background-color: #f0ad4e;border-color: #eea236;">Back</button>
		    	<button class="btn btn-success" style="height: 29px;font-size: 12px;padding: 0px 12px;position: relative;top: -1px;background-color: #5cb85c;border-color: #4cae4c;" id="finish-transaction-co-paid-group">Finish</button>
		    </div>
	    </div>
	</div>
</div>

<!-- pin -->
<div id="verify_pin" style="background: white !important;">
	<p style="font-family:'Open Sans', 'sans-serif';">Confirm your secret PIN:</p>
	<input type="hidden" id="h-pin_types" name="" value="0">

	<!--   pin verification types
	 	1 - new appointment;
		2 - resize
		3 - drag
		4 - edit
		5 - delegte
		6 - conclude
		7 - noshow -->

	<hr>
	<div class="form-group" style="margin-top: -10px;">
		<label style="color: #C8C8C7;font-family:'Open Sans', 'sans-serif';" for="pin">Enter your 4 digit pin</label>
		<input type="password" class="form-control" id="pin_verification" placeholder="" style="margin-top: 10px;margin-bottom: 10px;">
		<span id="pinerror" style="color: red;font-family:'Open Sans', 'sans-serif'; font-size: 15px; display: none" for="pin">you are not allowed!</span>
	</div>
	<div style="padding:18px">
		<button style="background: #3ABCEE; color: white; height: 38px;width: 90px; font-family:'Open Sans', 'sans-serif';" class="btn" id="pin_confirm">Confirm</button>
		<button style="background: #BFBFBF; color: white; margin-left: 25px;height: 38px;width: 90px;font-family:'Open Sans', 'sans-serif';" class="btn" id="pin_cancel">Cancel</button>
	</div>
</div>


<!-- RESERVE MODAL -->

<div class="modal fade" id="reserveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" style="padding-left: 35px;">Reserve Booking</h4>
			</div>

			<div class="modal-body" style="font-size: 12px;padding: 5px 0 0 0;">	

				<ul class="nav nav-tabs" id="tabs" style="padding-bottom: 5px;padding-left: 30px;">
				<li class="active enabledTab"><a data-toggle="tab" href="#booking">BOOKING</a></li>
				</ul>

  				<!-- Details tab contents -->
  				<div class="tab-content">
    				<div id="booking" class="tab-pane fade in active">
      					<div class="panel panel-default" style="margin: 0px;">
            				<div class="panel-body">

	  							<!-- Doctor list dropdown -->
								<div class="form-group">
									<label class="col-sm-2 details-label" style="padding: 0px; padding-left: 33px;">Doctor</label>
									<div class="col-sm-8">
										<label class="dropdown-btn input-width" style="height: 38px; cursor: pointer;">
											<i class="glyphicon glyphicon-ok" style="color: #1b9bd7"></i>&nbsp;&nbsp;
											<span class="doctor-selection" id="">Doctor Name</span>
										</label>
										<ul class="dropdown-menu dropdown-btn ul-width" id="appointment-doctor-list">
									        <li>
									        	<a href="#" id="">
									        	<span><img src="{{ URL::asset('assets/images/ico_Profile.svg') }}" alt="">&nbsp;&nbsp;</span>
									        	DocName
									        	</a>
									        </li>
										</ul>
									</div>
								</div>
	    						<br><br><br>

	    						<!-- slot blocker service list dropdown -->
							    <div class="form-group" id="abc">
							    	<label class="col-sm-2 details-label" style="padding: 0px; padding-left: 33px;">Service</label>

							    	<input type="hidden" name="" value="" id="h-duration">
								    <div class="col-sm-8">
								    	<label class="dropdown-btn slot-blocker-width" data-toggle="dropdown" id="service-lbl" style="height: 38px; cursor: pointer;">
								    		<i id="ok-icon" class="glyphicon" style="color: #1b9bd7"></i>&nbsp;&nbsp;<span class="service-selection" id="">Select a service</span>
								    		<div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div>
								    	</label>
							    		<ul class="dropdown-menu ul-width" id="service-list-reserve" style="width: 392px; max-height: 258px; overflow-y: auto; overflow-x: hidden;">
									        
									        <?php if($doctorprocedurelist){
									        	foreach ($doctorprocedurelist as $value) { ?>

									        	<li id="" style="padding: 5px 15px 5px 15px; color: #555555;">
													<span class="service" id="{{ $value->ProcedureID}}">{{ $value->Name}}</span>
													<span class="pull-right">{{ $value->Duration}} {{ $value->Duration_Format}}</span>
												</li>
									    	<?php } }?>

										</ul>
								    </div>
							    </div>
							    <br><br><br>

							    <!-- Cost & Time duration -->
							    <div id="Cost-Time-duration">
								    <div class="form-group">
								    	<label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Price</label>

									    <div class="col-sm-2" >
									     	<input type="text" id="service-price-reserve" class="dropdown-btn" style="height: 35px; width: 90px;">
									    </div>
								    	<label class="col-sm-1 details-label">Duration</label>
								    	<input type="text" id="service-time-Duration-reserve" class="dropdown-btn col-sm-1" style="height: 15px; width: 55px;">
									    <div class="col-sm-2" style="padding: 0px; padding-left: 9px;">
									    	<label class="dropdown-btn" data-toggle="dropdown" id="" style="height: 38px; cursor: pointer;">
										    	<span class="time-format" id="min">Mins</span>
										    	<div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div>
									    	</label>
								    		<ul class="dropdown-menu" id="select-time-Format">
										        <li><a href="#" id="mins">Mins</a></li>
										        <li><a href="#" id="hours">Hours</a></li>
											</ul>
									    </div>
								    </div>
								    <br><br><br>
							    </div>


							    <!-- Date & Time pickers -->
							    <div class="form-group">
								    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Day</label>

								    <div class="col-sm-4">
								    	<input type="text" id="appointment-date-reserve" class="dropdown-btn" style="height: 30px; width: 180px; cursor: pointer;">
								    </div>
								    <label class="col-sm-1 details-label" style="padding-right: 0px;">Time</label>
								    <div class="col-sm-2" style="padding: 0px; padding-left: 5px;">
										<div class="right-inner-addon">
										    <i class="glyphicon glyphicon-chevron-down" style="padding: 10px 0px;"></i>
										    <input type="search" class="dropdown-btn" id="appointment-time-reserve" style="width: 65px; height: 35px; cursor: pointer;" />
										</div>
								    </div>
							    </div>
							    <br><br><br>
	    
							    <!-- name of reserver -->
							    <div class="form-group">
								    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Name</label>

								    <div class="col-sm-8">
								    	<input type="text" id="name-reserve" class="dropdown-btn input-width" placeholder="Name" style="height: 27px;">
								    </div>
							    </div>
	    						<br><br><br>
	    
							    <div class="form-group">
							    	<label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Email <span style="margin: 0; font-size: 10px;">(Optional)</span></label>

								    <div class="col-sm-8">
								    	<input type="text" id="email-reserve" class="dropdown-btn input-width" placeholder="email" style="height: 27px;">
								    </div>
							    </div>
								<br><br><br>

							    <div class="form-group">
								    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Phone <span style="margin: 0; font-size: 10px;">(Optional)</span></label>

								    <div class="col-sm-8">
										<div id="mobile-dropdown" class="btn-group" style="border: 1px solid #d9d9d9; border-radius: 5px; display: block; width: 375px;">
											<button type="button" id="phone-code-reserve" class="btn dropdown-toggle" data-toggle="dropdown" style="height: 28px; font-size: 12px; color: #686868; background: #F4F4F4; border-right: 1px solid #d9d9d9; width: 35px; text-align: left;">+65</button>
											<input type="number" id="phone-no-reserve" class="dropdown-btn " value="" placeholder="Mobile Number" style="height: 25px; width: 293px; font-size: 12px; border: 0px;">
											<ul class="dropdown-menu" id="phone-code-list-reserve" style="width: 375px; max-height: 150px; overflow-y: auto; overflow-x: hidden;">

											</ul>
										</div>
									</div>
							    </div>
	    						<br><br><br>

							    <!-- Notes -->
							    <div class="form-group">
								    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">Notes</label>

								    <div class="col-sm-8">
								    	<input type="text" id="notes-reserve" class="dropdown-btn input-width" placeholder="Notes / Instructions" style="height: 27px;">
								    </div>
							    </div>

							    <div class="form-group" style="display: none" id="error_div1">
								    <label class="details-label col-sm-2"></label>
								    <div class="col-sm-8 alert alert-danger" id="error1" style="margin: 0px; margin-top: 12px;">
								    </div>
							    </div>

							    <div style="clear: both;">
							    </div>
							    <br>
							    <br>

							    <div class="form-group">
								    <label class="details-label col-sm-2" style="padding: 0px; padding-left: 33px;">&nbsp;</label>

								    <div class="col-sm-8">
								    	<button type="button" class="hide btn btn-update font-type-Montserrat col-sm-3" id="update-reserve">Update Reserve</button>
								    	<button type="button" class=" btn btn-update font-type-Montserrat col-sm-3" id="blocker-reserve-group">Reserve</button>
								    </div>
							    </div>
	    						<br>

			            	</div>
			            </div>
    				</div>

  				</div>

      		</div>
    	</div>
  	</div>
</div>

<!-- ......................... dialog box for view slot blocker detais ............................. -->


<div id="bocker-dialog" title="Blocker Details" style="padding: 0px; display: none; background: white !important;">
	<div class="panel panel-default">
	  	<div class="panel-body col-sm-11" style="border-bottom: 2px solid #DDDDDD; margin-top: 10px; background: white !important;">
	  	<div style="margin-bottom: 70px; font-size: 15px; color: #332F2F;">
	    	<span class="col-sm-11"><b id="blocker-date-lbl"></b></span>
	    	<span class="col-sm-11" id="blocker-time-lbl"></span>
	    </div>

	    <input type="hidden" id="blocker-id">

	  	</div>
  	</div>
  	<div style="padding-top: 100px; padding-bottom: 50px; padding-right: 15px; background: white !important;">
        <button type="button" id="bocker-delete" class="appt-details-btn" style="font-size: medium; color: #333; border: 1px solid #BFB6B6; background: #f5f5f5; width: 60px; float: right; margin-top: 6px;">Delete</button>
    </div>
</div>


<!-- SEARCH CORPORATE MODAL -->

<div id="search-booking-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title font">Appointment</h4>
      </div>
      <div class="modal-body" style="overflow: hidden;padding: 0 25px;">
      	<ul class="nav navbar-nav">
      		<li class="font active"><a href="javascript:void(0)">Mednefits Member</a></li>
      		<li class="font"><a href="javascript:void(0)">Booking</a></li>
      	</ul>
      </div>
      <div id="content">

	      <form class="form-horizontal">

	      	<div class="form-group">
			    <label class="col-sm-2 c control-label ">First Name</label>
			    <div class="col-sm-8" >
			      <input type="text" id="fname" class="form-control tf" placeholder="Name">
			    </div>
			</div>
			  </br>

			<div class="form-group">
			    <label class="col-sm-2 c control-label ">Last Name</label>

			    <div class="col-sm-8" >

					<input type="text" id="lname" class="tf form-control"  placeholder="Last Name">
			    </div>


			</div>
			</br>

			<div class="form-group">
			    <label class="col-sm-2 c control-label " >IC Number</label>

			    <div class="col-sm-8" >

					<input type="text" id="IDnum" class="tf form-control"  placeholder="IC Number">
			    </div>

			</div>
			</br>

			<div class="form-group">
			    <label class="col-sm-2 c control-label " >Company Name</label>

			    <div class="col-sm-8" >

					<input type="text" id="comp_name" class="tf form-control"  placeholder="Company Name">
			    </div>

			</div>
			</br>


	      </form>
		<span class="error font" style="margin-left: 78px; display:none"></span>
      </div>

      <div id="content-two" hidden>
      	  <form class="form-horizontal">

	      	<div class="form-group">
			    <label class="col-sm-2 c control-label ">Doctor</label>
			    <div class="col-sm-8" >
			      <!-- <div class="dropdown"> -->
					  <label class="dropdown-btn input-width" style="height: 38px; cursor: pointer;" data-toggle="dropdown" id="dropdownMenu1" aria-haspopup="true" aria-expanded="true">
					    <i class="glyphicon glyphicon-ok" style="color: #1b9bd7"></i>&nbsp;&nbsp;<span class="doctor-selection" id="{{ $doctorlist[0]->DoctorID }}">{{ $doctorlist[0]->DocName }}</span><div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div>
					  </label>
					  <ul class="dropdown-menu dropdown-btn ul-width" id="appointment-doctor-list">
					    <?php foreach ($doctorlist as $val) { ?>
				        <li><a href="#" id="{{ $val->DoctorID}}">
				        <span><img src="{{ URL::asset('assets/images/ico_Profile.svg') }}" alt="">&nbsp;&nbsp;</span>{{ $val->DocName}}</a></li>
				        <?php } ?>
					  </ul>
					<!-- </div> -->

			    </div>
			</div>
			  </br>

			<div class="form-group">
			    <label class="col-sm-2 c control-label ">Service</label>
			    <input type="hidden" name="" value="" id="h-duration">
			    <div class="col-sm-8" >
			    	<label class="dropdown-btn input-width" style="height: 38px; cursor: pointer;" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					    <i class="glyphicon glyphicon-ok" style="color: #1b9bd7"></i>&nbsp;&nbsp;<span class="service-selection">Service Name</span><div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div>
					  </label>
					  <ul class="dropdown-menu ul-width" id="service-list-search" style="width: 392px; max-height: 258px; overflow-y: auto; overflow-x: hidden;">
					    <!-- <li id="" style="padding: 5px 15px 5px 15px; color: #555555;">
							<span class="service" id="0">Slot Blocker</span>
							<span class="pull-right">Custom</span>
						</li>
				         <li class="divider"></li> -->

				        <?php if($doctorprocedurelist){
				        	foreach ($doctorprocedurelist as $value) { ?>

				        	<li id="" style="padding: 5px 15px 5px 15px; color: #555555;">
								<span class="service" id="{{ $value->ProcedureID}}">{{ $value->Name}}</span>
								<span class="pull-right">{{ $value->Duration}} {{ $value->Duration_Format}}</span>
								<span id="selected-duration" hidden>{{ $value->Duration}}</span>
							</li>
		        			<!-- <li><a href="#">
				        	<span class="service" id="{{ $value->ProcedureID}}">{{ $value->Name}}</span> 
				        	<span class="ext-right">{{ $value->Duration}} {{ $value->Duration_Format}}</span></a>
				        	</li> -->
				    	<?php } }?>
					  </ul>
			    </div>
			</div>
			</br>
			<input type="hidden" id="selected-pro-id" name="" >
			<div class="form-group">
			    <!-- <label class="col-sm-2 c control-label " >Price</label>

			    <div class="col-sm-2" >

					<input type="text" id="service-price-search" class="tf form-control"  placeholder="Price" value="0">
			    </div> -->

			    <label class="col-sm-1 c control-label " style="padding-left: 0; margin-left: 15px;">Duration</label>
 
			    <div class="col-sm-2" style="padding-right: 0; margin-left: 50px;">

					<input type="text" id="block-time-Duration-search" class="tf form-control" min="0"  placeholder="Price" value="0">
			    </div>

			    <div class="col-sm-2" >

					<label class="dropdown-btn" data-toggle="dropdown" id="" style="height: 38px; cursor: pointer;"><span class="blocker-time-format" id="min">Mins</span><div class="ext-right"><i class="glyphicon glyphicon-chevron-down"></i></div></label>
		    		<ul class="dropdown-menu" id="select-blocker-time-Format" style="min-width: 85px;">
				        <li><a href="#" id="mins">Mins</a></li>
				        <li><a href="#" id="hours">Hours</a></li>
					</ul>
			    </div>

			</div>
			</br>

			<div class="form-group">
			    <label class="col-sm-2 c control-label " >Day</label>

			    <div class="col-sm-5" style="width: 37%;padding-right: 0;">

					<input type="text" id="appointment-date-search" class="dropdown-btn" style="height: 32px !important; width: 180px; cursor: pointer;">
			    </div>

			    <label class="col-sm-1 c control-label " style="padding-right: 0; padding-left: 0">Time</label>

			    <div class="col-sm-2" style="padding-left: 0">
			    	<div class="right-inner-addon">
					    <i class="glyphicon glyphicon-chevron-down" style="padding: 10px 0px;"></i>
					    <input type="search" class="dropdown-btn" id="appointment-time-search" style="width: 65px; height: 35px; cursor: pointer;" />
					</div>

			    </div>

			</div>
			</br>

			<div class="form-group">
			    <label class="col-sm-2 c control-label " >Notes</label>

			    <div class="col-sm-8" >

					<input id="notes" type="text" class="tf form-control input-width"  placeholder="Notes / Instructions" style="width: 96%;">
			    </div>

			</div>
			</br>

			<div class="form-group">
			    <p id="search-error-mess" class="text-danger" style="text-align: center;"></p>

			</div>
			</br>


	      </form>
		<span class="error font" style="margin-left: 78px; display:none"></span>
      </div>
      <div class="modal-footer">
      	<div class="col-md-2"></div>
      	<div class="col-md-8 text-left">
      		<button id="back-search-button" type="button" class="btn btn-primary font bt" style="display: none">Back</button>
      		<button id="continue-search-button" type="button" class="btn btn-primary font bt" >Continue</button>
        	<button id="book-search-button" type="button" class="btn btn-primary font bt" style="display: none">Book</button>
      	</div>
        
      </div>
    </div>

  </div>
</div>