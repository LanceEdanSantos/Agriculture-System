<?php

namespace App\Filament\Resources\StockLogs\Pages;

use App\Filament\Resources\StockLogs\StockLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockLog extends ViewRecord
{
    protected static string $resource = StockLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
