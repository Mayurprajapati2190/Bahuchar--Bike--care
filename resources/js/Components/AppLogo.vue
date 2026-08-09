<script setup>
defineProps({
    variant: {
        type: String,
        default: 'icon',
        validator: (value) => ['icon', 'full'].includes(value),
    },
    extraClass: {
        type: String,
        default: '',
    },
    iconClass: {
        type: String,
        default: 'h-10 w-10',
    },
});
</script>

<template>
    <img
        v-if="variant === 'full'"
        src="/logo.svg"
        alt="Bahuchar Bike Care"
        :class="['h-10 w-auto', extraClass]"
    />
    <img
        v-else
        src="/logo-icon.svg"
        alt="Bahuchar Bike Care"
        :class="[iconClass, extraClass]"
    />
</template>
