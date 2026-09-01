<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Http\Resources\ServiceRecordResource;
use App\Http\Resources\SmsMessageResource;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\ServiceRecord;
use App\Models\SmsMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $today = now()->toDateString();
        $weekAhead = now()->addDays(7)->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $completedToday = ServiceRecord::query()
            ->with(['customer', 'bike'])
            ->where('status', ServiceRecord::STATUS_COMPLETED)
            ->whereDate('completed_at', $today)
            ->latest('completed_at')
            ->limit(10)
            ->get();

        $upcomingReminders = ServiceRecord::query()
            ->with(['customer', 'bike'])
            ->where('status', ServiceRecord::STATUS_COMPLETED)
            ->whereNull('reminder_sms_sent_at')
            ->whereNotNull('next_service_due_at')
            ->whereBetween('next_service_due_at', [$today, $weekAhead])
            ->orderBy('next_service_due_at')
            ->limit(10)
            ->get();

        $pendingPayments = Bill::query()
            ->with(['serviceRecord.customer', 'serviceRecord.bike'])
            ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])
            ->latest('bill_date')
            ->limit(10)
            ->get();

        $recentSms = SmsMessage::query()
            ->with('serviceRecord.customer')
            ->latest()
            ->limit(8)
            ->get();

        $pendingAmount = Bill::query()
            ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])
            ->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0) as balance')
            ->value('balance');

        return response()->json([
            'stats' => [
                'total_customers' => Customer::query()->count(),
                'services_this_month' => ServiceRecord::query()
                    ->whereDate('service_date', '>=', $monthStart)
                    ->count(),
                'in_progress' => ServiceRecord::query()
                    ->where('status', ServiceRecord::STATUS_IN_PROGRESS)
                    ->count(),
                'due_reminders' => ServiceRecord::query()
                    ->where('status', ServiceRecord::STATUS_COMPLETED)
                    ->whereNull('reminder_sms_sent_at')
                    ->whereNotNull('next_service_due_at')
                    ->where('next_service_due_at', '<=', $weekAhead)
                    ->count(),
                'pending_payments' => Bill::query()
                    ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])
                    ->count(),
                'pending_amount' => (float) $pendingAmount,
            ],
            'completed_today' => ServiceRecordResource::collection($completedToday),
            'upcoming_reminders' => ServiceRecordResource::collection($upcomingReminders),
            'pending_payments' => BillResource::collection($pendingPayments),
            'recent_sms' => SmsMessageResource::collection($recentSms),
            'shop' => $this->shopPayload(),
        ]);
    }
}
