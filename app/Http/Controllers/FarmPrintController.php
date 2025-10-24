<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;

class FarmPrintController extends Controller
{
    public function print(Request $request, $id)
    {
        // Get the specific farm with relationships
        $farm = Farm::with(['users', 'categories', 'inventoryItems'])->findOrFail($id);

        // Return a simple HTML view for printing
        return response()->view('farms.print', [
            'farm' => $farm,
        ]);
    }
}
