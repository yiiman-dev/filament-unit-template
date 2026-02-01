<?php

namespace Units\Shield\Manage\Policies;

use Units\Auth\Manage\Models\UserModel;
use Units\Shield\Manage\Models\ModelHasRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModelHasRolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the userModel can view any models.
     */
    public function viewAny( $userModel): bool
    {
        return $userModel->can('view_any_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can view the model.
     */
    public function view( $userModel, ModelHasRole $modelHasRole): bool
    {
        return $userModel->can('view_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can create models.
     */
    public function create( $userModel): bool
    {
        return $userModel->can('create_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can update the model.
     */
    public function update( $userModel, ModelHasRole $modelHasRole): bool
    {
        return $userModel->can('update_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can delete the model.
     */
    public function delete( $userModel, ModelHasRole $modelHasRole): bool
    {
        return $userModel->can('delete_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can bulk delete.
     */
    public function deleteAny( $userModel): bool
    {
        return $userModel->can('delete_any_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can permanently delete.
     */
    public function forceDelete( $userModel, ModelHasRole $modelHasRole): bool
    {
        return $userModel->can('force_delete_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can permanently bulk delete.
     */
    public function forceDeleteAny( $userModel): bool
    {
        return $userModel->can('force_delete_any_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can restore.
     */
    public function restore( $userModel, ModelHasRole $modelHasRole): bool
    {
        return $userModel->can('restore_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can bulk restore.
     */
    public function restoreAny( $userModel): bool
    {
        return $userModel->can('restore_any_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can replicate.
     */
    public function replicate( $userModel, ModelHasRole $modelHasRole): bool
    {
        return $userModel->can('replicate_units::shield::manage::filament::user::role');
    }

    /**
     * Determine whether the userModel can reorder.
     */
    public function reorder( $userModel): bool
    {
        return $userModel->can('reorder_units::shield::manage::filament::user::role');
    }
}
