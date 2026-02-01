<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 6:11 PM
 */

namespace Units\Users\Admin\Manage\Filament\Resources\UserResource\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Modules\Basic\BaseKit\Filament\Form\Components\MobileInput;
use Modules\Basic\BaseKit\Filament\Form\Components\PasswordInput;
use Modules\Basic\BaseKit\Filament\HasNotification;
use Modules\Basic\Helpers\Helper;
use Modules\Basic\Rules\RemoteUnique;
use Modules\FilamentAdmin\Filament\Resources\ManageData\UserResource\Pages\Throwable;
use Units\SMS\Common\Services\BaseSmsService;
use Units\Users\Admin\Manage\Filament\Resources\UserResource;
use Units\Users\Admin\Manage\Models\User;
use Units\Users\Admin\Manage\Services\UserService;

/**
 * صفحه ایجاد کاربر
 * این صفحه از مدل User که از APIModel ارث‌بری می‌کند استفاده می‌کند
 */
class CreateUser extends CreateRecord
{
    protected static string $routePath = 'manage/users';
    protected static string $resource = UserResource::class;
    use HasNotification;

    protected UserService $user_service;
    protected BaseSmsService $sms_service;


    public function __construct()
    {
        $this->user_service = app(UserService::class);
        $this->sms_service = app(BaseSmsService::class);
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('username')
                    ->extraAlpineAttributes(['tabindex' => 1])
                    ->rule((
                    new RemoteUnique(app(User::class), 'username', ' نام کاربری')
                    ))
                    ->label('نام کاربری')
                    ->extraAlpineAttributes([
                        'style' => 'text-align:left;direction:ltr'
                    ]),
                MobileInput::make('phone_number')
                    ->extraAlpineAttributes(['tabindex' => 2])
                    ->rule((
                    new RemoteUnique(app(User::class), 'phone_number', 'شماره همراه')
                    ))
                    ->required(),
                PasswordInput::make('password')
                    ->label('تعیین رمز عبور')
                    ->minLength(8)
                    ->regeneratePassword()
                    ->revealable()
                    ->copyable(),
                Select::make('status')
                    ->options(
                        [
                            UserService::STATUS_ACTIVE => 'کاربر فعال',
                            UserService::STATUS_DE_ACTIVE => 'غیر فعال (عدم امکان ورود به پنل)'
                        ]
                    )
                    ->default(UserService::STATUS_ACTIVE)
                    ->disabled(function ($record) {
                        if (!empty($record)) {
                            return true;
                        }
                    }),
                TextInput::make('created_by')
                    ->label('ایجاد شده توسط')
                    ->extraAlpineAttributes([
                        'style' => 'text-align:left;direction:ltr'
                    ])
                    ->disabled()
                    ->helperText(function ($record) {
                        if (isset($record['created_by']) && $record['created_by'] == $logged_in_user = Filament::auth()->user()->phone_number) {
                            return 'شما این کاربر را ایجاد کردید';
                        }
                    }),
                Checkbox::make('send_sms')
                    ->columns(2)
                    ->label('آیا برای کاربر ساخته شده پیامک ارسال شود؟')
                ->default(1),
            ]);
    }


    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        try {


            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeCreate($data);

            $this->callHook('beforeCreate');

            $this->user_service->actCreate(
                username: $data['username'],
                password: $data['password'],
                de_normalized_mobile: $data['phone_number'],
                status: (int)$data['status'],
                created_by: Filament::auth()->user()->phone_number
            );
            if ($this->user_service->hasErrors()) {
                $this->alert_error($this->user_service->getErrorMessages()[0]);
                return;
            }
            if ((bool)$data['send_sms']) {
                $this->sms_service->voidSend(
                    normalized_mobile: Helper::normalize_phone_number($data['phone_number']),
                    message: "با سلام - حساب کاربری شما در پنل مدیریت سامانه تامین مالی زنجیره ای آرین ایجاد شد.  \n "
                    . 'UserName: ' . $data['username'] . "\n"
                    . "Password: " . $data['password'] . "\n"
                    . "Address: "
                );
            }
            $this->record = $this->user_service->getSuccessResponse()->getData()['user'];
//            $this->form->setModel($this->user_service->getSuccessResponse()->getData()['user']);
//            $this->form->model($this->getRecord())->saveRelationships();

            $this->callHook('afterCreate');
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {

            throw $exception;
        }

        $this->rememberData();

        $this->getCreatedNotification()?->send();

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->getRecord()::class);
            $this->record = null;

            $this->fillForm();

            return;
        }

        $redirectUrl = $this->getRedirectUrl();

        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
    }

}
