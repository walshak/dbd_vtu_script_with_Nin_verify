<?php

namespace App\Enums;

enum UserType: int
{
    case SUBSCRIBER = 1;
    case AGENT = 2;
    case VENDOR = 3;

    public function label(): string
    {
        return match($this) {
            self::SUBSCRIBER => 'Subscriber',
            self::AGENT => 'Agent',
            self::VENDOR => 'Vendor',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::SUBSCRIBER => 'Regular user with standard pricing',
            self::AGENT => 'Agent with better discounts and commission',
            self::VENDOR => 'Vendor with wholesale pricing',
        };
    }

    public function discountMultiplier(): float
    {
        return match($this) {
            self::SUBSCRIBER => 1.0,
            self::AGENT => 0.98, // 2% better discount
            self::VENDOR => 0.97, // 3% better discount
        };
    }
}