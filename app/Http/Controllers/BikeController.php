<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBikeRequest;
use App\Models\Bike;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;

class BikeController extends Controller
{
    public function store(StoreBikeRequest $request, Customer $customer): RedirectResponse
    {
        $customer->bikes()->create($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Bike added successfully.');
    }

    public function destroy(Customer $customer, Bike $bike): RedirectResponse
    {
        abort_unless($bike->customer_id === $customer->id, 404);
        $this->authorizeSuperAdmin();

        $bike->delete();

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Bike removed successfully.');
    }
}
