<script setup>
defineProps({
    status: { type: String, required: true },
});

const classes = {
    paid: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30',
    unpaid: 'bg-red-500/10 text-red-300 border-red-500/30',
    partial: 'bg-amber-400/10 text-amber-300 border-amber-400/30',
};

const labels = {
    paid: 'Paid',
    unpaid: 'Pending',
    partial: 'Partial',
};
</script>

<template>
    <span
        class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide"
        :class="classes[status] ?? classes.unpaid"
    >
        {{ labels[status] ?? status }}
    </span>
</template>
