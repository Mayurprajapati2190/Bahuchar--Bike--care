<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceItemsEditor from '@/Components/ServiceItemsEditor.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    customers: Array,
    selectedCustomerId: Number,
    selectedBikeId: Number,
});

const form = useForm({
    customer_id: props.selectedCustomerId ? Number(props.selectedCustomerId) : '',
    bike_id: props.selectedBikeId ? Number(props.selectedBikeId) : '',
    service_date: new Date().toISOString().slice(0, 10),
    work_done: '',
    items: [{ description: 'General service', quantity: 1, unit_price: '' }],
    return_to: 'service',
});

const selectedCustomer = computed(() =>
    props.customers.find((customer) => customer.id === Number(form.customer_id)),
);

watch(
    () => form.customer_id,
    (customerId) => {
        const customer = props.customers.find((c) => c.id === Number(customerId));
        if (!customer?.bikes?.some((b) => b.id === Number(form.bike_id))) {
            form.bike_id = customer?.bikes?.length === 1 ? customer.bikes[0].id : '';
        }
    },
);

watch(
    () => props.selectedCustomerId,
    (id) => {
        if (id && !form.customer_id) {
            form.customer_id = Number(id);
        }
    },
    { immediate: true },
);

const backLink = computed(() =>
    props.selectedCustomerId ? `/customers/${props.selectedCustomerId}` : '/services',
);

const backLabel = computed(() =>
    props.selectedCustomerId ? 'Back to customer' : 'Back to services',
);

const submit = () => {
    form.post('/services');
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="New Service" />

        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <Link :href="backLink" class="text-sm text-amber-400 hover:text-amber-300">← {{ backLabel }}</Link>
                <h1 class="mt-2 text-3xl font-semibold text-white">New Service Record</h1>
                <p class="mt-1 text-slate-400">Add bill items — a tax invoice is generated when you complete the service.</p>
            </div>

            <form class="space-y-5 rounded-2xl border border-slate-800 bg-slate-900/70 p-6" @submit.prevent="submit">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">Customer *</label>
                        <select
                            v-model="form.customer_id"
                            required
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                        >
                            <option value="" disabled>Select customer</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.name }} ({{ customer.phone }})
                            </option>
                        </select>
                        <p v-if="form.errors.customer_id" class="mt-1 text-sm text-red-400">{{ form.errors.customer_id }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">Bike *</label>
                        <select
                            v-model="form.bike_id"
                            required
                            :disabled="!selectedCustomer?.bikes?.length"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none disabled:opacity-50"
                        >
                            <option value="" disabled>Select bike</option>
                            <option v-for="bike in selectedCustomer?.bikes ?? []" :key="bike.id" :value="bike.id">
                                {{ bike.brand }} {{ bike.model }}
                                {{ bike.registration_number ? `(${bike.registration_number})` : '' }}
                            </option>
                        </select>
                        <p v-if="selectedCustomer && !selectedCustomer.bikes?.length" class="mt-1 text-sm text-amber-300">
                            This customer has no bikes.
                            <Link :href="`/customers/${selectedCustomer.id}`" class="underline">Add a bike on the customer page</Link>.
                        </p>
                        <p v-if="form.errors.bike_id" class="mt-1 text-sm text-red-400">{{ form.errors.bike_id }}</p>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">Service Date *</label>
                    <input
                        v-model="form.service_date"
                        type="date"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none sm:max-w-xs"
                    />
                </div>

                <ServiceItemsEditor v-model="form.items" :errors="form.errors" />

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">Work Done / Notes</label>
                    <textarea
                        v-model="form.work_done"
                        rows="3"
                        placeholder="Additional notes for service record..."
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-50"
                >
                    Create Service
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
