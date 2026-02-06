<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Measurement extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'user_id',
        'name',
        'chest',
        'waist',
        'sleeve',
        'shoulder',
        'hip',
        'height',
    ];

    /**
     * تحويل أنواع البيانات
     */
    protected $casts = [
        'chest' => 'decimal:2',
        'waist' => 'decimal:2',
        'sleeve' => 'decimal:2',
        'shoulder' => 'decimal:2',
        'hip' => 'decimal:2',
        'height' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
