<?php

namespace App\Filament\Farmer\Resources\ItemRequests\Pages;

use App\Filament\Farmer\Resources\ItemRequests\ItemRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewItemRequest extends ViewRecord
{
    protected static string $resource = ItemRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
