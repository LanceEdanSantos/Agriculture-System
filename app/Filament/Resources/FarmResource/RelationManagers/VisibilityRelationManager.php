<?php

namespace App\Filament\Resources\FarmResource\RelationManagers;

use App\Models\Category;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class VisibilityRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    public function table(Table $table): Table
    {
        return $table
            ->columns([]) // no default columns
            ->content(function () {
                $farm = $this->getOwnerRecord();
                $categories = Category::with('inventoryItems')->get();

                return view(
                    'filament.resources.farm-resource.relation-managers.visibility-matrix',
                    [
                        'farm' => $farm,
                        'categories' => $categories,
                    ]
                );
            })
            ->emptyState(function () {
                $farm = $this->getOwnerRecord();
                $categories = Category::with('inventoryItems')->get();

                return view(
                    'filament.resources.farm-resource.relation-managers.visibility-matrix',
                    [
                        'farm' => $farm,
                        'categories' => $categories,
                    ]
                );
            });
    }

    public function syncCategory(int $categoryId, bool $visible): void
    {
        $this->getOwnerRecord()
            ->categories()
            ->syncWithoutDetaching([$categoryId => ['is_visible' => $visible]]);
    }

    public function syncItem(int $itemId, bool $visible): void
    {
        $this->getOwnerRecord()
            ->inventoryItems()
            ->syncWithoutDetaching([$itemId => ['is_visible' => $visible]]);
    }
}
