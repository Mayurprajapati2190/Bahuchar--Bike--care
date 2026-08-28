<script setup>
import AppLogo from '@/Components/AppLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const shop = computed(() => usePage().props.shop ?? {});
const user = computed(() => usePage().props.auth?.user ?? null);
const currentPath = computed(() => (usePage().url ?? '/').split('?')[0]);
const phoneHref = computed(() => {
    const phone = (shop.value.phone ?? '').replace(/\D/g, '').replace(/^91/, '');
    return phone ? `tel:+91${phone}` : null;
});
const hours = computed(() => (shop.value.hours ?? '').split('|').map((item) => item.trim()).filter(Boolean));

const navItems = [
    { href: '/our-services', label: 'Services' },
    { href: '/why-us', label: 'Why us' },
    { href: '/contact', label: 'Contact' },
];

const navClass = (href) => currentPath.value === href
    ? 'text-amber-400'
    : 'text-slate-300 transition hover:text-amber-400';
</script>

<template>
    <div class="min-h-screen overflow-x-hidden bg-[#070b12] text-slate-100">
        <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_20%_0%,rgba(245,158,11,.12),transparent_35%),radial-gradient(circle_at_90%_30%,rgba(249,115,22,.08),transparent_30%)]" />

        <header class="sticky top-0 z-50 border-b border-white/5 bg-[#070b12]/80 backdrop-blur-xl">
            <div class="mx-auto grid max-w-7xl grid-cols-[1fr_auto] items-center gap-3 px-4 py-3 lg:grid-cols-[auto_1fr_auto] lg:gap-8 lg:px-8">
                <Link href="/" class="flex min-w-0 items-center gap-3">
                    <AppLogo icon-class="h-10 w-10 lg:h-11 lg:w-11" />
                    <div class="min-w-0">
                        <p class="truncate text-xs font-extrabold uppercase tracking-wide text-white sm:text-sm">{{ shop.name ?? 'Bahuchar Bike Care' }}</p>
                        <p class="hidden text-[10px] font-medium uppercase tracking-[0.2em] text-amber-400 sm:block">Ride. Restore. Repeat.</p>
                    </div>
                </Link>
                <nav class="hidden items-center justify-center gap-8 text-sm font-medium lg:flex">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        :class="navClass(item.href)"
                    >
                        {{ item.label }}
                    </Link>
                </nav>
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <Link
                        v-if="user"
                        href="/dashboard"
                        class="inline-flex items-center rounded-full bg-amber-400 px-3 py-2 text-xs font-bold text-slate-950 shadow-lg shadow-amber-500/10 transition hover:bg-amber-300 sm:px-4 sm:py-2.5 sm:text-sm"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            href="/register"
                            class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-bold text-white transition hover:border-amber-400/40 hover:bg-amber-400/10 sm:px-4 sm:py-2.5 sm:text-sm"
                        >
                            Register
                        </Link>
                        <Link
                            href="/login"
                            class="inline-flex items-center rounded-full bg-amber-400 px-3 py-2 text-xs font-bold text-slate-950 shadow-lg shadow-amber-500/10 transition hover:bg-amber-300 sm:px-4 sm:py-2.5 sm:text-sm"
                        >
                            Staff login
                        </Link>
                    </template>
                    <a
                        v-if="phoneHref"
                        :href="phoneHref"
                        class="hidden items-center rounded-full border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-bold text-white transition hover:border-amber-400/40 hover:bg-amber-400/10 xl:inline-flex"
                    >
                        Call {{ shop.phone }}
                    </a>
                </div>
            </div>
            <nav class="flex items-center justify-center gap-6 overflow-x-auto px-4 pb-3 text-xs font-medium sm:text-sm lg:hidden">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    :class="navClass(item.href)"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </header>

        <main class="relative">
            <slot />
        </main>

        <footer class="relative border-t border-white/5 bg-[#05080d]">
            <div class="absolute inset-x-0 top-0 mx-auto h-px max-w-4xl bg-gradient-to-r from-transparent via-amber-400/70 to-transparent" />
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:grid-cols-2 lg:grid-cols-4 lg:px-8">
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
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Explore</p>
                    <div class="mt-4 space-y-3 text-sm">
                        <Link href="/" class="block text-slate-300 transition hover:text-amber-300">Home</Link>
                        <Link href="/our-services" class="block text-slate-300 transition hover:text-amber-300">Services</Link>
                        <Link href="/why-us" class="block text-slate-300 transition hover:text-amber-300">Why us</Link>
                        <Link href="/contact" class="block text-slate-300 transition hover:text-amber-300">Contact</Link>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Contact & location</p>
                    <a v-if="phoneHref" :href="phoneHref" class="mt-4 block text-xl font-bold text-white transition hover:text-amber-300">+91 {{ shop.phone }}</a>
                    <p v-if="shop.address" class="mt-3 max-w-sm text-sm leading-6 text-slate-400">{{ shop.address }}</p>
                    <Link href="/contact" class="mt-4 inline-flex items-center gap-2 text-xs text-slate-500 transition hover:text-amber-300">
                        View contact page <span>→</span>
                    </Link>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-400">Opening hours</p>
                    <div class="mt-4 space-y-2">
                        <p v-for="item in hours" :key="item" class="text-sm font-medium text-slate-300">{{ item }}</p>
                    </div>
                    <div v-if="user" class="mt-5">
                        <Link
                            href="/dashboard"
                            class="inline-flex items-center gap-2 text-xs text-slate-500 transition hover:text-amber-300"
                        >
                            Open dashboard <span>→</span>
                        </Link>
                    </div>
                    <div v-else class="mt-5 flex flex-wrap gap-4">
                        <Link href="/register" class="inline-flex items-center gap-2 text-xs text-slate-500 transition hover:text-amber-300">
                            Register <span>→</span>
                        </Link>
                        <Link href="/login" class="inline-flex items-center gap-2 text-xs text-slate-500 transition hover:text-amber-300">
                            Staff login <span>→</span>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/5 py-5 text-center text-xs text-slate-600">
                © {{ new Date().getFullYear() }} {{ shop.name ?? 'Bahuchar Bike Care' }} · Crafted for better rides.
            </div>
        </footer>
    </div>
</template>
