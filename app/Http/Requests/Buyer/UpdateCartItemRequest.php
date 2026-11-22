<?php

namespace App\Http\Requests\Buyer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() ?? false;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Kuantitas wajib diisi.',
            'quantity.min' => 'Kuantitas minimal adalah 1.',
        ];
    }
}
