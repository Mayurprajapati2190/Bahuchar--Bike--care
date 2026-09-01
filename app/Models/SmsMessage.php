<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id',
    'service_record_id',
    'phone',
    'type',
    'provider_message_id',
    'status',
    'body',
    'error_message',
    'sent_at',
])]
class SmsMessage extends Model
{
    use BelongsToTeam;

    public const TYPE_CONFIRMATION = 'confirmation';

    public const TYPE_REMINDER = 'reminder';

    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(ServiceRecord::class);
    }
}
