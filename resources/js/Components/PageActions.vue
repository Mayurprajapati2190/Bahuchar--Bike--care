<script setup>
import IconLink from '@/Components/IconLink.vue';
import IconButton from '@/Components/IconButton.vue';
import ConfirmDelete from '@/Components/ConfirmDelete.vue';

defineProps({
    editHref: { type: String, default: '' },
    editLabel: { type: String, default: 'Edit' },
    viewHref: { type: String, default: '' },
    viewLabel: { type: String, default: 'View' },
    printHref: { type: String, default: '' },
    printLabel: { type: String, default: 'Print' },
    deleteHref: { type: String, default: '' },
    deleteLabel: { type: String, default: 'Delete' },
    deleteMessage: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-wrap items-center gap-1">
        <IconLink v-if="editHref" :href="editHref" icon="edit" :label="editLabel" />
        <IconLink v-if="viewHref" :href="viewHref" icon="view" :label="viewLabel" />
        <IconButton
            v-if="printHref"
            type="link"
            :href="printHref"
            target="_blank"
            icon="print"
            :label="printLabel"
            color="amber"
        />
        <ConfirmDelete
            v-if="deleteHref && deleteMessage"
            :href="deleteHref"
            :label="deleteLabel"
            :message="deleteMessage"
            size="md"
        />
        <slot />
    </div>
</template>
