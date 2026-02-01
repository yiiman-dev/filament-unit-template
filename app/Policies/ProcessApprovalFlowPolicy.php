<?php

namespace App\Policies;

use Units\Auth\Manage\Models\UserModel;
use RingleSoft\LaravelProcessApproval\Models\ProcessApprovalFlow;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcessApprovalFlowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the userModel can view any models.
     */
    public function viewAny( $userModel): bool
    {
        return $userModel->can('view_any_approval::flow');
    }

    /**
     * Determine whether the userModel can view the model.
     */
    public function view( $userModel, ProcessApprovalFlow $processApprovalFlow): bool
    {
        return $userModel->can('view_approval::flow');
    }

    /**
     * Determine whether the userModel can create models.
     */
    public function create( $userModel): bool
    {
        return $userModel->can('create_approval::flow');
    }

    /**
     * Determine whether the userModel can update the model.
     */
    public function update( $userModel, ProcessApprovalFlow $processApprovalFlow): bool
    {
        return $userModel->can('update_approval::flow');
    }

    /**
     * Determine whether the userModel can delete the model.
     */
    public function delete( $userModel, ProcessApprovalFlow $processApprovalFlow): bool
    {
        return $userModel->can('delete_approval::flow');
    }

    /**
     * Determine whether the userModel can bulk delete.
     */
    public function deleteAny( $userModel): bool
    {
        return $userModel->can('delete_any_approval::flow');
    }

    /**
     * Determine whether the userModel can permanently delete.
     */
    public function forceDelete( $userModel, ProcessApprovalFlow $processApprovalFlow): bool
    {
        return $userModel->can('force_delete_approval::flow');
    }

    /**
     * Determine whether the userModel can permanently bulk delete.
     */
    public function forceDeleteAny( $userModel): bool
    {
        return $userModel->can('force_delete_any_approval::flow');
    }

    /**
     * Determine whether the userModel can restore.
     */
    public function restore( $userModel, ProcessApprovalFlow $processApprovalFlow): bool
    {
        return $userModel->can('restore_approval::flow');
    }

    /**
     * Determine whether the userModel can bulk restore.
     */
    public function restoreAny( $userModel): bool
    {
        return $userModel->can('restore_any_approval::flow');
    }

    /**
     * Determine whether the userModel can replicate.
     */
    public function replicate( $userModel, ProcessApprovalFlow $processApprovalFlow): bool
    {
        return $userModel->can('replicate_approval::flow');
    }

    /**
     * Determine whether the userModel can reorder.
     */
    public function reorder( $userModel): bool
    {
        return $userModel->can('reorder_approval::flow');
    }
}
