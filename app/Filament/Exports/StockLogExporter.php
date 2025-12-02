<?php

namespace App\Filament\Exports;

use App\Models\StockLog;
use Illuminate\Support\Number;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class StockLogExporter extends Exporter
{
    protected static ?string $model = StockLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.first_name')
                ->label('First name'),
            ExportColumn::make('user.middle_name')
                ->label('Middle name'),
            ExportColumn::make('user.last_name')
                ->label('Last name'),
            ExportColumn::make('user.suffix')
                ->label('Suffix'),
            ExportColumn::make('user.email')
                ->label('Email'),
            ExportColumn::make('item.name')
                ->label('Item'),
            ExportColumn::make('quantity')
                ->label('Quantity'),
            ExportColumn::make('created_at')
                ->label('Created At'),

            ExportColumn::make('type')
                ->formatStateUsing(function ($state) {
                    if (is_array($state)) {
                        $state = $state[0] ?? null;
                    }

                    if ($state instanceof \App\Enums\TransferType) {
                        return $state->value;
                    }

                    return $state ?? '';
                })
                ->label('Type'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your stock log export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
