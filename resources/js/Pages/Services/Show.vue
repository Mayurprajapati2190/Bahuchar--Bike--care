<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PaymentBadge from '@/Components/PaymentBadge.vue';
import PageActions from '@/Components/PageActions.vue';
import SmsModeNotice from '@/Components/SmsModeNotice.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    service: Object,
});

const showCompleteModal = ref(false);

const completeForm = useForm({
    payment_status: 'paid',
    payment_method: 'cash',
});

const openCompleteModal = () => {
    showCompleteModal.value = true;
};

const completeService = () => {
    completeForm.post(`/services/${props.service.id}/complete`, {
        onSuccess: () => {
            showCompleteModal.value = false;
        },
    });
};

const sendReminder = () => {
    if (confirm('Send reminder SMS to this customer now?')) {
        router.post(`/services/${props.service.id}/send-reminder`);
    }
};

const formatDate = (value) =>
    value ? new Date(value).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const formatDateTime = (value) =>
    value
        ? new Date(value).toLocaleString('en-IN', {
              day: 'numeric',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
          })
        : '—';

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value ?? 0);
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Service #${service.id}`" />

        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <Link href="/services" class="text-sm text-amber-400 hover:text-amber-300">← Services</Link>
                    <div class="mt-2 flex items-center gap-3">
                        <h1 class="text-3xl font-semibold text-white">Service #{{ service.id }}</h1>
                        <StatusBadge :status="service.status" />
                    </div>
                    <p class="mt-1 text-slate-400">{{ service.customer?.name }} · {{ service.customer?.phone }}</p>
                    <p v-if="service.bill" class="mt-2 flex items-center gap-2 text-sm">
                        <span class="text-slate-400">Bill:</span>
                        <Link :href="`/bills/${service.bill.id}`" class="font-medium text-amber-300 underline hover:text-amber-200">
                            {{ service.bill.bill_number }}
                        </Link>
                        <PaymentBadge :status="service.bill.payment_status" />
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <PageActions
                        v-if="service.status === 'in_progress'"
                        :edit-href="`/services/${service.id}/edit`"
                        edit-label="Edit service"
                        :delete-href="`/services/${service.id}`"
                        delete-label="Delete service"
                        delete-message="This will permanently delete this service record, bill, and related data."
                    />
                    <PageActions
                        v-else
                        :print-href="service.bill ? `/bills/${service.bill.id}/print` : ''"
                        print-label="Print bill"
                        :view-href="service.bill ? `/bills/${service.bill.id}` : ''"
                        view-label="View bill"
                        :delete-href="`/services/${service.id}`"
                        delete-label="Delete service"
                        delete-message="This will permanently delete this service record, bill, and related data."
                    />
                    <button
                        v-if="service.status === 'in_progress'"
                        type="button"
                        class="inline-flex h-9 items-center rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-400"
                        @click="openCompleteModal"
                    >
                        Complete & Bill
                    </button>
                    <button
                        v-if="service.status === 'completed'"
                        type="button"
                        class="inline-flex h-9 items-center rounded-xl border border-amber-400/40 px-4 text-sm text-amber-300 hover:bg-amber-400/10"
                        @click="sendReminder"
                    >
                        Send SMS
                    </button>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 lg:col-span-2">
                    <h2 class="mb-4 text-lg font-semibold text-white">Bill Items</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="text-slate-400">
                                <tr>
                                    <th class="pb-3 pr-4 font-medium">Description</th>
                                    <th class="pb-3 pr-4 font-medium text-right">Qty</th>
                                    <th class="pb-3 pr-4 font-medium text-right">Rate</th>
                                    <th class="pb-3 font-medium text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr v-for="item in service.items" :key="item.id">
                                    <td class="py-3 pr-4 text-white">{{ item.description }}</td>
                                    <td class="py-3 pr-4 text-right text-slate-300">{{ item.quantity }}</td>
                                    <td class="py-3 pr-4 text-right text-slate-300">{{ formatCurrency(item.unit_price) }}</td>
                                    <td class="py-3 text-right text-emerald-300">{{ formatCurrency(item.amount) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="pt-4 text-right font-medium text-slate-300">Total</td>
                                    <td class="pt-4 text-right text-xl font-semibold text-amber-300">
                                        {{ formatCurrency(service.total_amount) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div v-if="service.work_done" class="mt-4 border-t border-slate-800 pt-4">
                        <p class="text-sm text-slate-400">Work Done / Notes</p>
                        <p class="mt-1 whitespace-pre-wrap text-slate-200">{{ service.work_done }}</p>
                    </div>
                </section>

                <div class="space-y-4">
                    <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                        <h2 class="mb-4 text-lg font-semibold text-white">Service Info</h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Service Date</dt>
                                <dd class="text-white">{{ formatDate(service.service_date) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Bike</dt>
                                <dd class="text-right text-white">
                                    {{ service.bike?.brand }} {{ service.bike?.model }}
                                </dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Completed</dt>
                                <dd class="text-white">{{ formatDateTime(service.completed_at) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Next Service</dt>
                                <dd class="text-amber-300">{{ formatDate(service.next_service_due_at) }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                        <h2 class="mb-4 text-lg font-semibold text-white">SMS Status</h2>
                        <SmsModeNotice class="mb-4" />
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Confirmation</dt>
                                <dd class="text-white">{{ formatDateTime(service.confirmation_sms_sent_at) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Reminder</dt>
                                <dd class="text-white">{{ formatDateTime(service.reminder_sms_sent_at) }}</dd>
                            </div>
                        </dl>
                        <div v-if="service.sms_messages?.length" class="mt-5 border-t border-slate-800 pt-4">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Message log</p>
                            <ul class="space-y-3">
                                <li
                                    v-for="sms in service.sms_messages"
                                    :key="sms.id"
                                    class="rounded-xl border border-slate-800 bg-slate-950/50 px-3 py-2 text-sm"
                                >
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="capitalize text-amber-300">{{ sms.type }}</span>
                                        <span
                                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="
                                                sms.status === 'sent'
                                                    ? 'bg-emerald-500/15 text-emerald-300'
                                                    : sms.status === 'failed'
                                                      ? 'bg-red-500/15 text-red-300'
                                                      : 'bg-slate-700 text-slate-300'
                                            "
                                        >
                                            {{ sms.status }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-slate-400">{{ sms.body }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ formatDateTime(sms.sent_at) }} · {{ sms.phone }}</p>
                                </li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div
            v-if="showCompleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4"
            @click.self="showCompleteModal = false"
        >
            <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-6">
                <h3 class="text-lg font-semibold text-white">Complete Service & Create Bill</h3>
                <p class="mt-2 text-sm text-slate-400">
                    A tax invoice will be generated and a confirmation SMS will be recorded for the customer.
                </p>

                <form class="mt-5 space-y-4" @submit.prevent="completeService">
                    <div>
                        <label class="mb-1.5 block text-sm text-slate-300">Payment Status</label>
                        <select
                            v-model="completeForm.payment_status"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                        >
                            <option value="paid">Paid — full payment received</option>
                            <option value="unpaid">Pending — payment not received yet</option>
                        </select>
                    </div>

                    <div v-if="completeForm.payment_status === 'paid'">
                        <label class="mb-1.5 block text-sm text-slate-300">Payment Method</label>
                        <select
                            v-model="completeForm.payment_method"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                        >
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-300"
                        @click="showCompleteModal = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-400 disabled:opacity-50"
                        :disabled="completeForm.processing"
                    >
                        Complete & Create Bill
                    </button>
                </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
