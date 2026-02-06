<?php

namespace App\Enums;
    
enum RoleEnum: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';
    

    public function label(): string
    {
        return match($this) {
            self::GUEST => __('admin.admins.role_admin'), // Guest uses admin translation
            self::USER => __('admin.users.normal_user'),
            self::ADMIN => __('admin.admins.role_admin'),
            self::SUPER_ADMIN => __('admin.admins.role_super_admin'),
        };
    }
    
   
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    

    public static function apiRoles(): array
    {
        return [
            self::GUEST->value,
            self::USER->value,
        ];
    }
  
    public static function dashboardRoles(): array
    {
        return [
            self::ADMIN->value,
            self::SUPER_ADMIN->value,
        ];
    }
}
