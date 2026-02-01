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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Concerns\CanAuthorizeAccess;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Basic\BaseKit\Filament\BasePage;
use Modules\Basic\BaseKit\Filament\Form\Components\NationalCodeInput;
use Modules\Basic\Helpers\Helper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\Auth\Common\Jobs\LegalInquiryJob;
use Units\Auth\Common\Jobs\NaturalInquiryJob;
use Units\Auth\My\Filament\Schematics\LegalRegisterFormSchematic;
use Units\Auth\My\Services\AuthService;
use Units\Corporates\FieldOfActivity\Common\Models\FieldOfActivityModel;
use Units\Corporates\Registering\Common\DTO\LegalCorporateRegisteringDTO;
use Units\Corporates\Registering\My\Services\CorporatesRegisteringService;
use Units\FinnoTech\Common\Jobs\MobileAndNationalCodeVerifyJob;
use Units\Panels\My\Middleware\MyCorporateUserMiddleware;
use Units\SMS\Common\Services\BaseSmsService;

/**
 * @url https://www.figma.com/design/9yyjiyWgnE0HA8XYVQZIiL/Arian-SCF?node-id=3418-9043&t=K6jNoZm73uCREJGz-4
 */
class LegalPage extends BasePage implements HasForms, HasActions
{
    use InteractsWithFormActions;
    use CanAuthorizeAccess;
    use HasIcons;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string $route_path = 'register/legal';
    protected static string $view = 'my_auth::filament.pages.auth.register.legal';
    protected static string $layout = 'my_auth::filament.layout.register';
    protected static bool $shouldRegisterNavigation = false;
    protected AuthService $auth_service;
    protected CorporatesRegisteringService $register_service;
    protected static ?string $title = 'ثبت نام شخصیت حقوقی';
    protected BaseSmsService $sms_service;

    public ?string $corporate_name = null;
    public ?string $corporate_national_id = null;
    public ?string $field_of_activity = null;
    public ?string $ceo_first_name = null;
    public ?string $ceo_last_name = null;
    public ?string $ceo_national_code = null;
    public ?string $ceo_phone_number = null;
    public ?string $agent_first_name = null;
    public ?string $agent_last_name = null;
    public ?string $agent_national_code = null;
    public ?string $agent_phone_number = null;


