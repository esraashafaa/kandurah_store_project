<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city',
        'area',
        'street',
        'house_number',
        'lat',
        'lng',
        'is_default',
    ];

 
    protected $casts = [
        'lat' => 'decimal:8',       
        'lng' => 'decimal:8',
        'is_default' => 'boolean',   
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ═══════════════════════════════════════════════════════
    // 🔍 QUERY SCOPES (نطاقات الاستعلام)
    // ═══════════════════════════════════════════════════════

    /**
     * البحث في المواقع
     * مثال: Location::search('القاهرة')->get()
     * 
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        // إذا لم يكن هناك بحث، ارجع الاستعلام كما هو
        if (empty($search)) {
            return $query;
        }

        // البحث في المدينة، المنطقة، الشارع، واسم المستخدم
        return $query->where(function (Builder $q) use ($search) {
            $q->where('city', 'LIKE', "%{$search}%")
              ->orWhere('area', 'LIKE', "%{$search}%")
              ->orWhere('street', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                  $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * فلترة حسب المدينة
     * مثال: Location::filterByCity('القاهرة')->get()
     * 
     * @param Builder $query
     * @param string|null $city
     * @return Builder
     */
    public function scopeFilterByCity(Builder $query, ?string $city): Builder
    {
        if (empty($city)) {
            return $query;
        }

        return $query->where('city', $city);
    }

    /**
     * فلترة حسب المنطقة
     * مثال: Location::filterByArea('مدينة نصر')->get()
     * 
     * @param Builder $query
     * @param string|null $area
     * @return Builder
     */
    public function scopeFilterByArea(Builder $query, ?string $area): Builder
    {
        if (empty($area)) {
            return $query;
        }

        return $query->where('area', $area);
    }

    /**
     * الترتيب الديناميكي
     * مثال: Location::sortBy('city', 'desc')->get()
     * 
     * @param Builder $query
     * @param string $column
     * @param string $direction
     * @return Builder
     */
    public function scopeSortBy(Builder $query, string $column = 'created_at', string $direction = 'desc'): Builder
    {
       
        $allowedColumns = [
            'id',
            'city',
            'area',
            'street',
            'created_at',
            'updated_at',
        ];

       
        if (!in_array($column, $allowedColumns)) {
            $column = 'created_at';
        }

      
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($column, $direction);
    }

    /**
     * المواقع الافتراضية فقط
     * مثال: Location::onlyDefault()->get()
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * المواقع غير الافتراضية
     * مثال: Location::exceptDefault()->get()
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeExceptDefault(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }


    /**
     * الحصول على العنوان الكامل
     * مثال: $location->fullAddress
     * 
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->house_number}, {$this->street}, {$this->area}, {$this->city}";
    }

    /**
     * رابط Google Maps
     * مثال: $location->googleMapsUrl
     * 
     * @return string
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps?q={$this->lat},{$this->lng}";
    }
}