<?php

namespace Units\Shield\Manage\Filament;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Basic\BaseKit\Filament\Components\Table\TextColumn;
use Units\Shield\Manage\Models\ModelHasRole;

class UserRoleResource extends Resource
{
    protected static ?string $model = ModelHasRole::class;
    protected static bool $shouldRegisterNavigation=false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('model_type'),
                TextColumn::make('model_id'),
                TextColumn::make('role_id'),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => \Units\Shield\Manage\Filament\UserRoleResource\Pages\ListUserRoles::route('/'),
            'create' => \Units\Shield\Manage\Filament\UserRoleResource\Pages\CreateUserRole::route('/create'),
            'edit' => \Units\Shield\Manage\Filament\UserRoleResource\Pages\EditUserRole::route('/{record}/edit'),
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
