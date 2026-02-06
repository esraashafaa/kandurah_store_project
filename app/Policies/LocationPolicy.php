<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LocationPolicy
{
 
    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Location $location): bool
    {
        if ($user instanceof User) {
            return $user->id === $location->user_id || $this->isAdmin($user);
        }
        return $this->isAdmin($user);
    }

    public function create($user): bool
    {
        if ($user instanceof User) {
            $locationsCount = Location::where('user_id', $user->id)->count();
            return $locationsCount < 10;
        }
        return false;
    }


    public function update($user, Location $location): bool|Response
    {
        if ($user instanceof User && $user->id === $location->user_id) {
            return true;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        return Response::deny('You do not own this location.');
    }


    public function delete($user, Location $location): bool|Response
    {
        if ($user instanceof User && $user->id === $location->user_id) {
            return true;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        return Response::deny('You cannot delete this location.');
    }


    public function restore($user, Location $location): bool
    {
        return $this->isAdmin($user);
    }

  
    public function forceDelete($user, Location $location): bool
    {
        return $this->isAdmin($user);
    }


    private function isAdmin($user): bool
    {
        return $user instanceof Admin;
    }

    private function owns($user, Location $location): bool
    {
        return $user instanceof User && $user->id === $location->user_id;
    }


    public function before($user, string $ability): ?bool
    {
        if ($user instanceof Admin && $user->role->value === 'super_admin') {
            return true;
        }
        return null;
    }
}