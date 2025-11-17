<?php

namespace App\Filament\Farmer\Resources\ItemRequests\Pages;

use App\Filament\Farmer\Resources\ItemRequests\ItemRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateItemRequest extends CreateRecord
{
    protected static string $resource = ItemRequestResource::class;
}
