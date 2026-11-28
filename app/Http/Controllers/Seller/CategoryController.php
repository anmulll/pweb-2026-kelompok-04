<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreCategoryRequest;
use App\Http\Requests\Seller\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\SellerCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected SellerCatalogService $catalogService
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Category::class);

        $categories = $this->catalogService->listCategories($request->only('search'), 10);

        return view('seller.categories.index', compact('categories'));
    }

    public function create(): View
    {
        Gate::authorize('create', Category::class);

        return view('seller.categories.form');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Gate::authorize('create', Category::class);

        $this->catalogService->createCategory($request->validated());

        return redirect()->route('seller.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        Gate::authorize('update', $category);

        return view('seller.categories.form', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        Gate::authorize('update', $category);

        $this->catalogService->updateCategory($category, $request->validated());

        return redirect()->route('seller.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        try {
            $this->catalogService->deleteCategory($category);

            return redirect()->route('seller.categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator);
        }
    }

    public function toggle(Category $category): RedirectResponse
    {
        Gate::authorize('toggle', $category);

        $this->catalogService->toggleCategoryStatus($category);

        return redirect()->back()
            ->with('success', 'Status keaktifan kategori berhasil diperbarui.');
    }
}
