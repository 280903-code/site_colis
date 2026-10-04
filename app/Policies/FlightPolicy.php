<?php

namespace App\Policies;

use App\Models\Flight;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FlightPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAgencyAdmin();
    }

    public function view(User $user, Flight $flight): bool
    {
        return $user->isAdmin() || $user->id === $flight->agency->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAgencyAdmin();
    }

    public function update(User $user, Flight $flight): bool
    {
        return $user->isAdmin() || $user->id === $flight->agency->user_id;
    }

    public function delete(User $user, Flight $flight): bool
    {
        return $user->isAdmin() || $user->id === $flight->agency->user_id;
    }

    public function restore(User $user, Flight $flight): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Flight $flight): bool
    {
        return $user->isAdmin();
    }
}
