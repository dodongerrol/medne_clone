<div health-spending-account-confirm-directive>
  <div class="remove-health-spending-account-confirm-container">
    <div class="health-spending-account-wrapper">
      <span ng-bind="emp_details.member.Name">Filbert Tan</span>
      <h1 class="text-center">Health Spending Account</h1>
      <p class="text-center members-wallet-text">Do you want us to update the member’s wallet by reflecting the
      pro-rated credits amount and balance?</p>
      <p class="text-center members-wallet-note">(note: by doing so, this member might not be able to pay with credits if the current usage exceeded the pro-rated allocation)
      </p>
      <div class="spending-account-btn-container">
        <button ng-class="{ 'active' : emp_details.wallet_opt == false }" ng-click="selectOption( false )">NO</button>
        <button ng-class="{ 'active' : emp_details.wallet_opt == true }" ng-click="selectOption( true )">YES</button>
      </div>
    </div>
  </div>

  <div class="prev-next-buttons-container">
		<div class="container">
			<button ng-click="backBtn()" class="pull-left btn btn-info back-btn remove-emp-back-btn">BACK</button>
			<button ng-click="nextBtn()" class="pull-right btn btn-info next-wizard-button">NEXT</button>
		</div>
	</div>

  <div style="padding-top: 70px;border-radius: 0;" class="modal fade" id="remove-employee-confirm-modal" tabindex="-1" role="dialog"
		aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document" style="width: 620px;">
			<div class="modal-content" style="border-radius: 0;">
        <div class="confirm-modal-container">
          <div class="header-title">Remove Employee</div>

          <div class="body-content">
            <div ng-if="!isRemoveSuccess && emp_details.wallet_opt == false && emp_details.account_type != 'lite_plan'">
              <p>
                Member’s wallet will not reflect the pro-rated amount, and will continue reflecting the initial allocated amount. Any unused 
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">credits</span> 
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method != 'pre_paid'">medical credits</span> 
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method != 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">wellness credits</span> 
                will be returned to 
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">Company Available Credits</span> 
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method != 'pre_paid'">Company Medical Available Credits</span>
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method != 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">Company Wellness Available Credits</span>
                on <b>{{ emp_details.return_credits_date }}</b>.</p>
              <p> Please confirm to proceed.</p>
            </div>

            <div ng-if="!isRemoveSuccess && emp_details.wallet_opt == true && emp_details.account_type != 'lite_plan'">
              <p>
                The 
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid'">
                  <b>(Medical) Remaining Allocated Credits</b> of <b><span class="text-uppercase">{{ emp_details.summary.medical.currency_type }}</span> {{ emp_details.summary.medical.exceed ? emp_details.summary.medical.credits_to_be_returned : emp_details.summary.medical.remaining_allocated_credits }}</b> 
                </span>
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'"> & the</span>
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.wellness.plan_method == 'pre_paid'">
                   <b>(Wellness) Remaining Allocated Credits</b> of <b><span class="text-uppercase">{{ emp_details.summary.wellness.currency_type }}</span> {{ emp_details.summary.medical.exceed ? emp_details.summary.wellness.credits_to_be_returned : emp_details.summary.wellness.remaining_allocated_credits }}</b> 
                </span>
                will be returned to 
                <span ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">respective</span>
                <b ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">Company Available Credits</b> 
                <b ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method != 'pre_paid'">Company Medical Available Credits</b>
                <b ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method != 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">Company Wellness Available Credits</b>
                immediately after clicking “Confirm”.
              </p>
              <p ng-if="emp_details.summary.medical.exceed != true && emp_details.summary.wellness.exceed != true">
                Any unused credits will be returned to 
                <b ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">Company Available Credits</b> 
                <b ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method == 'pre_paid' && emp_details.summary.wellness.plan_method != 'pre_paid'">Company Medical Available Credits</b>
                <b ng-if="emp_details.account_type == 'lite_plan' && emp_details.summary.medical.plan_method != 'pre_paid' && emp_details.summary.wellness.plan_method == 'pre_paid'">Company Wellness Available Credits</b>
                on <b>{{ emp_details.return_credits_date }}</b>.</p>
              <p>Please confirm to proceed.</p>
            </div>

            <div ng-if="!isRemoveSuccess && emp_details.account_type == 'lite_plan'">
              <p>Are you sure you want to remove this employee?</p>
              <p>Please confirm to proceed.</p>
            </div>
            

            <div ng-if="isRemoveSuccess" class="remove-success-div">
              <img src="../assets/hr-dashboard/img/remove-employee-success.png">  
              <p>This employee has been successfully removed.</p>
            </div>
          </div>

          <div class="btn-container">
            <button ng-if="!isRemoveSuccess" class="btn-modal-cancel" ng-click="closeConfirm()">Cancel</button>
            <button ng-if="!isRemoveSuccess" class="btn-modal-confirm" ng-click="removeEmployeeRequests()">Confirm</button>
            <button ng-if="isRemoveSuccess" type="button" class="btn-modal-close" data-dismiss="modal" ng-click="doneConfirmModal()">Close</button>
          </div>
        </div>
			</div>
		</div>
	</div>
</div>

