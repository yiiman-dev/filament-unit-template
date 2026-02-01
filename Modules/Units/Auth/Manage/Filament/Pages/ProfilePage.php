<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:57 PM
 */

namespace Units\Auth\Manage\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Modules\Basic\BaseKit\Filament\BasePage;
use Modules\Basic\BaseKit\Filament\Form\Components\MobileInput;
use Modules\Basic\BaseKit\Filament\Form\Components\PasswordInput;
use Units\ActLog\Manage\Filament\Widgets\ProfileUserLogsTableWidget;
use Units\ActLog\Manage\Services\ActLogService;
use Units\Auth\Manage\Services\UserMetadataService;
use Units\Auth\Manage\Services\UserService;
use Units\SMS\Common\Services\BaseSmsService;

/**
 * @url https://www.figma.com
 */
class ProfilePage extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'manage_auth::filament.pages.profile';
    protected static ?string $navigationLabel = 'پروفایل';
    protected static ?string $title = 'پروفایل کاربری';
    protected static string $route_path = 'profile';
    public ?array $data = [];
    protected UserMetadataService $metadataService;
    protected ActLogService $actLogService;
    protected UserService $userService;
    protected BaseSmsService $smsService;
    public function __construct()
    {
        parent::__construct();
        $this->metadataService = app(UserMetadataService::class);
        $this->actLogService = app(ActLogService::class);
        $this->userService = app(UserService::class);
        $this->smsService = app(BaseSmsService::class);
    }

    public static function getRoutePath(): string
    {
        return self::$route_path;
    }

    public function mount(): void
    {
        $this->loadProfileData();
    }

    protected function loadProfileData(): void
    {
        $phone_number = Filament::auth()->user()->phone_number;

        $data=$this->metadataService->getProfile($phone_number);
        if (empty($data)){
            $this->data = [];
        }else{
            $this->data = $data;
        }

        $this->data['phone_number'] = $phone_number;
        $this->data['username'] = Filament::auth()->user()->username;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات حساب کاربری')
                    ->extraAlpineAttributes(['style'=>"margin-top:40px"])

                ->schema([
                    MobileInput::make('phone_number')
                        ->disabled(),
                    TextInput::make('username')
                        ->label('نام کاربری')
                        ->extraAlpineAttributes(['style'=>'text-align:left;direction:ltr'])
                        ->disabled(),
                ])->columns(2),
                    Section::make('اطلاعات شخصی')
                        ->extraAlpineAttributes(['style' => 'margin-bottom:40px'])
                        ->schema([
    //                        FileUpload::make('profile_image')
    //                            ->label('تصویر پروفایل')
    //                            ->image()
    //                            ->avatar()
    //                            ->imageEditor()
    //                            ->directory('profile_images')
    //                            ->columnSpanFull(),

                            TextInput::make('first_name')
                                ->label('نام')
                                ->required(),
                            TextInput::make('last_name')
                                ->label('نام خانوادگی')
                                ->required(),
                            Textarea::make('bio')
                                ->label('درباره من')
                                ->columnSpanFull(),
                            TextInput::make('address')
                                ->label('آدرس')
                                ->columnSpanFull(),
                        ])->columns(2),

                ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data=$this->data;
        unset($data['username']);
        unset($data['phone_number']);
        $phone_number = Filament::auth()->user()->phone_number;
        $this->metadataService->actUpdateProfile(phone_number: $phone_number, data:$data);

        if (!$this->metadataService->hasErrors()) {

            $this->alert_success( 'اطلاعات پروفایل با موفقیت به‌روزرسانی شد.');
        } else {
            $this->alert_error( 'خطا در به‌روزرسانی اطلاعات پروفایل.');
        }
    }
    protected function getHeaderWidgets(): array
    {
        return [
            ProfileUserLogsTableWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('change_password')
                ->label('تغییر رمز عبور')
                ->form([
                    PasswordInput::make('current_password')
                        ->label('رمز عبور فعلی')
                        ->password()
                        ->required(),
                    PasswordInput::make('new_password')
                        ->label('رمز عبور جدید')
                        ->password()
                        ->required()
                        ->helperText('رمز عبور باید حداقل 8 کاراکتر باشد و باید شامل عدد و حروف باشد')
                        ->revealable()
                        ->minLength(8),
                    PasswordInput::make('new_password_confirmation')
                        ->label('تکرار رمز عبور جدید')
                        ->password()
                        ->required()
                        ->revealable()
                        ->same('new_password'),
                    Checkbox::make('send_sms')
                       ->label('ارسال رمز عبور جدید به شماره موبایل من')
                       ->default(true),
                ])
                ->extraModalFooterActions([
                    \Filament\Actions\Action::make('cancel')
                        ->label('انصراف')
                        ->color(Color::Blue)
                        ->close(),
                    \Filament\Actions\Action::make('help')
                        ->label('رمز عبور را فراموش کرده ام')
                        ->color(Color::Gray)
                        ->icon('heroicon-o-information-circle')
                        ->action(function () {
                            $this->alert_info('برای بازیابی رمز عبور با مدیر ارشد خود هماهنگ کنید تا رمز عبور شما را بازنشانی کنند.');
                        }),
                ])
                ->action(function (array $data) {
                    $this->userService->actChangePasswordConfirmCurrent(
                        Filament::auth()->user()->phone_number,
                        $data['current_password'],
                        $data['new_password']
                    );
                    if ($this->userService->getSuccessResponse()) {
                        $this->actLogService->actLog( 'update_password',['رمز عبور توسط کاربر تعویض شد']);
                        $this->actLogService->actLog('send_new_Password', ['رمز عبور جدید به کاربر ارسال شد.']);
                        if ($data['send_sms']){
                            $this->smsService->voidSend(Filament::auth()->user()->phone_number, "رمز عبور جدید شما: {$data['new_password']}"."\n"."نام کاربری : ".Filament::auth()->user()->username."\n");
                        }
                        $this->alert_success('رمز عبور با موفقیت تغییر کرد.');
                    } else {
                        $this->alert_error('خطا در تغییر رمز عبور.');
                    }
                }),
        ];
    }
}
