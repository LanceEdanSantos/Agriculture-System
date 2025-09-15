<?php

namespace App\Filament\Resources\FarmResource\RelationManagers;

use App\Models\Category;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class VisibilityRelationManager extends RelationManager
{
    protected static string $relationship = 'categories'; // dummy relation, UI is overridden

    public function table(Table $table): Table
    {
        return $table
            ->content(function () {
                $farm = $this->getOwnerRecord();
                $categories = Category::with('inventoryItems')->get();

                return view('filament.resources.farm-resource.relation-managers.visibility-matrix', [
                    'farm' => $farm,
                    'categories' => $categories,
                ]);
            });
    }
    public function syncCategory($categoryId, $visible)
    {
        $this->getOwnerRecord()
            ->categories()
            ->syncWithoutDetaching([
                $categoryId => ['is_visible' => $visible],
            ]);
    }

    public function syncItem($itemId, $visible)
    {
        $this->getOwnerRecord()
            ->inventoryItems()
            ->syncWithoutDetaching([
                $itemId => ['is_visible' => $visible],
            ]);
    }
}
