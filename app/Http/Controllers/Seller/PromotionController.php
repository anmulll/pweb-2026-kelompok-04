<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StorePromotionRequest;
use App\Http\Requests\Seller\UpdatePromotionRequest;
use App\Models\Promotion;
use App\Services\SellerCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function __construct(
        protected SellerCatalogService $catalogService
    ) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Promotion::class);

        $promotions = $this->catalogService->listPromotions(10);

        return view('seller.promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        Gate::authorize('create', Promotion::class);

        return view('seller.promotions.form');
    }

    public function store(StorePromotionRequest $request): RedirectResponse
    {
        Gate::authorize('create', Promotion::class);

        $this->catalogService->createPromotion(
            $request->user()->id,
            $request->validated(),
            $request->file('image')
        );

        return redirect()->route('seller.promotions.index')
            ->with('success', 'Promosi berhasil ditambahkan.');
    }

    public function edit(Promotion $promotion): View
    {
        Gate::authorize('update', $promotion);

        return view('seller.promotions.form', compact('promotion'));
    }

    public function update(UpdatePromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        Gate::authorize('update', $promotion);

        $this->catalogService->updatePromotion(
            $promotion,
            $request->validated(),
            $request->file('image')
        );

        return redirect()->route('seller.promotions.index')
            ->with('success', 'Promosi berhasil diperbarui.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        Gate::authorize('delete', $promotion);

        $this->catalogService->deletePromotion($promotion);

        return redirect()->route('seller.promotions.index')
            ->with('success', 'Promosi berhasil dihapus.');
    }

    public function toggle(Promotion $promotion): RedirectResponse
    {
        Gate::authorize('toggle', $promotion);

        $this->catalogService->togglePromotionStatus($promotion);

        return redirect()->back()
            ->with('success', 'Status keaktifan promosi berhasil diperbarui.');
    }
}
