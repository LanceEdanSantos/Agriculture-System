<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Actions\ImportInventoryAction;

class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ImportInventoryAction::make(),
            Actions\CreateAction::make(),
        ];
    }
}
