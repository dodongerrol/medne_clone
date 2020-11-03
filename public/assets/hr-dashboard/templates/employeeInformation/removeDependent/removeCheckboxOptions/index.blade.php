<div remove-checkbox-options-directive>

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
			<label ng-if="refund_status == true && !isBasicPlan"  class="review-container" >
        <input type="checkbox" ng-model="checkboxes_options.remove" ng-click="checkboxOption(3)">
        <span class="review-prepare-template-text">Please remove the seat completely, and proceed for refund.</span>
				<span class="review-checkmark"></span>
			</label>
			<label ng-if="isBasicPlan" class="review-container" >
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

</div>