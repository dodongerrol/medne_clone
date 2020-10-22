<div replace-employee-input-directive>

  <div class="employee-replacement-wrapper">
		<div class="employee-details">
			<span class="text-center weight-700">Replacement</span>
			<h1 class="text-center weight-700 font-helvetica-medium">Employee details</h1>
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
					<label for="fname">Work Email</label>
					<input type="text" name="work-email" ng-model="replace_emp_details.email">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname">Mobile</label>
					<input valid-number id="area_code2" type="text" name="mobile" ng-model="replace_emp_details.mobile">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname">Postal Code</label>
					<input type="text" name="postal-code" ng-model="replace_emp_details.postal_code">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname">Start Date</label>
					<input type="text" name="start-date" class="start-date-datepicker-replace"
						ng-model="replace_emp_details.plan_start">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname" style="line-height: 1;margin-top: 5px;">Medical Credits</label>
					<label class="subtext">*If there are no credits to allocate, please key in 0</label>
					<input type="number" name="medical-credits" ng-model="replace_emp_details.medical_credits">
				</div>
				<div class="employee-input-wrapper">
					<label for="fname" style="line-height: 1;margin-top: 5px;">Wellness Credits</label>
					<label class="subtext">*If there are no credits to allocate, please key in 0</label>
					<input type="number" name="wellness-credits" ng-model="replace_emp_details.wellness_credits">
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
