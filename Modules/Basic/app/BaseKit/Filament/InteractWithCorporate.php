<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/7/25, 3:49 PM
 */

namespace Modules\Basic\BaseKit\Filament;

use Modules\Basic\Helpers\Helper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\Auth\My\Models\UserModel;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Users\Common\Enums\CorporateUserRolesEnum;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;

use function Livewire\of;

trait InteractWithCorporate
{
    public function getCorporateNationalCode()
    {
        return  Helper::getMyPanelCurrentCorporate()->national_code;
    }

    /**
     * This will change current corporate on session
     * @return void
     */
    public function setCurrentCorporate($corporate_national_code)
    {
        Helper::setCurrentCorporate($corporate_national_code);
    }

    public function setCurrentUserCorporate($corporate_national_code)
    {
        session()->put('corporate_national_code',$corporate_national_code);
    }

    /**
     * @return CorporateModel|null
     */
    public function getCorporateModel(): CorporateModel|null
    {
            return Helper::getMyPanelCurrentCorporate();
    }

    /**
     * گرفتن کاربران کورپوریت با یا بدون اعلام نقش آنها
     * @param $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCorporateUsers($role = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = CorporateUsersModel::where('national_code', $this->getCorporateNationalCode());
        if ($role) {
            $query->where('rule_of_user', $role);
        }
        return $query->get();
    }

    /**
     * دریافت مدل مدیرعامل بنگاه
     * @return UserModel|null
     */
    public function getCorporateCEOModel(): UserModel|null
    {
        $corporate_user_collection = $this->getCorporateUsers(CorporateUserRolesEnum::CEO->value);
        if (!empty($corporate_user_model = $corporate_user_collection->first())) {
            $user_id = $corporate_user_model->user_id;
            return UserModel::first(['id' => $user_id]);
        }
        return null;
    }

    public function getCurrentCorporateName()
    {
        return $this->getCorporateModel()?->corporates_name;
    }


}
