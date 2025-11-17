<?php

namespace App\Filament\Farmer\Resources\ItemRequests\Pages;

use App\Filament\Farmer\Resources\ItemRequests\ItemRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemRequests extends ListRecords
{
    protected static string $resource = ItemRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
