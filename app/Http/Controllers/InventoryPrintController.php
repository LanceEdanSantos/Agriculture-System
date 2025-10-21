<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryPrintController extends Controller
{
    public function print(Request $request)
    {
        // Get filter parameters from the request
        $query = InventoryItem::query();

        // Apply filters based on request parameters
        if ($request->has('table')) {
            $tableParams = $request->get('table');

            // Category filter
            if (isset($tableParams['category_id']) && $tableParams['category_id']) {
                $query->where('category_id', $tableParams['category_id']);
            }

            // Supplier filter
            if (isset($tableParams['supplier_id']) && $tableParams['supplier_id']) {
                $query->where('supplier_id', $tableParams['supplier_id']);
            }

            // Unit filter
            if (isset($tableParams['unit_id']) && $tableParams['unit_id']) {
                $query->where('unit_id', $tableParams['unit_id']);
            }

            // Low stock filter
            if (isset($tableParams['low_stock']) && $tableParams['low_stock']) {
                $query->whereRaw('current_stock <= minimum_stock');
            }

            // Deleted items filter
            if (isset($tableParams['deleted']) && $tableParams['deleted']) {
                $query->onlyTrashed();
            }
        }

        // Get the filtered items
        $items = $query->with(['category', 'supplier', 'unit'])->get();

        // Return a simple HTML view for printing
        return response()->view('inventory.print', [
            'items' => $items,
            'filters' => $request->get('table', [])
        ]);
    }
}
