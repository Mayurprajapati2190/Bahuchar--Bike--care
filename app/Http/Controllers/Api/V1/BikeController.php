<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBikeRequest;
use App\Http\Resources\BikeResource;
use App\Models\Bike;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class BikeController extends Controller
{
    public function store(StoreBikeRequest $request, Customer $customer): JsonResponse
    {
        $bike = $customer->bikes()->create($request->validated());

        return response()->json([
            'bike' => new BikeResource($bike),
            'message' => 'Bike added successfully.',
        ], 201);
    }

    public function destroy(Customer $customer, Bike $bike): JsonResponse
    {
        abort_unless($bike->customer_id === $customer->id, 404);
        $this->authorizeSuperAdmin();

        $bike->delete();

        return response()->json(['message' => 'Bike removed successfully.']);
    }
}
