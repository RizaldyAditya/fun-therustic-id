<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Anime7Rss;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class Anime7RssPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Anime7Rss');
    }

    public function view(AuthUser $authUser, Anime7Rss $anime7Rss): bool
    {
        return $authUser->can('View:Anime7Rss');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Anime7Rss');
    }

    public function update(AuthUser $authUser, Anime7Rss $anime7Rss): bool
    {
        return $authUser->can('Update:Anime7Rss');
    }

    public function delete(AuthUser $authUser, Anime7Rss $anime7Rss): bool
    {
        return $authUser->can('Delete:Anime7Rss');
    }

    public function restore(AuthUser $authUser, Anime7Rss $anime7Rss): bool
    {
        return $authUser->can('Restore:Anime7Rss');
    }

    public function forceDelete(AuthUser $authUser, Anime7Rss $anime7Rss): bool
    {
        return $authUser->can('ForceDelete:Anime7Rss');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Anime7Rss');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Anime7Rss');
    }

    public function replicate(AuthUser $authUser, Anime7Rss $anime7Rss): bool
    {
        return $authUser->can('Replicate:Anime7Rss');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Anime7Rss');
    }
}
