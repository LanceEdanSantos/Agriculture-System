<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Request New Item</h2>
    
    <form wire:submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Farm Selection -->
            <div class="col-span-1">
                <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-1">Farm</label>
                <select 
                    id="farm_id" 
                    wire:model="farmId" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                    <option value="">-- Select Farm --</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                    @endforeach
                </select>
                @error('farmId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Inventory Item Selection -->
            <div class="col-span-1">
                <label for="inventory_item_id" class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                <select 
                    id="inventory_item_id" 
                    wire:model="inventoryItemId"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @if(empty($farmId)) disabled @endif
                    required
                >
                    <option value="">-- Select Item --</option>
                    @foreach($inventoryItems as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }} ({{ $item->unit }})
                        </option>
                    @endforeach
                </select>
                @error('inventoryItemId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Quantity -->
            <div class="col-span-1">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input 
                        type="number" 
                        id="quantity" 
                        wire:model="quantity" 
                        step="0.01" 
                        min="0.01"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                </div>
                @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-4">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
            <textarea 
                id="notes" 
                wire:model="notes" 
                rows="3" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Any additional information about your request..."
            ></textarea>
            @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end">
            <button 
                type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                <span wire:loading.remove wire:target="submit">Submit Request</span>
                <span wire:loading wire:target="submit" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </button>
        </div>
    </form>
</div>
