<script setup>
import { computed } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['update:modelValue']);

const items = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const addItem = () => {
    items.value = [...items.value, { description: '', quantity: 1, unit_price: '' }];
};

const removeItem = (index) => {
    if (items.value.length <= 1) return;
    items.value = items.value.filter((_, i) => i !== index);
};

const updateItem = (index, field, value) => {
    const next = [...items.value];
    next[index] = { ...next[index], [field]: value };
    items.value = next;
};

const lineAmount = (item) => {
    const qty = Number(item.quantity) || 0;
    const rate = Number(item.unit_price) || 0;
    return qty * rate;
};

const grandTotal = computed(() =>
    items.value.reduce((sum, item) => sum + lineAmount(item), 0),
);

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value ?? 0);
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-slate-300">Bill Items</label>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-1.5 text-sm text-slate-200 hover:border-amber-400 hover:text-amber-300"
                @click="addItem"
            >
                <AppIcon name="plus" class="h-4 w-4" />
                Add Item
            </button>
        </div>

        <div class="space-y-3">
            <div
                v-for="(item, index) in items"
                :key="index"
                class="grid gap-3 rounded-xl border border-slate-800 bg-slate-950/50 p-4 sm:grid-cols-12"
            >
                <div class="sm:col-span-5">
                    <input
                        :value="item.description"
                        type="text"
                        placeholder="Description (e.g. Engine oil change)"
                        required
                        class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-amber-400 focus:outline-none"
                        @input="updateItem(index, 'description', $event.target.value)"
                    />
                    <p v-if="errors[`items.${index}.description`]" class="mt-1 text-xs text-red-400">
                        {{ errors[`items.${index}.description`] }}
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <input
                        :value="item.quantity"
                        type="number"
                        min="0.01"
                        step="0.01"
                        placeholder="Qty"
                        required
                        class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-amber-400 focus:outline-none"
                        @input="updateItem(index, 'quantity', $event.target.value)"
                    />
                </div>
                <div class="sm:col-span-2">
                    <input
                        :value="item.unit_price"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="Rate ₹"
                        required
                        class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-amber-400 focus:outline-none"
                        @input="updateItem(index, 'unit_price', $event.target.value)"
                    />
                </div>
                <div class="flex items-center justify-between sm:col-span-2">
                    <span class="text-sm font-medium text-emerald-300">{{ formatCurrency(lineAmount(item)) }}</span>
                </div>
                <div class="flex items-center justify-end sm:col-span-1">
                    <button
                        v-if="items.length <= 1"
                        type="button"
                        class="inline-flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg text-slate-600"
                        disabled
                        title="Cannot remove last item"
                    >
                        <AppIcon name="delete" class="h-4 w-4" />
                    </button>
                    <button
                        v-else
                        type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-500/10 hover:text-red-300"
                        title="Remove item"
                        aria-label="Remove item"
                        @click="removeItem(index)"
                    >
                        <AppIcon name="delete" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-end border-t border-slate-800 pt-4">
            <div class="text-right">
                <p class="text-sm text-slate-400">Bill Total</p>
                <p class="text-2xl font-semibold text-amber-300">{{ formatCurrency(grandTotal) }}</p>
            </div>
        </div>

        <p v-if="errors.items" class="text-sm text-red-400">{{ errors.items }}</p>
    </div>
</template>
