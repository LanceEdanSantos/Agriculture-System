<?php

namespace App\Filament\Farmer\Resources\ItemRequests;

use App\Filament\Farmer\Resources\ItemRequests\Pages\CreateItemRequest;
use App\Filament\Farmer\Resources\ItemRequests\Pages\EditItemRequest;
use App\Filament\Farmer\Resources\ItemRequests\Pages\ListItemRequests;
use App\Filament\Farmer\Resources\ItemRequests\Pages\ViewItemRequest;
use App\Filament\Farmer\Resources\ItemRequests\Schemas\ItemRequestForm;
use App\Filament\Farmer\Resources\ItemRequests\Schemas\ItemRequestInfolist;
use App\Filament\Farmer\Resources\ItemRequests\Tables\ItemRequestsTable;
use App\Models\ItemRequest;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemRequestResource extends Resource
{
    protected static ?string $model = ItemRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    public static function form(Schema $schema): Schema
    {
        return ItemRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItemRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemRequestsTable::configure($table);
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
            'index' => ListItemRequests::route('/'),
            'create' => CreateItemRequest::route('/create'),
            'view' => ViewItemRequest::route('/{record}'),
            'edit' => EditItemRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
