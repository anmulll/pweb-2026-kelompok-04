<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CatalogService
{
    /**
     * Get active categories that have at least one active product.
     */
    public function getActiveCategories(): Collection
    {
        return Category::query()
            ->active()
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get promotions currently valid today.
     */
    public function getRunningPromotions(): Collection
    {
        return Promotion::query()
            ->currentlyRunning()
            ->latest('id')
            ->get();
    }

    /**
     * Get featured products adhering strictly to SD-01 rule.
     */
    public function getFeaturedProducts(int $limit = 8): Collection
    {
        return Product::query()
            ->visiblePublic()
            ->with('category')
            ->latest('id')
            ->take($limit)
            ->get();
    }

    /**
     * Get catalog products with search, filters, sorting, and pagination (SD-01 compliant).
     */
    public function getCatalogProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        // Enforce SD-01: products.is_active = 1 AND categories.is_active = 1
        $query = Product::query()
            ->visiblePublic()
            ->with('category');

        // Search by name or model
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by category slug
        if (! empty($filters['category'])) {
            $categorySlug = $filters['category'];
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter by condition (new / used)
        if (! empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        // Filter by condition grade (A / B / C)
        if (! empty($filters['grade'])) {
            $query->where('condition_grade', $filters['grade']);
        }

        // Filter by price range
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest('id'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Find a single product for detail page adhering strictly to SD-01 rule.
     * Returns null if product is inactive OR category is inactive.
     */
    public function findActiveProduct(int|string $idOrSku): ?Product
    {
        $query = Product::query()
            ->visiblePublic()
            ->with('category');

        if (is_numeric($idOrSku)) {
            return $query->where('id', (int) $idOrSku)->first();
        }

        return $query->where('sku', $idOrSku)->first();
    }
}
