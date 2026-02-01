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

namespace Units\Auth\Admin\Filament\Pages;


use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Concerns\CanAuthorizeAccess;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Colors\Color;
use Modules\Basic\BaseKit\Filament\BasePage;
use Modules\Basic\BaseKit\Filament\Form\Components\MobileInput;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\ActLog\Admin\Services\ActLogService;
use Units\Auth\Admin\Services\AuthService;
use Units\Users\Admin\Admin\Services\UserService;

/**
 * @property Form $form
 */
class VerifyPage extends BasePage implements HasForms, HasActions
{
//    use InteractsWithForms;
    use InteractsWithFormActions;
    use CanAuthorizeAccess;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];
    protected static string $route_path = 'auth/verify';
    protected static string $view = 'admin_auth::filament.pages.auth.verify';
    protected static string $layout = 'admin_auth::filament.layout.auth';
    protected static ?string $title = 'اعتبار سنجی ورود';
    protected static string|array $withoutRouteMiddleware = [
        Authenticate::class
    ];
    protected AuthService $auth_service;
    protected UserService $user_service;
    protected ActLogService $user_log_service;
    public function __construct()
    {
        $this->auth_service = app(AuthService::class);
        $this->user_service = app(UserService::class);
        $this->user_log_service = app(ActLogService::class);
        $this->auth_service->doif_hasMobileOnCache(!$this->auth_service->hasMobileOnCache(), function () {
            $this->redirect(Filament::getPanel('admin')->getUrl(), true);
        });
    }

    protected static bool $shouldRegisterNavigation = false;

    public static function getRoutePath(): string
    {
        return self::$route_path;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            Action::make('change_number')
                ->label('تغییر اطلاعات کاربری')
                ->color(Color::Red)
                ->url(Filament::getPanel('admin')->getUrl())
                ->link()
        ];
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function submit()
    {
        // Verify auth code
        {
            $data = $this->form->getState();
            $this->auth_service->actVerify($data['verify_code']);
            if ($this->auth_service->hasErrors()) {
                $this->alert_error($this->auth_service->getErrorMessages()[0]);
                return null;
            }
        }

        //Get mobile number from auth_service
        {
            $this->auth_service->refreshService();
            $mobile_number = $this->auth_service->getMobileNumber();
        }

        // check if user exists
        {
            if (empty($mobile_number)) {
                $this->redirect(Filament::getPanel('admin')->getUrl(), true);
            }
        }


        //Login to panel
        {
            $this->auth_service->actSetLoggedInUser($mobile_number);
            if ($this->auth_service->getSuccessResponse()) {
                $this->user_log_service->login();
                return app(LoginResponse::class);
            }
        }
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('verify')
            ->label('ورود به پلتفرم')
            ->submit('actionVerify');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getForms(): array
    {

        $mobile = $this->auth_service->getMobileNumber();
        if (empty($mobile)) {
            $mobile = '';
        }

        $verify_code = '';
        if (env('SHOW_VERIFY_OTP',false) && !empty($mobile)) {
            $verify_code = $this->auth_service->getOTPVerifyCode($mobile);
        }

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        MobileInput::make('mobile')
                            ->label('شماره همراه')
                            ->required()
                            ->disabled()
                            ->default($mobile),
                        TextInput::make('verify_code')
                            ->label('کد ورود پیامک شده')
                            ->required()
                            ->default($verify_code)
                            ->mask('9-9-9-9-9-9')
                            ->extraAlpineAttributes(['style' => 'text-align:center'])
                    ])
                    ->statePath('data')
            )
        ];
    }


    public function mount(): void
    {
        $app=app();
        $mobile = resolve(AuthService::class)->getMobileNumber();
        if (empty($mobile)) {
            $this->redirect(Filament::getPanel('admin')->getUrl(), true);
        }
        $this->form->fill();
    }
}
