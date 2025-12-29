<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';       // قيد الانتظار
    case COMPLETED = 'completed';   // مكتمل
    case FAILED = 'failed';         // فاشل
    case CANCELLED = 'cancelled';   // ملغي

    // الحصول على جميع القيم كمصفوفة
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // الحصول على الوصف بالعربية
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::COMPLETED => 'مكتمل',
            self::FAILED => 'فاشل',
            self::CANCELLED => 'ملغي',
        };
    }

    // الحصول على اللون للواجهة
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'secondary',
        };
    }

    // الحصول على الأيقونة
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'bi-clock',
            self::COMPLETED => 'bi-check-circle',
            self::FAILED => 'bi-x-circle',
            self::CANCELLED => 'bi-dash-circle',
        };
    }

    // التحقق من حالات معينة
    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }
}