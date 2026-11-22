<?php

namespace App\Http\Requests\Seller;

use App\Enums\ConditionGrade;
use App\Enums\ProductCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSeller() ?? false;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : $product;
        $isUsed = $this->input('condition') === ProductCondition::USED->value || $this->input('condition') === 'used';

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'storage' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'condition' => ['required', Rule::enum(ProductCondition::class)],
            'price' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', $isUsed ? 'in:0,1' : 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],

            // Inspection fields for Used products (BR-02)
            'condition_grade' => [
                $isUsed ? 'required' : 'nullable',
                Rule::enum(ConditionGrade::class),
            ],
            'battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'functional_status' => [$isUsed ? 'required' : 'nullable', 'string', 'max:255'],
            'defect_notes' => ['nullable', 'string'],
            'included_accessories' => ['nullable', 'string'],
            'warranty_info' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.gt' => 'Harga produk harus lebih besar dari 0.',
            'stock.in' => 'Stok unit second harus bernilai 0 atau 1 (1 unit = 1 SKU unik).',
            'condition_grade.required' => 'Grade kondisi fisik wajib dipilih untuk produk second.',
            'functional_status.required' => 'Status fungsi wajib diisi untuk produk second.',
            'battery_health.max' => 'Kesehatan baterai tidak boleh melebihi 100%.',
        ];
    }
}
