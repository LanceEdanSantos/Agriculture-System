<?php

namespace App\Filament\Resources\Farms\RelationManagers;

use App\Models\User;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Schemas\Components\Grid;
use Filament\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\RelationManagers\RelationManager;

class UsersRelationManager extends RelationManager
{
    // Must match your Farm model's belongsToMany relationship method
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table  
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()
                ->state(function (User $record): string {
                    $name = Str::headline($record['first_name'] . ' ' . $record['middle_name'] . ' ' . $record['last_name'] . ' ' . $record['suffix']);

                    return "{$name}";
                }),
                TextColumn::make('number')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('association')->searchable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add User')
                    ->recordTitle(
                        fn(User $record) =>
                        Str::headline("{$record->first_name} {$record->middle_name} {$record->last_name} {$record->suffix}")
                    )
                    ->recordSelectSearchColumns(['first_name', 'middle_name', 'last_name', 'suffix'])
                    ->preloadRecordSelect() // Preload user list
                    ->multiple(),           // Attach multiple users at once
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->schema([  
                           Grid::make()
                            ->columns(2)
                            ->schema([
                                Section::make('User Details')
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Name')
                                            ->state(function (User $record): string {
                                                $name = Str::headline($record['first_name'] . ' ' . $record['middle_name'] . ' ' . $record['last_name'] . ' ' . $record['suffix']);

                                                return "{$name}";
                                            }),
                                        TextEntry::make('number')
                                            ->label('Contact Number'),
                                        TextEntry::make('email')
                                            ->label('Email'),
                                        TextEntry::make('association')
                                            ->label('Association'),
                                        ]),
                                Section::make('Item Requests')
                                        ->schema([
                                            RepeatableEntry::make('itemRequests')
                                                ->schema([
                                                    Grid::make()
                                                    ->columns(3)
                                                    ->schema([
                                                        TextEntry::make('item.name')
                                                            ->label('Item'),
                                                        TextEntry::make('quantity')
                                                            ->label('Quantity'),
                                                        TextEntry::make('status')
                                                            ->badge()
                                                            ->label('Status'),
                                                    ])
                                                ]   )
                                        ])
                            ])
                        ]),
                    DetachAction::make()
                        ->label('Remove User'),
                    
                ]),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),   // Detach multiple users at once
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
