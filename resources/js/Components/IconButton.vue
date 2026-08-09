<script setup>
import AppIcon from '@/Components/AppIcon.vue';

defineProps({
    label: { type: String, required: true },
    icon: { type: String, default: 'view' },
    color: { type: String, default: 'slate' },
    type: { type: String, default: 'button' },
    href: { type: String, default: '' },
    target: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
});

const colors = {
    amber: 'text-amber-400 hover:bg-amber-400/10 hover:text-amber-300 border-amber-400/20',
    slate: 'text-slate-300 hover:bg-slate-800 hover:text-white border-slate-700',
    emerald: 'text-emerald-400 hover:bg-emerald-500/10 hover:text-emerald-300 border-emerald-500/20',
    red: 'text-red-400 hover:bg-red-500/10 hover:text-red-300 border-red-500/20',
};
</script>

<template>
    <a
        v-if="type === 'link' && href"
        :href="href"
        :target="target"
        :title="label"
        :aria-label="label"
        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border transition"
        :class="colors[color] ?? colors.slate"
    >
        <AppIcon :name="icon" class="h-4 w-4" />
    </a>
    <button
        v-else
        :type="type === 'submit' ? 'submit' : 'button'"
        :title="label"
        :aria-label="label"
        :disabled="disabled"
        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border transition disabled:opacity-50"
        :class="colors[color] ?? colors.slate"
    >
        <AppIcon :name="icon" class="h-4 w-4" />
    </button>
</template>
