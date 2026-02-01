<?php

namespace Units\Settings\Manage\Filament\Pages;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Modules\Basic\BaseKit\Filament\Components\Forms\TextInput;
use Outerweb\FilamentSettings\Filament\Pages\Settings;
use Outerweb\Settings\Models\Setting;
use Units\FinanceRequest\Common\Settings\Schematics\FinanceRequestSmsSettingsSchematic;
use Units\Settings\Manage\Models\ManageSettings;

class BasicParameterSettings extends Settings
{
    protected static ?string $navigationGroup = 'تنظیمات';
    protected static ?string $navigationLabel = 'تنظیمات پارامترهای پایه';


    protected Setting|string $model = ManageSettings::class;

    public function schema(): array
    {
        return [
            Tabs::make()->schema([
                Tab::make('finance_request')
                    ->label('درخواست های تامین مالی')
                    ->schema([
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
                    ]),
                Tab::make('financial_settings')
                    ->label('محاسبات مالی')
                    ->schema([
                        Section::make('')
                            ->schema(
                                [
                                    Grid::make()
                                        ->schema([
                                            TextInput::make('financial.vat_percentage')
                                                ->suffix('%')
                                                ->numeric()
                                                ->label('مالیات بر ارزش افزوده'),
                                        ])
                                ]
                            )
                    ]),
                Tab::make('helper_information')
                    ->label('اطلاعات راهنما')
                    ->schema([
                        Fieldset::make('مرکز تماس های شرکت')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('global.brand.phone1_title')
                                    ->label('عنوان نمایشی کریدور تماس اول'),
                                \Filament\Forms\Components\TextInput::make('global.brand.phone1')
                                    ->label('شماره برقراری ارتباط با کریدور تماس اول'),

                                \Filament\Forms\Components\TextInput::make('global.brand.phone2_title')
                                    ->label('عنوان نمایشی کریدور تماس دوم'),
                                \Filament\Forms\Components\TextInput::make('global.brand.phone2')
                                    ->label('شماره برقراری ارتباط با کریدور تماس دوم'),
                            ])
                            ->columns(2),
                    ]),
                Tab::make('sms_templates')
                    ->label('متن پیامک ها')
                    ->schema([
                        ...FinanceRequestSmsSettingsSchematic::makeSchema()->returnCommonSchema()

                    ]),

                Tab::make('system')
                    ->label('سیستم')
                    ->schema([
                        TextInput::make('date')
                            ->disabled()
                            ->formatStateUsing(function () {
                                return date('Y-m-d');
                            })
                            ->label('تاریخ سیستم'),
                        TextInput::make('time')
                            ->disabled()
                            ->formatStateUsing(function () {
                                return date('H:i:s');
                            })
                            ->label('زمان سیستم'),
                        TextInput::make('zone')
                            ->disabled()
                            ->formatStateUsing(function () {
                                return config('app.timezone');
                            })
                            ->label('منطقه زمانی سیستم'),
                    ])
            ])
        ];
    }
}
