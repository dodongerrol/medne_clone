<div class="remove-emp-health-summary-wrapper" health-spending-account-summary-directive>
  
  <div class="account-summary-wrapper" >
		<span class="account-summary-name weight-700" ng-bind="selectedEmployee.name">Calvin Lee</span>
		<h1 class="text-center weight-700">Health Spending Account Summary</h1>
		<div ng-if="selectedEmployee.emp_status != 'deleted' && selectedEmployee.schedule != true" class="account-summary-pro-rated weight-700 text-center" style="margin-left: 30px;">Pro-rated allocation from
			<span>
				<strong class="font-helvetica-medium">Start</strong> -
				<button class="btn btn-custom-start">
					<i class="fa fa-calendar"></i>
					<span id="rangePicker_start">01/01/2018</span>
					<i class="fa fa-caret-down"></i>
				</button>
			</span> to
			<span>
				<strong class="font-helvetica-medium">End</strong> -
				<button class="btn btn-custom-end">
					<i class="fa fa-calendar"></i>
					<span id="rangePicker_end">04/01/2018</span>
					<i class="fa fa-caret-down"></i>
				</button>
			</span>
			<button class="btn btn-calculate" ng-click="calculate()">Calculate</button>
		</div>

		<div ng-if="selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true" class="account-summary-pro-rated weight-700 text-center">Pro-rated allocation from
			<span><strong class="font-helvetica-medium">Start</strong> - <span class="account-summary-date"
					ng-bind="formatDate(health_spending_summary.date.pro_rated_start, null, 'DD/MM/YYYY')">01/11/2018</span></span> to
			<span><strong class="font-helvetica-medium">End</strong> - <span class="account-summary-date"
					ng-bind="formatDate(health_spending_summary.date.pro_rated_end, null, 'DD/MM/YYYY')">01/01/2019</span></span>
		</div>

		<div class="account-summary-usage weight-700 text-center">Usage from
			<span><strong class="font-helvetica-medium"> Start</strong> - <span class="account-summary-date"
					ng-bind="health_spending_summary.date.usage_start">01/11/2018</span></span> to
			<span> <strong class="font-helvetica-medium">Current</strong> - <span class="account-summary-date"
					ng-bind="health_spending_summary.date.usage_end">01/12/2018</span></span>
		</div>

		<div class="medical-wellness-container" ng-class="{'display-inline-block' : !health_spending_summary.medical.status || !health_spending_summary.wellness.status}">
			<div ng-if="health_spending_summary.medical.status == true" class="medical-container">
				<h4 class="font-helvetica-medium text-center">Medical Spending Account</h4>
				<div class="spending-account-details">
					<div class="inital-allocation-container weight-700">
            <strong ng-if="emp_details.account_type != 'lite_plan' || ( emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method != 'pre_paid')" class="font-helvetica-medium">Initial Allocation</strong>
            <strong ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid'" class="font-helvetica-medium">Initial Allocated Credits</strong>
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.medical.initial_allocation">1,000.00</span></span>
					</div>
					<div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.medical.pro_allocation_status == false}" class="pro-rated-container weight-700">
						<strong ng-if="emp_details.account_type != 'lite_plan' || ( emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method != 'pre_paid')" class="font-helvetica-medium">Pro-rated Allocation</strong>
						<strong ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid'" class="font-helvetica-medium">Pro-rated Allocated Credits</strong>
						<span class="font-helvetica-medium"><span class="currency-type"
								ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.medical.pro_allocation">169.86</span></span>
					</div>
					<div class="current-usage-container weight-700">
						<strong class="font-helvetica-medium">Current Usage</strong>
						<span ng-if="health_spending_summary.medical.exceed == false" class="on-track"><span class="currency-type"
								ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.medical.current_usage">700.00</span></span>
						<span ng-if="health_spending_summary.medical.exceed == true" class="exceed"><span class="currency-type"
								ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.medical.current_usage">700.00</span></span>
					</div>
					<div class="spent-container weight-700">
						Spent
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.medical.spent">650.00</span></span>
					</div>
					<div class="pending-claim-container weight-700">
						Pending claim
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.medical.pending_e_claim">50.00</span></span>
					</div>
					<div class="balance-container weight-700 font-helvetica-medium" style="margin-top: 10px;">
						<strong class="font-helvetica-medium">Balance</strong>
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.medical.balance">700.00</span></span>
					</div>
        </div>
        
        <div class="spending-details-separator"></div>

				<span ng-if="health_spending_summary.medical.exceed == false"
					class="spending-account-status on-track weight-700">On Track</span>
				<span ng-if="health_spending_summary.medical.exceed == true"
          class="spending-account-status exceed weight-700">Exceed</span>
          
        <div ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid'" class="spending-details-separator"></div>

        <div ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid'" class="balance-container grid-width-100 weight-700 font-helvetica-medium" style="margin-top: 10px;">
          <strong class="font-helvetica-medium">Return to Company Medical Available Credits: </strong>
        </div>

        <div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.medical.pro_allocation_status == false}" ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid'" class="balance-container weight-700" style="margin-top: 10px;">
          <strong class="font-helvetica-medium">
						<span class="p-label-value">
							{{ (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.medical.exceed == true ? 'Returned' : 'Remaining' }}
							Allocated Credits
						</span>
            <span class="p-tooltip">
              <img ng-click="toggleSumamryTooltipDrop('medical')" src="../assets/hr-dashboard/img/summary-info-tooltip.png">

              <div ng-if="isMedicalDropShow" class="dropdown-tooltip-container">
                <p class="tooltip-title">
                  (Medical) Remaining Allocated Credits
                </p>
                <p>
                  This is the difference between (Medical) Initial Allocated Credits and (Medical) Pro-rated Allocated Credits.<br>
                  <b>(Initial Allocated Credits minus Pro-rated Allocated Credits).</b>
                </p>
                <div class="btn-container">
                  <button ng-click="toggleSumamryTooltipDrop('medical')">Got it</button>
                </div>
              </div>
            </span>
						<p ng-if="health_spending_summary.medical.remaining_credits_date">(returned on {{ health_spending_summary.medical.remaining_credits_date }})</p>

          </strong>
          <span>
						<span ng-bind="health_spending_summary.medical.currency_type" class="currency-type"></span> 
						<span>{{ (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.medical.exceed == true ? health_spending_summary.medical.credits_to_be_returned : health_spending_summary.medical.remaining_allocated_credits}}</span>
          </span>
				</div>

				<div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true)}" ng-if="health_spending_summary.medical.exceed == true && emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid'" class="balance-container weight-700" style="margin-top: 10px;margin-bottom: 10px;">
          <strong class="font-helvetica-medium">
            <span class="p-label-value">Deduct: Exceeded Credits</span>
          </strong>
          <span>
						<div class="exceed-credit-border">
							(
							<span ng-bind="health_spending_summary.medical.currency_type" class="currency-type"></span> 
							<span ng-bind="health_spending_summary.medical.exceed_balance">84.62</span>
							)
						</div>
          </span>
				</div>

				<div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true)}" ng-if="health_spending_summary.medical.exceed == true && emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid'" class="balance-container weight-700 font-helvetica-medium" style="margin-top: 0px;">
          <strong class="font-helvetica-medium">
            <span class="p-label-value">Credits to be returned</span>
          </strong>
          <span>
						<span ng-bind="health_spending_summary.medical.currency_type" class="currency-type"></span> 
						<span ng-bind="health_spending_summary.medical.credits_to_be_returned">84.62</span>
          </span>
				</div>
				


        <div ng-if="!health_spending_summary.medical.exceed && emp_details.account_type == 'lite_plan' && health_spending_summary.medical.plan_method == 'pre_paid' && (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true)" class="balance-container weight-700" style="margin-top: 10px;">
          <strong class="font-helvetica-medium">
            <span class="p-label-value">Balance</span>
            <span class="p-tooltip">
              <img ng-click="toggleSumamryTooltipDrop('medical-balance')" src="../assets/hr-dashboard/img/summary-info-tooltip.png">

              <div ng-if="isMedicalBalanceDropShow" class="dropdown-tooltip-container">
                <p class="tooltip-title">
                  (Medical) Balance
                </p>
                <p>
                  On the last day of coverage, the Balance amount shown here will be the amount returned to Company Medical Available Credits.
                </p>
                <div class="btn-container">
                  <button ng-click="toggleSumamryTooltipDrop('medical-balance')">Got it</button>
                </div>
              </div>
            </span>
						<p ng-if="health_spending_summary.medical.balance_credits_date">({{ health_spending_summary.medical.returned_balance_status ? 'returned on' : 'as of' }} {{ health_spending_summary.medical.balance_credits_date }})</p>
          </strong>
          <span>
            <span ng-bind="health_spending_summary.medical.currency_type" class="currency-type"></span> 
            <span ng-bind="health_spending_summary.medical.balance">84.62</span>
          </span>
        </div>
      </div>

      <div ng-if="health_spending_summary.medical.status == true && health_spending_summary.wellness.status == true" class="separator"></div>

			<div ng-if="health_spending_summary.wellness.status == true" class="wellness-container">
				<h4 class="font-helvetica-medium text-center">Wellness Spending Account</h4>
				<div class="spending-account-details">
					<div class="inital-allocation-container weight-700">
            <strong ng-if="emp_details.account_type != 'lite_plan' || ( emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method != 'pre_paid')" class="font-helvetica-medium">Initial Allocation</strong>
              <strong ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid'" class="font-helvetica-medium">Initial Allocated Credits</strong>
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.wellness.initial_allocation">1,000.00</span></span>
					</div>
					<div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.wellness.pro_allocation_status == false}" class="pro-rated-container weight-700">
            <strong ng-if="emp_details.account_type != 'lite_plan' || ( emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method != 'pre_paid')" class="font-helvetica-medium">Pro-rated Allocation</strong>
						<strong ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid'" class="font-helvetica-medium">Pro-rated Allocated Credits</strong>
						<span class="font-helvetica-medium"><span class="currency-type"
								ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.wellness.pro_allocation">169.86</span></span>
					</div>
					<div class="current-usage-container weight-700">
						<strong class="font-helvetica-medium">Current Usage</strong>
						<span ng-if="health_spending_summary.wellness.exceed == false" class="on-track"><span class="currency-type"
								ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.wellness.current_usage">700.00</span></span>
						<span ng-if="health_spending_summary.wellness.exceed == true" class="exceed"><span class="currency-type"
								ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.wellness.current_usage">700.00</span></span>
					</div>
					<div class="spent-container weight-700">
						Spent
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.wellness.spent">650.00</span></span>
					</div>
					<div class="pending-claim-container weight-700">
						Pending claim
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.wellness.pending_e_claim">50.00</span></span>
					</div>
					<div class="balance-container weight-700 font-helvetica-medium" style="margin-top: 10px;">
						<strong class="font-helvetica-medium">Balance</strong>
						<span><span class="currency-type" ng-bind="selectedEmployee.spending_account.currency_type"></span> <span
								ng-bind="health_spending_summary.wellness.balance">700.00</span></span>
					</div>
        </div>
        
        <div class="spending-details-separator"></div>

				<span ng-if="health_spending_summary.wellness.exceed == false"
					class="spending-account-status on-track weight-700">On Track</span>
				<span ng-if="health_spending_summary.wellness.exceed == true"
          class="spending-account-status exceed weight-700">Exceed</span>
          
        <div ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid'" class="spending-details-separator"></div>

        <div ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid'" class="balance-container grid-width-100 weight-700 font-helvetica-medium" style="margin-top: 10px;">
          <strong class="font-helvetica-medium">Return to Company Wellness Available Credits: </strong>
        </div>

        <div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.wellness.pro_allocation_status == false}" ng-if="emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid'" class="balance-container weight-700" style="margin-top: 10px;">
          <strong class="font-helvetica-medium">
            <span class="p-label-value">
							{{ (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.wellness.exceed == true ? 'Returned' : 'Remaining' }}
							Allocated Credits
						</span>
            <span class="p-tooltip">
              <img ng-click="toggleSumamryTooltipDrop('wellness')" src="../assets/hr-dashboard/img/summary-info-tooltip.png">

              <div ng-if="isWellnessDropShow" class="dropdown-tooltip-container">
                <p class="tooltip-title">
                  (Wellness) Remaining Allocated Credits
                </p>
                <p>
                  This is the difference between (Wellness) Initial Allocated Credits and (Wellness) Pro-rated Allocated Credits.<br>
                  <b>(Initial Allocated Credits minus Pro-rated Allocated Credits).</b>
                </p>
                <div class="btn-container">
                  <button ng-click="toggleSumamryTooltipDrop('wellness')">Got it</button>
                </div>
              </div>
            </span>
						<p ng-if="health_spending_summary.wellness.remaining_credits_date">(returned on {{ health_spending_summary.wellness.remaining_credits_date }})</p>

          </strong>
          <span>
            <span ng-bind="health_spending_summary.wellness.currency_type" class="currency-type"></span> 
            <span>{{ (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true) && health_spending_summary.wellness.exceed == true ? health_spending_summary.wellness.credits_to_be_returned : health_spending_summary.wellness.remaining_allocated_credits}}</span>
          </span>
				</div>
				
				<div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true)}" ng-if="health_spending_summary.wellness.exceed == true && emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid'" class="balance-container weight-700" style="margin-top: 10px;margin-bottom: 10px;">
          <strong class="font-helvetica-medium">
            <span class="p-label-value">Deduct: Exceeded Credits</span>
          </strong>
          <span>
						<div class="exceed-credit-border">
							(
							<span ng-bind="health_spending_summary.wellness.currency_type" class="currency-type"></span> 
							<span ng-bind="health_spending_summary.wellness.exceed_balance">84.62</span>
							)
						</div>
          </span>
				</div>

				<div ng-class="{'isHide' : (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true)}" ng-if="health_spending_summary.wellness.exceed == true && emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid'" class="balance-container weight-700 font-helvetica-medium" style="margin-top: 0px;">
          <strong class="font-helvetica-medium">
            <span class="p-label-value">Credits to be returned</span>
          </strong>
          <span>
						<span ng-bind="health_spending_summary.wellness.currency_type" class="currency-type"></span> 
						<span ng-bind="health_spending_summary.wellness.credits_to_be_returned">84.62</span>
          </span>
				</div>

        <div ng-if="!health_spending_summary.wellness.exceed && emp_details.account_type == 'lite_plan' && health_spending_summary.wellness.plan_method == 'pre_paid' && (selectedEmployee.emp_status == 'deleted' || selectedEmployee.schedule == true)" class="balance-container weight-700" style="margin-top: 10px;">
          <strong class="font-helvetica-medium">
            <span class="p-label-value">Balance</span>
            <span class="p-tooltip">
              <img ng-click="toggleSumamryTooltipDrop('wellness-balance')" src="../assets/hr-dashboard/img/summary-info-tooltip.png">

              <div ng-if="isWellnessBalanceDropShow" class="dropdown-tooltip-container">
                <p class="tooltip-title">
                  (Wellness) Balance
                </p>
                <p>
                  On the last day of coverage, the Balance amount shown here will be the amount returned to Company Wellness Available Credits.
                </p>
                <div class="btn-container">
                  <button ng-click="toggleSumamryTooltipDrop('wellness-balance')">Got it</button>
                </div>
              </div>
            </span>
						<p ng-if="health_spending_summary.wellness.balance_credits_date">({{ health_spending_summary.wellness.returned_balance_status ? 'returned on' : 'as of' }} {{ health_spending_summary.wellness.balance_credits_date }})</p>
          </strong>
          <span>
            <span ng-bind="health_spending_summary.wellness.currency_type" class="currency-type"></span> 
            <span ng-bind="health_spending_summary.wellness.balance">84.62</span>
          </span>
        </div>
			</div>
		</div>
		<img ng-if="selectedEmployee.emp_status != 'deleted' && selectedEmployee.schedule != true" ui-sref="member-remove.emp-details" src="../assets/hr-dashboard/img/icons/cancel.png">
	</div>


  <div ng-if="selectedEmployee.emp_status != 'deleted' && selectedEmployee.schedule != true" class="prev-next-buttons-container">
		<div class="container">
			<button ng-click="backBtn()" class="pull-left btn btn-info back-btn remove-emp-back-btn">BACK</button>
			<button ng-click="nextBtn()" class="pull-right btn btn-info next-wizard-button" ng-disabled="!isCalculated">NEXT</button>
		</div>
	</div>
</div>