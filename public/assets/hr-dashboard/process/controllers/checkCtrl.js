var checkCtrl = angular.module('checkCtrl', [])


checkCtrl.controller('checkCtrls', function( $scope, $http, $stateParams, $state, hrSettings ){
	var vm = this;
	vm.account_type = null;
	vm.isEmpDropShow	=	false;
	vm.isAccDropShow = false;

	vm.linkedAccountsArr = [];
	vm.accountDetails = {};
	vm.companyBillingDetails = {};
	vm.primaryHrDetails = {};


	vm.getCompanyContacts = async function() {
    await hrSettings.getContacts().then(function(response) {
			console.log(response);
			vm.companyBillingDetails = response.data;
      // console.log( response.data.data.business_information.created_at );
      // console.log( moment( response.data.data.business_information.created_at ).unix() );
      window.Appcues.identify(
				// "57952", // unique, required
				response.data.data.business_information.customer_buy_start_id,
		    {
					created_at : moment( response.data.data.business_information.created_at ).unix(),
					first_name : response.data.data.business_contact.first_name,
					last_name : response.data.data.business_contact.last_name,
					company_name : response.data.data.business_information.company_name,
					company_address : response.data.data.business_information.company_address,
					company_postal_code : response.data.data.business_information.postal_code,
					company_email : response.data.data.business_contact.work_email,
		    }
		  );
    });
  };

	vm.showGlobalModal = ( message ) =>{
    $( "#global_modal" ).modal('show');
    $( "#global_message" ).text(message);
  }

	vm.accountType = async function(){
	 await $http.get(window.location.origin + '/hr/get_company_account_type' )
		.success(function(response){
			console.log(response);
			vm.account_type = response.account_type;
			localStorage.setItem('company_account_type', vm.account_type);
		});
	};

	vm.hideIntroLoader = ( ) =>{
		setTimeout(function() {
			$( ".main-loader" ).fadeOut();
			introLoader_trap = false;
		}, 1000);
	}

	vm.toggleEmployeeNavDrop	=	function(){
		vm.isEmpDropShow	=	vm.isEmpDropShow ? false : true;
	}

	vm.toggleAccountNavDrop = function() {
		vm.isAccDropShow	=	vm.isAccDropShow ? false : true;
	}

	$("body").click(function (e) {
		if ($(e.target).parents(".emp-nav-click-drop").length === 0) {
			vm.isEmpDropShow = false;
			$scope.$apply();
		}
	});

	vm._checkSessionOneSignal_	=	async function(){
		if($stateParams.token) {
			// console.log('has token');
		} else {
			// console.log('no token');
			await $http.get(window.location.origin + '/get-hr-session')
				.then(async function(result){
					// console.log(result);
					// get config for realtime notification
					await $http.get(window.location.origin + '/config/notification')
					.then(async function(response){
						// console.log(response);
						await OneSignal.push(["init", {
								appId: response.data,
								autoRegister: true, // Set to true to automatically prompt visitors 
								httpPermissionRequest: {
									enable: true
								},
								notifyButton: {
									enable: false /* Set to false to hide */
								}
							}]);
						OneSignal.push(["sendTag", "customer_id", result.data.customer_buy_start_id]);
					});
				});
		}
	}

	vm._getLinkedAccounts_	=	function(){
		var url = window.location.origin + `/hr/get/corporate_linked_account?limit=5&page=1&total_enrolled_employee_status=true&total_enrolled_dependent_status=true&except_current=enable`;
		$http.get(url)
		.success(function(response){
			console.log(response);
			vm.linkedAccountsArr = response.data;
		});
	}

	vm._getLinkedAccountsWithCurrent_	=	function(){
		var url = window.location.origin + `/hr/get/corporate_linked_account?limit=5&page=1&total_enrolled_employee_status=true&total_enrolled_dependent_status=true`;
		$http.get(url)
		.success(function(response){
			console.log(response);
			vm.allLinkedAccountsArr = response.data;
		});
	}

	vm._switchAccount_	=	function(data){
		console.log(data);
		$http.get(window.location.origin + '/hr/login_company_linked?id=' + data.id + '&token=' + localStorage.getItem('token'))
			.success(function(response){
				console.log(response);
				if(response.status){
					window.localStorage.setItem('token', response.token);
					window.location.href = window.location.origin + "/company-benefits-dashboard/";
				}else{
					swal('Error!', response.message, 'error');
				}
			});
	}
	vm._getAccountDetails	= async	function(){
		await $http.get(window.location.origin + "/hr/get_business_information")
			.success(function(response){
				console.log(response);
				vm.accountDetails	=	response.data;
			});
	}

	vm._getPrimaryHrDetails_	=	async function(){
		await $http.get(window.location.origin + "/hr/get_business_contact")
			.success(function(response){
				console.log(response);
				vm.primaryHrDetails	=	response[0];
			});
	}
	

	vm.onLoad = async function(){
		await vm._getAccountDetails();
		await vm._getLinkedAccounts_();
		await vm._getLinkedAccountsWithCurrent_();
		await vm._getPrimaryHrDetails_();
		await vm.accountType();
		await vm.getCompanyContacts();
		vm.hideIntroLoader();
	};

	setTimeout(function() {
		vm.onLoad();
	}, 500);
});

checkCtrl.controller('resetCtrl', function( $scope, $http, $stateParams){
	var vm = this;
	vm.reset_pass = {};
	vm.message = "";
	vm.onLoad = function(){
		$http.get(window.location.origin + '/hr/reset-password-details/' + $stateParams.token)
		.success(function(response){
			// console.log(response);
			if(response.status == false) {
				$('#token-expired').fadeIn();
			} else if(response.status == true) {
				vm.reset_pass.id = response.data;
				$('#form-reset').fadeIn();
			}
		});
	};

	vm.resetHr = function( ) {
		// console.log(vm.reset_pass);
		if(vm.reset_pass.password != vm.reset_pass.confirm_password) {
			alert('Password and Confirm Password did not match.');
			return false;
		}

		$('#reset-btn').attr('disabled', true);
		$('#reset-btn').text('RESETTING...');
		$http.post(window.location.origin + '/hr/reset-password-data',  vm.reset_pass)
		.success(function(response){
			vm.message = response.message;
			$('#reset-btn').attr('disabled', false);
			$('#reset-btn').text('DONE');
			if(response.status == true) {
				$('#form-reset').slideUp();
			}
			$('#success-message').fadeIn();
		})
	};

	setTimeout(function() {
		vm.onLoad();
	}, 500);
});

