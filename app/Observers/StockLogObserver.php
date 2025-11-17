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
        //
    }

    /**
     * Handle the StockLog "deleted" event.
     */
    public function deleted(StockLog $stockLog): void
    {
        //
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
