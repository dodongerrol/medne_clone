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
          scope.clinic_type_block_ids = [];
          scope.clinic_block_arr = [];
          scope.clinic_type_block_arr = [];
          scope.block_pagination = {};
          scope.block_page_active = 1;
          scope.block_per_page = 10;
          scope.filter_regionBlocked = 'all_region';
          scope.allBlockSelected = false;
          scope.list_opt_block = 'type';
        //-------------//

        //-- opened --//
          scope.clinic_type_open_ids = [];
          scope.clinic_open_arr = [];
          scope.clinic_type_open_arr = [];
          scope.open_pagination = {};
          scope.open_page_active = 1;
          scope.open_per_page = 10;
          scope.filter_regionOpened = 'all_region';
          scope.allOpenSelected = false;
          scope.list_opt_open = 'type';
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
          }else{
            scope.clinic_blocked_search_trap = false;
            scope.clinic_opened_search_trap = false;
            scope.onLoad();
          }
        }
        scope.changeFilterType = function( type ){
          if( type == 'open' ){
            scope.resetOpenCheckBoxes();
          }else{
            scope.resetBlockCheckBoxes();
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
            scope.resetOpenCheckBoxes();
          } else if (source == 'blocked') {
            scope.filter_regionBlocked = opt;
            if(opt == 'all_region') {
              scope.filterByRegionBlocked = undefined;
            } else if(opt == 'sgd') {
              scope.filterByRegionBlocked = 'Singapore';
            } else if (opt == 'myr') {
              scope.filterByRegionBlocked = 'Malaysia';
            }
            scope.resetBlockCheckBoxes();
          }
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
            scope.onLoad();
          }
        }
        scope.toggleOpenedClinicSearch = function () {
          if (scope.clinic_opened_search_trap == false) {
            scope.clinic_opened_search_trap = true;
          } else {
            scope.clinic_opened_search_trap = false;
            scope.onLoad();
          }
        }
        scope.toggleAllBlockedClinic = function( ){
          var arr = scope.list_opt_block == 'name' ? scope.clinic_block_arr : scope.clinic_type_block_arr;
          if( scope.allBlockSelected == true ){
            angular.forEach( arr, function(value,key){
              value.selected = true;
            });
          }else{
            angular.forEach( arr, function(value,key){
              value.selected = false;
            });
          }
        }
        scope.toggleAllOpenedClinic = function(){
          var arr = scope.list_opt_open == 'name' ? scope.clinic_open_arr : scope.clinic_type_open_arr;
          if( scope.allOpenSelected == true ){
            scope.allOpenSelected = true;
            angular.forEach( arr, function(value,key){
              value.selected = true;
            });
          }else{
            scope.allOpenSelected = false;
            angular.forEach( arr, function(value,key){
              value.selected = false;
            });
          }
        }
        scope.resetOpenCheckBoxes = function(){
          scope.clinic_type_open_ids = [];
          scope.allOpenSelected = false;
          angular.forEach( scope.clinic_type_open_arr, function( value, key ){
            value.selected = false;
          });
          angular.forEach( scope.clinic_open_arr, function( value, key ){
            value.selected = false;
          });
        }
        scope.resetBlockCheckBoxes = function(){
          scope.clinic_type_block_ids = [];
          scope.allBlockSelected = false;
          angular.forEach( scope.clinic_type_block_arr, function( value, key ){
            value.selected = false;
          });
          angular.forEach( scope.clinic_block_arr, function( value, key ){
            value.selected = false;
          });
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
          scope.openToBlock = function( status, region, opt ) {
            if( opt == 'name' ){
              var ctr = 0;
              angular.forEach( scope.clinic_open_arr, function( value, key ){
                if( value.selected ){
                  ctr += 1;
                  scope.showLoading();
                  scope.updateClinics( value.ClinicID, status, region, opt );
                }
                if( ctr > 0 && scope.clinic_open_arr.length - 1 == key ){
                  scope.hideLoading();
                  scope.onLoad();
                  swal('Success!', 'Clinic Block Lists updated.', 'success');
                }else if( ctr == 0 && scope.clinic_open_arr.length - 1 == key ){
                  swal('Error!', 'Please Select a clinic first.', 'error');
                }
              });
              if( scope.clinic_open_arr.length == 0 ){
                swal('Error!', 'Please Select a clinic first.', 'error');
              }
            }
            if( opt == 'type' ){
              var ctr = 0;
              angular.forEach( scope.clinic_type_open_arr, function( value, key ){
                if( value.selected ){
                  ctr += 1;
                  scope.showLoading();
                  scope.clinic_type_block_ids.push( value.ClinicTypeID );
                }
                if( ctr > 0 && scope.clinic_type_open_arr.length - 1 == key ){
                  scope.updateClinics( scope.clinic_type_block_ids, status, region, opt );
                }else if( ctr == 0 && scope.clinic_type_open_arr.length - 1 == key ){
                  swal('Error!', 'Please Select a clinic type first.', 'error');
                }
              });
              if( scope.clinic_type_open_arr.length == 0 ){
                swal('Error!', 'Please Select a clinic type first.', 'error');
              }
            }
          }
        // --------------------------------- //

        // ----- BLOCK CLINIC FUNCTIONS ----- //
          scope.blockToOpen = function( status, region, opt ) {
            if( opt == 'name' ){
              var ctr = 0;
              let toOpenArr = [];
              angular.forEach( scope.clinic_block_arr, function( value, key ){
                if( value.selected ){
                  ctr += 1;
                  toOpenArr.push(value.ClinicID);
                // Comment if ever got issue w
                  console.log( value.selected );
                  scope.showLoading();
                  // scope.updateClinics( value.ClinicID, status, region, opt );
                }
                if( ctr > 0 && scope.clinic_block_arr.length - 1 == key ){
                  scope.updateClinics( toOpenArr, status, region, opt );
                  scope.hideLoading();
                  // scope.onLoad();
                  // swal('Success!', 'Clinic Block Lists updated.', 'success');
                }else if( ctr == 0 && scope.clinic_block_arr.length - 1 == key ){
                  swal('Error!', 'Please Select a clinic first.', 'error');
                }
              });
              if( scope.clinic_block_arr.length == 0 ){
                swal('Error!', 'Please Select a clinic first.', 'error');
              }
            }
            if( opt == 'type' ){
              var ctr = 0;
              angular.forEach( scope.clinic_type_block_arr, function( value, key ){
                if( value.selected ){
                  ctr += 1;
                  scope.showLoading();
                  scope.clinic_type_open_ids.push( value.ClinicTypeID );
                }
                if( ctr > 0 && scope.clinic_type_block_arr.length - 1 == key ){
                  scope.updateClinics( scope.clinic_type_open_ids, status, region, opt );
                }else if( ctr == 0 && scope.clinic_type_block_arr.length - 1 == key ){
                  swal('Error!', 'Please Select a clinic type first.', 'error');
                }
              });
              if( scope.clinic_type_block_arr.length == 0 ){
                swal('Error!', 'Please Select a clinic type first.', 'error');
              }
            }
          }
        // --------------------------------- //



        // --------- HTTP REQUESTS ---------- //
          scope.updateClinics = function( id, status, region, type ) {
            var data = {
              access_status: status == 0 ? 'open' : 'block',
              region: region,
              clinic_id: id,
              clinic_type_id: id,
              status: status,
              type: type == 'name' ? 'clinic_name' : 'clinic_type',
            }
            hrActivity.OpenBlockClinics( data ) 
              .then(function(response) { 
                console.log(response);
                if( response.data.status ){
                  if( type == 'type' ){
                    swal('Success!', response.data.message, 'success');
                    scope.onLoad();
                    scope.hideLoading();
                  }
                }
              });
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
          },100)
        }
       
        scope.onLoad = function( ){
          scope.search = {
            clinic_open_search_text : '',
            clinic_blocked_search_text : '',
          }
          scope.resetOpenCheckBoxes();
          scope.resetBlockCheckBoxes();
          scope.getClinicTypes();
        	scope.getBlockedClinics();
        }

        scope.onLoad();
			}
		}
	}
]);
