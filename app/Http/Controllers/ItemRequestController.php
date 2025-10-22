<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\ItemRequest;
use App\Models\ItemRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd(auth()->user()->getAllPermissions()->pluck('name'));
        $this->authorize('viewAny', ItemRequest::class);

        return redirect()->route('item-requests.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', ItemRequest::class);

        $farms = \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id()))->with(['inventoryItems' => function($query) {
            $query->where('status', 'active');
        }])->get();

        return view('item-requests.create', [
            'farms' => $farms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ItemRequest::class);
        
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            
            $itemRequest = ItemRequest::create([
                'user_id' => Auth::id(),
                'farm_id' => $validated['farm_id'],
                'inventory_item_id' => $validated['inventory_item_id'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
                'requested_at' => now(),
                'status' => 'pending',
            ]);

            // Record the initial status
            $itemRequest->statuses()->create([
                'status' => 'pending',
                'changed_by' => Auth::id(),
                'notes' => 'Request created',
            ]);

            DB::commit();

            return redirect()->route('item-requests.show', $itemRequest)
                ->with('success', 'Item request created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create item request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemRequest $itemRequest)
    {
        $this->authorize('view', $itemRequest);
        
        $itemRequest->load([
            'farm',
            'inventoryItem',
            'user',
            'statuses' => function($query) {
                $query->latest();
            },
            'statuses.changedBy',
            'feedback'
        ]);

        return view('item-requests.show', [
            'itemRequest' => $itemRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemRequest $itemRequest)
    {
        $this->authorize('update', $itemRequest);

        if ($itemRequest->status !== 'pending') {
            return redirect()->route('item-requests.show', $itemRequest)
                ->with('error', 'Only pending requests can be edited.');
        }

        $farms = \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id()))->with(['inventoryItems' => function($query) {
            $query->where('status', 'active');
        }])->get();

        return view('item-requests.edit', [
            'itemRequest' => $itemRequest,
            'farms' => $farms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemRequest $itemRequest)
    {
        $this->authorize('update', $itemRequest);
        
        if ($itemRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be updated.');
        }
        
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            
            $itemRequest->update([
                'farm_id' => $validated['farm_id'],
                'inventory_item_id' => $validated['inventory_item_id'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Record the update in status history
            $itemRequest->statuses()->create([
                'status' => $itemRequest->status,
                'changed_by' => Auth::id(),
                'notes' => 'Request details updated',
            ]);

            DB::commit();

            return redirect()->route('item-requests.show', $itemRequest)
                ->with('success', 'Item request updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update item request: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemRequest $itemRequest)
    {
        $this->authorize('delete', $itemRequest);
        
        if ($itemRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be deleted.');
        }
        
        try {
            DB::beginTransaction();
            
            // Record the deletion in status history
            $itemRequest->statuses()->create([
                'status' => 'cancelled',
                'changed_by' => Auth::id(),
                'notes' => 'Request deleted by user',
            ]);
            
            // Soft delete the request
            $itemRequest->delete();
            
            DB::commit();
            
            return redirect()->route('item-requests.index')
                ->with('success', 'Item request deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete item request: ' . $e->getMessage());
        }
    }
}
