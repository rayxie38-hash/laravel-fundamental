<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'code' => 'required|string|max:100|unique:items,code,' . $itemId,
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string|max:100',
        ];
    }
}
