<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('slug')
                //     ->required(),
                Grid::make()
                    ->columns(  [
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
                                    ->default(true  ),
                            ])
                ])->columnSpanFull(),
                
            ]);
    }
}
