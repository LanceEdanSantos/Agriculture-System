<?php

namespace App\Filament\Exports;

use App\Models\Farm;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class FarmExporter extends Exporter
{
    protected static ?string $model = Farm::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Farm Name'),
            ExportColumn::make('description')->label('Description')->limit(200),
            ExportColumn::make('is_active')
                ->label('Status')
                ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),
            ExportColumn::make('users_count')
                ->label('Total Users')
                ->state(fn(Farm $record) => $record->users()->count()),
            ExportColumn::make('created_at')->label('Date Created'),
            ExportColumn::make('updated_at')->label('Last Updated'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your farm export completed with ' . number_format($export->successful_rows) . ' '
            . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failed = $export->getFailedRowsCount()) {
            $body .= " ⚠️ {$failed} " . str('row')->plural($failed) . ' failed to export.';
        }

        return $body;
    }
}
