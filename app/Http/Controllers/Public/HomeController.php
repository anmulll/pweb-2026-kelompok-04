<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService
    ) {}

    public function index(): View
    {
        $promotions = $this->catalogService->getRunningPromotions();
        $categories = $this->catalogService->getActiveCategories();
        $featuredProducts = $this->catalogService->getFeaturedProducts(8);

        return view('public.home', compact('promotions', 'categories', 'featuredProducts'));
    }
}
