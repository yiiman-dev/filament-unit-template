<?php

namespace PipLines\GetData\Corporate;

use Filament\Facades\Filament;
use Modules\Basic\BaseKit\BaseService;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;

class FindAuthUserCorporateMy extends BaseService
{
    public function handle(): self
    {
        $authUser = Filament::getPanel('my')->auth()->user();

        $corporateUser = CorporateUsersModel::where('user_id', $authUser->id)->firstOrFail();

        $corporate = CorporateModel::where('national_code', $corporateUser->corporate_national_code)
            ->firstOrFail();

        $this->setSuccessResponse(['corporate' => $corporate]);

        return $this;
    }
}
