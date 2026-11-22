<?php

namespace App\Http\Requests\Buyer;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $productId = $this->input('product_id');
            $quantity = (int) $this->input('quantity', 1);

            if (! $productId) {
                return;
            }

            $product = Product::with('category')->find($productId);
            if (! $product) {
                $validator->errors()->add('product_id', 'Produk tidak ditemukan.');
                return;
            }

            // SD-01 rule check
            if (! $product->is_active || ! ($product->category?->is_active ?? false)) {
                $validator->errors()->add('product_id', 'Produk ini sedang tidak tersedia.');
                return;
            }

            // Second-hand unit quantity must be 1 max
            if ($product->isUsed() && $quantity > 1) {
                $validator->errors()->add('quantity', 'Produk unit second hanya dapat dibeli 1 unit.');
                return;
            }

            // Stock check
            if ($quantity > $product->stock) {
                $validator->errors()->add('quantity', "Kuantitas melebihi stok yang tersedia ({$product->stock}).");
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk tidak valid.',
            'quantity.required' => 'Kuantitas wajib diisi.',
            'quantity.min' => 'Kuantitas minimal adalah 1.',
        ];
    }
}
