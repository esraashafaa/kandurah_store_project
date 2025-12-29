<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;

class Design extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    /**
     * الحقول القابلة للترجمة
     */
    public $translatable = ['name', 'description'];

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'is_active',
    ];

    /**
     * تحويل أنواع البيانات
     */
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع المستخدم (صاحب التصميم)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع الصور
     */
    public function images(): HasMany
    {
        return $this->hasMany(DesignImage::class)->orderBy('sort_order');
    }

    /**
     * الصورة الرئيسية
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(DesignImage::class)->where('is_primary', true);
    }

    /**
     * العلاقة مع المقاسات (Many to Many)
     */
    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'design_size')
                    ->withTimestamps();
    }

    /**
     * العلاقة مع خيارات التصميم (Many to Many)
     */
    public function designOptions(): BelongsToMany
    {
        return $this->belongsToMany(DesignOption::class, 'design_design_option')
                    ->withTimestamps();
    }

    /**
     * العلاقة مع عناصر الطلبات (Order Items)
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope: التصاميم النشطة فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: البحث في الاسم والوصف
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
              ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"])
              ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%{$search}%"])
              ->orWhereRaw("JSON_EXTRACT(description, '$.ar') LIKE ?", ["%{$search}%"]);
        });
    }

    /**
     * Scope: فلترة حسب نطاق السعر
     */
    public function scopePriceRange(Builder $query, ?float $minPrice = null, ?float $maxPrice = null): Builder
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope: فلترة حسب المستخدم
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: فلترة حسب المقاس
     */
    public function scopeBySize(Builder $query, int|array $sizeId): Builder
    {
        if (is_array($sizeId)) {
            return $query->whereHas('sizes', function ($q) use ($sizeId) {
                $q->whereIn('sizes.id', $sizeId);
            });
        }
        return $query->whereHas('sizes', function ($q) use ($sizeId) {
            $q->where('sizes.id', $sizeId);
        });
    }

    /**
     * Scope: فلترة حسب خيار التصميم
     */
    public function scopeByDesignOption(Builder $query, int|array $optionId): Builder
    {
        if (is_array($optionId)) {
            return $query->whereHas('designOptions', function ($q) use ($optionId) {
                $q->whereIn('design_options.id', $optionId);
            });
        }
        return $query->whereHas('designOptions', function ($q) use ($optionId) {
            $q->where('design_options.id', $optionId);
        });
    }

    /**
     * Scope: استثناء تصاميم المستخدم الحالي (لعرض تصاميم الآخرين)
     */
    public function scopeExcludeUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', '!=', $userId);
    }

    /**
     * Scope: ترتيب حسب عمود معين
     */
    public function scopeSortBy(Builder $query, string $column = 'created_at', string $direction = 'desc'): Builder
    {
        $allowedColumns = ['created_at', 'updated_at', 'price', 'name'];
        $column = in_array($column, $allowedColumns) ? $column : 'created_at';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';
        
        return $query->orderBy($column, $direction);
    }
}
