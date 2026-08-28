<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({
    backups: Array,
});

const formatSize = (bytes) => {
    if (!bytes) {
        return '0 KB';
    }

    return `${Math.max(1, Math.round(bytes / 1024))} KB`;
};

const createBackup = () => {
    router.post('/backups');
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Backups" />

        <div class="space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-white">Monthly backups</h1>
                    <p class="mt-1 text-slate-400">Super admin can save and download shop data backups.</p>
                </div>
                <button
                    type="button"
                    class="rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-300"
                    @click="createBackup"
                >
                    Save backup now
                </button>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-800 bg-slate-950/50 text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">File</th>
                            <th class="px-4 py-3 font-medium">Size</th>
                            <th class="px-4 py-3 font-medium">Saved</th>
                            <th class="px-4 py-3 font-medium text-right">Download</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr v-if="backups.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No backups yet.</td>
                        </tr>
                        <tr v-for="backup in backups" :key="backup.name" class="hover:bg-slate-950/40">
                            <td class="px-4 py-3 font-medium text-white">{{ backup.name }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ formatSize(backup.size) }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ backup.updated_at }}</td>
                            <td class="px-4 py-3 text-right">
                                <a
                                    :href="`/backups/${backup.name}/download`"
                                    class="text-sm font-medium text-amber-400 hover:text-amber-300"
                                >
                                    Download
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
