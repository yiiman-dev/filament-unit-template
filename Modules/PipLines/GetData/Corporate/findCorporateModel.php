<?php

namespace PipLines\GetData\Corporate;


use Modules\Basic\BaseKit\BaseService;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Financier\FinancingMode\Common\Models\CommonFinancingModeModel;

class findCorporateModel extends BaseService
{
    public function handle($nationalCode): self
    {
        $data = CorporateModel::where('national_code', $nationalCode)->first();
        $this->setSuccessResponse(array($data));
        return $this;
    }

}
