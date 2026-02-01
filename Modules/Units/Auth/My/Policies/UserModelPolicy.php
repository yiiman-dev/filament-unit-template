<?php

namespace Units\BusinessCoworkers\Common\Policies;

use Units\Auth\Manage\Models\UserModel;
use Units\BusinessCoworkers\Common\Models\BusinessCoWorkerModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the userModel can view any models.
     */
    public function viewAny( $userModel): bool
    {
        return true;//$userModel->can('view_any_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can view the model.
     */
    public function view( $userModel, BusinessCoWorkerModel $businessCoWorkerModel): bool
    {
        return true;//$userModel->can('view_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can create models.
     */
    public function create( $userModel): bool
    {
        return true;//$userModel->can('create_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can update the model.
     */
    public function update( $userModel, BusinessCoWorkerModel $businessCoWorkerModel): bool
    {
        return true;//$userModel->can('update_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can delete the model.
     */
    public function delete( $userModel, BusinessCoWorkerModel $businessCoWorkerModel): bool
    {
        return true;//$userModel->can('delete_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can bulk delete.
     */
    public function deleteAny( $userModel): bool
    {
        return true;//$userModel->can('delete_any_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can permanently delete.
     */
    public function forceDelete( $userModel, BusinessCoWorkerModel $businessCoWorkerModel): bool
    {
        return true;//$userModel->can('force_delete_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can permanently bulk delete.
     */
    public function forceDeleteAny( $userModel): bool
    {
        return true;//$userModel->can('force_delete_any_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can restore.
     */
    public function restore( $userModel, BusinessCoWorkerModel $businessCoWorkerModel): bool
    {
        return true;//$userModel->can('restore_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can bulk restore.
     */
    public function restoreAny( $userModel): bool
    {
        return true;//$userModel->can('restore_any_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can replicate.
     */
    public function replicate( $userModel, BusinessCoWorkerModel $businessCoWorkerModel): bool
    {
        return true;//$userModel->can('replicate_business::co::worker::graph');
    }

    /**
     * Determine whether the userModel can reorder.
     */
    public function reorder( $userModel): bool
    {
        return true;//$userModel->can('reorder_business::co::worker::graph');
    }
}
