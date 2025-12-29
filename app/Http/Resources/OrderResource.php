<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => '#' . str_pad($this->id, 6, '0', STR_PAD_LEFT),
            
            // معلومات المستخدم
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            
            // عنوان الشحن
            'location' => [
                'id' => $this->location->id,
                'city' => $this->location->city,
                'area' => $this->location->area,
                'street' => $this->location->street,
                'house_number' => $this->location->house_number,
                'full_address' => $this->location->full_address,
            ],
            
            // عناصر الطلب
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->items->count(),
            
            // المبالغ
            'total_amount' => (float) $this->total_amount,
            'total_amount_formatted' => number_format($this->total_amount, 2) . ' SAR',
            
            // الحالة
            'status' => $this->status->value,
            'status_label' => $this->status_label,
            'status_color' => $this->status->color(),
            'status_icon' => $this->status->icon(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'is_completed' => $this->isCompleted(),
            'next_status' => $this->status->next()?->value,
            'next_status_label' => $this->status->next()?->label(),
            
            // ملاحظات
            'notes' => $this->notes,
            
            // التواريخ
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}