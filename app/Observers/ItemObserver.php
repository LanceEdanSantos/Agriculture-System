<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\User;
use Filament\Notifications\Notification;

class ItemObserver
{
    public function created(Item $item): void
    {
        $this->checkLowStock($item);
    }

    public function updated(Item $item): void
    {
        $this->checkLowStock($item);
    }

    public function restored(Item $item): void
    {
        $this->checkLowStock($item);
    }

    protected function checkLowStock(Item $item): void
    {
        // Only notify if stock is at or below minimum
        if ($item->stock <= $item->minimum_stock) {
            Notification::make()
                ->title("Low Stock Alert: {$item->name}")
                ->body("This item is low on stock ({$item->stock} remaining, minimum is {$item->minimum_stock})")
                ->danger()
                ->send();
            // Get all users except those with the farmer role
                $recipient = User::whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Farmer');
                })->get();

                // Send Filament database notification
                Notification::make()
                    ->title('Low Stock Alert')
                    ->body("{$item->name} is low on stock ({$item->stock} remaining, minimum is {$item->minimum_stock})")
                    ->sendToDatabase($recipient);
        }
    }

    public function deleted(Item $item): void {}
    public function forceDeleted(Item $item): void {}
}
