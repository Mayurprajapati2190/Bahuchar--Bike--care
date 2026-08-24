<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import PaymentBadge from '@/Components/PaymentBadge.vue';
import IconLink from '@/Components/IconLink.vue';
import CustomerMessagingNotice from '@/Components/CustomerMessagingNotice.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    completedToday: Array,
    upcomingReminders: Array,
    pendingPayments: Array,
    recentSms: Array,
});

const formatDate = (value) => {
    if (!value) return '—';
    return new Date(value).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value ?? 0);
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="space-y-8">
            <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-400">Overview</p>
                    <h1 class="mt-2 text-4xl font-bold text-white">Dashboard</h1>
                    <p class="mt-2 text-slate-400">Bahuchar Bike Care — garage at a glance</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link href="/services/create" class="rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-950 hover:bg-amber-300">
                        + New Service
                    </Link>
                    <Link href="/customers/create" class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-white hover:border-amber-400">
                        + Customer
                    </Link>
                </div>
            </section>

            <CustomerMessagingNotice />

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <StatCard title="Total Customers" accent="blue">{{ stats.totalCustomers }}</StatCard>
                <StatCard title="Services This Month" accent="amber">{{ stats.servicesThisMonth }}</StatCard>
                <StatCard title="In Progress" accent="amber">{{ stats.inProgress }}</StatCard>
                <StatCard title="Due Reminders (7 days)" accent="emerald">{{ stats.dueReminders }}</StatCard>
                <StatCard title="Pending Payments" accent="red" :subtitle="formatCurrency(stats.pendingAmount) + ' outstanding'">
                    {{ stats.pendingPayments }}
                </StatCard>
            </section>

            <section
                v-if="pendingPayments.length > 0"
                class="rounded-2xl border border-red-500/20 bg-gradient-to-br from-red-500/5 to-slate-900/70 p-6"
            >
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-red-200">Pending Payments</h2>
                        <p class="text-sm text-slate-400">Bills awaiting customer payment</p>
                    </div>
                    <Link href="/bills/pending/list" class="text-sm font-medium text-amber-400 hover:text-amber-300">
                        View all →
                    </Link>
                </div>
                <ul class="space-y-3">
                    <li
                        v-for="bill in pendingPayments"
                        :key="bill.id"
                        class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/60 px-4 py-3"
                    >
                        <div>
                            <Link :href="`/bills/${bill.id}`" class="font-semibold text-white hover:text-amber-300">
                                {{ bill.bill_number }}
                            </Link>
                            <p class="text-sm text-slate-400">{{ bill.service_record?.customer?.name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-red-300">{{ formatCurrency(bill.balance_due) }}</p>
                            <div class="mt-1 flex items-center justify-end gap-1">
                                <PaymentBadge :status="bill.payment_status" />
                                <IconLink :href="`/bills/${bill.id}`" label="View bill" />
                            </div>
                        </div>
                    </li>
                </ul>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 backdrop-blur">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Completed Today</h2>
                        <Link href="/services?status=completed" class="text-sm text-amber-400 hover:text-amber-300">View all</Link>
                    </div>
                    <div v-if="completedToday.length === 0" class="rounded-xl border border-dashed border-slate-800 py-8 text-center text-sm text-slate-500">
                        No services completed today yet.
                    </div>
                    <ul v-else class="space-y-3">
                        <li
                            v-for="service in completedToday"
                            :key="service.id"
                            class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3"
                        >
                            <div>
                                <Link :href="`/services/${service.id}`" class="font-medium text-white hover:text-amber-300">
                                    {{ service.customer?.name }}
                                </Link>
                                <p class="text-sm text-slate-400">{{ service.bike?.brand }} {{ service.bike?.model }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <p class="font-medium text-emerald-300">{{ formatCurrency(service.total_amount) }}</p>
                                <IconLink :href="`/services/${service.id}`" label="View service" color="slate" />
                            </div>
                        </li>
                    </ul>
                </section>

                <section class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 backdrop-blur">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Upcoming Reminders</h2>
                        <Link href="/services?status=completed" class="text-sm text-amber-400 hover:text-amber-300">View services</Link>
                    </div>
                    <div v-if="upcomingReminders.length === 0" class="rounded-xl border border-dashed border-slate-800 py-8 text-center text-sm text-slate-500">
                        No reminders due in the next 7 days.
                    </div>
                    <ul v-else class="space-y-3">
                        <li
                            v-for="service in upcomingReminders"
                            :key="service.id"
                            class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3"
                        >
                            <div>
                                <Link :href="`/services/${service.id}`" class="font-medium text-white hover:text-amber-300">
                                    {{ service.customer?.name }}
                                </Link>
                                <p class="text-sm text-slate-400">{{ service.customer?.phone }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-amber-300">{{ formatDate(service.next_service_due_at) }}</p>
                                <IconLink :href="`/services/${service.id}`" label="View service" color="slate" />
                            </div>
                        </li>
                    </ul>
                </section>
            </div>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 backdrop-blur">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Recent SMS Log</h2>
                        <p class="text-sm text-slate-400">Free mode — messages saved here; see storage/logs/laravel.log for detail</p>
                    </div>
                </div>
                <div v-if="recentSms.length === 0" class="rounded-xl border border-dashed border-slate-800 py-8 text-center text-sm text-slate-500">
                    No SMS recorded yet. Complete a service to log a confirmation message.
                </div>
                <ul v-else class="space-y-3">
                    <li
                        v-for="sms in recentSms"
                        :key="sms.id"
                        class="rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <Link
                                    v-if="sms.service_record"
                                    :href="`/services/${sms.service_record.id}`"
                                    class="font-medium text-white hover:text-amber-300"
                                >
                                    {{ sms.service_record.customer?.name ?? 'Customer' }}
                                </Link>
                                <span class="ml-2 capitalize text-xs text-amber-300">{{ sms.type }}</span>
                            </div>
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="sms.status === 'sent' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-red-500/15 text-red-300'"
                            >
                                {{ sms.status }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-slate-400">{{ sms.body }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ sms.phone }} · {{ formatDate(sms.sent_at) }}</p>
                    </li>
                </ul>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
