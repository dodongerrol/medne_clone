var app = angular.module('app', []);
window.base_url = window.location.origin + '/app/';
app.filter('cmdate', [
'$filter', function($filter) {
    return function(input, format) {
      return $filter('date')(new Date(input), format);
    };
  }
]);
app.factory('AuthToken', function($window){
  var authTokenFactory = {};
  authTokenFactory.getToken = function( ) {
    return $window.localStorage.getItem('token');
  }
  authTokenFactory.setToken = function( token ) {
    console.log( token );
    if(token) {
      return $window.localStorage.setItem('token', token);
    } else {
      $window.localStorage.removeItem('token');
    }
  }
  return authTokenFactory;
});

app.factory('AuthInterceptor', function($q, $window, $injector, $rootScope, AuthToken){
  var interceptorFactory = {};
  interceptorFactory.request = function( config ) {
    var token = AuthToken.getToken( );

    if(token) {
      config.headers['Authorization'] = token;
    }
    // console.log(config);
    return config;
  };
  interceptorFactory.response = function( response ) {
    // console.log(response);
    return response;
  };
  interceptorFactory.requestError = function( response ) {
    return $q.reject(response);
  };
  interceptorFactory.responseError = function( response ) {
    console.log(response);
    // if(response.status == 403) {
    //   if(!response.config.headers.Authorization) {
    //     // window.location.href = window.location.origin + '/company-benefits-dashboard-login';
    //     $('#newdash_modal').modal('show');
    //     $('#newdash_message').text(response.data);
    //     $('#newdash_modal #login-status').show();
    //   }
    // } else if(response.status == 401) {
    //   $('#newdash_modal').modal('show');
    //   $('#newdash_message').text(response.data);
    //   $('#newdash_modal #login-status').show();
    // } else 

    if(response.status == 500 || response.status == 408) {
      $('#newdash_modal').modal('show');
      $('#newdash_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
      $('#newdash_modal #login-status').show();
    } else {
      $('#newdash_modal').modal('show');
      $('#newdash_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
      $('#newdash_modal #login-status').show();
    }
    return $q.reject(response);
  };
  return interceptorFactory;
});

app.config(function( $httpProvider ){
  $httpProvider.interceptors.push('AuthInterceptor');
});
app.directive('newDashboardDirective', [
	'$http',
	'$timeout',
	function directive($http, $timeout) {
		return {
			restrict: "A",
			scope: true,
			link: function link(scope, element, attributeSet) {
				console.log('newDashboardDirective');

				var monthToday = moment().format('MM');
				var monthToday2 = moment().format('MM');
				var yearToday = moment().format('YYYY');
				scope.loading = true;
				scope.showCustomPicker = false;
				scope.year_active = 1;
				scope.showTodayDate = false;

				var date_slider = null;

				scope.rangePicker_start = moment().startOf('year').format( 'DD/MM/YYYY' );
				scope.rangePicker_end = moment().format( 'DD/MM/YYYY' );

				scope.getFirstEndDate = function( firstMonth, lastMonth ){
					firstMonth = moment( firstMonth + " " + yearToday ,'MM YYYY').format('YYYY-MM-DD');
					lastMonth = moment( lastMonth + " " + yearToday,'MM YYYY').format('YYYY-MM-DD');

					var date1 = new Date(firstMonth);
					var date2 = new Date(lastMonth);
					var y1 = date1.getFullYear();
					var m1 = date1.getMonth();
					var y2 = date2.getFullYear();
					var m2 = date2.getMonth();
					var firstDay = new Date(y1, m1, 1);
					var lastDay = new Date(y2, m2 + 1, 0);

					firstDay = moment(firstDay).format('YYYY-MM-DD');
					lastDay = moment(lastDay).format('YYYY-MM-DD');

					// console.log(firstDay);
					// console.log(lastDay);

					return {
						start: firstDay,
						end: lastDay,
						// user_id: scope.user_details.UserID
					}
				}

				scope.getTransactions = function( data ){
					scope.loading = true;
					// console.log(data);
					$http.post(window.base_url + 'clinic/transaction_lists',data)
						.then(function(response){
							console.log(response);
							scope.loading = false;
							scope.trans_data = response.data.data;
						});
				}

				scope.setDateToday = function(){
					// scope.rangePicker_start = moment( ).format( 'DD/MM/YYYY' );
					// scope.rangePicker_end = moment( ).format( 'DD/MM/YYYY' );
					// $("#rangePicker_start").text( scope.rangePicker_start );
					// $("#rangePicker_end").text( scope.rangePicker_end );

					// $('.btn-custom-end').data('daterangepicker').setMinDate( scope.rangePicker_start );
					scope.showCustomPicker = false;
					scope.showTodayDate = true;
					scope.year_active = 4;
					$( '.showCustomPickerTrue' ).hide();

					var activity_search = {
				  	start: moment().format('YYYY-MM-DD'),
						end: moment().format('YYYY-MM-DD'),
				  };
				  console.log(activity_search);
					scope.getTransactions( activity_search );

					$timeout(function() {
						$(".rangePicker_start").text( moment( ).format( 'DD/MM/YYYY' ) );
					}, 500);
				}

				scope.showCustomDate = function( num ){
					scope.year_active = num;
					scope.showTodayDate = false;
					scope.showCustomPicker = true;
					$( '.showCustomPickerTrue' ).hide();

					setTimeout(function() {
						
						$('.btn-custom-start').daterangepicker({
							autoUpdateInput : true,
							autoApply : true,
							singleDatePicker: true,
							startDate : moment( scope.rangePicker_start, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
						}, function(start, end, label) {

						  scope.rangePicker_start = moment( start ).format( 'DD/MM/YYYY' );
							$("#rangePicker_start").text( scope.rangePicker_start );

							$('.btn-custom-end').data('daterangepicker').setMinDate( start );

							if( scope.rangePicker_end && ( scope.rangePicker_end > scope.rangePicker_start ) ){
								var activity_search = {
							  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
									end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
							  };
							  // console.log(activity_search);
								scope.getTransactions( activity_search );
							}else{
								scope.rangePicker_end = moment( start ).format( 'DD/MM/YYYY' );
								$("#rangePicker_end").text( scope.rangePicker_end );
							}
						});

						$('.btn-custom-end').daterangepicker({
							autoUpdateInput : true,
							autoApply : true,
							singleDatePicker: true,
							startDate : moment( scope.rangePicker_end, 'DD/MM/YYYY' ).format( 'MM/DD/YYYY' ),
						}, function(start, end, label) {
						  
						  scope.rangePicker_end = moment( end ).format( 'DD/MM/YYYY' );
							$("#rangePicker_end").text( scope.rangePicker_end );

							var activity_search = {
						  	start: moment(scope.rangePicker_start,'DD/MM/YYYY').format('YYYY-MM-DD'),
								end: moment(scope.rangePicker_end,'DD/MM/YYYY').format('YYYY-MM-DD'),
						  };
						  // console.log(activity_search);
							scope.getTransactions( activity_search );
						});

						$("#rangePicker_start").text( scope.rangePicker_start );
						$("#rangePicker_end").text( scope.rangePicker_end );

					}, 100);
				}

				scope.setYear = function( num ){
					$( '.showCustomPickerTrue' ).fadeIn();
					scope.showCustomPicker = false;
					scope.showTodayDate = false;
					scope.initializeRangeSlider( );

					scope.year_active = num;
					if( num == 1 ){
						yearToday = moment().format('YYYY');
					}else{
						yearToday = moment().subtract(1,'years').format('YYYY');
					}

					var range_data = date_slider.getValue();

					monthToday = range_data[0];
		    	monthToday2 = range_data[1];

		    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
					scope.getTransactions( activity_search );
				}

				scope.initializeRangeSlider = function( ){

					date_slider = new Slider("#timeframe-range", { 
						id: "timeframe-range", 
						min: 1, 
						max: 12, 
						range: true, 
						value: [1,parseInt(monthToday2)],
						// value: [parseInt(monthToday), parseInt(monthToday2)],
						ticks: [1,2,3,4,5,6,7,8,9,10,11,12],
						ticks_labels: ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'],
						tooltip : 'hide',
						ticks_tooltip : false,
					});

					var slide_trap = null;

					$( '#timeframe-range' ).on('slideStop', function(ev){
						clearTimeout(slide_trap);

				    slide_trap = setTimeout(function() {
				    	var range_data = date_slider.getValue();

				    	var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
				    	console.log(activity_search);
							scope.getTransactions( activity_search );
				    }, 800);
					});
				}

				scope.onLoad = function( ){
					scope.initializeRangeSlider();

					setTimeout(function() {
						var range_data = date_slider.getValue();

				    var activity_search = scope.getFirstEndDate( range_data[0], range_data[1] );
						// var activity_search = scope.getFirstEndDate( monthToday , monthToday );
						// console.log(activity_search);
						scope.getTransactions( activity_search );
					}, 500);
				}

				scope.onLoad();
			}
		}
	}
])

.directive('userDirective', ['$http', function($http) {
  return {
    restrict: 'AE',

    template: '{{data.Name}} - <span ng-if="data.UserType == 1">Public User</span> <span ng-if="data.UserType == 5 && data.access_type == 1">Invidual User</span><span ng-if="data.UserType == 5 && data.access_type == 0">Corporate User</span>',
    scope: {
      id: '@user'
    },
    link: function link(scope, element, attrs) {
      scope.data;
      $http({
        method: 'GET',
        url: base_url + 'clinic/get/user/details/' + attrs.user
      }).then(function(result) {
          scope.data = result.data[0];
      }, function(result) {
        console.log("Error: No data returned");
      });
    }
  };
}])
