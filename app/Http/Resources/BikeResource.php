<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Bike */
class BikeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'brand' => $this->brand,
            'model' => $this->model,
            'registration_number' => $this->registration_number,
            'display_name' => $this->displayName(),
        ];
    }
}
