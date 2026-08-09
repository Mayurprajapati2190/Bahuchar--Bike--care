<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Customer */
class CustomerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'bikes_count' => $this->whenCounted('bikes'),
            'service_records_count' => $this->whenCounted('serviceRecords'),
            'bikes' => BikeResource::collection($this->whenLoaded('bikes')),
            'service_records' => ServiceRecordResource::collection($this->whenLoaded('serviceRecords')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
