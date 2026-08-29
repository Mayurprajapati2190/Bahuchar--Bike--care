<?php

namespace App\Http\Requests;

use App\Support\RegistrationNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreBikeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('registration_number')) {
            return;
        }

        $this->merge([
            'registration_number' => RegistrationNumber::normalize($this->input('registration_number')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'registration_number.required' => 'Please enter the bike registration number.',
        ];
    }
}
