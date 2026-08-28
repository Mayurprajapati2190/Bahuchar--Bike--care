<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBillPaymentRequest;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->trim()->toString();
        $payment = $request->string('payment')->trim()->toString();

        $bills = Bill::query()
            ->with(['serviceRecord.customer', 'serviceRecord.bike'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('bill_number', 'like', "%{$search}%")
                        ->orWhereHas('serviceRecord.customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            })
            ->when($payment === 'pending', fn ($query) => $query->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL]))
            ->when($payment === 'paid', fn ($query) => $query->where('payment_status', Bill::PAYMENT_PAID))
            ->latest('bill_date')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => BillResource::collection($bills->items()),
            'meta' => [
                'current_page' => $bills->currentPage(),
                'last_page' => $bills->lastPage(),
                'per_page' => $bills->perPage(),
                'total' => $bills->total(),
            ],
            'summary' => [
                'pending_count' => Bill::query()->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])->count(),
                'pending_amount' => (float) (Bill::query()
                    ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])
                    ->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0) as balance')
                    ->value('balance') ?? 0),
            ],
        ]);
    }

    public function show(Bill $bill): BillResource
    {
        $bill->load([
            'serviceRecord.customer',
            'serviceRecord.bike',
            'serviceRecord.items',
            'serviceRecord.creator',
        ]);

        return new BillResource($bill);
    }

    public function updatePayment(UpdateBillPaymentRequest $request, Bill $bill): BillResource|JsonResponse
    {
        $total = (float) $bill->total_amount;
        $status = $request->input('payment_status');
        $amountPaid = match ($status) {
            Bill::PAYMENT_PAID => $total,
            Bill::PAYMENT_PARTIAL => min($total, (float) ($request->input('amount_paid') ?? 0)),
            default => 0,
        };

        if ($status === Bill::PAYMENT_PARTIAL && $amountPaid <= 0) {
            return response()->json(['message' => 'Enter amount received for partial payment.', 'errors' => ['amount_paid' => ['Enter amount received for partial payment.']]], 422);
        }

        if ($status === Bill::PAYMENT_PARTIAL && $amountPaid >= $total) {
            $status = Bill::PAYMENT_PAID;
            $amountPaid = $total;
        }

        $bill->update([
            'payment_status' => $status,
            'amount_paid' => $amountPaid,
            'payment_method' => $request->input('payment_method') ?? $bill->payment_method ?? 'cash',
        ]);

        return new BillResource($bill->fresh(['serviceRecord.customer', 'serviceRecord.bike', 'serviceRecord.items']));
    }

    public function print(Bill $bill): View
    {
        $bill->load([
            'serviceRecord.customer',
            'serviceRecord.bike',
            'serviceRecord.items',
        ]);

        return view('bills.print', [
            'bill' => $bill,
            'shop' => config('shop'),
        ]);
    }

    public function destroy(Bill $bill): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $service = $bill->serviceRecord;
        $billNumber = $bill->bill_number;

        if ($service) {
            $service->delete();
        } else {
            $bill->delete();
        }

        return response()->json(['message' => "Bill {$billNumber} and related service record deleted."]);
    }
}
