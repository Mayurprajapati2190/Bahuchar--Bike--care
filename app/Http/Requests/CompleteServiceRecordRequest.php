<?php

namespace App\Http\Requests;

use App\Models\Bill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteServiceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_status' => ['required', 'string', Rule::in([Bill::PAYMENT_PAID, Bill::PAYMENT_UNPAID])],
            'payment_method' => ['nullable', 'string', Rule::in(['cash', 'upi', 'card', 'other'])],
        ];
    }
}
