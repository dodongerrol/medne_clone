app.directive('mednefitsCreditAccountDirective', [
	'$state',
  '$location',
  'hrSettings',
	function directive($state,$location,hrSettings) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("mednefits credit account directive Runnning !");
				console.log($location);

        scope.showLastTermSelector = false;
        

        scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
        }

        scope.formatDate = function (date) {
          return moment(new Date(date)).format("DD/MM/YYYY");
        };

        scope.formatTableDate = function (date) {
          return moment(new Date(date)).format("DD MMMM YYYY");
        };

        scope.getDateTerms = function () {
          hrSettings.fetchDateTerms()
          .then(function(response){
            scope.dateTerm = response.data.data;
            console.log(scope.dateTerm);

            scope.currentTerm = scope.dateTerm.slice(-1).pop();
            console.log(scope.currentTerm );

            scope.getMednefitsCreditAccount();
            scope.getMednefitsCreditActivities();
          })
        }

        scope.getMednefitsCreditAccount = function () {
          scope.currentTermStartDate = moment(scope.currentTerm.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment( scope.currentTerm.end ).format('YYYY-MM-DD');
          
          hrSettings.fetchMednefitsCreditsAccountData( scope.currentTermStartDate, scope.currentTermEndDate )
            .then(function(response){
              scope.mednefitsCreditsData = response.data.data;

              scope.hideLoading();
              console.log(scope.mednefitsCreditsData);
            })
        }

        scope.getMednefitsCreditActivities = function () {
          hrSettings.fetchMednefitsActivitiesData( scope.currentTermStartDate, scope.currentTermEndDate )
            .then(function(response){
              console.log(response);
              scope.mednefitsActivitiesData = response.data.data;
              // console.log(scope.mednefitsActivitiesData);
            })
        }


        scope.showLoading = function () {
          $(".circle-loader").fadeIn();
          loading_trap = true;
        };

        scope.hideLoading = function () {
          setTimeout(function () {
            $(".circle-loader").fadeOut();
            loading_trap = false;
          }, 10);
        };

       
        scope.onLoad = function () {
          scope.showLoading();
          scope.getDateTerms();
          scope.getMednefitsCreditActivities();
        }

        scope.onLoad();
				
			}
		}
	}
]);
