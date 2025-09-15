<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class TestController extends Controller
{

    public function getCategory(){
        Category::with('purchase_request_items')->get()->all();
    }
}
