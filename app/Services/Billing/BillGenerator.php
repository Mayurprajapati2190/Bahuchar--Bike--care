<?php

namespace App\Services\Billing;

use App\Models\Bill;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\DB;

class BillGenerator
{
    public function createForService(
        ServiceRecord $service,
        string $paymentStatus = Bill::PAYMENT_PAID,
        ?string $paymentMethod = null,
    ): Bill {
        if ($service->bill()->exists()) {
            return $service->bill;
        }

        $service->loadMissing('items');

        return DB::transaction(function () use ($service, $paymentStatus, $paymentMethod) {
            $subtotal = $service->items->sum(fn ($item) => (float) $item->amount);
            $subtotal = round($subtotal, 2);

            if ($subtotal <= 0) {
                $subtotal = round((float) $service->total_amount, 2);
            }

            $amountPaid = $paymentStatus === Bill::PAYMENT_PAID ? $subtotal : 0;

            $bill = Bill::query()->create([
                'service_record_id' => $service->id,
                'bill_number' => $this->nextBillNumber(),
                'bill_date' => $service->completed_at?->toDateString() ?? now()->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'amount_paid' => $amountPaid,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod ?? ($paymentStatus === Bill::PAYMENT_PAID ? 'cash' : null),
            ]);

            $service->update(['total_amount' => $subtotal]);

            return $bill;
        });
    }

    public function nextBillNumber(): string
    {
        $prefix = config('shop.bill_prefix', 'BBC');
        $yearMonth = now()->format('Ym');

        $latest = Bill::query()
            ->where('bill_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderByDesc('bill_number')
            ->value('bill_number');

        $sequence = 1;

        if ($latest !== null && preg_match('/-(\d+)$/', $latest, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $yearMonth, $sequence);
    }
}
