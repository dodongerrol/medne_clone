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
        scope.member_details = {};
        scope.emp_dob_error = false;
        scope.emp_dob_error_message = '';
        scope.emp_mobile_error = false;
        scope.emp_mobile_error_message = '';
        scope.isConfirmActive = true;
        scope.token = null;
        scope.deviceOs = null;

        var iti = null;

        var isBackspaceActive = false;

        scope.validateEmpDOB = function( data ){
          if( data.length <= 1 ){
            isBackspaceActive = false;
          }
          if( data.length == 2 ){
            if( data.charAt(1) == '/' ){
              scope.member_details.dob = "0" + data;
              isBackspaceActive = true;
            }else{
              if( !isBackspaceActive ){
                scope.member_details.dob = data + "/";
                isBackspaceActive = true;
              }
            }
          }
          if( data.length == 3 && data.indexOf('/') == -1 ){
            scope.member_details.dob = data.substr(0, 2) + "/" + data.substr(2);
            isBackspaceActive = true;
          }
          if( data.length == 3 ){
            isBackspaceActive = true;
          }
          if( data.length == 4 ){
            isBackspaceActive = false;
            scope.member_details.dob = data.replace(/\/{2,}/g, "/");
          }
          if( data.length == 5 ){
            if( data.charAt(4) == '/' ){
              scope.member_details.dob = data.substr(0, 3) + "0" + data.substr(3);
              isBackspaceActive = true;
            }else{
              if( !isBackspaceActive ){
                scope.member_details.dob = data + "/";
                isBackspaceActive = true;
              }
            }
          }
          if( data.length == 6 && data.match(/\//g).length == 1 ){
            scope.member_details.dob = data.substr(0, 5) + "/" + data.substr(5);
            isBackspaceActive = true;
          }
          if( data.length == 7 ){
            scope.member_details.dob = data.replace(/\/{2,}/g, "/");
          }
          scope.validateForm();
        }

        scope.validateDepDOB = function( list, data ){
          if( data.length <= 1 ){
            list.isBackspaceActive = false;
          }
          if( data.length == 2 ){
            if( data.charAt(1) == '/' ){
              list.dob = "0" + data;
              list.isBackspaceActive = true;
            }else{
              if( !list.isBackspaceActive ){
                list.dob = data + "/";
                list.isBackspaceActive = true;
              }
            }
          }
          if( data.length == 3 && data.indexOf('/') == -1 ){
            list.dob = data.substr(0, 2) + "/" + data.substr(2);
            list.isBackspaceActive = true;
          }
          if( data.length == 3 ){
            list.isBackspaceActive = true;
          }
          if( data.length == 4 ){
            list.isBackspaceActive = false;
            list.dob = data.replace(/\/{2,}/g, "/");
          }
          if( data.length == 5 ){
            if( data.charAt(4) == '/' ){
              list.dob = data.substr(0, 3) + "0" + data.substr(3);
              list.isBackspaceActive = true;
            }else{
              if( !list.isBackspaceActive ){
                list.dob = data + "/";
                list.isBackspaceActive = true;
              }
            }
          }
          if( data.length == 6 && data.match(/\//g).length == 1 ){
            list.dob = data.substr(0, 5) + "/" + data.substr(5);
            list.isBackspaceActive = true;
          }
          if( data.length == 7 ){
            list.dob = data.replace(/\/{2,}/g, "/");
          }
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
            }
          })
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

        scope.getOs = function(){
          var userAgent = window.navigator.userAgent,
              platform = window.navigator.platform,
              macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
              windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
              iosPlatforms = ['iPhone', 'iPad', 'iPod'],
              os = null;
          if (macosPlatforms.indexOf(platform) !== -1) {
            os = 'Mac OS';
          } else if (iosPlatforms.indexOf(platform) !== -1) {
            os = 'iOS';
          } else if (windowsPlatforms.indexOf(platform) !== -1) {
            os = 'Windows';
          } else if (/Android/.test(userAgent)) {
            os = 'Android';
          } else if (!os && /Linux/.test(platform)) {
            os = 'Linux';
          }
          // return os;
          scope.deviceOs = os;
        }

        scope.getOs();

        scope.cancelBtn = function(){
          if( scope.step == 1 || scope.step == 3 ){
            if( scope.deviceOs == 'Mac OS' || scope.deviceOs == 'Windows' ){
              window.location = '/member-portal-login';
            }
            if( scope.deviceOs == 'iOS' || scope.deviceOs == 'Android' ){
              // alert( scope.deviceOs + "  " + 'mednefitsapp://' );
              window.location = 'mednefitsapp://';
            }
          }else{
            scope.step -= 1;
          }
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
                  scope.validateForm();
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
                  scope.member_details.dob = moment( scope.member_details.dob, 'DD/MM/YYYY' ).format('DD/MM/YYYY');
                }
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
                scope.token = response.data.token;
                scope.getMemberInfo( response.data.token );
              }else{
                scope.hideLoading();
                swal( 'Error!', response.data.message, 'error' );
              }
            });
        }

        scope.updateDetails = function( data ){
          console.log( data );
          var update_data = {
            dob : moment( data.dob, 'DD/MM/YYYY' ).format('YYYY-MM-DD'),
            mobile : data.mobile,
            mobile_country_code : data.mobile_country_code,
            name : data.name,
            dependents : [],
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
              scope.showLoading();
              $http.post( 
                base_url + "exercise/update_member_details", update_data,
                {
                  headers: {
                    'Authorization': scope.token,
                  }
                })
                .then(function(response){
                  console.log(response);
                  if( response.data.status ){
                    scope.step = 3;
                  }else{
                    swal( 'Error!', response.data.message, 'error' );
                  }
                  scope.hideLoading();
                });
            }
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
            iti.setNumber( scope.member_details.mobile_format );
            $timeout(function() {
              scope.member_details.mobile = scope.member_details.mobile;
              $("#area_code").val( scope.member_details.mobile );
            },300)
            input.addEventListener("countrychange", function() {
              console.log( iti.getSelectedCountryData() );
              scope.member_details.mobile_country_code = iti.getSelectedCountryData().dialCode;
              scope.member_details.mobile_country_code_country = iti.getSelectedCountryData().iso2;
            });


            scope.validateForm();

          },200)
        }
        
        scope.onLoad = function (){
          scope.getOs();
        }

        scope.onLoad();



        
      }
    };
  }
]);
