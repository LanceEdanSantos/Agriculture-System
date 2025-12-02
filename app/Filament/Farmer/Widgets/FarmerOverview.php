<?php

namespace App\Filament\Farmer\Widgets;

use App\Models\ItemRequest;
use App\Enums\ItemRequestStatus;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FarmerOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $farms = $user?->farms ?? collect();
        $farmIds = $farms->pluck('id');

        // Safe: if no farms → empty array, still works
        $query = ItemRequest::whereIn('farm_id', $farmIds);

        $pending     = (clone $query)->where('status', ItemRequestStatus::PENDING)->where('user_id', $user->id)->count();
        $approved    = (clone $query)->where('status', ItemRequestStatus::APPROVED)->where('user_id', $user->id)->count();
        $rejected    = (clone $query)->where('status', ItemRequestStatus::REJECTED)->where('user_id', $user->id)->count();

        return [
            Stat::make('Pending', $pending)
                ->description('Awaiting approval')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color(ItemRequestStatus::PENDING->getColor()),

            Stat::make('Approved', $approved)
                ->description('Confirmed requests')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color(ItemRequestStatus::APPROVED->getColor()),

            Stat::make('Rejected', $rejected)
                ->description('Denied requests')
                ->descriptionIcon(Heroicon::OutlinedXCircle)
                ->color(ItemRequestStatus::REJECTED->getColor()),
        ];
    }
}
