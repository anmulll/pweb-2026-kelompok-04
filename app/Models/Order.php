<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'courier_name',
        'tracking_number',
        'stock_restored_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'stock_restored_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            OrderStatus::PENDING_PAYMENT,
            OrderStatus::PAID,
            OrderStatus::PROCESSING,
        ], true);
    }
}
