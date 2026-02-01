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
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Concerns\CanAuthorizeAccess;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Basic\BaseKit\Filament\BasePage;
use Modules\Basic\Helpers\Helper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\Auth\Common\Jobs\NaturalInquiryJob;
use Units\Auth\My\Filament\Schematics\NaturalRegisterFormSchematic;
use Units\Auth\My\Services\AuthService;
use Units\Corporates\Registering\Common\DTO\NaturalCorporateRegisteringDTO;
use Units\Corporates\Registering\My\Services\CorporatesRegisteringService;
use Units\Panels\My\Middleware\MyCorporateUserMiddleware;
use Units\SMS\Common\Services\BaseSmsService;

/**
 * @url https://www.figma.com/design/9yyjiyWgnE0HA8XYVQZIiL/Arian-SCF?node-id=3603-17383&t=K6jNoZm73uCREJGz-4
 */
class NaturalPage extends BasePage implements HasForms, HasActions
{
    use InteractsWithFormActions;
    use CanAuthorizeAccess;
    use HasIcons;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string $route_path = 'register/natural';
    protected static string $view = 'my_auth::filament.pages.auth.register.natural';
    protected static string $layout = 'my_auth::filament.layout.register';
    protected static bool $shouldRegisterNavigation = false;
    protected AuthService $auth_service;
    protected CorporatesRegisteringService $register_service;
    protected BaseSmsService $sms_service;
    protected static ?string $title = 'ثبت نام شخصیت حقیقی';
    public ?string $corporate_name = null;
    public ?string $ceo_national_code = null;
    public ?string $ceo_first_name = null;
    public ?string $ceo_last_name = null;
    public ?string $ceo_phone_number = null;
    public ?string $field_of_activity = null;
    public ?string $agent_first_name = null;
    public ?string $agent_last_name = null;
    public ?string $agent_phone_number = null;
    public ?string $agent_national_code = null;

    public function __construct()
    {
        parent::__construct();
        if (auth()->hasUser()) {
            $this->redirect(filament()->getCurrentPanel()->getUrl(), true);
        }
        $this->auth_service = app(AuthService::class);
        $this->register_service = new CorporatesRegisteringService();
        $this->sms_service = new BaseSmsService();
        $this->auth_service->doif_is_not_registering(function () {
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
            Action::make('submit')
                ->label('ثبت نام در پلتفرم')
                ->submit('form'),
            Action::make('return')
                ->label('تغییر شماره همراه')
                ->color('danger')
                ->url(filament()->getPanel('my')->getLoginUrl()),
        ];
    }

    protected function getLayoutData(): array
    {
        return
            [
                'title' => 'ثبت نام شخصیت حقیقی'
            ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function submit()
    {
        DB::beginTransaction();
        $this->validate();
        $dto = NaturalCorporateRegisteringDTO::make(
            $this->ceo_first_name ?? '',
            $this->ceo_last_name ?? '',
            str($this->ceo_national_code ?? '')->numbers()->toString(),
            Helper::normalize_phone_number($this->ceo_phone_number ?? ''),
            $this->agent_first_name ?? '',
            $this->agent_last_name ?? '',
            Helper::normalize_phone_number($this->agent_phone_number ?? ''),
            str($this->agent_national_code ?? '')->numbers()->toString(),
            $this->field_of_activity ?? '',
            $this->auth_service->getMobileNumber(),
        );


        //check exists data
        {
            if ($this->register_service->exists($dto->national_id, $dto->ceo_national_code, $dto->ceo_mobile)) {
                $this->alert_error(
                    'بخشی از اطلاعات شما در پایگاه اطلاعاتی سیستم موجود است٬ لطفا اطمینان حاصل فرمایید با کد ملی و شماره همراه مشابه در سیستم حساب فعال نداشته باشید٬ در غیر اینصورت لطفا با پشتیبانی تماس حاصل فرمایید'
                );
                DB::rollBack();
                return null;
            }
        }


        $this->register_service->actCreateNatural($dto);
        if (
            $data = $this->register_service->getSuccessResponse()
        ) {
            $redirect_path = 'show/' . $data->getData('id') . '/' . $data->getData('trust_token');
            $this->alert_success('ثبت نام با موفقیت انجام شد');
            Log::info('Natural user registered: ' . '/auth/register/' . $redirect_path);

            //send SMS
            {
                $this->sms_service->voidSend(
                    $this->auth_service->getMobileNumber(),
                    __(
                        'my_auth::sms/register/natural.success.creator',
                        ['corporate_name' => $this->ceo_last_name]
                    )
                );
                $this->sms_service->voidSend(
                    $this->ceo_phone_number,
                    __(
                        'my_auth::sms/register/natural.success.invited',
                        [
                            'corporate_name' => $this->corporate_name,
                            'ceo_name' => $this->ceo_first_name . ' ' . $this->ceo_last_name,
                            'created_by' => $this->auth_service->getMobileNumber()
                        ]
                    )
                );

                if (!empty($dto->agent_mobile)){
                    $this->sms_service->voidSend(
                        $this->ceo_phone_number,
                        __(
                            'my_auth::sms/register/legal.success.invited',
                            [
                                'corporate_name' => $this->corporate_name,
                                'ceo_name' => $this->agent_first_name.' '.$this->agent_last_name,
                                'created_by' => $this->auth_service->getMobileNumber()
                            ]
                        )
                    );
                }
            }
            DB::commit();
            NaturalInquiryJob::dispatch($data->getData('id'));
            auth()->guard()->logout();
            $this->redirect($redirect_path, true);
        } else {
            DB::rollBack();
            $this->alert_error('ثبت نام با خطا مواجه شد٬ لطفا دوباره امتحان کنید');
            return null;
        }
    }

    public function form(Form $form): Form
    {
        return NaturalRegisterFormSchematic::makeForm($form)
            ->returnCommonForm();
    }


    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public static function getUrl(
        array $parameters = [],
        bool $isAbsolute = true,
        ?string $panel = null,
        ?Model $tenant = null
    ): string {
        return '/my/register/natural';
    }
}
