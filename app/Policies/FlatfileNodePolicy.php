<?php

namespace App\Policies;

use Units\Auth\Manage\Models\UserModel;
use Guava\FilamentKnowledgeBase\Models\FlatfileNode;
use Illuminate\Auth\Access\HandlesAuthorization;

class FlatfileNodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the userModel can view any models.
     */
    public function viewAny( $userModel): bool
    {
        return $userModel->can('view_any_documentation');
    }

    /**
     * Determine whether the userModel can view the model.
     */
    public function view( $userModel, FlatfileNode $flatfileNode): bool
    {
        return $userModel->can('view_documentation');
    }

    /**
     * Determine whether the userModel can create models.
     */
    public function create( $userModel): bool
    {
        return $userModel->can('create_documentation');
    }

    /**
     * Determine whether the userModel can update the model.
     */
    public function update( $userModel, FlatfileNode $flatfileNode): bool
    {
        return $userModel->can('update_documentation');
    }

    /**
     * Determine whether the userModel can delete the model.
     */
    public function delete( $userModel, FlatfileNode $flatfileNode): bool
    {
        return $userModel->can('delete_documentation');
    }

    /**
     * Determine whether the userModel can bulk delete.
     */
    public function deleteAny( $userModel): bool
    {
        return $userModel->can('delete_any_documentation');
    }

    /**
     * Determine whether the userModel can permanently delete.
     */
    public function forceDelete( $userModel, FlatfileNode $flatfileNode): bool
    {
        return $userModel->can('force_delete_documentation');
    }

    /**
     * Determine whether the userModel can permanently bulk delete.
     */
    public function forceDeleteAny( $userModel): bool
    {
        return $userModel->can('force_delete_any_documentation');
    }

    /**
     * Determine whether the userModel can restore.
     */
    public function restore( $userModel, FlatfileNode $flatfileNode): bool
    {
        return $userModel->can('restore_documentation');
    }

    /**
     * Determine whether the userModel can bulk restore.
     */
    public function restoreAny( $userModel): bool
    {
        return $userModel->can('restore_any_documentation');
    }

    /**
     * Determine whether the userModel can replicate.
     */
    public function replicate( $userModel, FlatfileNode $flatfileNode): bool
    {
        return $userModel->can('replicate_documentation');
    }

    /**
     * Determine whether the userModel can reorder.
     */
    public function reorder( $userModel): bool
    {
        return $userModel->can('reorder_documentation');
    }
}
