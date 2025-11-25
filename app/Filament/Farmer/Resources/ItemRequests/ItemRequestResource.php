<?php

namespace App\Filament\Farmer\Resources\ItemRequests;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use App\Models\ItemRequest;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Farmer\Resources\ItemRequests\Pages\EditItemRequest;
use App\Filament\Farmer\Resources\ItemRequests\Pages\ViewItemRequest;
use App\Filament\Farmer\Resources\ItemRequests\Pages\ListItemRequests;
use App\Filament\Farmer\Resources\ItemRequests\Pages\CreateItemRequest;
use App\Filament\Farmer\Resources\ItemRequests\Schemas\ItemRequestForm;
use App\Filament\Farmer\Resources\ItemRequests\Tables\ItemRequestsTable;
use App\Filament\Farmer\Resources\ItemRequests\Schemas\ItemRequestInfolist;
use App\Filament\Farmer\Resources\ItemRequests\RelationManagers\MessagesRelationManager;

class ItemRequestResource extends Resource
{
    protected static ?string $model = ItemRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'item.name';

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
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItemRequests::route('/'),
            'create' => CreateItemRequest::route('/create'),
            'view' => ViewItemRequest::route('/{record}'),
            // 'edit' => EditItemRequest::route('/{record}/edit'),
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
