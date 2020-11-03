app.directive('replaceDependentInputDirective', [
	'$state',
	'removeDependentFactory',
  'hrSettings',
  '$timeout',
  'dependentsSettings',
	function directive( $state, removeDependentFactory, hrSettings, $timeout, dependentsSettings ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'replaceDependentInputDirective running!' );
				scope.emp_details = removeDependentFactory.getEmployeeDetails();
				scope.replace_emp_details = {
					plan_start : moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').add('days', 1).format('DD/MM/YYYY'),
					dob : moment().format('DD/MM/YYYY')
        }
        var iti2 = null;
        console.log(scope.credit_status);

				scope.checkReplaceEmpForm	=	function( formData ){
					if (!formData.fullname) {
            swal('Error!', 'Full Name is required.', 'error');
            return false;
          }
          if (!formData.dob) {
            swal('Error!', 'Date of Birth is required.', 'error');
            return false;
          }
          if (!formData.relationship) {
            formData.relationship = null;
          }
          if (!formData.start_date) {
            swal('Error!', 'Start Date is required.', 'error');
            return false;
          }

          return true;
        }
        scope.checkEmail = function (email) {
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
          return regex.test(email);
        }
				scope.backBtn	=	function(){
					$state.go('dependent-remove.remove-emp-checkboxes');
				}
				scope.nextBtn	=	function(){
					if( scope.checkReplaceEmpForm(scope.replace_emp_details) == true ){
            var data  = scope.replace_emp_details;
            data.first_name = data.fullname;
            data.last_name = '';
            data.last_day_coverage = moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').format('YYYY-MM-DD');
            data.plan_start = moment(data.start_date, 'DD/MM/YYYY').format('YYYY-MM-DD');
            data.replace_id = scope.emp_details.user_id;
            data.customer_id = scope.selected_customer_id;
            console.log(data);
            scope.showLoading();
            dependentsSettings.replaceDependentService(data)
              .then(function(response){
                console.log(response);
                if(response.data.status == true){
                  scope.hideLoading();
                  removeDependentFactory.setReplaceEmployeeDetails(scope.replace_emp_details);
                  // $state.go('member-remove.health-spending-account-summary');
                  $state.go('employee-overview');
                }else{
                  scope.hideLoading();
                  if(response.data.credit_balance_exceed == true){
                    swal({
                      title: "Error:",
                      text: "You have reached your limit of <b>Available Credits.</b><br>Please contact us if you wish to allocate more credits.",
                      type: "error",
                      html: true,
                      showCancelButton: false,
                      confirmButtonText: "Close",
                      confirmButtonColor: "#0392CF",
                      closeOnConfirm: true,
                      customClass: "errorCreditsModal",
                    });
                  }else{
                    swal('Error!', response.data.message, 'error');
                  }
                }
              });
					}
        }
        
        setTimeout(() => {
          var dt = new Date();
          $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            endDate: dt
          });
          $('.datepicker').datepicker().on('hide', function (evt) {
            var val = $(this).val();
            if (val != "") {
              $(this).datepicker('setDate', val);
            }
          })
					$('.start-date-datepicker-replace').datepicker({
            format: 'dd/mm/yyyy',
          });
          $('.start-date-datepicker-replace').datepicker().on('hide', function (evt) {
            var val = $(this).val();
            if (val == "") {
              $('.start-date-datepicker-replace').datepicker('setDate', scope.emp_details.start_date);
            }
          })

          var settings = {
            separateDialCode: true,
            initialCountry: "SG",
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
          };
          var input2 = document.querySelector("#area_code2");
          iti2 = intlTelInput(input2, settings);
          iti2.setCountry("SG");
          scope.replace_emp_details.country_code = '65';
          input2.addEventListener("countrychange", function () {
            console.log(iti2.getSelectedCountryData());
            scope.replace_emp_details.country_code = iti2.getSelectedCountryData().dialCode;
            scope.replace_emp_details.mobile_area_code = iti2.getSelectedCountryData().dialCode;
            scope.replace_emp_details.mobile_area_code_country = iti2.getSelectedCountryData().iso2;
          });
        }, 500);

        scope.showLoading = function () {
          $(".circle-loader").fadeIn();
        };
        scope.hideLoading = function () {
          $timeout(function () {
            $(".circle-loader").fadeOut();
          }, 10);
        };
        
        scope.onLoad	=	function(){
					scope.hideLoading();
				}
				scope.onLoad();
			}
		}
	}
]);


