<?php

namespace App\Filament\Resources;

use Filament\Tables\Table;
use Pages\ListCustomActivityLogs;
use Rmsramos\Activitylog\Resources\ActivitylogResource;
use App\Filament\Resources\CustomActivityLogResource\Pages;

class CustomActivityLogResource extends ActivitylogResource
{
    protected static ?string $navigationLabel = 'Activity Logs';
    protected static ?string $pluralLabel = 'Activity Logs';
    protected static ?string $model = \Spatie\Activitylog\Models\Activity::class;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomActivityLogs::route('/'),
        ];
    }

    public static function table(Table $table): Table
    {
        // Start with the parent's table
        $table = parent::table($table);
        // Filter out the subject_type column
        $filteredColumns = collect($table->getColumns())
            ->reject(fn($column) => $column->getName() === 'log_name')
            ->values()
            ->all();

        return $table->columns($filteredColumns);
    }
}
