var mainCtrl = angular.module('mainCtrl', ['ui.router'])

mainCtrl.controller('MainCtrl', function( Auth , $state, $element, $scope ){
	var vm = this;
	vm.user = {};
	vm.new_user = {};
	vm.loginUser = function( ) {
		
		$('.login-wrapper').hide();
		$('.login-logo').hide();
		$('#loader-home').fadeIn(500);

		vm.error = '';
	 	Auth.login(vm.loginData)
	 		.success(function(data){
	 			console.log(data);
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

		console.log(vm.new_user);
		console.log(vm.new_user.password.length);
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

	vm.onLoad = function(){
		$('.modal').modal('hide');
	};

	vm.onLoad();
});

mainCtrl.controller('HeaderCtrl', function( Auth , $state, $element, $scope ){
	var vm = this;
	vm.user = {};
	vm.new_user = {};

	Auth.getUser()
	.then(function(response){
		vm.user = response.data.data.profile;
		vm.user.name = response.data.data.profile.full_name.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
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