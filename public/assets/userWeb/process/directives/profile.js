app.directive('profileDirective', [
	"$http",
	"profilesModule",
	"Upload",
	"serverUrl",
	function directive( $http, profilesModule, Upload, serverUrl ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("profile Directive Runnning !");
				scope.user = {};
				scope.user_credentials = {};
				scope.add_medication = {};
				scope.medical_list = {};
				$( ".sidebar ul li" ).removeClass('active');
				$( ".sidebar ul li#profile_li" ).addClass('active');

				scope.$watch('picFile', function(newVal, oldVal) {
					// console.log(newVal);
					if(newVal !== oldVal) {
						scope.user.file = newVal;
					} else {
						// console.log('empty');
					}
				  });

				$(".modal").on("hidden.bs.modal", function () {
				    scope.userInfo( );
				});

				scope.formatDate = function(date) {
					return moment(date).format('MMM D, YYYY');
				};

				scope.userInfo = function userInfo( ) {
					$http.get(serverUrl.url + 'auth/userprofile')
					.success(function(response){
						scope.user = response.data.profile;
						scope.medical_list = response.data;
						scope.add_medication.userid = response.data.profile.user_id;
					})
					.error(function(err){
						
					});
				};

				scope.showForm = function(){
					$(".form").fadeIn();
					$(".default").hide();
				};

				scope.hideForm = function(){
					$(".default").fadeIn();
					$(".form").hide();
				};

				scope.onLoad = function(){
					$('.modal').on('hidden.bs.modal', function (e) {
					  $(".default").fadeIn();
						$(".form").hide();
					});

					$('.modal').modal('hide');
				};

				scope.updateUserProfile = function( ) {
					var btn = $('#update-profile').text();
					$('#update-profile').text('Updating Profile...');
					$('#update-profile').attr('disabled', true);
					var user = {
						age: scope.user.age,
						blood_type: scope.user.blood_type,
						bmi: scope.user.bmi,
						dob: scope.user.dob,
						email: scope.user.email,
						fin: scope.user.fin,
						full_name: scope.user.full_name,
						height: scope.user.height,
						insurance_company: scope.user.insurance_company,
						insurance_policy_name: scope.user.insurance_policy_name,
						insurance_policy_no: scope.user.insurance_policy_no,
						mobile_phone: scope.user.mobile_phone,
						nric: scope.user.nric,
						photo_url: scope.user.photo_url,
						user_id: scope.user.user_id,
						weight: scope.user.weight,
						file: scope.user.file
					};
					var upload = profilesModule.updateProfile(user)
					upload.then(function(response){
						// console.log(response);
						$('#update-profile').text(btn);
						$('#update-profile').attr('disabled', false);
						scope.picFile = false;
						scope.userInfo( );
					});
					upload.progress(function( evt ) {
				    	// console.log( evt );
				    	scope.progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
				    });
				};

				scope.updateCredentials = function( ) {
					// console.log(scope.user_credentials);
					var btn = $('#update-pass').text();
					$('#update-pass').attr('disabled', true);
					$('#update-pass').text('Updating Password...');
					if(scope.user_credentials.password == scope.user_credentials.retype_password) {
						profilesModule.updateUserCredentials(scope.user_credentials)
						.success(function(response){
							// console.log(response);
							$('#update-pass').attr('disabled', false);
							$('#update-pass').text(btn);
							$.alert({
							    title: '',
							    content: response.web_message,
							    columnClass: 'small'
							});
							scope.user_credentials = {};
						})
						.error(function(err){
							// console.log(err);
						});
					} else {
						$('#update-pass').attr('disabled', false);
						$('#update-pass').text(btn);
						// alert(response.data.message);
						$.alert({
						    title: '',
						    content: 'Password did not match on New Password and Confirm Password!',
						    columnClass: 'small'
						});
					}
				};

				scope.addMedication = function( ) {
					// console.log(scope.add_medication);
					profilesModule.addMedication(scope.add_medication)
					.success(function(response){
						// console.log(response);
						scope.add_medication = {};
						scope.userInfo( );
						scope.hideForm();
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.delete = function(type, data) {
					// console.log(type);
					// console.log(data);
					if(type == 'history') {
						profilesModule.deleteMedicalHistory(data.record_id)
						.success(function(response){
							// console.log(response);
							if(response.status == true) {
								scope.medical_list.history.splice(scope.medical_list.history.indexOf(data), 1);
								scope.userInfo( );
							}
						})
						.error(function(err){
							// console.log(err);
						});
					} else if(type == 'medication') {
						profilesModule.deleteMedication(data.medication_id)
						.success(function(response){
							// console.log(response);
							if(response.status == true) {
								scope.medical_list.medications.splice(scope.medical_list.medications.indexOf(data), 1);
								scope.userInfo( );
							}
						})
						.error(function(err){
							// console.log(err);
						});
					} else if(type == 'allergy') {
						profilesModule.deleteAllergy(data.allergy_id)
						.success(function(response){
							// console.log(response);
							if(response.status == true) {
								scope.medical_list.allergies.splice(scope.medical_list.allergies.indexOf(data), 1);
								scope.userInfo( );
							}
						})
						.error(function(err){
							// console.log(err);
						});
					} else if(type == 'condition') {
						profilesModule.deleteMedicalCondition(data.condition_id)
						.success(function(response){
							// console.log(response);
							if(response.status == true) {
								scope.medical_list.conditions.splice(scope.medical_list.conditions.indexOf(data), 1);
								scope.userInfo( );
							}
						})
						.error(function(err){
							// console.log(err);
						});
					}
				};

				scope.addMedicalHistory = function( ) {
					scope.add_medication.user_id = scope.add_medication.userid;
					profilesModule.addMedicalHistory(scope.add_medication)
					.success(function(response){
						// console.log(response);
						scope.add_medication = {};
						scope.userInfo( );
						scope.hideForm();
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.addAllergy = function( ) {
					profilesModule.addAllergy(scope.add_medication)
					.success(function(response){
						// console.log(response);
						scope.add_medication = {};
						scope.userInfo( );
						scope.hideForm();
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.addMedicalCondition = function( ) {
					profilesModule.addMedicalCondition(scope.add_medication)
					.success(function(response){
						// console.log(response);
						scope.add_medication = {};
						scope.userInfo( );
						scope.hideForm();
					})
					.error(function(err){
						// console.log(err);
					});
				};

				scope.onLoad();
				scope.userInfo( );
			}
		}
	}
]);
