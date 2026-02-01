<?php

namespace Units\Shield\Manage\Filament;

use BezhanSalleh\FilamentShield\Resources\RoleResource as BaseRoleResource;
use Filament\Forms\Form;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Units\Shield\My\Models\Role;

class RoleResource extends BaseRoleResource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Grid::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make()
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(function (?Model $record): bool {
                                        // Disable name editing for CEO role
                                        return $record && strtolower($record->name) === 'ceo';
                                    }),
                                \Filament\Forms\Components\TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(\BezhanSalleh\FilamentShield\Support\Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255)
                                    ->disabled(function (?Model $record): bool {
                                        // Disable guard editing for CEO role
                                        return $record && strtolower($record->name) === 'ceo';
                                    }),
                                \BezhanSalleh\FilamentShield\Forms\ShieldSelectAllToggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(
                                        fn(): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                                            __('filament-shield::filament-shield.field.select_all.message')
                                        )
                                    )
                                    ->disabled(function (?Model $record): bool {
                                        // Disable select all for CEO role
                                        return $record && strtolower($record->name) === 'ceo';
                                    })
                                    ->dehydrated(fn(bool $state): bool => $state),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                static::getShieldFormComponents()
                    ->disabled(function (?Model $record): bool {
                        // Disable all permission components for CEO role
                        return $record && strtolower($record->name) === 'ceo';
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->weight('font-medium')
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn($state): string => \Illuminate\Support\Str::headline($state))
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning')
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                \Filament\Tables\Columns\TextColumn::make('team.name')
                    ->default('Global')
                    ->badge()
                    ->color(fn(mixed $state): string => str($state)->contains('Global') ? 'gray' : 'primary')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn(): bool => static::shield()->isCentralApp() && \BezhanSalleh\FilamentShield\Support\Utils::isTenancyEnabled()),
                \Filament\Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->colors(['success']),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->visible(function (Model $record): bool {
                        // Prevent editing of CEO roles
                        return strtolower($record->name) !== 'ceo';
                    }),
                DeleteAction::make()
                    ->visible(function (Model $record): bool {
                        // Prevent deletion of CEO roles
                        return strtolower($record->name) !== 'ceo';
                    })
                    ->disabled(fn(\Units\Shield\Manage\Models\Role $record)=>str($record->name)->contains(['super_admin']))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make()
                    ->visible(false), // Disable bulk delete to prevent CEO role deletion
            ]);
    }
}
