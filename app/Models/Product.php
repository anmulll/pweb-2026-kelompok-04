<?php

namespace App\Models;

use App\Enums\ConditionGrade;
use App\Enums\ProductCondition;
use App\Services\MediaStorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'model',
        'sku',
        'storage',
        'color',
        'description',
        'condition',
        'price',
        'stock',
        'image',
        'is_active',
        'condition_grade',
        'battery_health',
        'functional_status',
        'defect_notes',
        'included_accessories',
        'warranty_info',
    ];

    protected function casts(): array
    {
        return [
            'condition' => ProductCondition::class,
            'condition_grade' => ConditionGrade::class,
            'price' => 'decimal:2',
            'stock' => 'integer',
            'battery_health' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * SD-01 rule: Product is visible only if products.is_active = 1 AND categories.is_active = 1
     */
    public function scopeVisiblePublic(Builder $query): Builder
    {
        return $query->where('products.is_active', true)
            ->whereHas('category', fn (Builder $q) => $q->where('is_active', true));
    }

    public function scopeUsed(Builder $query): Builder
    {
        return $query->where('condition', ProductCondition::USED);
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('condition', ProductCondition::NEW);
    }

    public function isUsed(): bool
    {
        $conditionValue = is_object($this->condition) ? $this->condition->value : $this->condition;

        return $conditionValue === ProductCondition::USED->value;
    }

    public function isNew(): bool
    {
        $conditionValue = is_object($this->condition) ? $this->condition->value : $this->condition;

        return $conditionValue === ProductCondition::NEW->value;
    }

    public function isAvailable(): bool
    {
        $categoryActive = $this->relationLoaded('category')
            ? ($this->category?->is_active ?? true)
            : ($this->category()->value('is_active') ?? true);

        return $this->is_active && $this->stock > 0 && $categoryActive;
    }

    public function imageUrl(): ?string
    {
        return app(MediaStorageService::class)->url($this->image);
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->name);
    }

    protected function accessoriesIncluded(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->included_accessories,
            set: fn ($value) => ['included_accessories' => $value],
        );
    }

    protected function warranty(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->warranty_info,
            set: fn ($value) => ['warranty_info' => $value],
        );
    }
}
