<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Request New Item</h2>

    <form wire:submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- FARM SELECTION -->
            <div class="col-span-1" 
                 x-data="{
                    open: false,
                    search: '',
                    selected: @entangle('farmId'),
                    farms: {{ json_encode($farms->map(fn($f)=>['id'=>$f->id,'name'=>$f->name])) }},
                    get filtered() {
                        if (this.search === '') return this.farms;
                        return this.farms.filter(f => f.name.toLowerCase().includes(this.search.toLowerCase()));
                    },
                    select(farm) {
                        this.selected = farm.id;
                        this.search = farm.name;
                        this.open = false;
                        $wire.call('updatedFarmId', farm.id);
                    }
                 }"
                 @click.outside="open = false">

                <label class="block text-sm font-medium text-gray-700 mb-1">Farm</label>
                <div class="relative">
                    <input 
                        type="text"
                        x-model="search"
                        @focus="open = true"
                        placeholder="Type to search..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    <ul x-show="open" x-transition
                        class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-md max-h-48 overflow-y-auto">
                        <template x-for="farm in filtered" :key="farm.id">
                            <li @click="select(farm)"
                                class="px-3 py-2 hover:bg-indigo-100 cursor-pointer text-sm">
                                <span x-text="farm.name"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                @error('farmId') 
                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                @enderror
            </div>

            <!-- ITEM SELECTION -->
            <div class="col-span-1"
                 x-data="{
                    open: false,
                    search: '',
                    selected: @entangle('inventoryItemId'),
                    items: {{ json_encode($inventoryItems->map(fn($i)=>['id'=>$i->id,'name'=>$i->name.' ('.$i->unit.')'])) }},
                    get filtered() {
                        if (this.search === '') return this.items;
                        return this.items.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
                    },
                    select(item) {
                        this.selected = item.id;
                        this.search = item.name;
                        this.open = false;
                    }
                 }"
                 @click.outside="open = false">

                <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                <div class="relative">
                    <input 
                        type="text"
                        x-model="search"
                        @focus="open = true"
                        placeholder="Type to search..."
                        :disabled="$wire.farmId == ''"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >

                    <ul x-show="open" x-transition
                        class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-md max-h-48 overflow-y-auto">
                        <template x-for="item in filtered" :key="item.id">
                            <li @click="select(item)"
                                class="px-3 py-2 hover:bg-indigo-100 cursor-pointer text-sm">
                                <span x-text="item.name"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                @error('inventoryItemId') 
                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                @enderror
            </div>

            <!-- QUANTITY -->
            <div class="col-span-1">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <input 
                    type="number" 
                    id="quantity" 
                    wire:model="quantity" 
                    step="0.01" 
                    min="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- NOTES -->
        <div class="mt-4">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
            <textarea 
                id="notes" 
                wire:model="notes" 
                rows="3" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Any additional information about your request..."></textarea>
            @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- SUBMIT -->
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
