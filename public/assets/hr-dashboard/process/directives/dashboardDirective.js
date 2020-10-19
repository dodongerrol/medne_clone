app.directive('dashboardDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state, hrSettings, dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log("dashboardDirective Runnning !");

				scope.options = {};
				scope.progress = {};
				// scope.spendingAccountType = 0;
				scope.plan_status = {};
				scope.dependents = {};
				scope.total_plan_due = 0;
				scope.total_spending = 0;
				scope.intro = {};
				scope.time_now = moment(moment(), 'HH:mm A');
				scope.isMorning = scope.time_now.isAfter(moment('5:00 AM', 'HH:mm A')) && scope.time_now.isBefore(moment('11:59 AM', 'HH:mm A'));
				scope.isAfternoon = scope.time_now.isAfter(moment('12:00 PM', 'HH:mm A')) && scope.time_now.isBefore(moment('6:00 PM', 'HH:mm A'));
				scope.isEvening = scope.time_now.isAfter(moment('6:01 PM', 'HH:mm A')) && scope.time_now.isBefore(moment('4:59 AM', 'HH:mm A').add('days', 1));
				scope.statementHide = true;
				scope.empStatementShow = false;
				scope.spending_account_status = {};
				scope.employee_plan_status = {};

				scope.companyAccountType = function () {
					scope.account_type = localStorage.getItem('company_account_type');
					if (scope.account_type === 'enterprise_plan') {
						$('.statement-hide').hide();
						scope.statementHide = false;
						scope.empStatementShow = true;
						// setTimeout();
					}
				}

				scope.goToEnroll = function () {
					localStorage.setItem('fromEmpOverview', false);

					// if(scope.spending_account_status.medical == true || scope.spending_account_status.wellness == true) {
					// 	$state.go('enrollment-options');
					// } else {
					// 	$state.go( 'create-team-benefits-tiers' );
					// }
					
					$state.go('create-team-benefits-tiers');
				}

				scope.selectSpending = (opt) => {
					scope.spendingAccountType = opt;
				}

				scope.initializeChart = function () {

					var total = 0;
					var completed = 0;

					if (scope.progress.in_progress != 0 && scope.progress.completed != 0) {
						total = scope.progress.total_employees;
						completed = (scope.progress.completed * 100) / total;
					}

					var data = [{
						values: [100 - completed, completed],
						type: 'pie',
						hole: .7,
						showlegend: false,
						hoverinfo: 'none',
						marker: {
							colors: ['B3DEF1', '33A2D4']
						},
						textinfo: 'none',
						sort: false
					}];
					var layout = {
						height: 280,
						width: 280
					};

					Plotly.newPlot('progressPieGraph', data, layout);
				}

				scope.addEmp = function () {
					if (scope.progress.added_purchase_status == true) {
						swal({
							title: "",
							text: "Employees have yet to enroll into the company benefits plan. Enroll them now.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Go",
							cancelButtonText: "Cancel",
							closeOnConfirm: true,
							customClass: "alertPendingEmp"
						},
							function (isConfirm) {
								if (isConfirm) {
									dashboardFactory.setHeadCountStatus(true);
									$state.go("enrollment-method");
								}
							});

					} else if (scope.progress.completed == scope.progress.total_employees) {
						$state.go("congratulations");
						dashboardFactory.setHeadCountStatus(true);
					} else {
						localStorage.setItem('fromEmpOverview', false);
						$state.go('enrollment-options');
						dashboardFactory.setHeadCountStatus(false);
					}
				}

				scope.task_lists = {};

				scope.getProgress = function () {
					hrSettings.getEnrollmentProgress()
						.then(function (response) {
							scope.progress = response.data.data;
							scope.spendingAccountType = 0;
							scope.companySpendingTotalDue();
						});
				}

				scope.getTaskList = function () {
					$('.task-load').show();
					$('.task-box-list').hide();

					hrSettings.getTaskList()
						.then(function (response) {
							scope.task_lists = response.data
							$('.task-load').hide();
							$('.task-box-list').fadeIn();
							scope.dashCredits();
						});
				}

				scope.formatMinusDate = function (date) {
					var new_date = moment(date).subtract(5, 'days');
					return new_date.format("DD/MM/YYYY");
				}

				scope.dashCredits = function () {
					$('.credit-load').show();
					$('.credit-box').hide();
					hrSettings.getCheckCredits('current_term')
						.then(function (response) {
							scope.credits = response.data;
							localStorage.setItem('currency_type', scope.credits.currency);
							$('.credit-load').hide();
							$('.credit-box').fadeIn();
						});
				}

				scope.showLoading = function () {
					$(".circle-loader").fadeIn();
					loading_trap = true;
				}

				scope.hideLoading = function () {
					setTimeout(function () {
						$(".circle-loader").fadeOut();
						loading_trap = false;
					}, 100)
				}

				scope.showGlobalModal = function (message) {
					$("#global_modal").modal('show');
					$("#global_message").text(message);
				}

				scope.getPlanStatus = function () {
					hrSettings.getPlanStatus()
						.then(function (response) {
							scope.plan_status = response.data;
						});
				}

				scope.getCompanyIntroMessage = function () {
					hrSettings.getIntroMessage()
						.then(function (response) {
							if (response.data.status) {
								scope.intro = response.data.data;
							}
						});
				}

				scope.companyPlanTotalDue = function () {
					hrSettings.companyPlanTotalDue()
						.then(function (response) {
							if (response.data.status) {
								scope.total_plan_due = response.data;
							}
						});
				}

				scope.companySpendingTotalDue = function () {
					hrSettings.companySpendingTotalDue()
						.then(function (response) {
							if (response.data.status) {
								scope.total_spending = response.data;
							}
						});
				}

				scope.companyDependents = function () {
					hrSettings.companyDependents()
						.then(function (response) {
							scope.dependents = response.data;
							// console.log(response);
						});
				}

				scope.getSpendingAccountStatus = function () {
					hrSettings.getSpendingAccountStatus()
						.then(function (response) {
							// console.log(response);
							scope.spending_account_status = response.data;
						});
				}

				scope._toggleInfo_ = function ( type ) {
					if ( type == 'pending' ) {
						$('.status-tooltip-wrapper.pending').toggle();
						$('.status-tooltip-wrapper.active').hide();
						$('.status-tooltip-wrapper.activated').hide();
					}
					if ( type == 'active' ) {
						$('.status-tooltip-wrapper.active').toggle();
						$('.status-tooltip-wrapper.pending').hide();
						$('.status-tooltip-wrapper.activated').hide();
					}
					if ( type == 'activated' ) {
						$('.status-tooltip-wrapper.activated').toggle();
						$('.status-tooltip-wrapper.active').hide();
						$('.status-tooltip-wrapper.pending').hide();
					}
				}

				scope.onLoad = function () {
					scope.showLoading();
					hrSettings.getSession()
						.then(function (response) {
							scope.hideLoading();
							scope.options.accessibility = response.data.accessibility;
							scope.dashCredits();
							scope.getProgress();
							scope.getTaskList();
							scope.getCompanyDetails();
							scope.getSpendingAccountStatus();
						});

					localStorage.setItem('method', 'input');
					dashboardFactory.setHeadCountStatus(false);
				}

				scope.getCompanyDetails = function () {
					hrSettings.getCompanyDetails()
						.then(function (response) {
							console.log(response);
							scope.options.company_name = response.data.data;
						});
				}

				scope.getEmployeeStatus = function( ) {
					hrSettings.getEmployeeStatus( )
					.then(function(response) {
					  // console.log(response);
					  scope.employee_plan_status = response.data.data;
					});
				};

				scope.onLoad();
				scope.getPlanStatus();
				scope.companyDependents();
				scope.getCompanyIntroMessage();
				scope.companyPlanTotalDue();
				scope.getEmployeeStatus();
				scope.companyAccountType();
			}
		}
	}
]);
