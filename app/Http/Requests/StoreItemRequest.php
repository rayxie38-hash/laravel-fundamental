<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:items,code',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category selection is required.',
            'code.unique' => 'Item code has already been used.',
            'stock.min' => 'Stock cannot be negative.',
        ];
    }
}
