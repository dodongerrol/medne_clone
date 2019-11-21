app.directive('blockHealthPartnersDirective', [
	'$state',
  '$http',
	'$timeout',
	'serverUrl',
  'hrActivity',
	function directive($http,$state,$timeout,serverUrl,hrActivity) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("blockHealthPartnersDirective Runnning !");
				scope.clinic_blocked_search_trap = false;
				scope.clinic_opened_search_trap = false;
				scope.settings_active = 1;
        scope.isBlockSearch = false;
        scope.isOpenSearch = false;

        scope.transaction_ctr = 0;

        scope.search = {
          clinic_open_search_text : '',
          clinic_blocked_search_text : '',
        }

        scope.per_page_arr = [10,20,30,40,50,100];

        

        //-- blocked --//
          scope.clinic_block_selected = [];
          scope.clinic_id_block_selected = [];
          scope.clinic_type_block_selected = [];
          scope.clinic_type_id_block_selected = [];

          scope.clinic_block_arr = [];
          scope.clinic_type_block_arr = [];
          scope.block_pagination = {};
          scope.block_page_active = 1;
          scope.block_per_page = 10;
          scope.filter_regionBlocked = 'all_region';
          scope.allBlockSelected = false;
          scope.list_opt_block = 'name';
        //-------------//

        //-- opened --//
          scope.clinic_open_selected = [];
          scope.clinic_id_open_selected = [];
          scope.clinic_type_open_selected = [];
          scope.clinic_type_id_open_selected = [];

          scope.clinic_open_arr = [];
          scope.clinic_type_open_arr = [];
          scope.open_pagination = {};
          scope.open_page_active = 1;
          scope.open_per_page = 10;
          scope.filter_regionOpened = 'all_region';
          scope.allOpenSelected = false;
          scope.list_opt_open = 'name';
        //-------------//


        scope.range = function (range) {
          var arr = []; 
          for (var i = 0; i < range; i++) {
            arr.push(i+1);
          }
          return arr;
        }
        scope.hideDropDowns = function() {
          $('.blocked-page-scroll').hide();
          $('.blocked-per-page-scroll').hide();
          $('.opened-page-scroll').hide();
          $('.opened-per-page-scroll').hide();
        }
        scope.searchClinics = function( search, opt ) {
          console.log( scope.search );
          if( search != "" ){
            if( opt == 'block' ){
              scope.block_page = 1;
              scope.block_per_page = 10;
              scope.getBlockedClinics();
            }
            if( opt == 'open' ){
              scope.open_page = 1;
              scope.open_per_page = 10;
              scope.getOpenedClinics();
            }
          }
        }
        scope.changeFilterType = function( filter, type ){
          scope.clinic_id_block_selected = [];
          scope.clinic_block_selected = [];
          scope.clinic_type_id_block_selected = [];
          scope.clinic_type_block_selected = [];
          if( type == 'open' ){
            scope.allOpenSelected = false;
            angular.forEach( scope.clinic_type_open_arr, function( value, key ){
              value.selected = false;
            });
            angular.forEach( scope.clinic_open_arr, function( value, key ){
              value.selected = false;
            });
          }else{
            scope.allBlockSelected = false;
            angular.forEach( scope.clinic_type_block_arr, function( value, key ){
              value.selected = false;
            });
            angular.forEach( scope.clinic_block_arr, function( value, key ){
              value.selected = false;
            });
          }
        }
        scope.regionOpt = function( opt, source ){
          if (source == 'open') {
            scope.filter_regionOpened = opt;
            if(opt == 'all_region') {
              scope.filterByRegionOpened = undefined;
            } else if(opt == 'sgd') {
              scope.filterByRegionOpened = 'Singapore';
            } else if (opt == 'myr') {
              scope.filterByRegionOpened = 'Malaysia';
            }
          } else if (source == 'blocked') {
            scope.filter_regionBlocked = opt;
            if(opt == 'all_region') {
              scope.filterByRegionBlocked = undefined;
            } else if(opt == 'sgd') {
              scope.filterByRegionBlocked = 'Singapore';
            } else if (opt == 'myr') {
              scope.filterByRegionBlocked = 'Malaysia';
            }
          }
          console.log('opt',opt);
          scope.onLoad();
        }
				scope.showPageScroll = function ( data ) {
          let x = data;
          if (x === 'blocked_page') {
            $('.blocked-page-scroll').show();
          } 
          if (x === 'blocked_per_page') {
            $('.blocked-per-page-scroll').show();
          } 
          if (x === 'opened-page-scroll') {
            $('.opened-page-scroll').show();
          } 
          if (x === 'opened-per-page-scroll') {
            $('.opened-per-page-scroll').show();
          } 

          $("body").click(function(e){ 
            if ($(e.target).parents(".page-blocked").length === 0) {
              $(".blocked-page-scroll").hide();
            }
            if ($(e.target).parents(".rows-per-page-blocked").length === 0) {
              $(".blocked-per-page-scroll").hide();
            }
            if ($(e.target).parents(".page-opened").length === 0) {
              $(".opened-page-scroll").hide();
            }
            if ($(e.target).parents(".rows-per-page-opened").length === 0) {
              $(".opened-per-page-scroll").hide();
            }
          });
        }
        scope.toggleBlockedClinicSearch = function () {
          if (scope.clinic_blocked_search_trap == false) {
            scope.clinic_blocked_search_trap = true;
          } else {
            scope.clinic_blocked_search_trap = false;
          }
        }
        scope.toggleOpenedClinicSearch = function () {
          if (scope.clinic_opened_search_trap == false) {
            scope.clinic_opened_search_trap = true;
          } else {
            scope.clinic_opened_search_trap = false;
          }
        }



        // -- PAGINATION FUNCTIONS -- //
          scope.nextPageBlock = function(){
            if( scope.block_page_active != scope.block_pagination.last_page ){
              scope.block_page_active++;
              scope.onLoad();
            }
          }
          scope.backPageBlock = function(){
            if( scope.block_page_active != 1 ){
              scope.block_page_active--;
              scope.onLoad();
            }
          }
          scope.perPageBlock = function(page){
            scope.hideDropDowns();
            scope.block_per_page = page;
            scope.block_page_active = 1;
            scope.onLoad();
          }
          scope.pageBlock = function(page){
            scope.hideDropDowns();
            scope.block_page_active = page;
            scope.onLoad();
          }


          scope.nextPageOpen = function(){
            if( scope.open_page_active != scope.open_pagination.last_page ){
              scope.open_page_active++;
              scope.onLoad();
            }
          }
          scope.backPageOpen = function(){
            if( scope.open_page_active != 1 ){
              scope.open_page_active--;
              scope.onLoad();
            }
          }
          scope.perPageOpen = function(page){
            scope.hideDropDowns();
            scope.open_per_page = page;
            scope.open_page_active = 1;
            scope.onLoad();
          }
          scope.pageOpen = function(page){
            scope.hideDropDowns();
            scope.open_page_active = page;
            scope.onLoad();
          }
        // --------------------------- // 




        // ----- OPEN CLINIC FUNCTIONS ----- //

        // --------------------------------- //

        // ----- BLOCK CLINIC FUNCTIONS ----- //
          scope.addBlockClinicToArr = function( data, opt ) {
            // scope.clinic_id_block_selected = [];
            // scope.clinic_block_selected = [];
            // scope.clinic_type_id_block_selected = [];
            // scope.clinic_type_block_selected = [];
            if( opt == 'name' ){
              var index = $.inArray( data.ClinicID, scope.clinic_id_block_selected );
              if( index < 0 ){
                scope.clinic_id_block_selected.push( data.ClinicID );
                scope.clinic_block_selected.push( data );
              }else{
                scope.clinic_id_block_selected.splice( index, 1 );
                scope.clinic_block_selected.splice( index, 1 );
              }
            }
            if( opt == 'type' ){
              var index = $.inArray( data.ClinicTypeID, scope.clinic_type_id_block_selected );
              if( index < 0 ){
                scope.clinic_type_id_block_selected.push( data.ClinicTypeID );
                scope.clinic_type_block_selected.push( data );
              }else{
                scope.clinic_type_id_block_selected.splice( index, 1 );
                scope.clinic_type_block_selected.splice( index, 1 );
              }
            }

            console.log( scope.clinic_id_block_selected );
            console.log( scope.clinic_block_selected );
            console.log( scope.clinic_type_id_block_selected );
            console.log( scope.clinic_type_block_selected );
          }
        // --------------------------------- //



        // --------- HTTP REQUESTS ---------- //
          scope.saveClinics = function() {
            console.log( "open clinics", scope.clinic_id_open_selected );
            console.log( "open clinic types", scope.clinic_type_id_open_selected );

            console.log( "block clinics", scope.clinic_id_block_selected );
            console.log( "block clinic types", scope.clinic_type_id_block_selected );

            // scope.showLoading();
            // var data = {
            //   access_status: opt,
            //   clinic_id: scope.clinic_id_selected,
            //   clinic_type_id: scope.clinic_type_id_selected,
            //   region: region,
            //   status: status,
            //   type: type,
            // }
            // hrActivity.OpenBlockClinics( data ) 
            //   .then(function(response) { 
            //     console.log(response);
            //     scope.hideLoading();
            //   });
          }

          scope.updateClinics = function( status, clinic_opt ) {
            console.log( "open clinics", scope.clinic_open_selected );
            console.log( "open clinic types", scope.clinic_type_open_selected );
            console.log( "block clinics", scope.clinic_block_selected );
            console.log( "block clinic types", scope.clinic_type_block_selected );
            if( scope.clinic_open_selected.length > 0 || scope.clinic_type_open_selected.length > 0 || scope.clinic_block_selected.length > 0 || scope.clinic_type_block_selected.length > 0 ){
              scope.transaction_ctr += 1;
            }
            scope.showLoading();
            if( clinic_opt == 'name' ){
              if( status == 'block-to-open' ){
                angular.forEach( scope.clinic_block_selected, function( value, key ){
                  scope.clinic_open_arr.push( value );
                  if( scope.clinic_block_selected.length - 1 == key ){
                    scope.clinic_open_arr.sort(function (a, b) {
                      return b.ClinicID - a.ClinicID;
                    });

                    console.log( scope.clinic_open_arr );
                  }
                });
              }
              if( status == 'open-to-block' ){
                angular.forEach( scope.clinic_open_selected, function( value, key ){
                  scope.clinic_block_arr.push( value );
                  if( scope.clinic_open_selected.length - 1 == key ){
                    scope.clinic_block_arr.sort(function (a, b) {
                      return a.ClinicID - b.ClinicID;
                    });
                  }
                });
              }
            }
            if( clinic_opt == 'type' ){
              if( status == 'block-to-open' ){
                angular.forEach( scope.clinic_type_block_selected, function( value, key ){
                  scope.clinic_type_open_arr.push( value );
                  if( scope.clinic_type_block_selected.length - 1 == key ){
                    scope.clinic_type_open_arr.sort(function (a, b) {
                      return a.company_block_clinic_access_id - b.company_block_clinic_access_id;
                    });
                  }
                });
              }
              if( status == 'open-to-block' ){
                angular.forEach( scope.clinic_type_open_selected, function( value, key ){
                  scope.clinic_type_block_arr.push( value );
                  if( scope.clinic_type_open_selected.length - 1 == key ){
                    scope.clinic_type_block_arr.sort(function (a, b) {
                      return a.company_block_clinic_access_id - b.company_block_clinic_access_id;
                    });
                  }
                });
              }
            }
            scope.hideLoading();
          }

          scope.getClinicTypes = function() {
            hrActivity.fetchClinicTypes( 'open', scope.filter_regionOpened ) 
              .then(function(response) { 
                // console.log(response);
                scope.clinic_type_open_arr = response.data;
              });
            hrActivity.fetchClinicTypes( 'block', scope.filter_regionBlocked ) 
              .then(function(response) { 
                // console.log(response);
                scope.clinic_type_block_arr = response.data;
              });
          }
          scope.getBlockedClinics = function() {
            scope.showLoading();
            hrActivity.fetchBlockedClinics( scope.block_per_page, scope.block_page_active, scope.filter_regionBlocked, scope.search.clinic_blocked_search_text ) 
              .then(function(response) {
                // console.log(response);
                if( scope.search.clinic_blocked_search_text == null || scope.search.clinic_blocked_search_text == '' ){
                  scope.clinic_block_arr = response.data.data;
                  scope.block_pagination = response.data;
                  scope.isBlockSearch = false;
                }else{
                  scope.clinic_block_arr = response.data;
                  scope.isBlockSearch = true;
                }
                scope.getOpenedClinics();
              });
          }
          scope.getOpenedClinics = function() {
            hrActivity.fetchOpenedClinics( scope.open_per_page, scope.open_page_active, scope.filter_regionOpened, scope.search.clinic_open_search_text ) 
              .then(function(response) {
                // console.log(response);
                if( scope.search.clinic_open_search_text == null || scope.search.clinic_open_search_text == '' ){
                  scope.clinic_open_arr = response.data.data;
                  scope.open_pagination = response.data;
                  scope.isOpenSearch = false;
                }else{
                  scope.clinic_open_arr = response.data;
                  scope.isOpenSearch = true;
                }
                scope.hideLoading();
              });
          }

        // ---------------------------------- //

        scope.showLoading = function( ){
          $( ".circle-loader" ).fadeIn(); 
        }

        scope.hideLoading = function( ){
          setTimeout(function() {
            $( ".circle-loader" ).fadeOut();
          },1000)
        }
       
        scope.onLoad = function( ){
          scope.getClinicTypes();
        	scope.getBlockedClinics();
        }

        scope.onLoad();
			}
		}
	}
]);
