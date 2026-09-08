<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AnimeEpisode;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnimeEpisodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AnimeEpisode');
    }

    public function view(AuthUser $authUser, AnimeEpisode $animeEpisode): bool
    {
        return $authUser->can('View:AnimeEpisode');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AnimeEpisode');
    }

    public function update(AuthUser $authUser, AnimeEpisode $animeEpisode): bool
    {
        return $authUser->can('Update:AnimeEpisode');
    }

    public function delete(AuthUser $authUser, AnimeEpisode $animeEpisode): bool
    {
        return $authUser->can('Delete:AnimeEpisode');
    }

    public function restore(AuthUser $authUser, AnimeEpisode $animeEpisode): bool
    {
        return $authUser->can('Restore:AnimeEpisode');
    }

    public function forceDelete(AuthUser $authUser, AnimeEpisode $animeEpisode): bool
    {
        return $authUser->can('ForceDelete:AnimeEpisode');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AnimeEpisode');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AnimeEpisode');
    }

    public function replicate(AuthUser $authUser, AnimeEpisode $animeEpisode): bool
    {
        return $authUser->can('Replicate:AnimeEpisode');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AnimeEpisode');
    }

}