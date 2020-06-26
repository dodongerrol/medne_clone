<div refund-summary-directive>

	<div id="refund-summary" class="refund-summary-wrapper">
		<div class="refund-container"> 
			<div class="refund-header">
				<h1 class="font-25">Refund Summary</h1>
			</div>
			<div class="refund-sub-header">
				<p class="font-16">
					Please check and confirm details. Once successfully confirmed, <br>
					the total refund amount show here will be refunded to your company account.
				</p>
			</div>
			<div class="refund-main">
				<div class="main-item font-16">
					<div class="main-section-1">
						<label class="font-20">Details</label>
						<div>Plan type: {{member_refund_details.account_type}}</div>
						<div>Unutilised period - {{member_refund_details.unutilised_start_date}} to {{member_refund_details.unutilised_end_date}}</div>
					</div>
					<div class="main-section-2">
						<div class="section-2-left">
							<div>Total refund</div>
						</div>
						<div class="section-2-right">
							<div style="text-transform: uppercase">{{member_refund_details.currency_type}} {{member_refund_details.amount}}</div>
							<small ng-click="showCalculation = !showCalculation" class="font-12">See calculation <i class="fa font-10" ng-class="[{'fa-chevron-down': showCalculation == true}, {'fa-chevron-right' : showCalculation == false}]" aria-hidden="true"></i></small>
							<div ng-if="showCalculation" class="see-calculation font-12">
								<div>Refund = {{member_refund_details.calculations.pro_rated_refund}}% of unutilised period</div>
								<div>{{member_refund_details.calculations.pro_rated_refund}}/100 x [{{member_refund_details.calculations.days_unused}}/{{member_refund_details.calculations.total_days}} x <span style="text-transform: uppercase">{{member_refund_details.currency_type}}</span> {{member_refund_details.calculations.price_per_employee}}]</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="refund-side-main">
				<div class="side-main-item">
					<label for="" class="font-20">Total Refund</label>
					<label for="" class="font-25" style="text-transform: uppercase">{{member_refund_details.currency_type}} {{member_refund_details.amount}}</label>
				</div>
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
            <div ng-if="!isRemoveSuccess">
							<p>Are you sure you want to remove this employee completely?</p>  

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