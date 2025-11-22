<?php

namespace App\Filament\Resources\Farms;

use BackedEnum;
use App\Models\Farm;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Farms\Pages\EditFarm;
use App\Filament\Resources\Farms\Pages\ViewFarm;
use App\Filament\Resources\Farms\Pages\ListFarms;
use App\Filament\Resources\Farms\Pages\CreateFarm;
use App\Filament\Resources\Farms\Schemas\FarmForm;
use App\Filament\Resources\Farms\Tables\FarmsTable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Farms\Schemas\FarmInfolist;
use App\Filament\Resources\Farms\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Farms\RelationManagers\UsersRelationManager;
use UnitEnum;

class FarmResource extends Resource
{
    protected static ?string $model = Farm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    // protected static string|UnitEnum|null $navigationGroup = 'Farms';

    protected static ?string $navigationLabel = 'Farmers';


    public static function form(Schema $schema): Schema
    {
        return FarmForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FarmInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
            ItemsRelationManager::class,
        ];
    }   

    public static function getPages(): array
    {
        return [
            'index' => ListFarms::route('/'),
            'create' => CreateFarm::route('/create'),
            'view' => ViewFarm::route('/{record}'),
            'edit' => EditFarm::route('/{record}/edit'),
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
