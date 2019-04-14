{{ HTML::style('assets/css/np-autocomplete.min.css') }}
{{ HTML::script('assets/care-plan/js/angular.min.js') }}
{{ HTML::script('assets/js/np-autocomplete.min.js') }}
{{ HTML::script('assets/js/ngStorage.min.js') }}
<style type="text/css">
	.modal-content {
		border-radius: 10px!important;
	}
	body
	{
		font-family: 'sans-serif'
	}
	#group-list .list-group
	{
		width: 180px!important;
	}
</style>
<div class="input-group" style="width: 100%!important;" ng-app="app" search-directive>
   <!-- <input id="search-customer-feature" type="text" class="form-control" placeholder="Search IC Number" name="" style="height: 32px !important;"> -->
  <!-- <div > -->
  <form ng-submit="searchFrominputText()">
	  <div ng-repeat="list in temp_list" np-autocomplete="options" ng-model="list.id" np-input-model="list.nric" id="group-list">
			<input type="text" placeholder="Search NRIC" name="nric" id="nric" class="form-control" style="height: 32px !important;width: 60%"/>
			<!-- <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate form-control-feedback" aria-hidden="true"></span> -->
	    <span class="input-group-btn">
	      <button ng-click="searchFrominputText()" class="btn btn-default btn-search-top" type="button" style="background: #CADDEC;width: 30px;border-top-left-radius: 0;border-bottom-left-radius: 0;">
	      	<i class="fa fa-search" style="color: #1868AC"></i>
	      </button>
	    </span>
		</div>
  </form>
  <!-- </div> -->

  <!-- modal -->
  <div class="modal fade" id="user-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document" style="width: 30%!important;">
	    <div class="modal-content">
	      <div class="modal-header" style="background: #0392cf!important;border-top-left-radius: 10px;border-top-right-radius: 10px;">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" style="padding: 10px;">Mednefits Member<b></b></h4>
	      </div>
	      <div class="modal-body" style="background: #f0f3f5!important;overflow: hidden;">
	      	<div class="col-md-10 col-md-offset-1">
	      	 <div class="info-container" style="width: inherit;height: auto;background: #ffffff;border-radius: 10px;    box-shadow: 3px 3px 5px #adb0b1;border-left: 1px solid #adb0b1;border-top: 1px solid #adb0b1;">
	      	 		<div class="info-details" style="padding: 40px;">
	      				<img src="{{ URL::asset('images/mednefits_logo.png') }}" style="width: 55px;">
	      	 			<h4 style="margin: 0;margin-top: 30px;color:#2fafe3;"><b ng-bind="user_details.fullname"></b></h4>
	      	 			<h4 style="margin: 0;margin-top: 5px;color:#2fafe3;"><b ng-bind="user_details.nric"></b></h4>
	      	 			<br />
	      	 			<h5 style="margin: 0;margin-top: 10px;color: #4f535d;">Member ID <span ng-bind="user_details.member_id"></span></h5>
	      	 			<h5 style="margin: 0;margin-top: 5px;color: #4f535d;">Mednefits Care Plan (<span ng-bind="user_details.plan_type"></span>)</h5>
	      	 			<h5 style="margin: 0;margin-top: 5px;color: #4f535d;">Start Date <span ng-bind="user_details.start_date"></span></h5>
	      	 			<h5 style="margin: 0;margin-top: 5px;color: #4f535d;">Valid Thru <span ng-bind="user_details.valid_date"></span></h5>
	      	 			<hr style="margin-top: 30px;border-top: 2px solid #eee;">
	      	 		</div>
	      	 </div>
	      	</div>
	      </div>
	       <div class="modal-body text-center" style="background: #f0f3f5!important;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;">
	      	<button class="btn btn-primary btn-lg" style="background:#0392cf!important;" ng-click="saveClaim(user_details)">Claim</button>
	      </div>
	    </div>
	  </div>
	</div>
</div>


