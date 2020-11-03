app.directive("companyProfileDirective", [
  "$state",
  "serverUrl",
  "$timeout",
  "hrSettings",
  function directive($state, serverUrl, $timeout, hrSettings) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('company profile directive');
        scope.business_arr = [];
        scope.business_data = {
          phone_code: '65',
        };
        scope.business_ctr = 0; 
        scope.isUpdateContact = false;
        scope.activeBusinessUpdate = false;
        scope.addMoreContactDisabled = false;

        scope.addMoreContact = function ( contact_data ) {
          if ( scope.checkEmployeeForm( contact_data ) == false ) {
            return false; 
          }
          scope.business_ctr += 1;
          scope.business_arr.push( contact_data );
          scope.business_data = {
            phone_code: '65',
          }
          scope.initializeAddContactCountryCode();
        }
        scope.prevAddContact = function () {
          if ( scope.business_ctr != 0 ) {
            scope.business_ctr -= 1;
            scope.business_data = scope.business_arr[scope.business_ctr];
            // console.log(scope.business_arr[(scope.business_ctr)-1]);
            // if ( scope.business_arr[(scope.business_ctr)+1] == undefined ) {
            //   scope.business_arr.splice((scope.business_ctr)+1,1);
            //   console.log('splice dayun');
            //   console.log(scope.business_ctr);
            //   console.log( scope.business_arr.splice((scope.business_ctr)+1,1) )
            //   console.log(scope.business_arr );
            // }
          } 
        }

        scope.nextAddContact = function () {
          scope.business_ctr += 1;
          console.log( scope.business_arr[scope.business_ctr] )
          if (scope.business_arr[scope.business_ctr]) {
            scope.business_data = scope.business_arr[scope.business_ctr];
          } else {
            scope.business_data = {};
          }
        }
       
        scope.addBusinessContact = async function () {
          scope.business_ctr = 0;
          console.log(scope.business_ctr);
          // scope.business_data = scope.business_arr[ scope.business_ctr ];
          await scope.initializeAddContactCountryCode();
        }

        scope.initializeAddContactCountryCode = function(){
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#phone_number_add_contact");
          primaryAdminCountry = intlTelInput(input, settings);
          primaryAdminCountry.setCountry("SG");
          input.addEventListener("countrychange", function () {
          scope.business_data.phone_code = primaryAdminCountry.getSelectedCountryData().dialCode;
          });
        }

        scope.initializeEditBusinessContactCountryCode = function(){
          var settings = {
            preferredCountries: [],
            separateDialCode: true,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#business_mobile_area_code");
          primaryAdminCountry = intlTelInput(input, settings);
          primaryAdminCountry.setCountry("SG");
          input.addEventListener("countrychange", function () {
          scope.get_primary_contact_data.mobile_code = primaryAdminCountry.getSelectedCountryData().dialCode;
          });
        }

        scope.initializeEditBusinessInfoCountryCode = function(){
          var settings = {
            preferredCountries: [],
            separateDialCode: false,
            initialCountry: false,
            autoPlaceholder: "off",
            utilsScript: "../assets/hr-dashboard/js/utils.js",
            onlyCountries: ["sg", "my"],
          };

          var input = document.querySelector("#business_info_country");
          primaryAdminCountry = intlTelInput(input, settings);
          if ( scope.get_business_info_data.country == 'Singapore' ) {
            primaryAdminCountry.setCountry("SG");
          } else {
            primaryAdminCountry.setCountry("MY");
          }
          
          input.addEventListener("countrychange", function () {
          scope.get_business_info_data.country = primaryAdminCountry.getSelectedCountryData().name;
          console.log(primaryAdminCountry.getSelectedCountryData());
          scope.activeBusinessUpdate = true;
          });
        }

        scope.getBusinessInformation = async function () {
          await hrSettings.fetchBusinessInformation()
          .then(async function (response) {
            scope.business_info_data = response.data.data;
            console.log(scope.business_info_data);
          });
        }
        
        scope.getBusinessContacts = async function () {
          await hrSettings.fetchBusinessContact()
          .then(async function (response) {
            console.log(response);
            scope.business_contacts_data = response.data;
          });
        }

        scope.editPrimaryBusinessContact = async function ( data, type ) {
          console.log(data);
          scope.get_primary_contact_data = data;
          scope.get_primary_contact_data.type = type;
          scope.activeBusinessUpdate = false;
          await scope.initializeEditBusinessContactCountryCode();
        }

        scope.updatePrimaryBusinessContact = async function ( primary_data ) {
          if(primary_data.type == "primary") {
            let data = {
              first_name: primary_data.first_name,
              work_email: primary_data.email,
              phone: primary_data.phone,
              phone_code: primary_data.mobile_code,
              customer_business_contact_id: primary_data.customer_business_contact_id,
            }
            scope.showLoading();
            await hrSettings.updateBusinessContact( data )
            .then(async function (response) {
              console.log(response);
  
              scope.hideLoading();
              await scope.getBusinessContacts();
              
              $("#business-contact-modal").modal('hide');
  
              swal('Success', response.data.message, 'success');
            });
          } else {
            let data = {
              first_name: primary_data.first_name,
              work_email: primary_data.email,
              phone: primary_data.phone,
              phone_code: primary_data.mobile_code,
              medi_company_contact_id: primary_data.medi_company_contact_id,
            }
            scope.showLoading();
            await hrSettings.updateCompanyContact( data )
            .then(async function (response) {
              console.log(response);
  
              scope.hideLoading();
              await scope.getBusinessContacts();
              
              $("#business-contact-modal").modal('hide');
  
              swal('Success', response.data.message, 'success');
            });
          }
        }

        scope.editBusinessInformation = async function ( data ) {
          scope.get_business_info_data = data;
          scope.activeBusinessUpdate =false;

          if ( scope.get_business_info_data.currency_type == 'sgd' ) {
            scope.get_business_info_data.country = 'Singapore'
          } else {
            scope.get_business_info_data.country = 'Malaysia'
          }

          await scope.initializeEditBusinessInfoCountryCode();
        }

        scope.updateBusinessInformation = async function ( business_info ) {
          if ( business_info.country == 'Singapore' ) {
            business_info.currency_type = 'sgd';
          } else {
            business_info.currency_type = 'myr';
          }

          let data = {
            customer_business_information_id: business_info.customer_business_information_id,
            account_name: business_info.account_name,
            currency_type: business_info.currency_type, 
            company_name: business_info.company_name,
            company_address: business_info.company_address,
            unit_number: business_info.unit_number,
            building_name: business_info.building_name
          }

          console.log(data);
          scope.showLoading();
          await hrSettings.updateBusinessInformation( data )
          .then(async function (response) {
            console.log(response);

            scope.hideLoading();
            await scope.getBusinessInformation();
            
            $("#business-information-modal").modal('hide');

            swal('Success', response.data.message, 'success');
          });
        }

        scope.getCompanyContacts = async function () {
          await hrSettings.fetchCompanyContacts()
          .then( function (response) {
            console.log(response);

            scope.get_company_contact_data = response.data;

            angular.forEach(scope.get_company_contact_data, function (value, key) {
							console.log(key);
							if ( key == 2 ) {
                console.log('true and disabled');
                scope.addMoreContactDisabled = true;
              }
						});
          });
        }

        scope.updateAddBusinessContact = async function ( form_data ) {
            if ( scope.checkEmployeeForm( form_data ) == false ) {
              return false; 
            }
            console.log(scope.business_arr);
            console.log(scope.business_ctr);
            console.log(scope.business_arr.length-1);
            if ( scope.business_ctr > (scope.business_arr.length-1) ) {
              console.log('test');
              scope.business_arr.push( form_data );
            }

            let data = {
              business_contacts: scope.business_arr,
            }
            
            // console.log(data);
            // scope.business_arr = [];
            // scope.business_data = {};
            // $("#business-add-contact-modal").modal('hide');
            scope.showLoading();
            await hrSettings.updateMoreBusinessContact( data )
            .then(async function (response) {
              console.log(response);
              scope.hideLoading();
              await scope.getCompanyContacts();
              scope.business_arr = [];
              $("#business-add-contact-modal").modal('hide');
              
              swal('Success', response.data.message, 'success');
            });
        }

        scope.removeDisable = function () {
          scope.activeBusinessUpdate = true;
        }

        scope.checkEmployeeForm = function () {
          if (scope.business_data.email) {
						if (scope.checkEmail(scope.business_data.email) == false) {
							swal('Error!', 'Email is invalid.', 'error');
							return false;
						}
					}
        }

        scope.checkEmail = function (email) {
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					return regex.test(email);
				}

        scope.showLoading = function () {
					$(".circle-loader").fadeIn();
					loading_trap = true;
				}

				scope.hideLoading = function () {
					setTimeout(function () {
						$(".circle-loader").fadeOut();
						loading_trap = false;
					},100)
				}

        scope.onLoad = async function () {
          await scope.getBusinessInformation();
          await scope.getBusinessContacts();
          await scope.getCompanyContacts();
        }

        scope.onLoad();
    
      }
    }
  }
]);