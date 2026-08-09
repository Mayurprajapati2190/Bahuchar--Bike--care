<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PaginationLinks from '@/Components/PaginationLinks.vue';
import TableActions from '@/Components/TableActions.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get('/customers', { search: value || undefined }, { preserveState: true, replace: true });
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Customers" />

        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-white">Customers</h1>
                    <p class="mt-1 text-slate-400">Manage customer records and bikes</p>
                </div>
                <Link
                    href="/customers/create"
                    class="inline-flex justify-center rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-300"
                >
                    Add Customer
                </Link>
            </div>

            <div>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by name or phone..."
                    class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white placeholder:text-slate-500 focus:border-amber-400 focus:outline-none sm:max-w-md"
                />
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-slate-800 bg-slate-950/50 text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Phone</th>
                                <th class="px-4 py-3 font-medium">Bikes</th>
                                <th class="px-4 py-3 font-medium">Services</th>
                                <th class="px-4 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-if="customers.data.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">No customers found.</td>
                            </tr>
                            <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-slate-950/40">
                                <td class="px-4 py-3 font-medium text-white">{{ customer.name }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ customer.phone }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ customer.bikes_count }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ customer.service_records_count }}</td>
                                <td class="px-4 py-3">
                                    <TableActions
                                        :view-href="`/customers/${customer.id}`"
                                        view-label="View customer"
                                        :delete-href="`/customers/${customer.id}`"
                                        delete-label="Delete customer"
                                        delete-message="This will permanently delete the customer, their bikes, services, and bills."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-800 px-4 py-3">
                    <PaginationLinks :links="customers.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
