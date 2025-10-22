<div class="space-y-6">
    @foreach ($categories as $category)
        <div class="border rounded-lg p-4 shadow-sm" x-data="{
            items: {
                @foreach ($category->inventoryItems as $item)
                    {{ $item->id }}: {{ $farm->inventoryItems()->where('inventory_items.id', $item->id)->wherePivot('is_visible', true)->exists() ? 'true' : 'false' }},
                @endforeach
            },
            init() {
                Object.keys(this.items).forEach(key => {
                    this.items[key] = this.items[key] === true || this.items[key] === 'true';
                });

                this.$watch('items', () => {
                    this.$nextTick(() => {
                        const allChecked = Object.values(this.items).every(Boolean);
                        this.$wire.call('syncCategory', {{ $category->id }}, allChecked);
                    });
                }, { deep: true });
            },
            get allChecked() {
                return Object.values(this.items).length > 0 && Object.values(this.items).every(Boolean);
            },
            get someChecked() {
                return Object.values(this.items).some(Boolean) && !this.allChecked;
            },
            toggleAll() {
                const newValue = !this.allChecked;
                Object.keys(this.items).forEach(key => {
                    this.items[key] = newValue;
                    this.$wire.call('syncItem', key, newValue);
                });
            }
        }" x-init="init()">

            {{-- Category Header --}}
            <div class="flex items-center justify-between mb-3">
                <span class="font-semibold text-lg">{{ $category->name }}</span>

                <x-filament::input.checkbox 
                    x-bind:checked="allChecked"
                    x-bind:indeterminate.prop="someChecked"
                    x-on:change="toggleAll()" />
            </div>

            {{-- Inventory Items --}}
            @if ($category->inventoryItems->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($category->inventoryItems as $item)
                        <div class="flex items-center justify-between px-3 py-2 rounded-md shadow-inner">
                            <span class="text-sm">{{ $item->name }}</span>

                            <x-filament::input.checkbox 
                                x-model="items[{{ $item->id }}]"
                                x-on:change="$wire.call('syncItem', {{ $item->id }}, items[{{ $item->id }}])" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
