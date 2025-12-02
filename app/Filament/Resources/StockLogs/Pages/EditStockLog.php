<?php

namespace App\Filament\Resources\StockLogs\Pages;

use App\Filament\Resources\StockLogs\StockLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStockLog extends EditRecord
{
    protected static string $resource = StockLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
