<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBillPaymentRequest;
use App\Models\Bill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(Request $request): \Inertia\Response
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
            ->paginate(15)
            ->withQueryString();

        return \Inertia\Inertia::render('Bills/Index', [
            'bills' => $bills,
            'filters' => [
                'search' => $search,
                'payment' => $payment,
            ],
            'summary' => [
                'pendingCount' => Bill::query()->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])->count(),
                'pendingAmount' => (float) (Bill::query()
                    ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])
                    ->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0) as balance')
                    ->value('balance') ?? 0),
            ],
        ]);
    }

    public function pending(Request $request): \Inertia\Response
    {
        $request->merge(['payment' => 'pending']);

        return $this->index($request);
    }

    public function show(Bill $bill): \Inertia\Response
    {
        $bill->load([
            'serviceRecord.customer',
            'serviceRecord.bike',
            'serviceRecord.items',
            'serviceRecord.creator',
        ]);

        return \Inertia\Inertia::render('Bills/Show', [
            'bill' => $bill,
            'shop' => config('shop'),
        ]);
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

    public function updatePayment(UpdateBillPaymentRequest $request, Bill $bill): RedirectResponse
    {
        $total = (float) $bill->total_amount;
        $status = $request->input('payment_status');
        $amountPaid = match ($status) {
            Bill::PAYMENT_PAID => $total,
            Bill::PAYMENT_PARTIAL => min($total, (float) ($request->input('amount_paid') ?? 0)),
            default => 0,
        };

        if ($status === Bill::PAYMENT_PARTIAL && $amountPaid <= 0) {
            return back()->withErrors(['amount_paid' => 'Enter amount received for partial payment.']);
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

        return redirect()
            ->route('bills.show', $bill)
            ->with('status', 'Payment status updated successfully.');
    }

    public function destroy(Bill $bill): RedirectResponse
    {
        $service = $bill->serviceRecord;
        $billNumber = $bill->bill_number;

        if ($service) {
            $service->delete();
        } else {
            $bill->delete();
        }

        return redirect()
            ->route('bills.index')
            ->with('status', "Bill {$billNumber} and related service record deleted.");
    }
}
