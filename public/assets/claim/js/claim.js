var app = angular.module("app", [
  "ng-pros.directive.autocomplete",
  "ngStorage",
  "multipleSelect",
  "LocalStorageModule",
]);
window.base_url = window.location.origin + "/app/";

app.filter("cmdate", [
  "$filter",
  function($filter) {
    return function(input, format) {
      if (input && format) {
        return $filter("date")(new Date(input), format);
      }
    };
  }
]);

app.factory('AuthToken', function($window){
  var authTokenFactory = {};
  authTokenFactory.getToken = function( ) {
    return $window.localStorage.getItem('token');
  }
  authTokenFactory.setToken = function( token ) {
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
    // if(response.status == 403) {
    //   if(!response.config.headers.Authorization) {
    //     // window.location.href = window.location.origin + '/company-benefits-dashboard-login';
    //     $('#claim_modal').modal('show');
    //     $('#claim_message').text(response.data);
    //     $('#claim_modal #login-status').show();
    //   }
    // } else if(response.status == 401) {
    //   $('#claim_modal').modal('show');
    //   $('#claim_message').text(response.data);
    //   $('#claim_modal #login-status').show();
    // } else 

    if(response.status == 500 || response.status == 408) {
      $('#claim_modal').modal('show');
      $('#claim_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
      $('#claim_modal #login-status').show();
    } else {
      $('#claim_modal').modal('show');
      $('#claim_message').text('Ooops! Something went wrong. Please check you internet connection or reload the page.');
      $('#claim_modal #login-status').show();
    }
    return $q.reject(response);
  };
  return interceptorFactory;
});

app.factory('socket', function ($rootScope) {
  var socket = io.connect('https://sockets.medicloud.sg');
  return {
    on: function (eventName, callback) {
      socket.on(eventName, function () {  
        var args = arguments;
        $rootScope.$apply(function () {
          callback.apply(socket, args);
        });
      });
    },
    emit: function (eventName, data, callback) {
      socket.emit(eventName, data, function () {
        var args = arguments;
        $rootScope.$apply(function () {
          if (callback) {
            callback.apply(socket, args);
          }
        });
      })
    }
  };
});

app.config(function( $httpProvider ){
  $httpProvider.interceptors.push('AuthInterceptor');
});

app.directive('validNumber', function() {
  return {
    require: '?ngModel',
    link: function(scope, element, attrs, ngModelCtrl) {
      if(!ngModelCtrl) {
        return; 
      }

      ngModelCtrl.$parsers.push(function(val) {
        if (angular.isUndefined(val)) {
            var val = '';
        }
        var clean = val.replace(/[^0-9\.]/g, '');
        var decimalCheck = clean.split('.');

        if(!angular.isUndefined(decimalCheck[1])) {
            decimalCheck[1] = decimalCheck[1].slice(0,2);
            clean =decimalCheck[0] + '.' + decimalCheck[1];
        }

        if (val !== clean) {
          ngModelCtrl.$setViewValue(clean);
          ngModelCtrl.$render();
        }
        return clean;
      });

      element.bind('keypress', function(event) {
        if(event.keyCode === 32) {
          event.preventDefault();
        }
      });
    }
  };
});

app.directive("claimDirective", [
  "$http",
  "$localStorage",
  "localStorageService",
  "socket",
  function directive($http, $localStorage, localStorageService, socket) {
    return {
      restrict: "A",
      scope: true,
      link: function link(scope, element, attributeSet) {
        console.log("claimDirective");
        scope.claim_list = [];
        scope.services = {};
        scope.temp_list = [];
        scope.onlyNumbers = /^\d+$/;
        scope.load_status = false;
        scope.notification = {};
        scope.clinic = {};
        scope.service_list = [];
        scope.service_selected = [];
        scope.service_length = [0];
        scope.vist_time_day = "AM";
        scope.search = "";
        scope.search_btn_status = false;

        scope.selected_hour = parseInt(moment().format("hh"));
        scope.selected_minute = parseInt(moment().format("mm"));
        var socket = io.connect('https://sockets.medicloud.sg');

        var visit_date_dp = null;

        if ($localStorage.member) {
          scope.temp_list = [
            {
              nric: $localStorage.member.nric,
              procedure: "",
              book_date: "",
              display_book_date: "",
              time: "",
              id: $localStorage.member.member_id,
              amount: null,
              user_type: "",
              access_type: "",
              back_date: 0,
              health_provider: 0,
              automatic: true,
              multiple_procedures: false,
              procedure_ids: []
            }
          ];
        } else {
          scope.temp_list = [
            {
              nric: "",
              procedure: "",
              book_date: "",
              display_book_date: "",
              id: "",
              time: "",
              amount: null,
              user_type: "",
              access_type: "",
              back_date: 0,
              health_provider: 0,
              automatic: false,
              multiple_procedures: false,
              procedure_ids: []
            }
          ];
        }

        scope.options = {
          url: base_url + "clinic/get/all/users",
          delay: 500,
          nameAttr: "nric",
          minlength: 4,
          dataHolder: "items",
          limitParam: "per_page",
          searchParam: "q",
          // loadStateClass: 'has-feedback',
          onSelect: function(item) {
            $http.get(base_url + "clinic/get/special_user/details/" + item.id)
              .success(function(response){
                // console.log(response);
                if(response.public_user && response.no_data) {

                } else {
                  $('#e-card-modal').modal('show');
                  $('#e-card-modal #ecard-name').text( response.fullname );
                  $('#e-card-modal #ecard-member-id').text( response.member_id );
                  $('#e-card-modal #ecard-plan-type').text( response.plan_type );
                  $('#e-card-modal #ecard-company').text( response.company_name );
                  $('#e-card-modal #ecard-start-date').text( response.start_date );
                  $('#e-card-modal #ecard-valid-date').text( response.valid_date );

                  for( var i = 0; i < response.packages.length; i++ ){
                    $('#e-card-modal .coverage-box').append('<div class="cov-item"><div class="col-md-4" style="padding-left: 0;">' + response.packages[i].package_name  + '</div><div class="col-md-6">' + response.packages[i].package_description  + '</div></div>');
                  }
                }
              })
          },
          highlightExactSearch: "false",
          itemTemplate:
            '<button type="button" ng-class="getItemClasses($index)" ng-mouseenter="onItemMouseenter($index)" ng-repeat="item in searchResults" ng-click="select(item)">' +
            '<div class="media">' +
            '<div class="media-left">' +
            '<img class="media-object img-circle" ng-src="{{item.image}}" alt="{{item.image}}" width="48" ng-if="item.image"/>' +
            '<img class="media-object img-circle" src="https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png" alt="default-image" width="48" ng-if="!item.image"/>' +
            "</div>" +
            '<div class="media-body">' +
            '<h5 class="media-heading"><strong ng-bind-html="highlight(item.name)"></strong></h5>' +
            '<span ng-bind-html="highlight(item.email)"></span>' +
            "<br />" +
            '<span ng-bind-html="highlight(item.nric)"></span>' +
            "<br />" +
            '<span ng-if="item.user_type == 1">Public User</span>' +
            '<span ng-if="item.user_type == 5 && item.access_type == 0">Corporate User</span>' +
            '<span ng-if="item.user_type == 5 && item.access_type == 1">Invidual User</span>' +
            '<span ng-if="item.user_type == 5 && item.access_type == 2">Dependent User</span>' +
            '<span ng-if="item.user_type == 5 && item.access_type == 3">Dependent User</span>' +
            "</div>" +
            "</div>" +
            "</button>"
        };

        scope.conversion = {
          sgd : null,
          myr : null,
          current_sgd : 3.00,
          current_myr : 3.00,
          sgd_to_myr : false,
          myr_to_sgd : true,
        }

        // scope.conversion.current_sgd = 3.00;

        scope.switchConvertion = function() {
          if( scope.conversion.sgd_to_myr == true ){
            scope.conversion.sgd_to_myr = false;
            scope.conversion.myr_to_sgd = true;
          }else{
            scope.conversion.sgd_to_myr = true;
            scope.conversion.myr_to_sgd = false;
          }
        } 

        // scope.getMyrValue = function() {
        //   // https://min-api.cryptocompare.com/data/price?fsym=SGD&tsyms=MYR
        //   $.ajax({
        //     url: 'https://free.currencyconverterapi.com/api/v5/convert?q=MYR_SGD&compact=y',
        //     success: function(response){
        //       // console.log(response);
        //       scope.conversion.current_sgd = response.MYR_SGD.val;
        //     }
        //   });
        //   $.ajax({
        //     url: 'https://free.currencyconverterapi.com/api/v5/convert?q=SGD_MYR&compact=y',
        //     success: function(response){
        //       // console.log(response);
        //       scope.conversion.current_myr = response.SGD_MYR.val;
        //     }
        //   });
        // }

        scope.convertToMyr = function( value ) {
          scope.conversion.myr = ( value * scope.conversion.current_myr );
        }

        scope.convertToSgd = function( value ) {
          scope.conversion.sgd = ( value / scope.conversion.current_sgd );
        }

        scope.clearCurrency = function( ) {
          scope.conversion.sgd = null;
          scope.conversion.myr = null;
        }

        scope.convertCurrency = function( ) {
          if( scope.conversion.sgd_to_myr == true ){
            if( scope.conversion.sgd != null ){
              scope.conversion.myr = ( scope.conversion.sgd * scope.conversion.current_myr );
            }
          }
          if( scope.conversion.myr_to_sgd == true ){
            if( scope.conversion.myr != null ){
              scope.conversion.sgd = ( scope.conversion.myr * scope.conversion.current_sgd );
            }
          } 
        }

        scope.setFilterMonth = function( month ){
          scope.filterMonthValue = month;
          scope.setTransFilterData();
        }

        scope.yearRange = function(num){
          var curr_year = moment().format('YYYY');
          var arr = [];
          arr.push(curr_year);
          for( var i = num; i > 0; i-- ){
            arr.push(curr_year-i);
          }
          return arr;
        }

        scope.monthList = moment.months();
        scope.yearList = moment().years();
        scope.filterMonthValue = moment().format('MMMM');
        scope.filterYearValue = moment().year();
        scope.transFilterTrap = false;
        scope.transFilterData = {
          start_date: moment().startOf('month').format("YYYY-MM-DD"),
          end_date: moment().endOf('month').format("YYYY-MM-DD")
        }
        $("#rangePicker_start").text( moment( ).startOf('month').format( 'MM/DD/YYYY' ) );
        $("#rangePicker_end").text( moment( ).endOf('month').format( 'MM/DD/YYYY' ) );
        scope.isLoading = false;
        scope.isFiltered = false;

        scope.setFilterMonth = function( month ){
          scope.filterMonthValue = month;
          scope.setTransFilterData();
        }

        scope.setFilterYear = function( year ){
          scope.filterYearValue = year;
          scope.setTransFilterData();
        }

        scope.setTransFilterData  = function( ){
          scope.transFilterData = {
            start_date: moment( scope.filterMonthValue + " " + scope.filterYearValue ,'MMMM YYYY' ).startOf('month').format("YYYY-MM-DD"),
            end_date: moment( scope.filterMonthValue + " " + scope.filterYearValue ,'MMMM YYYY' ).endOf('month').format("YYYY-MM-DD")
          }
        }

        scope.toggleTransFilter = function(){
          if( scope.transFilterTrap ){
            scope.transFilterTrap = false;
          }else{
            scope.transFilterTrap = true;
          }
        }

        scope.toggleBackLoading = function(){
          if( scope.isLoading == false ){
            $("#isLoading").show();
            $("#isNotLoading").hide();
            scope.isLoading = true;
          }else{
            $("#isLoading").hide();
            $("#isNotLoading").show();
            scope.isLoading = false;
          }
          
        }

        scope.filterByDate = function(){
          scope.toggleBackLoading();
          scope.isFiltered = true;
          scope.transFilterTrap = false;
          $("#rangePicker_start").text( moment( scope.transFilterData.start_date ).startOf('month').format( 'MM/DD/YYYY' ) );
          $("#rangePicker_end").text( moment( scope.transFilterData.end_date ).endOf('month').format( 'MM/DD/YYYY' ) );
          $http.post(base_url + "clinic/search_by_nric_transactions", {
              nric: scope.search,
              start_date: scope.transFilterData.start_date,
              end_date: scope.transFilterData.end_date,
            })
            .success(function(response) {
              scope.backdate_list = response;
              scope.toggleBackLoading();
            });
        }

        scope.showVisitTime = function(ev) {
          $(".time-select-container").hide();
          $(ev.target).closest(".form-group").find(".time-select-container").show();
          scope.setVisitTime();
        };

        scope.hideVisitTime = function(ev) {
          $(ev.target).closest(".form-group").find(".time-select-container").hide();
        };

        scope.addHour = function( opt ) {
          if( scope.isEditOpen == false ){
            if (scope.selected_hour < 12) {
              scope.selected_hour++;
            } else {
              scope.selected_hour = 1;
            }
          }else{
            if( opt == 'visit' ){
              if (scope.row_edit_selected.visit.selected_hour < 12) {
                scope.row_edit_selected.visit.selected_hour++;
              } else {
                scope.row_edit_selected.visit.selected_hour = 1;
              }
            }

            if( opt == 'claim' ){
              if (scope.row_edit_selected.claim.selected_hour < 12) {
                scope.row_edit_selected.claim.selected_hour++;
              } else {
                scope.row_edit_selected.claim.selected_hour = 1;
              }
            }
          }
          scope.setVisitTime( opt );
        };

        scope.deductHour = function( opt ){
          if( scope.isEditOpen == false ){
            if (scope.selected_hour > 1) {
              scope.selected_hour--;
            } else {
              scope.selected_hour = 12;
            }
          }else{
            if( opt == 'visit' ){
              if (scope.row_edit_selected.visit.selected_hour > 1) {
                scope.row_edit_selected.visit.selected_hour--;
              } else {
                scope.row_edit_selected.visit.selected_hour = 12;
              }
            }

            if( opt == 'claim' ){
              if (scope.row_edit_selected.claim.selected_hour > 1) {
                scope.row_edit_selected.claim.selected_hour--;
              } else {
                scope.row_edit_selected.claim.selected_hour = 12;
              }
            }
          }

          scope.setVisitTime( opt );
        };

        scope.addMinute = function( opt ){
          if( scope.isEditOpen == false ){
            if (scope.selected_minute < 59) {
              scope.selected_minute++;
            } else {
              scope.selected_minute = 0;
            }
          }else{
            if( opt == 'visit' ){
              if (scope.row_edit_selected.visit.selected_minute < 59) {
                scope.row_edit_selected.visit.selected_minute++;
              } else {
                scope.row_edit_selected.visit.selected_minute = 0;
              }
            }

            if( opt == 'claim' ){
              if (scope.row_edit_selected.claim.selected_minute < 59) {
                scope.row_edit_selected.claim.selected_minute++;
              } else {
                scope.row_edit_selected.claim.selected_minute = 0;
              }
            }
          }

          scope.setVisitTime(opt);
        };

        scope.deductMinute = function( opt ){
          if( scope.isEditOpen == false ){
            if (scope.selected_minute > 0) {
              scope.selected_minute--;
            } else {
              scope.selected_minute = 59;
            }
          }else{
            if( opt == 'visit' ){
              if (scope.row_edit_selected.visit.selected_minute > 0) {
                scope.row_edit_selected.visit.selected_minute--;
              } else {
                scope.row_edit_selected.visit.selected_minute = 59;
              }
            }

            if( opt == 'claim' ){
              if (scope.row_edit_selected.claim.selected_minute > 0) {
                scope.row_edit_selected.claim.selected_minute--;
              } else {
                scope.row_edit_selected.claim.selected_minute = 59;
              }
            }
          }

          scope.setVisitTime(opt);
        };

        scope.setVisitTime = function( opt ){
          if( scope.isEditOpen == false ){
            var hour = "" + (scope.selected_hour < 10 ? 0 : "") + scope.selected_hour + ":" + (scope.selected_minute < 10 ? 0 : "") + scope.selected_minute;
            scope.temp_list[0].time = hour;
          }else{
            if( opt == 'visit' ){
              var hour = "" + (scope.row_edit_selected.visit.selected_hour < 10 ? 0 : "") + scope.row_edit_selected.visit.selected_hour + ":" + (scope.row_edit_selected.visit.selected_minute < 10 ? 0 : "") + scope.row_edit_selected.visit.selected_minute;
              scope.row_edit_selected.visit_time = hour;
            }

            if( opt == 'claim' ){
              var hour = "" + (scope.row_edit_selected.claim.selected_hour < 10 ? 0 : "") + scope.row_edit_selected.claim.selected_hour + ":" + (scope.row_edit_selected.claim.selected_minute < 10 ? 0 : "") + scope.row_edit_selected.claim.selected_minute;
              scope.row_edit_selected.time_claim = hour;
            }
            
          }
        };

        scope.visitTimeDayChanged = function(opt){
          scope.vist_time_day = opt;
        };
        scope.editVisitTimeDayChanged = function(opt){
          scope.row_edit_selected.visit_period = opt;
        };
        scope.editClaimTimeDayChanged = function(opt){
          scope.row_edit_selected.period_claim = opt;
        };

        scope.addServiceLength = function(num) {
          scope.service_length.push(num);
        };

        scope.removeServiceLength = function(num) {
          scope.service_selected.splice( $.inArray(scope.service_selected[num], scope.service_selected), 1);
          scope.temp_list[0].procedure_ids.splice( $.inArray(scope.service_selected[num], scope.service_selected), 1);
          if (scope.service_length.length != 1) {
            scope.service_length.pop();
          }
        };

        scope.removeServiceFromArray = function(list) {
          scope.service_list[ $.inArray(list, scope.service_list) ].selected = false;
          var index = $.inArray(list, scope.service_selected);
          scope.service_selected.splice( index, 1 );

          if( scope.service_selected.length != 0){
            scope.service_length.pop( );
          }
          
        };

        scope.isServiceFocused = function(num) {
          $(".services-list-container").show();
        };
        
        scope.isListinArray = function(data) {
          var index = $.inArray(data, scope.service_selected);
          if (index > -1) {
            return true;
          } else {
            return false;
          }
        };

        scope.serviceSelected = function(data) {
          var index = $.inArray(data, scope.service_selected);
          if( index < 0 ){
            data.selected = true;
            scope.service_selected.push(data);
            scope.temp_list[0].procedure_ids.push(data);

            if( scope.service_selected.length > 1 ){
              scope.service_length.push( scope.service_length.length);
            }
          }

          $(".services-list-container").hide();
        };

        // console.log(scope.options);
        scope.your_transaction = {};
        scope.other_transaction = [];

        scope.checkClaim = function(){
          $( '#check-claim-modal' ).modal( 'show' );
          $('.isNotDoneChecking').show();
          $('.isDoneChecking').hide();
          $http.post(base_url + "check_duplicate_transaction", {
            user_id: scope.temp_list[0].id,
            date_transaction : moment($('#visitDateInput').val()).format('YYYY-MM-DD'),
            amount : scope.temp_list[0].amount,
          })
          .then(function(response) {
            // console.log(response);
            if( response.data.status == true && response.data.error == 0){
              scope.your_transaction = response.data.new_transaction;
              scope.other_transaction = response.data.duplicates;
              
              $('.isNotDoneChecking').hide();
              $('.isDoneChecking').show();
            }else{
              $( '#check-claim-modal' ).modal( 'hide' );
              scope.add();
            }
          })
          .catch(function(err){
            $('#check-claim-modal').modal('hide');
          });
        }

        // console.log(scope.options);

        scope.add = function() {
          $('#check-claim-modal').modal('hide');
          var date = $("#visitDateInput").val();
          var time = $("#visitTimeInput").val();
          scope.temp_list[0].display_book_date = date;
          scope.temp_list[0].book_date = date;
          scope.temp_list[0].time = time;
          scope.temp_list[0].book = moment(new Date(date)).format("YYYY-MM-DD");
          var amount = null;
          if (scope.temp_list[0].amount) {
            var amount_temp = scope.temp_list[0].amount.split("S$");
            amount = amount_temp[amount_temp.length - 1];

            if (Number.isInteger(parseInt(amount)) == false) {
              amount = 0;
            }
          } else {
            amount = 0;
          }

          if (scope.temp_list[0].display_book_date == "") {
            sweetAlert("Ooops!", "Please fill in the details.", "error");
            return false;
          }
          if ( scope.temp_list[0].id == "" || scope.temp_list[0].id == undefined ) {
            sweetAlert("Ooops!","Please select Name from the NRIC drop down result.","error");
            return false;
          }

          if (scope.temp_list[0].procedure_ids.length == 0) {
            sweetAlert("Ooops!","Please select a procedure/service.","error");
            return false;
          }

          if (amount < 0) {
            sweetAlert("Ooops!", "Negative values are not allowed.", "error");
            return false;
          }
          // var temp_date = moment(scope.temp_list[0].book_date, "DD/MM/YYYY");
          // scope.temp_list[0].book_date = temp_date;
          var procedure_ids = [];
          if(!time) {
            sweetAlert("Ooops!", "Please enter time of transaction.", "error");
            return false;
          }

          if (amount < 0) {
            sweetAlert("Ooops!", "Negative values are not allowed.", "error");
            return false;
          }
          // var temp_date = moment(scope.temp_list[0].book_date, "DD/MM/YYYY");
          // scope.temp_list[0].book_date = temp_date;
          var procedure_ids = [];
          var procedures = [];

          if (scope.temp_list[0].procedure_ids.length == 1) {
            var multiple_procedures = false;
          } else {
            var multiple_procedures = true;
          }
          angular.forEach(scope.temp_list[0].procedure_ids, function(value,key) {
            procedure_ids.push(value.id);

            $http.get( base_url + "clinic/get/service/details/" + value.id )
              .then(function(response){
                console.log( response );
                procedures.push( response.data );
              });

            if (scope.temp_list[0].procedure_ids.length - 1 == key) {
              $http.get( base_url + "clinic/get/user/details/" + scope.temp_list[0].id )
                .then(function(response){
                  console.log(response);
                  scope.claim_list.push({
                    user_type: response.data[0].UserType,
                    access_type: response.data[0].access_type,
                    name: response.data[0].Name,
                    nric: scope.temp_list[0].nric,
                    procedure_ids: procedure_ids,
                    procedures: procedures,
                    multiple_procedures: multiple_procedures,
                    display_book_date: scope.temp_list[0].display_book_date + " " + scope.temp_list[0].time + " " + scope.vist_time_day,
                    book_date: scope.temp_list[0].book,
                    time: scope.temp_list[0].time + " " + scope.vist_time_day,
                    id: scope.temp_list[0].id,
                    amount: amount,
                    back_date: 1,
                    health_provider: 0,
                    transaction_date: scope.temp_list[0].book + " " + scope.temp_list[0].time + " " + scope.vist_time_day
                  });

                  localStorageService.set("trans_table_" + scope.clinic.ClinicID, scope.claim_list);

                  scope.temp_list[0].nric = "";
                  scope.temp_list[0].procedure = "";
                  scope.temp_list[0].id = "";
                  scope.temp_list[0].amount = "";
                  scope.temp_list[0].user_type = "";
                  scope.temp_list[0].access_type = "";
                  scope.temp_list[0].back_date = "";
                  scope.temp_list[0].health_provider = "";
                  scope.temp_list[0].automatic = "";
                  scope.temp_list[0].multiple_procedures = "";
                  scope.temp_list[0].procedure_ids = "";
                  scope.temp_list[0].procedure_ids = [];

                  scope.service_selected = [];
                  scope.service_length = [0];
                  scope.vist_time_day = "AM";
                  scope.selected_hour = 1;
                  scope.selected_minute = 0;

                  console.log( scope.claim_list );
                });
            }
          });
        };

        scope.remove = function(data) {
          var data = data;
          swal(
            {
              title: "Are you sure you want to delete this?",
              text: "",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
              if (isConfirm) {
                if (data.transaction_id) {
                  // delete transaction
                  $http
                    .post(base_url + "clinic/delete_transaction", {
                      transaction_id: data.transaction_id
                    })
                    .success(function(response) {
                      swal("Success!", response.message, "success");
                      scope.claim_list.splice(
                        scope.claim_list.indexOf(data),
                        1
                      );

                      localStorageService.set("trans_table_" + scope.clinic.ClinicID, scope.claim_list);
                    });
                } else {
                  scope.$apply(function() {
                    scope.claim_list.splice(
                      scope.claim_list.indexOf(data),
                      1
                    );

                    localStorageService.set("trans_table_" + scope.clinic.ClinicID, scope.claim_list);
                  });
                }
              }
            }
          );
        };

        scope.removeBackDate = function(data, key) {
          console.log(data);
          // var data = data;
          if (data.mednefits_credits != 0) {
            var text =
              "Are you sure you want to make a refund for this transaction?";
            var btn = "Refund";
            var btn_removing = "Processing...";
          } else {
            var text = "Are you sure you want to delete this?";
            var btn = "Remove";
            var btn_removing = "Removing...";
          }
          swal(
            {
              title: text,
              text: "",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
              $("#delete_btn_" + data.trans_id).attr("disabled", true);
              $("#delete_btn_" + data.trans_id).text(btn_removing);
              if (isConfirm) {
                $http
                  .get(
                    base_url +
                      "clinic/remove/transaction?id=" + data.trans_id
                  )
                  .success(function(response) {
                    if (!response.status) {
                      $("#delete_btn_" + data.trans_id).attr(
                        "disabled",
                        false
                      );
                      $("#delete_btn_" + data.trans_id).text(btn);
                      swal("Ooops!", response.message, "error");
                    } else {
                      $("#delete_btn_" + data.trans_id).attr(
                        "disabled",
                        false
                      );
                      $("#delete_btn_" + data.trans_id).text(btn);

                      if( scope.isFiltered == true ){
                        scope.filterByDate();
                      }else{
                        scope.getBackDateTransaction();
                      }
                    }
                    // scope.$apply(function(){
                    // scope.backdate_list.splice(scope.backdate_list.indexOf(data), 1);
                    // });
                  });
              } else {
                $("#delete_btn_" + data.trans_id).attr(
                  "disabled",
                  false
                );
                $("#delete_btn_" + data.trans_id).text(btn);
              }
            }
          );
        };

        scope.getServices = function() {
          $http
            .get(base_url + "clinic/get/services")
            .success(function(response) {
              scope.service_list = response;
              scope.services = response;
              // console.log(response);
            });
        };

        scope.conclude = function() {
          swal({
              title: "Are you sure?",
              text:
                "This " +
                scope.claim_list.length +
                " transaction data will be save.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes!",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
              if (isConfirm) {
                scope.$apply(function() {
                  $("#loader").show();
                  $("#btn-conclude").attr("disabled", true);
                  $(".btn-remove").attr("disabled", true);
                  $http
                    .post(base_url + "clinic/save/bulk/transaction",scope.claim_list)
                    .success(function(response) {
                      $localStorage.$reset();
                      $("#btn-conclude").attr("disabled", false);
                      $(".btn-remove").attr("disabled", false);
                      if( scope.isFiltered == true ){
                        scope.filterByDate();
                      }else{
                        scope.getBackDateTransaction();

                      }
                      sweetAlert("Success!","The Transaction Successfully Saved.","success");
                      scope.claim_list = [];
                      $("#loader").hide();
                    });
                });
              }
            }
          );
        };

        scope.toggleClaimSummaryModal = function( data, index ){
          console.log( data );
          scope.selected_submit_data = data;
          scope.selected_submit_data.index = index;
          $('#summary-claim-modal').modal('show');
        }

        scope.submitData = function(data, index) {
          data.currency_type = scope.clinic.currency_type;
          if (data.amount < 0) {
            swal("Ooops!", "Amount should not be negative.", "error");
            return false;
          }

          data.currency_amount = scope.conversion.current_myr;
          // console.log(data);
          // return false;
          swal({
              title: "Are you sure?",
              text: "This transaction data will be save.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes!",
              cancelButtonText: "Cancel",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
              if (isConfirm) {
                scope.$apply(function() {
                  $("#submit_btn_" + index).attr("disabled", true);
                  $("#loader_" + index).show();
                  if(data.currency_type == 'myr') {
                    data.amount = data.amount / data.currency_amount;
                  }
                  $http.post(base_url + "clinic/save/claim/transaction", data)
                    .success(function(response) {

                      if(!response.status) {
                        sweetAlert("Oooops!", response.message, "error");
                      } else {
                        $('#summary-claim-modal').modal('hide');
                        $localStorage.$reset();
                        // $('#submit_btn_' + index).attr('disabled', true);
                        // $('#loader_' + index).show();
                        if( scope.isFiltered == true ){
                          scope.filterByDate();
                        }else{
                          scope.getBackDateTransaction();
                        }
                        sweetAlert("Success!","The Transaction Successfully Saved.","success");
                        scope.claim_list.splice(scope.claim_list.indexOf(data),1);
                        localStorageService.set("trans_table_" + scope.clinic.ClinicID, scope.claim_list);
                      }
                      $("#submit_btn_" + index).attr("disabled", false);
                      $("#loader_" + index).hide();

                    });
                });
              }
            }
          );
        };

        scope.backdate_list = {};
        scope.getBackDateTransaction = function() {
          scope.toggleBackLoading();
          $http.get(base_url + "clinic/all_transactions")
            .success(function(response) {
              scope.backdate_list = response;
              scope.toggleBackLoading();
            });
        };

        scope.searchUserByNRIC = function(search) {
          if (search) {
            scope.searchingUser = true;
            $http.get(base_url + "clinic/search_all_users?q=" + search)
              .success(function(response) {
                scope.searchingUser = false;
                scope.searchUserByNRIC_list = response;
              });
          }
        };

        scope.refreshModal = function() {
          scope.searchUserByNRIC_list = null;
          scope.searchUser = null;
        };

        scope.getHeathProvider = function() {
          $http.get(base_url + "clinic/get/health_provider/transaction")
            .success(function(response) {
              angular.forEach(response, function(value, key) {
                console.log( value );
                var procedures = [];
                angular.forEach( value.procedure_ids, function(value2, key){ 
                  $http.get( base_url + "clinic/get/service/details/" + value2 )
                    .then(function(response){
                      procedures.push( response.data );
                    });
                });
                $http.get( base_url + "clinic/get/user/details/" + value.user_id )
                  .then(function(response){
                    var data = {
                      name: response.data[0].Name,
                      nric: value.nric,
                      procedures: procedures,
                      procedure: value.procedure,
                      display_book_date: value.date,
                      book_date: moment(value.date).format("YYYY-MM-DD"),
                      id: value.user_id,
                      amount: value.amount,
                      user_type: value.user_type,
                      access_type: value.access_type,
                      back_date: 0,
                      health_provider: 1,
                      multiple_procedures: value.multiple_procedures,
                      procedure_ids: value.procedure_ids,
                      transaction_id: value.transaction_id
                    };

                    var check_exist = 0;

                    if( scope.claim_list.length > 0 ){
                      angular.forEach(scope.claim_list, function(value2, key2) {
                        if( value2.transaction_id == data.transaction_id ){
                          check_exist = 1;
                        }
                        if( key2 == (scope.claim_list.length-1) && check_exist == 0 ){
                          scope.claim_list.push(data);
                        }
                      });
                    }else{
                      scope.claim_list.push(data);
                    }

                    if (response.length - 1 === key) {
                      scope.load_status = true;
                    }
                  });
                
              });
            });
        };

        scope.row_edit_selected = {};
        scope.isEditOpen = false;

        scope.selectRowToUpdate = function( data ){
          scope.isEditOpen = true;
          scope.row_edit_selected = data;
          scope.row_edit_selected.visit_date = moment(scope.row_edit_selected.date_of_transaction).format('DD MMMM YYYY');
          scope.row_edit_selected.visit_time = moment(scope.row_edit_selected.date_of_transaction).format('hh:mm');
          scope.row_edit_selected.visit_period = moment(scope.row_edit_selected.date_of_transaction).format('a');
          scope.row_edit_selected.visit = {
            selected_hour : parseInt(moment(scope.row_edit_selected.date_of_transaction).format('hh')),
            selected_minute : parseInt(moment(scope.row_edit_selected.date_of_transaction).format('mm')),
          };
          
          scope.row_edit_selected.date_claim = moment(scope.row_edit_selected.claim_date).format('DD MMMM YYYY');
          scope.row_edit_selected.time_claim = moment(scope.row_edit_selected.claim_date).format('hh:mm');
          scope.row_edit_selected.period_claim = moment(scope.row_edit_selected.claim_date).format('a');
          scope.row_edit_selected.claim = {
            selected_hour : parseInt(moment(scope.row_edit_selected.claim_date).format('hh')),
            selected_minute : parseInt(moment(scope.row_edit_selected.claim_date).format('mm')),
          };

          $("#edit-dates-modal").modal('show');
        }

        scope.updateTransactionDates = function() {
          var visit_date = $("#edit-visit-datepicker").val();
          var claim_date = $("#edit-claim-datepicker").val();

          var data = {
            transaction_id : scope.row_edit_selected.transaction_id,
            date_of_transaction : visit_date + ", " + scope.row_edit_selected.visit_time + " " + scope.row_edit_selected.visit_period,
            claim_date : claim_date + ", " + scope.row_edit_selected.time_claim + " " + scope.row_edit_selected.period_claim,
          };
          
          $http.post(base_url + "clinic/update_transaction", data)
            .then(function(response){
              if( response.data.status == true ){
                scope.update_trans_err_msg = false;
                scope.update_trans_succ_msg = response.data.message;
                if( scope.isFiltered == true ){
                  scope.filterByDate();
                }else{
                  scope.getBackDateTransaction();
                }
                setTimeout(function() {
                  $("#edit-dates-modal").modal('hide');
                }, 2000);
              }else{
                scope.update_trans_err_msg = response.data.message;
                scope.update_trans_succ_msg = false;
              }
            });
        }

        $('#edit-dates-modal').on('hidden.bs.modal', function (e) {
          scope.isEditOpen = false;
          scope.row_edit_selected = {};
          scope.update_trans_err_msg = false;
          scope.update_trans_succ_msg = false;
        })

        setTimeout(function() {
          visit_date_dp = $("#visitDateInput").datetimepicker({
            format: "DD MMMM, YYYY",
            maxDate: new Date()
          });

          $("#edit-visit-datepicker").datetimepicker({
            format: "DD MMMM YYYY",
            maxDate: new Date()
          });

          $("#edit-claim-datepicker").datetimepicker({
            format: "DD MMMM YYYY",
            maxDate: new Date()
          });
        }, 100);

        scope.checkDataClaimLists = function(id) {
          var status = false;
          if (_.find(scope.claim_list, { transaction_id: id })) {
            var status = true;
          }

          return status;
        };

        scope.placeholder = null;
        // get clinic details
        scope.getClinicDetails = function() {
          $http.get(base_url + "clinic/details").success(function(response) {
            scope.clinic = response.clinic;

            if(scope.clinic.currency_type == "myr") {
              scope.placeholder = "Enter Amount in MYR";
            } else {
              scope.placeholder = "Enter Amount in SGD";
            }

            var stored_list = localStorageService.get("trans_table_" + scope.clinic.ClinicID);
            if ( stored_list != null ) {
              angular.forEach(stored_list, function(value, key) {
                scope.claim_list.push( value );
              })
            }
          });
        };

        scope.$watch("search", function() {
          if (scope.search.length == 0) {
            scope.search_btn_status = true;
            if( scope.isFiltered == true ){
              scope.filterByDate();
            }else{
              scope.getBackDateTransaction();
            }
          } else {
            scope.search_btn_status = false;
          }
        });

        scope.existSearch = function() {
          scope.search = "";
          scope.search_btn_status = true;
          if( scope.isFiltered == true ){
            scope.filterByDate();
          }else{
            scope.getBackDateTransaction();
          }
        };
        // search by nric
        scope.search_text = "";
        scope.search_status = true;
        scope.searchByNric = function() {
          scope.toggleBackLoading();
          scope.search_btn_status = false;
          if (scope.search.length > 5) {
            $http.post(base_url + "clinic/search_by_nric_transactions", {
                nric: scope.search,
                start_date: scope.transFilterData.start_date,
                end_date: scope.transFilterData.end_date,
              })
              .success(function(response) {
                scope.backdate_list = response;
                scope.toggleBackLoading();
              });
          }
        };

        // get pusher config
        // scope.getPusherConfig = function(connection) {
        //   console.log('connection', connection);
        //   // socket.on(connection, function (data) {
        //   //   console.log(data);
        //   // });
        //   $http.get(base_url + "pusher/config").success(function(response) {
        //     console.log(response);
        //     scope.notification.pusher = new Pusher(response.key, {
        //       cluster: "ap1"
        //     });
        //     scope.notification.channel = scope.notification.pusher.subscribe(
        //       response.channel
        //     );
        //     scope.notification.channel.bind(
        //       connection,
        //       function(data) {
        //         console.log(data);
        //         if (parseInt(data.ClinicID) == parseInt(scope.clinic.ClinicID)) {
        //           // check if transaction is already push to the array of claim_list

        //           if (!scope.checkDataClaimLists(data.id)) {
        //             $http.get(base_url + "clinic/transaction_specific/" + data.id)
        //               .success(function(response) {
        //                 setTimeout(function() {
        //                   scope.load_status = true;
        //                 }, 100);

        //                 var procedures = [];
        //                 angular.forEach( response.procedure_ids, function(value, key){ 
        //                   $http.get( base_url + "clinic/get/service/details/" + value )
        //                     .then(function(response){
        //                       procedures.push( response.data );
        //                     });
        //                 });

        //                 var data = {
        //                   nric: response.NRIC,
        //                   procedure: response.ProcedureID,
        //                   procedures: procedures,
        //                   display_book_date: response.date_of_transaction,
        //                   book_date: moment(
        //                     response.date_of_transaction
        //                   ).format("YYYY-MM-DD"),
        //                   id: response.UserID,
        //                   amount: response.procedure_cost,
        //                   back_date: 0,
        //                   health_provider: response.health_provider,
        //                   multiple_procedures: response.multiple_procedures,
        //                   procedure_ids: response.procedure_ids,
        //                   transaction_id: response.transaction_id
        //                 };

        //                 $http.get( base_url + "clinic/get/user/details/" + response.UserID )
        //                   .then(function(response2){
        //                     data.name = response2.data[0].Name;
        //                     data.user_type = response2.data[0].UserType;
        //                     data.access_type = response2.data[0].access_type;
        //                     scope.claim_list.push(data);
        //                   });
                        
        //               });
        //           }
        //         }
        //       }
        //     );
        //   });
        // };
        scope.getPusherConfig = function(connection) {
          console.log('connection', connection);
          socket.on(connection, function (data) {
            console.log(data);
            if (parseInt(data.clinic_id) == parseInt(scope.clinic.ClinicID)) {
                  // check if transaction is already push to the array of claim_list
                if (!scope.checkDataClaimLists(data.transaction_id)) {
                  $http.get(base_url + "clinic/transaction_specific?transaction_id=" + data.transaction_id)
                    .success(function(response) {
                      setTimeout(function() {
                        scope.load_status = true;
                      }, 100);

                      var procedures = [];
                      angular.forEach( response.procedure_ids, function(value, key){ 
                        $http.get( base_url + "clinic/get/service/details/" + value )
                          .then(function(response){
                            procedures.push( response.data );
                          });
                      });

                      var data = {
                        nric: response.NRIC,
                        procedure: response.ProcedureID,
                        procedures: procedures,
                        display_book_date: response.date_of_transaction,
                        book_date: moment(
                          response.date_of_transaction
                        ).format("YYYY-MM-DD"),
                        id: response.UserID,
                        amount: response.procedure_cost,
                        back_date: 0,
                        health_provider: response.health_provider,
                        multiple_procedures: response.multiple_procedures,
                        procedure_ids: response.procedure_ids,
                        transaction_id: response.transaction_id
                      };

                      $http.get( base_url + "clinic/get/user/details/" + response.UserID )
                        .then(function(response2){
                          data.name = response2.data[0].Name;
                          data.user_type = response2.data[0].UserType;
                          data.access_type = response2.data[0].access_type;
                          scope.claim_list.push(data);
                        });
                      
                    });
                }
              }
          });
        };

        scope.getClinicSocketConnection = function( ) {
            $http.get(base_url + 'clinic_socket_connection')
            .then(function(response){
              if(response.data.status) {
                scope.getPusherConfig(response.data.socket_connection);
              }
            })
            .catch(function(error){

            });
        }

        // scope.getMyrValue();
        scope.getClinicDetails();
        scope.getClinicSocketConnection();
        // scope.getPusherConfig();
        scope.getHeathProvider();
        scope.getBackDateTransaction();
        scope.getServices();
        
      }
    };
  }
])


// .directive("procedureDirective", [
//   "$http",
//   function($http) {
//     return {
//       restrict: "AE",

//       template: "{{data}}",
//       scope: {
//         id: "@procedure"
//       },
//       link: function link(scope, element, attrs) {
//         scope.data = "";
//         var json = JSON.parse(attrs.procedure);
//         // if(json.multiple_procedures == true || json.multiple_procedures == "true") {
//         // get multiple services
//         angular.forEach(json.procedure_ids, function(value, key) {
//           $http({
//             method: "GET",
//             url: base_url + "clinic/get/service/details/" + value
//           }).then(
//             function(result) {
//               scope.data += result.data + ", ";
//             },
//             function(result) {
//               console.log("Error: No data returned");
//             }
//           );
//         });
//         // } else {
//         // $http({
//         //   method: 'GET',
//         //   url: base_url + 'clinic/get/service/details/' + json.
//         // }).then(function(result) {
//         //     scope.data = result.data;
//         //   console.log(result);
//         // }, function(result) {
//         //   console.log("Error: No data returned");
//         // });
//         // }
//       }
//     };
//   }
// ])

// .directive("userDirective", [
//   "$http",
//   function($http) {
//     return {
//       restrict: "AE",

//       template:
//         '{{data.Name}} - <span ng-if="data.UserType == 1">Public User</span> <span ng-if="data.UserType == 5 && data.access_type == 1">Invidual User</span><span ng-if="data.UserType == 5 && data.access_type == 0">Corporate User</span>',
//       scope: {
//         id: "@user"
//       },
//       link: function link(scope, element, attrs) {
//         scope.data;
//         $http({
//           method: "GET",
//           url: base_url + "clinic/get/user/details/" + attrs.user
//         }).then(
//           function(result) {
//             scope.data = result.data[0];
//           },
//           function(result) {
//             console.log("Error: No data returned");
//             window.location.reloa();
//           }
//         );
//       }
//     };
//   }
// ])


