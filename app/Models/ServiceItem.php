<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_record_id',
    'description',
    'quantity',
    'unit_price',
    'amount',
    'sort_order',
])]
class ServiceItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(ServiceRecord::class);
    }

    public static function calculateAmount(float $quantity, float $unitPrice): string
    {
        return number_format($quantity * $unitPrice, 2, '.', '');
    }
}
