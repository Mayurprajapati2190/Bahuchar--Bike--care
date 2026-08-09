<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const message = computed(() => page.props.flash?.status);
</script>

<template>
    <div
        v-if="message"
        class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200"
    >
        {{ message }}
    </div>
</template>
