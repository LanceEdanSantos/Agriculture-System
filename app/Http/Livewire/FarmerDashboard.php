<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Enums\ItemRequestStatus;
use Illuminate\Support\Facades\Auth;

class FarmerDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $showRequestForm = false;
    public $selectedItem = null;
    public $quantity = 1;
    public $notes = '';
    public $farmId;

    protected $queryString = ['search' => ['except' => '']];

    public function mount()
    {
        $this->farmId = Auth::user()->farm_id; // Assuming users have a farm_id
    }

    public function showRequestForm($itemId)
    {
        $this->selectedItem = Item::findOrFail($itemId);
        $this->showRequestForm = true;
    }

    public function submitRequest()
    {
        $this->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        ItemRequest::create([
            'user_id' => auth()->user()->id,
            'item_id' => $this->selectedItem->id,
            'quantity' => $this->quantity,
            'status' => ItemRequestStatus::PENDING->value,
            'farm_id' => $this->farmId,
            'notes' => $this->notes,
        ]);

        $this->reset(['quantity', 'notes', 'showRequestForm', 'selectedItem']);
        session()->flash('message', 'Item request submitted successfully!');
    }

    public function render()
    {
        $items = Item::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->where('active', true)
            ->orderBy('name')
            ->paginate(12);

        $requests = ItemRequest::with('item')
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.farmer-dashboard', [
            'items' => $items,
            'requests' => $requests,
        ]);
    }
}
