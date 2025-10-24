<?php

use App\Http\Controllers\FarmPrintController;
use App\Http\Controllers\InventoryPrintController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/inventory/print', [InventoryPrintController::class, 'print'])
    ->name('inventory.print')
    ->middleware(['auth']);

Route::get('/farms/{id}/print', [FarmPrintController::class, 'print'])
    ->name('farms.print')
    ->middleware(['auth']);

Route::get('/dashboard', function () {
    return redirect()->route('item-requests.index');
})->name('dashboard')->middleware('auth');

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});
// Authentication routes
require __DIR__.'/auth.php';

// Farmer routes
require __DIR__.'/farmer.php';

// Add other role-based route files here when needed
