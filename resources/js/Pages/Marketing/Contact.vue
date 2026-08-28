<script setup>
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const shop = computed(() => usePage().props.shop ?? {});
const user = computed(() => usePage().props.auth?.user ?? null);
const hours = computed(() => (shop.value.hours ?? '').split('|').map((item) => item.trim()).filter(Boolean));
const phoneHref = computed(() => {
    const phone = shop.value.phone?.replace(/\D/g, '') ?? '';
    return phone ? `tel:+91${phone.replace(/^91/, '')}` : null;
});
const mapHref = computed(() => `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(shop.value.address ?? '')}`);
const mapEmbed = computed(() => `https://maps.google.com/maps?q=${encodeURIComponent(shop.value.address ?? '')}&z=16&output=embed`);
</script>

<template>
    <Head title="Contact" />

    <MarketingLayout>
        <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-20">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-400">Visit us</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-black tracking-tight text-white sm:text-5xl">Contact Bahuchar Bike Care.</h1>
            <p class="mt-5 max-w-2xl leading-7 text-slate-400">Walk in during business hours, call to plan a service, or find us near Vishwas City 3 in Gota, Ahmedabad.</p>

            <div class="mt-12 grid gap-6 lg:grid-cols-2">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-white/10 bg-[#0b1019] p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Phone</p>
                        <a v-if="phoneHref" :href="phoneHref" class="mt-3 block text-2xl font-black text-white transition hover:text-amber-300">+91 {{ shop.phone }}</a>
                        <p class="mt-2 text-sm text-slate-400">Call for estimates, booking, or service status.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-[#0b1019] p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Workshop</p>
                        <p class="mt-3 max-w-md text-sm leading-6 text-slate-300">{{ shop.address }}</p>
                        <a :href="mapHref" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-amber-400 transition hover:text-amber-300">
                            Get directions ↗
                        </a>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-[#0b1019] p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Opening hours</p>
                        <div class="mt-4 space-y-2">
                            <p v-for="item in hours" :key="item" class="text-sm font-medium text-slate-300">{{ item }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <Link
                            v-if="user"
                            href="/dashboard"
                            class="rounded-full bg-amber-400 px-6 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-amber-300"
                        >
                            Open dashboard
                        </Link>
                        <template v-else>
                            <Link href="/register" class="rounded-full border border-white/10 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:border-amber-400/40">Register</Link>
                            <Link href="/login" class="rounded-full bg-amber-400 px-6 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-amber-300">Staff login</Link>
                        </template>
                    </div>
                </div>
                <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-[#0b1019]">
                    <iframe
                        v-if="shop.address"
                        title="Bahuchar Bike Care location"
                        :src="mapEmbed"
                        class="h-[28rem] w-full border-0 lg:h-full"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    />
                </div>
            </div>
        </section>
    </MarketingLayout>
</template>
