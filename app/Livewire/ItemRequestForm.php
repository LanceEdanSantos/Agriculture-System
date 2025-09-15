<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ItemRequestForm extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection $farms */
    public $farms;
    
    /** @var \Illuminate\Database\Eloquent\Collection $inventoryItems */
    public $inventoryItems;
    
    public string $farmId = '';
    public string $inventoryItemId = '';
    public float $quantity = 1;
    public string $notes = '';
    
    protected $rules = [
        'farmId' => 'required|exists:farms,id',
        'inventoryItemId' => 'required|exists:inventory_items,id',
        'quantity' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string|max:1000',
    ];
    
    public function mount(): void
    {
        $this->farms = collect();
        $this->inventoryItems = collect();
        $this->loadFarms();
    }
    
    public function loadFarms(): void
    {
        $this->farms = Auth::user()
            ->farms()
            ->wherePivot('is_visible', true)
            ->get();
            
        if ($this->farms->count() === 1) {
            $this->farmId = (string)$this->farms->first()->id;
            $this->updatedFarmId($this->farmId);
        }
    }
    
    public function updatedFarmId($value): void
    {
        $this->inventoryItems = collect();
        $this->inventoryItemId = '';
        
        if (!empty($value)) {
            $this->inventoryItems = InventoryItem::query()
                ->whereHas('farms', function($query) use ($value) {
                    $query->where('farms.id', $value);
                })
                ->get();
        }
        
        if ($this->inventoryItems->count() === 1) {
            $this->inventoryItemId = (string)$this->inventoryItems->first()->id;
        }
    }
    
    public function submit(): void
    {
        $this->validate();
        
        $itemRequest = new ItemRequest([
            'user_id' => Auth::id(),
            'farm_id' => (int)$this->farmId,
            'inventory_item_id' => (int)$this->inventoryItemId,
            'quantity' => (float)$this->quantity,
            'notes' => $this->notes,
            'requested_at' => now(),
            'status' => ItemRequest::STATUS_PENDING,
        ]);
        
        $itemRequest->save();
        
        // Log the initial status
        $itemRequest->statuses()->create([
            'status' => ItemRequest::STATUS_PENDING,
            'changed_by' => Auth::id(),
            'notes' => 'Request submitted',
        ]);
        
        session()->flash('message', 'Item request submitted successfully!');
        
        // Reset form
        $this->reset(['quantity', 'notes']);
        $this->loadFarms();
    }
    
    public function render()
    {
        return view('livewire.item-request-form');
    }
}
