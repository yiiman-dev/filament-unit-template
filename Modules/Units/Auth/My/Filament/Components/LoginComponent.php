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

namespace Units\Auth\My\Filament\Components;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Units\Auth\My\Filament\Pages\Auth\VerifyPage;
use Units\Auth\My\Filament\Pages\AuthLayoutComponent;
use Units\Auth\My\Services\AuthService;

/**
 * @property Form $form
 */
class LoginComponent extends AuthLayoutComponent
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'my_auth::filament.pages.auth.login';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            $this->redirect(Filament::getPanel('my')->getUrl(), true);
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

        app(AuthService::class)->actSendOTP($data['mobile']);

        $this->redirect('/my/'.VerifyPage::getSlug());

        return null;
    }


    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(
                __('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => $exception->minutesUntilAvailable,
                ])
            )
            ->body(
                array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __(
                    'filament-panels::pages/auth/login.notifications.throttled.body',
                    [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => $exception->minutesUntilAvailable,
                    ]
                ) : null
            )
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
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getMobileFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getLayoutData(): array
    {
        return [
            'title' => 'به سامانه جامع تامین مالی خوش آمدید',
            'sub_titles' => [
                'ورود به حساب کاربری بنگاه ها صرفا با',
                'شماره همراه مدیرعامل شرکت یا مالک کسب و کار',
                'میسر میباشد.',
            ],
        ];
    }

    protected function getMobileFormComponent()
    {

        return TextInput::make('mobile')
            ->label('شماره همراه')
            ->regex('/^0\d{10}$/')
            ->validationMessages(
                [
                    'regex' => 'شماره تلفن وارد شده باید با زبان انگلیسی و با فرمت صحیح باشد',
                ]
            )
            ->helperText('قالب شماره همراه: ۰۹۱۲۱۲۳۴۵۶۷')
            ->default(
                env('ENABLE_DEFAULT_LOGIN_DETAILS', false) ? '09350000001' : ''
            )
            ->required()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1, 'style' => 'text-align:left']);
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::pages/auth/login.actions.register.label'))
            ->url(filament()->getRegistrationUrl());
    }

    public function getTitle(): string|Htmlable
    {
        return 'سامانه تامین مالی آرین';
    }

    public function getHeading(): string|Htmlable
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
            ->label('ارسال کد پیامکی')
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
