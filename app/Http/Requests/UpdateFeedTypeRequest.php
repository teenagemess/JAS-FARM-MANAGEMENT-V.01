<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeedTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $feedTypeId = $this->route('feed_type')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('feed_types', 'name')->ignore($feedTypeId)
            ],
            'unit' => 'required|string|max:50',
            'price_per_unit' => 'required|numeric|min:0', // TAMBAHKAN VALIDASI INI
            'description' => 'nullable|string|max:1000',
        ];
    }
}
