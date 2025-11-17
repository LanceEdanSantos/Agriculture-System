<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\Categories\Schemas\CategoryForm;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'xl' => 2,
                    ])
                    ->schema([
                        Section::make('Basic Information')
                            ->description('Name and basic details of the item')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->rules([
                                        'string',
                                        'max:255',
                                    ]),
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Grid::make()
                                            ->columns([
                                                'sm' => 1,
                                                'md' => 2,
                                                'xl' => 2,
                                            ])->schema([
                                                Section::make('Name')
                                                    ->description('Name and description of your category')
                                                    ->components([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->rules([
                                                                'string',
                                                                'max:255',
                                                            ]),
                                                        RichEditor::make('description')
                                                        ->columnSpanFull(),
                                                    ]),
                                                Section::make('Active')
                                                    ->description('This will show if this category should be used or not')
                                                    ->schema([
                                                        Toggle::make('active')
                                                            ->default(true),
                                                    ])
                                            ])->columnSpanFull(),
                                    ]),
                                RichEditor::make('description')
                                    ->columnSpanFull(),
                            ]),
                        
                        Section::make('Inventory')
                            ->description('Stock and inventory settings')
                            ->schema([
                                TextInput::make('stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('minimum_stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('active')
                                    ->default(true)
                                    ->required(),
                            ]),
                        
                        Section::make('Additional Information')
                            ->description('Extra details and notes')
                            ->schema([
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
