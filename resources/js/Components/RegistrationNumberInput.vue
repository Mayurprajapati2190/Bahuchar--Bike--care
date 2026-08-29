<script setup>
defineOptions({ inheritAttrs: false });

const model = defineModel({ type: String, default: '' });

const onInput = (event) => {
    model.value = event.target.value.toUpperCase();
};
</script>

<template>
    <input
        :value="model"
        type="text"
        autocomplete="off"
        spellcheck="false"
        v-bind="$attrs"
        @input="onInput"
    />
</template>
