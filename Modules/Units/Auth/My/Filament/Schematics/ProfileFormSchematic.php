<?php

namespace Units\Auth\My\Filament\Schematics;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Modules\Basic\BaseKit\Filament\Components\Forms\DateTimePicker;
use Modules\Basic\BaseKit\Filament\Form\Components\MobileInput;
use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;
use Modules\Basic\Helpers\Helper;

class ProfileFormSchematic extends BaseFormSchematic
{

    function commonFormSchema(): array
    {
        return [
            Section::make('اطلاعات حساب کاربری')
                ->extraAlpineAttributes(['style' => 'margin-top:40px'])
                ->schema([
                    $this->normalPhoneNumberInput('phone_number')
                        ->label('شماره همراه')
                        ->visible()
                        ->disabled(),
                    $this->nationalCodeInput('national_code')
                        ->label('کد ملی')
                        ->visible()
                        ->extraAlpineAttributes(['style' => 'text-align:left;direction:ltr'])
                        ->disabled(),

                ])->columns(2),
            Section::make('اطلاعات شخصی')
                ->description('
                دقت داشته باشید٬ وارد کردن نام - نام خانوادگی - سمت شغلی در بنگاه به صورت صحیح الزامیست. از این اطلاعات جهت انعقاد تفاهم نامه و قراردادهای آتی در فرآیند تامین مالی استفاه خواهد شد.
                ')
                ->visible()
                ->extraAlpineAttributes(['style' => 'margin-bottom:40px'])
                ->schema([
                    //                        FileUpload::make('profile_image')
                    //                            ->label('تصویر پروفایل')
                    //                            ->image()
                    //                            ->avatar()
                    //                            ->imageEditor()
                    //                            ->directory('profile_images')
                    //                            ->columnSpanFull(),

                    $this->textInput('first_name')
                        ->label('نام')
                        ->visible()
                        ->required(),
                    $this->textInput('last_name')
                        ->visible()
                        ->label('نام خانوادگی')
                        ->required(),
                    $this->datePickerInput('birth_day')
                        ->visible()
                        ->label('تاریخ تولد')
                        ->nullable(),
                    $this->textAreaInput('bio')
                        ->visible()
                        ->label('درباره من')
                        ->columnSpanFull(),
                    $this->textInput('address')
                        ->visible()
                        ->label('آدرس')
                        ->columnSpanFull(),
                ])->columns(2),
            Section::make(function () {
                $corp_name = Helper::getMyPanelCurrentCorporate()->corporates_name;
                return "اطلاعات شما در بنگاه {$corp_name}";
            })
                ->schema([
                    $this->textInput('job_position')
                        ->required()
                        ->visible()
                        ->label('سمت شغلی شما در بنگاه')
                ]),
            Section::make('اطلاعات حساب بانکی')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            $this->shebaTextInput('bank_account_sheba')
                                ->visible()
                                ->label('شماره شبا'),
                            $this->paymentCardTextInput('bank_account_payment_card_no')
                                ->visible()
                                ->label('شماره کارت بانکی')
                        ])
                ])
        ];
    }

}
