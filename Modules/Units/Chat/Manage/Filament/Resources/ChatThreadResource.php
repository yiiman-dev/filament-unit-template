<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:31 AM
 */

namespace Modules\Units\Chat\Manage\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Units\Chat\Common\Models\ChatThreadModel;
use Modules\Units\Chat\Manage\Filament\Resources\ChatThreadResource\Pages;

/**
 * منبع ترد چت
 * Chat thread resource
 *
 * منبع فیلمنت برای مدیریت تردهای چت
 * Filament resource for managing chat threads
 */
class ChatThreadResource extends Resource
{
    protected static ?string $model = ChatThreadModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'چت‌ها';

    protected static ?string $pluralModelLabel = 'چت‌ها';

    protected static ?string $modelLabel = 'چت';

    protected static ?string $navigationGroup = 'ارتباطات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255)
                    ->extraInputAttributes(['dir' => 'rtl', 'class' => 'text-right']),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->maxLength(65535)
                    ->extraInputAttributes(['dir' => 'rtl', 'class' => 'text-right']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('model_type')
                    ->label('نوع مدل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_id')
                    ->label('آیدی مدل')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->extraAttributes(['dir' => 'rtl', 'class' => 'text-right']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListChatThreads::route('/'),
            'view' => Pages\ViewChatThread::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
