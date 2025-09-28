<div class="space-y-6">
    @foreach ($categories as $category)
        <div class="border rounded-lg p-4 shadow-sm" x-data="{
            items: {
                @foreach ($category->inventoryItems as $item)
                    {{ $item->id }}: {{ $farm->inventoryItems()->where('inventory_items.id', $item->id)->wherePivot('is_visible', true)->exists() ? 'true' : 'false' }},
                @endforeach
            },
            init() {
                // Convert string 'true'/'false' to actual booleans
                Object.keys(this.items).forEach(key => {
                    this.items[key] = this.items[key] === true || this.items[key] === 'true';
                });
                
                // Watch for changes and update the category state
                this.$watch('items', () => {
                    this.$nextTick(() => {
                        const allChecked = Object.values(this.items).every(Boolean);
                        this.$wire.call('syncCategory', {{ $category->id }}, allChecked);
                    });
                }, { deep: true });
            },
            get allChecked() {
                return Object.values(this.items).every(Boolean);
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
            },
            toggleItem(id) {
                this.items[id] = !this.items[id];
                this.$wire.call('syncItem', id, this.items[id]);
            }
        }" x-init="init()">
            {{-- Category Header --}}
            <div class="flex items-center justify-between mb-3">
                <span class="font-semibold text-lg">{{ $category->name }}</span>

                {{-- Filament checkbox (Alpine binding via :checked + manual click) --}}
                <x-filament::input.checkbox 
                    :checked="$category->inventoryItems->count() > 0 && $category->inventoryItems->every(
                        fn($item) => $farm->inventoryItems()
                            ->where('inventory_items.id', $item->id)
                            ->wherePivot('is_visible', true)
                            ->exists()
                    )"
                    :attributes="new \Illuminate\View\ComponentAttributeBag([
                        'x-ref' => 'categoryCheckbox',
                        'x-bind:checked' => 'allChecked',
                        'x-bind:indeterminate.prop' => 'someChecked',
                        'x-on:change' => 'toggleAll()'
                    ])" />
            </div>

            {{-- Inventory Items --}}
            @if ($category->inventoryItems->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($category->inventoryItems as $item)
                        <div class="flex items-center justify-between px-3 py-2 rounded-md shadow-inner">
                            <span class="text-sm">{{ $item->name }}</span>

                            {{-- Filament checkbox, manual Alpine binding --}}
                            <x-filament::input.checkbox 
                                :checked="$farm->inventoryItems()
                                    ->where('inventory_items.id', $item->id)
                                    ->wherePivot('is_visible', true)
                                    ->exists()"
                                :attributes="new \Illuminate\View\ComponentAttributeBag([
                                    'x-model' => 'items[' . $item->id . ']',
                                    'x-on:change' => 'toggleItem(' . $item->id . ')'
                                ])" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
