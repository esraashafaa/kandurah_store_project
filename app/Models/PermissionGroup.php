<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع الصلاحيات
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_group_permission',
            'permission_group_id',
            'permission_id'
        )->withTimestamps();
    }

    /**
     * الحصول على جميع الصلاحيات في المجموعة
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * مزامنة الصلاحيات مع المجموعة
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * إضافة صلاحية للمجموعة
     */
    public function addPermission(int $permissionId): void
    {
        $this->permissions()->attach($permissionId);
    }

    /**
     * إزالة صلاحية من المجموعة
     */
    public function removePermission(int $permissionId): void
    {
        $this->permissions()->detach($permissionId);
    }

    /**
     * الحصول على اسم المجموعة حسب اللغة
     */
    public function getName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $name = $this->name;
        
        // التعامل مع البيانات القديمة (JSON string)
        if (is_string($name)) {
            $name = json_decode($name, true) ?? [];
        }
        
        // إذا كان array
        if (is_array($name)) {
            return $name[$locale] ?? $name['ar'] ?? $name['en'] ?? '';
        }
        
        return '';
    }

    /**
     * الحصول على وصف المجموعة حسب اللغة
     */
    public function getDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $description = $this->description;
        
        if (!$description) {
            return null;
        }
        
        // التعامل مع البيانات القديمة (JSON string)
        if (is_string($description)) {
            $description = json_decode($description, true) ?? [];
        }
        
        // إذا كان array
        if (is_array($description)) {
            return $description[$locale] ?? $description['ar'] ?? $description['en'] ?? null;
        }
        
        return null;
    }

    /**
     * Scope للمجموعات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
