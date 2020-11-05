(function (angular) {
    'use strict';
    class CostCentreController {
        constructor(locationAPI, departmentAPI, costCentreAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/cost-centre';
            this.element = 'cost-centre-form';
            this.locationAPI = locationAPI;
            this.departmentAPI = departmentAPI;
            this.costCentreAPI = costCentreAPI;
            this.state = {
                selectedCentre: 'Click to select options',
                isShowAllDropdownList: false
            };
            this.locations = [];
        }
        async $onInit() {
            await this.getLocationData();
            await this.getDepartmentData();
            await this.checkLocDepStatus();
            await this.getPermission();
            
        }
        async checkLocDepStatus(){
            if(this.locations.length < 2 && this.departments.length < 2){
                this.state.isShowAllDropdownList = true;
            }else if(this.locations.length >= 2 && this.departments.length < 2){
                this.state.selectedCentre = 'Locations';
                this.state.isShowAllDropdownList = false;
            }
        }
        async getDepartmentData() {
            await this.departmentAPI.get().then((response) => {
                console.log(response);
                this.departments = response;
            });
          }
        async getLocationData() {
            await this.locationAPI.get().then(response => {
                console.log(response)
                this.locations = response;
            });
        }
        open() {
            if ( this.get_permissions_data.manage_billing_and_payments == 1 ) { 
                presentModal('cost-centre-form')
            } else {
                $('#permission-modal').modal('show');
            }
            
        }
        dismiss() {
            presentModal('cost-centre-form', 'hide')
        }
        submit() {
        }
        setCostCentre(opt){
            this.state.selectedCentre = opt;
        }
        getPermission() {
            this.costCentreAPI.getPermission()
                .then((response) => {
                    this.get_permissions_data = response.data;
                    console.log(this.get_permissions_data);
                });
        }
    }

    angular.module('app')
        .component('costcentre', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/cost-centre/index.html',
            controller: CostCentreController
        });
}(angular));