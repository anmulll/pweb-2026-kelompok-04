<?php

namespace App\Services;

use App\Enums\ProductCondition;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SellerCatalogService
{
    public function __construct(
        protected MediaStorageService $mediaStorage
    ) {}

    // ==========================================
    // Category Operations
    // ==========================================

    public function listCategories(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Category::query()->withCount('products');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function createCategory(array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueCategorySlug($data['name']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        if (! empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } elseif (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = $this->generateUniqueCategorySlug($data['name'], $category->id);
        }

        $category->update($data);

        return $category->fresh();
    }

    public function deleteCategory(Category $category): void
    {
        if ($category->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Kategori tidak dapat dihapus karena masih memiliki produk terkait.',
            ]);
        }

        $category->delete();
    }

    public function toggleCategoryStatus(Category $category): bool
    {
        $category->is_active = ! $category->is_active;
        $category->save();

        return $category->is_active;
    }

    protected function generateUniqueCategorySlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 1;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }

    // ==========================================
    // Product Operations
    // ==========================================

    public function listProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::query()->with('category');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image'] = $this->mediaStorage->upload($image, 'products');
        }

        // BR-02: Second-hand product stock is strictly 0 or 1
        if (($data['condition'] ?? null) === ProductCondition::USED->value || ($data['condition'] ?? null) === 'used') {
            $data['stock'] = min((int) ($data['stock'] ?? 1), 1);
        }

        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image'] = $this->mediaStorage->replace($product->image, $image, 'products');
        }

        // BR-02: Second-hand product stock is strictly 0 or 1
        $condition = $data['condition'] ?? ($product->condition?->value ?? $product->condition);
        if ($condition === ProductCondition::USED->value || $condition === 'used') {
            if (isset($data['stock'])) {
                $data['stock'] = min((int) $data['stock'], 1);
            }
        }

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(Product $product): void
    {
        // FR-SEL-01: Prevent deleting product referenced by carts or orders
        if (Schema::hasTable('cart_items') && DB::table('cart_items')->where('product_id', $product->id)->exists()) {
            throw ValidationException::withMessages([
                'product' => 'Produk tidak dapat dihapus karena masih tersimpan di keranjang belanja pembeli.',
            ]);
        }

        if (Schema::hasTable('order_items') && DB::table('order_items')->where('product_id', $product->id)->exists()) {
            throw ValidationException::withMessages([
                'product' => 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi pesanan.',
            ]);
        }

        if ($product->image) {
            $this->mediaStorage->delete($product->image);
        }

        $product->delete();
    }

    public function toggleProductStatus(Product $product): bool
    {
        $newStatus = ! $product->is_active;

        // BR-02: Used product must have photo, condition grade, and functional status before activating
        if ($newStatus && $product->isUsed()) {
            if (empty($product->image) || empty($product->condition_grade) || empty($product->functional_status)) {
                throw ValidationException::withMessages([
                    'product' => 'Unit Second wajib memiliki foto, grade fisik, dan status fungsi sebelum diaktifkan.',
                ]);
            }
        }

        $product->is_active = $newStatus;
        $product->save();

        return $product->is_active;
    }

    // ==========================================
    // Promotion Operations
    // ==========================================

    public function listPromotions(int $perPage = 10): LengthAwarePaginator
    {
        return Promotion::query()->with('creator')->latest('id')->paginate($perPage);
    }

    public function createPromotion(int $userId, array $data, ?UploadedFile $image = null): Promotion
    {
        if ($image) {
            $data['image'] = $this->mediaStorage->upload($image, 'promotions');
        }

        $data['created_by'] = $userId;

        return Promotion::create($data);
    }

    public function updatePromotion(Promotion $promotion, array $data, ?UploadedFile $image = null): Promotion
    {
        if ($image) {
            $data['image'] = $this->mediaStorage->replace($promotion->image, $image, 'promotions');
        }

        $promotion->update($data);

        return $promotion->fresh();
    }

    public function deletePromotion(Promotion $promotion): void
    {
        if ($promotion->image) {
            $this->mediaStorage->delete($promotion->image);
        }

        $promotion->delete();
    }

    public function togglePromotionStatus(Promotion $promotion): bool
    {
        $promotion->is_active = ! $promotion->is_active;
        $promotion->save();

        return $promotion->is_active;
    }
}
