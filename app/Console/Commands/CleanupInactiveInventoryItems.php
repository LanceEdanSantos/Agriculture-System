<?php

namespace App\Console\Commands;

use App\Models\InventoryItem;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupInactiveInventoryItems extends Command
{
    protected $signature = 'inventory:cleanup-inactive';
    protected $description = 'Delete inventory items that have been out of stock for 5 years';

    public function handle()
    {
        $fiveYearsAgo = Carbon::now()->subYears(5);

        $itemsToDelete = InventoryItem::where('current_stock', '<=', 0)
            ->where('updated_at', '<', $fiveYearsAgo)
            ->get();

        if ($itemsToDelete->isEmpty()) {
            $this->info('No inactive inventory items found for cleanup.');
            return;
        }

        $deletedCount = 0;
        foreach ($itemsToDelete as $item) {
            $this->line("Deleting inactive item: {$item->name} (Last updated: {$item->updated_at->format('Y-m-d')})");
            $item->delete();
            $deletedCount++;
        }

        $this->info("Cleanup completed. Deleted {$deletedCount} inactive inventory items.");
    }
}
