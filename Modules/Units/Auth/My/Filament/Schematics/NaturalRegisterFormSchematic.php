<?php

namespace Units\Auth\My\Filament\Schematics;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Modules\Basic\BaseKit\Filament\Form\Components\NationalCodeInput;
use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;
use Modules\Basic\Helpers\Helper;
use Units\Auth\My\Filament\Pages\Auth\Register\LegalPage;
use Units\Auth\My\Services\AuthService;
use Units\Corporates\FieldOfActivity\Common\Models\FieldOfActivityModel;

class NaturalRegisterFormSchematic extends BaseFormSchematic
{

    public function attributeLabels(): array
    {
        return [
            'ceo_first_name' => 'نام',
            'ceo_last_name' => 'نام خانوادگی',
            'ceo_national_code' => 'کدملی مالک کسب و کار',
            'ceo_phone_number' => 'شماره همراه مالک کسب و کار',
            'field_of_activity' => 'حوزه فعالیت',
            'agent_first_name' => 'نام',
            'agent_last_name' => 'نام خانوادگی',
            'agent_phone_number' => 'شماره همراه',
            'agent_national_code' => 'کد ملی'
        ];
    }


    function commonFormSchema(): array
    {
        $mobile = Helper::denormalize_phone_number(app(AuthService::class)->getMobileNumber());

        return [
            Section::make('اطلاعات مالک کسب و کار ( در حال ثبت نام به عنوان شخصیت حقیقی )')
                ->footerActions([
                    \Filament\Forms\Components\Actions\Action::make('legal')
                        ->label('تغییر به حقوقی')
                        ->url(LegalPage::getUrl())
                ])
                ->schema([
                    Grid::make(12)
                        ->schema([
                            Fieldset::make('نام و نام خانوادگی شخص حقیقی مالک کسب و کار')
                                ->schema([
                                    $this->textInput('ceo_first_name')
                                        ->visible()
                                        ->columnSpan(6)
                                        ->required(),
                                    $this->textInput('ceo_last_name')
                                        ->visible()
                                        ->columnSpan(6)
                                        ->required(),
                                ])
                                ->columns(12),

                            $this->nationalCodeInput('ceo_national_code')
                                ->visible()
                                ->columnSpan(6)
                                ->extraAlpineAttributes([
                                    'class' => 'text-left'
                                ])
                                ->maxLength(13)
                                ->required(),
                            $this->normalPhoneNumberInput('ceo_phone_number')
                                ->visible()
                                ->helperText('مالکیت شماره همراه و کد ملی مطابقت داشته باشد.')
                                ->columnSpan(6)
                                ->regex('/^0\d{10}$/')
                                ->default($mobile)
                                ->extraAlpineAttributes([
                                    'style' => 'text-align:left;direction:ltr'
                                ])
                                ->required()
                        ])
                ]),
            Select::make('field_of_activity')
                ->label('حوزه فعالیت')
                ->options(FieldOfActivityModel::activated()->pluck('title', 'id'))
                ->required(),
            Section::make('معرفی نماینده تام الاختیار (اختیاری)')
                ->collapsible()
                ->collapsed(true)
                ->schema([
                    Grid::make(12)
                        ->schema([
                            $this->textInput('agent_first_name')
                                ->visible()
                                ->columnSpan(6),
                            $this->textInput('agent_last_name')
                                ->visible()
                                ->columnSpan(6)
                                ->required(function ($get) {
                                    if (!empty($get('agent_first_name'))) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }),
                            $this->normalPhoneNumberInput('agent_phone_number')
                                ->visible()
                                ->columnSpan(6)
                                ->required(function ($get) {
                                    if (!empty($get('agent_first_name'))) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }),
                            $this->nationalCodeInput('agent_national_code')
                                ->helperText('مالکیت شماره همراه و کد ملی مطابقت داشته باشد.')
                                ->visible()
                                ->columnSpan(6)
                                ->required(function ($get) {
                                    if (!empty($get('agent_first_name'))) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }),
                        ])
                ]),
        ];
    }

    function editFormSchema(): array|null
    {
        return [];
    }

    function createFormSchema(): array|null
    {
        return [];
    }

    public function invisibleAttributes(): array
    {
        return [];
    }

    public function disableAttributes(): array
    {
        return [];
    }
}
