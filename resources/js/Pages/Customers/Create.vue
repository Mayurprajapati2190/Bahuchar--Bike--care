<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceItemsEditor from '@/Components/ServiceItemsEditor.vue';
import AppIcon from '@/Components/AppIcon.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
    bike: {
        brand: '',
        model: '',
        registration_number: '',
    },
    add_service: true,
    service_date: new Date().toISOString().slice(0, 10),
    work_done: '',
    items: [{ description: 'General service', quantity: 1, unit_price: '' }],
});

const submitLabel = computed(() =>
    form.add_service ? 'Save Customer & Create Service' : 'Save Customer & Bike',
);

const submit = () => {
    form.post('/customers');
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Add Customer" />

        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <Link href="/customers" class="text-sm text-amber-400 hover:text-amber-300">← Back to customers</Link>
                <h1 class="mt-2 text-3xl font-semibold text-white">New Customer</h1>
                <p class="mt-1 text-slate-400">
                    Register customer, add their bike, and optionally start a service — all in one step.
                </p>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <!-- Customer -->
                <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-400/15 text-sm font-bold text-amber-300">1</span>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Customer Details</h2>
                            <p class="text-sm text-slate-500">Name and contact information</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm text-slate-300">Full Name *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Customer name"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Mobile *</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                required
                                maxlength="10"
                                pattern="[6-9][0-9]{9}"
                                placeholder="9876543210"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-400">{{ form.errors.phone }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="Optional"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm text-slate-300">Address</label>
                            <textarea
                                v-model="form.address"
                                rows="2"
                                placeholder="Optional"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                        </div>
                    </div>
                </section>

                <!-- Bike -->
                <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-400/15 text-sm font-bold text-amber-300">2</span>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Bike Details</h2>
                            <p class="text-sm text-slate-500">Required — every customer needs at least one bike</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Brand *</label>
                            <input
                                v-model="form.bike.brand"
                                type="text"
                                required
                                placeholder="Honda, Hero, TVS..."
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                            <p v-if="form.errors['bike.brand']" class="mt-1 text-sm text-red-400">{{ form.errors['bike.brand'] }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Model</label>
                            <input
                                v-model="form.bike.model"
                                type="text"
                                placeholder="Activa, Splendor..."
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Registration No. *</label>
                            <input
                                v-model="form.bike.registration_number"
                                type="text"
                                required
                                maxlength="50"
                                placeholder="GJ XX XX XXXX"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                            <p v-if="form.errors['bike.registration_number']" class="mt-1 text-sm text-red-400">
                                {{ form.errors['bike.registration_number'] }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Service -->
                <section class="rounded-2xl border border-amber-400/20 bg-gradient-to-br from-amber-400/5 to-slate-900/70 p-6">
                    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-400/15 text-sm font-bold text-amber-300">3</span>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Service Record</h2>
                                <p class="text-sm text-slate-500">Start a service job immediately after saving</p>
                            </div>
                        </div>
                        <label class="inline-flex cursor-pointer items-center gap-3 rounded-xl border border-slate-700 bg-slate-950/50 px-4 py-2.5">
                            <input v-model="form.add_service" type="checkbox" class="rounded border-slate-600 text-amber-400 focus:ring-amber-400" />
                            <span class="text-sm font-medium text-white">Record service now</span>
                        </label>
                    </div>

                    <div v-if="form.add_service" class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Service Date *</label>
                            <input
                                v-model="form.service_date"
                                type="date"
                                required
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none sm:max-w-xs"
                            />
                            <p v-if="form.errors.service_date" class="mt-1 text-sm text-red-400">{{ form.errors.service_date }}</p>
                        </div>

                        <ServiceItemsEditor v-model="form.items" :errors="form.errors" />

                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Work Done / Notes</label>
                            <textarea
                                v-model="form.work_done"
                                rows="3"
                                placeholder="Oil change, brake service, etc."
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                            />
                        </div>
                    </div>

                    <p v-else class="rounded-xl border border-dashed border-slate-700 px-4 py-3 text-sm text-slate-400">
                        Customer and bike will be saved. You can add a service later from the customer profile.
                    </p>
                </section>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-50"
                    >
                        <AppIcon name="plus" class="h-4 w-4" />
                        {{ submitLabel }}
                    </button>
                    <Link
                        href="/customers"
                        class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
