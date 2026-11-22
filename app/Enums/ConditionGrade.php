<?php

namespace App\Enums;

enum ConditionGrade: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';

    public function label(): string
    {
        return match ($this) {
            self::A => 'Grade A (Mulus)',
            self::B => 'Grade B (Lecet Halus)',
            self::C => 'Grade C (Dent/Baret Terlihat)',
        };
    }
}
