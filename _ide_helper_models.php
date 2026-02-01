<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $phone_number
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 */
	class User extends \Eloquent {}
}

namespace Modules\Basic\BaseKit\Model{
/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 * @mixin \Eloquent
 */
	class BaseModel extends \Eloquent {}
}

namespace Modules\Basic\BaseKit\Model{
/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMongoModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMongoModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMongoModel query()
 * @mixin \Eloquent
 */
	class BaseMongoModel extends \Eloquent {}
}

namespace Modules\Basic\BaseKit\Model{
/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseSqlModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseSqlModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseSqlModel query()
 * @mixin \Eloquent
 */
	class BaseSqlModel extends \Eloquent {}
}

namespace Modules\FilamentAdmin\Models{
/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseAdminModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseAdminModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseAdminModel query()
 * @mixin \Eloquent
 */
	class BaseAdminModel extends \Eloquent {}
}

namespace Modules\FilamentAdmin\Models{
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereUpdatedAt($value)
 * @property string $national_code
 * @property string $phone_number
 * @property int $status
 * @property int $validate_status check phone number and national code is for validated person
 * @property string $validate_request_at
 * @property string|null $deleted_at
 * @property string $created_by
 * @property string $deleted_by
 * @property string $deleted_reason
 * @property string $username
 * @property string $password_hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereDeletedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereNationalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereValidateRequestAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereValidateStatus($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel wherePasswordHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\Admin\Models\UserModel whereUsername($value)
 */
	class UserModel extends \Eloquent {}
}

namespace Modules\FilamentMy\Models{
/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMyModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMyModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMyModel query()
 * @mixin \Eloquent
 */
	class BaseMyModel extends \Eloquent {}
}

namespace Modules\FilamentMy\Models{
/**
 * مدل ثبت‌نام شرکت‌ها
 *
 * این مدل برای ذخیره اطلاعات ثبت‌نام شرکت‌ها و اشخاص حقیقی استفاده می‌شود
 *
 * @property string $id شناسه یکتا (UUID)
 * @property string $name نام شرکت
 * @property string $national_id شناسه ملی شرکت
 * @property string $ceo_name نام مدیرعامل
 * @property string $ceo_national_code کد ملی مدیرعامل
 * @property string $ceo_mobile شماره همراه مدیرعامل
 * @property int $field_of_activity حوزه فعالیت
 * @property int $corporate_type نوع شخصیت (حقیقی/حقوقی)
 * @property string $trust_token توکن اعتماد (UUID)
 * @property string $created_by ایجاد کننده
 * @property \DateTime $created_at زمان ایجاد
 * @property \DateTime $updated_at زمان بروزرسانی
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel query()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereCeoMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereCeoName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereCeoNationalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereCorporateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereFieldOfActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereTrustToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Corporates\Registering\My\Models\CorporatesRegisteringModel whereUpdatedAt($value)
 */
	class CorporatesRegistering extends \Eloquent {}
}

namespace Modules\FilamentMy\Models{use Illuminate\Contracts\Support\Arrayable;use Symfony\Component\HttpFoundation\Request as SymfonyRequest;use Units\Corporates\Placed\Common\Models\CorporateModel;
/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $national_code
 * @property string $phone_number
 * @property int $status
 * @property int $validate_status check phone number and national code is for validated person
 * @property string|null $validate_request_at
 * @property string|null $deleted_at
 * @property string $created_by
 * @property string $deleted_by
 * @property string $deleted_reason
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereDeletedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereNationalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereValidateRequestAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\Units\Auth\My\Models\UserModel whereValidateStatus($value)
 */
	class UserModel extends \Eloquent {}

    /**
     * @param null|CorporateModel $corporate
     * @method array validate(array $rules, ...$params)
     * @method array validateWithBag(string $errorBag, array $rules, ...$params)
     * @method bool hasValidSignature(bool $absolute = true)
     * @method bool hasValidRelativeSignature()
     * @method bool hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
     * @method bool hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
     */
    class Request extends SymfonyRequest implements Arrayable, ArrayAccess{}


}

namespace Units\Shield\Manage\Models{

    /**
     *
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
     * @mixin \Eloquent
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role wherePhoneNumber($value)
     */
    class Role extends \Spatie\Permission\Models\Role{}
}
