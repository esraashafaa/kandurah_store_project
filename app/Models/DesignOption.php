<?php

namespace App\Models;

use App\Enums\DesignOptionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;

class DesignOption extends Model
{
    use HasFactory, HasTranslations;
    
    public $translatable = ['name'];

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    /**
     * تحويل أنواع البيانات
     */
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'type' => DesignOptionTypeEnum::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * العلاقة مع التصاميم (Many to Many)
     */
    public function designs(): BelongsToMany
    {
        return $this->belongsToMany(Design::class, 'design_design_option')
                    ->withTimestamps();
    }

    /**
     * Scope: الخيارات النشطة فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: فلترة حسب النوع
     */
    public function scopeByType(Builder $query, string|DesignOptionTypeEnum $type): Builder
    {
        if ($type instanceof DesignOptionTypeEnum) {
            $type = $type->value;
        }
        return $query->where('type', $type);
    }
}
