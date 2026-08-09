<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    code: '',
    recovery_code: '',
});

const submit = () => {
    form.post('/two-factor-challenge');
};
</script>

<template>
    <Head title="Two-Factor Challenge" />

    <GuestLayout title="Two-factor authentication">
        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="code" class="mb-1 block text-sm text-slate-300">Authentication code</label>
                <input
                    id="code"
                    v-model="form.code"
                    type="text"
                    inputmode="numeric"
                    autofocus
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none ring-amber-400 transition focus:ring-2"
                />
                <p v-if="form.errors.code" class="mt-1 text-sm text-red-400">{{ form.errors.code }}</p>
            </div>

            <div>
                <label for="recovery_code" class="mb-1 block text-sm text-slate-300">Recovery code</label>
                <input
                    id="recovery_code"
                    v-model="form.recovery_code"
                    type="text"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none ring-amber-400 transition focus:ring-2"
                />
                <p v-if="form.errors.recovery_code" class="mt-1 text-sm text-red-400">
                    {{ form.errors.recovery_code }}
                </p>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-amber-400 px-4 py-2.5 font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-60"
                :disabled="form.processing"
            >
                Verify
            </button>
        </form>
    </GuestLayout>
</template>
