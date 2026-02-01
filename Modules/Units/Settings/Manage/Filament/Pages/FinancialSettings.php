<?php

namespace Units\Settings\Manage\Filament\Pages;

use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Modules\Basic\BaseKit\Filament\Components\Forms\TextInput;
use Outerweb\FilamentSettings\Filament\Pages\Settings;
use Outerweb\Settings\Models\Setting;
use Units\Settings\Manage\Models\ManageSettings;

class FinancialSettings extends Settings
{
    protected static ?string $navigationGroup = 'تنظیمات';
    protected static ?string $navigationLabel = 'تنظیمات مالی سیستم';

    /**
     * @return string|null
     */
    public static function getNavigationLabel(): string
    {
        return 'تنظیمات مالی سیستم';
    }

    protected Setting|string $model = ManageSettings::class;

    public function schema(): array|Closure
    {
        return
            [
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
            ];
    }
}
