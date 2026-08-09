<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';

defineProps({
    href: { type: String, required: true },
    label: { type: String, default: 'View' },
    icon: { type: String, default: 'view' },
    color: { type: String, default: 'amber' },
});

const colors = {
    amber: 'text-amber-400 hover:bg-amber-400/10 hover:text-amber-300',
    slate: 'text-slate-400 hover:bg-slate-800 hover:text-white',
    emerald: 'text-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-300',
};
</script>

<template>
    <Link
        :href="href"
        :title="label"
        :aria-label="label"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition"
        :class="colors[color] ?? colors.amber"
    >
        <AppIcon :name="icon" class="h-4 w-4" />
    </Link>
</template>
