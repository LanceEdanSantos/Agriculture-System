<?php

namespace App\Filament\Resources\CustomActivityLogResource\Pages;

use App\Filament\Resources\CustomActivityLogResource;
use Rmsramos\Activitylog\Resources\ActivitylogResource\Pages\ListActivitylog;

class ListCustomActivityLogs extends ListActivitylog
{
    protected static string $resource = CustomActivityLogResource::class;
}
