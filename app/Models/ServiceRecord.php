<?php

namespace App\Models;

use Database\Factories\ServiceRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'customer_id',
    'bike_id',
    'created_by',
    'service_date',
    'completed_at',
    'next_service_due_at',
    'total_amount',
    'work_done',
    'status',
    'confirmation_sms_sent_at',
    'confirmation_email_sent_at',
    'reminder_sms_sent_at',
    'reminder_email_sent_at',
])]
class ServiceRecord extends Model
{
    /** @use HasFactory<ServiceRecordFactory> */
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'completed_at' => 'datetime',
            'next_service_due_at' => 'date',
            'total_amount' => 'decimal:2',
            'confirmation_sms_sent_at' => 'datetime',
            'confirmation_email_sent_at' => 'datetime',
            'reminder_sms_sent_at' => 'datetime',
            'reminder_email_sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceItem::class)->orderBy('sort_order');
    }

    public function bill(): HasOne
    {
        return $this->hasOne(Bill::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markCompleted(): void
    {
        $completedAt = now();

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => $completedAt,
            'next_service_due_at' => $completedAt->copy()->addMonths(2)->toDateString(),
        ]);
    }
}
