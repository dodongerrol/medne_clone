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
								<div>{{member_refund_details.calculations.pro_rated_refund}}/100 x [{{member_refund_details.calculations.days_used}}/{{member_refund_details.calculations.total_days}} x <span style="text-transform: uppercase">{{member_refund_details.currency_type}}</span> {{member_refund_details.calculations.price_per_employee}}]</div>
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
			<button ng-click="nextBtn()" class="pull-right btn btn-info next-wizard-button" ng-disabled="!checkboxes_options.replace && !checkboxes_options.reserve && !checkboxes_options.remove">NEXT</button>
		</div>
	</div>

</div>