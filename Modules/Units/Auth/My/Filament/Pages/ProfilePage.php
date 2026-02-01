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

namespace Units\Auth\My\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Log;
use Modules\Basic\BaseKit\Filament\BasePage;
use Modules\Basic\Helpers\Helper;
use Units\ActLog\My\Services\ActLogService;
use Units\Auth\My\Filament\Schematics\ProfileFormSchematic;
use Units\Auth\My\Filament\Widgets\ProfileUserLogsTableWidget;
use Units\Auth\My\Models\UserModel;
use Units\Auth\My\Services\UserMetadataService;
use Units\Auth\My\Services\UserService;
use Units\Avatar\My\MyAvatarProvider;
use Units\SMS\Common\Services\BaseSmsService;

/**
 * @url https://www.figma.com
 *
 * @property UserModel $record
 */
class ProfilePage extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'my_auth::filament.pages.profile';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationLabel = 'پروفایل';
    protected static ?string $title = 'پروفایل کاربری';
    protected static string $route_path = 'profile';

    public ?array $data = [];

    // Profile form fields as class properties
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $birth_day = null;
    public ?string $bio = null;
    public ?string $address = null;
    public ?string $bank_account_sheba = null;
    public ?string $bank_account_payment_card_no = null;
    public ?string $phone_number = null;
    public ?string $national_code = null;

    public ?string $job_position=null;

    protected UserMetadataService $metadataService;
    protected ActLogService $actLogService;
    protected UserService $userService;
    protected BaseSmsService $smsService;

    public static function canAccess(): bool
    {
        return  true;
    }

    public function __construct()
    {
        parent::__construct();
        $this->metadataService = app(UserMetadataService::class);
        $this->actLogService = app(ActLogService::class);
        $this->userService = app(UserService::class);
        $this->smsService = app(BaseSmsService::class);
        $this->fill($this->data);
    }

    public function mount(): void {
        $this->loadProfileData();
    }

    public static function getRoutePath(): string
    {
        return self::$route_path;
    }


    protected function loadProfileData(): void
    {
        $userModel = Filament::getPanel('my')->auth()->user();
        $national_code = $userModel->national_code;

        $data = $this->metadataService->getProfile($national_code);
        if (empty($data)) {
            $this->data = [];
        } else {
            $this->data = $data;
        }
        $corporateUserModel=$userModel
            ->corporateUsers()
            ->where(['corporate_national_code'=>Helper::getMyPanelCurrentCorporate()->national_code])
            ->first();
        $this->data['phone_number'] = Helper::denormalize_phone_number($userModel->phone_number);
        $this->data['national_code'] = $userModel->national_code;
        $this->data['job_position'] = $corporateUserModel->job_position;
    }

    public function form(Form $form): Form
    {
        return ProfileFormSchematic::makeForm($form)->returnCommonForm()
            ->statePath('data');
    }

    public function save(): void
    {
        $this->validate();
        $data = $this->data;
        $userModel=filament()->auth()->user();
        /**
         * @var UserModel $userModel
         */
        try {
            $corporateUserModel=$userModel
                ->corporateUsers()
                ->where(['corporate_national_code'=>Helper::getMyPanelCurrentCorporate()->national_code])
                ->first();
            $corporateUserModel->job_position=$data['job_position'];
            $corporateUserModel->save();
        }catch(\Exception $e){
            $this->alert_error('ذخیره موقعیت شغلی شما با خطا مواجه شد');
            Log::error($e->getMessage(),$e->getTrace());
        }
        unset($data['job_position']);
        unset($data['phone_number']);

        $national_code = Filament::getPanel('my')->auth()->user()->national_code;
        $this->metadataService->actUpdateProfile(national_code: $national_code, data: $data);

        if (!$this->metadataService->hasErrors()) {
            MyAvatarProvider::make()->clearCache($userModel);
            $this->alert_success('اطلاعات پروفایل با موفقیت به‌روزرسانی شد.');
        } else {
            $this->alert_error('خطا در به‌روزرسانی اطلاعات پروفایل.');
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
//            ProfileUserLogsTableWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
