<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Size extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'code',
        'name',
        'sort_order',
        'is_active',
    ];

    /**
     * تحويل أنواع البيانات
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * العلاقة مع التصاميم (Many to Many)
     */
    public function designs(): BelongsToMany
    {
        return $this->belongsToMany(Design::class, 'design_size')
                    ->withTimestamps();
    }

    /**
     * Scope: المقاسات النشطة فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: ترتيب حسب الترتيب المحدد
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
