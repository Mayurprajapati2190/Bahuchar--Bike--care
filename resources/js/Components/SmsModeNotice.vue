<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const smsFreeMode = computed(() => usePage().props.notifications?.smsMode === 'free');
</script>

<template>
    <div
        v-if="smsFreeMode"
        class="rounded-xl border border-sky-500/25 bg-sky-500/10 px-4 py-3 text-sm text-sky-100"
    >
        <span class="font-semibold text-sky-200">Free SMS mode</span>
        — messages are saved here and in the log. To send real texts, set
        <code class="rounded bg-slate-800 px-1 text-xs">MSG91_ENABLED=true</code>
        in <code class="rounded bg-slate-800 px-1 text-xs">.env</code>.
    </div>
</template>
