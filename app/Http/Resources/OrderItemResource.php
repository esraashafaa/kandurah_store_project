<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            // معلومات التصميم
            'design' => [
                'id' => $this->design->id,
                'name' => $this->design->name,
                'description' => $this->design->description,
                'image' => $this->design->images->first()?->image_url ?? null,
                'creator' => [
                    'id' => $this->design->user->id,
                    'name' => $this->design->user->name,
                ],
            ],
            
            // المقاس
            'size' => $this->when($this->size_id, [
                'id' => $this->size->id ?? null,
                'code' => $this->size->code ?? null,
                'name' => $this->size->name ?? null,
            ]),
            'size_id' => $this->size_id,
            
            // الكمية والسعر
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'price_formatted' => number_format($this->price, 2) . ' SAR',
            'subtotal' => (float) $this->subtotal,
            'subtotal_formatted' => number_format($this->subtotal, 2) . ' SAR',
            
            // الخيارات المختارة
            'selected_options' => $this->selected_options,
            'formatted_options' => $this->formatted_options,
            
            // Design Options المختارة (مفصلة ومجمعة حسب النوع)
            'design_options' => $this->when(
                !empty($this->selected_options['design_option_ids']),
                function () {
                    $options = \App\Models\DesignOption::whereIn('id', $this->selected_options['design_option_ids'] ?? [])
                        ->get();
                    
                    // تجميع حسب النوع
                    $grouped = $options->groupBy(fn($opt) => $opt->type->value);
                    
                    return $grouped->map(function ($typeOptions, $type) {
                        return [
                            'type' => $type,
                            'type_label' => $typeOptions->first()->type->label(),
                            'type_label_ar' => $typeOptions->first()->type->labelAr(),
                            'options' => $typeOptions->map(fn($opt) => [
                                'id' => $opt->id,
                                'name' => $opt->name,
                            ])->values(),
                        ];
                    })->values();
                }
            ),
        ];
    }
}