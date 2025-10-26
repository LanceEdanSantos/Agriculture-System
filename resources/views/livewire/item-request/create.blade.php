<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2">
                Create Item Request
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                Request items from your assigned farms
            </p>
        </div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
            @if (!$hasFarms)
                <!-- No Farms State -->
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No Farms Available</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                        You currently don't have access to any farms. Please contact your administrator to get assigned to a farm.
                    </p>
                    <button wire:click="$set('mode', 'index')" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl shadow-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Requests
                    </button>
                </div>
            @else
                <!-- Form Section -->
                <form wire:submit.prevent="store" class="p-8 space-y-6">
                    <!-- Farm Selection -->
                    <div class="space-y-2">
                        <label for="farm_id" class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Farm
                        </label>
                        @if ($userFarmsCount === 1)
                            <div class="relative">
                                <div class="flex items-center gap-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-300 dark:border-green-700 rounded-xl p-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $farmNames[$farm_id] ?? 'Selected Farm' }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Auto-selected (your only farm)</p>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="farm_id" value="{{ $farm_id }}">
                            </div>
                        @else
                            <select wire:model="farm_id" id="farm_id"
                                class="w-full bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-base rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-4 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500">
                                <option value="">🏡 Select a farm...</option>
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm['id'] }}">{{ $farm['name'] }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('farm_id') 
                            <p class="flex items-center gap-2 text-red-600 dark:text-red-400 text-sm mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
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
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Inventory Item
                        </label>
                        
                        <div class="relative">
                            <!-- Selected Item Display -->
                            <button type="button" @click="open = !open"
                                class="w-full bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-base rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-4 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500 flex items-center justify-between">
                                <span x-text="getSelectedName()"></span>
                                <svg class="w-5 h-5" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" x-transition
                                class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl shadow-lg max-h-60 overflow-hidden">
                                
                                <!-- Search Input -->
                                <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                    <input type="text" x-model="search" @click.stop
                                        placeholder="🔍 Search items..."
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                                </div>

                                <!-- Items List -->
                                <div class="overflow-y-auto max-h-48">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <button type="button" @click="selectItem(item.id)"
                                            class="w-full text-left px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-600 transition-colors border-b border-gray-100 dark:border-gray-600 last:border-0"
                                            :class="selected == item.id && 'bg-blue-100 dark:bg-gray-600'">
                                            <div class="font-semibold text-gray-900 dark:text-white" x-text="item.name"></div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                <span x-text="item.farm_name"></span> • 
                                                <span class="text-green-600 dark:text-green-400 font-medium">Stock: <span x-text="item.current_stock"></span> <span x-text="item.unit"></span></span>
                                            </div>
                                        </button>
                                    </template>
                                    
                                    <div x-show="filteredItems.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        No items found
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @error('inventory_item_id')
                            <p class="flex items-center gap-2 text-red-600 dark:text-red-400 text-sm mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
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
                                <label for="available_stock" class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Available Stock
                                </label>
                                <input type="text"
                                       id="available_stock"
                                       :value="selectedItem.current_stock + ' ' + selectedItem.unit"
                                       class="w-full bg-green-50 dark:bg-green-900/20 border-2 border-green-300 dark:border-green-700 text-green-900 dark:text-green-100 text-base rounded-xl p-4 font-semibold cursor-not-allowed"
                                       disabled
                                       readonly>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Available in <span x-text="selectedItem.name"></span></p>
                            </div>
                        </template>
                    </div>

                    <!-- Quantity Input -->
                    <div class="space-y-2">
                        <label for="quantity" class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Quantity
                        </label>
                        <input type="number" wire:model="quantity" id="quantity" step="1" min="1"
                            class="w-full bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-base rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-4 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500"
                            placeholder="Enter quantity (e.g., 10)">
                        @error('quantity') 
                            <p class="flex items-center gap-2 text-red-600 dark:text-red-400 text-sm mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Notes Textarea -->
                    <div class="space-y-2">
                        <label for="notes" class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Notes <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        <textarea wire:model="notes" id="notes" rows="4"
                            class="w-full bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-base rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-4 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500 resize-none"
                            placeholder="Add any additional details or special requirements..."></textarea>
                        @error('notes') 
                            <p class="flex items-center gap-2 text-red-600 dark:text-red-400 text-sm mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-6 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Submit Request
                        </button>
                        <button type="button" wire:click="$set('mode', 'index')"
                            class="inline-flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold px-6 py-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
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
