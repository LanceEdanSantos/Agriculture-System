<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemRequest;

class FarmerController extends Controller
{
    public function dashboard()
    {
        return view('farmer.dashboard');
    }
}
