<?php

namespace App\Filament\Resources;

use Filament\Tables\Table;
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
            'view' => Pages\ViewCustomActivityLog::route('/{record}'),
        ];
    }

    public static function table(Table $table): Table
    {
        // Start with the parent's table
        $table = parent::table($table);

        // Filter out only the 'log_name' column
        $columns = collect($table->getColumns())
            ->reject(fn($column) => $column->getName() === 'log_name')
            ->all();

        // Re-attach the filtered columns while keeping actions intact
        return $table
            ->columns($columns)
            ->filters($table->getFilters())
            ->actions($table->getActions())
            ->bulkActions($table->getBulkActions());
    }
}
