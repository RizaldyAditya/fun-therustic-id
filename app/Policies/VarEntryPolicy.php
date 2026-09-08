<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VarEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class VarEntryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VarEntry');
    }

    public function view(AuthUser $authUser, VarEntry $varEntry): bool
    {
        return $authUser->can('View:VarEntry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VarEntry');
    }

    public function update(AuthUser $authUser, VarEntry $varEntry): bool
    {
        return $authUser->can('Update:VarEntry');
    }

    public function delete(AuthUser $authUser, VarEntry $varEntry): bool
    {
        return $authUser->can('Delete:VarEntry');
    }

    public function restore(AuthUser $authUser, VarEntry $varEntry): bool
    {
        return $authUser->can('Restore:VarEntry');
    }

    public function forceDelete(AuthUser $authUser, VarEntry $varEntry): bool
    {
        return $authUser->can('ForceDelete:VarEntry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VarEntry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VarEntry');
    }

    public function replicate(AuthUser $authUser, VarEntry $varEntry): bool
    {
        return $authUser->can('Replicate:VarEntry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VarEntry');
    }

}