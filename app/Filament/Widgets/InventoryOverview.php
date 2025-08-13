<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use App\Models\PurchaseRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class InventoryOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalItems = InventoryItem::count();
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')->count();
        $expiredItems = InventoryItem::where('expiration_date', '<', now())->count();
        $totalValue = InventoryItem::sum(DB::raw('current_stock * unit_cost'));
        $pendingRequests = PurchaseRequest::where('status', 'pending')->count();
        $approvedRequests = PurchaseRequest::where('status', 'approved')->count();

        return [
            Stat::make('Total Inventory Items', $totalItems)
                ->description('Items in stock')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Low Stock Items', $lowStockItems)
                ->description('Items below minimum stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Expired Items', $expiredItems)
                ->description('Items past expiration date')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make('Total Inventory Value', '₱' . number_format($totalValue, 2))
                ->description('Current stock value')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Pending Purchase Requests', $pendingRequests)
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Approved Purchase Requests', $approvedRequests)
                ->description('Ready for procurement')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
