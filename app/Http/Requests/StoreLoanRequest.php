<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Please select an item to borrow.',
            'quantity.min' => 'Loan quantity must be at least 1.',
            'due_date.after_or_equal' => 'Due date cannot be before loan date.',
        ];
    }
}
