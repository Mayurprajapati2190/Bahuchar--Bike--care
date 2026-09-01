<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const available = computed(() => page.props.teams?.available ?? []);
const current = computed(() => page.props.teams?.current ?? null);
const showSwitcher = computed(() => available.value.length > 1);

const switchTeam = (event) => {
    const teamId = Number(event.target.value);
    if (!teamId || teamId === current.value?.id) {
        return;
    }

    router.put('/current-team', { team_id: teamId }, {
        preserveState: false,
        preserveScroll: true,
    });
};
</script>

<template>
    <div v-if="showSwitcher" class="space-y-1">
        <label class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500">Shop team</label>
        <select
            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm font-medium text-white outline-none transition focus:border-amber-400"
            :value="current?.id ?? ''"
            @change="switchTeam"
        >
            <option v-for="team in available" :key="team.id" :value="team.id">
                {{ team.name }}
            </option>
        </select>
    </div>
    <p v-else-if="current" class="truncate text-xs text-slate-400">{{ current.name }}</p>
</template>
