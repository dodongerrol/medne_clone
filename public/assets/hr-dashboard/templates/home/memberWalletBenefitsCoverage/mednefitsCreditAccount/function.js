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
        scope.defaultDateTerms = {};

        scope.termSelector = function () {
          scope.showLastTermSelector = scope.showLastTermSelector ? false : true;
          console.log(scope.showLastTermSelector);
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
            console.log(response);
            scope.dateTerm = response.data.data;
            // console.log(scope.dateTerm);

            // scope.currentTerm = scope.dateTerm.slice(-1).pop();
            // console.log(scope.currentTerm );

            let termLength = scope.dateTerm.length;
            // console.log(termLength);

            scope.dateTerm.map(function(value,index) {
              if (index == termLength-1) {
                value.term = `Current term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
                value.index = index;
                scope.defaultDateTerms = value;
                scope.selectedTerm = value;
                scope.dateTermIndex = value.index;
              } else {
                value.term = `Last term (${moment(value.start).format('DD/MM/YYYY')} - ${moment(value.end).format('DD/MM/YYYY')})`;
              }
            });

            scope.getMednefitsCreditAccount(scope.defaultDateTerms);
            scope.getMednefitsCreditActivities();
          })
        }

        scope.termSelection = async function (data,src) {
          // data is ang value kai ang index g select sa date terms
          // src if sa select ba or sa apply na button
          if( src == 'select') {
            // scope.dateTermIndex = parseInt(data);
            scope.termSelector();
            console.log(data);
            scope.selectedTerm = data;
          } else if (src == 'applyBtn') {
            // let termData = _.filter(scope.dateTerms, index => index.index == scope.dateTermIndex);  //{ 'index': scope.dateTermIndex }
            console.log(data);
            scope.getMednefitsCreditAccount(data);
          }
          console.log(scope.selectedTerm)
        }

        scope.getMednefitsCreditAccount = function (data) {
          scope.currentTermStartDate = moment(data.start).format('YYYY-MM-DD');
          scope.currentTermEndDate = moment( data.end ).format('YYYY-MM-DD');
          scope.showLoading();
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
              scope.mednefitsActivitiesData = response.data.data.data;
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
