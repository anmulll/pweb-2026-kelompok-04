<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) (($this->product?->price ?? 0) * $this->quantity);
    }

    /**
     * An item is stale if:
     * 1. Product is not found or is_active is false
     * 2. Category is not found or is_active is false (SD-01)
     * 3. Current stock is less than cart item quantity
     */
    public function isStale(): bool
    {
        if (! $this->product) {
            return true;
        }

        if (! $this->product->is_active) {
            return true;
        }

        $category = $this->product->category;
        if (! $category || ! $category->is_active) {
            return true;
        }

        if ($this->product->stock < $this->quantity) {
            return true;
        }

        return false;
    }
}
