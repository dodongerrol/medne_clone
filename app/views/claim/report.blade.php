@include('common.home_header')
	{{ HTML::script('assets/care-plan/js/calendar/moment/moment.js') }}
	{{ HTML::script('assets/js/lodash.js') }}
	{{ HTML::style('assets/care-plan/css/sweetalert.css') }}
	{{ HTML::style('assets/css/np-autocomplete.min.css') }}
	{{ HTML::style('assets/css/multiple-select.min.css') }}
	{{ HTML::style('assets/css/bootstrap-datetimepicker.min.css') }}
	{{ HTML::script('assets/care-plan/js/angular.min.js') }}
	{{ HTML::script('assets/care-plan/js/angular-local-storage.min.js') }}
	{{ HTML::script('assets/care-plan/js/sweetalert.min.js') }}
	{{ HTML::script('assets/js/jquery.autocomplete.min.js') }}
	{{ HTML::script('assets/js/np-autocomplete.min.js') }}
	{{ HTML::script('assets/js/ngStorage.min.js') }}
	{{ HTML::script('assets/js/multiple-select.min.js') }}
	{{ HTML::script('assets/js/bootstrap-datetimepicker.min.js') }}
	{{ HTML::script('assets/js/pusher.min.js') }}
	{{ HTML::style('assets/css/claim.css') }}
	{{ HTML::style('assets/css/transac-view-page.css') }}
	{{ HTML::style('assets/hr-dashboard/css/daterangepicker.css') }}
	{{ HTML::script('assets/hr-dashboard/js/daterangepicker.js') }}
	<script type="text/javascript" src="<?php echo $server; ?>/assets/claim/js/claim.js?_={{ $date->format('U') }}"></script>
	<script src="/assets/js/OneSignalSDK.js" async='async'></script>
	<script>
	  var OneSignal = window.OneSignal || [];

	  jQuery.ajax({
	    type: "GET",
	    url : window.location.origin + "/config/notification",
	    success : function(data){
	       console.log(data);
			    OneSignal.push(["init", {
			      appId: data,
			      autoRegister: true, // Set to true to automatically prompt visitors
			      httpPermissionRequest: {
			        enable: true
			      },
			      notifyButton: {
			          enable: false /* Set to false to hide */
			      }
			    }]);
				OneSignal.push(["sendTag", "clinicid", "{{$clincID}}"])

	    }
	  });

	</script>

	<style type="text/css">

		.tooltip-cash{
	    position: absolute;
	    left: -20px;
	    top: 20px;
	    margin: 0;
	    width: 300px;
	    line-height: 1.3;
	    font-family: 'Helvetica Light';
	    font-weight: 700;
	    color: #444;
	    background: #c1c3c5;
	    padding: 8px 15px;
	    border-radius: 4px;
			display: none;
			z-index: 99;
		}

		.cash-tooltip:hover .tooltip-cash{
			display: block;
		}
		.label-custom
		{
			padding: 13px;
	    background-color: #439057;
	    font-size: 12px;
	    width: 55px;
	    display: inline-block;
		}

		.search-user-tooltip:hover .tooltip-cash{
			display: block;
		}

		#search-user-modal .modal-body {
	    background: #FFF;
		}

		#search-user-modal .modal-footer {
	    padding: 15px 0px !important;
	    margin: 0 15px !important;
		}

		#search-user-modal .modal-footer .btn{
      background: #EEE !important;
	    font-size: 16px;
	    font-weight: 700;
	    padding: 6px 25px;
		}
	</style>

	<script type="text/javascript">
		$(function () {
			var trap = false;
			$('body').on('click','.daytime-drop',function(ev){
				$( ".time-select-container" ).hide();
				$(".daytime-drop .dropdown-menu").hide();
				if( trap == true ){
					$(ev.target).closest(".form-group").find(".daytime-drop .dropdown-menu").hide();
					trap = false;
				}else{
					$(ev.target).closest(".form-group").find(".daytime-drop .dropdown-menu").show();
					trap = true;
				}
			});

		  $("[data-toggle='tooltip']").tooltip();

		  $("body").click(function(e){
		  	if ($(e.target).parents(".visit-date-form.time").length === 0) {
	        $(".time-select-container").hide();
	        $(".daytime-drop .dropdown-menu").hide();
	        trap = false;
		    }

		    if ( $(e.target).parents(".autocp-form").length === 0) {
	        $(".services-list-container").hide();
		    }
			});

		  var convert_trap = false;

			$(".converter-icon").click(function() {
				if( convert_trap == false ){
					$('.converter-container').css({
						'transition' : 'all 0.5s ease-in-out',
						'left' : '10px',
					});
					$('.converter-icon #show').hide();
					$('.converter-icon #hide').show();
					convert_trap = true;
				}else{
					$('.converter-container').css({
						'transition' : 'all 0.5s ease-in-out',
						'left' : '-270px',
					});
					$('.converter-icon #show').show();
					$('.converter-icon #hide').hide();
					convert_trap = false;
				}
				
			});

		  $(".converter-container .input-group input").keypress(function (e) {
        if (String.fromCharCode(e.keyCode).match(/[^0-9.]/g)) return false;
      });
		})
	</script>

	<div ng-app="app">
		<div claim-directive>
			<div class="converter-container">

				<!-- <div class="converter-content"> -->
				<div class="converter-content" ng-if="clinic.currency_type == 'myr'">
					<div class="header-title">
						SGD to MYR Converter
					</div>

					<div ng-if="conversion.sgd_to_myr" class="input-group" style="margin-bottom: 5px;">
					  <input type="text" class="form-control" placeholder="00.00" ng-model="conversion.sgd" ng-change="convertToMyr( conversion.sgd )">
					  <span class="input-group-addon" >SGD</span>
					</div>
					<div ng-if="conversion.myr_to_sgd" class="input-group" style="margin-bottom: 5px;">
					  <input type="text" class="form-control" placeholder="00.00" ng-model="conversion.myr" ng-change="convertToSgd( conversion.myr )">
					  <span class="input-group-addon" >MYR</span>
					</div>

					<div style="display: inline-block;width: 100%;text-align: center;">
						<i class="fa fa-arrow-down" style="color: #666"></i>
					</div>

					<div ng-if="conversion.sgd_to_myr" class="input-group" style="margin-top: 5px;">
					  <input type="text" class="form-control" placeholder="00.00" ng-model="conversion.myr" style="cursor: not-allowed;" readonly>
					  <span class="input-group-addon" >MYR</span>
					</div>
					<div ng-if="conversion.myr_to_sgd" class="input-group" style="margin-top: 5px;">
					  <input type="text" class="form-control" placeholder="00.00" ng-model="conversion.sgd" style="cursor: not-allowed;" readonly>
					  <span class="input-group-addon" >SGD</span>
					</div>


					<div style="display: inline-block;width: 100%;padding: 8px;box-sizing: border-box !important">
						<button class="btn btn-myr-sgd" ng-click="switchConvertion()">
							<span ng-if="conversion.sgd_to_myr">MYR to SGD</span>
							<span ng-if="conversion.myr_to_sgd">SGD to MYR</span>
						</button>
						<button class="btn btn-clear" ng-click="clearCurrency()">Clear</button>
						<!-- <button class="btn btn-convert" ng-click="convertCurrency()">Convert</button> -->
					</div>
				</div>

				<div class="converter-icon">
					<img id="show" src="/assets/images/currency-white.png">
					<img id="hide" src="/assets/images/cancel.png" hidden>
				</div>

			</div>

			<div class="container claim-page" >
				<div class="col-md-12 border-box" style="margin-top: 20px;" >
					<table class="table" style="width: 95%;margin: 30px auto 50px auto;">
						<thead>
				      <tr>
				        <th>NRIC</th>
				        <th style="width: 170px;">Service</th>
				        <th>Date of Visit</th>
				        <th>Time of Visit</th>
				        <th>
				        	Cash
				        	<i class="fa fa-info-circle cash-tooltip" style="color: #0190CD;position: relative;" >
						        	<p class="tooltip-cash">This is the amount, Mednefits Member paid to you either in Cash/Nets/Credit Card.</p>
						        </i>
				        </th>
				        <th>Option</th>
				      </tr>
				    </thead>
						<tbody>
							<tr ng-repeat="list in temp_list">
				    		<td ng-if="!temp_list[0].automatic">
				    			<div class="form-group" np-autocomplete="options" ng-model="list.id" np-input-model="list.nric">
										<input type="text" placeholder="Search NRIC" class="form-control"/>
										<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate form-control-feedback" aria-hidden="true"></span>
									</div>
				    		</td>
				    		<td ng-if="temp_list[0].automatic">
				    			<div class="form-group">
										<input type="text" placeholder="Search NRIC" class="form-control" ng-model="list.nric"/>
									</div>
				    		</td>
				    		<td class="autocp-form" style="position: relative;padding-right: 0;width: 190px;">
									<div ng-repeat="list in service_length" class="form-group" style="margin-bottom: 5px !important;">
										<div class="display-flex">
											<input type="text" placeholder="Select Procedure" class="form-control" ng-model="service_selected[$index].name" ng-focus="isServiceFocused( $index )" ng-blur="isServiceBlur()" style="flex: 8" />

											<!-- <div class="add-btn-service" style="flex: 2;text-align: center;">
												<a ng-if="service_selected[$index] && !service_selected[$index + 1] && service_list.length != service_selected.length" href="javascript:void(0)" ng-click="addServiceLength($index + 1)"><i class="fa fa-plus"></i></a>

												<a ng-if="( service_selected[$index] && service_selected[$index + 1] ) || service_list.length == service_selected.length" href="javascript:void(0)" ng-click="removeServiceLength($index)" style="background: #ea4141"><i class="fa fa-times" ></i></a>
											</div> -->
										</div>
									</div>

									<div class="services-list-container">
										<ul class="nav">
											<li ng-repeat="list in service_list">
												<a href="javascript:void(0)" ng-bind="list.name" ng-click="serviceSelected(list)"></a>
												<i ng-if="list.selected" class="fa fa-times" ng-click="removeServiceFromArray(list)"></i>
											</li>
										</ul>
									</div>
				    		</td>
				    		<td>
				    			<div class="form-group visit-date-form" >
			              <div class="input-group date">
										  <input id="visitDateInput" type="text" class="form-control eclaim-date-picker" placeholder="Date" aria-describedby="sizing-addon2" style="border-right: none;" ng-model="list.book_date">
										  <span class="input-group-addon visit-date-add" id="sizing-addon2">
										  	<img src="/assets/e-claim/img/new-assets/Submit-E-Claim---Visit-Date.png" style="width: 18px;">
										  </span>
										</div>
			            </div>
				    		</td>
				    		<td>
				    			<div class="form-group visit-date-form time" style="position: relative;">
			              <div class="input-group date">
            				  <span class="input-group-addon visit-date-add" id="sizing-addon2">
									  		<img src="/assets/e-claim/img/new-assets/Submit-E-Claim---Visit-Time.png" style="width: 18px;">
										  </span>

										  <input id="visitTimeInput" type="text" class="form-control eclaim-date-picker visitTimeInput" aria-describedby="sizing-addon2" style="border-right: none;padding: 6px 8px;" ng-model="list.time" ng-focus="showVisitTime( $event )" >

										  <span class="input-group-addon daytime-drop">
										    <span ng-bind="vist_time_day">AM</span>
									    	<span class="caret" style="border-width: 6px;display: block;position: relative;top: 5px;left: 2px"></span>
											  <ul class="dropdown-menu">
											    <li><a href="javascript:void(0)" ng-click="visitTimeDayChanged('AM')">AM</a></li>
											    <li><a href="javascript:void(0)" ng-click="visitTimeDayChanged('PM')">PM</a></li>
											  </ul>
										  </span>

										  <div class="time-select-container" >
									  		<!-- <a href="javascript:void(0)" ng-click="hideVisitTime()" style="position: absolute;right: 5px;top: 0;">
									  			<i class="fa fa-times" style="font-size: 12px;"></i>
									  		</a> -->

										  	<div class="arrow-up-wrapper">
										  		<div class="display-flex">
											  		<div class="hour-arrow">
											  			<a href="javascript:void(0)" ng-click="addHour()"><i class="fa fa-chevron-up"></i></a>
											  		</div>
											  		<div class="minute-arrow">
											  			<a href="javascript:void(0)" ng-click="addMinute()"><i class="fa fa-chevron-up"></i></a>
											  		</div>
										  		</div>
										  	</div>
										  	<div class="time-wrapper">
										  		<div class="display-flex">
											  		<div class="hour-time">
											  			<span ng-if="selected_hour < 10">0</span><span ng-bind="selected_hour">01</span>
											  		</div>
											  		<div class="separate">
											  			:
											  		</div>
											  		<div class="minute-time">
											  			<span ng-if="selected_minute < 10">0</span><span ng-bind="selected_minute">59</span>
											  		</div>
										  		</div>
										  	</div>
										  	<div class="arrow-down-wrapper">
										  		<div class="display-flex">
											  		<div class="hour-arrow">
											  			<a href="javascript:void(0)" ng-click="deductHour()"><i class="fa fa-chevron-down"></i></a>
											  		</div>
											  		<div class="minute-arrow">
											  			<a href="javascript:void(0)" ng-click="deductMinute()"><i class="fa fa-chevron-down"></i></a>
											  		</div>
										  		</div>
										  	</div>
										  </div>
										</div>
			            </div>
				    		</td>
				    		<td>
				    			<div class="input-group date" ng-cloak>
								  <input id="claimAmountInput" valid-number type="text" class="form-control" placeholder="@{{ placeholder }}" name="amount" aria-describedby="sizing-addon2" style="border-right: none;" ng-model="list.amount">
								  <span class="input-group-addon amount-add" id="sizing-addon2">
								  	<span ng-if="clinic.currency_type == 'sgd'">S$</span>
								  	<span ng-if="clinic.currency_type == 'myr'">RM</span>
								  </span>
								</div>
				    		</td>
				    		<td>
				    			<!-- <button type="button" class="btn btn-primary btn-add" ng-click="checkClaim()">Add</button> -->
				    			<button ng-disabled="!list.nric || list.procedure_ids.length == 0 || !list.time" type="button" class="btn btn-primary btn-add" ng-click="checkClaim()">Add</button>
				    		</td>
				    	</tr>
						</tbody>
					</table>

					<h4 style="font-weight: 700;font-size: 18px;margin-bottom: 20px">Transaction Preview:</h4>

					<table class="trans-prev table table-no-border">
						<thead>
					      <tr>
					        <th>Date/Time of Visit</th>
					      	<th>Name</th>
					        <th>NRIC</th>
					        <th>Service</th>
					        <th>
						        Cash
						        <i class="fa fa-info-circle cash-tooltip" style="color: #0190CD;position: relative;" >
						        	<p class="tooltip-cash">This is the amount, Mednefits Member paid to you either in Cash/Nets/Credit Card.</p>
						        </i>

					        </th>
					        <th>Option</th>
					      </tr>
					    </thead>
					     <tbody>
					     <!--  ng-if="load_status" -->
					    	<tr ng-repeat="list in claim_list">
					    		<td>
					    			<span ng-bind="list.display_book_date"></span>
					    		</td>
					    		<td>
					    			<span ng-bind="list.name"></span> - 
					    			<span ng-if="list.user_type == 1">Public User</span> 
					    			<span ng-if="list.user_type == 5 && list.access_type == 1">Invidual User</span>
					    			<span ng-if="list.user_type == 5 && list.access_type == 0">Corporate User</span>
					    			<span ng-if="list.user_type == 5 && list.access_type == 2 || list.user_type == 5 && list.access_type == 3">Dependent User</span>
					    		</td>
					    		<td>
					    			<span ng-bind="list.nric"></span>
					    		</td>
					    		<td>
					    			<span ng-repeat="service in list.procedures track by $index">
					    				<span ng-bind="service"></span>
					    				<span ng-if="$index != list.procedures.length-1">,</span>
					    			</span>
					    		</td>
					    		<td>
					    			<span ng-if="clinic.currency_type == 'myr'">RM</span>
					    			<span ng-if="clinic.currency_type == 'sgd'">SGD</span>
					    			<input valid-number type="text" placeholder="Enter Amount" ng-model="list.amount" ng-value="list.amount">
					    			<!-- <span>S$<span ng-bind="list.amount"></span></span> -->
					    		</td>
					    		<td>
					    		<!-- ng-disabled="list.amount == 0" -->
					    			<button type="button" id="submit_btn_@{{$index}}" class="btn btn-primary btn-submit" ng-click="toggleClaimSummaryModal(list, $index)">
					    				Submit
					    				<img src="{{ URL::asset('images/loading_apple.gif') }}" style="width: 15px;" class="load_state" id="loader_@{{$index}}">
					    			</button>
					    			<button type="button" class="btn btn-danger btn-remove" ng-click="remove(list)">Remove</button>
					    		</td>
					    	</tr>
					    </tbody>
					</table>

				</div>
			</div>

			<div class="table-trans-list-container">
				<div class="col-md-2 no-padding">
					<h4 style="font-weight: 700;font-size: 18px;margin-bottom: 20px">Transaction List:</h4>
				</div>
				<div class="col-md-3 no-padding search-wrapper">
					<form style="display: inline-block;">
						<div class="input-group">
							<input class="form-control font-13 search-table" placeholder="Search by NRIC" ng-model="search" ng-change="searchByNric()"/>
						      <span class="input-group-btn">
						        <button class="btn btn-default" type="button" ng-if="search_btn_status"><i class="fa fa-search font-18"></i></button>
						        <button class="btn btn-default" type="button" ng-if="!search_btn_status" ng-click="existSearch()"><i class="fa fa-close font-18"></i></button>
						      </span>
						</div>
					</form>
				</div>

				<div class="col-md-5 no-padding">
					<div ng-if="transFilterTrap == true" class="trans-filter-container">
						<select class="form-control" ng-model="filterMonthValue" ng-change="setFilterMonth(filterMonthValue)">
							<option ng-repeat="list in monthList" ng-selected="list == filterMonthValue" ng-value="list" ng-bind="list"></option>
						</select>
						<select class="form-control" ng-model="filterYearValue" ng-change="setFilterYear(filterYearValue)">
							<option ng-repeat="list in yearRange(10) | orderBy:'-' " ng-selected="list == filterYearValue" ng-value="list" ng-bind="list"></option>
						</select>
						<button class="btn btn-submit-filter" ng-click="filterByDate()">Go</button>
					</div>
					<!-- <button class="btn btn-transaction-filter-date" ng-click="toggleTransFilter()"><span ng-bind="filterMonthValue"></span> <span ng-bind="filterYearValue"></span></button>
					<a href="javascript:void(0)" ng-if="transFilterTrap" ng-click="toggleTransFilter()"><i class="fa fa-times-circle" style="font-size: 25px;position: relative;top: 5px;"></i></a> -->
					<div class="custom-date-selector btn-custom-date" style="display: inline-block;">
						<button class="btn btn-custom-start" ng-click="toggleTransFilter()">
							<i class="fa fa-calendar font-15" style="margin-right: 10px;"></i>
							<span id="rangePicker_start">01/01/2018</span>
							<i class="fa fa-caret-down font-15" style="margin-left: 10px;"></i>
						</button>

						<span style="margin: 0 10px;"><i class="fa fa-arrow-right"></i></span>

						<button class="btn btn-custom-end" ng-click="toggleTransFilter()">
							<i class="fa fa-calendar font-15" style="margin-right: 10px;"></i>
							<span id="rangePicker_end">04/01/2018</span>
							<i class="fa fa-caret-down font-15" style="margin-left: 10px;"></i>
						</button>
					</div>
				</div>

				<div class="col-md-2 no-padding search-wrapper text-right">
					<a href="javascript:void(0)" class="search-user-tooltip" ng-click="refreshModal()" data-toggle="modal" data-target="#search-user-modal">
						<img src="{{ URL::asset('assets/images/search_black.png') }}" style="width: 30px;">
						<p class="tooltip-cash" style="left: auto;right: 22px;top: -30px;width: 78px;">Search User</p>
					</a>
				</div>

				<table class="trans-list-tbl table table-no-border">
					<thead>
			      <tr>
			        <th>Visit Date/Time</th>
			        <th>Claim Date/Time</th>
			        <th>Transaction ID</th>
			      	<th>Name</th>
			        <th>NRIC</th>
			        <th>Service</th>
			       	<th style="text-align: center;">Mednefits Fee</th>
			       	<th style="text-align: center;">Mednefits Credit</th>
			        <th style="text-align: center;">Cash</th>
			        <th>Option</th>
			      </tr>
			    </thead>
			    <tbody id="isLoading" hidden>
			    	<tr >
			     		<td colspan="10" class="text-center">
			     			<div class="loader-container">
			     				<div class="loader"></div>
			     			</div>
			     			<span>Loading...</span>
			     		</td>
			     	</tr>
			    </tbody>
		     <tbody id="isNotLoading" hidden>
			     	<tr ng-if="backdate_list.length == 0">
			     		<td colspan="10" class="text-center">No Transactions found</td>
			     	</tr>

			    	<tr ng-if="backdate_list.length > 0" ng-repeat="list in backdate_list track by $index">
			    		<td>
			    			<a href="javascript:void(0)" ng-click="selectRowToUpdate(list)"><i class="fa fa-pencil"></i></a>
			    			<span ng-bind="list.date_of_transaction" style="font-size: 12px;"></span>
			    		</td>
			    		<td>
			    			<a href="javascript:void(0)" ng-click="selectRowToUpdate(list)"><i class="fa fa-pencil"></i></a>
			    			<span ng-bind="list.claim_date" style="font-size: 12px;"></span>
			    		</td>
			    		<td>
			    			<span ng-bind="list.transaction_id"></span>
			    		</td>
			    		<td>
			    			<span ng-bind="list.user_name"></span>
			    		</td>
			    		<td>
			    			<span ng-bind="list.NRIC"></span>
			    		</td>
			    		<td>
			    			<span ng-bind="list.procedure_name"></span>
			    		</td>
			    		<td style="text-align: center;">
			    			<span ng-if="list.currency_type == 'sgd'">S$<span ng-bind="list.mednefits_fee"></span></span>
			    			<span ng-if="list.currency_type == 'myr'">RM<span ng-bind="list.mednefits_fee * list.currency_amount | number: 2"></span></span>
			    		</td>
			    		<td style="text-align: center;">
			    			<span ng-if="list.currency_type == 'sgd'">S$<span ng-bind="list.mednefits_credits"></span></span>
			    			<span ng-if="list.currency_type == 'myr'">RM<span ng-bind="list.mednefits_credits * list.currency_amount | number: 2"></span></span>
			    		</td>
			    		<td style="text-align: center;">
			    			<span ng-if="list.currency_type == 'sgd'">S$<span ng-bind="list.cash"></span></span>
			    			<span ng-if="list.currency_type == 'myr'">RM<span ng-bind="list.cash * list.currency_amount | number: 2"></span></span>
			    		</td>
			    		<td>
			    			<button type="button" id="delete_btn_@{{list.trans_id}}" class="btn btn-danger btn-remove" ng-click="removeBackDate(list)" ng-if="list.deleted_option == 'refund' && !list.deleted">Refund</button>
			    			<button type="button" id="delete_btn_@{{list.trans_id}}" class="btn btn-danger btn-remove" ng-click="removeBackDate(list)" ng-if="list.deleted_option == 'remove' && !list.deleted">Remove</button>
			    			<label class="label label-success label-custom" ng-if="list.data_status == 'removed' && list.deleted">REMOVED</label>
			    			<label class="label label-success label-custom" ng-if="list.data_status == 'refunded' && list.deleted">REFUNDED</label>
			    		</td>
			    	</tr>
			    </tbody>
				</table>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="search-user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="myModalLabel" style="margin-left: 15px;"></h4>
			      </div>
			      <div class="modal-body">
			      	<p style="font-size: 11px;margin: 5px 0;">*Search user by NRIC</p>
			        <div class="form-inline" style="margin-bottom: 30px;">
			        	<input type="text" class="form-control" ng-model="searchUser" style="height: 34px !important;width: 200px;">
			        	<button ng-click="searchUserByNRIC(searchUser)" class="btn btn-search-user" style="background: #6195f5;color: #FFF;font-weight: 700;">Search</button>
			        </div>

			        <table class="table">
			        	<thead>
			        		<tr>
			        			<th>Name</th>
			        			<th>NRIC</th>
			        			<th class="text-center">Status</th>
			        		</tr>
			        	</thead>
			        	<tbody>
			        		<tr ng-if="searchingUser">
			        			<td colspan="3" class="text-center">
			        				<i class="fa fa-spinner fa-pulse fa-spin fa-3x fa-fw"></i>
											<span class="sr-only">Loading...</span>
			        			</td>
			        		</tr>
			        		<tr ng-if="searchUserByNRIC_list.number_of_results == 0">
			        			<td colspan="3" class="text-center">
			        				No Data Found
			        			</td>
			        		</tr>
			        		<tr ng-if="!searchingUser && searchUserByNRIC_list.number_of_results > 0" ng-repeat="list in searchUserByNRIC_list.results">
			        			<td ng-bind="list.name">Jeamar</td>
			        			<td ng-bind="list.nric">MDC1222309</td>
			        			<td class="text-center">
			        				<span ng-if="list.status == 1">Active</span>
			        				<span ng-if="list.status == 0">Inactive</span>
			        			</td>
			        		</tr>
			        	</tbody>
			        </table>
			      </div>
			      <div class="modal-footer">
			      </div>
			    </div>
			  </div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="e-card-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog" role="document" style="width: 450px;">
			    <div class="modal-content" style="top: 0;border-radius: 8px;">
			      <!-- <div class="modal-header" style="background: #f8f8f8;padding: 5px 10px;border-radius: 8px 8px 0 0;">
			        
			      </div> -->
			      <div class="modal-body" style="overflow: hidden;background: #f8f8f8;border-radius: 8px;padding: 10px 15px 30px 30px;">
			      	<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: relative;top: -6px;right: -6px"><span aria-hidden="true" style="color: #000 !important;font-size: 25px !important;">&times;</span></button>

			      	<div class="ecard-container">
				      	<img src="{{ URL::asset('e-template-img/mednefits logo v3 (blue-box) LARGE.png') }}" style="width:50px;margin-bottom: 10px;">
				      	<p style="font-size: 20px;color: #319EF4" id="ecard-name">Allan Cheam Alzula</p>
				      	<p style="color: #333" >Member ID : <span id="ecard-member-id">5685</span> </p>
				      	<p style="color: #333"><span id="ecard-plan-type">Corporate</span></p>
				      	<p style="color: #333" id="ecard-company">Company : Allan Test</p>
				      	<p style="margin-bottom: 10px;color: #333">Start Date : <span id="ecard-start-date">04 September 2017</span></p>
				      	<p style="margin-bottom: 10px;color: #333">Valid Thru : <span id="ecard-valid-date">31 July 2018</span></p>
				      	<p style="margin-bottom: 10px;color: #333">Your Basic Coverage</p>
				      	<div class="coverage-box">
				      		
				      	</div>
			      	</div>
			      </div>
			      <!-- <div class="modal-footer">

			      </div> -->
			    </div>
			  </div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="claim_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog" role="document" style="width: 450px;">
		      <div class="modal-content" style="">
		      	<div class="modal-header" style="border-bottom: none;padding: 10px 15px;background: none;">
			        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
			      </div>
		        <div class="modal-body" style="padding: 15px 30px 40px 30px;background: #fff;border-radius: 0 0 6px 6px;">
		        	<p class="text-center" style="margin-left: 0;">
		        		<span class="warning-icon" style="display: inline-block;border-radius: 50%;border: 3px solid #869dd2;width: 65px;height: 65px;padding: 12px;">
		        			<i class="fa fa-exclamation" style="color: #869dd2;font-size: 70px;"></i>
		        		</span>
		        	</p>
		          <p id="claim_message" class="text-center weight-700" style="color: #666;margin: 20px 0;">Message goes here.</p>
		          <p class="text-center weight-700" id="login-status" style="margin-left: 0;" hidden>
		          	<a href="javascript:void(0)" onclick="window.location.reload()" class="btn btn-primary" style="background: #1667AC!important">Reload</a>
		          </p>
		        </div>
		      </div>
		    </div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="check-claim-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog" role="document" style="width: 550px;">
			  	<div class="modal-content isNotDoneChecking" style="height: 65px;text-align: center;padding: 40px;">
			  		<p>
			  			<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
			  		</p>
			  		<p>
			  			Checking...
			  		</p>
			  	</div>
		      <div class="modal-content isDoneChecking" style="" hidden>
		      	<div class="modal-header">
		      		You have similar transactions with this user.
		      		<p style="font-size: 12px;padding-left: 4px;">Please check and make sure it is not a duplicate claim entry.</p>
			      </div>
		        <div class="modal-body" style="padding: 0px;background: #fff;border-radius: 0 0 6px 6px;">
		        	<div class="your-transaction">
		        		<p style="font-size: 20px;margin-bottom: 10px;border-bottom: 1px solid #ccc;">Your transaction </p>
		        		<p><label>Name:</label> <span ng-bind="your_transaction.name">Allan Alzula</span></p>
		        		<p>
		        			<label>Service:</label> 
		        			<span ng-repeat="list in service_selected">
		        				<span ng-bind="list.name">Dental</span>
		        				<span ng-if="service_selected.length > 1">,</span>
		        			</span>
		        		</p>
		        		<p><label>Date:</label> <span ng-bind="your_transaction.date">Jun 14,2016</span></p>
		        		<p><label>Amount:</label> S$<span ng-bind="temp_list[0].amount">50.00</span></p>
		        		<p><label>Type:</label> <span ng-bind="your_transaction.type" style="font-family: 'Helvetica Medium';color: #000;">Cash</span></p>
		        	</div>

		        	<div class="other-transaction">
		        		<p style="font-size: 20px;margin-bottom: 10px;border-bottom: 1px solid #ccc;">Similar transactions </p>

		        		<div ng-repeat="list in other_transaction" class="similar">
			        		<p><label>Name:</label> <span ng-bind="list.user_name">Allan Alzula</span></p>
		        		<p><label>Service:</label> <span ng-bind="list.service">Dental</span></p>
		        		<p><label>Date:</label> <span ng-bind="list.date_of_transaction">Jun 14,2016</span></p>
		        		<p><label>Amount:</label> S$<span ng-bind="list.procedure_cost">50.00</span></p>
		        		<p><label>Type:</label> <span ng-bind="list.transaction_type" style="font-family: 'Helvetica Medium';color: #000;">Cash</span></p>
		        		</div>
		        	</div>
		        </div>
		        <div class="modal-footer">
			      	<button class="btn btn-primary" style="background: #b52c2c !important;border-radius: 2px;padding: 10px 30px;" data-dismiss="modal" >Cancel</button>
			      	<button class="btn btn-success" style="background: #0190CD !important;border-radius: 2px;padding: 10px 30px;" ng-click="add()">Proceed</button>
				    </div>
		      </div>
		    </div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="edit-dates-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="myModalLabel" style="margin-left: 15px;">Edit Dates</h4>
			      </div>
			      <div class="modal-body">
			      	<div class="col-md-4">
			      		<div class="form-group visit-date-form" >
			      			<label>Visit Date</label>
			      			<input id="edit-visit-datepicker" type="text" class="form-control eclaim-date-picker" aria-describedby="sizing-addon2" style="border-right: none;" ng-model="row_edit_selected.visit_date">
		            </div>
			      	</div>
			      	<div class="col-md-4">
			      		<div class="form-group visit-date-form time" style="position: relative;">
			      			<label>Time</label>
			      			<div class="input-group date">
			      				<input type="text" class="form-control eclaim-date-picker visitTimeInput" aria-describedby="sizing-addon2" style="border-right: none;padding: 6px 8px;" ng-model="row_edit_selected.visit_time" ng-focus="showVisitTime( $event )" >

									  <span class="input-group-addon daytime-drop">
									    <span ng-bind="row_edit_selected.visit_period" style="text-transform: uppercase;">AM</span>
								    	<span class="caret" style="border-width: 6px;display: block;position: relative;top: 5px;left: 2px"></span>
										  <ul class="dropdown-menu">
										    <li><a href="javascript:void(0)" ng-click="editVisitTimeDayChanged('am')">AM</a></li>
										    <li><a href="javascript:void(0)" ng-click="editVisitTimeDayChanged('pm')">PM</a></li>
										  </ul>
									  </span>

									  <div class="time-select-container" >
									  	<div class="arrow-up-wrapper">
									  		<div class="display-flex">
										  		<div class="hour-arrow">
										  			<a href="javascript:void(0)" ng-click="addHour('visit')"><i class="fa fa-chevron-up"></i></a>
										  		</div>
										  		<div class="minute-arrow">
										  			<a href="javascript:void(0)" ng-click="addMinute('visit')"><i class="fa fa-chevron-up"></i></a>
										  		</div>
									  		</div>
									  	</div>
									  	<div class="time-wrapper">
									  		<div class="display-flex">
										  		<div class="hour-time">
										  			<span ng-if="row_edit_selected.visit.selected_hour < 10">0</span><span ng-bind="row_edit_selected.visit.selected_hour">01</span>
										  		</div>
										  		<div class="separate">
										  			:
										  		</div>
										  		<div class="minute-time">
										  			<span ng-if="row_edit_selected.visit.selected_minute < 10">0</span><span ng-bind="row_edit_selected.visit.selected_minute">59</span>
										  		</div>
									  		</div>
									  	</div>
									  	<div class="arrow-down-wrapper">
									  		<div class="display-flex">
										  		<div class="hour-arrow">
										  			<a href="javascript:void(0)" ng-click="deductHour('visit')"><i class="fa fa-chevron-down"></i></a>
										  		</div>
										  		<div class="minute-arrow">
										  			<a href="javascript:void(0)" ng-click="deductMinute('visit')"><i class="fa fa-chevron-down"></i></a>
										  		</div>
									  		</div>
									  	</div>
									  </div>
			      			</div>
								  
		            </div>
			      	</div>

			      	<div class="col-md-12">
			      		<div class="white-space-20"></div>
			      		<div class="white-space-10"></div>
			      	</div>

			      	<div class="col-md-4">
			      		<div class="form-group">
			      			<label>Claim Date</label>
			      			<input id="edit-claim-datepicker" type="text" class="form-control eclaim-date-picker" aria-describedby="sizing-addon2" style="border-right: none;" ng-model="row_edit_selected.date_claim">
			      		</div>
			      	</div>

			      	<div class="col-md-4">
			      		<div class="form-group visit-date-form time" style="position: relative;">
			      			<label>Time</label>
			      			<div class="input-group date">
			      				<input type="text" class="form-control eclaim-date-picker visitTimeInput" aria-describedby="sizing-addon2" style="border-right: none;padding: 6px 8px;" ng-model="row_edit_selected.time_claim" ng-focus="showVisitTime( $event )" >

									  <span class="input-group-addon daytime-drop">
									    <span ng-bind="row_edit_selected.period_claim" style="text-transform: uppercase;">AM</span>
								    	<span class="caret" style="border-width: 6px;display: block;position: relative;top: 5px;left: 2px"></span>
										  <ul class="dropdown-menu">
										    <li><a href="javascript:void(0)" ng-click="editClaimTimeDayChanged('am')">AM</a></li>
										    <li><a href="javascript:void(0)" ng-click="editClaimTimeDayChanged('pm')">PM</a></li>
										  </ul>
									  </span>

									  <div class="time-select-container" >
								  		<!-- <a href="javascript:void(0)" ng-click="hideVisitTime()" style="position: absolute;right: 5px;top: 0;">
								  			<i class="fa fa-times" style="font-size: 12px;"></i>
								  		</a> -->

									  	<div class="arrow-up-wrapper">
									  		<div class="display-flex">
										  		<div class="hour-arrow">
										  			<a href="javascript:void(0)" ng-click="addHour('claim')"><i class="fa fa-chevron-up"></i></a>
										  		</div>
										  		<div class="minute-arrow">
										  			<a href="javascript:void(0)" ng-click="addMinute('claim')"><i class="fa fa-chevron-up"></i></a>
										  		</div>
									  		</div>
									  	</div>
									  	<div class="time-wrapper">
									  		<div class="display-flex">
										  		<div class="hour-time">
										  			<span ng-if="row_edit_selected.claim.selected_hour < 10">0</span><span ng-bind="row_edit_selected.claim.selected_hour">01</span>
										  		</div>
										  		<div class="separate">
										  			:
										  		</div>
										  		<div class="minute-time">
										  			<span ng-if="row_edit_selected.claim.selected_minute < 10">0</span><span ng-bind="row_edit_selected.claim.selected_minute">59</span>
										  		</div>
									  		</div>
									  	</div>
									  	<div class="arrow-down-wrapper">
									  		<div class="display-flex">
										  		<div class="hour-arrow">
										  			<a href="javascript:void(0)" ng-click="deductHour('claim')"><i class="fa fa-chevron-down"></i></a>
										  		</div>
										  		<div class="minute-arrow">
										  			<a href="javascript:void(0)" ng-click="deductMinute('claim')"><i class="fa fa-chevron-down"></i></a>
										  		</div>
									  		</div>
									  	</div>
									  </div>
			      			</div>
								  
		            </div>
			      	</div>
			      	
			      </div>
			      <div class="modal-footer text-right" style="margin: 0 10px !important;">
			      	<span ng-if="update_trans_err_msg" class="text-error" ng-bind="update_trans_err_msg" style="font-size: 16px;margin-right: 20px;"></span>
			      	<span ng-if="update_trans_succ_msg" class="text-success" ng-bind="update_trans_succ_msg" style="font-size: 16px;margin-right: 20px;"></span>
			      	<button ng-click="updateTransactionDates()" class="btn btn-update" style="float: none;font-weight: 700;padding: 8px 25px">Update</button>
			      </div>
			    </div>
			  </div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="summary-claim-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog" role="document" >
		      <div class="modal-content">
		      	<div class="modal-header">
		      		<p>Please check your claim before you proceed.</p>
			      </div>
		        <div class="modal-body">
		        	<div class="top-content">
		        		<div ng-if="selected_submit_data.transaction_id" class="row-summary">
		        			<div class="img-wrapper">
		        				<img src="../../e-template-img/Trans-ID---Mednefits-Credits-Email.png">
		        			</div>
		        			<p><label>Transaction ID:</label> <span ng-bind="selected_submit_data.transaction_id">ELYDI8</span></p>
		        		</div>
		        		<div class="row-summary">
		        			<div class="img-wrapper">
		        				<img src="../../e-template-img/clock.png">
		        			</div>
		        			<p><label>Transaction Date:</label> <span ng-bind="selected_submit_data.display_book_date">15 April 2019, 11:49am</span></p>
		        		</div>
		        	</div>
		        	<div class="middle-content">
		        		<div class="column-details">
		        			<label>Member</label>
		        			<p ng-bind="selected_submit_data.name">Terinn Teo</p>
		        		</div>
		        		<div class="column-details">
		        			<label>NRIC</label>
		        			<p ng-bind="selected_submit_data.nric">S345D3</p>
		        		</div>
		        		<div class="column-details">
		        			<label>Payment Type</label>
		        			<p>Cash</p>
		        		</div>
		        		<div class="column-details">
		        			<label>Currency Type</label>
		        			<p ng-bind="clinic.currency_type" style="text-transform: uppercase;">MYR</p>
		        		</div>
		        	</div>
		        	<div class="bottom-content">
		        		<div class="item">
			        		<label>Item/Service</label>
			        		<div class="item-content">	
			        			<div class="img-wrapper">
			        				<img ng-src="@{{ clinic.clinic_type_image }}">
			        			</div>
			        			<p ng-bind="selected_submit_data.procedures">Lorem Ipsum</p>
			        		</div>
		        		</div>

		        		<div class="item2">
		        			<label>Cash:</label> 
		        			<p>
		        				<span ng-if="clinic.currency_type == 'sgd'">S$</span> 
		        				<span ng-if="clinic.currency_type == 'myr'">RM</span> 
		        				<span ng-bind="selected_submit_data.amount | number:2">15.00</span>
		        			</p>
		        		</div>
		        	</div>
		        </div>
		        <div class="modal-footer">
			      	<button class="btn btn-cancel-claim" data-dismiss="modal">Cancel</button>
			      	<button class="btn" ng-click="submitData( selected_submit_data, selected_submit_data.index )">Proceed</button>
				    </div>
		      </div>
		    </div>
			</div>


		</div>
	</div>

	<style type="text/css">
		#e-card-modal .modal-body p{
			margin-left: 0;
			color: #666;
			font-size: 14px;
			margin-bottom: 5px;
		}

		#e-card-modal .coverage-box{
			color: #111;
			font-size: 12px;
			overflow-y: auto;
      max-height: 200px;
		}

		#e-card-modal .coverage-box .cov-item{
			overflow: hidden;
    	margin-bottom: 20px;
		}

		#e-card-modal .ecard-container{
			background: #fff;
	    padding: 20px;
	    margin-top: 20px;
	    border-radius: 8px;
	    border: 2px solid #ddd;
	    overflow: hidden;
		}

		#check-claim-modal .modal-content{
			top: 25px;
		}

		#check-claim-modal .modal-header{
	    padding: 10px 15px !important;
	    background: #0190CD !important;
	    color: #e9e9e9!important;
	    border-radius: 4px 4px 0 0 !important;
	    font-size: 20px;
		}

		#check-claim-modal p{
			margin-left: 0;
		}

		#check-claim-modal .your-transaction{
	    background: #f5f5f5;
    	padding: 10px 20px;
    	border-bottom: 1px solid #ccc;
		}

		#check-claim-modal .other-transaction{
	    height: 220px;
    	overflow-x: auto;
    	padding: 10px 20px;
		}

		#check-claim-modal .other-transaction .similar{
      padding-bottom: 5px;
	    border-bottom: 1px solid #888;
	    margin-bottom: 5px;
		}

		#check-claim-modal p label{
	    display: inline-block;
	    width: 120px;
	    margin-left: 15px;
		}

		#check-claim-modal p span{

		}

		#check-claim-modal .modal-footer{
	    margin: 0 10px !important;
		}

		#check-claim-modal .modal-footer .btn-success{
	    background-image: -webkit-linear-gradient(top, #5cb85c 0%, #419641 100%);
	    background-image: -o-linear-gradient(top, #5cb85c 0%, #419641 100%);
	    background-image: -webkit-gradient(linear, left top, left bottom, from(#5cb85c), to(#419641));
	    background-image: linear-gradient(to bottom, #5cb85c 0%, #419641 100%);
	    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5cb85c', endColorstr='#ff419641', GradientType=0);
	    filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
	    background-repeat: repeat-x;
	    border-color: #3e8f3e;
		}

		.loader-container{
		    text-align: center;
		    padding-top:10px 20px;
		}

		.loader-container .loader {
		    border: 8px solid #b9d5e8;
		    border-top: 8px solid #3498db;
		    border-radius: 50%;
		    width: 60px;
		    height: 60px;
		    animation: spin 2s linear infinite;
		    margin: 0 auto;
		    margin-top: 20px !important;
		}

		@keyframes spin {
		    0% { transform: rotate(0deg); }
		    100% { transform: rotate(360deg); }
		}
	</style>

	<!-- {{ HTML::script('assets/claim/js/authService.js') }} -->

	<script type="text/javascript">
		$("[data-toggle='tooltip']").tooltip();
	</script>
@include('common.footer')
