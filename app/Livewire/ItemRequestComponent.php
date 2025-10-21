<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\ItemRequest;
use App\Models\ItemRequestAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ItemRequestComponent extends Component
{
    use WithFileUploads;

    public $farms = [];
    public $itemRequest;
    public $farm_id;
    public $inventory_item_id;
    public $quantity;
    public $notes;
    public $mode = 'index'; // Can be 'index', 'create', 'show', 'edit'
    public $hasFarms = false;
    public $userFarmsCount = 0;
    public $availableItems = [];
    public $farmNames = [];
    public $attachments = [];
    public $existingAttachments = [];

    protected $rules = [
        'farm_id' => 'required|exists:farms,id',
        'inventory_item_id' => 'required|exists:inventory_items,id',
        'quantity' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string|max:1000',
        'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
    ];

    public function mount()
    {
        if ($this->mode === 'create') {
            $this->authorize('create', ItemRequest::class);
            $this->loadFarms();
            $this->checkUserFarms();
        } elseif ($this->mode === 'edit' || $this->mode === 'show') {
            $this->authorize($this->mode === 'edit' ? 'update' : 'view', $this->itemRequest);
            if ($this->mode === 'edit' && $this->itemRequest->status !== 'pending') {
                session()->flash('error', 'Only pending requests can be edited.');
                $this->mode = 'show';
            }
            if ($this->mode === 'edit') {
                $this->loadFarms();
                $this->checkUserFarms();
                $this->farm_id = $this->itemRequest->farm_id;
                $this->inventory_item_id = $this->itemRequest->inventory_item_id;
                $this->quantity = $this->itemRequest->quantity;
                $this->notes = $this->itemRequest->notes;
                $this->existingAttachments = $this->itemRequest->attachments;
            }
            if ($this->mode === 'show') {
                $this->itemRequest->load([
                    'farm',
                    'inventoryItem',
                    'user',
                    'statuses' => fn($query) => $query->latest(),
                    'statuses.changedBy',
                    'feedback',
                    'attachments'
                ]);
            }
        } else {
            $this->authorize('viewAny', ItemRequest::class);
            $this->loadFarms();
            $this->checkUserFarms();
        }
    }

    public function loadFarms()
    {
        // Check if user has any farms associated
        $userFarms = Auth::user()->farms()->get();
        
        if ($userFarms->isEmpty()) {
            // User has no farms - return empty array
            $this->farms = [];
            return;
        }
        
        // User has farms - get all inventory items from those farms
        $inventoryItems = collect();
        
        foreach ($userFarms as $farm) {
            // Get all active inventory items for this farm
            $farmItems = $farm->inventoryItems()
                ->where('status', 'active')
                ->get();
                
            foreach ($farmItems as $item) {
                $inventoryItems->push([
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'unit' => $item->unit,
                    'current_stock' => $item->current_stock,
                    'unit_cost' => $item->unit_cost,
                    'farm_name' => $farm->name,
                    'farm_id' => $farm->id,
                ]);
            }
        }
        
        // Group items by farm for easier display
        $this->farms = $userFarms->map(function ($farm) use ($inventoryItems) {
            $farmItems = $inventoryItems->where('farm_id', $farm->id);
            
            return [
                'id' => $farm->id,
                'name' => $farm->name,
                'description' => $farm->description,
                'inventory_items' => $farmItems->values()->toArray(),
            ];
        })->toArray();
    }

    public function checkUserFarms()
    {
        $userFarms = Auth::user()->farms()->with(['inventoryItems'])->get();

        $this->userFarmsCount = $userFarms->count();
        $this->hasFarms = $this->userFarmsCount > 0;

        if ($this->hasFarms) {
            $this->availableItems = [];
            $this->farmNames = [];

            foreach ($userFarms as $farm) {
                $this->farmNames[$farm->id] = $farm->name;

                // If you want to filter using pivot "is_visible"
                $farmItems = $farm->inventoryItems()
                    ->wherePivot('is_visible', true)
                    ->get();

                foreach ($farmItems as $item) {
                    $this->availableItems[] = [
                        'id'            => $item->id,
                        'name'          => $item->name,
                        'farm_id'       => $farm->id,
                        'farm_name'     => $farm->name,
                        'unit'          => $item->unit,
                        'current_stock' => $item->current_stock,
                    ];
                }
            }

            // Debug after collecting all items
            // dd($this->availableItems);

            // Auto-select farm if user has only one farm
            if ($this->userFarmsCount === 1) {
                $this->farm_id = $userFarms->first()->id;
            }
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
        $this->loadFarms();
    }

    public function removeAttachment($attachmentId)
    {
        if ($this->mode === 'edit' && $this->itemRequest) {
            $attachment = $this->itemRequest->attachments()->find($attachmentId);
            if ($attachment) {
                $attachment->deleteWithFile();
                $this->existingAttachments = $this->existingAttachments->reject(fn($a) => $a->id == $attachmentId);
            }
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

            // Handle file uploads
            $this->handleFileUploads($itemRequest);

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

    protected function handleFileUploads($itemRequest)
    {
        if ($this->attachments) {
            foreach ($this->attachments as $file) {
                if ($file && $file->isValid()) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('item-requests/' . $itemRequest->id, $fileName, 'public');

                    ItemRequestAttachment::create([
                        'item_request_id' => $itemRequest->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }
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

            // Handle new file uploads
            $this->handleFileUploads($this->itemRequest);

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
        // Don't reset farm_id if user has only one farm (it was auto-selected)
        if ($this->userFarmsCount !== 1) {
            $this->farm_id = null;
        }
        $this->inventory_item_id = null;
        $this->quantity = null;
        $this->notes = null;
        $this->attachments = [];
        $this->existingAttachments = [];
    }
}
