{{ HTML::style('assets/care-plan/css/sweetalert.css') }}
{{ HTML::style('assets/e-claim/css/bootstrap-slider.css') }}
{{ HTML::style('assets/hr-dashboard/css/daterangepicker.css') }}
{{ HTML::style('assets/css/transac-view-page.css') }}


<div class="trans-view-page-container">
	<div class="col-md-12 no-padding">
		<div class="time-frame-container" >
			<span class="trans-tbl-box" >
				<h4 class="color-gray weight-700" style="padding: 10px 15px;">Transaction History</h4>

				<div class="col-md-12">
					<div class="slider-wrapper">
						<p class="color-black3 weight-700 no-margin activity-date-header">
							<span style="margin-right: 10px;">Select a timeframe: </span>

							<span class="year-selector"><a href="javascript:void(0)" onclick="setYear(1)">This year</a></span>
							<span class="year-selector"><a href="javascript:void(0)" onclick="setYear(2)">Last year</a></span>
							<span class="year-selector"><a href="javascript:void(0)" onclick="showCustomDate(3)">Custom</a></span>
						</p>

						<div class="showCustomPickerTrue">
							<div class="white-space-50"></div>

							<div class="slider-container">
								<input id="timeframe-range" type="text"/><br/>
							</div>
						</div>

						<div class="showCustomPickerFalse text-center" style="width: 100%;display: none">
							<div class="custom-date-selector btn-custom-date" style="display: inline-block;">
								<div class="white-space-20"></div>
								<div class="white-space-20"></div>
								<button class="btn btn-custom-start">
									<i class="fa fa-calendar font-15" style="margin-right: 10px;"></i>
									<span id="rangePicker_start">01/01/2018</span>
									<i class="fa fa-caret-down font-15" style="margin-left: 10px;"></i>
								</button>

								<span style="margin: 0 10px;"><i class="fa fa-arrow-right"></i></span>

								<button class="btn btn-custom-end">
									<i class="fa fa-calendar font-15" style="margin-right: 10px;"></i>
									<span id="rangePicker_end">04/01/2018</span>
									<i class="fa fa-caret-down font-15" style="margin-left: 10px;"></i>
								</button>
							</div>
							<div class="white-space-20"></div>
							<div class="white-space-20"></div>
						</div>

						<div class="col-md-6 no-padding search-wrapper">
							<form>
								<div class="input-group">
									<input class="form-control font-13 search-table" placeholder="Search"/>
								      <span class="input-group-btn">
								        <button class="btn btn-default btn-search-tbl" type="button"><i class="fa fa-search font-18"></i></button>
								      </span>
								</div>
							</form>
						</div>
						<div class="col-md-6 no-padding text-right">
							<a href="javascript:void(0)" id="trans-history-down" class="btn exportBtn"><i class="glyphicon glyphicon-file"></i>  Export as .PDF</a>
							<a href="javascript:void(0)" id="trans-csv-down" class="btn exportBtn"><i class="glyphicon glyphicon-file"></i>  Export as .CSV</a>
						</div>
					</div>
				</div>

				<table id="pdf-print" class="table trans-history-tbl">
					<thead>
						<tr>
							<th>DATE</th>
							<th>TRANSACTION ID</th>
							<th>NAME</th>
							<!-- <th>NRIC</th> -->
							<th>SERVICE/S</th>
							<th>MEDNEFITS FEE</th>
							<th>MEDNEFITS CREDIT</th>
							<th>CASH</th>
						</tr>
					</thead>

					<tbody>

					</tbody>
				</table>
			</span>

			<span class="trans-invoice-box" hidden>
				<a href="javascript:void(0)" id="hide-trans-history-invoice" class="color-black3"><i class="fa fa-chevron-left color-black3"></i> Back</a>
				<div class="white-space-20"></div>
				<div id="invoice-print-dl" class="invoice-history-dl-wrapper">
					<div class="inv-header">
						<div class="col-md-6">
							<div class="white-space-10"></div>
							<p class="weight-700 font-medium color-black3">Transaction for <span>1 December</span> - <span>31 December</span> <span>2017</span></p>
							<div class="white-space-10"></div>
							<div class="white-space-5"></div>
							<p class="weight-700 font-medium color-black3">Health Partner Transaction History</p>
							<p class="weight-700 font-medium color-black3">Medicloud Clinic Pte Ltd</p>
							<div class="white-space-10"></div>
							<div class="white-space-5"></div>
							<p class="">1 Raffles Road</p>
							<p class="">#01-88</p>
							<p class="">Singapore 000948</p>
							<p class="">+65 6254 7889</p>
							<p class="">clinic@mednefits.com</p>
						</div>
						<div class="col-md-6 text-right">
							<img src="{{ URL::asset('assets/images/mednefits logo v3 (blue) LARGE.png') }}" style="width: 150px;">
							<div class="white-space-10"></div>
							<div class="white-space-5"></div>
							<p class="weight-700 font-medium color-black3">Medicloud Pte Ltd</p>
							<p class="">7 Temasek Boulevard</p>
							<p class="">#18-02 Suntec Tower one</p>
							<p class="">038987</p>
							<p class="">Singapore</p>
							<div class="white-space-10"></div>
							<div class="white-space-5"></div>
							<p class="">+65 6254 7889</p>
							<p class="">mednefits.com</p>
						</div>
					</div>

					<div class="stats-box">
						<div class="flex-1">
							<p class="">Total Transactions</p>
							<p class="color-blue total-trans-num"></p>
						</div>
						<div class="flex-5">
							<p class="">Mednefits Wallet</p>
							<p class="color-blue">S$ <span class="medni-wallet-num">189.40</span></p>
						</div>
					</div>

					<table id="pdf-print-invoice" class="table trans-invoice-tbl" style="width: 100%;">
						<thead>
							<tr>
								<th>DATE</th>
								<th>TRANSACTION ID</th>
								<th>NAME</th>
								<!-- <th>NRIC</th> -->
								<th>SERVICE/S</th>
								<th>MEDNEFITS FEE</th>
								<th>MEDNEFITS CREDIT</th>
								<th>CASH</th>
							</tr>
						</thead>

						<tbody>

						</tbody>
					</table>
					
				</div>
			</span>
		</div>
	</div>
</div>


{{ HTML::script('assets/care-plan/js/sweetalert.min.js') }}
{{ HTML::script('assets/e-claim/js/bootstrap-slider.js') }}
{{ HTML::script('assets/hr-dashboard/js/daterangepicker.js') }}
{{ HTML::script('assets/directives/transactionHistoryPage.js') }}

<script type="text/javascript">
	window.localStorage.setItem('pay-view', false);
</script>
