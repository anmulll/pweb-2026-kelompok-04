<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CHALLENGED = 'challenged';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Berhasil',
            self::CHALLENGED => 'Perlu Verifikasi',
            self::EXPIRED => 'Kadaluarsa',
            self::FAILED => 'Gagal',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::CHALLENGED => 'warning',
            self::EXPIRED => 'muted',
            self::FAILED => 'error',
            self::CANCELLED => 'error',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::PAID, self::EXPIRED, self::FAILED, self::CANCELLED], true);
    }
}
