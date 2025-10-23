<?php

namespace App\Livewire;

use App\Models\ItemRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ItemRequestList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public int $perPage = 10;
    public string $sortField = 'requested_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'requested_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /**
     * Reset pagination when search term changes
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle sort direction for a field
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        // Get user's farm IDs for privacy filtering
        $farmIds = DB::table('farm_user')
            ->where('user_id', Auth::id())
            ->pluck('farm_id');

        $query = ItemRequest::query()
            ->with(['farm:id,name', 'inventoryItem:id,name,unit'])
            ->whereIn('farm_id', $farmIds)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('inventoryItem', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('farm', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.item-request-list', [
            'requests' => $query->paginate($this->perPage),
            'statuses' => $this->getStatuses(),
        ]);
    }

    /**
     * Get the available status options
     */
    protected function getStatuses(): array
    {
        return [
            '' => 'All Statuses',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'in_progress' => 'In Progress',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
    }
}
