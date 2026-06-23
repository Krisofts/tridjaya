<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
    <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">
                Users &amp; Revenue Statistics
            </h3>
            <p class="text-theme-sm mt-1 text-gray-500 dark:text-gray-400">
                Visualize month-to-month progress and engagement.
            </p>
        </div>

        <div x-data="{ selected: 'optionOne' }"
            class="inline-flex h-11 w-fit items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
            <button @click="selected = 'optionOne'"
                :class="selected === 'optionOne' ? 'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                    'text-gray-500 dark:text-gray-400'"
                class="text-theme-sm h-10 rounded-md px-3 py-2.5 font-medium transition-colors hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800">
                Daily
            </button>

            <button @click="selected = 'optionTwo'"
                :class="selected === 'optionTwo' ? 'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                    'text-gray-500 dark:text-gray-400'"
                class="text-theme-sm h-10 rounded-md px-3 py-2.5 font-medium transition-colors hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white text-gray-500 dark:text-gray-400">
                Weekly
            </button>

            <button @click="selected = 'optionThree'"
                :class="selected === 'optionThree' ? 'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                    'text-gray-500 dark:text-gray-400'"
                class="text-theme-sm h-10 rounded-md px-3 py-2.5 font-medium transition-colors hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white text-gray-500 dark:text-gray-400">
                Monthly
            </button>
        </div>
    </div>

    <div class="custom-scrollbar max-w-full overflow-x-auto">
        <div class="-ml-4 min-w-[1000px] pl-2 xl:min-w-full" style="min-height: 265px;">
            
            
        </div>
    </div>
</div>
