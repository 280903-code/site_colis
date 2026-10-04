<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AgencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAgencyAdmin();
    }

    public function view(User $user, Agency $agency): bool
    {
        return $user->isAdmin() || $user->id === $agency->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Agency $agency): bool
    {
        return $user->isAdmin() || $user->id === $agency->user_id;
    }

    public function delete(User $user, Agency $agency): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Agency $agency): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Agency $agency): bool
    {
        return $user->isAdmin();
    }
}
