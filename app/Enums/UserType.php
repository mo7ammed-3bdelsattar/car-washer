<?php

namespace App\Enums;

enum UserType: int
{
    case ADMIN = 1;
    case MANAGER = 2;
    case CUSTOMER = 3;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => __('Admin'),
            self::MANAGER => __('Manager'),
            self::CUSTOMER => __('Customer'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
