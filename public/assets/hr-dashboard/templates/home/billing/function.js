app.directive('spendingBillingDirective', [
	'$state',
  '$location',
  'hrSettings',
  'serverUrl',
	function directive($state,$location,hrSettings,serverUrl) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				console.log("billing directive Runnning !");
				console.log($location);

        scope.invoiceSelectorValue = 'spending';
        scope.page_active = 1;
        scope.per_page = 5;
        scope.billing_pagination_dropdown = false;
        scope.applyTerm = false
        // scope.showLastTermSelector = false;
        

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

            // scope.getBenefitsCoverageData(scope.defaultDateTerms);

            
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
            scope.applyTerm = true;
          } else if (src == 'applyBtn') {
            // let termData = _.filter(scope.dateTerms, index => index.index == scope.dateTermIndex);  //{ 'index': scope.dateTermIndex }
            console.log(data);
            scope.getBillingInvoiceHistory();
          }
          console.log(scope.selectedTerm)
        }

        scope.getBillingInvoiceHistory = function ( type,download ) {
          // console.log( type );

          scope.showLoading();
          hrSettings.fetchCompanyInvoiceHistory( type,download )
            .then(function(response){
              console.log(response);

              scope.billingData = response.data.data;
              scope.billingPagination = response.data;
              scope.totalOutstanding = parseFloat(response.data.total_due).toFixed(2);
							scope.hideLoading();
            })
				}

        scope.invoiceSelector = function ( type ) {
          console.log( type );
          scope.invoiceSelectorValue = type;

          scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
        }

        scope.downloadAction = function ( data,index ) {
 
          scope.billingData.map((value,key)  => {
            
            if ( index == key ) {
              value.isActionShow = value.isActionShow == true ? false : true;
            } 
          })
        } 
        
        scope.invoiceAccordion = function ( data,index ) {
          console.log(index);

          scope.billingData.map((value,key)  => {
            if ( index == key ) {
              value.isAccordionShow = value.isAccordionShow == true ? false : true;
            } 
          })
        }

        scope.range = function (num) {
          var arr = [];
          for (var i = 0; i < num; i++) {
            arr.push(i);
          }
          return arr;
        };

        $("body").click(function(e){
          if ($(e.target).parents(".download-dot-container").length === 0) {
            scope.hideDownloadDrops();
          }
        });

        scope.hideDownloadDrops  = function(){
          angular.forEach(scope.billingData, function(value, key){
            // console.log(value);
            if( value.isActionShow == true ){
              value.isActionShow = false;
              scope.$apply();
            }
          });
        }

        scope.downloadViewInvoice = function ( id ) {
          window.open(serverUrl.url + '/benefits/invoice/' + id );
        }
        scope.downloadViewTransactions = function ( id ) {
          window.open(serverUrl.url + '/benefits/invoice/' + id );
        }

        scope._selectNumList_ = function ( num ) {
          scope.per_page = num;
          scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
        }

        scope._prevPageList_ = function () {
          scope.page_active -= 1;
          scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
        }

        scope._nextPageList_ = function () {
          scope.page_active += 1;
          scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
        }

        scope._setPageLimit_ = function ( num ) {
          scope.per_page = num;
          scope.page_active = 1;
          scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
        }

        scope._toggleOpenPerPage_ = function () {
          scope.billing_pagination_dropdown = !scope.billing_pagination_dropdown;
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

        scope.downloadSoa = function ( status ) {
          scope.downloadStatus = status;
          console.log(scope.invoiceSelectorValue);
          
          hrSettings.downloadSoaData( scope.invoiceSelectorValue,scope.downloadStatus )
            .then(function(response){
              console.log(response);

            })

        }
       
        scope.onLoad = async function () {
          // scope.showLoading();
          await scope.getDateTerms();
          await scope.getBillingInvoiceHistory( scope.invoiceSelectorValue );
        }

        scope.onLoad();
				
			}
		}
	}
]);
