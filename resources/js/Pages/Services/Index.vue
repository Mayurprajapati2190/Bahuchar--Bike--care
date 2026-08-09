<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PaginationLinks from '@/Components/PaginationLinks.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PaymentBadge from '@/Components/PaymentBadge.vue';
import TableActions from '@/Components/TableActions.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    services: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(
        '/services',
        {
            search: search.value || undefined,
            status: status.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

watch([search, status], applyFilters);

const formatDate = (value) =>
    value ? new Date(value).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value ?? 0);
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Services" />

        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-white">Service Records</h1>
                    <p class="mt-1 text-slate-400">Track bike services and SMS notifications</p>
                </div>
                <Link
                    href="/services/create"
                    class="inline-flex justify-center rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-300"
                >
                    New Service
                </Link>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search customer..."
                    class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white placeholder:text-slate-500 focus:border-amber-400 focus:outline-none sm:max-w-xs"
                />
                <select
                    v-model="status"
                    class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white focus:border-amber-400 focus:outline-none sm:max-w-xs"
                >
                    <option value="">All statuses</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-slate-800 bg-slate-950/50 text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Date</th>
                                <th class="px-4 py-3 font-medium">Customer</th>
                                <th class="px-4 py-3 font-medium">Bike</th>
                                <th class="px-4 py-3 font-medium">Amount</th>
                                <th class="px-4 py-3 font-medium">Bill</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-if="services.data.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">No service records found.</td>
                            </tr>
                            <tr v-for="service in services.data" :key="service.id" class="hover:bg-slate-950/40">
                                <td class="px-4 py-3 text-slate-300">{{ formatDate(service.service_date) }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-white">{{ service.customer?.name }}</p>
                                    <p class="text-slate-500">{{ service.customer?.phone }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    {{ service.bike?.brand }} {{ service.bike?.model }}
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ formatCurrency(service.total_amount) }}</td>
                                <td class="px-4 py-3">
                                    <Link
                                        v-if="service.bill"
                                        :href="`/bills/${service.bill.id}`"
                                        class="text-amber-400 hover:text-amber-300"
                                    >
                                        {{ service.bill.bill_number }}
                                    </Link>
                                    <span v-else class="text-slate-600">—</span>
                                </td>
                                <td class="px-4 py-3"><StatusBadge :status="service.status" /></td>
                                <td class="px-4 py-3">
                                    <TableActions
                                        :view-href="`/services/${service.id}`"
                                        view-label="View service"
                                        :delete-href="`/services/${service.id}`"
                                        delete-label="Delete service"
                                        delete-message="This will permanently delete this service record and its bill."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-800 px-4 py-3">
                    <PaginationLinks :links="services.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
