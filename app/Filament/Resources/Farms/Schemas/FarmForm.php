<?php

namespace App\Filament\Resources\Farms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class FarmForm
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
                        Section::make('Farm Information')
                            ->description('Basic details about the farm')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                // TextInput::make('slug')
                                //     ->unique(ignoreRecord: true)
                                //     ->maxLength(255)
                                //     ->disabledOn('edit'),
                            ]),

                        Section::make('Status')
                            ->description('Farm status and visibility')
                            ->schema([
                                Toggle::make('active')
                                    ->default(true)
                                    ->required(),
                            ]),

                        Section::make('Description')
                            ->description('Detailed information about the farm')
                            ->schema([
                                RichEditor::make('description')
                                    ->columnSpanFull()
                                    ->maxLength(65535),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
