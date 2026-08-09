<script setup>
import AppLogo from '@/Components/AppLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const shop = computed(() => usePage().props.shop ?? {});
const phoneHref = computed(() => `tel:+91${(shop.value.phone ?? '').replace(/\D/g, '').replace(/^91/, '')}`);
const hours = computed(() => (shop.value.hours ?? '').split('|').map((item) => item.trim()).filter(Boolean));
</script>

<template>
    <div class="min-h-screen overflow-hidden bg-[#070b12] text-slate-100">
        <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_20%_0%,rgba(245,158,11,.12),transparent_35%),radial-gradient(circle_at_90%_30%,rgba(249,115,22,.08),transparent_30%)]" />

        <header class="sticky top-0 z-50 border-b border-white/5 bg-[#070b12]/80 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 md:px-8">
                <Link href="/" class="flex items-center gap-3">
                    <AppLogo icon-class="h-11 w-11" />
                    <div>
                        <p class="text-sm font-extrabold uppercase tracking-wide text-white">{{ shop.name ?? 'Bahuchar Bike Care' }}</p>
                        <p class="text-[10px] font-medium uppercase tracking-[0.2em] text-amber-400">Ride. Restore. Repeat.</p>
                    </div>
                </Link>
                <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
                    <a href="#services" class="text-slate-300 transition hover:text-amber-400">Services</a>
                    <a href="#why-us" class="text-slate-300 transition hover:text-amber-400">Why us</a>
                    <a href="#contact" class="text-slate-300 transition hover:text-amber-400">Contact</a>
                </nav>
                <a
                    :href="phoneHref"
                    class="group inline-flex items-center gap-2 rounded-full bg-amber-400 px-4 py-2.5 text-sm font-bold text-slate-950 shadow-lg shadow-amber-500/10 transition hover:-translate-y-0.5 hover:bg-amber-300"
                >
                    <span class="grid h-5 w-5 place-items-center rounded-full bg-slate-950 text-[10px] text-amber-300">●</span>
                    <span class="hidden sm:inline">Call {{ shop.phone }}</span>
                    <span class="sm:hidden">Call now</span>
                </a>
            </div>
        </header>

        <main class="relative">
            <slot />
        </main>

        <footer id="contact" class="relative border-t border-white/5 bg-[#05080d]">
            <div class="absolute inset-x-0 top-0 mx-auto h-px max-w-4xl bg-gradient-to-r from-transparent via-amber-400/70 to-transparent" />
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 md:grid-cols-[1.1fr_1fr_1fr] md:px-8">
                <div>
                    <div class="flex items-center gap-3">
                        <AppLogo icon-class="h-12 w-12" />
                        <div>
                            <p class="font-extrabold uppercase tracking-wide text-white">{{ shop.name ?? 'Bahuchar Bike Care' }}</p>
                            <p class="text-xs text-amber-400">Two-wheeler service experts</p>
                        </div>
                    </div>
                    <p class="mt-5 max-w-xs text-sm leading-6 text-slate-400">{{ shop.tagline }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Contact & location</p>
                    <a :href="phoneHref" class="mt-4 block text-xl font-bold text-white transition hover:text-amber-300">+91 {{ shop.phone }}</a>
                    <p v-if="shop.address" class="mt-3 max-w-sm text-sm leading-6 text-slate-400">{{ shop.address }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Opening hours</p>
                    <div class="mt-4 space-y-2">
                        <p v-for="item in hours" :key="item" class="text-sm font-medium text-slate-300">{{ item }}</p>
                    </div>
                    <Link href="/login" class="mt-5 inline-flex items-center gap-2 text-xs text-slate-500 transition hover:text-amber-300">
                        Staff login <span>→</span>
                    </Link>
                </div>
            </div>
            <div class="border-t border-white/5 py-5 text-center text-xs text-slate-600">
                © {{ new Date().getFullYear() }} {{ shop.name ?? 'Bahuchar Bike Care' }} · Crafted for better rides.
            </div>
        </footer>
    </div>
</template>
