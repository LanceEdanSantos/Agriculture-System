<?php

namespace App\Filament\Resources\FarmResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // this form is only used by EditAction (on pivot)
                Forms\Components\Select::make('id')
                    ->label('User')
                    ->options(User::pluck('fname', 'id'))
                    ->searchable()
                    ->required()
                    ->preload(),
                Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'manager' => 'Manager',
                        'viewer' => 'Viewer',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->required(),
                Forms\Components\TextInput::make('association')
                    ->datalist([
                        'List',
                    ])
                    ->label('Associations')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->getStateUsing(fn($record) => trim(collect([
                    $record->fname,
                    $record->mname,
                    $record->lname,
                    $record->suffix,
                ])->filter()->implode(' ')))
                ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                    return $query
                        ->where('fname', 'like', "%{$search}%")
                        ->orWhere('mname', 'like', "%{$search}%")
                        ->orWhere('lname', 'like', "%{$search}%")
                        ->orWhere('suffix', 'like', "%{$search}%");
                })
                ->sortable(query: function ($query, string $direction): \Illuminate\Database\Eloquent\Builder {
                    return $query->orderBy('lname', $direction)
                        ->orderBy('fname', $direction);
                }),

            Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form([
                Forms\Components\Select::make('recordId')
                    ->label('User')
                    ->options(User::pluck('fname', 'id'))
                    ->searchable()
                    ->required()
                    ->preload(),
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'manager' => 'Manager',
                                'viewer' => 'Viewer',
                            ])
                            ->required(),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'manager' => 'Manager',
                                'viewer' => 'Viewer',
                            ])
                            ->required(),
                    ]),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
