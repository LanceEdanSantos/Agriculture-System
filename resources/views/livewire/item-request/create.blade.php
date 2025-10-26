<div class="min-h-screen bg-white dark:bg-gray-900 py-4 px-3 sm:py-8 sm:px-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Create Item Request
            </h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                Request items from your assigned farms
            </p>
        </div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            @if (!$hasFarms)
                <!-- No Farms State -->
                <div class="p-6 sm:p-8 text-center">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3">No Farms Available</h2>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-6">
                        You currently don't have access to any farms. Please contact your administrator to get assigned to a farm.
                    </p>
                    <button wire:click="$set('mode', 'index')" class="inline-flex items-center gap-2 px-4 py-2 sm:px-6 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Requests
                    </button>
                </div>
            @else
                <!-- Form Section -->
                <form wire:submit.prevent="store" class="p-4 sm:p-6 space-y-5">
                    <!-- Farm Selection -->
                    <div class="space-y-2">
                        <label for="farm_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Farm
                        </label>
                        @if ($userFarmsCount === 1)
                            <div class="relative">
                                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-3">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">{{ $farmNames[$farm_id] ?? 'Selected Farm' }}</p>
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Auto-selected</p>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="farm_id" value="{{ $farm_id }}">
                            </div>
                        @else
                            <select wire:model="farm_id" id="farm_id"
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3">
                                <option value="">Select a farm...</option>
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm['id'] }}">{{ $farm['name'] }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('farm_id') 
                            <p class="text-red-600 dark:text-red-400 text-xs sm:text-sm mt-1">
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Inventory Item Selection (Searchable) -->
                    <div class="space-y-2" x-data="{
                        open: false,
                        search: '',
                        selected: @entangle('inventory_item_id').live,
                        items: {{ json_encode($availableItems) }},
                        get filteredItems() {
                            if (this.search === '') return this.items;
                            return this.items.filter(item => 
                                item.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                item.farm_name.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        selectItem(id) {
                            this.selected = id;
                            this.open = false;
                            this.search = '';
                        },
                        getSelectedName() {
                            const item = this.items.find(i => i.id == this.selected);
                            return item ? `${item.name} (${item.farm_name})` : 'Select an item...';
                        }
                    }" @click.away="open = false">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Inventory Item
                        </label>
                        
                        <div class="relative">
                            <!-- Selected Item Display -->
                            <button type="button" @click="open = !open"
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3 flex items-center justify-between">
                                <span class="truncate" x-text="getSelectedName()"></span>
                                <svg class="w-5 h-5 flex-shrink-0 ml-2" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" x-transition
                                class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                
                                <!-- Search Input -->
                                <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                    <input type="text" x-model="search" @click.stop
                                        placeholder="Search items..."
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-500 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:text-white">
                                </div>

                                <!-- Items List -->
                                <div class="overflow-y-auto max-h-48">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <button type="button" @click="selectItem(item.id)"
                                            class="w-full text-left px-3 py-2 sm:px-4 sm:py-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-b border-gray-100 dark:border-gray-600 last:border-0"
                                            :class="selected == item.id && 'bg-gray-100 dark:bg-gray-600'">
                                            <div class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white" x-text="item.name"></div>
                                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                                <span x-text="item.farm_name"></span> • 
                                                <span>Stock: <span x-text="item.current_stock"></span> <span x-text="item.unit"></span></span>
                                            </div>
                                        </button>
                                    </template>
                                    
                                    <div x-show="filteredItems.length === 0" class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No items found
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @error('inventory_item_id')
                            <p class="text-red-600 dark:text-red-400 text-xs sm:text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Available Stock Display -->
                    <div class="space-y-2" x-data="{
                        itemId: @entangle('inventory_item_id').live,
                        items: {{ json_encode($availableItems) }},
                        get selectedItem() {
                            return this.items.find(i => i.id == this.itemId);
                        }
                    }">
                        <template x-if="itemId && selectedItem">
                            <div>
                                <label for="available_stock" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Available Stock
                                </label>
                                <input type="text"
                                       id="available_stock"
                                       :value="selectedItem.current_stock + ' ' + selectedItem.unit"
                                       class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg p-3 font-semibold cursor-not-allowed"
                                       disabled
                                       readonly>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Available in <span x-text="selectedItem.name"></span></p>
                            </div>
                        </template>
                    </div>

                    <!-- Quantity Input -->
                    <div class="space-y-2">
                        <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Quantity
                        </label>
                        <input type="number" wire:model="quantity" id="quantity" step="1" min="1"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3"
                            placeholder="Enter quantity (e.g., 10)">
                        @error('quantity') 
                            <p class="text-red-600 dark:text-red-400 text-xs sm:text-sm mt-1">
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Notes Textarea -->
                    <div class="space-y-2">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Notes <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <textarea wire:model="notes" id="notes" rows="4"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3 resize-none"
                            placeholder="Add any additional details or special requirements..."></textarea>
                        @error('notes') 
                            <p class="text-red-600 dark:text-red-400 text-xs sm:text-sm mt-1">
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold px-4 py-3 rounded-lg shadow transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Submit Request
                        </button>
                        <button type="button" wire:click="$set('mode', 'index')"
                            class="inline-flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold px-4 py-3 rounded-lg shadow transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
