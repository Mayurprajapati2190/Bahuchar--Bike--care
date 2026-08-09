<script setup>
defineProps({
    status: {
        type: String,
        required: true,
    },
});

const classes = {
    in_progress: 'bg-amber-400/10 text-amber-300 border-amber-400/20',
    completed: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
    pending: 'bg-slate-500/10 text-slate-300 border-slate-500/20',
    sent: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
    paid: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
    unpaid: 'bg-red-500/10 text-red-300 border-red-500/20',
    partial: 'bg-amber-400/10 text-amber-300 border-amber-400/20',
};

const labels = {
    in_progress: 'In Progress',
    completed: 'Completed',
    pending: 'Pending',
    sent: 'Sent',
    failed: 'Failed',
    paid: 'Paid',
    unpaid: 'Unpaid',
    partial: 'Partial',
};
</script>

<template>
    <span
        class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize"
        :class="classes[status] ?? classes.pending"
    >
        {{ labels[status] ?? status }}
    </span>
</template>
