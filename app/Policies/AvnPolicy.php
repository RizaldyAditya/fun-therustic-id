<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Avn;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvnPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Avn');
    }

    public function view(AuthUser $authUser, Avn $avn): bool
    {
        return $authUser->can('View:Avn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Avn');
    }

    public function update(AuthUser $authUser, Avn $avn): bool
    {
        return $authUser->can('Update:Avn');
    }

    public function delete(AuthUser $authUser, Avn $avn): bool
    {
        return $authUser->can('Delete:Avn');
    }

    public function restore(AuthUser $authUser, Avn $avn): bool
    {
        return $authUser->can('Restore:Avn');
    }

    public function forceDelete(AuthUser $authUser, Avn $avn): bool
    {
        return $authUser->can('ForceDelete:Avn');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Avn');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Avn');
    }

    public function replicate(AuthUser $authUser, Avn $avn): bool
    {
        return $authUser->can('Replicate:Avn');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Avn');
    }

}