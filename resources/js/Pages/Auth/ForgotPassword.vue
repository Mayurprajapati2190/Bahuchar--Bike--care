<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Forgot Password" />

    <GuestLayout title="Reset your password">
        <p v-if="page.props.flash?.status" class="mb-4 rounded-lg bg-emerald-500/10 px-3 py-2 text-sm text-emerald-300">
            {{ page.props.flash.status }}
        </p>

        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="email" class="mb-1 block text-sm text-slate-300">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none ring-amber-400 transition focus:ring-2"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-amber-400 px-4 py-2.5 font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-60"
                :disabled="form.processing"
            >
                Email reset link
            </button>

            <p class="text-center text-sm text-slate-400">
                <Link href="/login" class="text-amber-400 hover:text-amber-300">Back to login</Link>
            </p>
        </form>
    </GuestLayout>
</template>
