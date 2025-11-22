<?php

namespace App\Filament\Resources\StockLogs;

use UnitEnum;
use BackedEnum;
use App\Models\StockLog;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StockLogs\Pages\EditStockLog;
use App\Filament\Resources\StockLogs\Pages\ViewStockLog;
use App\Filament\Resources\StockLogs\Pages\ListStockLogs;
use App\Filament\Resources\StockLogs\Pages\CreateStockLog;
use App\Filament\Resources\StockLogs\Schemas\StockLogForm;
use App\Filament\Resources\StockLogs\Tables\StockLogsTable;
use App\Filament\Resources\StockLogs\Schemas\StockLogInfolist;

class StockLogResource extends Resource
{
    protected static ?string $model = StockLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocument;


    protected static ?string $recordTitleAttribute = 'id';

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'History Log';

    public static function form(Schema $schema): Schema
    {
        return StockLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockLogs::route('/'),
            'create' => CreateStockLog::route('/create'),
            'view' => ViewStockLog::route('/{record}'),
            'edit' => EditStockLog::route('/{record}/edit'),
        ];
    }
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
