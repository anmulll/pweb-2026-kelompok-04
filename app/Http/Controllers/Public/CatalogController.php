<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService
    ) {}

    public function index(Request $request): View
    {
        $products = $this->catalogService->getCatalogProducts($request->all(), 12);
        $categories = $this->catalogService->getActiveCategories();

        return view('public.catalog.index', compact('products', 'categories'));
    }

    public function show(int|string $id): View
    {
        // Enforce SD-01: Product must be active AND category must be active, otherwise HTTP 404
        $product = $this->catalogService->findActiveProduct($id);

        if (! $product) {
            abort(404, 'Produk tidak ditemukan atau tidak tersedia.');
        }

        return view('public.catalog.show', compact('product'));
    }
}
