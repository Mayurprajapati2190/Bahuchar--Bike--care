<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bill {{ $bill->bill_number }} - {{ config('shop.name') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: #111; background: #fff; padding: 24px; }
        .bill { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 32px; }
        .header { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #111; padding-bottom: 20px; margin-bottom: 24px; }
        .shop-brand { display: flex; align-items: center; gap: 16px; }
        .shop-logo { width: 56px; height: 56px; flex-shrink: 0; }
        .shop-name { font-size: 24px; font-weight: bold; color: #b45309; }
        .shop-meta { margin-top: 8px; font-size: 13px; color: #555; line-height: 1.6; }
        .bill-title { text-align: right; }
        .bill-title h1 { font-size: 28px; letter-spacing: 1px; }
        .bill-title p { margin-top: 6px; font-size: 14px; color: #555; }
        .section { margin-bottom: 24px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .label { font-size: 12px; text-transform: uppercase; color: #777; margin-bottom: 4px; }
        .value { font-size: 14px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; font-size: 14px; }
        th { background: #f8fafc; font-size: 12px; text-transform: uppercase; color: #555; }
        .text-right { text-align: right; }
        .totals { margin-top: 16px; margin-left: auto; width: 280px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .totals-row.grand { border-top: 2px solid #111; margin-top: 8px; padding-top: 10px; font-size: 18px; font-weight: bold; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px dashed #ccc; font-size: 12px; color: #666; }
        .notes { margin-top: 20px; font-size: 13px; line-height: 1.6; }
        @media print {
            body { padding: 0; }
            .bill { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width:800px;margin:0 auto 16px;text-align:right;">
        <button onclick="window.print()" style="padding:10px 18px;background:#b45309;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:bold;">
            Print Bill
        </button>
    </div>

    <div class="bill">
        <div class="header">
            <div class="shop-brand">
                <img src="{{ url('/logo-icon.svg') }}" alt="{{ $shop['name'] }}" class="shop-logo">
                <div>
                    <div class="shop-name">{{ $shop['name'] }}</div>
                    <div class="shop-meta">
                        @if ($shop['address']) {{ $shop['address'] }}<br>@endif
                        @if ($shop['phone']) Phone: {{ $shop['phone'] }}<br>@endif
                        @if ($shop['gstin']) GSTIN: {{ $shop['gstin'] }} @endif
                    </div>
                </div>
            </div>
            <div class="bill-title">
                <h1>TAX INVOICE</h1>
                <p><strong>{{ $bill->bill_number }}</strong></p>
                <p>Date: {{ $bill->bill_date->format('d M Y') }}</p>
            </div>
        </div>

        <div class="section grid">
            <div>
                <div class="label">Bill To</div>
                <div class="value">{{ $bill->serviceRecord->customer->name }}</div>
                <div style="margin-top:4px;font-size:14px;">Phone: {{ $bill->serviceRecord->customer->phone }}</div>
                @if ($bill->serviceRecord->customer->address)
                    <div style="margin-top:4px;font-size:13px;color:#555;">{{ $bill->serviceRecord->customer->address }}</div>
                @endif
            </div>
            <div>
                <div class="label">Vehicle Details</div>
                <div class="value">{{ $bill->serviceRecord->bike->brand }} {{ $bill->serviceRecord->bike->model }}</div>
                @if ($bill->serviceRecord->bike->registration_number)
                    <div style="margin-top:4px;font-size:14px;">Reg. No: {{ $bill->serviceRecord->bike->registration_number }}</div>
                @endif
                <div style="margin-top:4px;font-size:14px;">Service Date: {{ $bill->serviceRecord->service_date->format('d M Y') }}</div>
            </div>
        </div>

        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Rate (₹)</th>
                        <th class="text-right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bill->serviceRecord->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="totals-row"><span>Subtotal</span><span>₹ {{ number_format($bill->subtotal, 2) }}</span></div>
                @if ($bill->discount_amount > 0)
                    <div class="totals-row"><span>Discount</span><span>- ₹ {{ number_format($bill->discount_amount, 2) }}</span></div>
                @endif
                @if ($bill->tax_amount > 0)
                    <div class="totals-row"><span>Tax</span><span>₹ {{ number_format($bill->tax_amount, 2) }}</span></div>
                @endif
                <div class="totals-row grand"><span>Total</span><span>₹ {{ number_format($bill->total_amount, 2) }}</span></div>
                        <div class="flex justify-between text-slate-600 capitalize">
                            <span>Payment</span>
                            <span>{{ $bill->payment_status }} @if($bill->payment_method)({{ $bill->payment_method }})@endif</span>
                        </div>
                        @if($bill->amount_paid > 0)
                        <div class="totals-row"><span>Amount Paid</span><span>₹ {{ number_format($bill->amount_paid, 2) }}</span></div>
                        @endif
                        @if($bill->balanceDue() > 0)
                        <div class="totals-row" style="color:#dc2626;font-weight:bold;"><span>Balance Due</span><span>₹ {{ number_format($bill->balanceDue(), 2) }}</span></div>
                        @endif
            </div>
        </div>

        @if ($bill->serviceRecord->work_done)
            <div class="notes">
                <div class="label">Work Done / Notes</div>
                <div>{{ $bill->serviceRecord->work_done }}</div>
            </div>
        @endif

        <div class="footer">
            Thank you for choosing {{ $shop['name'] }}. Next service due:
            {{ $bill->serviceRecord->next_service_due_at?->format('d M Y') ?? 'N/A' }}.
        </div>
    </div>
</body>
</html>
