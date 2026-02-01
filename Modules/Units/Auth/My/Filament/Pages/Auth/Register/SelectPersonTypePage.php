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

namespace Units\Auth\My\Filament\Pages\Auth\Register;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Concerns\HasIcons;
use Filament\Forms\Contracts\HasForms;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Concerns\CanAuthorizeAccess;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Colors\Color;
use Modules\Basic\BaseKit\Filament\BasePage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\Auth\My\Services\AuthService;
use Units\Panels\My\Middleware\MyCorporateUserMiddleware;

/**
 * @url https://www.figma.com/design/9yyjiyWgnE0HA8XYVQZIiL/Arian-SCF?node-id=3418-8944&t=PbCuwSH1fhK41onv-4
 */
class SelectPersonTypePage extends BasePage implements HasForms, HasActions
{
    use InteractsWithFormActions;
    use CanAuthorizeAccess;
    use HasIcons;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string $route_path = 'register/select-type';
    protected static string $view = 'my_auth::filament.pages.auth.register.select_person_type';
    protected static string $layout = 'my_auth::filament.layout.auth';
    protected static ?string $title='انتخاب نوع ثبت نام';
    protected static bool $shouldRegisterNavigation=false;
    protected AuthService $auth_service;


    public function __construct()
    {
        parent::__construct();
        $this->auth_service = app(AuthService::class);
        $this->auth_service->doif_is_not_registering(function(){
            $this->redirect(Filament::getPanel('my')->getUrl(), true);
        });
    }

    protected static string|array $withoutRouteMiddleware = [
        Authenticate::class,
        MyCorporateUserMiddleware::class
    ];

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
            Action::make('legal')
                ->label('ثبت نام با شخصیت حقوقی')
                ->color(Color::Blue)
                ->icon('heroicon-m-building-office')
                ->extraAttributes(['style'=>'width:100%'])
                ->url('legal'),
            Action::make('natural')
                ->label('ثبت نام با شخصیت حقیقی')
                ->color(Color::Amber)
                ->icon('heroicon-m-user-circle')
                ->extraAttributes(['style'=>'width:100%'])
                ->url('natural')
        ];
    }

    protected function getLayoutData(): array
    {
        return
            [
                'title' => 'شما دارای حساب کاربری نیستید',
                'sub_titles' =>
                    [
                        'بدلیل تاثیر مستقیم در تحقق تامین مالی٬',
                        'لطفا در انتخاب نوع شخصیت دقت فرمایید.'
                    ],
                'pre_titles' =>
                    [
                        'شماره همراه وارد شده:',
                        '09353466620'
                    ]
            ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function submit()
    {
        $data = $this->data;
    }


    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

}
