<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Anime;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnimePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Anime');
    }

    public function view(AuthUser $authUser, Anime $anime): bool
    {
        return $authUser->can('View:Anime');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Anime');
    }

    public function update(AuthUser $authUser, Anime $anime): bool
    {
        return $authUser->can('Update:Anime');
    }

    public function delete(AuthUser $authUser, Anime $anime): bool
    {
        return $authUser->can('Delete:Anime');
    }

    public function restore(AuthUser $authUser, Anime $anime): bool
    {
        return $authUser->can('Restore:Anime');
    }

    public function forceDelete(AuthUser $authUser, Anime $anime): bool
    {
        return $authUser->can('ForceDelete:Anime');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Anime');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Anime');
    }

    public function replicate(AuthUser $authUser, Anime $anime): bool
    {
        return $authUser->can('Replicate:Anime');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Anime');
    }

}