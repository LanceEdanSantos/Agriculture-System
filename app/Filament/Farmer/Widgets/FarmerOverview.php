<?php

namespace App\Filament\Farmer\Widgets;

use App\Models\Farm;
use App\Models\ItemRequest;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FarmerOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        $farms = $user->farms;

        if ($farms->count() === 1) {
    $farm = $farms->first();

    // ItemRequest stats
    $totalRequests = ItemRequest::where('farm_id', $farm->id)->count();
    $pendingRequests = ItemRequest::where('farm_id', $farm->id)
        ->where('status', 'pending')
        ->count();
    $approvedRequests = ItemRequest::where('farm_id', $farm->id)
        ->where('status', 'approved')
        ->count();

    // Item stock stats
    $totalItems = $farm->items()->count();
    $lowStockItems = $farm->items()->whereColumn('stock', '<=', 'minimum_stock')->count();
    $outOfStockItems = $farm->items()->where('stock', 0)->count();

    // Recent activity
    $recentRequests = ItemRequest::where('farm_id', $farm->id)
        ->where('created_at', '>=', now()->subDays(7))
        ->count();

    return [
        Stat::make("Requests for {$farm->name}", $totalRequests)
            ->description('Total item requests')
            ->descriptionIcon(Heroicon::OutlinedArchiveBox)
            ->color('primary'),

        Stat::make('Pending Requests', $pendingRequests)
            ->description('Awaiting approval')
            ->descriptionIcon(Heroicon::OutlinedClock)
            ->color($pendingRequests > 0 ? 'warning' : 'success'),

        Stat::make('Approved Requests', $approvedRequests)
            ->description('Approved by admin')
            ->descriptionIcon(Heroicon::OutlinedCheckBadge)
            ->color('success'),

        Stat::make('Total Items', $totalItems)
            ->description('Items assigned to this farm')
            ->descriptionIcon(Heroicon::OutlinedCube)
            ->color('primary'),
    ];
}

        // Multiple farms
        $totalRequests = ItemRequest::whereIn('farm_id', $farms->pluck('id'))->count();
        $pendingRequests = ItemRequest::whereIn('farm_id', $farms->pluck('id'))
            ->where('status', 'pending')
            ->count();
        $approvedRequests = ItemRequest::whereIn('farm_id', $farms->pluck('id'))
            ->where('status', 'approved')
            ->count();
        $farmCount = $farms->count();

        return [
            Stat::make('Total Farms', $farmCount)
                ->description('Farms you are assigned to')
                ->descriptionIcon(Heroicon::OutlinedBuildingLibrary)
                ->color('primary'),

            Stat::make('Total Requests', $totalRequests)
                ->description('All item requests for your farms')
                ->descriptionIcon(Heroicon::OutlinedClipboardDocumentCheck)
                ->color('primary'),

            Stat::make('Pending Requests', $pendingRequests)
                ->description('Awaiting approval')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($pendingRequests > 0 ? 'warning' : 'success'),

            Stat::make('Approved Requests', $approvedRequests)
                ->description('Approved by admin')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
        ];
    }
}
