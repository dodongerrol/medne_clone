<div ng-if="linkedAccountsPagi.total_data > 5 && !isSearchActive" class="flex items-center justify-between container px-56">
    <div class="flex items-center space-x-3">
        <p class="flex items-center text-gray-600 m-0" ng-click="prevPage()">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 mr-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Prev
        </p>
        <div class="flex items-center space-x-2">
            <p ng-repeat="list in range(linkedAccountsPagi.last_page)" class="pagination-item" ng-class="{'pagination-active' : $index + 1 == pageActive}" ng-click="setPage($index+1)" ng-bind="$index+1"></p>
        </div>
        <p class="flex items-center text-gray-600 m-0" ng-click="nextPage()">
            Next
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 ml-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </p>
    </div>
    <div class="relative inline-block text-left" x-data="{ open: false }">
        <a href="#" x-on:click.prevent="open = !open" class="ui-btn ui-btn-link-secondary">
            @{{perPage}} per page
            <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </a>
        <div
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            @click.away="open = false"
            class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg"
            @click="open = false"
        >
            <div class="rounded-md bg-white shadow-xs">
                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                  <a href="#" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" role="menuitem" ng-click="setPerPage(5)">5</a>
                  <a href="#" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" role="menuitem" ng-click="setPerPage(10)">10</a>
                  <a href="#" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" role="menuitem" ng-click="setPerPage(20)">20</a>
                </div>
            </div>
        </div>
    </div>
</div>