<?php

namespace PipLines\GetData\FinancingModel;


use Modules\Basic\BaseKit\BaseService;
use Units\Financier\FinancingMode\Common\Models\CommonFinancingModeModel;

class getAllFinancingModelData extends BaseService
{
    public function handle(): self
    {
        $data = CommonFinancingModeModel::where('active', true)->pluck('name', 'id');
        $this->setSuccessResponse($data->toArray());
        return $this;
    }

}
