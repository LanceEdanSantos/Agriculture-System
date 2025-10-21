<?php

use App\Http\Controllers\InventoryPrintController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/inventory/print', [InventoryPrintController::class, 'print'])
    ->name('inventory.print')
    ->middleware(['auth']);

// Authentication routes
require __DIR__.'/auth.php';

// Farmer routes
require __DIR__.'/farmer.php';

// Add other role-based route files here when needed
