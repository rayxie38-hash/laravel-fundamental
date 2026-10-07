<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $itemId = $this->route('item')->id ?? $this->route('item');

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('items', 'code')->ignore($itemId)->whereNull('deleted_at'),
            ],
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string|max:100',
        ];
    }
}
