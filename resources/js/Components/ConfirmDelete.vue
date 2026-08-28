<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';

const props = defineProps({
    title: { type: String, default: 'Delete record?' },
    message: { type: String, required: true },
    href: { type: String, required: true },
    label: { type: String, default: 'Delete' },
    variant: { type: String, default: 'icon' },
    size: { type: String, default: 'sm' },
});

const canDelete = computed(() => Boolean(usePage().props.auth?.user?.is_super_admin));

const open = ref(false);

const confirm = () => {
    router.delete(props.href, {
        onFinish: () => {
            open.value = false;
        },
    });
};

const buttonClasses = {
    icon: 'inline-flex items-center justify-center rounded-lg text-red-400 transition hover:bg-red-500/10 hover:text-red-300',
    button: 'inline-flex items-center gap-2 rounded-xl border border-red-500/30 px-4 py-2 text-sm text-red-300 transition hover:bg-red-500/10',
};

const sizeClasses = {
    sm: 'h-8 w-8',
    md: 'h-9 w-9',
    lg: 'h-10 w-10',
};
</script>

<template>
    <div v-if="canDelete">
        <button
            type="button"
            :title="label"
            :aria-label="label"
            :class="[buttonClasses[variant] ?? buttonClasses.icon, variant === 'icon' ? sizeClasses[size] : '']"
            @click="open = true"
        >
            <AppIcon name="delete" :class="size === 'lg' ? 'h-5 w-5' : 'h-4 w-4'" />
            <span v-if="variant === 'button'" class="text-sm">{{ label }}</span>
        </button>

        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm"
            @click.self="open = false"
        >
            <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500/10 text-red-400">
                        <AppIcon name="delete" class="h-5 w-5" />
                    </div>
                    <h3 class="text-lg font-semibold text-white">{{ title }}</h3>
                </div>
                <p class="mt-3 text-sm text-slate-400">{{ message }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800"
                        @click="open = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-400"
                        @click="confirm"
                    >
                        <AppIcon name="delete" class="h-4 w-4" />
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
