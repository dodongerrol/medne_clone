<div employee-details-input-directive>
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
		<img ng-click="removeBackBtn()" src="../assets/hr-dashboard/img/icons/cancel.png">
  </div>
  
  <div class="prev-next-buttons-container">
		<div class="container">
			<button ng-click="backBtn()" class="pull-left btn btn-info back-btn remove-emp-back-btn">CANCEL</button>
			<button ng-click="nextBtn()" class="pull-right btn btn-info next-wizard-button">NEXT</button>
		</div>
	</div>
</div>