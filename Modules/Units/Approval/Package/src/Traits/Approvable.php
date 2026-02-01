<?php

namespace EightyNine\Approvals\Traits;

use App\Models\User;
use RingleSoft\LaravelProcessApproval\Models\ProcessApproval;


trait Approvable
{
    use TraitApprovable;

    public function createdBy()
    {
        return User::find($this->approvalStatus->creator_id);
    }

    /**
     * Check if Approval process is completed
     * @return bool
     */
    public function isApprovalCompleted(): bool
    {
        foreach (collect($this->approvalStatus->steps ?? []) as $index => $item) {
            if ($item['process_approval_action'] === null || $item['process_approval_id'] === null) {
                return false;
            }
        }
        return true;
    }

    public function onApprovalCompleted(ProcessApproval $approval): bool
    {
        // Write logic to be executed when the approval process is completed
        return true;
    }
}
