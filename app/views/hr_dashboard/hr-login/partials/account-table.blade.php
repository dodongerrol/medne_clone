<div class="container mx-auto px-12 py-12" ng-if="showAccounts">
    <div class="grid-layout-one">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex flex-col justify-center">
                <a
                    href="#"
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
        <div class="table-v1-wrapper">
            <table class="table-v1">
                <thead>
                    <th class="thead-v1"> Account Name </th>
                    <th class="thead-v1"> Company ID </th>
                    <th class="thead-v1"> Total Enrolled Employees</th>
                    <th class="thead-v1"> Total Enrolled Dependents </th>
                    <th class="thead-v1"> Plan Type </th>
                </thead>
                <tbody>
                    <tr class="tr-v1" ng-click="chooseAccount('1')">
                        <td class="td-v1 text-blue-500">
                            Mednefits
                        </td>
                        <td class="td-v1">
                            200
                        </td>
                        <td class="td-v1">
                            50
                        </td>
                        <td class="td-v1">
                        20
                        </td>
                        <td class="td-v1">
                            Basic Plan (Pre-paid)
                        </td>
                    </tr>
                    <tr class="tr-v1" ng-click="chooseAccount('1')">
                        <td class="td-v1  text-blue-500">
                            Mednefits
                        </td>
                        <td class="td-v1">
                            200
                        </td>
                        <td class="td-v1">
                            50
                        </td>
                        <td class="td-v1">
                        20
                        </td>
                        <td class="td-v1">
                            Basic Plan (Pre-paid)
                        </td>
                    </tr>
                    <tr class="tr-v1" ng-click="chooseAccount('3')">
                        <td class="td-v1 text-blue-500">
                            Mednefits
                        </td>
                        <td class="td-v1">
                            200
                        </td>
                        <td class="td-v1">
                            50
                        </td>
                        <td class="td-v1">
                        20
                        </td>
                        <td class="td-v1">
                            Basic Plan (Pre-paid)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>