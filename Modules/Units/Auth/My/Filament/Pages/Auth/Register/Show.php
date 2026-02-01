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

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Concerns\CanAuthorizeAccess;
use Modules\Basic\BaseKit\Filament\BasePage;
use Units\Auth\My\Services\AuthService;
use Units\Corporates\Registering\My\Services\CorporatesRegisteringService;
use Units\Panels\My\Middleware\MyCorporateUserMiddleware;

/**
 * @url https://www.figma.com/design/9yyjiyWgnE0HA8XYVQZIiL/Arian-SCF?node-id=3603-17401&t=WAlvpCWyE3SMeMIQ-4
 */
class Show extends BasePage
{
    use CanAuthorizeAccess;
    public $data;
    protected static null|string $slug = 'register/show/{id}/{trust_token}';
    protected static string $view = 'my_auth::filament.pages.auth.register.show';
    protected static string $layout = 'my_auth::filament.layout.auth';

    protected static ?string $title='نمایش اطلاعات ثبت شده';
    protected static bool $shouldRegisterNavigation=false;

    protected AuthService $auth_service;
    protected CorporatesRegisteringService $register_service;



    public function __construct()
    {
        parent::__construct();
        $this->auth_service = app(AuthService::class);
        $this->register_service = app(CorporatesRegisteringService::class);
//        $this->auth_service->doif_is_not_registering(function () {
//            $this->redirect(Filament::getCurrentPanel()->getUrl(), true);
//        });
    }




    protected static string|array $withoutRouteMiddleware = [
        Authenticate::class,
        MyCorporateUserMiddleware::class
    ];



    public function mount($id=null,$trust_token=null)
    {
        if(empty($id)or empty($trust_token)){
            throw new \Exception('فاقد پارامترهای لازم');
        }
        $data=$this->register_service->getPublic($id, $trust_token);
        if (empty($data)){
            $this->alert_error(_('my_auth::pages/auth/register.show.data_not_found'));
            $this->redirect(Filament::getCurrentPanel()->getUrl(),true);
        }

        $this->data=$data;
    }


}
