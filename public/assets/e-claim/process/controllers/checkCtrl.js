var checkCtrl = angular.module('checkCtrl', [])

checkCtrl.controller('checkController', function( $scope, $http, eclaimSettings, $state ){
	var vm = this;

	vm.updatePassData = {
		curr_password : "",
		new_password : "",
		retype_password : "",
	}

	vm.logout = function(){
		window.localStorage.clear();
		window.location.href = window.location.origin + '/member-portal-login';
	}

	vm.updatePasswordModalShow = function(){
		$("#update-pass-modal").modal('show');
	}

	vm.updatePassword = function( data ){
		if( !data.curr_password || !data.new_password || !data.retype_password){
			alert( "Please input all fields." );
		}
		if( data.new_password != data.retype_password ){
			alert( "Passwords did not match." );
			return false;
		}

		var pass = {
			oldpassword: data.curr_password ,
			password: data.new_password
		}

		eclaimSettings.updatePassword( pass )
		.then(function(response){
			if( response.data.result.status == true ){
				alert( response.data.result.web_message );
				$("#update-pass-modal").modal('hide');
			}else{
				alert( response.data.result.web_message );
			}
		});		
	}

	vm.onLoad = function(){
		// $http.get(window.location.origin + '/get-hr-session')
		// 	.then(function(response){
		// 		console.log(response);
		// 	});
	};

	vm.onLoad();
});
