<?php



namespace App\Http\Controllers;



use App\Models\Bill;

use App\Models\Customer;

use App\Models\ServiceRecord;

use App\Models\SmsMessage;

use Illuminate\Http\Request;

use Inertia\Inertia;

use Inertia\Response;



class DashboardController extends Controller

{

    public function __invoke(Request $request): Response

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

            ->get()

            ->map(fn (Bill $bill) => [

                ...$bill->toArray(),

                'balance_due' => $bill->balanceDue(),

            ]);



        $recentSms = SmsMessage::query()

            ->with('serviceRecord.customer')

            ->latest()

            ->limit(8)

            ->get();



        $pendingAmount = Bill::query()

            ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])

            ->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0) as balance')

            ->value('balance');



        return Inertia::render('Dashboard', [

            'stats' => [

                'totalCustomers' => Customer::query()->count(),

                'servicesThisMonth' => ServiceRecord::query()

                    ->whereDate('service_date', '>=', $monthStart)

                    ->count(),

                'inProgress' => ServiceRecord::query()

                    ->where('status', ServiceRecord::STATUS_IN_PROGRESS)

                    ->count(),

                'dueReminders' => ServiceRecord::query()

                    ->where('status', ServiceRecord::STATUS_COMPLETED)

                    ->whereNull('reminder_sms_sent_at')

                    ->whereNotNull('next_service_due_at')

                    ->where('next_service_due_at', '<=', $weekAhead)

                    ->count(),

                'pendingPayments' => Bill::query()

                    ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])

                    ->count(),

                'pendingAmount' => (float) $pendingAmount,

            ],

            'completedToday' => $completedToday,

            'upcomingReminders' => $upcomingReminders,

            'pendingPayments' => $pendingPayments,

            'recentSms' => $recentSms,

        ]);

    }

}


