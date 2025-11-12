<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Role;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('fname')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('mname')
                                    ->label('Middle Name')
                                    ->maxLength(255),
                                TextInput::make('lname')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('suffix')
                                    ->label('Suffix')
                                    ->maxLength(10),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('position')
                                    ->label('Position')
                                    ->maxLength(255),
                            ]),
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3),
                    ]),

                Section::make('Role & Department')
                    ->schema([
                        Grid::make(2)   
                            ->schema([
                                Select::make('roles')
                                    ->label('Roles')
                                    ->multiple()
                                    ->relationship('roles', 'name') // ← this is how Shield expects it
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                TextInput::make('department')
                                    ->label('Department')
                                    ->maxLength(255),
                            ]),
                        // CheckboxList::make('roles')
                        //     ->label('Shield Roles')
                        //     ->relationship('roles', 'name')
                        //     ->columns(3),
                        // CheckboxList::make('permissions')
                        //     ->label('Shield Permissions')
                        //     ->relationship('permissions', 'name')
                        //     ->columns(3),
                    ]),

                Section::make('Security')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $context): bool => $context === 'create'),
                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->dehydrated(false)
                                    ->required(fn(string $context): bool => $context === 'create'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => 
                        trim(collect([
                            $record->fname,
                            $record->mname,
                            $record->lname,
                            $record->suffix,
                        ])->filter()->implode(' '))
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('fname', 'like', "%{$search}%")
                            ->orWhere('mname', 'like', "%{$search}%")
                            ->orWhere('lname', 'like', "%{$search}%")
                            ->orWhere('suffix', 'like', "%{$search}%");
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('lname', $direction)
                                   ->orderBy('fname', $direction);
                    }),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles')
                    ->label('Roles')
                    ->getStateUsing(fn($record) => $record->roles->pluck('name')->join(', '))
                    ->sortable(),
                TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Position')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y h:i A', 'Asia/Manila')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(Role::pluck('name', 'id')->toArray())
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('roles', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    }),
                SelectFilter::make('department')
                    ->label('Department')
                    ->options(fn () => User::select('department')
                        ->distinct()
                        ->whereNotNull('department')
                        ->orderBy('department')
                        ->pluck('department', 'department'))
                    ->searchable()
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    ActivityLogTimelineTableAction::make('Activities')
                        ->timelineIcons([
                            'created' => 'heroicon-m-check-badge',
                        'updated' => 'heroicon-m-pencil-square',
                        'deleted' => 'heroicon-m-trash',
                    ])
                    ->timelineIconColors([
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                    ])
                    ->limit(20),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
