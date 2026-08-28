<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    users: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
});

const submit = () => {
    form.post('/staff', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Staff logins" />

        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-semibold text-white">Staff logins</h1>
                <p class="mt-1 text-slate-400">Super admin can create simple shop staff accounts.</p>
            </div>

            <form class="grid gap-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 md:grid-cols-4" @submit.prevent="submit">
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
                    <label class="mb-1.5 block text-sm text-slate-300">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm text-slate-300">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        minlength="8"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-amber-400 focus:outline-none"
                    />
                </div>
                <div class="flex items-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300 disabled:opacity-50"
                    >
                        Add staff login
                    </button>
                </div>
                <p v-if="form.errors.name || form.errors.email || form.errors.password" class="text-sm text-red-400 md:col-span-4">
                    {{ form.errors.name || form.errors.email || form.errors.password }}
                </p>
            </form>

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-800 bg-slate-950/50 text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Access</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr v-for="account in users" :key="account.id" class="hover:bg-slate-950/40">
                            <td class="px-4 py-3 font-medium text-white">{{ account.name }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ account.email }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2 py-1 text-xs font-semibold"
                                    :class="account.is_platform_admin ? 'bg-amber-400/15 text-amber-300' : 'bg-slate-800 text-slate-300'"
                                >
                                    {{ account.is_platform_admin ? 'Super admin' : 'Staff' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <ConfirmDelete
                                    v-if="!account.is_platform_admin"
                                    :href="`/staff/${account.id}`"
                                    label="Remove staff login"
                                    message="This staff login will be removed. They will no longer be able to sign in."
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
