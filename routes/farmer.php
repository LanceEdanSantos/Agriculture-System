<?php

use App\Http\Controllers\FarmerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/farmer/dashboard', [FarmerController::class, 'dashboard'])->name('farmer.dashboard');
    // Add more farmer-specific routes here
});
