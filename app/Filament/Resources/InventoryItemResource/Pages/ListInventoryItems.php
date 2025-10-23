<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Actions\ImportInventoryAction;
use App\Filament\Resources\InventoryItemResource;
use App\Filament\Resources\StockMovementResource;

class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('goToCreate')
                ->label('Create Stock Movement')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(StockMovementResource::getUrl('create')),
            Actions\CreateAction::make(),
        ];
    }
}
