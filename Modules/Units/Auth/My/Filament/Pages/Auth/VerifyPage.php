<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:58 PM
 */

namespace Units\Auth\My\Filament\Pages\Auth;

use App\Filament\Forms\Components\Heading;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Concerns\CanAuthorizeAccess;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Model;
use Modules\Basic\BaseKit\Filament\BasePage;
use Modules\Basic\BaseKit\Filament\InteractWithCorporate;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\Auth\My\Enums\UserStatusEnum;
use Units\Auth\My\Services\AuthService;
use Units\Auth\My\Services\UserService;
use Units\Corporates\Placed\Common\Enums\CorporateStatusEnum;
use Units\Panels\My\Middleware\MyCorporateUserMiddleware;

/**
 * @url https://www.figma.com/design/9yyjiyWgnE0HA8XYVQZIiL/Arian-SCF?node-id=3418-8944&t=4mev1uz46NNZ9PLB-4
 *
 * @property Form $form
 */
class VerifyPage extends BasePage implements HasActions, HasForms
{
    use CanAuthorizeAccess;

    //    use InteractsWithForms;
    use InteractsWithFormActions;
    use InteractWithCorporate;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string $route_path = 'auth.verify';

    protected static string $view = 'my_auth::filament.pages.auth.verify';

    protected static string $layout = 'my_auth::filament.layout.auth';

    protected static ?string $title = 'اعتبار سنجی ورود';

    protected static bool $shouldRegisterNavigation = false;



    protected static string|array $withoutRouteMiddleware = [
        Authenticate::class,
//        MyCorporateUserMiddleware::class,
    ];

    protected AuthService $auth_service;

    protected UserService $user_service;

    public function __construct()
    {
        parent::__construct();
        $this->auth_service = app(AuthService::class);
        $this->user_service = app(UserService::class);
        $this->auth_service->doif_has_notVerifyTime(function () {
            $this->redirect(Filament::getPanel('my')->getUrl(), true);
        });
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        //Based on Modules/Units/Auth/My/routes/web.php file for ignore tenanty errors
        return 'filament.my.auth.verify';
    }

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
                ->label('تغییر شماره همراه')
                ->color(Color::Red)
                ->url(Filament::getPanel('my')->getUrl())
                ->link(),
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function submit()
    {
        // Verify auth code

        $data = $this->form->getState();
        $this->auth_service->actVerify($data['verify_code']);
        if ($this->auth_service->hasErrors()) {
            $this->alert_error('کد وارد شده صحیح نیست');

            return;
        }

        // Get mobile number from auth_service

        $mobile_number = $this->auth_service->getMobileNumber();

        // check if user exists

        if (empty($mobile_number)) {
            $this->redirect(Filament::getPanel('my')->getUrl(), true);
        }

        // Start Registering

        $redirect_url = Filament::getPanel('my')->getUrl().'/';
        $userModel = $this->user_service->getByMobile($mobile_number);

        if (empty($userModel) || (!empty($userModel) && empty($userModel->getTenantIds()))) {
            $this->redirect('/my/register/select-type', true);

            return;
        }

        if ($userModel['status'] == UserStatusEnum::ACTIVE->value) {
            $this->auth_service->actSetLoggedInUser($mobile_number)->getSuccessResponse();
            $activatedCorporate=$userModel->corporates()->where('status',CorporateStatusEnum::ACTIVE)->first();
            if ($activatedCorporate){
                $this->redirect('/'.filament()->getCurrentPanel()->getId().'/corporate/'.$activatedCorporate->national_code, true);
            }else{
                $this->alert_error('حساب کاربری شما غیرفعال است٬ متاسفانه امکان ورود به سیستم را ندارید.');
                $this->auth_service->logoutUser();
                $this->redirect($redirect_url, true);
            }
        } else {
            $this->alert_error('حساب کاربری شما غیرفعال است٬ متاسفانه امکان ورود به سیستم را ندارید.');
            $this->auth_service->logoutUser();
            $this->redirect($redirect_url, true);
        }

    }

    protected function getLayoutData(): array
    {
        return
            [
                'title' => 'به سامانه جامع تامین مالی خوش آمدید',
            ];
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
        $code = '';
        $mobile = $this->auth_service->getMobileNumber();
        if (env('SHOW_VERIFY_OTP', false)) {
            $code = $this->auth_service->getVerificationCode();
        }

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('mobile')
                            ->label('شماره همراه')
                            ->required()
                            ->disabled()
                            ->extraAlpineAttributes([
                                'style' => 'text-align:left;direction:ltr',
                            ])
                            ->default($mobile),
                        Heading::make('head')
                            ->content('لطفا کد پیامک شده به شماره همراه فوق را وارد نمایید:')
                            ->extraAttributes(
                                [
                                    'style' => 'font-size:small',
                                ]
                            )
                            ->three(),
                        TextInput::make('verify_code')
                            ->label('کد ورود پیامک شده')
                            ->required()
                            ->extraAlpineAttributes([
                                'style' => 'text-align:center ',
                            ])
                            ->default($code)
                            ->mask('9-9-9-9-9-9'),
                        //                Button::make()
                        //                    ->label('تغییر شماره همراه')
                        //                    ->colors(Color::Red)
                        //                    ->url('auth/resend')
                    ])
                    ->statePath('data')
            ),
        ];
    }

    public function mount(): void
    {
        // Add debug logging to track session state on page load
        $authService = resolve(AuthService::class);
        if (empty($authService->getMobileNumber())) {
            $this->redirect(Filament::getPanel('my')->getUrl(), true);
        }
        $this->form->fill();
    }
}
