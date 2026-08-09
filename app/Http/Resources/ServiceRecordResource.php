<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ServiceRecord */
class ServiceRecordResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'bike_id' => $this->bike_id,
            'service_date' => $this->service_date?->toDateString(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'next_service_due_at' => $this->next_service_due_at?->toDateString(),
            'total_amount' => (float) $this->total_amount,
            'work_done' => $this->work_done,
            'status' => $this->status,
            'confirmation_sms_sent_at' => $this->confirmation_sms_sent_at?->toIso8601String(),
            'reminder_sms_sent_at' => $this->reminder_sms_sent_at?->toIso8601String(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'bike' => new BikeResource($this->whenLoaded('bike')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'items' => ServiceItemResource::collection($this->whenLoaded('items')),
            'bill' => new BillResource($this->whenLoaded('bill')),
            'sms_messages' => SmsMessageResource::collection($this->whenLoaded('smsMessages')),
        ];
    }
}
