<?php

namespace Units\Settings\Manage\Filament\Pages;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;
use Modules\Basic\BaseKit\Filament\Components\Forms\TextInput;
use Outerweb\FilamentSettings\Filament\Pages\Settings;
use Outerweb\Settings\Models\Setting;
use Units\Settings\Manage\Models\ManageSettings;

class GlobalSettings extends Settings
{
    protected static ?string $navigationGroup = 'تنظیمات';
    protected static ?string $navigationLabel = 'تنظیمات سامانه';

    /**
     * @return string|null
     */
    public static function getNavigationLabel(): string
    {
        return 'تنظیمات سامانه';
    }

    protected Setting|string $model = ManageSettings::class;

    public function schema(): array|Closure
    {
        return
            [
                Tabs::make('')
                    ->tabs([
                        Tab::make('سیستم')
                            ->schema([
                                Fieldset::make('زمان سیستم')
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
                                    ]),
                            ]),
                        Tab::make('هویت')
                            ->schema([
                                Fieldset::make('شماره تماس اول')
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('global.brand.phone1_title')
                                            ->label('عنوان شماره تماس '),
                                        \Filament\Forms\Components\TextInput::make('global.brand.phone1')
                                            ->label('شماره تماس '),
                                    ])
                                    ->columns(2),
                                Fieldset::make('شماره های تماس دوم')
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('global.brand.phone2_title')
                                            ->label('عنوان شماره تماس '),
                                        \Filament\Forms\Components\TextInput::make('global.brand.phone2')
                                            ->label('شماره تماس '),
                                    ])
                                    ->columns(2),
                            ])
                    ]),

            ];
    }
}
