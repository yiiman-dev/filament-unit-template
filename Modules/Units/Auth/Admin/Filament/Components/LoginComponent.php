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

namespace Units\Auth\Admin\Filament\Components;
use App\Console\Commands\DevOnboardingCommand;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Modules\Basic\BaseKit\Filament\HasNotification;
use Units\Auth\Admin\Filament\Pages\AuthLayoutComponent;
use Units\Auth\Admin\Services\AuthService;


/**
 * @property Form $form
 */
class LoginComponent extends AuthLayoutComponent
{
    use InteractsWithFormActions;
    use WithRateLimiting;
    use HasNotification;
    /**
     * @var view-string
     */
    protected static string $view = 'admin_auth::filament.pages.auth.login';

    protected AuthService $auth_service;

    public function __construct()
    {
        $this->auth_service=app(AuthService::class);
    }


    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            $this->redirect(Filament::getPanel('admin')->getUrl(),true);
        }

        $this->form->fill();
    }

    public function submit(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

//        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
//            $this->throwFailureValidationException();
//        }
//        $user = Filament::auth()->user();
//
//        if (
//            ($user instanceof FilamentUser) &&
//            (! $user->canAccessPanel(Filament::getCurrentPanel()))
//        ) {
//            Filament::auth()->logout();
//
//            $this->throwFailureValidationException();
//        }

        $this->auth_service->actValidate($data['username'], $data['password'], $data['mobile']);
        if ($this->auth_service->hasErrors()){
            $this->alert_error($this->auth_service->getErrorMessages()[0]);
            return null;
        }
        $this->auth_service->refreshService();
        $this->auth_service->actSendOTP($data['mobile']);
        if ($this->auth_service->hasErrors()){
            $this->alert_error('مشکلی در ارسال پیامک پیش آمد٬ لطفا مجددا امتحان فرمایید');
            return null;
        }

        $this->redirect('auth/verify',true);
        return null;
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.mobile' => 'لطفا شماره همراه را با فرمت ۰۹۱۲۰۰۰۰۰۰۰ وارد کنید',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        $defaults=[
            'mobile'=>null,
            'username'=>null,
            'password'=>null,
        ];
        if (env('ENABLE_DEFAULT_LOGIN_DETAILS',false)){
            $defaults=resolve(DevOnboardingCommand::class)->model_data['admin_user'][0];
        }
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('mobile')
                            ->label('شماره همراه')
                            ->regex('/^0\d{10}$/')
                            ->default($defaults['mobile'])
                            ->required()
                            ->autofocus(),
                        TextInput::make('username')
                            ->label('نام کاربری')
                            ->default($defaults['username'])
                            ->required(),
                        TextInput::make('password')
                            ->label('رمز عبور')
                            ->default($defaults['password'])
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('current-password')
                            ->required()
                    ])
                    ->statePath('data'),
            ),
        ];
    }




    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('filament-panels::pages/auth/login.form.remember.label'));
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::pages/auth/login.actions.register.label'))
            ->url(filament()->getRegistrationUrl());
    }

    public function getTitle(): string | Htmlable
    {
        return 'سامانه تامین مالی آرین';
    }

    public function getHeading(): string | Htmlable
    {
        return 'ورود به پنل مدیریت';
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('ارسال کد')
            ->submit('authenticate');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'mobile' => $data['mobile'],
        ];
    }
}
