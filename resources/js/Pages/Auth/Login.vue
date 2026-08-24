<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <GuestLayout title="Sign in to your account">
        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="email" class="mb-1 block text-sm text-slate-300">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none ring-amber-400 transition focus:ring-2"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm text-slate-300">Password</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none ring-amber-400 transition focus:ring-2"
                />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-400">{{ form.errors.password }}</p>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input
                    v-model="form.remember"
                    type="checkbox"
                    class="rounded border-slate-600 bg-slate-950 text-amber-400 focus:ring-amber-400"
                />
                Remember me
            </label>

            <button
                type="submit"
                class="w-full rounded-lg bg-amber-400 px-4 py-2.5 font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-60"
                :disabled="form.processing"
            >
                Log in
            </button>

            <div class="flex items-center justify-between text-sm">
                <Link href="/" class="text-slate-400 hover:text-white">
                    ← Back to website
                </Link>
                <Link href="/forgot-password" class="text-amber-400 hover:text-amber-300">
                    Forgot password?
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
