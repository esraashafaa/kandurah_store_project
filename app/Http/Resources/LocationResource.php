<?php

namespace App\Http\Resources;

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
    
            'id' => $this->id,
            'city' => $this->city,
            'area' => $this->area,
            'street' => $this->street,
            'house_number' => $this->house_number,

            'coordinates' => [
                'lat' => (float) $this->lat,  
                'lng' => (float) $this->lng,
            ],

            'is_default' => (bool) $this->is_default,  

            'full_address' => $this->full_address, 
            'google_maps_url' => $this->google_maps_url,  

            'user' => UserResource::make($this->whenLoaded('user')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'user_id' => $this->when(
                $request->user() && in_array($request->user()->role, [RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]), 
                $this->user_id
            ),

            'default_since' => $this->when(
                $this->is_default,
                $this->created_at?->diffForHumans()  
            ),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }


    public function withResponse(Request $request, $response): void
    {
   
        $response->header('X-Resource-Type', 'Location');
    }
}