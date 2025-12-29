<?php

namespace App\Services;

use App\Models\Location;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationService
{
    public function getAllLocations(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Location::with('user:id,name,email'); 

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['city'])) {
            $query->filterByCity($filters['city']);
        }

        if (!empty($filters['area'])) {
            $query->filterByArea($filters['area']);
        }


        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['is_default']) && $filters['is_default'] !== null) {
            if ($filters['is_default']) {
                $query->onlyDefault();
            } else {
                $query->exceptDefault();
            }
        }

        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->sortBy($sortColumn, $sortDirection);

        // إذا كان get_all = true، إرجاع جميع المواقع بدون pagination
        if (isset($filters['get_all']) && $filters['get_all'] === true) {
            return $query->get();
        }

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    public function getUserLocations(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Location::where('user_id', $userId);
 
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['city'])) {
            $query->filterByCity($filters['city']);
        }


        if (!empty($filters['area'])) {
            $query->filterByArea($filters['area']);
        }

        $sortColumn = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->sortBy($sortColumn, $sortDirection);

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }


    public function getLocationById(int $locationId): Location
    {
        return Location::with('user:id,name,email')->findOrFail($locationId);
    }

    public function getUserDefaultLocation(int $userId): ?Location
    {
        return Location::where('user_id', $userId)
                       ->onlyDefault()
                       ->first();
    }


    public function createLocation(int $userId, array $data): Location
    {
        return DB::transaction(function () use ($userId, $data) {
            

            if (!empty($data['is_default']) && $data['is_default']) {
                $this->unsetDefaultLocations($userId);
            }

            if (!isset($data['is_default'])) {
                $hasLocations = Location::where('user_id', $userId)->exists();
                $data['is_default'] = !$hasLocations;
            }


            $location = Location::create([
                'user_id' => $userId,
                'city' => $data['city'],
                'area' => $data['area'],
                'street' => $data['street'],
                'house_number' => $data['house_number'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'is_default' => $data['is_default'] ?? false,
            ]);

            Log::info('Location created', [
                'location_id' => $location->id,
                'user_id' => $userId,
            ]);

            return $location->load('user:id,name,email');
        });
    }

    public function updateLocation(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {
            // إذا تم تعيين الموقع كافتراضي
            if (isset($data['is_default']) && $data['is_default'] && !$location->is_default) {
                $this->unsetDefaultLocations($location->user_id);
            }

            // إذا تم إلغاء الافتراضي وكان هذا الموقع هو الوحيد، يجب منع ذلك
            if (isset($data['is_default']) && !$data['is_default'] && $location->is_default) {
                $totalLocations = Location::where('user_id', $location->user_id)->count();
                if ($totalLocations === 1) {
                    // لا يمكن إلغاء الافتراضي إذا كان هذا الموقع الوحيد
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ['is_default' => ['لا يمكن إلغاء الموقع الافتراضي إذا كان الموقع الوحيد.']]
                    );
                }
                // إذا كان هناك مواقع أخرى، قم بتعيين أول موقع آخر كافتراضي
                $this->setFirstLocationAsDefault($location->user_id, $location->id);
            }

            $location->update([
                'city' => $data['city'] ?? $location->city,
                'area' => $data['area'] ?? $location->area,
                'street' => $data['street'] ?? $location->street,
                'house_number' => $data['house_number'] ?? $location->house_number,
                'lat' => $data['lat'] ?? $location->lat,
                'lng' => $data['lng'] ?? $location->lng,
                'is_default' => $data['is_default'] ?? $location->is_default,
            ]);

            Log::info('Location updated', [
                'location_id' => $location->id,
                'user_id' => $location->user_id,
            ]);

            return $location->fresh(['user:id,name,email']);
        });
    }

    public function deleteLocation(Location $location): bool
    {
        return DB::transaction(function () use ($location) {
            $wasDefault = $location->is_default;
            $userId = $location->user_id;
            $locationId = $location->id;

            $deleted = $location->delete();

            if ($deleted && $wasDefault) {
                $this->setFirstLocationAsDefault($userId);
            }

            if ($deleted) {
                Log::info('Location deleted', [
                    'location_id' => $locationId,
                    'user_id' => $userId,
                ]);
            }

            return $deleted;
        });
    }


    private function unsetDefaultLocations(int $userId): void
    {
        Location::where('user_id', $userId)
                ->where('is_default', true)
                ->update(['is_default' => false]);
    }

    private function setFirstLocationAsDefault(int $userId, ?int $excludeLocationId = null): void
    {
        $query = Location::where('user_id', $userId);
        
        if ($excludeLocationId) {
            $query->where('id', '!=', $excludeLocationId);
        }
        
        $firstLocation = $query->orderBy('created_at', 'asc')->first();

        if ($firstLocation) {
            $firstLocation->update(['is_default' => true]);
        }
    }

    public function getLocationStats(): array
    {
        return [
            'total_locations' => Location::count(),
            'total_cities' => Location::distinct('city')->count('city'),
            'total_users_with_locations' => Location::distinct('user_id')->count('user_id'),
            'locations_by_city' => Location::select('city', DB::raw('count(*) as count'))
                                           ->groupBy('city')
                                           ->orderBy('count', 'desc')
                                           ->limit(10)
                                           ->get(),
        ];
    }
}