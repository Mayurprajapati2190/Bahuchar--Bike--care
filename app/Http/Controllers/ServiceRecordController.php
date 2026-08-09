<?php



namespace App\Http\Controllers;



use App\Http\Requests\CompleteServiceRecordRequest;

use App\Http\Requests\StoreServiceRecordRequest;

use App\Http\Requests\UpdateServiceRecordRequest;

use App\Jobs\SendServiceConfirmationSms;

use App\Jobs\SendServiceReminderSms;

use App\Models\Bill;

use App\Models\Customer;

use App\Models\ServiceRecord;

use App\Services\Billing\BillGenerator;

use App\Services\Billing\ServiceItemSync;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Inertia\Inertia;

use Inertia\Response;



class ServiceRecordController extends Controller

{

    public function __construct(

        private ServiceItemSync $itemSync,

        private BillGenerator $billGenerator,

    ) {}



    public function index(Request $request): Response

    {

        $search = $request->string('search')->trim()->toString();

        $status = $request->string('status')->trim()->toString();



        $services = ServiceRecord::query()

            ->with(['customer', 'bike', 'bill'])

            ->when($search !== '', function ($query) use ($search) {

                $query->whereHas('customer', function ($customerQuery) use ($search) {

                    $customerQuery->where('name', 'like', "%{$search}%")

                        ->orWhere('phone', 'like', "%{$search}%");

                });

            })

            ->when(in_array($status, [ServiceRecord::STATUS_IN_PROGRESS, ServiceRecord::STATUS_COMPLETED], true), function ($query) use ($status) {

                $query->where('status', $status);

            })

            ->latest('service_date')

            ->paginate(15)

            ->withQueryString();



        return Inertia::render('Services/Index', [

            'services' => $services,

            'filters' => [

                'search' => $search,

                'status' => $status,

            ],

        ]);

    }



    public function create(Request $request): Response

    {

        $customers = Customer::query()

            ->with('bikes')

            ->orderBy('name')

            ->get(['id', 'name', 'phone']);



        return Inertia::render('Services/Create', [

            'customers' => $customers,

            'selectedCustomerId' => $request->integer('customer_id') ?: null,
            'selectedBikeId' => $request->integer('bike_id') ?: null,
        ]);

    }



    public function store(StoreServiceRecordRequest $request): RedirectResponse

    {

        $bike = \App\Models\Bike::query()->findOrFail($request->integer('bike_id'));



        abort_unless($bike->customer_id === $request->integer('customer_id'), 422);



        $service = ServiceRecord::query()->create([

            'customer_id' => $request->integer('customer_id'),

            'bike_id' => $request->integer('bike_id'),

            'service_date' => $request->input('service_date'),

            'work_done' => $request->input('work_done'),

            'total_amount' => 0,

            'created_by' => $request->user()?->id,

            'status' => ServiceRecord::STATUS_IN_PROGRESS,

        ]);



        $total = $this->itemSync->sync($service, $request->input('items', []));

        $service->update(['total_amount' => $total]);

        if ($request->input('return_to') === 'customer') {
            return redirect()
                ->route('customers.show', $service->customer_id)
                ->with('status', 'Service record created successfully. You can complete it from the service list below.');
        }

        return redirect()
            ->route('services.show', $service)
            ->with('status', 'Service record created successfully.');

    }



    public function show(ServiceRecord $service): Response

    {

        $service->load(['customer', 'bike', 'creator', 'items', 'bill', 'smsMessages' => fn ($query) => $query->latest()]);



        return Inertia::render('Services/Show', [

            'service' => $service,

        ]);

    }



    public function edit(ServiceRecord $service): Response

    {

        abort_if($service->isCompleted(), 403, 'Completed services cannot be edited.');



        $service->load(['customer', 'bike', 'items']);



        return Inertia::render('Services/Edit', [

            'service' => $service,

        ]);

    }



    public function update(UpdateServiceRecordRequest $request, ServiceRecord $service): RedirectResponse

    {

        abort_if($service->isCompleted(), 403, 'Completed services cannot be edited.');



        $total = $this->itemSync->sync($service, $request->input('items', []));



        $service->update([

            'service_date' => $request->input('service_date'),

            'work_done' => $request->input('work_done'),

            'total_amount' => $total,

        ]);



        return redirect()

            ->route('services.show', $service)

            ->with('status', 'Service record updated successfully.');

    }



    public function complete(CompleteServiceRecordRequest $request, ServiceRecord $service): RedirectResponse

    {

        abort_if($service->isCompleted(), 403, 'Service is already completed.');



        $paymentStatus = $request->input('payment_status', Bill::PAYMENT_PAID);



        $service->markCompleted();

        $bill = $this->billGenerator->createForService(

            $service,

            $paymentStatus,

            $request->input('payment_method'),

        );



        if ($service->confirmation_sms_sent_at === null) {

            SendServiceConfirmationSms::dispatch($service->id);

        }



        $smsNote = config('services.msg91.enabled')

            ? 'Confirmation SMS queued.'

            : 'Confirmation message recorded (free mode — not sent to phone).';



        $message = $bill->isPending()

            ? "Service completed. Bill {$bill->bill_number} created (payment pending). {$smsNote}"

            : "Service completed. Bill {$bill->bill_number} created. {$smsNote}";



        return redirect()

            ->route('bills.show', $bill)

            ->with('status', $message);

    }



    public function sendReminder(ServiceRecord $service): RedirectResponse

    {

        abort_unless($service->isCompleted(), 403, 'Reminders can only be sent for completed services.');



        SendServiceReminderSms::dispatch($service->id, force: true);



        $status = config('services.msg91.enabled')

            ? 'Reminder SMS queued.'

            : 'Reminder message recorded (free mode — not sent to phone).';



        return redirect()

            ->route('services.show', $service)

            ->with('status', $status);

    }



    public function destroy(ServiceRecord $service): RedirectResponse

    {

        $service->delete();



        return redirect()

            ->route('services.index')

            ->with('status', 'Service record deleted successfully.');

    }

}


