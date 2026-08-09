<script setup>
import AppLogo from '@/Components/AppLogo.vue';

defineProps({
    title: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6 py-12">
            <div class="mb-8 flex flex-col items-center text-center">
                <AppLogo icon-class="h-14 w-14" />
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
