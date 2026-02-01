<?php

namespace Units\Auth\My\Filament\Schematics;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;
use Modules\Basic\Helpers\Helper;
use Units\Auth\My\Filament\Pages\Auth\Register\NaturalPage;
use Units\Corporates\FieldOfActivity\Common\Models\FieldOfActivityModel;

class LegalRegisterFormSchematic extends BaseFormSchematic
{
    public function attributeLabels(): array
    {
        return [
            'corporate_name' => 'نام شرکت',
            'corporate_national_id' => 'شناسه ملی شرکت',
            'field_of_activity' => 'حوزه فعالیت اصلی شرکت',
            'ceo_first_name' => 'نام',
            'ceo_last_name' => 'نام خانوادگی',
            'ceo_national_code' => 'کدملی مدیرعامل',
            'ceo_phone_number' => 'شماره همراه مدیرعامل',
            'agent_first_name' => 'نام',
            'agent_last_name' => 'نام خانوادگی',
            'agent_national_code' => 'کدملی نماینده',
            'agent_phone_number' => 'شماره همراه نماینده',
        ];
    }

    public function attributeHints(): array
    {
        return [
            'corporate_national_id' => 'شماره شناسه ملی شرکت',
            'ceo_national_code' => 'کدملی مدیرعامل مطابق آگهی قانونی شرکت باشد',
            'ceo_phone_number' => 'مالکیت شماره همراه و کدملی مطابقت داشته باشد',
            'agent_phone_number' => 'شماره همراه نماینده',
        ];
    }

    public function attributeHelperTexts(): array
    {
        return [
            'ceo_national_code' => 'کدملی مدیرعامل مطابق آگهی قانونی شرکت باشد',
            'ceo_phone_number' => 'مالکیت شماره همراه و کدملی مطابقت داشته باشد',
            'agent_phone_number' => 'شماره همراه نماینده مطابقت داشته باشد',
        ];
    }

    public function attributePlaceholders(): array
    {
        return [
            'corporate_name' => 'نام شرکت',
            'corporate_national_id' => '1234567890123',
            'ceo_name' => 'نام و نام خانوادگی مدیرعامل',
            'ceo_national_code' => '1234567890',
            'ceo_phone_number' => '09123456789',
            'agent_name' => 'نام و نام خانوادگی نماینده',
            'agent_national_code' => '1234567890',
            'agent_phone_number' => '09123456789',
        ];
    }

    function commonFormSchema(): array
    {
        return [
            Section::make('اطلاعات شرکت ( در حال ثبت نام به عنوان شخصیت حقوقی )')
                ->footerActions([
                    Action::make('natural')
                        ->label('تغییر به حقیقی')
                        ->color(Color::Amber)
                        ->url(NaturalPage::getUrl())
                ])
                ->collapsible()
                ->schema([
                    Grid::make(12)
                        ->schema([
                            $this->textInput('corporate_name')
                                ->visible()
                                ->columnSpan(12)
                                ->required(),
                            $this->textInput('corporate_national_id')
                                ->visible()
                                ->columnSpan(6)
                                ->extraAlpineAttributes([
                                    'class' => 'text-left'
                                ])
                                ->numeric()
                                ->maxLength(13)
                                ->required(),
                            $this->selectInput('field_of_activity')
                                ->visible()
                                ->columnSpan(6)
                                ->options(
                                    FieldOfActivityModel::query()->activated()->pluck(
                                        'title',
                                        'id'
                                    )->toArray()
                                )
                                ->required(),
                        ]),
                ]),
            Section::make('اطلاعات مدیر عامل')
                ->collapsible()
                ->schema([
                    Grid::make(12)
                        ->schema([
                            $this->textInput('ceo_first_name')
                                ->visible()
                                ->columnSpan(6)
                                ->required(),
                            $this->textInput('ceo_last_name')
                                ->visible()
                                ->columnSpan(6)
                                ->required(),
                            $this->nationalCodeInput('ceo_national_code')
                                ->visible()
                                ->columnSpan(6)
                                ->helperText('کدملی مدیرعامل مطابق آگهی قانونی شرکت باشد')
                                ->required(),
                            $this->normalPhoneNumberInput('ceo_phone_number')
                                ->visible()
                                ->columnSpan(6)
                                ->label('شماره همراه مدیرعامل')
                                ->default(function () {
                                    $mobile = app()->make(\Units\Auth\My\Services\AuthService::class)->getMobileNumber(
                                    );
                                    return Helper::denormalize_phone_number($mobile);
                                })
                                ->helperText('مالکیت شماره همراه و کدملی مطابقت داشته باشد')
                                ->required()
                        ])
                ]),
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
                            $this->nationalCodeInput('agent_national_code')
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
                                ->label('شماره همراه')
                                ->regex('/^0\d{10}$/')
                                ->required(function ($get) {
                                    if (!empty($get('agent_first_name'))) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                })
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
