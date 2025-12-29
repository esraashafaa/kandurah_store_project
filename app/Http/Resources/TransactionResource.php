<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'amount' => (float) $this->amount,
            'formatted_amount' => number_format($this->amount, 2) . ' ريال',
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'description' => $this->description,
            'stripe_session_id' => $this->stripe_session_id,
            'payment_intent' => $this->payment_intent,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Get type label in Arabic
     */
    private function getTypeLabel(): string
    {
        return match($this->type) {
            'deposit' => 'إيداع',
            'withdrawal' => 'سحب',
            'purchase' => 'شراء',
            'refund' => 'استرداد',
            default => $this->type,
        };
    }

    /**
     * Get status label in Arabic
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }
}

