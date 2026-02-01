<?php

namespace Units\Users\Admin\Manage\Policies;

use Units\Auth\Admin\Models\UserModel;
use Units\Users\Admin\Manage\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the userModel can view any models.
     */
    public function viewAny( $userModel): bool
    {
        return $userModel->can('view_any_user');
    }

    /**
     * Determine whether the userModel can view the model.
     */
    public function view( $userModel, User $user): bool
    {
        return $userModel->can('view_user');
    }

    /**
     * Determine whether the userModel can create models.
     */
    public function create( $userModel): bool
    {
        return $userModel->can('create_user');
    }

    /**
     * Determine whether the userModel can update the model.
     */
    public function update( $userModel, User $user): bool
    {
        return $userModel->can('update_user');
    }

    /**
     * Determine whether the userModel can delete the model.
     */
    public function delete( $userModel, User $user): bool
    {
        return $userModel->can('delete_user');
    }

    /**
     * Determine whether the userModel can bulk delete.
     */
    public function deleteAny( $userModel): bool
    {
        return $userModel->can('delete_any_user');
    }

    /**
     * Determine whether the userModel can permanently delete.
     */
    public function forceDelete( $userModel, User $user): bool
    {
        return $userModel->can('force_delete_user');
    }

    /**
     * Determine whether the userModel can permanently bulk delete.
     */
    public function forceDeleteAny( $userModel): bool
    {
        return $userModel->can('force_delete_any_user');
    }

    /**
     * Determine whether the userModel can restore.
     */
    public function restore( $userModel, User $user): bool
    {
        return $userModel->can('restore_user');
    }

    /**
     * Determine whether the userModel can bulk restore.
     */
    public function restoreAny( $userModel): bool
    {
        return $userModel->can('restore_any_user');
    }

    /**
     * Determine whether the userModel can replicate.
     */
    public function replicate( $userModel, User $user): bool
    {
        return $userModel->can('replicate_user');
    }

    /**
     * Determine whether the userModel can reorder.
     */
    public function reorder( $userModel): bool
    {
        return $userModel->can('reorder_user');
    }
}
