<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_record_id',
    'bill_number',
    'bill_date',
    'subtotal',
    'tax_amount',
    'discount_amount',
    'total_amount',
    'amount_paid',
    'payment_status',
    'payment_method',
    'notes',
])]
class Bill extends Model
{
    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PARTIAL = 'partial';

    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function isPending(): bool
    {
        return in_array($this->payment_status, [self::PAYMENT_UNPAID, self::PAYMENT_PARTIAL], true);
    }

    public function balanceDue(): float
    {
        return max(0, round((float) $this->total_amount - (float) $this->amount_paid, 2));
    }

    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(ServiceRecord::class);
    }
}
