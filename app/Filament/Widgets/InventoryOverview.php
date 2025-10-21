<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use App\Models\PurchaseRequest;
use App\Models\Category;
use App\Models\StockMovement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Basic inventory metrics
        $totalItems = InventoryItem::count();
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')->count();
        $outOfStockItems = InventoryItem::where('current_stock', 0)->count();
        $expiredItems = InventoryItem::where('expiration_date', '<', now())->count();

        // Stock level calculations
        $totalStock = InventoryItem::sum('current_stock');
        $avgStockPerItem = $totalItems > 0 ? round($totalStock / $totalItems, 1) : 0;

        // Recent activity
        $recentMovements = StockMovement::where('created_at', '>=', now()->subDays(7))->count();
        $recentlyAdded = InventoryItem::where('created_at', '>=', now()->subDays(7))->count();

        // Category breakdown
        $categoryCount = Category::count();
        $itemsByCategory = DB::table('inventory_items')
            ->join('categories', 'inventory_items.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.name')
            ->pluck('count', 'name');

        $mostStockedCategory = $itemsByCategory->sortDesc()->keys()->first() ?? 'N/A';

        // Request metrics
        $pendingRequests = PurchaseRequest::where('status', 'pending')->count();
        $approvedRequests = PurchaseRequest::where('status', 'approved')->count();

        // Stock health percentage
        $healthyStockItems = InventoryItem::whereRaw('current_stock > minimum_stock')->count();
        $stockHealthPercentage = $totalItems > 0 ? round(($healthyStockItems / $totalItems) * 100, 1) : 0;

        return [
            Stat::make('Total Inventory Items', $totalItems)
                ->description('Items in stock')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Low Stock Items', $lowStockItems)
                ->description('Items below minimum stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockItems > 0 ? 'warning' : 'success'),

            Stat::make('Out of Stock Items', $outOfStockItems)
                ->description('Items with zero stock')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($outOfStockItems > 0 ? 'danger' : 'success'),

            Stat::make('Stock Health', $stockHealthPercentage . '%')
                ->description('Items above minimum stock')
                ->descriptionIcon('heroicon-m-heart')
                ->color($stockHealthPercentage >= 80 ? 'success' : ($stockHealthPercentage >= 60 ? 'warning' : 'danger')),

            Stat::make('Average Stock Level', $avgStockPerItem)
                ->description('Average units per item')
                ->descriptionIcon('heroicon-m-scale')
                ->color('info'),

            Stat::make('Recent Activity (7 days)', $recentMovements)
                ->description('Stock movements recorded')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary'),

            Stat::make('Recently Added Items', $recentlyAdded)
                ->description('New items this week')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success'),

            Stat::make('Categories', $categoryCount)
                ->description('Most stocked: ' . $mostStockedCategory)
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            // Stat::make('Pending Requests', $pendingRequests)
            //     ->description('Awaiting approval')
            //     ->descriptionIcon('heroicon-m-clock')
            //     ->color($pendingRequests > 0 ? 'warning' : 'success'),

            // Stat::make('Approved Requests', $approvedRequests)
            //     ->description('Ready for procurement')
            //     ->descriptionIcon('heroicon-m-check-circle')
            //     ->color('success'),
        ];
    }
}
