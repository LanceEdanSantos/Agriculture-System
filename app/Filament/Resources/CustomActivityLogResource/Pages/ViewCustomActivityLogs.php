<?php

namespace App\Filament\Resources\CustomActivityLogResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CustomActivityLogResource;

class ViewCustomActivityLog extends ViewRecord
{
    public static function getResource(): string
    {
        return CustomActivityLogResource::class;
    }
}
