<div class="min-h-screen bg-white dark:bg-gray-900 py-4 px-3 sm:py-8 sm:px-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit Item Request</h1>
        </div>

        <!-- Messages -->
        @if (session()->has('success'))
            <div class="mb-4 p-3 sm:p-4 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 sm:p-4 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <form wire:submit.prevent="update" class="p-4 sm:p-6 space-y-5">
                <!-- Farm -->
                <div class="space-y-2">
                    <label for="farm_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Farm</label>
                    @if ($userFarmsCount === 1)
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-3">
                            <p class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">{{ $farmNames[$farm_id] ?? 'Selected Farm' }}</p>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Auto-selected</p>
                        </div>
                        <input type="hidden" wire:model="farm_id" value="{{ $farm_id }}">
                    @else
                        <select wire:model="farm_id" id="farm_id" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3">
                            <option value="">Select a farm</option>
                            @foreach ($farms as $farm)
                                <option value="{{ $farm['id'] }}" {{ $farm['id'] == $farm_id ? 'selected' : '' }}>{{ $farm['name'] }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('farm_id') <span class="text-red-600 dark:text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Inventory Item -->
                <div class="space-y-2">
                    <label for="inventory_item_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Inventory Item</label>
                    <select wire:model.live="inventory_item_id" id="inventory_item_id" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3">
                        <option value="">Select an item</option>
                        @foreach ($availableItems as $item)
                            <option value="{{ $item['id'] }}" {{ $item['id'] == $inventory_item_id ? 'selected' : '' }}>{{ $item['name'] }} ({{ $item['farm_name'] }})</option>
                        @endforeach
                    </select>
                    @error('inventory_item_id') <span class="text-red-600 dark:text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Available Stock Display -->
                @if($inventory_item_id && $selectedItemStock)
                <div class="space-y-2">
                    <label for="available_stock" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Available Stock</label>
                    <input type="text"
                           id="available_stock"
                           value="{{ $selectedItemStock['current_stock'] ?? 0 }} {{ $selectedItemStock['unit'] ?? 'units' }}"
                           class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg p-3 font-semibold cursor-not-allowed"
                           disabled
                           readonly>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Available in {{ $selectedItemStock['name'] ?? '' }}</p>
                </div>
                @elseif($inventory_item_id)
                <div class="p-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                    <p class="text-xs sm:text-sm text-gray-800 dark:text-gray-300">Loading stock information...</p>
                </div>
                @endif

                <!-- Quantity -->
                <div class="space-y-2">
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quantity</label>
                    <input type="number" wire:model="quantity" id="quantity" step="1" min="1" placeholder="Enter quantity (e.g., 10)" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3">
                    @error('quantity') <span class="text-red-600 dark:text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea wire:model="notes" id="notes" rows="4" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 p-3 resize-none">{{ $notes }}</textarea>
                    @error('notes') <span class="text-red-600 dark:text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold px-4 py-3 rounded-lg shadow transition">Update</button>
                    <a href="{{ route('item-requests.show', $itemRequest->id) }}" class="inline-flex items-center justify-center bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold px-4 py-3 rounded-lg shadow transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
