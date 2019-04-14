@include('common.home_header')
{{ HTML::script('assets/care-plan/js/angular.min.js') }}
{{ HTML::script('assets/care-plan/js/calendar/moment/moment.js') }}
{{ HTML::style('assets/care-plan/css/sweetalert.css') }}
{{ HTML::style('assets/e-claim/css/bootstrap-slider.css') }}
{{ HTML::style('assets/hr-dashboard/css/daterangepicker.css') }}
{{ HTML::script('assets/care-plan/js/sweetalert.min.js') }}
{{ HTML::script('assets/e-claim/js/bootstrap-slider.js') }}
{{ HTML::script('assets/hr-dashboard/js/daterangepicker.js') }}
{{ HTML::style('assets/css/new-dashboard.css') }}

<style type="text/css">

	.tooltip-wallet{
    position: absolute;
    left: 165px;
    top: 45px;
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
	}

	.wallet-tooltip:hover .tooltip-wallet{
		display: block;
	}
	.label-custom
	{
		padding: 10px;
    background-color: #439057;
    font-size: 15px;
    display: inline-block;
    border-radius: 0;
    margin-top: 5px;
	}
</style>


<div ng-app="app">
	<div new-dashboard-directive>
		<div class="new-dashboard-container">
			<div class="col-md-12 no-padding">
				<div class="time-frame-container">
					<div class="col-md-12">
						<p class="color-black3 weight-700 no-margin activity-date-header">
							<span style="margin-right: 10px;">Select a timeframe: </span>

							<span class="year-selector"><a href="javascript:void(0)" ng-class="{'active' : year_active == 1}" ng-click="setYear('1')">This year</a></span>
							<span class="year-selector"><a href="javascript:void(0)" ng-class="{'active' : year_active == 2}" ng-click="setYear('2')">Last year</a></span>
							<span class="year-selector"><a href="javascript:void(0)" ng-class="{'active' : year_active == 3}" ng-click="showCustomDate(3)">Custom</a></span>
							<span class="year-selector"><a href="javascript:void(0)" ng-class="{'active' : year_active == 4}" ng-click="setDateToday()">Today</a></span>
						</p>

						<div class="showCustomPickerTrue">
							<div class="white-space-50"></div>

							<div class="slider-container">
								<input id="timeframe-range" type="text"/><br/>
							</div>
						</div>

						<div ng-if="showCustomPicker"  class="text-center" style="width: 100%;">
							<div class="custom-date-selector btn-custom-date" style="display: inline-block;">
								<div class="white-space-20"></div>
								<div class="white-space-20"></div>
								<div class="white-space-20"></div>
								<!-- <button class="btn btn-custom-today" ng-click="setDateToday()">Today</button> -->
								<button class="btn btn-custom-start">
									<i class="fa fa-calendar font-15" style="margin-right: 10px;"></i>
									<span id="rangePicker_start">01/01/2018</span>
									<i class="fa fa-caret-down font-15" style="margin-left: 10px;"></i>
								</button>

								<span style="margin: 0 10px;"><i class="fa fa-arrow-right"></i></span>

								<button class="btn btn-custom-end">
									<i class="fa fa-calendar font-15" style="margin-right: 10px;"></i>
									<span id="rangePicker_end">04/01/2018</span>
									<i class="fa fa-caret-down font-15" style="margin-left: 10px;"></i>
								</button>
							</div>
							<div class="white-space-20"></div>
							<div class="white-space-20"></div>
						</div>

						<div ng-if="showTodayDate" class="text-center" style="width: 100%;">
							<div class="custom-date-selector btn-custom-date"  style="display: inline-block;">
								<div class="white-space-20"></div>
								<div class="white-space-20"></div>
								<div class="white-space-20"></div>
								<button class="btn btn-custom-start" disabled style="cursor: not-allowed;">
									<i class="fa fa-calendar font-15" style="margin-right: 10px;"></i>
									<span class="rangePicker_start">01/01/2018</span>
								</button>
							</div>
							<div class="white-space-20"></div>
							<div class="white-space-20"></div>
						</div>
						
					</div>

					<div class="col-md-12">
						<div class="values-container">
							<div class="value-box">
								<h4 class="font-30 weight-700 color-gray" ng-bind="trans_data.total_transactions"></h4>
								<p class="no-margin">Transactions</p>
							</div>
							<div class="value-box wallet-tooltip" style="position: relative;">
								<h4 class="font-30 weight-700 color-gray">S$ <span ng-bind="trans_data.mednefits_wallet">2536</span></h4>
								<p class="no-margin">Mednefits Wallet</p>

								<p class="tooltip-wallet">Combine amount from Mednefits Fee and Mednefits Credit.</p>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="col-md-12 no-padding">
				<div class="dash-trans-history-container">
					<h4 class="color-gray weight-700" style="border-bottom: 2px solid #e1e1e1;padding-bottom: 20px;margin: 20px 50px;">Transaction History <a href="javascript:void(0)" class="pull-right view-trans-history"><i class="fa fa-arrow-right font-18"></i></a></h4>
					<table class="table trans-history-tbl">
						<thead>
							<tr>
								<th>DATE</th>
								<th>TRANSACTION ID</th>
								<th>NAME</th>
								<th>NRIC</th>
								<th>SERVICE/S</th>
								<th>MEDNEFITS FEE</th>
								<th>MEDNEFITS CREDIT</th>
								<th>CASH</th>
							</tr>
						</thead>

						<tbody>
							<tr ng-if="!loading" ng-repeat="trans in trans_data.transactions">
								<td ng-bind="trans.date_of_transaction"></td>
								<td>
									<span ng-bind="trans.transaction_id"></span>
									<br />
									<label class="label label-success label-custom" ng-if="trans.deleted"><span ng-bind="trans.transaction_status"></span></label>
								</td>
								<td ng-bind="trans.user_name"></td>
								<td ng-bind="trans.NRIC"></td>
								<td ng-bind="trans.procedure_name"></td>
								<td style="text-align: center;">
									<span>S$ <span ng-bind="trans.mednefits_fee"></span></span>
									<br>
									<span ng-if="trans.currency_type == 'myr'">(RM<span ng-bind="trans.mednefits_fee * trans.currency_amount | number: 2"></span>)</span>
								</td>
								<td style="text-align: center;">
									S$ <span ng-bind="trans.mednefits_credits"></span>
									<br>
									<span ng-if="trans.currency_type == 'myr'">(RM<span ng-bind="trans.mednefits_credits * trans.currency_amount"></span>)</span>
								</td>
								<td style="text-align: center;">
									S$ <span ng-bind="trans.cash">0</span>
									<br>
									<span ng-if="trans.currency_type == 'myr'">(RM<span ng-bind="trans.cash * trans.currency_amount | number: 2"></span>)</span>
								</td>
							</tr>

							<tr ng-if="loading">
								<td colspan="7" class="text-center">
									<h3>loading ...</h3>
								</td>
							</tr>

						</tbody>
					</table>

					<div class="button-wrapper">
						<button class="btn btn-view-invoice view-trans">View Invoice</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="newdash_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
	          <p id="newdash_message" class="text-center weight-700" style="color: #666;margin: 20px 0;">Message goes here.</p>
	          <p class="text-center weight-700" id="login-status" style="margin-left: 0;" hidden>
	          	<a href="javascript:void(0)" onclick="window.location.reload()" class="btn btn-primary" style="background: #1667AC!important">Reload</a>
	          </p>
	        </div>
	      </div>
	    </div>
		</div>
	</div>
</div>

{{ HTML::script('assets/new-dashboard/js/newDashboard.js') }}

<script type="text/javascript">
	$(function () {
		$('.view-trans-history').click(function( ){
		  	window.localStorage.setItem('pay-view', true);
		  	window.location.href = window.base_url + 'setting/main-setting';
		  });

		$('.view-trans').click(function( ){
		  	window.localStorage.setItem('invoice-view', true);
		  	window.location.href = window.base_url + 'setting/main-setting';
		  });

		$("[data-toggle='tooltip']").tooltip();
	})
</script>
@include('common.footer')