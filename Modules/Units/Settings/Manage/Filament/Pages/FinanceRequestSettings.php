<?php

namespace Units\Settings\Manage\Filament\Pages;

use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Modules\Basic\BaseKit\Filament\Components\Forms\TextInput;
use Outerweb\FilamentSettings\Filament\Pages\Settings;
use Outerweb\Settings\Models\Setting;
use Units\Settings\Manage\Models\ManageSettings;

class FinanceRequestSettings extends Settings
{
    protected static ?string $navigationGroup = 'تنظیمات';
    protected static ?string $navigationLabel = 'درخواست تامین مالی';

    /**
     * @return string|null
     */
    public static function getNavigationLabel(): string
    {
        return 'درخواست تامین مالی';
    }

    protected Setting|string $model = ManageSettings::class;

    public function schema(): array|Closure
    {
        return
            [
                Section::make('بازه ی درخواست تامین مالی')
                    ->description(
                        'در این بخش می توانید٬ بازه ی حداقل و حداکثر مقدار درخواست تامین مالی توسط بنگاه ها را تعیین نمایید.'
                    )
                    ->schema(
                        [
                            Grid::make()
                                ->schema([
                                    TextInput::make('finance_request.amount.min')
                                        ->suffix('میلیارد ریال')
                                        ->numeric()
                                        ->label('حداقل مقدار درخواست'),
                                    TextInput::make('finance_request.amount.max')
                                        ->suffix('میلیارد ریال')
                                        ->numeric()
                                        ->label('حداکثر مقدار درخواست'),

                                ])
                        ]
                    ),
                Section::make('دوره ی بازپرداخت درخواستی')
                    ->description(
                        'در این بخش می توانید٬ بازه ی حداقل و حداکثر دوره ی بازپرداخت درخواست تامین مالی توسط بنگاه ها را تعیین نمایید.'
                    )
                    ->schema(
                        [
                            Grid::make()
                                ->schema([
                                    TextInput::make('finance_request.repayment_period.min')
                                        ->suffix('ماه')
                                        ->numeric()
                                        ->label('حداقل زمان دوره بازپرداخت'),
                                    TextInput::make('finance_request.repayment_period.max')
                                        ->suffix('ماه')
                                        ->numeric()
                                        ->label('حداکثر زمان دوره بازپرداخت'),

                                ])
                        ]
                    ),

                Section::make('دوره ی تنفس درخواستی')
                    ->description(
                        'در این بخش می توانید٬ بازه ی حداقل و حداکثر دوره ی تنفس در درخواست تامین مالی توسط بنگاه ها را تعیین نمایید.'
                    )
                    ->schema(
                        [
                            Grid::make()
                                ->schema([
                                    TextInput::make('finance_request.breathing_period.min')
                                        ->suffix('ماه')
                                        ->numeric()
                                        ->label('حداقل زمان دوره تنفس'),
                                    TextInput::make('finance_request.breathing_period.max')
                                        ->suffix('ماه')
                                        ->numeric()
                                        ->label('حداکثر زمان دوره تنفس'),

                                ])
                        ]
                    ),


            ];
    }
}
