<div class="payment-wrapper center-align" callback-directive>

	<div id="fail-form">
		<div class="row">
			<div class="col s12">
				<div class="white-space-50"></div>
				<h1 class="font-40 weight-500 color-dark-grey">Unsuccessful</h1>
			</div>
		</div>

		<div class="row">
			<div class="col s12">
				<div class="white-space-20"></div>
				<i class="material-icons font-80 red-text text-lighten-1">cancel</i>
				<div class="white-space-20"></div>
			</div>
		</div>

		<div class="row margin-bottom-30">
			<div class="col s12">
				<h4 class="color-dark-grey weight-500">Sorry we are unable to process your payment.<br>Kindly please click the back button to try again</h4>
				<h5 class="color-dark-grey weight-500" ng-bind="$stateParams.message"></h5>
			</div>
		</div>

		<div class="row margin-bottom-30">
	        <div class="input-field col s12 center-align">
	          <button id="btn-four" class="btn btn-large blue white-text font-20 radius-8" >BACK</button>
	        </div>
		</div>	
	</div>
</div>
