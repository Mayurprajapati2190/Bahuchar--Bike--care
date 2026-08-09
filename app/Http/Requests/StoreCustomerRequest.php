<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'bike.brand' => ['required', 'string', 'max:255'],
            'bike.model' => ['nullable', 'string', 'max:255'],
            'bike.registration_number' => ['required', 'string', 'max:50'],
            'add_service' => ['boolean'],
            'service_date' => ['required_if:add_service,true', 'nullable', 'date'],
            'work_done' => ['nullable', 'string', 'max:5000'],
            'items' => ['required_if:add_service,true', 'nullable', 'array', 'min:1'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bike.brand.required' => 'Please enter the bike brand.',
            'bike.registration_number.required' => 'Please enter the bike registration number.',
            'service_date.required_if' => 'Service date is required when recording a service.',
            'items.required_if' => 'Add at least one bill item when recording a service.',
        ];
    }
}
