<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'discount_type',
        'expires_at',
        'is_active',
        'max_usage',
        'usage_count',
        'min_purchase',
        'description',
    ];

    protected $casts = [
        'discount' => 'decimal:2',
        'discount_type' => 'string',
        'expires_at' => 'date',
        'is_active' => 'boolean',
        'max_usage' => 'integer',
        'usage_count' => 'integer',
        'min_purchase' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع الطلبات
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * العلاقة مع المستخدمين الذين استخدموا الكوبون
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_user')
                    ->withPivot('order_id', 'used_at')
                    ->using(\App\Models\CouponUser::class);
    }

    /**
     * Query Scopes
     */
    
    // الكوبونات النشطة فقط
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // الكوبونات غير المنتهية
    public function scopeValid(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', now());
        });
    }

    // البحث بالكود
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    /**
     * Helper Methods
     */
    
    // التحقق من صحة الكوبون
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_usage && $this->usage_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    // التحقق من إمكانية استخدام الكوبون لمبلغ معين
    public function canBeUsedForAmount(float $amount): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->min_purchase && $amount < $this->min_purchase) {
            return false;
        }

        return true;
    }

    // حساب مبلغ الخصم
    public function calculateDiscount(float $amount): float
    {
        if (!$this->canBeUsedForAmount($amount)) {
            return 0;
        }

        // إذا كان الخصم نسبة مئوية (أو لم يتم تحديد النوع - للتوافق مع الكوبونات القديمة)
        if (!$this->discount_type || $this->discount_type === 'percentage') {
            return ($amount * $this->discount) / 100;
        }
        
        // إذا كان الخصم مبلغ ثابت
        // التأكد من أن مبلغ الخصم لا يتجاوز المبلغ الأصلي
        return min($this->discount, $amount);
    }

    // التحقق من أن المستخدم لم يستخدم الكوبون من قبل
    public function hasBeenUsedByUser(int $userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    // زيادة عدد مرات الاستخدام
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
