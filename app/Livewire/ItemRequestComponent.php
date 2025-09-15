<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemRequestComponent extends Component
{
    public $farms = [];
    public $itemRequest;
    public $farm_id;
    public $inventory_item_id;
    public $quantity;
    public $notes;
    public $mode = 'index'; // Can be 'index', 'create', 'show', 'edit'

    protected $rules = [
        'farm_id' => 'required|exists:farms,id',
        'inventory_item_id' => 'required|exists:inventory_items,id',
        'quantity' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        if ($this->mode === 'create') {
            $this->authorize('create', ItemRequest::class);
            $this->loadFarms();
        } elseif ($this->mode === 'edit' || $this->mode === 'show') {
            $this->authorize($this->mode === 'edit' ? 'update' : 'view', $this->itemRequest);
            if ($this->mode === 'edit' && $this->itemRequest->status !== 'pending') {
                session()->flash('error', 'Only pending requests can be edited.');
                $this->mode = 'show';
            }
            if ($this->mode === 'edit') {
                $this->loadFarms();
                $this->farm_id = $this->itemRequest->farm_id;
                $this->inventory_item_id = $this->itemRequest->inventory_item_id;
                $this->quantity = $this->itemRequest->quantity;
                $this->notes = $this->itemRequest->notes;
            }
            if ($this->mode === 'show') {
                $this->itemRequest->load([
                    'farm',
                    'inventoryItem',
                    'user',
                    'statuses' => fn($query) => $query->latest(),
                    'statuses.changedBy',
                    'feedback'
                ]);
            }
        } else {
            $this->authorize('viewAny', ItemRequest::class);
        }
    }

    public function loadFarms()
    {
        // First, get all farms visible to the user
        $farms = Farm::whereHas('users', function ($query) {
            $query->where('user_id', Auth::id())
                ->where('is_visible', true);
        })->get();

        // Then, for each farm, get the visible inventory items
        $this->farms = $farms->map(function ($farm) {
            // Get inventory items directly visible to this farm
            $directlyVisible = $farm->inventoryItems()
                ->where('is_active', true)
                ->whereHas('farms', function ($query) use ($farm) {
                    $query->where('farms.id', $farm->id)
                        ->where('farm_inventory_visibility.is_visible', true);
                })
                ->get();

            // Get inventory items visible through category
            $categoryVisible = $farm->inventoryItems()
                ->where('is_active', true)
                ->whereHas('category.farms', function ($query) use ($farm) {
                    $query->where('farms.id', $farm->id)
                        ->where('farm_category_visibility.is_visible', true);
                })
                ->get();

            // Merge and deduplicate the collections
            $inventoryItems = $directlyVisible->merge($categoryVisible)->unique('id');

            // Convert farm to array and add inventory items
            $farmArray = $farm->toArray();
            $farmArray['inventory_items'] = $inventoryItems->toArray();
            
            return $farmArray;
        })->toArray();
    }

    public function render()
    {
        if ($this->mode === 'create') {
            return view('livewire.item-request.create')
                ->layout('layouts.app');
        } elseif ($this->mode === 'show') {
            return view('livewire.item-request.show', [
                'itemRequest' => $this->itemRequest,
            ])->layout('layouts.app');
        } elseif ($this->mode === 'edit') {
            return view('livewire.item-request.edit')
                ->layout('layouts.app');
        } else {
            return view('livewire.item-request.index')
                ->layout('layouts.app');
        }
    }

    public function create()
    {
        $this->authorize('create', ItemRequest::class);
        $this->mode = 'create';
        $this->resetForm();
        $this->loadFarms();
    }

    public function store()
    {
        $this->authorize('create', ItemRequest::class);
        $this->validate();

        try {
            DB::beginTransaction();

            $itemRequest = ItemRequest::create([
                'user_id' => Auth::id(),
                'farm_id' => $this->farm_id,
                'inventory_item_id' => $this->inventory_item_id,
                'quantity' => $this->quantity,
                'notes' => $this->notes,
                'requested_at' => now(),
                'status' => 'pending',
            ]);

            $itemRequest->statuses()->create([
                'status' => 'pending',
                'changed_by' => Auth::id(),
                'notes' => 'Request created',
            ]);

            DB::commit();

            session()->flash('success', 'Item request created successfully.');
            $this->itemRequest = $itemRequest;
            $this->mode = 'show';
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create item request: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->itemRequest = ItemRequest::findOrFail($id);
        $this->authorize('view', $this->itemRequest);
        $this->mode = 'show';
    }

    public function edit($id)
    {
        $this->itemRequest = ItemRequest::findOrFail($id);
        $this->authorize('update', $this->itemRequest);
        if ($this->itemRequest->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be edited.');
            $this->mode = 'show';
            return;
        }
        $this->mode = 'edit';
    }

    public function update()
    {
        $this->authorize('update', $this->itemRequest);
        if ($this->itemRequest->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be updated.');
            $this->mode = 'show';
            return;
        }
        $this->validate();

        try {
            DB::beginTransaction();

            $this->itemRequest->update([
                'farm_id' => $this->farm_id,
                'inventory_item_id' => $this->inventory_item_id,
                'quantity' => $this->quantity,
                'notes' => $this->notes,
            ]);

            $this->itemRequest->statuses()->create([
                'status' => $this->itemRequest->status,
                'changed_by' => Auth::id(),
                'notes' => 'Request details updated',
            ]);

            DB::commit();

            session()->flash('success', 'Item request updated successfully.');
            $this->mode = 'show';
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update item request: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->itemRequest = ItemRequest::findOrFail($id);
        $this->authorize('delete', $this->itemRequest);

        if ($this->itemRequest->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be deleted.');
            return;
        }

        try {
            DB::beginTransaction();

            $this->itemRequest->statuses()->create([
                'status' => 'cancelled',
                'changed_by' => Auth::id(),
                'notes' => 'Request deleted by user',
            ]);

            $this->itemRequest->delete();

            DB::commit();

            session()->flash('success', 'Item request deleted successfully.');
            $this->mode = 'index';
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete item request: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->farm_id = null;
        $this->inventory_item_id = null;
        $this->quantity = null;
        $this->notes = null;
    }
}
