<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          10/27/25, 1:32 AM
 */

namespace Modules\Units\Chat\My\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\Units\Chat\Common\Models\ChatThreadModel;
use Modules\Units\Chat\My\Filament\Resources\ChatThreadResource\Pages;

/**
 * منبع ترد چت مای
 * Chat thread my resource
 *
 * منبع فیلمنت برای مدیریت تردهای چت در پنل مای
 * Filament resource for managing chat threads in my panel
 */
class ChatThreadResource extends Resource
{
    protected static ?string $model = ChatThreadModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'چت‌های من';

    protected static ?string $pluralModelLabel = 'چت‌های من';

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
        // For multi-tenant support in My panel, we might need to filter by tenant
        // This is a simplified version - actual implementation may vary based on your tenant system
        $query = parent::getEloquentQuery();

        // Assuming we want to filter by related model's tenant or user ownership
        // You may need to adjust this based on your specific multi-tenant implementation
        $user = Auth::user();
        if ($user) {
            // This is a placeholder - you'll need to implement the actual tenant filtering
            // based on your multi-tenant architecture
        }

        return $query;
    }
}
