<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    public function show(Request $request)
    {
        $user = $request->user();

        if ($request->wantsJson()) {
            return response()->json($user);
        }

        if (view()->exists('buyer.profile')) {
            return view('buyer.profile', compact('user'));
        }

        return response()->view('buyer.profile', compact('user'), 200, ['Content-Type' => 'text/html']);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'default_shipping_address' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:1000',
        ]);

        $user = $this->profileService->updateProfile($request->user(), $validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Profil berhasil diperbarui.',
                'user' => $user,
            ]);
        }

        return redirect()->route('buyer.profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
