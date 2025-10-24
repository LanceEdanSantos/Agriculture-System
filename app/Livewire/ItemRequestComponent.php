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
    public $itemRequest;
    public $farm_id;
    public $selectedItemStock = null;
    public $inventory_item_id = null;
    public $quantity = null;
    public $notes = null;
    public $mode = 'index';
    public $hasFarms = false;
    public $userFarmsCount = 0;
    public $availableItems = [];
    public $farmNames = [];
    public $farms = [];
    public $newMessage = '';
    public $messages = [];

    protected $rules = [
        'farm_id' => 'required|exists:farms,id',
        'inventory_item_id' => 'required|exists:inventory_items,id',
        'quantity' => 'required|numeric|min:1|integer',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $messageRules = [
        'newMessage' => 'required|string|max:1000',
    ];

    public function updatedInventoryItemId()
    {
        $this->updateSelectedItemStock();
    }

    public function mount()
    {
        $this->loadData();
        if ($this->mode === 'create') {
            $this->authorize('create', ItemRequest::class);
        } elseif ($this->mode === 'edit' || $this->mode === 'show') {
            $this->authorize($this->mode === 'edit' ? 'update' : 'view', $this->itemRequest);
            if ($this->mode === 'edit' && $this->itemRequest->status !== 'pending') {
                session()->flash('error', 'Only pending requests can be edited.');
                $this->mode = 'show';
            }
            // Update stock information for edit/show modes
            if ($this->mode === 'edit') {
                $this->updateSelectedItemStock();
            }
        } else {
            $this->authorize('viewAny', ItemRequest::class);
        }
    }

    public function updateSelectedItemStock()
    {
        // In edit mode, use the itemRequest relationship
        if ($this->mode === 'edit' && $this->itemRequest && $this->itemRequest->inventoryItem) {
            $this->selectedItemStock = [
                'current_stock' => $this->itemRequest->inventoryItem->current_stock,
                'unit' => $this->itemRequest->inventoryItem->unit,
                'name' => $this->itemRequest->inventoryItem->name
            ];
            return;
        }

        // In create mode, always check database first (most reliable)
        if ($this->mode === 'create' && $this->inventory_item_id) {
            $inventoryItem = InventoryItem::find($this->inventory_item_id);
            if ($inventoryItem) {
                $this->selectedItemStock = [
                    'current_stock' => $inventoryItem->current_stock,
                    'unit' => $inventoryItem->unit,
                    'name' => $inventoryItem->name
                ];
                return;
            }
        }

        // Default case
        $this->selectedItemStock = null;
    }

    public function loadData()
    {
        // Query farm_user table directly to get user's farm IDs
        $farmIds = DB::table('farm_user')
            ->where('user_id', Auth::id())
            ->pluck('farm_id');
        if ($farmIds->isEmpty()) {
            $this->farms = [];
            $this->availableItems = [];
            $this->hasFarms = false;
            $this->userFarmsCount = 0;
            return;
        }

        // Get the actual farm records
        $farms = Farm::whereIn('id', $farmIds)
            ->where('is_active', true)
            ->get();

        $this->userFarmsCount = $farms->count();
        $this->hasFarms = $this->userFarmsCount > 0;

        if (!$this->hasFarms) {
            $this->farms = [];
            $this->availableItems = [];
            return;
        }

        // If a specific farm is selected, only load items for that farm
        $farmsToLoad = $farms;
        if ($this->farm_id) {
            $farmsToLoad = $farms->where('id', $this->farm_id);
        }

        // Build farms array with inventory items
    $this->farms = [];
        $this->farmNames = [];
        $this->availableItems = [];

        foreach ($farmsToLoad as $farm) {
            $this->farmNames[$farm->id] = $farm->name;

            // Get inventory items visible to this farm
            $farmItemIds = DB::table('farm_inventory_visibility')
                ->where('farm_id', $farm->id)
                ->where('is_visible', true)
                ->pluck('inventory_item_id');

            $farmItems = InventoryItem::whereIn('id', $farmItemIds)
                ->where('status', 'active')
                ->get();

            $this->farms[] = [
                'id' => $farm->id,
                'name' => $farm->name,
                'description' => $farm->description,
                'inventory_items' => $farmItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'unit' => $item->unit,
                        'current_stock' => $item->current_stock,
                    ];
                })->toArray()
            ];

            // Add items to available items list
            foreach ($farmItems as $item) {
                $this->availableItems[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'farm_id' => $farm->id,
                    'farm_name' => $farm->name,
                    'unit' => $item->unit,
                    'current_stock' => $item->current_stock,
                ];
            }
        }

        // Auto-select farm if user has only one
        if ($this->userFarmsCount === 1) {
            $this->farm_id = $farms->first()->id;
        }
    }

    public function render()
    {
        if ($this->mode === 'create') {
            return view('livewire.item-request.create');
        } elseif ($this->mode === 'show') {
            return view('livewire.item-request.show', [
                'itemRequest' => $this->itemRequest,
            ]);
        } elseif ($this->mode === 'edit') {
            return view('livewire.item-request.edit');
        } else {
            return view('livewire.item-request.index');
        }
    }

    public function create()
    {
        $this->authorize('create', ItemRequest::class);
        $this->mode = 'create';
        $this->resetForm();
        $this->loadData();

        // If user has only one farm, auto-select it and reload data for that farm
        if ($this->userFarmsCount === 1) {
            $this->farm_id = $this->farms[0]['id'];
            $this->loadData(); // Reload data with selected farm
        }
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
        $this->itemRequest = ItemRequest::with(['inventoryItem', 'user', 'farm', 'messages.user'])->findOrFail($id);
        $this->authorize('view', $this->itemRequest);
        $this->loadMessages();
        $this->mode = 'show';
    }

    public function loadMessages()
    {
        if ($this->itemRequest) {
            $this->messages = $this->itemRequest->messages()->with('user')->latest()->get()->toArray();
        }
    }

    public function sendMessage()
    {
        $this->validate($this->messageRules);

        try {
            \App\Models\RequestMessage::create([
                'item_request_id' => $this->itemRequest->id,
                'user_id' => Auth::id(),
                'message' => $this->newMessage,
            ]);

            $this->newMessage = '';
            $this->loadMessages();
            session()->flash('message-success', 'Message sent successfully.');
        } catch (\Exception $e) {
            session()->flash('message-error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    public function refreshMessages()
    {
        $this->loadMessages();
    }

    public function edit($id)
    {
        $this->itemRequest = ItemRequest::with('inventoryItem')->findOrFail($id);
        $this->authorize('update', $this->itemRequest);

        if ($this->itemRequest->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be edited.');
            $this->mode = 'show';
            return;
        }

        $this->mode = 'edit';

        // Set inventory_item_id first so computed property works
        $this->inventory_item_id = $this->itemRequest->inventory_item_id;
        $this->farm_id = $this->itemRequest->farm_id;
        $this->quantity = $this->itemRequest->quantity;
        $this->notes = $this->itemRequest->notes;

        // Then load data so availableItems includes the selected item
        $this->loadData();

        // Update stock information after loading data
        $this->updateSelectedItemStock();
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
        if ($this->userFarmsCount !== 1) {
            $this->farm_id = null;
        }
        $this->inventory_item_id = null;
        $this->quantity = null;
        $this->notes = null;
    }
}
