<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    teams: Array,
});

const form = useForm({
    name: '',
    address: '',
    phone: '',
    hours: '',
    tagline: '',
    gstin: '',
    bill_prefix: 'BBC',
});

const editingId = ref(null);
const editForm = useForm({
    name: '',
    address: '',
    phone: '',
    hours: '',
    tagline: '',
    gstin: '',
    bill_prefix: '',
});

const submit = () => {
    form.post('/teams', {
        onSuccess: () => form.reset(),
    });
};

const startEdit = (team) => {
    editingId.value = team.id;
    editForm.name = team.name;
    editForm.address = team.address ?? '';
    editForm.phone = team.phone ?? '';
    editForm.hours = team.hours ?? '';
    editForm.tagline = team.tagline ?? '';
    editForm.gstin = team.gstin ?? '';
    editForm.bill_prefix = team.bill_prefix ?? 'BBC';
};

const saveEdit = () => {
    editForm.put(`/teams/${editingId.value}`, {
        onSuccess: () => {
            editingId.value = null;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Shop teams" />

        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-semibold text-white">Shop teams</h1>
                <p class="mt-1 text-slate-400">
                    Super admin can manage every shop. The last team you select is remembered the next time you open the app.
                </p>
            </div>

            <form class="grid gap-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 md:grid-cols-2" @submit.prevent="submit">
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-white">Add a shop team</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Phone</label>
                    <input
                        v-model="form.phone"
                        type="text"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm text-slate-300">Address</label>
                    <input
                        v-model="form.address"
                        type="text"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Hours</label>
                    <input
                        v-model="form.hours"
                        type="text"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Bill prefix</label>
                    <input
                        v-model="form.bill_prefix"
                        type="text"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>
                <div class="md:col-span-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-50"
                    >
                        Create shop team
                    </button>
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-400">{{ form.errors.name }}</p>
                </div>
            </form>

            <div class="space-y-4">
                <div
                    v-for="team in teams"
                    :key="team.id"
                    class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6"
                >
                    <div v-if="editingId === team.id" class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Name</label>
                            <input v-model="editForm.name" type="text" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Phone</label>
                            <input v-model="editForm.phone" type="text" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm text-slate-300">Address</label>
                            <input v-model="editForm.address" type="text" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Hours</label>
                            <input v-model="editForm.hours" type="text" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm text-slate-300">Bill prefix</label>
                            <input v-model="editForm.bill_prefix" type="text" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div class="flex gap-2 md:col-span-2">
                            <button type="button" class="rounded-xl bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950" @click="saveEdit">Save</button>
                            <button type="button" class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-200" @click="editingId = null">Cancel</button>
                        </div>
                    </div>
                    <div v-else class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-white">{{ team.name }}</h2>
                            <p class="mt-1 text-sm text-slate-400">{{ team.address || 'No address' }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ team.users_count }} staff · {{ team.customers_count }} customers · bills {{ team.bill_prefix }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="rounded-xl border border-slate-700 px-3 py-2 text-sm text-slate-200 hover:border-amber-400/50 hover:text-amber-300"
                                @click="startEdit(team)"
                            >
                                Edit
                            </button>
                            <ConfirmDelete
                                v-if="teams.length > 1"
                                :href="`/teams/${team.id}`"
                                label="Remove shop team"
                                message="This shop team will be removed. Customers must be moved first."
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
