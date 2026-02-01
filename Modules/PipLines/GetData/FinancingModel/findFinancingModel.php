<?php

namespace PipLines\GetData\FinancingModel;


use Modules\Basic\BaseKit\BaseService;
use Units\Financier\FinancingMode\Common\Models\CommonFinancingModeModel;

/**
 * @property string $id
 * @property string $name
 * @property string $resource_type
 * @property string $procedure_title
 * @property string $tool
 * @property string $applicant
 * @property string $annual_loan_interest
 * @property string $loan_recipient
 * @property string $loan_payer
 * @property string $financier_fee
 * @property string $financier_fee_receiver
 * @property string $scf_fee
 * @property string $scf_fee_receiver
 * @property string $description
 * @property string $active
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $supplier_financier_fee_share
 * @property string $buyer_financier_fee_share
 * @property string $supplier_broker_fee_share
 * @property string $buyer_broker_fee_share
 */
class findFinancingModel extends BaseService
{
    public function handle($id): self
    {
        $t=new CommonFinancingModeModel();
        $data = CommonFinancingModeModel::find($id);
        $this->setSuccessResponse(array($data));
        return $this;
    }

}
