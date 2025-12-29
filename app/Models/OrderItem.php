<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'design_id',
        'size_id',
        'quantity',
        'price',
        'selected_options',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'selected_options' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Accessors
     */
    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    public function getFormattedOptionsAttribute(): array
    {
        $formatted = [];

        // المقاس (من size_id مباشرة أو من selected_options)
        $sizeId = $this->size_id ?? ($this->selected_options['size_id'] ?? null);
        if ($sizeId && $this->size) {
            $formatted['size'] = [
                'id' => $this->size->id,
                'code' => $this->size->code,
                'name' => $this->size->name,
            ];
        }

        // Design Options (من design_option_ids)
        $designOptionIds = $this->selected_options['design_option_ids'] ?? [];
        
        if (!empty($designOptionIds) && is_array($designOptionIds)) {
            $options = \App\Models\DesignOption::whereIn('id', $designOptionIds)->get();
            
            // تجميع حسب النوع
            $grouped = $options->groupBy(fn($opt) => $opt->type->value);
            
            foreach ($grouped as $type => $typeOptions) {
                $formatted[$type] = $typeOptions->map(fn($opt) => [
                    'id' => $opt->id,
                    'name' => $opt->name,
                ])->values()->toArray();
            }
        }

        // دعم الطريقة القديمة (للتوافق مع البيانات القديمة)
        if (empty($formatted) && $this->selected_options && is_array($this->selected_options)) {
            // الألوان
            if (isset($this->selected_options['color_ids']) && is_array($this->selected_options['color_ids'])) {
                $colors = \App\Models\DesignOption::whereIn('id', $this->selected_options['color_ids'])
                    ->where('type', 'color')
                    ->get();
                if ($colors->isNotEmpty()) {
                    $formatted['color'] = $colors->map(fn($opt) => [
                        'id' => $opt->id,
                        'name' => $opt->name,
                    ])->values()->toArray();
                }
            }

            // القماش
            if (isset($this->selected_options['fabric_id'])) {
                $fabric = \App\Models\DesignOption::find($this->selected_options['fabric_id']);
                if ($fabric && $fabric->type->value === 'fabric_type') {
                    $formatted['fabric_type'] = [[
                        'id' => $fabric->id,
                        'name' => $fabric->name,
                    ]];
                }
            }

            // نوع القبة
            if (isset($this->selected_options['dome_type_id'])) {
                $domeType = \App\Models\DesignOption::find($this->selected_options['dome_type_id']);
                if ($domeType && $domeType->type->value === 'dome_type') {
                    $formatted['dome_type'] = [[
                        'id' => $domeType->id,
                        'name' => $domeType->name,
                    ]];
                }
            }

            // نوع الكم
            if (isset($this->selected_options['sleeve_type_id'])) {
                $sleeveType = \App\Models\DesignOption::find($this->selected_options['sleeve_type_id']);
                if ($sleeveType && $sleeveType->type->value === 'sleeve_type') {
                    $formatted['sleeve_type'] = [[
                        'id' => $sleeveType->id,
                        'name' => $sleeveType->name,
                    ]];
                }
            }
        }

        return $formatted;
    }
}
