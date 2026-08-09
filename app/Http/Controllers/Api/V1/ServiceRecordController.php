<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteServiceRecordRequest;
use App\Http\Requests\StoreServiceRecordRequest;
use App\Http\Requests\UpdateServiceRecordRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\ServiceRecordResource;
use App\Jobs\SendServiceConfirmationSms;
use App\Jobs\SendServiceReminderSms;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\ServiceRecord;
use App\Services\Billing\BillGenerator;
use App\Services\Billing\ServiceItemSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceRecordController extends Controller
{
    public function __construct(
        private ServiceItemSync $itemSync,
        private BillGenerator $billGenerator,
    ) {}

    public function index(Request $request): JsonResponse
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
            ->paginate($request->integer('per_page', 15));

        return ServiceRecordResource::collection($services)->response();
    }

    public function store(StoreServiceRecordRequest $request): JsonResponse
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

        return response()->json([
            'service' => new ServiceRecordResource($service->load(['customer', 'bike', 'items'])),
        ], 201);
    }

    public function show(ServiceRecord $service): ServiceRecordResource
    {
        $service->load(['customer', 'bike', 'creator', 'items', 'bill', 'smsMessages' => fn ($query) => $query->latest()]);

        return new ServiceRecordResource($service);
    }

    public function update(UpdateServiceRecordRequest $request, ServiceRecord $service): ServiceRecordResource|JsonResponse
    {
        abort_if($service->isCompleted(), 403, 'Completed services cannot be edited.');

        $total = $this->itemSync->sync($service, $request->input('items', []));

        $service->update([
            'service_date' => $request->input('service_date'),
            'work_done' => $request->input('work_done'),
            'total_amount' => $total,
        ]);

        return new ServiceRecordResource($service->fresh(['customer', 'bike', 'items']));
    }

    public function complete(CompleteServiceRecordRequest $request, ServiceRecord $service): JsonResponse
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

        return response()->json([
            'message' => $message,
            'service' => new ServiceRecordResource($service->fresh(['customer', 'bike', 'items', 'bill'])),
            'bill' => new BillResource($bill->load(['serviceRecord.customer', 'serviceRecord.bike', 'serviceRecord.items'])),
        ]);
    }

    public function sendReminder(ServiceRecord $service): JsonResponse
    {
        abort_unless($service->isCompleted(), 403, 'Reminders can only be sent for completed services.');

        SendServiceReminderSms::dispatch($service->id, force: true);

        $status = config('services.msg91.enabled')
            ? 'Reminder SMS queued.'
            : 'Reminder message recorded (free mode — not sent to phone).';

        return response()->json(['message' => $status]);
    }

    public function destroy(ServiceRecord $service): JsonResponse
    {
        $service->delete();

        return response()->json(['message' => 'Service record deleted successfully.']);
    }

    public function createOptions(): JsonResponse
    {
        $customers = Customer::query()
            ->with('bikes')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return response()->json([
            'customers' => $customers->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'bikes' => $customer->bikes->map(fn ($bike) => [
                    'id' => $bike->id,
                    'display_name' => $bike->displayName(),
                ]),
            ]),
        ]);
    }
}
