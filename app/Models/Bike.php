<?php

namespace App\Models;

use App\Support\RegistrationNumber;
use Database\Factories\BikeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer_id', 'brand', 'model', 'registration_number'])]
class Bike extends Model
{
    /** @use HasFactory<BikeFactory> */
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function displayName(): string
    {
        $parts = array_filter([$this->brand, $this->model, $this->registration_number]);

        return implode(' · ', $parts) ?: 'Bike #'.$this->id;
    }

    protected function registrationNumber(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => RegistrationNumber::normalize($value),
        );
    }
}
