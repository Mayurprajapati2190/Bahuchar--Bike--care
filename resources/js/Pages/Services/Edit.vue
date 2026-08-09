<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceItemsEditor from '@/Components/ServiceItemsEditor.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    service: Object,
});

const form = useForm({
    service_date: props.service.service_date?.slice(0, 10) ?? '',
    work_done: props.service.work_done ?? '',
    items:
        props.service.items?.length > 0
            ? props.service.items.map((item) => ({
                  description: item.description,
                  quantity: item.quantity,
                  unit_price: item.unit_price,
              }))
            : [{ description: '', quantity: 1, unit_price: '' }],
});

const submit = () => {
    form.put(`/services/${props.service.id}`);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Edit Service" />

        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <Link :href="`/services/${service.id}`" class="text-sm text-amber-400 hover:text-amber-300">
                    ← Back to service
                </Link>
                <h1 class="mt-2 text-3xl font-semibold text-white">Edit Service #{{ service.id }}</h1>
                <p class="mt-1 text-slate-400">{{ service.customer?.name }} · {{ service.bike?.brand }}</p>
            </div>

            <form class="space-y-5 rounded-2xl border border-slate-800 bg-slate-900/70 p-6" @submit.prevent="submit">
                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Service Date</label>
                    <input
                        v-model="form.service_date"
                        type="date"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none sm:max-w-xs"
                    />
                </div>

                <ServiceItemsEditor v-model="form.items" :errors="form.errors" />

                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Work Done / Notes</label>
                    <textarea
                        v-model="form.work_done"
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
                        Update Service
                    </button>
                    <ConfirmDelete
                        :href="`/services/${service.id}`"
                        label="Delete service"
                        message="This will permanently delete this service record."
                        size="md"
                    />
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
