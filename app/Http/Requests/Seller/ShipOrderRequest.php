<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class ShipOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSeller() ?? false;
    }

    public function rules(): array
    {
        return [
            'courier_name' => ['required', 'string', 'max:100'],
            'tracking_number' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'courier_name.required' => 'Nama kurir wajib diisi.',
            'tracking_number.required' => 'Nomor resi wajib diisi.',
        ];
    }
}