<script type="text/javascript">
	// $(document).ready(function( ){
			var app = angular.module('app', ['ng-pros.directive.autocomplete', 'ngStorage']);
			window.base_url = window.location.origin + '/app/';
			window.base_loading_image = '<img src="'+ window.location.origin +'/assets/images/loading.svg" width="32" height="32" alt=""/>';

			app.directive('searchDirective', [
			'$http',
			'$localStorage',
			function directive($http, $localStorage) {
				return {
					restrict: "A",
					scope: true,
					link: function link(scope, element, attributeSet) {
						// $('#user-details').modal('show');
						console.log('searchDirective');
						scope.temp_list = [];
						scope.temp_list = [{
							nric: '',
							procedure: '',
							book_date: '',
							display_book_date: '',
							id: '',
							nric: '',
							amount: '',
							user_type: '',
							access_type: ''
						}];

						scope.user_details = {};

					  scope.options = {
							url: base_url + 'clinic/get/all/special/users',
							delay: 500,
							nameAttr: 'nric',
							minlength: 4,
							dataHolder: 'items',
							limitParam: 'per_page',
							searchParam: 'q',
							// loadStateClass: 'has-feedback',
							highlightExactSearch: 'false',
							itemTemplate: '<button type="button" ng-class="getItemClasses($index)" ng-mouseenter="onItemMouseenter($index)" ng-repeat="item in searchResults" ng-click="select(item)">' +
							'<div class="media">' +
							'<div class="media-left">' +
							'<img class="media-object img-circle" ng-src="@{{item.image}}" alt="@{{item.image}}" width="48" ng-if="item.image"/>' +
							'<img class="media-object img-circle" src="https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png" alt="default-image" width="48" ng-if="!item.image"/>' +
							'</div>' +
							'<div class="media-body">' +
							'<h5 class="media-heading"><strong ng-bind-html="highlight(item.name)"></strong></h5>' +
							'<span ng-bind-html="highlight(item.nric)"></span>' +
							'<br />' +
							'<span ng-if="item.user_type == 1">Public User</span>' +
							'<span ng-if="item.user_type == 5 && item.access_type == 0"><corporate-directive corporate="@{{item.id}}"></corporate-directive></span>' +
							'<span ng-if="item.user_type == 5 && item.access_type == 1">Invidual Member</span>' +
							'</div>' +
							'</div>' +
							'</button>',
							onSelect: getItem
						};

						scope.searchFrominputText = function( ) {
							var data = $('#nric').val();
							console.log(data);
							jQuery.blockUI({message: '<h1> ' + base_loading_image + ' </h1>'});
							$http.post(base_url + 'clinic/get/nric_user', { search: data })
							.success(function(response){
								console.log(response.length);
								if(response.length > 0) {
									getItem(response[0]);
								} else {
									$.alert({
			                title: 'Alert!',
			                content: 'IC Number not found!',
			                columnClass: 'col-md-4 col-md-offset-4',
			                    theme: 'material',
			                confirm: function(){
			                    jQuery.unblockUI();
			                }
			            });
								}
							});
						}

						function getItem(data) {
							console.log(data);
							if(data.id) {
								var id = data.id
							} else if(data.UserID) {
								var id = data.UserID
							}
							$http.get(base_url + 'clinic/get/special_user/details/' + id)
							.success(function(response){
								if(response == -1) {
									alert("Plan Start of this User haven't started");
									return false;
								} else {
									scope.user_details = response;
									$('#user-details').modal('show');
								}
								jQuery.unblockUI();
								// console.log(scope.user_details);
							});
						};

						scope.saveClaim = function(data) {
							console.log(data);
							$localStorage.member = data;
							window.location.href = base_url + 'setting/claim-report'
							// $localStorage.$reset();
						};
					}
				}
			}
		])
		.directive('corporateDirective', ['$http', function($http) {
	    return {
	      restrict: 'AE',

	      template: '@{{data}}',
	      scope: {
	        id: '@corporate'
	      },
	      link: function link(scope, element, attrs) {
	        scope.data;
	        console.log(attrs.corporate);
	        $http({
	          method: 'GET',
	          url: base_url + 'clinic/get/corporate/name/' + attrs.corporate
	        }).then(function(result) {
	            scope.data = result.data;
	          console.log(result);
	        }, function(result) {
	          console.log("Error: No data returned");
	        });
	      }
	    };
	  }]);
	// })
</script>