<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';
import IconLink from '@/Components/IconLink.vue';
import PageActions from '@/Components/PageActions.vue';
import ServiceItemsEditor from '@/Components/ServiceItemsEditor.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import AppIcon from '@/Components/AppIcon.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, nextTick, ref, watch } from 'vue';

const props = defineProps({
    customer: Object,
});

const serviceFormRef = ref(null);
const showServiceForm = ref(false);

const bikeForm = useForm({
    brand: '',
    model: '',
    registration_number: '',
});

const serviceForm = useForm({
    customer_id: props.customer.id,
    bike_id: '',
    service_date: new Date().toISOString().slice(0, 10),
    work_done: '',
    items: [{ description: 'General service', quantity: 1, unit_price: '' }],
    return_to: 'customer',
});

const hasBikes = computed(() => props.customer.bikes?.length > 0);

const selectedBike = computed(() =>
    props.customer.bikes?.find((bike) => bike.id === Number(serviceForm.bike_id)),
);

watch(
    () => props.customer.bikes,
    (bikes) => {
        if (bikes?.length === 1 && !serviceForm.bike_id) {
            serviceForm.bike_id = bikes[0].id;
        }
    },
    { immediate: true },
);

const addBike = () => {
    bikeForm.post(`/customers/${props.customer.id}/bikes`, {
        onSuccess: () => bikeForm.reset(),
        preserveScroll: true,
    });
};

