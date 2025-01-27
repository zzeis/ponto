<div wire:poll.10s class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 w-64 md:w-full">
    <div class=" bg-gray-100 dark:bg-gray-700  sm:p-6 rounded-lg">
        <h3 class="text-xl font-semibold mb-3 text-gray-600 dark:text-gray-400">Hoje</h3>
        
        <div class="flex flex-col items-center mb-4 p-2 justify-center text-center">
            
            <div 
                x-data="workingHoursEmoji({{$progressPercentage}})" 
                x-text="emoji"
                class="text-7xl mb-4 transition-transform duration-300">
            </div>

            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4 mb-2">
                <div class="bg-blue-600 h-4 rounded-full transition-all duration-1000"
                     style="width: {{ $progressPercentage }}%">
                </div>
            </div>

            <div class="text-3xl font-bold text-gray-600 dark:text-gray-400">
                {{ number_format($dailyHours, 2) }}h / 6h
            </div>
        </div>
    </div>

    <div class="bg-gray-100 dark:bg-gray-700 p-4 sm:p-6 rounded-lg">
        <h3 class="text-xl font-semibold mb-3 text-gray-600 dark:text-gray-400">Este Mês</h3>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 dark:text-gray-400">Total:</span>
            <span class="text-3xl font-bold text-gray-600 dark:text-gray-400">
                {{ number_format($monthlyHours, 2) }}h
            </span>
        </div>
    </div>
</div>
