<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\ServiceRecordResource;
use App\Models\Customer;
use App\Models\ServiceRecord;
use App\Services\Billing\ServiceItemSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct(private ServiceItemSync $itemSync) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->trim()->toString();

        $customers = Customer::query()
            ->withCount(['bikes', 'serviceRecords'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return CustomerResource::collection($customers)->response();
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $addService = $request->boolean('add_service');

        $result = DB::transaction(function () use ($validated, $addService, $request) {
            $customer = Customer::query()->create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            $bike = $customer->bikes()->create([
                'brand' => $validated['bike']['brand'],
                'model' => $validated['bike']['model'] ?? null,
                'registration_number' => $validated['bike']['registration_number'] ?? null,
            ]);

            if (! $addService) {
                return ['customer' => $customer, 'service' => null];
            }

            $service = ServiceRecord::query()->create([
                'customer_id' => $customer->id,
                'bike_id' => $bike->id,
                'service_date' => $validated['service_date'],
                'work_done' => $validated['work_done'] ?? null,
                'total_amount' => 0,
                'created_by' => $request->user()?->id,
                'status' => ServiceRecord::STATUS_IN_PROGRESS,
            ]);

            $total = $this->itemSync->sync($service, $validated['items'] ?? []);
            $service->update(['total_amount' => $total]);

            return ['customer' => $customer, 'service' => $service];
        });

        $customer = $result['customer']->load(['bikes', 'serviceRecords' => fn ($q) => $q->latest()->limit(10)]);

        return response()->json([
            'customer' => new CustomerResource($customer),
            'service' => $result['service']
                ? new ServiceRecordResource($result['service']->load(['bike', 'items']))
                : null,
        ], 201);
    }

    public function show(Customer $customer): CustomerResource
    {
        $customer->load([
            'bikes',
            'serviceRecords' => fn ($query) => $query->with('bike')->latest()->limit(10),
        ]);

        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer->update($request->validated());

        return new CustomerResource($customer->fresh());
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully.']);
    }
}
