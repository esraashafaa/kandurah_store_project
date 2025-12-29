<?php

namespace App\Enums;

enum DesignOptionTypeEnum: string
{
    case COLOR = 'color';
    case DOME_TYPE = 'dome_type';
    case FABRIC_TYPE = 'fabric_type';
    case SLEEVE_TYPE = 'sleeve_type';
    
    /**
     * الحصول على label مقروء
     */
    public function label(): string
    {
        return match($this) {
            self::COLOR => 'Color',
            self::DOME_TYPE => 'Dome Type',
            self::FABRIC_TYPE => 'Fabric Type',
            self::SLEEVE_TYPE => 'Sleeve Type',
        };
    }
     public function labelAr(): string
    {
        return match($this) {
            self::COLOR => 'اللون',
            self::DOME_TYPE => 'نوع القبة',
            self::FABRIC_TYPE => 'نوع القماش',
            self::SLEEVE_TYPE => 'نوع الأكمام',
        };
    }
    
    /**
     * الحصول على جميع القيم
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    /**
     * الحصول على خيارات للـ Select
     */
    public static function options(): array
    {
        return array_map(
            fn($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'label_ar' => $case->labelAr(),
            ],
             self::cases()
        );
    }
}