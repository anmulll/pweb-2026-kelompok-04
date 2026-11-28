<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreProductRequest;
use App\Http\Requests\Seller\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\SellerCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected SellerCatalogService $catalogService
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $products = $this->catalogService->listProducts($request->all(), 10);
        $categories = Category::orderBy('name')->get();

        return view('seller.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);

        $categories = Category::active()->orderBy('name')->get();

        return view('seller.products.form', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);

        $this->catalogService->createProduct($request->validated(), $request->file('image'));

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        $categories = Category::active()->orderBy('name')->get();

        return view('seller.products.form', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $this->catalogService->updateProduct($product, $request->validated(), $request->file('image'));

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        try {
            $this->catalogService->deleteProduct($product);

            return redirect()->route('seller.products.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator);
        }
    }

    public function toggle(Product $product): RedirectResponse
    {
        Gate::authorize('toggle', $product);

        try {
            $this->catalogService->toggleProductStatus($product);

            return redirect()->back()
                ->with('success', 'Status keaktifan produk berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator);
        }
    }
}
