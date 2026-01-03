<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Studio;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Studio');
    }

    public function view(AuthUser $authUser, Studio $studio): bool
    {
        return $authUser->can('View:Studio');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Studio');
    }

    public function update(AuthUser $authUser, Studio $studio): bool
    {
        return $authUser->can('Update:Studio');
    }

    public function delete(AuthUser $authUser, Studio $studio): bool
    {
        return $authUser->can('Delete:Studio');
    }

    public function restore(AuthUser $authUser, Studio $studio): bool
    {
        return $authUser->can('Restore:Studio');
    }

    public function forceDelete(AuthUser $authUser, Studio $studio): bool
    {
        return $authUser->can('ForceDelete:Studio');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Studio');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Studio');
    }

    public function replicate(AuthUser $authUser, Studio $studio): bool
    {
        return $authUser->can('Replicate:Studio');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Studio');
    }

}