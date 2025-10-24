<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Item Request</h1>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="update" class="space-y-6">
        <div>
            <label for="farm_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Farm</label>
            @if ($userFarmsCount === 1)
                <div class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    {{ $farmNames[$farm_id] ?? 'Selected Farm' }} <span class="text-xs text-gray-500">(Auto-selected)</span>
                </div>
                <input type="hidden" wire:model="farm_id" value="{{ $farm_id }}">
            @else
                <select wire:model="farm_id" id="farm_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="">Select a farm</option>
                    @foreach ($farms as $farm)
                        <option value="{{ $farm['id'] }}" {{ $farm['id'] == $farm_id ? 'selected' : '' }}>{{ $farm['name'] }}</option>
                    @endforeach
                </select>
            @endif
            @error('farm_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="inventory_item_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Inventory Item</label>
            <select wire:model="inventory_item_id" id="inventory_item_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                <option value="">Select an item</option>
                @foreach ($availableItems as $item)
                    <option value="{{ $item['id'] }}" {{ $item['id'] == $inventory_item_id ? 'selected' : '' }}>{{ $item['name'] }} ({{ $item['farm_name'] }})</option>
                @endforeach
            </select>
            @error('inventory_item_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Available Stock Display -->
        @if($selectedItemStock)
        <div class="space-y-2">
            <label for="available_stock" class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Available Stock
            </label>
            <input type="text"
                   id="available_stock"
                   value="{{ $selectedItemStock['current_stock'] }} {{ $selectedItemStock['unit'] }}"
                   class="w-full bg-green-50 dark:bg-green-900/20 border-2 border-green-300 dark:border-green-700 text-green-900 dark:text-green-100 text-sm rounded-lg p-2.5 font-semibold cursor-not-allowed"
                   disabled
                   readonly>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Available in {{ $selectedItemStock['name'] }}</p>
        </div>
        @endif

        <div>
            <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity</label>
            <input type="number" wire:model="quantity" id="quantity" step="1" min="1" placeholder="Enter quantity (e.g., 10)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            @error('quantity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
            <textarea wire:model="notes" id="notes" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $notes }}</textarea>
            @error('notes') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">Update</button>
            <a href="{{ route('item-requests.show', $itemRequest->id) }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 focus:ring-4 focus:ring-gray-300">Cancel</a>
        </div>
    </form>
</div>