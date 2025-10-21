<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ItemRequestComponent;
use App\Http\Controllers\ItemRequestController;

// All routes in this file are protected by 'auth' and 'role:farmer' middleware

Route::get('/', function () {
    return redirect()->route('item-requests.index');
})->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Item Requests
    Route::get('/item-requests', ItemRequestComponent::class)->name('item-requests.index');
    Route::get('/item-requests/create', ItemRequestComponent::class, ['mode' => 'create'])->name('item-requests.create');
    Route::get('/item-requests/{id}', ItemRequestComponent::class, ['mode' => 'show'])->name('item-requests.show');
    Route::get('/item-requests/{id}/edit', ItemRequestComponent::class, ['mode' => 'edit'])->name('item-requests.edit');
});
