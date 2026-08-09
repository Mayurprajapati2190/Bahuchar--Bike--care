<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    accent: { type: String, default: 'amber' },
});

const accents = {
    amber: 'border-amber-400/30 from-amber-400/10',
    emerald: 'border-emerald-500/30 from-emerald-500/10',
    red: 'border-red-500/30 from-red-500/10',
    blue: 'border-sky-500/30 from-sky-500/10',
};
</script>

<template>
    <article
        class="rounded-2xl border bg-gradient-to-br to-slate-900/70 p-5 shadow-lg shadow-slate-950/40"
        :class="accents[accent] ?? accents.amber"
    >
        <p class="text-sm text-slate-400">{{ title }}</p>
        <p class="mt-2 text-3xl font-bold tracking-tight text-white">
            <slot />
        </p>
        <p v-if="subtitle" class="mt-1 text-xs text-slate-500">{{ subtitle }}</p>
    </article>
</template>
