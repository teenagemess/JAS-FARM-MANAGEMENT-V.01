<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:feed_types,name',
            'unit' => 'required|string|max:50',
            'price_per_unit' => 'required|numeric|min:0', // TAMBAHKAN VALIDASI INI
            'description' => 'nullable|string|max:1000',
        ];
    }
}
