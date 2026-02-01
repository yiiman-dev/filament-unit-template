<?php

namespace Units\Users\Manage\My\Filament\Schematic\FormPartial;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;
use Modules\Basic\Helpers\Helper;
use Morilog\Jalali\Jalalian;
use Units\Auth\My\Models\UserMetadata;
use Units\Auth\My\Models\UserModel;
use Units\Corporates\Registering\Common\Models\CorporatesRegisteringModel;
use Units\FinnoTech\Common\Jobs\MobileAndNationalCodeVerifyJob;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechMobileAndNationalCodeVerifyDto;

class UserValidationFieldsetSchema extends BaseFormSchematic
{
    public function commonFormSchema(): array
    {
        return [
            Fieldset::make('وضعیت هویت سنجی سیستماتیک')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            $this->placeHolder('fnnotech_tracker_id')
                                ->visible()
                                ->content(function (?UserModel $record) {
                                    if (empty($record)) {
                                        return new HtmlString('<div style="color: orangered">هویت سنجی سیستمی اجرا نشده</div>');
                                    }
                                    $profile=UserMetadata::getProfile($record->national_code);
                                    if (!empty($profile['finnotech::inquiry-track_id'])) {
                                        return new HtmlString(
                                            '<div style="color: green">' . $profile['finnotech::inquiry-track_id'] . '</div>'
                                        );
                                    } else {
                                        return new HtmlString(
                                            '<div style="color: orangered">هویت سنجی سیستمی اجرا نشده</div>'
                                        );
                                    }
                                })
                                ->label('شناسه پیگیری هویت سنجی فینوتک'),
                            $this->placeHolder('finnotech_time')
                                ->visible()
                                ->label('زمان اجرای هویت سنجی')
                                ->content(function (?UserModel $record) {
                                    if (empty($record)) {
                                        return '---';
                                    }
                                    $profile=UserMetadata::getProfile($record->national_code);
                                    if (!empty($profile['finnotech::inquiry-date'])) {
                                        return new HtmlString(
                                            '<div style="color: green">' . Jalalian::fromDateTime(
                                                $profile['finnotech::inquiry-date']
                                            )->format('Y/m/d H:i:s') . '</div>'
                                        );
                                    } else {
                                        return '---';
                                    }
                                }),
                            $this->placeHolder('finnotech_result')
                                ->visible()
                                ->label('نتیجه ی هویت سنجی')
                                ->content(function (?UserModel $record) {
                                    if (empty($record)) {
                                        return '---';
                                    }
                                    $profile=UserMetadata::getProfile($record->national_code);
                                    if (isset($profile['finnotech::validation_result'])) {
                                        $mobile = $record->agent_mobile ?? '';
                                        $national = $record->agent_national_code ?? '';
                                        if ((boolean)$profile['finnotech::validation_result'] === true) {
                                            return new HtmlString(
                                                '<div style="color: green" >مالکیت شماره همراه ' . $mobile . 'با کد ملی ' . $national . ' تطابق دارد</div>'
                                            );
                                        } else {
                                            return new HtmlString(
                                                '<div style="color: red" >مالکیت شماره همراه ' . $mobile . 'با کد ملی ' . $national . ' تطابق ندارد</div>'
                                            );
                                        }
                                    } else {
                                        return '---';
                                    }
                                }),
                        ]),
                    Grid::make(3)
                        ->schema([
                            Actions::make([
                                $this->actionField('inquery')
                                    ->label('اجرای اعتبار سنجی')
                                    ->action(function(?UserModel $record){
                                        if (empty($record)) {
                                            $this->alert_error('رکورد کاربر یافت نشد');
                                            return;
                                        }
                                        $trackId=Str::uuid();
                                        UserMetadata::add_meta_key(
                                            'finnotech::inquiry-date',
                                            date('Y-m-d H:i:s'),
                                            $record->national_code
                                        );
                                        UserMetadata::add_meta_key(
                                            'finnotech::inquiry-track_id',
                                            $trackId,
                                            $record->national_code
                                        );
                                        $phone_number=Helper::denormalize_phone_number($record->phone_number);
                                        $result=MobileAndNationalCodeVerifyJob::dispatchSync($phone_number,$record->national_code,$trackId);
                                        /**
                                         * @var FinnoTechMobileAndNationalCodeVerifyDto|FinnoTechErrorDto $result
                                         */
                                        if($result->isSuccess()){
                                            UserMetadata::add_meta_key(
                                                'finnotech::validation_result',
                                                $result->getIsValid(),
                                                $record->national_code
                                            );
                                        }else{
                                            $this->alert_error('فینوتک: '.$result->message);
                                        }

                                    })
                                    ->color(Color::Indigo)
                                    ->visible()
                            ])->columnSpan(1)
                        ])

                ])
        ];
    }
}
