<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create Item Request</h1>

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

    @if (!$hasFarms)
        <div class="mb-6 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h2 class="text-lg font-semibold text-yellow-800 mb-2">No Farms Available</h2>
            <p class="text-yellow-700">You don't have access to any farms. Please contact your administrator to get access to farms before creating item requests.</p>
        </div>
    @else
        <form wire:submit.prevent="store" class="space-y-6">
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
                            <option value="{{ $farm['id'] }}">{{ $farm['name'] }}</option>
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
                        <option value="{{ $item['id'] }}">{{ $item['name'] }} ({{ $item['farm_name'] }})</option>
                    @endforeach
                </select>
                @error('inventory_item_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity</label>
                <input type="number" wire:model="quantity" id="quantity" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('quantity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                <textarea wire:model="notes" id="notes" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                @error('notes') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">Submit</button>
            </div>
        </form>
    @endif
</div>