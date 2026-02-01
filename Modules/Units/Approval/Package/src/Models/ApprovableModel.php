<?php

namespace EightyNine\Approvals\Models;

use EightyNine\Approvals\Traits\Approvable;
use Illuminate\Database\Eloquent\Model;
use Modules\Basic\BaseKit\Model\BaseSqlModel;
use RingleSoft\LaravelProcessApproval\Contracts\ApprovableModel as ContractsApprovableModel;
use Units\Approval\Package\src\Contracts\ApprovableAttributes;

abstract class ApprovableModel extends BaseSqlModel implements ContractsApprovableModel,ApprovableAttributes
{
    use Approvable;


}
