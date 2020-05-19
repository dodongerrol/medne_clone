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
            <div ng-if="!isRemoveSuccess && emp_details.wallet_opt == false">
              <p>Member’s wallet will not reflect the pro-rated amount, and will continue reflecting the initial allocated amount. Any unused credits will be returned to Company Available Credits on <b>02/06/2020</b>.</p>
              <p> Please confirm to proceed.</p>
            </div>

            <div ng-if="!isRemoveSuccess && emp_details.wallet_opt == true">
              <p>The <b>(Medical) Remaining Allocated Credits</b> of <b>SGD 290.40</b> & the <b>(Wellness) Remaining Allocated Credits</b> of <b>SGD 174.25</b> will be returned to respective <b>Company Available Credits</b> immediately after clicking “Confirm”.</p>
              <p>Any unused credits will be return to <b>Company Available Credits</b> on <b>02/06/2020</b>.</p>
              <p>Please confirm to proceed.</p>
            </div>

            <div ng-if="isRemoveSuccess" class="remove-success-div">
              <img src="../assets/hr-dashboard/img/remove-employee-success.png">  
              <p>This employee has been successfully removed.</p>
            </div>
          </div>

          <div class="btn-container">
            <button ng-if="!isRemoveSuccess" class="btn-modal-cancel" ng-click="closeConfirm()">Cancel</button>
            <button ng-if="!isRemoveSuccess" class="btn-modal-confirm" ng-click="confirmRemoveEmployee()">Confirm</button>
            <button ng-if="isRemoveSuccess" class="btn-modal-close" ng-click="doneConfirmModal()">Close</button>
          </div>
        </div>
			</div>
		</div>
	</div>
</div>
