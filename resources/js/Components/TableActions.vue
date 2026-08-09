<script setup>
import IconLink from '@/Components/IconLink.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';

defineProps({
    viewHref: { type: String, default: '' },
    deleteHref: { type: String, default: '' },
    viewLabel: { type: String, default: 'View details' },
    deleteLabel: { type: String, default: 'Delete' },
    deleteMessage: { type: String, required: true },
});
</script>

<template>
    <div class="flex items-center justify-end gap-1">
        <IconLink v-if="viewHref" :href="viewHref" :label="viewLabel" />
        <ConfirmDelete
            v-if="deleteHref"
            :href="deleteHref"
            :label="deleteLabel"
            :message="deleteMessage"
        />
        <slot />
    </div>
</template>
