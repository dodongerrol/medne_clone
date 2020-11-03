<div remove-dependent-checkbox-options-directive>

  <div class="employee-standalone-pro-wrapper">
		<h1 class="text-center weight-700">How would you like to proceed?</h1>
		<div class="employee-outcome-container">
			<span class="outcome-title">Please select one of the outcome:</span>
			<label class="review-container">
				<input type="checkbox" ng-model="checkboxes_options.replace" ng-click="checkboxOption(1)">
				<span class="review-prepare-template-text">To replace the leaving employee, I would like to pre-enroll the new
					joiner.</span>
				<span class="review-checkmark"></span>
			</label>
			<label class="review-container">
				<input type="checkbox" ng-model="checkboxes_options.reserve" ng-click="checkboxOption(2)">
				<span class="review-prepare-template-text">I'm not ready to pre-enroll the new joiner, please hold the seat for
					future hire.</span>
				<span class="review-prepare-template-text" style="font-size: 10px;line-height: 1;">*Note: Once this employee is
					removed, the occupied seat will move to a vacant seat.</span>
				<span class="review-checkmark"></span>
			</label>
			<!-- <label ng-if="refund_status == true && !isBasicPlan"  class="review-container" >
        <input type="checkbox" ng-model="checkboxes_options.remove" ng-click="checkboxOption(3)">
        <span class="review-prepare-template-text">Please remove the seat completely, and proceed for refund.</span>
				<span class="review-checkmark"></span>
			</label> -->
			<!-- ng-if="isBasicPlan"  -->
			<label class="review-container" >
        <input type="checkbox" ng-model="checkboxes_options.remove" ng-click="checkboxOption(3)">
				<span class="review-prepare-template-text">Please remove the seat completely.</span>
				<span class="review-checkmark"></span>
			</label>
		</div>
		<img ui-sref="member.emp-details" src="../assets/hr-dashboard/img/icons/cancel.png">
  </div>
  
  <div class="prev-next-buttons-container">
		<div class="container">
			<button ng-click="backBtn()" class="pull-left btn btn-info back-btn remove-emp-back-btn">BACK</button>
			<button ng-click="nextBtn()" class="pull-right btn btn-info next-wizard-button" ng-disabled="!checkboxes_options.replace && !checkboxes_options.reserve && !checkboxes_options.remove">NEXT</button>
		</div>
	</div>


	<div style="padding-top: 70px;border-radius: 0;" class="modal fade" id="remove-employee-confirm-modal" tabindex="-1" role="dialog"
		aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document" style="width: 620px;">
			<div class="modal-content" style="border-radius: 0;">
        <div class="confirm-modal-container">
          <div class="header-title">Remove Employee</div>

          <div class="body-content">
            <div ng-if="!isRemoveSuccess">
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
            <button ng-if="!isRemoveSuccess" class="btn-modal-confirm" ng-click="submitRemoveEmployee()">Confirm</button>
            <button ng-if="isRemoveSuccess" type="button" class="btn-modal-close" data-dismiss="modal" ng-click="doneConfirmModal()">Close</button>
          </div>
        </div>
			</div>
		</div>
	</div>

</div>