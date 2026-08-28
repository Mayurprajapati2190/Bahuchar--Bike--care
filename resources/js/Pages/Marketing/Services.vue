<script setup>
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    services: Array,
});

const shop = computed(() => usePage().props.shop ?? {});
const phoneHref = computed(() => {
    const phone = shop.value.phone?.replace(/\D/g, '') ?? '';
    return phone ? `tel:+91${phone.replace(/^91/, '')}` : null;
});
</script>

<template>
    <Head title="Our services" />

    <MarketingLayout>
        <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-20">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-400">What we do</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl">Everything your bike needs, under one roof.</h1>
            <p class="mt-5 max-w-2xl leading-7 text-slate-400">From everyday maintenance to detailed repairs, every job receives focused attention at Bahuchar Bike Care in Gota, Ahmedabad.</p>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="(service, index) in services" :key="service.title" class="service-card group relative overflow-hidden rounded-2xl border border-white/10 bg-[#0b1019] p-6 transition duration-300 hover:-translate-y-1 hover:border-amber-400/30">
                    <span class="absolute right-5 top-3 text-5xl font-black text-white/[0.03]">0{{ index + 1 }}</span>
                    <span class="grid h-11 w-11 place-items-center rounded-xl border border-amber-400/15 bg-amber-400/10 text-xl transition group-hover:rotate-6 group-hover:scale-110">{{ service.icon }}</span>
                    <h2 class="mt-5 text-lg font-bold text-white">{{ service.title }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-400">{{ service.description }}</p>
                </article>
            </div>

            <div class="mt-14 flex flex-wrap gap-3">
                <Link href="/contact" class="rounded-full bg-amber-400 px-6 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-amber-300">Plan a visit</Link>
                <a v-if="phoneHref" :href="phoneHref" class="rounded-full border border-white/10 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:border-amber-400/40">Call {{ shop.phone }}</a>
            </div>
        </section>
    </MarketingLayout>
</template>

<style scoped>
.service-card::after { content: ''; position: absolute; inset: auto 0 0; height: 2px; background: linear-gradient(90deg, transparent, #f59e0b, transparent); transform: scaleX(0); transition: transform .3s ease; }
.service-card:hover::after { transform: scaleX(1); }
</style>
