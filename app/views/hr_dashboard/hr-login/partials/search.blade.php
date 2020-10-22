<div ng-if="linkedAccountsPagi.total_data > 5" class="relative w-full md:w-64 font-greycliff">
    <form ng-submit="searchAccount(searchAccountText)">
        <svg
            viewBox="0 0 20 20"
            fill="currentColor"
            class="absolute text-gray-600 w-5 h-5 ml-3 left-0 mt-2">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
        </svg>
        <input
            class="bg-gray-light appearance-none border-solid border border-gray-300 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500 pl-10 shadow-inner-sm "
            id="inline-full-name"
            type="text"
            placeholder="Search account"
            value=""
            ng-model="searchAccountText"
        >
    </form>
</div>