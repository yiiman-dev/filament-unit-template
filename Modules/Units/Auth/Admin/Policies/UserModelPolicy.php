<?php

namespace Units\Auth\Admin\Policies;

use Units\Auth\Admin\Models\UserModel;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the userModel can view any models.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function viewAny( $userModel): bool
    {
        return $userModel->can('view_any_admin::users');
    }

    /**
     * Determine whether the userModel can view the model.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function view( $userModel): bool
    {
        return $userModel->can('view_admin::users');
    }

    /**
     * Determine whether the userModel can create models.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function create( $userModel): bool
    {
        return $userModel->can('create_admin::users');
    }

    /**
     * Determine whether the userModel can update the model.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function update( $userModel): bool
    {
        return $userModel->can('update_admin::users');
    }

    /**
     * Determine whether the userModel can delete the model.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function delete( $userModel): bool
    {
        return $userModel->can('delete_admin::users');
    }

    /**
     * Determine whether the userModel can bulk delete.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function deleteAny( $userModel): bool
    {
        return $userModel->can('delete_any_admin::users');
    }

    /**
     * Determine whether the userModel can permanently delete.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function forceDelete( $userModel): bool
    {
        return $userModel->can('force_delete_admin::users');
    }

    /**
     * Determine whether the userModel can permanently bulk delete.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function forceDeleteAny( $userModel): bool
    {
        return $userModel->can('force_delete_any_admin::users');
    }

    /**
     * Determine whether the userModel can restore.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function restore( $userModel): bool
    {
        return $userModel->can('restore_admin::users');
    }

    /**
     * Determine whether the userModel can bulk restore.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function restoreAny( $userModel): bool
    {
        return $userModel->can('restore_any_admin::users');
    }

    /**
     * Determine whether the userModel can bulk restore.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function replicate( $userModel): bool
    {
        return $userModel->can('replicate_admin::users');
    }

    /**
     * Determine whether the userModel can reorder.
     *
     * @param  \Units\Auth\Admin\Models\UserModel  $userModel
     * @return bool
     */
    public function reorder( $userModel): bool
    {
        return $userModel->can('reorder_admin::users');
    }
}
