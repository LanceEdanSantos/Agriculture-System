<?php

namespace App\Filament\Resources\StockLogs\Pages;

use App\Filament\Resources\StockLogs\StockLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockLogs extends ListRecords
{
    protected static string $resource = StockLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
