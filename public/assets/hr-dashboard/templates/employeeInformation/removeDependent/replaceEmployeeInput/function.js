app.directive('replaceEmployeeInputDirective', [
	'$state',
	'removeEmployeeFactory',
  'hrSettings',
  '$timeout',
	function directive( $state, removeEmployeeFactory, hrSettings, $timeout ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log( 'replaceEmployeeInputDirective running!' );
				scope.emp_details = removeEmployeeFactory.getEmployeeDetails();
				scope.replace_emp_details = {
					plan_start : moment(scope.emp_details.last_day_coverage, 'DD/MM/YYYY').add('days', 1).format('DD/MM/YYYY'),
					dob : moment().format('DD/MM/YYYY')
        }
        var iti2 = null;
        console.log(scope.credit_status);

				scope.checkReplaceEmpForm	=	function( formData ){
					console.log(formData);
					if (!formData.fullname) {
            swal('Error!', 'Full Name is required.', 'error');
            return false;
          }
          if (!formData.dob) {
            swal('Error!', 'Date of Birth is required.', 'error');
            return false;
          }
          if (!formData.mobile && !formData.email) {
            swal('Error!', 'Phone Number or Email Address is required.', 'error');
            return false;
          }
          if (formData.email) {
          //   swal('Error!', 'Email is required.', 'error');
          //   return false;
          // } else {
            if (scope.checkEmail(formData.email) == false) {
              swal('Error!', 'Email is invalid.', 'error');
              return false;
            }
          }
          if (formData.mobile) {
          //   swal('Error!', 'Mobile Number is required.', 'error');
          //   return false;
          // } else {
            // console.log( iti.getSelectedCountryData().iso2 );
            if (iti2.getSelectedCountryData().iso2 == 'sg' && formData.mobile.length < 8) {
              swal('Error!', 'Mobile Number for your country code should be 8 digits.', 'error');
              return false;
            }
            if (iti2.getSelectedCountryData().iso2 == 'my' && formData.mobile.length < 10) {
              swal('Error!', 'Mobile Number for your country code should be 10 digits.', 'error');
              return false;
            }
            if (iti2.getSelectedCountryData().iso2 == 'ph' && formData.mobile.length < 9) {
              swal('Error!', 'Mobile Number for your country code should be 9 digits.', 'error');
              return false;
            }
          }
          if (!formData.plan_start) {
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
					$state.go('member-remove.remove-emp-checkboxes');
				}
				scope.nextBtn	=	function(){
					if( scope.checkReplaceEmpForm(scope.replace_emp_details) == true ){
            var data  = {
              email: scope.replace_emp_details.email,
              mobile: scope.replace_emp_details.mobile,
              medical: scope.replace_emp_details.medical_credits,
              wellness: scope.replace_emp_details.wellness_credits,
            }
            scope.showLoading();
            hrSettings.checkReplaceEmpForm(data)
              .then(function(response){
                console.log(response);
                if(response.data.status == false){
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
                }else{
                  scope.showLoading();
                  removeEmployeeFactory.setReplaceEmployeeDetails(scope.replace_emp_details);
                  $state.go('member-remove.health-spending-account-summary');
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


