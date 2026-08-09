<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav v-if="links.length > 3" class="mt-6 flex flex-wrap gap-2">
        <Link
            v-for="link in links"
            :key="link.label"
            :href="link.url ?? '#'"
            class="rounded-lg border px-3 py-1.5 text-sm transition"
            :class="
                link.active
                    ? 'border-amber-400/40 bg-amber-400/10 text-amber-300'
                    : link.url
                      ? 'border-slate-700 text-slate-300 hover:border-slate-500'
                      : 'cursor-not-allowed border-slate-800 text-slate-600'
            "
            :preserve-scroll="true"
            v-html="link.label"
        />
    </nav>
</template>
