<div class="container mx-auto px-12 py-12" ng-if="showAccounts">
    <div class="grid-layout-one font-greycliff">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex flex-col justify-center">
                <a
                    ng-if="isSearchActive" 
                    href="javascript:void(0)"
                    ng-click="searchAccount('')"
                    class="flex items-center text-gray-900 hover:text-gray-500 transition duration-100 hover:no-underline"
                    >
                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Back
                </a>
            </div>
            <div class="flex flex-col justify-center">
                <p class="table-v1-title mx-auto">
                    Choose an account
                </p>
            </div>
            <div class="flex flex-col justify-center items-end">
                @include('hr_dashboard.hr-login.partials.search')
            </div>
        </div>
        <div class="ui-card border-solid border border-color">
            <table class="table-v1">
                <thead>
                    <th class="thead-v1"> Account Name </th>
                    <th class="thead-v1"> Company ID </th>
                    <th class="thead-v1">
                        <p class="m-0"> Total Enrolled </p>
                        <p class="m-0  leading-none"> Employees  </p>
                    </th>
                    <th class="thead-v1">
                        <p class="m-0"> Total Enrolled </p>
                        <p class="m-0 leading-none"> Dependents  </p>
                    </th>
                    <th class="thead-v1"> Plan Type </th>
                </thead>
                <tbody>
                    <tr ng-repeat="list in linkedAccounts" class="tr-v1" ng-click="chooseAccount(list)">
                        <td class="td-v1 text-blue-500" ng-bind="list.account_name">
                            Mednefits
                        </td>
                        <td class="td-v1" ng-bind="list.company_id">
                            200
                        </td>
                        <td class="td-v1" ng-bind="list.total_enrolled_employee_status">
                            50
                        </td>
                        <td class="td-v1" ng-bind="list.total_enrolled_dependent_status">
                            20
                        </td>
                        <td class="td-v1" ng-bind="list.plan_type">
                            Basic Plan (Pre-paid)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @include('hr_dashboard.hr-login.partials.pagination')
    </div>
</div>