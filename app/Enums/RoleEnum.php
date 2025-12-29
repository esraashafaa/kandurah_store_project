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
            self::GUEST => 'Guest',
            self::USER => 'User',
            self::ADMIN => 'Admin',
            self::SUPER_ADMIN => 'Super Admin',
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
