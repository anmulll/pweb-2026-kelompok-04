<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name_snapshot',
        'sku_snapshot',
        'condition_snapshot',
        'condition_grade_snapshot',
        'battery_health_snapshot',
        'functional_status_snapshot',
        'defect_notes_snapshot',
        'included_accessories_snapshot',
        'warranty_info_snapshot',
        'unit_price_snapshot',
        'quantity',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_snapshot' => 'decimal:2',
            'quantity' => 'integer',
            'line_total' => 'decimal:2',
            'battery_health_snapshot' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
