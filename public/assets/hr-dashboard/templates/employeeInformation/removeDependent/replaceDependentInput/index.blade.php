<div replace-dependent-input-directive>

  <div class="employee-replacement-wrapper">
		<div class="employee-details">
			<span class="text-center weight-700">Replacement</span>
			<h1 class="text-center weight-700 font-helvetica-medium">Dependent details</h1>
			<form>
				<div class="employee-input-wrapper">
					<label for="fname">Full Name</label>
					<input disabled-specific-characters type="text" name="fname" ng-model="replace_emp_details.fullname">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname">Date of Birth</label>
					<input type="text" name="datebirth" class="datepicker" ng-model="replace_emp_details.dob">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname">Relationship</label>
					<select ng-model="replace_emp_details.relationship">
						<option value="spouse">Spouse</option>
						<option value="child">Child</option>
						<option value="family">Family</option>
						<option value="parent">Parent</option>
					</select>
					<img class="down-arrow" src="../assets/hr-dashboard/img/icons/down-arrow.svg">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname">Start Date</label>
					<input type="text" name="start-date" class="start-date-datepicker-replace"
						ng-model="replace_emp_details.start_date">
				</div>
			</form>
		</div>

		<img ng-click="removeBackBtn()" src="../assets/hr-dashboard/img/icons/cancel.png">
	</div>

  <div class="prev-next-buttons-container">
		<div class="container">
			<button ng-click="backBtn()" class="pull-left btn btn-info back-btn remove-emp-back-btn">BACK</button>
			<button ng-click="nextBtn()" class="pull-right btn btn-info next-wizard-button">NEXT</button>
		</div>
	</div>
</div>
