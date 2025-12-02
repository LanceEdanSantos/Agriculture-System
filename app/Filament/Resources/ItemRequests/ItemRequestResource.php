<?php

namespace App\Filament\Resources\ItemRequests;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use App\Models\ItemRequest;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemRequests\Pages\EditItemRequest;
use App\Filament\Resources\ItemRequests\Pages\ViewItemRequest;
use App\Filament\Resources\ItemRequests\Pages\ListItemRequests;
use App\Filament\Resources\ItemRequests\Pages\CreateItemRequest;
use App\Filament\Resources\ItemRequests\Schemas\ItemRequestForm;
use App\Filament\Resources\ItemRequests\Tables\ItemRequestsTable;
use App\Filament\Resources\ItemRequests\Schemas\ItemRequestInfolist;
use App\Filament\Resources\ItemRequests\RelationManagers\MessagesRelationManager;

class ItemRequestResource extends Resource
{
    protected static ?string $model = ItemRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxArrowDown;

    protected static ?string $recordTitleAttribute = 'item_name';

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

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
            'edit' => EditItemRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('created_at', 'desc');
    }
    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->id;
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'quantity', 'status', 'farm_id', 'notes'];
    }

    public static function getGlobalSearchResultsQuery(string $search): Builder
    {
        return parent::getGlobalSearchResultsQuery($search)
            ->orWhereHas('item', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
    }
}