    public function __construct()
    {
        parent::__construct();
        if (auth()->hasUser()) {
            $this->redirect(filament()->getCurrentPanel()->getUrl(), true);
        }
        $this->auth_service = app(AuthService::class);
        $this->register_service = app(CorporatesRegisteringService::class);
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
                'title' => 'ثبت نام شخصیت حقوقی'
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


        $dto = LegalCorporateRegisteringDTO::make(
            $this->corporate_name ?? '',
            str($this->corporate_national_id ?? '')->numbers()->toString(),
            $this->ceo_first_name ?? '',
            $this->ceo_last_name ?? '',
            str($this->ceo_national_code ?? '')->numbers()->toString(),
            Helper::normalize_phone_number($this->ceo_phone_number ?? ''),
            $this->agent_first_name ?? '',
            $this->agent_last_name ?? '',
            Helper::normalize_phone_number($this->agent_phone_number ?? ''),
            str($this->agent_national_code ?? '')->numbers()->toString(),
            $this->field_of_activity ?? '',
            $this->auth_service->getMobileNumber()
        );


        //check exists data
        {
            if ($this->register_service->exists($dto->national_id, $dto->ceo_national_code, $dto->ceo_mobile)) {
                $this->alert_error(
                    'بخشی از اطلاعات شما در پایگاه اطلاعاتی سیستم موجود است٬ لطفا با پشتیبانی تماس حاصل فرمایید'
                );
                DB::rollBack();
                return null;
            }
        }


        $this->register_service->actCreate($dto);
        if (
            $data = $this->register_service->getSuccessResponse()
        ) {
            $redirect_path = 'show/' . $data->getData('id') . '/' . $data->getData('trust_token');
            $this->alert_success('ثبت نام با موفقیت انجام شد');
            Log::info('Legal user registered: ' . '/auth/register/' . $redirect_path);


            //send SMS
            {
                $this->sms_service->voidSend(
                    $this->auth_service->getMobileNumber(),
                    __(
                        'my_auth::sms/register/legal.success.creator',
                        ['corporate_name' => $this->corporate_name]
                    )
                );
                $this->sms_service->voidSend(
                    $this->ceo_phone_number,
                    __(
                        'my_auth::sms/register/legal.success.invited',
                        [
                            'corporate_name' => $this->corporate_name,
                            'ceo_name' => $this->ceo_first_name.' '.$this->ceo_last_name,
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


            $this->redirect($redirect_path, true);
            DB::commit();
            LegalInquiryJob::dispatch($data->getData('id'));
        } else {
            DB::rollBack();
            $this->alert_error('ثبت نام با خطا مواجه شد٬ لطفا دوباره امتحان کنید');
            return null;
        }
    }

    public function form(Form $form): Form
    {
        return LegalRegisterFormSchematic::makeForm($form)
            ->returnCommonForm();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('اطلاعات شرکت ( در حال ثبت نام به عنوان شخصیت حقوقی )')
                            ->footerActions([
                                \Filament\Forms\Components\Actions\Action::make('natural')
                                    ->label('تغییر به حقیقی')
                                    ->color(Color::Amber)
                                    ->url(NaturalPage::getUrl())
                            ])
                            ->collapsible()
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        TextInput::make('corporate_name')
                                            ->label('نام شرکت')
                                            ->columnSpan(12)
                                            ->required(),
                                        TextInput::make('corporate_national_code')
                                            ->columnSpan(6)
                                            ->label('شناسه ملی شرکت')
                                            ->extraAlpineAttributes([
                                                'class' => 'text-left'
                                            ])
                                            ->numeric()
                                            ->maxLength(13)
                                            ->required(),
                                        Select::make('field_of_activity')
                                            ->columnSpan(6)
                                            ->label('حوزه فعالیت اصلی شرکت‍')
                                            ->options(
                                                FieldOfActivityModel::query()->activated()->pluck(
                                                    'title',
                                                    'id'
                                                )->toArray()
                                            )
                                            ->required(),
                                    ]),

                            ]),
                        Section::make('اطلاعات مدیر عامل')
                            ->collapsible()
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        TextInput::make('ceo_name')
                                            ->label('نام و نام خانوادگی مدیرعامل')
                                            ->columnSpan(6)
                                            ->required(),
                                        TextInput::make('ceo_national_code')
                                            ->columnSpan(6)
                                            ->label('کدملی مدیر عامل')
                                            ->helperText('کدملی مدیرعامل مطابق آگهی قانونی شرکت باشد')
                                            ->required(),
                                        TextInput::make('ceo_phone_number')
                                            ->columnSpan(6)
                                            ->label('شماره همراه مدیرعامل')
                                            ->default(
                                                Helper::denormalize_phone_number($this->auth_service->getMobileNumber())
                                            )
                                            ->regex('/^0\d{10}$/')
                                            ->helperText('مالکیت شماره همراه و کدملی مطابقت داشته باشد')
                                            ->required()
                                    ])
                            ]),
                        Section::make('معرفی نماینده تام الاختیار (اختیاری)')
                            ->collapsible()
                            ->collapsed(true)
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        TextInput::make('agent_name')
                                            ->columnSpan(6)
                                            ->label('نام و نام خانوادگی')
                                            ->required(function ($get) {
                                                if (!empty($get('agent_phone_number'))) {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            }),
                                        NationalCodeInput::make('agent_national_code')
                                            ->columnSpan(6)
                                            ->required(function ($get) {
                                                if (!empty($get('agent_name'))) {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            }),
                                        TextInput::make('agent_phone_number')
                                            ->columnSpan(6)
                                            ->label('شماره همراه')
                                            ->regex('/^0\d{10}$/')
                                            ->required(function ($get) {
                                                if (!empty($get('agent_name'))) {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            })
                                        ,

                                    ])

                            ]),

                    ])
            )
        ];
    }
    public static function getUrl(
        array $parameters = [],
        bool $isAbsolute = true,
        ?string $panel = null,
        ?Model $tenant = null
    ): string {
        return '/my/register/legal';
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
