<div dependent-details-input-directive>
  <div class="remove-employee-wrapper">
		<h1 class="text-center weight-700">Remove employee</h1>
		<form>
			<div class="remove-employee-input-wrapper">
				<label for="fname">Full Name</label>
				<input ng-if="!isDeleteDependent" type="text" name="fname" ng-model="selectedEmployee.name" readonly>
				<input ng-if="isDeleteDependent" type="text" name="fname" ng-model="selectedDependent.name" readonly>
			</div>
			<div class="last-day-input-wrapper" style="display: inline-block;">
				<label for="fname">Last day of coverage</label>
				<input type="text" name="fname" class="last-day-coverage-datepicker" ng-model="selectedEmployee.last_day_coverage">
			</div>
		</form>
		<img ui-sref="member.emp-details" src="../assets/hr-dashboard/img/icons/cancel.png">
  </div>
  
  <div class="prev-next-buttons-container">
		<div class="container">
			<button ng-click="backBtn()" class="pull-left btn btn-info back-btn remove-emp-back-btn">CANCEL</button>
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