const openServiceForm = (bikeId = null) => {
    if (bikeId) {
        serviceForm.bike_id = bikeId;
    } else if (!serviceForm.bike_id && props.customer.bikes?.length === 1) {
        serviceForm.bike_id = props.customer.bikes[0].id;
    }
    showServiceForm.value = true;
    nextTick(() => {
        serviceFormRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
};

const submitService = () => {
    serviceForm.post('/services', {
        preserveScroll: true,
        onSuccess: () => {
            serviceForm.reset();
            serviceForm.customer_id = props.customer.id;
            serviceForm.service_date = new Date().toISOString().slice(0, 10);
            serviceForm.items = [{ description: 'General service', quantity: 1, unit_price: '' }];
            serviceForm.return_to = 'customer';
            if (props.customer.bikes?.length === 1) {
                serviceForm.bike_id = props.customer.bikes[0].id;
            }
            showServiceForm.value = false;
        },
    });
};

const formatDate = (value) =>
    value ? new Date(value).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value ?? 0);
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="customer.name" />

        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <Link href="/customers" class="text-sm text-amber-400 hover:text-amber-300">← Customers</Link>
                    <h1 class="mt-2 text-3xl font-semibold text-white">{{ customer.name }}</h1>
                    <p class="mt-1 text-slate-400">{{ customer.phone }}</p>
                    <p v-if="customer.email" class="text-slate-400">{{ customer.email }}</p>
                    <p v-if="customer.address" class="mt-1 text-sm text-slate-500">{{ customer.address }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <PageActions
                        :edit-href="`/customers/${customer.id}/edit`"
                        edit-label="Edit customer"
                        :delete-href="`/customers/${customer.id}`"
                        delete-label="Delete customer"
                        delete-message="This will permanently delete the customer, their bikes, services, and bills."
                    />
                    <button
                        v-if="hasBikes"
                        type="button"
                        class="inline-flex h-9 items-center gap-2 rounded-xl bg-amber-400 px-4 text-sm font-semibold text-slate-950 hover:bg-amber-300"
                        @click="openServiceForm()"
                    >
                        <AppIcon name="plus" class="h-4 w-4" />
                        Add Service
                    </button>
                </div>
            </div>

            <!-- Bikes -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-lg font-semibold text-white">Bikes</h2>

                <form class="mb-6 grid gap-3 sm:grid-cols-4" @submit.prevent="addBike">
                    <input
                        v-model="bikeForm.brand"
                        type="text"
                        placeholder="Brand *"
                        required
                        class="rounded-xl border border-slate-700 bg-slate-950 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none"
                    />
                    <input
                        v-model="bikeForm.model"
                        type="text"
                        placeholder="Model"
                        class="rounded-xl border border-slate-700 bg-slate-950 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none"
                    />
                    <input
                        v-model="bikeForm.registration_number"
                        type="text"
                        required
                        maxlength="50"
                        placeholder="Registration no. *"
                        class="rounded-xl border border-slate-700 bg-slate-950 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none"
                    />
                    <button
                        type="submit"
                        :disabled="bikeForm.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-700"
                    >
                        <AppIcon name="plus" class="h-4 w-4" />
                        Add Bike
                    </button>
                    <p v-if="bikeForm.errors.brand || bikeForm.errors.registration_number" class="text-sm text-red-400 sm:col-span-4">
                        {{ bikeForm.errors.brand || bikeForm.errors.registration_number }}
                    </p>
                </form>

                <div v-if="!hasBikes" class="rounded-xl border border-dashed border-amber-400/30 bg-amber-400/5 p-4 text-sm text-amber-200">
                    Add at least one bike before creating a service record.
                </div>
                <ul v-else class="space-y-2">
                    <li
                        v-for="bike in customer.bikes"
                        :key="bike.id"
                        class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3"
                    >
                        <div>
                            <p class="font-medium text-white">{{ bike.brand }} {{ bike.model }}</p>
                            <p v-if="bike.registration_number" class="text-sm text-slate-400">{{ bike.registration_number }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                title="Add service for this bike"
                                aria-label="Add service for this bike"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-400 transition hover:bg-emerald-500/10 hover:text-emerald-300"
                                @click="openServiceForm(bike.id)"
                            >
                                <AppIcon name="plus" class="h-4 w-4" />
                            </button>
                            <ConfirmDelete
                                :href="`/customers/${customer.id}/bikes/${bike.id}`"
                                label="Remove bike"
                                title="Remove bike?"
                                message="Remove this bike from the customer record?"
                                size="sm"
                            />
                        </div>
                    </li>
                </ul>
            </section>

            <!-- Inline Add Service Form -->
            <section
                v-if="showServiceForm && hasBikes"
                ref="serviceFormRef"
                class="rounded-2xl border border-amber-400/30 bg-gradient-to-br from-amber-400/5 to-slate-900/70 p-6"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">New Service for {{ customer.name }}</h2>
                        <p class="mt-1 text-sm text-slate-400">Fill in details below — no need to leave this page</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm text-slate-400 hover:bg-slate-800 hover:text-white"
                        @click="showServiceForm = false"
                    >
                        Cancel
                    </button>
                </div>

                <form class="space-y-5" @submit.prevent="submitService">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-300">Select Bike *</label>
                            <select
                                v-model="serviceForm.bike_id"
                                required
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            >
                                <option value="" disabled>Choose bike</option>
                                <option v-for="bike in customer.bikes" :key="bike.id" :value="bike.id">
                                    {{ bike.brand }} {{ bike.model }}
                                    {{ bike.registration_number ? `(${bike.registration_number})` : '' }}
                                </option>
                            </select>
                            <p v-if="serviceForm.errors.bike_id" class="mt-1 text-sm text-red-400">{{ serviceForm.errors.bike_id }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-300">Service Date *</label>
                            <input
                                v-model="serviceForm.service_date"
                                type="date"
                                required
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div v-if="selectedBike" class="rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3 text-sm text-slate-300">
                        Servicing: <span class="font-medium text-white">{{ selectedBike.brand }} {{ selectedBike.model }}</span>
                        <span v-if="selectedBike.registration_number" class="text-slate-500"> · {{ selectedBike.registration_number }}</span>
                    </div>

                    <ServiceItemsEditor v-model="serviceForm.items" :errors="serviceForm.errors" />

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-300">Work Done / Notes</label>
                        <textarea
                            v-model="serviceForm.work_done"
                            rows="3"
                            placeholder="Oil change, brake service, etc."
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                        />
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button
                            type="submit"
                            :disabled="serviceForm.processing"
                            class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-semibold text-slate-950 hover:bg-amber-300 disabled:opacity-50"
                        >
                            <AppIcon name="plus" class="h-4 w-4" />
                            Create Service
                        </button>
                        <button
                            type="button"
                            class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800"
                            @click="showServiceForm = false"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </section>

            <!-- Prompt to add service when bikes exist but form hidden -->
            <div
                v-else-if="hasBikes && !showServiceForm"
                class="flex flex-col items-start justify-between gap-3 rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 p-5 sm:flex-row sm:items-center"
            >
                <div>
                    <p class="font-medium text-white">Ready to record a service?</p>
                    <p class="mt-1 text-sm text-slate-400">Add service directly for {{ customer.name }} without leaving this page.</p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-950 hover:bg-amber-300"
                    @click="openServiceForm()"
                >
                    <AppIcon name="plus" class="h-4 w-4" />
                    Add Service
                </button>
            </div>

            <!-- Service History -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Service History</h2>
                    <span class="text-sm text-slate-500">{{ customer.service_records?.length ?? 0 }} recent</span>
                </div>
                <div v-if="!customer.service_records?.length" class="rounded-xl border border-dashed border-slate-800 py-8 text-center text-sm text-slate-500">
                    No service history yet. Add a service above to get started.
                </div>
                <ul v-else class="space-y-2">
                    <li
                        v-for="service in customer.service_records"
                        :key="service.id"
                        class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3"
                    >
                        <div>
                            <Link :href="`/services/${service.id}`" class="font-medium text-white hover:text-amber-300">
                                Service #{{ service.id }}
                            </Link>
                            <p class="text-sm text-slate-400">
                                {{ formatDate(service.service_date) }} · {{ service.bike?.brand }} {{ service.bike?.model }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span v-if="service.total_amount" class="hidden text-sm text-emerald-300 sm:block">
                                {{ formatCurrency(service.total_amount) }}
                            </span>
                            <StatusBadge :status="service.status" />
                            <IconLink :href="`/services/${service.id}`" label="View service" />
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
