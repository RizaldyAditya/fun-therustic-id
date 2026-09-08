<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SocigamesRss;
use Illuminate\Auth\Access\HandlesAuthorization;

class SocigamesRssPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SocigamesRss');
    }

    public function view(AuthUser $authUser, SocigamesRss $socigamesRss): bool
    {
        return $authUser->can('View:SocigamesRss');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SocigamesRss');
    }

    public function update(AuthUser $authUser, SocigamesRss $socigamesRss): bool
    {
        return $authUser->can('Update:SocigamesRss');
    }

    public function delete(AuthUser $authUser, SocigamesRss $socigamesRss): bool
    {
        return $authUser->can('Delete:SocigamesRss');
    }

    public function restore(AuthUser $authUser, SocigamesRss $socigamesRss): bool
    {
        return $authUser->can('Restore:SocigamesRss');
    }

    public function forceDelete(AuthUser $authUser, SocigamesRss $socigamesRss): bool
    {
        return $authUser->can('ForceDelete:SocigamesRss');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SocigamesRss');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SocigamesRss');
    }

    public function replicate(AuthUser $authUser, SocigamesRss $socigamesRss): bool
    {
        return $authUser->can('Replicate:SocigamesRss');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SocigamesRss');
    }

}