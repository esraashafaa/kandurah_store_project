<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image_url, // Uses the accessor
            'image_path' => $this->image_path,
            'sort_order' => $this->sort_order,
            'is_primary' => $this->is_primary,
        ];
    }
}

