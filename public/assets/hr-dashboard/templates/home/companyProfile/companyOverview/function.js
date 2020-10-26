app.directive("companyProfileDirective", [
  "$state",
  "serverUrl",
  "$timeout",
  "hrSettings",
  "$http",
  function directive($state, serverUrl, $timeout, hrSettings, $http) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log('company profile directive');

        scope.business_data = [];
        scope.business_arr = [];
        scope.business_ctr = 0; 
        scope.isUpdateContact = false;
        scope.activeBusinessUpdate = false;
        scope.addMoreContactDisabled = false;

        scope.addMoreContact = function () {
          if ( scope.isUpdateContact ) {

          } else {
            if ( scope.business_arr[scope.business_ctr] ) {
              scope.business_ctr += 1;
              scope.business_data = scope.business_arr[scope.business_ctr];
              console.log('sulod');
            } {
              console.log('push');
              scope.pushAddContact(scope.business_data);
            }
          }
          
        }

        scope.pushAddContact = async function (data) {
          console.log(data);
          scope.business_arr.push(data);
          scope.employee_enroll_count += 1;
          scope.business_ctr += 1;
          scope.business_data = {
            first_name: '',
            email: '',
            phone_code: '65',
            phone: '',
          };

          await scope.initializeAddContactCountryCode();
          console.log(scope.business_arr);

        }
        
        scope.prevAddContact = function () {
          console.log('prev contact');
          if (scope.business_ctr != 0) {
            scope.business_ctr -= 1;
            scope.business_data = scope.business_arr[scope.business_ctr];
          }
          console.log(scope.business_data);
          console.log(scope.business_ctr);
          console.log( scope.business_arr[scope.business_ctr] );
        }

        scope.nextAddContact = function () {
          scope.business_ctr += 1;
          scope.business_data = scope.business_arr[scope.business_ctr];
          console.log(scope.business_data);
          console.log(scope.business_ctr);
          console.log( scope.business_arr[scope.business_ctr] );
        }

        scope.addBusinessContact = async function () {
          scope.business_data = {
            phone_code: '65'
          }
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

        scope.editPrimaryBusinessContact = async function ( data ) {
          console.log(data);
          scope.get_primary_contact_data = data; 
          scope.activeBusinessUpdate = false;
          await scope.initializeEditBusinessContactCountryCode();
        }

        scope.updatePrimaryBusinessContact = async function ( primary_data ) {
          let data = {
            first_name: primary_data.first_name,
            work_email: primary_data.email,
            phone: primary_data.phone,
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
        }

        scope.editBusinessInformation = async function ( data ) {


          await scope.initializeEditBusinessInfoCountryCode();
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

        scope.updateAddBusinessContact = async function () {
          console.log(scope.business_arr);
          let data = {
            business_contacts: scope.business_arr,
          }
          scope.showLoading();
          await hrSettings.updateMoreBusinessContact( data )
          .then(async function (response) {
            console.log(response);

            scope.hideLoading();
            await scope.getCompanyContacts();
            
            $("#business-add-contact-modal").modal('hide');

            swal('Success', response.data.message, 'success');
          });
        }

        scope.removeDisable = function () {
          scope.activeBusinessUpdate = true;
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