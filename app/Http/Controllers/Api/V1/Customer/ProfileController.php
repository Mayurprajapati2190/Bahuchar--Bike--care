<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\BikeResource;
use App\Http\Resources\BillResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\ServiceRecordResource;
use App\Models\Bill;
use App\Models\ServiceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request): CustomerResource
    {
        return new CustomerResource($request->user());
    }

    public function bikes(Request $request): JsonResponse
    {
        $bikes = $request->user()->bikes()->get();

        return response()->json(['data' => BikeResource::collection($bikes)]);
    }

    public function services(Request $request): JsonResponse
    {
        $services = ServiceRecord::query()
            ->with(['bike', 'bill'])
            ->where('customer_id', $request->user()->id)
            ->latest('service_date')
            ->paginate($request->integer('per_page', 15));

        return ServiceRecordResource::collection($services)->response();
    }

    public function bills(Request $request): JsonResponse
    {
        $bills = Bill::query()
            ->with(['serviceRecord.bike'])
            ->whereHas('serviceRecord', fn ($q) => $q->where('customer_id', $request->user()->id))
            ->latest('bill_date')
            ->paginate($request->integer('per_page', 15));

        return BillResource::collection($bills)->response();
    }

    public function nextServiceDue(Request $request): JsonResponse
    {
        $nextDue = ServiceRecord::query()
            ->with('bike')
            ->where('customer_id', $request->user()->id)
            ->where('status', ServiceRecord::STATUS_COMPLETED)
            ->whereNotNull('next_service_due_at')
            ->orderBy('next_service_due_at')
            ->first();

        return response()->json([
            'next_service' => $nextDue ? new ServiceRecordResource($nextDue) : null,
            'shop' => $request->user()->team?->shopPayload() ?? config('shop'),
        ]);
    }
}
