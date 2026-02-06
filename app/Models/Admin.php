<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The default guard name for Spatie Permissions
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'role' => RoleEnum::class,
    ];

    /**
     * Scope a query to only include active admins.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Update the last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Sync permissions and trigger notification event
     * 
     * @param array|\Illuminate\Support\Collection $permissions
     * @return $this
     */
    public function syncPermissionsWithNotification($permissions)
    {
        $oldPermissions = $this->getAllPermissions()->pluck('name')->toArray();
        
        $this->syncPermissions($permissions);
        
        $newPermissions = $this->getAllPermissions()->pluck('name')->toArray();
        
        // إرسال إشعار تحديث الصلاحيات
        $currentAdmin = auth()->guard('admin')->user() ?? (auth()->user() instanceof \App\Models\Admin ? auth()->user() : null);
        event(new \App\Events\Admins\AdminPermissionsUpdated(
            $this,
            $oldPermissions,
            $newPermissions,
            $currentAdmin
        ));
        
        return $this;
    }

    /**
     * Assign role and trigger notification event if needed
     * 
     * @param string|\Spatie\Permission\Contracts\Role $role
     * @return $this
     */
    public function assignRoleWithNotification($role)
    {
        $this->assignRole($role);
        
        $permissions = $this->getAllPermissions()->pluck('name')->toArray();
        $currentAdmin = auth()->guard('admin')->user() ?? (auth()->user() instanceof \App\Models\Admin ? auth()->user() : null);
        event(new \App\Events\Admins\AdminPermissionsUpdated(
            $this,
            [],
            $permissions,
            $currentAdmin
        ));
        
        return $this;
    }
}
