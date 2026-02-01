<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/19/25, 1:07 PM
 */

namespace Units\ActLog\Admin\Filament\Widgets;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\HtmlString;
use Modules\Basic\BaseKit\Filament\InfoList\DateTimeColumn;
use Modules\Basic\Services\BaseActLogService;
use Units\ActLog\Admin\Models\ActLog;
use Units\ActLog\Admin\Services\ActLogService;

class ProfileUserLogsTableWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';


    protected static ?string $heading='گزارش فعالیت شما';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                app(ActLogService::class)->getLogs(
                    normalized_phone_number: auth()->user()->phone_number
                )
            )

            ->columns([
                TextColumn::make('action')
                    ->label('عملیات')
                    ->formatStateUsing(
                        fn (string $state) => BaseActLogService::_($state)
                    ),
                DateTimeColumn::make('created_at')
                    ->label('تاریخ')
                    ->toJalali(),
                TextColumn::make('ip_address')
                    ->label('آدرس IP'),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('view_details')
                    ->label('مشاهده جزئیات')
                    ->color(\Filament\Support\Colors\Color::Blue)
                    ->modalContent(function (ActLog $record){
                        if (empty($record->details)){
                            return new HtmlString( 'اطلاعاتی موجود نیست');
                        }else{
                            return new HtmlString(json_encode($record->details, JSON_PRETTY_PRINT));
                        }

                    })
            ]);
    }
}
