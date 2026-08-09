<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PaymentBadge from '@/Components/PaymentBadge.vue';
import PageActions from '@/Components/PageActions.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    bill: Object,
    shop: Object,
});

const showPaymentModal = ref(false);

const paymentForm = useForm({
    payment_status: props.bill.payment_status,
    amount_paid: props.bill.amount_paid ?? props.bill.total_amount,
    payment_method: props.bill.payment_method ?? 'cash',
});

const balanceDue = computed(() =>
    Math.max(0, Number(props.bill.total_amount) - Number(props.bill.amount_paid ?? 0)),
);

const formatDate = (value) =>
    value ? new Date(value).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value ?? 0);

const updatePayment = () => {
    paymentForm.patch(`/bills/${props.bill.id}/payment`, {
        onSuccess: () => {
            showPaymentModal.value = false;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="bill.bill_number" />

        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <Link href="/bills" class="text-sm text-amber-400 hover:text-amber-300">← All Bills</Link>
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-bold text-white">{{ bill.bill_number }}</h1>
                        <PaymentBadge :status="bill.payment_status" />
                    </div>
                    <p class="mt-1 text-slate-400">Tax Invoice · {{ formatDate(bill.bill_date) }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-if="bill.payment_status !== 'paid'"
                        type="button"
                        class="inline-flex h-9 items-center rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-400"
                        @click="showPaymentModal = true"
                    >
                        Update Payment
                    </button>
                    <PageActions
                        :print-href="`/bills/${bill.id}/print`"
                        print-label="Print bill"
                        :view-href="`/services/${bill.service_record_id}`"
                        view-label="View service"
                        :delete-href="`/bills/${bill.id}`"
                        delete-label="Delete bill"
                        delete-message="This will permanently delete the bill and its service record."
                    />
                </div>
            </div>

            <div
                v-if="bill.payment_status !== 'paid'"
                class="rounded-2xl border border-red-500/25 bg-red-500/5 p-5"
            >
                <p class="text-sm font-medium text-red-200">Payment Pending</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ formatCurrency(balanceDue) }} due</p>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-white p-8 text-slate-900 shadow-2xl">
                <div class="flex flex-col gap-6 border-b border-slate-200 pb-6 sm:flex-row sm:justify-between">
                    <div>
                        <p class="text-2xl font-bold text-amber-600">{{ shop.name }}</p>
                        <p v-if="shop.address" class="mt-2 text-sm text-slate-600">{{ shop.address }}</p>
                        <p v-if="shop.phone" class="text-sm text-slate-600">Phone: {{ shop.phone }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-3xl font-bold tracking-wide">TAX INVOICE</p>
                        <p class="mt-2 font-semibold">{{ bill.bill_number }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Bill To</p>
                        <p class="mt-1 font-semibold">{{ bill.service_record?.customer?.name }}</p>
                        <p class="text-sm text-slate-600">Phone: {{ bill.service_record?.customer?.phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Vehicle</p>
                        <p class="mt-1 font-semibold">
                            {{ bill.service_record?.bike?.brand }} {{ bill.service_record?.bike?.model }}
                        </p>
                    </div>
                </div>

                <table class="mt-8 w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">#</th>
                            <th class="py-3 pr-4">Description</th>
                            <th class="py-3 pr-4 text-right">Qty</th>
                            <th class="py-3 pr-4 text-right">Rate</th>
                            <th class="py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in bill.service_record?.items ?? []"
                            :key="item.id"
                            class="border-b border-slate-100"
                        >
                            <td class="py-3 pr-4">{{ index + 1 }}</td>
                            <td class="py-3 pr-4">{{ item.description }}</td>
                            <td class="py-3 pr-4 text-right">{{ item.quantity }}</td>
                            <td class="py-3 pr-4 text-right">{{ formatCurrency(item.unit_price) }}</td>
                            <td class="py-3 text-right font-medium">{{ formatCurrency(item.amount) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>{{ formatCurrency(bill.subtotal) }}</span></div>
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-lg font-bold">
                            <span>Total</span><span class="text-amber-600">{{ formatCurrency(bill.total_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-700"><span>Paid</span><span>{{ formatCurrency(bill.amount_paid) }}</span></div>
                        <div v-if="balanceDue > 0" class="flex justify-between font-bold text-red-600">
                            <span>Balance Due</span><span>{{ formatCurrency(balanceDue) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showPaymentModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm"
            @click.self="showPaymentModal = false"
        >
            <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-6">
                <h3 class="text-lg font-semibold text-white">Update Payment</h3>
                <form class="mt-5 space-y-4" @submit.prevent="updatePayment">
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-300">Payment Status</label>
                        <select v-model="paymentForm.payment_status" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                            <option value="paid">Paid (Full)</option>
                            <option value="partial">Partial</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div v-if="paymentForm.payment_status === 'partial'">
                        <label class="mb-1.5 block text-sm text-slate-300">Amount Received (₹)</label>
                        <input
                            v-model="paymentForm.amount_paid"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white"
                        />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-300">Payment Method</label>
                        <select v-model="paymentForm.payment_method" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-300" @click="showPaymentModal = false">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white" :disabled="paymentForm.processing">
                            Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
