<script setup>
import AppLogo from '@/Components/AppLogo.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-4 py-10 sm:px-6 sm:py-12">
            <div class="mb-6 flex flex-col items-center text-center">
                <Link href="/">
                    <AppLogo icon-class="h-14 w-14" />
                </Link>
                <p class="mt-4 text-sm font-medium uppercase tracking-[0.2em] text-amber-400">
                    Bahuchar Bike Care
                </p>
                <h1 v-if="title" class="mt-2 text-2xl font-semibold text-white">
                    {{ title }}
                </h1>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl shadow-black/30">
                <slot />
            </div>
        </div>
    </div>
</template>
