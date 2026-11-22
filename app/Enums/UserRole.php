<?php

namespace App\Enums;

enum UserRole: string
{
    case BUYER = 'buyer';
    case SELLER = 'seller';

    public function label(): string
    {
        return match ($this) {
            self::BUYER => 'Pembeli',
            self::SELLER => 'Penjual',
        };
    }
}
