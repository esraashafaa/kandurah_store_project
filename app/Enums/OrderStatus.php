<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';


    public function label(): string
    {
        return match($this) {
            self::PENDING => __('orders.status.pending'),
            self::PAID => __('orders.payment_status.paid'),
            self::CONFIRMED => __('orders.status.confirmed'),
            self::PROCESSING => __('orders.status.processing'),
            self::SHIPPED => __('orders.status.shipped'),
            self::DELIVERED => __('orders.status.delivered'),
            self::CANCELLED => __('orders.status.cancelled'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'primary',
            self::SHIPPED => 'secondary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::PAID => 'credit-card',
            self::CONFIRMED => 'check-circle',
            self::PROCESSING => 'cog',
            self::SHIPPED => 'truck',
            self::DELIVERED => 'check-double',
            self::CANCELLED => 'times-circle',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::PAID, self::CONFIRMED]);
    }

    public function isCompleted(): bool
    {
        return $this === self::DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }


    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }
        return $options;
    }

    public function next(): ?self
    {
        return match($this) {
            self::PENDING => self::PAID,
            self::PAID => self::CONFIRMED,
            self::CONFIRMED => self::PROCESSING,
            self::PROCESSING => self::SHIPPED,
            self::SHIPPED => self::DELIVERED,
            self::DELIVERED, self::CANCELLED => null,
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        // إذا الطلب ملغي، ما نقدر نغير الحالة
        if ($this === self::CANCELLED) {
            return false;
        }

        // إذا الطلب مكتمل، ما نقدر نغير الحالة
        if ($this === self::DELIVERED) {
            return false;
        }

        // يقدر يلغي من pending أو confirmed فقط
        if ($newStatus === self::CANCELLED) {
            return $this->canBeCancelled();
        }

        // الحالات الطبيعية
        $allowedTransitions = [
            self::PENDING->value => [self::PAID->value, self::CANCELLED->value],
            self::PAID->value => [self::CONFIRMED->value, self::CANCELLED->value],
            self::CONFIRMED->value => [self::PROCESSING->value, self::CANCELLED->value],
            self::PROCESSING->value => [self::SHIPPED->value],
            self::SHIPPED->value => [self::DELIVERED->value],
        ];

        return in_array($newStatus->value, $allowedTransitions[$this->value] ?? []);
    }

    /**
     * إنشاء instance من string
     */
    public static function fromString(string $value): self
    {
        return self::from($value);
    }
}