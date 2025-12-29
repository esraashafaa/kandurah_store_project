<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'coupon_id',
        'total_amount',
        'subtotal',
        'discount_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'status' => OrderStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Query Scopes
     */
    
    // البحث
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    // فلترة حسب الحالة
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    // فلترة حسب المستخدم (للأدمن)
    public function scopeForUser(Builder $query, ?int $userId): Builder
    {
        if (!$userId) {
            return $query;
        }

        return $query->where('user_id', $userId);
    }

    // فلترة حسب نطاق السعر
    public function scopePriceRange(Builder $query, ?float $minPrice, ?float $maxPrice): Builder
    {
        if ($minPrice) {
            $query->where('total_amount', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('total_amount', '<=', $maxPrice);
        }

        return $query;
    }

    // فلترة حسب التاريخ
    public function scopeDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    // الترتيب
    public function scopeSort(Builder $query, ?string $sortBy, ?string $sortDir): Builder
    {
        $sortBy = $sortBy ?? 'created_at';
        $sortDir = $sortDir ?? 'desc';

        $allowedSorts = ['id', 'created_at', 'total_amount', 'status'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        return $query->orderBy($sortBy, $sortDir);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof OrderStatus 
            ? $this->status->label() 
            : __('orders.status.unknown');
    }
    
    /**
     * Accessor للحصول على طريقة الدفع (مؤقت - حتى إضافة الحقل في قاعدة البيانات)
     */
    public function getPaymentMethodAttribute(): string
    {
        // يمكن إضافة logic هنا لتحديد طريقة الدفع من transactions أو wallet
        // حالياً نرجع قيمة افتراضية
        return 'cash'; // القيمة الافتراضية
    }

    /**
     * Helper Methods
     */
    public function canBeCancelled(): bool
    {
        return $this->status instanceof OrderStatus 
            ? $this->status->canBeCancelled() 
            : false;
    }

    public function isCompleted(): bool
    {
        return $this->status instanceof OrderStatus 
            ? $this->status->isCompleted() 
            : false;
    }

    public function isCancelled(): bool
    {
        return $this->status instanceof OrderStatus 
            ? $this->status->isCancelled() 
            : false;
    }
}
