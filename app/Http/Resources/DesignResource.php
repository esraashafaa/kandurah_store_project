<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', app()->getLocale());

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            
            // الاسم والوصف (بالترجمة)
            'name' => $this->getTranslation('name', $locale, false) ?: $this->name,
            'name_translations' => $this->getTranslations('name'),
            'description' => $this->getTranslation('description', $locale, false) ?: $this->description,
            'description_translations' => $this->getTranslations('description'),
            
            'price' => (float) $this->price,
            'is_active' => $this->is_active,
            
            // العلاقات
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'avatar' => $this->user->avatar,
                ];
            }),
            
            'images' => DesignImageResource::collection($this->whenLoaded('images')),
            'sizes' => SizeResource::collection($this->whenLoaded('sizes')),
            'design_options' => DesignOptionResource::collection($this->whenLoaded('designOptions')),
            
            // الصورة الرئيسية (للعرض السريع)
            'primary_image' => $this->whenLoaded('images', function () {
                $primary = $this->images->firstWhere('is_primary', true);
                return $primary ? new DesignImageResource($primary) : null;
            }),
            
            // التواريخ
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

