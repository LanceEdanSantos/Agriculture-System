<?php

namespace App\Observers;

use App\Models\StockLog;

class StockLogObserver
{
    /**
     * Handle the StockLog "created" event.
     */
    public function created(StockLog $log): void
    {
        $item = $log->item;

        $change = $log->type === 'in'
            ? $log->quantity
            : -$log->quantity;

        // Apply change but prevent negative
        // $item->stock = max(0, $item->stock + $change);
        $item->stock = $item->stock + $change;

        $item->save();
    }

    /**
     * Handle the StockLog "updated" event.
     */
    public function updated(StockLog $stockLog): void
    {
        $item = $stockLog->item;

        // Get original values
        $originalType = $stockLog->getOriginal('type');
        $originalQuantity = $stockLog->getOriginal('quantity');

        // Reverse the original change
        $reverseChange = $originalType === 'in'
            ? -$originalQuantity
            : $originalQuantity;

        // Apply new change
        $newChange = $stockLog->type === 'in'
            ? $stockLog->quantity
            : -$stockLog->quantity;

        $item->stock = $item->stock + $reverseChange + $newChange;
        $item->save();
    }

    /**
     * Handle the StockLog "deleted" event.
     */
    public function deleted(StockLog $stockLog): void
    {
        $item = $stockLog->item;

        // Reverse the change when deleted
        $change = $stockLog->type === 'in'
            ? -$stockLog->quantity
            : $stockLog->quantity;

        $item->stock = $item->stock + $change;
        $item->save();
    }

    /**
     * Handle the StockLog "restored" event.
     */
    public function restored(StockLog $stockLog): void
    {
        //
    }

    /**
     * Handle the StockLog "force deleted" event.
     */
    public function forceDeleted(StockLog $stockLog): void
    {
        //
    }
}
