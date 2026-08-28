<?php



namespace App\Http\Controllers;



use App\Http\Requests\StoreCustomerRequest;

use App\Http\Requests\UpdateCustomerRequest;

use App\Models\Customer;

use App\Models\ServiceRecord;

use App\Services\Billing\ServiceItemSync;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Inertia\Inertia;

use Inertia\Response;



class CustomerController extends Controller

{

    public function __construct(private ServiceItemSync $itemSync) {}



    public function index(Request $request): Response

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

            ->paginate(15)

            ->withQueryString();



        return Inertia::render('Customers/Index', [

            'customers' => $customers,

            'filters' => ['search' => $search],

        ]);

    }



    public function create(): Response

    {

        return Inertia::render('Customers/Create');

    }



    public function store(StoreCustomerRequest $request): RedirectResponse

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



        if ($result['service'] !== null) {

            return redirect()

                ->route('services.show', $result['service'])

                ->with('status', "Customer {$result['customer']->name} registered with bike and service #{$result['service']->id} created.");

        }



        return redirect()

            ->route('customers.show', $result['customer'])

            ->with('status', 'Customer and bike saved. Add a service when ready.');

    }



    public function show(Customer $customer): Response

    {

        $customer->load([

            'bikes',

            'serviceRecords' => fn ($query) => $query->with('bike')->latest()->limit(10),

        ]);



        return Inertia::render('Customers/Show', [

            'customer' => $customer,

        ]);

    }



    public function edit(Customer $customer): Response

    {

        return Inertia::render('Customers/Edit', [

            'customer' => $customer,

        ]);

    }



    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse

    {

        $customer->update($request->validated());



        return redirect()

            ->route('customers.show', $customer)

            ->with('status', 'Customer updated successfully.');

    }



    public function destroy(Customer $customer): RedirectResponse

    {

        $this->authorizeSuperAdmin();

        $customer->delete();



        return redirect()

            ->route('customers.index')

            ->with('status', 'Customer deleted successfully.');

    }

}


