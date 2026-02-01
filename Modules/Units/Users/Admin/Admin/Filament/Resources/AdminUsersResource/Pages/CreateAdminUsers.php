<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:53 PM
 */

namespace Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource\Pages;

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
use Modules\FilamentAdmin\Filament\Resources\AdminUsersResource\Pages\Throwable;
use Units\Auth\Admin\Models\UserModel;
use Units\SMS\Common\Services\BaseSmsService;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource;
use Units\Users\Admin\Admin\Services\UserService;

class CreateAdminUsers extends CreateRecord
{
    protected static string $resource = AdminUsersResource::class;
    use HasNotification;

    protected UserService $user_service;
    protected BaseSmsService $sms_service;


    public function __construct()
    {
        $this->user_service = app(UserService::class);
        $this->sms_service = app(BaseSmsService::class);
    }

    public function form(Form $form): Form
    {
        return parent::form($form)->schema([
            TextInput::make('username')
                ->extraAlpineAttributes(['tabindex' => 1])
                ->label('نام کاربری')
                ->unique(UserModel::class,ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'کاربر تکراری است'
                ])
                ->extraAlpineAttributes([
                    'style' => 'text-align:left;direction:ltr'
                ])
                ->required(),
            MobileInput::make('phone_number')
                ->extraAlpineAttributes(['tabindex' => 2])
                ->unique(UserModel::class,ignoreRecord: true)
                ->required(),
            PasswordInput::make('password')
                ->required()
                ->regeneratePassword()
                ->copyable()
                ->minLength(8)
                ->extraAlpineAttributes(['tabindex' => 3])
                ->default(''),
            Select::make('status')
                ->label('وضعیت حساب')
                ->extraInputAttributes(['tabindex' => 4])
                ->options([
                    UserService::STATUS_ACTIVE => 'کاربر فعال',
                    UserService::STATUS_DE_ACTIVE => 'کاربر غیرفعال'
                ])
                ->default(UserService::STATUS_ACTIVE),
            Checkbox::make('send_sms')
                ->label('ارسال پیامک اطلاعات کاربری')
                ->extraInputAttributes(['tabindex' => 5])
                ->default(1)
                ->helperText('در صورتی که این چک باکس فعال نشود٬ هیچگونه اطلاع رسانی به کاربری که ایجاد میکنید توسط سیستم انجام نمی شود.')

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
            $this->record=$this->user_service->getSuccessResponse()->getData()['user'];
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
