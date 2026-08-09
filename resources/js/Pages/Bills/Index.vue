<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PaginationLinks from '@/Components/PaginationLinks.vue';
import PaymentBadge from '@/Components/PaymentBadge.vue';
import TableActions from '@/Components/TableActions.vue';
import StatCard from '@/Components/StatCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    bills: Object,
    filters: Object,
    summary: Object,
});

const search = ref(props.filters.search ?? '');
const payment = ref(props.filters.payment ?? '');

const isPendingView = computed(() => payment.value === 'pending');

watch(search, (value) => {
    router.get('/bills', { search: value || undefined, payment: payment.value || undefined }, { preserveState: true, replace: true });
});

const setPayment = (value) => {
    payment.value = value;
    const url = value === 'pending' ? '/bills/pending/list' : '/bills';
    router.get(url, { search: search.value || undefined, payment: value || undefined }, { preserveState: true, replace: true });
};

const formatDate = (value) =>
    value ? new Date(value).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value ?? 0);

const balanceDue = (bill) => Math.max(0, Number(bill.total_amount) - Number(bill.amount_paid ?? 0));
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="isPendingView ? 'Pending Payments' : 'Bills'" />

        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        {{ isPendingView ? 'Pending Payments' : 'Bills & Invoices' }}
                    </h1>
                    <p class="mt-1 text-slate-400">
                        {{ isPendingView ? 'Track unpaid and partial bills separately' : 'All service invoices' }}
                    </p>
                </div>
            </div>

            <div v-if="summary.pendingCount > 0" class="grid gap-4 sm:grid-cols-2">
                <StatCard title="Pending Bills" accent="red">{{ summary.pendingCount }}</StatCard>
                <StatCard title="Outstanding Amount" accent="red">{{ formatCurrency(summary.pendingAmount) }}</StatCard>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    class="rounded-xl px-4 py-2 text-sm font-medium transition"
                    :class="!payment ? 'bg-amber-400 text-slate-950' : 'border border-slate-700 text-slate-300 hover:border-amber-400'"
                    @click="setPayment('')"
                >
                    All Bills
                </button>
                <button
                    type="button"
                    class="rounded-xl px-4 py-2 text-sm font-medium transition"
                    :class="payment === 'paid' ? 'bg-emerald-500 text-white' : 'border border-slate-700 text-slate-300 hover:border-emerald-500'"
                    @click="setPayment('paid')"
                >
                    Paid
                </button>
                <button
                    type="button"
                    class="rounded-xl px-4 py-2 text-sm font-medium transition"
                    :class="payment === 'pending' ? 'bg-red-500 text-white' : 'border border-slate-700 text-slate-300 hover:border-red-500'"
                    @click="setPayment('pending')"
                >
                    Pending
                    <span v-if="summary.pendingCount" class="ml-1 rounded-full bg-white/20 px-1.5 text-xs">{{ summary.pendingCount }}</span>
                </button>
            </div>

            <input
                v-model="search"
                type="search"
                placeholder="Search bill number, customer..."
                class="w-full rounded-xl border border-slate-700 bg-slate-900/80 px-4 py-3 text-white placeholder:text-slate-500 focus:border-amber-400 focus:outline-none sm:max-w-md"
            />

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/60 backdrop-blur">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-slate-800 bg-slate-950/60 text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Bill No.</th>
                                <th class="px-4 py-3 font-medium">Date</th>
                                <th class="px-4 py-3 font-medium">Customer</th>
                                <th class="px-4 py-3 font-medium">Total</th>
                                <th class="px-4 py-3 font-medium">Balance</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-if="bills.data.length === 0">
                                <td colspan="7" class="px-4 py-10 text-center text-slate-500">No bills found.</td>
                            </tr>
                            <tr v-for="bill in bills.data" :key="bill.id" class="transition hover:bg-slate-950/40">
                                <td class="px-4 py-3 font-semibold text-amber-300">{{ bill.bill_number }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ formatDate(bill.bill_date) }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-white">{{ bill.service_record?.customer?.name }}</p>
                                    <p class="text-slate-500">{{ bill.service_record?.customer?.phone }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ formatCurrency(bill.total_amount) }}</td>
                                <td class="px-4 py-3 font-medium" :class="balanceDue(bill) > 0 ? 'text-red-300' : 'text-emerald-300'">
                                    {{ formatCurrency(balanceDue(bill)) }}
                                </td>
                                <td class="px-4 py-3"><PaymentBadge :status="bill.payment_status" /></td>
                                <td class="px-4 py-3">
                                    <TableActions
                                        :view-href="`/bills/${bill.id}`"
                                        view-label="View bill"
                                        :delete-href="`/bills/${bill.id}`"
                                        delete-label="Delete bill"
                                        delete-message="This will permanently delete the bill and its service record."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-800 px-4 py-3">
                    <PaginationLinks :links="bills.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
