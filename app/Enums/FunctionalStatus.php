<?php

namespace App\Enums;

enum FunctionalStatus: string
{
    case NORMAL = 'Normal';
    case MINUS = 'Minus';

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Semua Fungsi Normal',
            self::MINUS => 'Ada Catatan Minus',
        };
    }
}
