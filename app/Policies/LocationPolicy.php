<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LocationPolicy
{
 
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Location $location): bool
    {
 
        return $user->id === $location->user_id || $this->isAdmin($user);
    }

    public function create(User $user): bool
    {

        $locationsCount = Location::where('user_id', $user->id)->count();
        return $locationsCount < 10;

    }


    public function update(User $user, Location $location): bool|Response
    {

        if ($user->id === $location->user_id) {
            return true;
        }

    
        if ($this->isAdmin($user)) {
            return true;
        }

   
        return Response::deny('You do not own this location.');
        
   
    }


    public function delete(User $user, Location $location): bool|Response
    {
  
        if ($user->id === $location->user_id) {
            return true;
        }

        if ($this->isAdmin($user)) {
            return true;
        }


        return Response::deny('You cannot delete this location.');
    }


    public function restore(User $user, Location $location): bool
    {
        return $this->isAdmin($user);
    }

  
    public function forceDelete(User $user, Location $location): bool
    {
 
        return $this->isAdmin($user);
    }


    private function isAdmin(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN || $user->role === RoleEnum::SUPER_ADMIN;
    }

    private function owns(User $user, Location $location): bool
    {
        return $user->id === $location->user_id;
    }


    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === RoleEnum::SUPER_ADMIN) {
            return true;
        }
        return null;
    }
}