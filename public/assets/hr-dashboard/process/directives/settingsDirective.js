app.directive('settingsDirective', [
	'$state',
	'hrSettings',
	'dashboardFactory',
	function directive($state,hrSettings,dashboardFactory) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("settingsDirective Runnning !");
				scope.clinic_blocked_search_trap = false;
				scope.clinic_opened_search_trap = false;
				scope.settings_active = 0;
        scope.statementHide = true;
        scope.empStatementShow = false;
        scope.arrowStatement = false;

        scope.companyAccountType = function () {
          scope.account_type = localStorage.getItem('company_account_type');
          console.log(scope.account_type);

          if(scope.account_type === 'enterprise_plan') {
            $('.statement-hide').hide();
            scope.statementHide = false;
            scope.empStatementShow = true;
            scope.arrowStatement = true;
          }
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
        scope.selectSettingsTab = function ( opt ) {
        	scope.settings_active = opt;
        }
        scope.onLoad = function( ){
        		        
        }

        scope.onLoad();
			}
		}
	}
]);
