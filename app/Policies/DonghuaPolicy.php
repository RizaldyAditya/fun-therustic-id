<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Donghua;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DonghuaPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Donghua');
    }

    public function view(AuthUser $authUser, Donghua $donghua): bool
    {
        return $authUser->can('View:Donghua');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Donghua');
    }

    public function update(AuthUser $authUser, Donghua $donghua): bool
    {
        return $authUser->can('Update:Donghua');
    }

    public function delete(AuthUser $authUser, Donghua $donghua): bool
    {
        return $authUser->can('Delete:Donghua');
    }

    public function restore(AuthUser $authUser, Donghua $donghua): bool
    {
        return $authUser->can('Restore:Donghua');
    }

    public function forceDelete(AuthUser $authUser, Donghua $donghua): bool
    {
        return $authUser->can('ForceDelete:Donghua');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Donghua');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Donghua');
    }

    public function replicate(AuthUser $authUser, Donghua $donghua): bool
    {
        return $authUser->can('Replicate:Donghua');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Donghua');
    }
}
