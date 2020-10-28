(function (angular) {
    'use strict';
    class CostCentreController {
        constructor(locationAPI, departmentAPI) {
            this.views = window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/cost-centre';
            this.element = 'cost-centre-form';
            this.locationAPI = locationAPI;
            this.departmentAPI = departmentAPI;
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
            presentModal('cost-centre-form')
        }
        dismiss() {
            presentModal('cost-centre-form', 'hide')
        }
        submit() {
        }
        setCostCentre(opt){
            this.state.selectedCentre = opt;
        }
    }

    angular.module('app')
        .component('costcentre', {
            templateUrl: window.location.origin + '/assets/hr-dashboard/templates/home/companyProfile/BillingPayments/cost-centre/index.html',
            controller: CostCentreController
        });
}(angular));