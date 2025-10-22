<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryItem extends CreateRecord
{
    protected static string $resource = InventoryItemResource::class;
    protected function getRedirectUrl(): string
    {
        // This makes it go back to the table page after creating a record
        return $this->getResource()::getUrl('index');
    }
}
