<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway_order_id',
        'provider',
        'status',
        'amount',
        'gateway_checkout_url',
        'gateway_token',
        'last_request_id',
        'expired_at',
        'paid_at',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'raw_response' => 'array',
            'expired_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentStatus::EXPIRED;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public function isCancelled(): bool
    {
        return $this->status === PaymentStatus::CANCELLED;
    }

    public function isTerminal(): bool
    {
        return is_object($this->status)
            ? $this->status->isTerminal()
            : in_array($this->status, [
                PaymentStatus::PAID->value,
                PaymentStatus::EXPIRED->value,
                PaymentStatus::FAILED->value,
                PaymentStatus::CANCELLED->value,
            ], true);
    }

    public function isHistorical(): bool
    {
        return $this->provider === 'midtrans_sandbox';
    }
}
