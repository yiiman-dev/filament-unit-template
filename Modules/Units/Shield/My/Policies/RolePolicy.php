<?php

namespace Units\Shield\My\Policies;

use Units\Auth\My\Models\UserModel;
use Units\Shield\My\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the userModel can view any models.
     */
    public function viewAny( $userModel): bool
    {
        return $userModel->can('view_any_role');
    }

    /**
     * Determine whether the userModel can view the model.
     */
    public function view( $userModel, Role $role): bool
    {
        return $userModel->can('view_role');
    }

    /**
     * Determine whether the userModel can create models.
     */
    public function create( $userModel): bool
    {
        return $userModel->can('create_role');
    }

    /**
     * Determine whether the userModel can update the model.
     */
    public function update( $userModel, Role $role): bool
    {
        return true;//$userModel->can('update_role');
    }

    /**
     * Determine whether the userModel can delete the model.
     */
    public function delete( $userModel, Role $role): bool
    {
        return $userModel->can('delete_role');
    }

    /**
     * Determine whether the userModel can bulk delete.
     */
    public function deleteAny( $userModel): bool
    {
        return $userModel->can('delete_any_role');
    }

    /**
     * Determine whether the userModel can permanently delete.
     */
    public function forceDelete( $userModel, Role $role): bool
    {
        return $userModel->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the userModel can permanently bulk delete.
     */
    public function forceDeleteAny( $userModel): bool
    {
        return $userModel->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the userModel can restore.
     */
    public function restore( $userModel, Role $role): bool
    {
        return $userModel->can('{{ Restore }}');
    }

    /**
     * Determine whether the userModel can bulk restore.
     */
    public function restoreAny( $userModel): bool
    {
        return $userModel->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the userModel can replicate.
     */
    public function replicate( $userModel, Role $role): bool
    {
        return $userModel->can('{{ Replicate }}');
    }

    /**
     * Determine whether the userModel can reorder.
     */
    public function reorder( $userModel): bool
    {
        return $userModel->can('{{ Reorder }}');
    }
}
