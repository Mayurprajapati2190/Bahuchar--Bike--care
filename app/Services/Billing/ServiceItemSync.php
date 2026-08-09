<?php

namespace App\Services\Billing;

use App\Models\ServiceItem;
use App\Models\ServiceRecord;

class ServiceItemSync
{
    /**
     * @param  array<int, array{description: string, quantity: float|int|string, unit_price: float|int|string}>  $items
     */
    public function sync(ServiceRecord $service, array $items): float
    {
        $service->items()->delete();

        $total = 0;
        $sortOrder = 0;

        foreach ($items as $item) {
            $description = trim($item['description'] ?? '');
            if ($description === '') {
                continue;
            }

            $quantity = max(0, (float) ($item['quantity'] ?? 1));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $amount = (float) ServiceItem::calculateAmount($quantity, $unitPrice);

            ServiceItem::query()->create([
                'service_record_id' => $service->id,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'amount' => $amount,
                'sort_order' => $sortOrder++,
            ]);

            $total += $amount;
        }

        if ($total <= 0 && ! empty($items)) {
            $total = 0;
        }

        return round($total, 2);
    }
}
