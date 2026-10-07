<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'loan_id' => 'required|exists:loans,id',
            'returned_at' => 'required|date',
            'condition_on_return' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
