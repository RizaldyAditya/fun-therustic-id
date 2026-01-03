<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Episode;
use Illuminate\Auth\Access\HandlesAuthorization;

class EpisodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Episode');
    }

    public function view(AuthUser $authUser, Episode $episode): bool
    {
        return $authUser->can('View:Episode');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Episode');
    }

    public function update(AuthUser $authUser, Episode $episode): bool
    {
        return $authUser->can('Update:Episode');
    }

    public function delete(AuthUser $authUser, Episode $episode): bool
    {
        return $authUser->can('Delete:Episode');
    }

    public function restore(AuthUser $authUser, Episode $episode): bool
    {
        return $authUser->can('Restore:Episode');
    }

    public function forceDelete(AuthUser $authUser, Episode $episode): bool
    {
        return $authUser->can('ForceDelete:Episode');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Episode');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Episode');
    }

    public function replicate(AuthUser $authUser, Episode $episode): bool
    {
        return $authUser->can('Replicate:Episode');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Episode');
    }

}