<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ItemRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_item::request');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ItemRequest $itemRequest): bool
    {
        // Super admins and farm managers can view any request
        if ($user->hasRole('super_admin') || $user->hasRole('farm_manager')) {
            return $user->can('view_item::request');
        }

        // Regular users can only view their own requests
        return $itemRequest->user_id === $user->id && $user->can('view_item::request');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_item::request');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ItemRequest $itemRequest): bool
    {
        // Super admins and farm managers can update any request
        if ($user->hasRole('super_admin') || $user->hasRole('farm_manager')) {
            return $user->can('update_item::request');
        }

        // Regular users can only update their own requests
        return $itemRequest->user_id === $user->id && $user->can('update_item::request');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ItemRequest $itemRequest): bool
    {
        // Super admins and farm managers can delete any request
        if ($user->hasRole('super_admin') || $user->hasRole('farm_manager')) {
            return $user->can('delete_item::request');
        }

        // Regular users can only delete their own requests
        return $itemRequest->user_id === $user->id && $user->can('delete_item::request');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_item::request');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('force_delete_item::request');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_item::request');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('restore_item::request');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_item::request');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ItemRequest $itemRequest): bool
    {
        return $user->can('replicate_item::request');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_item::request');
    }
}
