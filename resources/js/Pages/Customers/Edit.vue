<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    customer: Object,
});

const form = useForm({
    name: props.customer.name,
    phone: props.customer.phone,
    email: props.customer.email ?? '',
    address: props.customer.address ?? '',
});

const submit = () => {
    form.put(`/customers/${props.customer.id}`);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Edit Customer" />

        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <Link :href="`/customers/${customer.id}`" class="text-sm text-amber-400 hover:text-amber-300">
                    ← Back to customer
                </Link>
                <h1 class="mt-2 text-3xl font-semibold text-white">Edit Customer</h1>
            </div>

            <form class="space-y-5 rounded-2xl border border-slate-800 bg-slate-900/70 p-6" @submit.prevent="submit">
                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Mobile</label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        required
                        maxlength="10"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-400">{{ form.errors.phone }}</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Address</label>
                    <textarea
                        v-model="form.address"
                        rows="3"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-50"
                    >
                        Update Customer
                    </button>
                    <ConfirmDelete
                        :href="`/customers/${customer.id}`"
                        label="Delete customer"
                        message="This will permanently delete the customer and all related records."
                        size="md"
                    />
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
