app.directive("mobileExerciseDirective", [
  "$http",
  "$state",
  "$timeout",
  function directive($http, $state, $timeout) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("mobileExerciseDirective running!");
        
        scope.step = 1;
        scope.nric_data = {};
        scope.member_details = {
          mobile_country_code : '+65'
        };
        scope.emp_dob_error = false;
        scope.emp_dob_error_message = '';
        scope.emp_mobile_error = false;
        scope.emp_mobile_error_message = '';
        scope.isConfirmActive = false;
        scope.token = null;
        scope.devicePlatform = null;
        scope.optCode = [];
        scope.code_err = false;
        scope.stopAutoFocus = false;
        scope.reset_password_text = '';
        scope.isConfirmSelected = false;

        var iti = null;

        var isBackspaceActive = false;

        $("body").on("keydown", ".dob-input", function(event) {
          if (event.keyCode == 32) {
            event.preventDefault();
          }
        });
        scope.toggleForgotPassword = function(){
          if( scope.step != 0 ){
            scope.step = 0;
          }else{
            scope.step = 1;
          }
        }
        scope.validateEmpDOB = function( data ){
          if( scope.member_details.dob == '' ){
            return false;
          }
          scope.member_details.dob = scope.member_details.dob.replace( /([^0-9\/ ])/g, "" );
          if( scope.member_details.dob.length == 2 ){
            if( !isBackspaceActive ){
              scope.member_details.dob = scope.member_details.dob + "/";
              isBackspaceActive = true;
            }
          }
          if( scope.member_details.dob.length < 3 ){
            isBackspaceActive = false;
          }
          if( scope.member_details.dob.length == 3 && scope.member_details.dob.indexOf('/') == -1 ){
            scope.member_details.dob = scope.member_details.dob.substr(0, 2) + "/" + scope.member_details.dob.substr(2);
            isBackspaceActive = true;
          }
          if( scope.member_details.dob.length == 3 ){
            isBackspaceActive = true;
          }
          if( scope.member_details.dob.length == 4 ){
            isBackspaceActive = false;
          }
          if( scope.member_details.dob.length == 5 ){
            if( !isBackspaceActive ){
              scope.member_details.dob = scope.member_details.dob + "/";
              isBackspaceActive = true;
            }
          }
          if( scope.member_details.dob.length == 6 && scope.member_details.dob.match(/\//g).length > 0 ){
            scope.member_details.dob = scope.member_details.dob.substr(0, 5) + "/" + scope.member_details.dob.substr(5);
            isBackspaceActive = true;
          }
          var slashCtr = 0;
          angular.forEach( scope.member_details.dob, function(value, key){
            if( value == '/' ){
              slashCtr += 1;
            }
            if( slashCtr > 2 ){
              scope.member_details.dob = scope.member_details.dob.substr(0, key) + scope.member_details.dob.substr(key + 1);
              slashCtr = 0;
            }
          });
          scope.member_details.dob = scope.member_details.dob.replace(/\/{2,}/g, "/");
          scope.validateForm();
        }

        scope.validateDepDOB = function( list, data ){
          if( list.dob == '' ){
            return false;
          }
          list.dob = list.dob.replace( /([^0-9\/ ])/g, "" );
          if( list.dob.length == 2 ){
            if( !list.isBackspaceActive ){
              list.dob = list.dob + "/";
              list.isBackspaceActive = true;
            }
          }
          if( list.dob.length < 3 ){
            list.isBackspaceActive = false;
          }
          if( list.dob.length == 3 && list.dob.indexOf('/') == -1 ){
            list.dob = list.dob.substr(0, 2) + "/" + list.dob.substr(2);
            list.isBackspaceActive = true;
          }
          if( list.dob.length == 3 ){
            list.isBackspaceActive = true;
          }
          if( list.dob.length == 4 ){
            list.isBackspaceActive = false;
          }
          if( list.dob.length == 5 ){
            if( !list.isBackspaceActive ){
              list.dob = list.dob + "/";
              list.isBackspaceActive = true;
            }
          }
          if( list.dob.length == 6 && list.dob.match(/\//g).length > 0 ){
            list.dob = list.dob.substr(0, 5) + "/" + list.dob.substr(5);
            list.isBackspaceActive = true;
          }
          var slashCtr = 0;
          angular.forEach( list.dob, function(value, key){
            if( value == '/' ){
              slashCtr += 1;
            }
            if( slashCtr > 2 ){
              list.dob = list.dob.substr(0, key) + list.dob.substr(key + 1);
              slashCtr = 0;
            }
          });
          list.dob = list.dob.replace(/\/{2,}/g, "/");
          scope.validateForm();
        }

        scope.validateEmpMobile = function( data ){
          scope.validateForm();
        }

        scope.validateForm = function( ){
          var dep_err_crt = 0;

          if( !scope.member_details.dob ){
            scope.emp_dob_error = true;
            scope.emp_dob_error_message = 'Date of Birth is required.';
            dep_err_crt += 1;
          }else{
            if( scope.member_details.dob.length != 10 ){
              scope.emp_dob_error = true;
              scope.emp_dob_error_message = 'Date of Birth should be in "DD/MM/YYYY" format.';
              dep_err_crt += 1;
            }else{
              if( moment( scope.member_details.dob , 'DD/MM/YYYY' ).isValid() == false ){
                scope.emp_dob_error = true;
                scope.emp_dob_error_message = 'Date of Birth is Invalid and should be in "DD/MM/YYYY" format.';
                dep_err_crt += 1;
              }else{
                var a = moment( scope.member_details.dob , 'DD/MM/YYYY' );
                var b = moment( );
                console.log();
                if( a.diff( b, 'days' ) > 0 ){
                  scope.emp_dob_error = true;
                  scope.emp_dob_error_message = 'Date of Birth should be today or less';
                  dep_err_crt += 1;
                }else{
                  scope.emp_dob_error = false;
                  scope.emp_dob_error_message = '';
                }
              }

              
            }
          }

          if( !scope.member_details.mobile ){
            scope.emp_mobile_error = true;
            scope.emp_mobile_error_message = 'Employee Mobile Number is required.';
            dep_err_crt += 1;
          }else{
            if( iti.getSelectedCountryData().iso2 == 'sg' && scope.member_details.mobile.length < 8 ){
              scope.emp_mobile_error = true;
              scope.emp_mobile_error_message = 'Mobile Number for your country code should be 8 digits.';
              dep_err_crt += 1;
            }else if( iti.getSelectedCountryData().iso2 == 'my' && scope.member_details.mobile.length < 10 ){
              scope.emp_mobile_error = true;
              scope.emp_mobile_error_message = 'Mobile Number for your country code should be 10 digits.';
              dep_err_crt += 1;
            }else if( iti.getSelectedCountryData().iso2 == 'ph' && scope.member_details.mobile.length < 9 ){
              scope.emp_mobile_error = true;
              scope.emp_mobile_error_message = 'Mobile Number for your country code should be 9 digits.';
              dep_err_crt += 1;
            }else if( scope.member_details.mobile.length < 8 ){
              scope.emp_mobile_error = true;
              scope.emp_mobile_error_message = 'Mobile Number should be minimum of 8 digits.';
              dep_err_crt += 1;
            }else{
              scope.emp_mobile_error = false;
              scope.emp_mobile_error_message = '';
            }
          }

          angular.forEach( scope.member_details.dependents, function(value, key){
            if( !value.dob ){
              value.dob_error = true;
              value.dob_error_message = 'Date of Birth is required.';
              dep_err_crt += 1;
            }else{
              if( value.dob.length != 10 ){
                value.dob_error = true;
                value.dob_error_message = 'Date of Birth should be in "DD/MM/YYYY" format.';
                dep_err_crt += 1;
              }else{
                if( moment( value.dob , 'DD/MM/YYYY' ).isValid() == false ){
                  value.dob_error = true;
                  value.dob_error_message = 'Date of Birth is Invalid and should be in "DD/MM/YYYY" format.';
                  dep_err_crt += 1;
                }else{
                  var a = moment( value.dob , 'DD/MM/YYYY' );
                  var b = moment( );
                  console.log();
                  if( a.diff( b, 'days' ) > 0 ){
                    value.dob_error = true;
                    value.dob_error_message = 'Date of Birth should be today or less';
                    dep_err_crt += 1;
                  }else{
                    value.dob_error = false;
                    value.dob_error_message = '';
                  }
                }
              }
            }

            if( key == scope.member_details.dependents.length - 1 ){
              if( dep_err_crt == 0 ){
                scope.isConfirmActive = true;
              }else{
                scope.isConfirmActive = false;
              }
              scope.checkMobileTaken( scope.member_details.mobile );
            }
          })

          if( scope.member_details.dependents.length == 0 ){
            if( dep_err_crt == 0 ){
              scope.isConfirmActive = true;
            }else{
              scope.isConfirmActive = false;
            }
            scope.checkMobileTaken( scope.member_details.mobile );
          }
          console.log( scope.isConfirmActive );
        }

        scope.submitNric = function( data ){
          if( data.nric && data.password ){
            if( scope.checkNRIC( data.nric ) == true ){
              scope.sendNRIC( data );
            }else{
              swal( 'Error!', 'Invalid NRIC.', 'error' );
            }
          }else{
            swal( 'Error!', 'Please input your NRIC and password.', 'error' );
          }
        }

        scope.checkNRIC = function(theNric){
          var nric_pattern = null;
          if( theNric.length == 9 ){
            nric_pattern = new RegExp("^[stfgSTFG]{1}[0-9]{7}[a-zA-z]{1}$");
          }else if( theNric.length == 12 ){
            // nric_pattern = new RegExp("^[0-9]{2}(?:0[1-9]|1[-2])(?:[0-1]|[1-2][0-9]|[3][0-1])[0-9]{6}$");
            return true;
          }else{
            return false;
          }
          return nric_pattern.test(theNric);
        };

        scope.cancelBtn = function(){
          if( scope.step == 1 || scope.step == 4 ){
            if( scope.devicePlatform == 'mobile' ){
              window.location = 'mednefitsapp://';
            }else{
              window.location = '/member-portal-login';
            }
          }else{
            scope.step -= 1;
          }
        }

        scope.submitUpdateDetails = function( data ){
          if( scope.optCode.length != 6 ){
            swal('Error!', 'Please input your 6 digit verification code (OTP).', 'error');
            return false;
          }
          console.log( data );

          var update_data = {
            dob : moment( data.dob, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            mobile : data.mobile,
            mobile_country_code : data.mobile_country_code,
            name : data.name,
            dependents : [],
            otp_code : scope.optCode.join('')
          }
          angular.forEach( data.dependents,function(value,key){
            console.log( value );
            var dep = {
              dependent_id: value.dependent_id,
              dob: moment( value.dob, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
              name: value.name
            }
            update_data.dependents.push( dep );

            if( key == update_data.dependents.length - 1 ){
              scope.updateDetails( update_data );
            }
          });

          if( data.dependents.length == 0 ){
            scope.updateDetails( update_data );
          }

          console.log( update_data );
        }


        scope.setStep = function( num ){
          if( num == 2 ){
            scope.isConfirmSelected = false;
            scope.optCode = [];
            scope.member_details.mobile_format = "+" + scope.member_details.mobile_country_code + "" + scope.member_details.mobile;
            scope.initializeGeoCode();
            scope.step = num;
          }
          if( num < 3 ){
            scope.code_err = false;
          }
          if( num == 3 ){
            scope.validateForm();
            scope.checkMobileTaken( scope.member_details.mobile );
            scope.isConfirmSelected = true;
            scope.showLoading();
            $timeout(function() {
              if( scope.isConfirmActive == true ){
                scope.sendOtpCode();
                $timeout(function() {
                  $(".otp-input-wrapper input:eq(0)").focus();
                }, 300);
                scope.step = num;
              }
              scope.hideLoading();
            }, 1000);
          }
        }

        scope.otpChanged = function(e){
          var index = scope.optCode.join("").length;
          // $(".otp-input-wrapper input").blur();
          if( index == 6 ){
            scope.stopAutoFocus = true;
            $(".otp-input-wrapper input:eq(5)").focus();
          }else{
            scope.stopAutoFocus = false;
            $(".otp-input-wrapper input:eq(" + index + ")").focus();
          }
          
          // console.log( index );
          // console.log( scope.optCode );
        }
        scope.otpFocus = function( num_index ){
          if( scope.stopAutoFocus == false ){
            var index = scope.optCode.join("").length;
            if( num_index != index ){
              // $(".otp-input-wrapper input").blur();
              $(".otp-input-wrapper input:eq(" + index + ")").focus();
            }
          }
        }

        $('html').keydown(function(e){
          if(e.keyCode == 9){
            if( scope.step == 3 ){
              e.preventDefault(); 
              return false;
            }
          }
          if(e.keyCode == 8){
            if( scope.step == 3 && scope.optCode.join("").length > 0 ){
              var index = scope.optCode.join("").length;
              scope.optCode[ index - 1 ] = "";
              var index = scope.optCode.join("").length;
              $(".otp-input-wrapper input:eq(" + index + ")").focus();
              // console.log( index );
              // console.log( scope.optCode );
            }
          }
        })





        scope.sendOtpCode = function( ){
          scope.code_err = false;
          var data = {
            mobile : scope.member_details.mobile,
            mobile_country_code: "+" + scope.member_details.mobile_country_code
          }
          scope.showLoading();
          $http.post( 
            base_url + "exercise/send_sms_otp", 
            data, 
            {
              headers: {
                'Authorization': scope.token,
              }
            })
            .then(function(response){
              console.log(response);
              if( response.data.status ){
                scope.optCode = [];
                $(".otp-input-wrapper input:eq(0)").focus();
              }else{
                swal( 'Error!', response.data.message, 'error' );
              }
              scope.hideLoading();
            })
            .catch(function(err){
              console.log(err);
              // swal( 'Error!', "Invalid Mobile Number. Can't send OTP code.", 'error' );
              scope.hideLoading();
            });
        }

        scope.checkMobileTaken = function( mobile ){
          if( mobile.length >= 8 ){
            var data = {
              mobile : mobile
            }
            $http.post( 
              base_url + "exercise/validate_mobile_number", 
              data, 
              {
                headers: {
                  'Authorization': scope.token,
                }
              })
              .then(function(response){
                console.log(response);
                if( response.data.status ){

                }else{
                  scope.emp_mobile_error = true;
                  scope.emp_mobile_error_message = response.data.message;
                  scope.isConfirmActive = false;
                }
              });
          }
        }

        scope.getMemberInfo = function( token ){
          $http.get( 
            base_url + "exercise/get_member_details",
            {
              headers: {
                'Authorization': token,
              }
            })
            .then(function(response){
              console.log(response);
              if( response.data.status ){
                scope.member_details = response.data.data;
                if( scope.member_details.mobile_country_code != null ){
                  scope.member_details.mobile_country_code = "+" + ( scope.member_details.mobile_country_code ).split("+").join("");
                }else{
                  scope.member_details.mobile_country_code = "+65";
                }
                if( scope.member_details.dob != undefined && scope.member_details.dob != null ){
                  scope.member_details.dob = moment( scope.member_details.dob, 'DD/MM/YYYY' ).format('DD/MM/YYYY');
                }else{
                  scope.member_details.dob = "";
                }
                angular.forEach( scope.member_details.dependents, function(value,key){
                  if( value.dob != undefined && value.dob != null ){
                    value.dob = moment( value.dob, 'DD/MM/YYYY' ).format('DD/MM/YYYY');
                  }else{
                    value.dob = "";
                  }
                });

                scope.member_details.mobile_format = scope.member_details.mobile_country_code + "" + scope.member_details.mobile;
                scope.step = 2;
                scope.initializeGeoCode();
              }else{
                swal( 'Error!', response.data.message, 'error' );
              }
              scope.hideLoading();
            });
        }

        scope.sendNRIC = function( data ){
          scope.showLoading();
          $http.post( base_url + "exercise/validate_member", data)
            .then(function(response){
              console.log(response);
              if( response.data.status ){
                if(response.data.updated == true) {
                  scope.hideLoading();
                  scope.step = 4;
                } else {
                  scope.token = response.data.token;
                  scope.getMemberInfo( response.data.token );
                }
              }else{
                scope.hideLoading();
                swal( 'Error!', response.data.message, 'error' );
              }
            });
        }

        scope.updateDetails = function( data ){
          console.log( data );
          scope.showLoading();
          scope.code_err = false;
          $http.post( 
            base_url + "exercise/update_member_details", data,
            {
              headers: {
                'Authorization': scope.token,
              }
            })
            .then(function(response){
              console.log(response);
              if( response.data.status ){
                scope.step = 4;
                scope.code_err = false;
              }else{
                if( response.data.message != 'Incorrect code, please try again.' ){
                  swal( 'Error!', response.data.message, 'error' );
                }else{
                  scope.code_err = true;
                  scope.code_err_msg = response.data.message;
                  // scope.code_err_msg = 'Incorrect code, please try again.';
                }
              }
              scope.hideLoading();
            });
        }

        scope.resetPassword = function( value ){
          if( !value ){
            swal( 'Error!', 'Mobile Number or Email Address is required.', 'error' );
            return false;
          }
          console.log( value );
          scope.showLoading();
          var data = {
            email: value
          };
          $http.post( 
            base_url + "v2/auth/forgotpassword", data)
            .then(function(response){
              console.log(response);
              if( response.data.status ){
                swal( 'Success!', response.data.message, 'success' );
                scope.step = 1;
              }else{
                swal( 'Error!', response.data.message, 'error' );
              }
              scope.hideLoading();
            });
        }

        scope.showLoading = function( ){
          $( ".main-loader" ).fadeIn(); 
          loading_trap = true;
        }

        scope.hideLoading = function( ){
          $timeout(function() {
            $( ".main-loader" ).fadeOut();
            loading_trap = false;
          },1000)
        }

        scope.initializeDatePickers = function(){
          $timeout(function() {
            var dt = new Date();
            // dt.setFullYear(new Date().getFullYear()-18);
            // console.log( dt );
            $('.datepicker').datepicker({
              format: 'dd/mm/yyyy',
              endDate : dt,
              autoclose : true,
            });
            $('.dep-datepicker').datepicker({
              format: 'dd/mm/yyyy',
              endDate : dt,
              autoclose : true,
            });
            // $(".datepicker").datepicker("update", new Date( moment( scope.member_details.dob, 'DD/MM/YYYY' ) ));
            // angular.forEach( scope.member_details.dependents, function(value,key){ 
            //   console.log( value );
            //   $(".dep-datepicker:eq(" + key + ")").datepicker("update", new Date( moment( value.dob, 'DD/MM/YYYY' ) ));
            // });
          },300)
        }

        scope.initializeGeoCode = function(){
          $timeout(function() {
            var settings = {
              separateDialCode : true,
              initialCountry : "SG",
              autoPlaceholder : "off",
              utilsScript : "../assets/hr-dashboard/js/utils.js",
            };
            var input = document.querySelector("#area_code");
            iti = intlTelInput(input, settings);
            console.log( scope.member_details );
            iti.setNumber( scope.member_details.mobile_format );
            $timeout(function() {
              scope.member_details.mobile = scope.member_details.mobile;
              $("#area_code").val( scope.member_details.mobile );
            },300)
            input.addEventListener("countrychange", function() {
              console.log( iti.getSelectedCountryData() );
              scope.member_details.mobile_country_code = "+" + iti.getSelectedCountryData().dialCode;
              scope.member_details.mobile_country_code_country = iti.getSelectedCountryData().iso2;
            });


            // scope.validateForm();

          },200)
        }
        
        scope.onLoad = function (){
          var params = new URLSearchParams(window.location.search);
          scope.devicePlatform = params.get('platform');
          console.log( scope.devicePlatform );
        }

        scope.onLoad();





        // ================================================== //

        



        
      }
    };
  }
]);
