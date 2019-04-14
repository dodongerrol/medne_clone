var mainCtrl = angular.module('mainCtrl', ['ui.router'])

mainCtrl.controller('MainCtrl', function( Auth , $state, $element, $scope ){
	var vm = this;
	vm.user = {};
	vm.new_user = {};
	// console.log($state);

	vm.loginUser = function( ) {
		
		$('.login-wrapper').hide();
		$('.login-logo').hide();
		$('#loader-home').fadeIn(500);

		vm.error = '';
	 	Auth.login(vm.loginData)
	 		.success(function(data){
	 			// console.log(data);
	 			if(data.error == "false") {
	 				
					$('#loader-home').fadeOut(500);
	 				$state.go('home');

	 				setTimeout(function(){
	 					$('.login-wrapper').show();
	 					$('.login-logo').show();
	 				},5000);
	 				
	 			}
	 		})
	 		.error(function(err){
	 			console.log(err);
	 			vm.error = err.error_description;
	 			$('#loader-home').hide();
	 			$('.login-wrapper').fadeIn();
	 			$('.login-logo').fadeIn();
	 			$('#error_message').slideDown(300);
	 			setTimeout(function() {
	 				$('#error_message').slideUp(1300);
	 			}, 1000);
	 		});
	};

	vm.signUp = function( ) {
		var phone = $("#phone").intlTelInput("getNumber" ,intlTelInputUtils.numberFormat.E164);

		vm.new_user.phone = phone;

		// console.log(vm.new_user);
		// console.log(vm.new_user.password.length);
		if( vm.new_user.password.length < 8 ){
			vm.error = 'Password should have 8 characters';
			$('#error_message').slideDown(300);
 			setTimeout(function() {
 				$('#error_message').slideUp(1300);
 			}, 1000);
		}else{
			if( vm.new_user.password == vm.new_user.confirm_password){
				Auth.signUp(vm.new_user)
				.success(function(response){
					console.log(response);

					if(response.status == true || response.status == 'true' ){
						$state.go('login');

						setTimeout(function(){
							$('#succ_message').slideDown(300);
				 			setTimeout(function() {
				 				$('#succ_message').slideUp(1300);
				 			}, 1000);
						},2000);
					}else{
						vm.error = response.message;
						$('#error_message').slideDown(300);
			 			setTimeout(function() {
			 				$('#error_message').slideUp(1300);
			 			}, 1000);
					}
				})
				.error(function(err){
					console.log(err);
				});
				
			}else{
				vm.error = 'Password does not match';
				$('#error_message').slideDown(300);
	 			setTimeout(function() {
	 				$('#error_message').slideUp(1300);
	 			}, 1000);
			}
		}
	};
	vm.forgotPassword = function( ) {
		// console.log(vm.forgot);
		var btn = $('#reset-btn').text();
		$('#reset-btn').attr('disabled', true);
		Auth.resetPassword(vm.forgot.email)
		.success(function(response){
			// console.log(response);
			vm.message = response.message;
			$('#reset-btn').text(btn);
			$('#reset-btn').attr('disabled', false);
			$('#success-reset').fadeIn(500);
		})
		.error(function(err){
			console.log(err);
		});
	};
	vm.onLoad = function(){
		$('.modal').modal('hide');

		var status = Auth.isLoggedIn();
		console.log(status);
		// console.log($state.$current.self.name);
		if( $state.$current.self.name !== 'signup' && $state.$current.self.name !== 'forgot' ){
			// console.log("in");
			if( status == true || status == 'true' ){
				$state.go("home");
			}else{
				$state.go("login");
			}
		}

		
	};
	
	vm.onLoad();
});

mainCtrl.controller('HeaderCtrl', function( Auth , $state, $element, $scope, profilesModule ){
	var vm = this;
	vm.user = {};
	vm.new_user = {};

	profilesModule.getProfile()
	.success(function(response){
		vm.user = response.data.profile;
		vm.user.name = response.data.profile.full_name.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	});


	vm.logOut = function( ) {
		$('#loading').modal('show');

		setTimeout(function(){
			$state.go('login');
			$('#loading').modal('hide');
		},1000);
		
		Auth.logout();
		
	};

	vm.onLoad = function(){
		$('.modal').modal('hide');
	};

	vm.onLoad();
});

mainCtrl.controller('ResetCtrl', function( Auth , $state, $stateParams, $scope ){
	var vm = this;
	vm.reset = {};
	vm.onLoad = function(){
		$('.modal').modal('hide');
	};

	Auth.resetDetails($stateParams.token)
	.success(function(response){
		vm.reset.user_id = response.user_id;
	})
	.error(function(err){
		console.log(err);
	});

	vm.resetPassword = function( ) {
		if(vm.reset.new_password != vm.reset.re_password) {
			vm.message = 'Please specify same password!';
			$('#success-reset').fadeIn(500);
			setTimeout(function() {
				$('#success-reset').fadeOut(500);
			}, 2000);
			return false;
		} else {
			if(vm.reset.new_password.length < 8) {
				vm.message = 'Please select a Password atleat 8 characters.';
				$('#success-reset').fadeIn(500);
				setTimeout(function() {
					$('#success-reset').fadeOut(500);
				}, 2000);
				return false;
			} else {
				$('#reset-btn').text('Resetting...');
				$('#reset-btn').attr('disabled', true);
				Auth.resetProcess(vm.reset.user_id, vm.reset.re_password, vm.reset.new_password)
				.success(function(response){
					console.log(response);
					$('#reset-btn').text('Done');
					$('#reset-btn').attr('disabled', false);
					// vm.message = response.message;
					// $('#success-reset').fadeIn(500);

					$('#main-reset-form').hide();
					$('#success-reset-form').fadeIn();

					// if(response.status == true) {
						// setTimeout(function() {
						// 	$state.go('login');
						// }, 2000);
					// }
				})
				.error(function(error){
					vm.message = error;
					$('#reset-btn').text('Done');
					$('#reset-btn').attr('disabled', false);
					$('#success-reset').fadeIn(500);
				});
			}
		}
	};

	vm.onLoad();
});