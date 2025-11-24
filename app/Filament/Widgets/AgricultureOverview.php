<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Category;
use App\Models\Farm;
use App\Models\ItemRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgricultureOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Basic item metrics
        $totalItems = Item::count();
        $activeItems = Item::where('active', true)->count();
        $inactiveItems = $totalItems - $activeItems;

        // Stock level calculations
        $lowStockItems = Item::whereColumn('stock', '<=', 'minimum_stock')->count();
        $outOfStockItems = Item::where('stock', 0)->count();

        // Category breakdown
        $categoryCount = Category::count();
        $itemsByCategory = DB::table('items')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.name')
            ->pluck('count', 'name');

        $mostStockedCategory = $itemsByCategory->sortDesc()->keys()->first() ?? 'N/A';

        // Farm metrics
        $farmCount = Farm::count();

        // Item request metrics
        $pendingRequests = ItemRequest::where('status', 'pending')->count();
        $approvedRequests = ItemRequest::where('status', 'approved')->count();
        $recentRequests = ItemRequest::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Total Items', $totalItems)
                ->description('Items in inventory')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Active Items', $activeItems)
                ->description('Currently available items')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Low Stock Items', $lowStockItems)
                ->description('Items below minimum stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockItems > 0 ? 'warning' : 'success'),

            Stat::make('Out of Stock', $outOfStockItems)
                ->description('Items with zero stock')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($outOfStockItems > 0 ? 'danger' : 'success'),

            Stat::make('Categories', $categoryCount)
                ->description('Most items in: ' . $mostStockedCategory)
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make('Farmers', $farmCount)
                ->description('Total registered farmers')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Stat::make('Pending Requests', $pendingRequests)
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingRequests > 0 ? 'warning' : 'success'),

            Stat::make('Recent Requests (7d)', $recentRequests)
                ->description('New item requests')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary'),
        ];
    }
}
