<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StockLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockLog');
    }

    public function view(AuthUser $authUser, StockLog $stockLog): bool
    {
        return $authUser->can('View:StockLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockLog');
    }

    public function update(AuthUser $authUser, StockLog $stockLog): bool
    {
        return $authUser->can('Update:StockLog');
    }

    public function delete(AuthUser $authUser, StockLog $stockLog): bool
    {
        return $authUser->can('Delete:StockLog');
    }

    public function restore(AuthUser $authUser, StockLog $stockLog): bool
    {
        return $authUser->can('Restore:StockLog');
    }

    public function forceDelete(AuthUser $authUser, StockLog $stockLog): bool
    {
        return $authUser->can('ForceDelete:StockLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockLog');
    }

    public function replicate(AuthUser $authUser, StockLog $stockLog): bool
    {
        return $authUser->can('Replicate:StockLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockLog');
    }

}