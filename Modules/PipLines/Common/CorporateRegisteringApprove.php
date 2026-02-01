<?php

namespace PipLines\Common;

use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Basic\BaseKit\BaseService;
use Modules\Basic\BaseKit\Filament\HasNotification;
use Modules\Basic\Helpers\Helper;
use Spatie\Permission\PermissionRegistrar;
use Units\Auth\My\Enums\UserStatusEnum;
use Units\Auth\My\Enums\ValidateStatusEnum;
use Units\Auth\My\Models\UserMetadata;
use Units\Auth\My\Models\UserModel;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Placed\Common\Services\DTO\CorporateDTO;
use Units\Corporates\Placed\My\Services\CorporateService;
use Units\Corporates\Registering\Common\Enums\StatusEnum;
use Units\Corporates\Registering\Common\Models\CorporatesRegisteringModel;
use Units\Corporates\Users\Common\Enums\CorporateUserRolesEnum;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;
use Units\Shield\Common\ShieldHelper;

use Units\Shield\My\Models\ModelHasRole;

use Units\Shield\My\Models\Role;

use function Symfony\Component\String\b;

class CorporateRegisteringApprove extends BaseService
{

    public function handle(
        $corporate_service,
        $sms_service,
        $record,
        $form_data,
        $registering_service,
        $data
    ): self {
        try {
            /**
             * @var \Units\Corporates\Placed\Manage\Services\CorporateService $corporate_service
             */
            DB::beginTransaction();
            ShieldHelper::setConfig('my');
            $corporateRegisteringModel = CorporatesRegisteringModel::where('id', $record['id'])->first();
            if (empty($corporateRegisteringModel)) {
                $this->addError('به نظر میرسد رکورد ثبت نامی توسط کاربر دیگری حذف یا تایید شده است');
                return $this;
            }
            $dto = $this->createCorporate($form_data, $record['id'], $corporate_service, $corporateRegisteringModel);
            $corporateUserService = app(\Units\Corporates\Users\Manage\Services\CorporateUserService::class);
            if ($corporate_service->hasNotError()) {
                // Create user account for CEO
                // < check if CEO User exists >
                {
                    $ceoUserModel = UserModel::findByNationalCode($form_data['ceo_national_code']);
                    if (empty($ceoUserModel)) {
                        // < Create CEO User >
                        {
                            $ceoUserModel = UserModel::create([
                                'national_code' => str($form_data['ceo_national_code'])->numbers()->toString(),
                                'phone_number' => Helper::normalize_phone_number($form_data['ceo_mobile']),
                                'status' => UserStatusEnum::ACTIVE->value,
                                'validate_status' => ValidateStatusEnum::VALIDATED->value,
                                'validate_request_at' => $corporateRegisteringModel->validated_ceo_at
                            ]);

                            // < Attach profile data >
                            {
                                UserMetadata::add_meta_key(
                                    'first_name',
                                    $form_data['ceo_first_name'],
                                    str($form_data['ceo_national_code'])->numbers()->toString()
                                );
                                UserMetadata::add_meta_key(
                                    'last_name',
                                    $form_data['ceo_last_name'],
                                    str($form_data['ceo_national_code'])->numbers()->toString()
                                );
                                if (!empty($corporateRegisteringModel->meta['finnotech::ceo_inquiry-track_id'])){
                                    UserMetadata::add_meta_key('finnotech::inquiry-track_id',
                                        $corporateRegisteringModel->meta['finnotech::ceo_inquiry-track_id'],
                                        str($form_data['ceo_national_code'])->numbers()->toString()
                                    );
                                }
                                if (!empty($corporateRegisteringModel->meta['finnotech::ceo_inquiry-date'])){
                                    UserMetadata::add_meta_key('finnotech::inquiry-date',
                                        $corporateRegisteringModel->meta['finnotech::ceo_inquiry-date'],
                                        str($form_data['ceo_national_code'])->numbers()->toString()
                                    );
                                }
                                if (!empty($corporateRegisteringModel->meta['finnotech::ceo_validation_result'])){
                                    UserMetadata::add_meta_key('finnotech::validation_result',
                                        $corporateRegisteringModel->meta['finnotech::ceo_validation_result'],
                                        str($form_data['ceo_national_code'])->numbers()->toString()
                                    );
                                }
                            }
                            // </ Attach profile data >
                        }
                        // </ Create CEO User >
                    }


                    // < Attach CEO to corporate >
                    {
                        if ($ceoUserModel) {
                            if (!CorporateUsersModel::create([
                                'id' => Str::uuid(),
                                'corporate_national_code' => $form_data['national_id'],
                                'user_id' => $ceoUserModel->id,
                            ])) {
                                throw new \Exception('خطا در اتصال مدیر عامل به بنگاه ' . $record['name']);
                            }
                        } else {
                            throw new \Exception('حساب کاربری مدیر عامل ایجاد نشد');
                        }
                    }
                    // </ Attach CEO to corporate >

                    // Grant full access role to CEO for this tenant with custom role name
                    if ($ceoUserModel) {
                        app(PermissionRegistrar::class)->setPermissionClass(
                            ShieldHelper::getConfig('my', 'permission.models.permission')
                        );
                        app(PermissionRegistrar::class)->setRoleClass(
                            ShieldHelper::getConfig('my', 'permission.models.role')
                        );

                        setPermissionsTeamId($form_data['national_id']);

                        // Create/get CEO role for tenant with custom name
                        // Using raw Eloquent query instead of Role::create()
                        $ceoRole = DB::connection('my')->table('roles')->insertGetId([
                            'corporate_national_code' => $form_data['national_id'],
                            'name' => 'ceo',
                            'guard_name' => 'my',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Convert the ID to an object for compatibility
                        $ceoRole = Role::find($ceoRole);

                        // Sync all permissions to CEO role under this tenant
                        $permissionModel = config('permission.models.permission');
                        $allPermissionIds = $permissionModel::pluck('id');
                        $ceoRole->syncPermissions($allPermissionIds);

                        // Assign role to CEO user
                        ModelHasRole::create([
                            'role_id' => (int)$ceoRole->id,
                            'model_type' => UserModel::class,
                            'model_id' => $ceoUserModel->id,
                            'corporate_national_code' => $form_data['national_id']
                        ]);
                    }
                }
                // </ check if CEO User exists >


                // < check if Agent User exists >
                {
                    if (!empty($form_data['agent_national_code'])) {
                        $agentUserModel = UserModel::findByNationalCode($form_data['agent_national_code']);
                        if (empty($agentUserModel)) {
                            // < Create Agent User >
                            {
                                $agentUserModel = UserModel::create([
                                    'national_code' => str($form_data['agent_national_code'])->numbers()->toString(),
                                    'phone_number' => Helper::normalize_phone_number($form_data['agent_mobile']),
                                    'status' => UserStatusEnum::ACTIVE->value,
                                    'validate_status' => ValidateStatusEnum::VALIDATED->value,
                                    'validate_request_at' => $corporateRegisteringModel->validated_ceo_at
                                ]);

                                // < Attach profile data >
                                {
                                    UserMetadata::add_meta_key(
                                        'first_name',
                                        $form_data['agent_first_name'],
                                        str($form_data['agent_national_code'])->numbers()->toString()
                                    );
                                    UserMetadata::add_meta_key(
                                        'last_name',
                                        $form_data['agent_last_name'],
                                        str($form_data['agent_national_code'])->numbers()->toString()
                                    );
                                    if (!empty($corporateRegisteringModel->meta['finnotech::agent_inquiry-track_id'])){
                                        UserMetadata::add_meta_key('finnotech::inquiry-track_id',
                                            $corporateRegisteringModel->meta['finnotech::agent_inquiry-track_id'],
                                            str($form_data['agent_national_code'])->numbers()->toString()
                                        );
                                    }
                                    if (!empty($corporateRegisteringModel->meta['finnotech::agent_inquiry-date'])){
                                        UserMetadata::add_meta_key('finnotech::inquiry-date',
                                            $corporateRegisteringModel->meta['finnotech::agent_inquiry-date'],
                                            str($form_data['agent_national_code'])->numbers()->toString()
                                        );
                                    }
                                    if (!empty($corporateRegisteringModel->meta['finnotech::agent_validation_result'])){
                                        UserMetadata::add_meta_key('finnotech::validation_result',
                                            $corporateRegisteringModel->meta['finnotech::agent_validation_result'],
                                            str($form_data['agent_national_code'])->numbers()->toString()
                                        );
                                    }
                                }
                                // </ Attach profile data >
                            }
                            // </ Create Agent User >
                        }

                        // < Attach Agent to corporate >
                        {
                            if ($agentUserModel) {
                                if (!CorporateUsersModel::create([
                                    'id' => Str::uuid(),
                                    'corporate_national_code' => $form_data['national_id'],
                                    'user_id' => $agentUserModel->id,
                                ])) {
                                    throw new \Exception(
                                        'خطا در اتصال نماینده تام الاختیار به بنگاه ' . $record['name']
                                    );
                                }
                            } else {
                                throw new \Exception('حساب کاربری نماینده ی تام الاختیار ساختته نشد');
                            }
                        }
                        // </ Attach Agent to corporate >

                        // Grant full access role to Agent for this tenant with custom role name
                        if ($agentUserModel) {
                            app(PermissionRegistrar::class)->setPermissionClass(
                                ShieldHelper::getConfig('my', 'permission.models.permission')
                            );
                            app(PermissionRegistrar::class)->setRoleClass(
                                ShieldHelper::getConfig('my', 'permission.models.role')
                            );

                            setPermissionsTeamId($form_data['national_id']);

                            // Create/get Agent role for tenant with custom name
                            // Using raw Eloquent query instead of Role::create()
                            $agentRole = DB::connection('my')
                                ->table('roles')
                                ->insertGetId([
                                'corporate_national_code' => $form_data['national_id'],
                                'name' => 'agent',
                                'guard_name' => 'my',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // Convert the ID to an object for compatibility
                            $agentRole = Role::find($agentRole);

                            // Sync all permissions to Agent role under this tenant
                            $permissionModel = config('permission.models.permission');
                            $allPermissionIds = $permissionModel::pluck('id');
                            $agentRole->syncPermissions($allPermissionIds);

                            // Assign role to Agent user
                            ModelHasRole::create([
                                'role_id' => (int)$agentRole->id,
                                'model_type' => UserModel::class,
                                'model_id' => $agentUserModel->id,
                                'corporate_national_code' => $form_data['national_id']
                            ]);
                        }
                    }
                }
                // </ check if Agent User exists >


                // Deactivate registration form
                $registering_service->actDeactivate($record['id']);
                if ($registering_service->hasErrors()) {
                    $this->sendNotification(
                        'danger_',
                        'خطا در غیرفعال سازی فرم ثبت نام',
                        $registering_service->getErrorMessages()[0]
                    );
                    $this->addError();
                    return $this;
                }
                $this->sendNotification(
                    'success_',
                    'عملیات موفق',
                    'بنگاه با موفقیت ثبت و فعال شد و حساب کاربری برای مدیرعامل ایجاد گردید'
                );

                if ($data['send_sms']) {
                    $this->sendSMS($sms_service, $dto);
                }
                $this->setSuccessResponse();
            } else {
                $this->sendNotification('danger_', 'خطا', $corporate_service->getErrorMessages()[0]);
            }
            if (empty($this->errors)) {
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            Log::error('کرش در فعال سازی بنگاه ثبت نامی: ' . $e->getMessage(), $e->getTrace());
            DB::rollBack();
        }
        return $this;
    }

    /**
     * @param \Units\Corporates\Placed\Manage\Services\CorporateService $corporate_service
     * @return void
     */
    public function sendNotification($type, $title, $body): void
    {
        if ($type == 'danger_') {
            Notification::make($type . uniqid())
                ->danger()
                ->title($title)
                ->body($body)
                ->send();
        } else {
            Notification::make($type . uniqid())
                ->success()
                ->title($title)
                ->body($body)
                ->send();
        }
    }

    /**
     * @param string $default_password
     * @param $sms_service
     * @param CorporateDTO $dto
     * @return void
     */
    public function sendSMS($sms_service, CorporateDTO $dto): void
    {
        $text = "
                            به سامانه تامین مالی آرین خوش آمدید \n
                            بنگاه شما فعال شد و هم اکنون میتوانید با شماره همراه خود وارد پنل کاربری شوید \n
                        ";
        $model=CorporateModel::where(['national_code'=>$dto->national_code])->first();
        $sms_service->voidSend($model->ceo()->phone_number, $text);
    }

    /**
     * @param $form_data
     * @param $id
     * @param \Units\Corporates\Placed\Manage\Services\CorporateService $corporate_service
     * @return CorporateDTO
     */
    public function createCorporate(
        $form_data,
        $id,
        \Units\Corporates\Placed\Manage\Services\CorporateService $corporate_service,
        CorporatesRegisteringModel $corporateRegisteringModel
    ): CorporateDTO {

        $dto = CorporateDTO::make(
            national_code: $form_data['national_id'],
            corporates_name: $form_data['corporate_name'],
            field_of_activity: $form_data['field_of_activity'],
            birth_date: null,
            corporate_type: $form_data['corporate_type'],
            register_reference: $id,
            status: StatusEnum::ACTIVE->value,
            verification_date: $corporateRegisteringModel->validated_company_at,
            meta: [
                'finnotech::journal'=>!empty($corporateRegisteringModel->meta['finnotech::journal'])?$corporateRegisteringModel->meta['finnotech::journal']:null,
                'finnotech::journal-date'=>!empty($corporateRegisteringModel->meta['finnotech::journal-date'])?$corporateRegisteringModel->meta['finnotech::journal-date']:null,
                'finnotech::journal_error'=>!empty($corporateRegisteringModel->meta['finnotech::journal_error'])?$corporateRegisteringModel->meta['finnotech::journal_error']:null,
            ]
        );
        $corporate_service->actCreate($dto);
        if ($corporate_service->hasErrors()) {
            throw new \Exception('خطایی در ثبت بنگاه بوجود آمد');
        }
        return $dto;
    }

    /**
     * @param $form_data
     * @return array
     */
    public function createUserAccountForCEO($form_data): array
    {
        $user_service = app(\Units\Auth\My\Services\UserService::class);
        $default_password = substr(str_shuffle('0123456789'), 0, 6); // Generate 6-digit password
        $user_service->getByMobile($form_data['ceo_mobile']);
        $user_service->actCreate(
            national_code: $form_data['ceo_national_code'],
            phone_number: $form_data['ceo_mobile'],
            status: UserStatusEnum::ACTIVE->value,
            validate_status: ValidateStatusEnum::VALIDATED->value,
            created_by: 'manage_' . Filament::getCurrentPanel()->auth()->user()->phone_number
        );
        return array($user_service, $default_password);
    }
}
