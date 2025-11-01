<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Supply Request</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Request supplies and materials for your farm operations</p>
                </div>
                <div>
                    <button wire:click="$set('mode', 'index')" 
                        class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </button>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 rounded-lg text-sm {{ session('message.type') === 'success' ? 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800' }}">
                <div class="flex items-center">
                    <svg class="h-5 w-5 {{ session('message.type') === 'success' ? 'text-green-500' : 'text-red-500' }} mr-2" fill="currentColor" viewBox="0 0 20 20">
                        @if(session('message.type') === 'success')
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        @else
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        @endif
                    </svg>
                    <p class="{{ session('message.type') === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                        {{ session('message.text') }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if (!$hasFarms)
                <!-- No Farms State -->
                <div class="p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Farms Assigned</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                        You don't have access to any farms yet. Please contact your administrator to get assigned.
                    </p>
                    <button wire:click="$set('mode', 'index')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Requests
                    </button>
                </div>
            @else
                <!-- Form Section -->
                <form wire:submit.prevent="store" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Farm Selection -->
                    <div class="space-y-2">
                        <label for="farm_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Farm Location
                        </label>
                        @if ($userFarmsCount === 1)
                            <div class="relative">
                                <div class="flex items-center gap-3 bg-green-50 dark:bg-gray-700/50 border border-green-200 dark:border-gray-600 rounded-lg p-3.5">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $farmNames[$farm_id] ?? 'Selected Farm' }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Assigned farm</p>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="farm_id" value="{{ $farm_id }}">
                            </div>
                        @else
                            <select wire:model="farm_id" id="farm_id"
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 px-3 py-2.5 text-sm">
                                <option value="">Select a farm</option>
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

                    <!-- Item Selection -->
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Supply Item
                        </label>
                        
                        <div class="relative">
                            <!-- Selected Item Display -->
                            <button type="button" @click="open = !open"
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 px-4 py-3 flex items-center justify-between hover:border-gray-400">
                                <span class="truncate" x-text="getSelectedName()"></span>
                                <svg class="w-5 h-5 flex-shrink-0 ml-2 text-gray-400" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" x-transition
                                class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-64 overflow-hidden">
                                
                                <!-- Search Input -->
                                <div class="p-3 border-b border-gray-200 dark:border-gray-600">
                                    <input type="text" x-model="search" @click.stop
                                        placeholder="Search items..."
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-500 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-600 dark:text-white">
                                </div>

                                <!-- Items List -->
                                <div class="overflow-y-auto max-h-48">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <button type="button" @click="selectItem(item.id)"
                                            class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors border-b border-gray-100 dark:border-gray-600 last:border-0"
                                            :class="selected == item.id && 'bg-gray-50 dark:bg-gray-600'">
                                            <div class="font-medium text-gray-900 dark:text-white" x-text="item.name"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                                <span x-text="item.farm_name"></span> • 
                                                <span x-text="item.current_stock"></span> <span x-text="item.unit"></span> available
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
                            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Current Stock</p>
                                        <div class="flex items-baseline mt-1">
                                            <span class="text-2xl font-bold text-blue-900 dark:text-white" x-text="selectedItem.current_stock"></span>
                                            <span class="ml-2 text-sm text-blue-700 dark:text-blue-300" x-text="selectedItem.unit"></span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                        Available
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Quantity Input -->
                    <div class="space-y-2">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Quantity Needed
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" wire:model="quantity" id="quantity" step="1" min="1"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm px-3 py-2.5"
                                placeholder="Enter quantity">
                        </div>
                        @error('quantity') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Notes Textarea -->
                    <div class="space-y-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Additional Notes <span class="text-gray-500 dark:text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <textarea wire:model="notes" id="notes" rows="4"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm px-3 py-2.5"
                            placeholder="Add any special instructions or details..."></textarea>
                        @error('notes') 
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" 
                            wire:click="$set('mode', 'index')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Request
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
