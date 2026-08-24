<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const notifications = computed(() => usePage().props.notifications ?? {});

const smsFreeMode = computed(() => notifications.value.smsMode === 'free');
const emailLogMode = computed(() => notifications.value.emailEnabled && !notifications.value.emailLive);
</script>

<template>
    <div
        v-if="smsFreeMode || emailLogMode || notifications.whatsappEnabled"
        class="rounded-xl border border-sky-500/25 bg-sky-500/10 px-4 py-3 text-sm text-sky-100"
    >
        <span class="font-semibold text-sky-200">Free customer messaging</span>
        <ul class="mt-2 list-inside list-disc space-y-1 text-sky-100/90">
            <li v-if="notifications.whatsappEnabled">
                <strong>WhatsApp</strong> — click the green button to send (free, opens WhatsApp with ready message).
            </li>
            <li v-if="notifications.emailEnabled && emailLogMode">
                <strong>Email</strong> — saved to log. Set <code class="rounded bg-slate-800 px-1 text-xs">MAIL_MAILER=smtp</code> in
                <code class="rounded bg-slate-800 px-1 text-xs">.env</code> for real delivery.
            </li>
            <li v-if="notifications.emailEnabled && notifications.emailLive">
                <strong>Email</strong> — sent automatically when customer has an email address.
            </li>
            <li v-if="smsFreeMode">
                <strong>SMS</strong> — logged in app only. Set <code class="rounded bg-slate-800 px-1 text-xs">MSG91_ENABLED=true</code> for paid SMS.
            </li>
        </ul>
    </div>
</template>
