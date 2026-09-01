<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogo from '@/Components/AppLogo.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import TeamSwitcher from '@/Components/TeamSwitcher.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isSuperAdmin = computed(() => Boolean(user.value?.is_super_admin));
const pending = computed(() => page.props.pendingPayments ?? { count: 0, amount: 0 });
const messagingFreeMode = computed(
    () => page.props.notifications?.smsMode === 'free' || !page.props.notifications?.emailLive,
);

const navItems = computed(() => {
    const items = [
        { name: 'Dashboard', href: '/dashboard', icon: '⌂' },
        { name: 'Customers', href: '/customers', icon: '👤' },
        { name: 'Services', href: '/services', icon: '🔧' },
        { name: 'Bills', href: '/bills', icon: '🧾' },
        { name: 'Pending Payments', href: '/bills/pending/list', icon: '⏳', badge: true },
    ];

    if (isSuperAdmin.value) {
        items.push(
            { name: 'Teams', href: '/teams', icon: '🏷' },
            { name: 'Staff', href: '/staff', icon: '🛡' },
            { name: 'Backups', href: '/backups', icon: '💾' },
        );
    }

    return items;
});

const isActive = (href) => {
    if (href === '/bills/pending/list') {
        return page.url.includes('payment=pending') || page.url.includes('/bills/pending');
    }
    if (href === '/bills') {
        return page.url.startsWith('/bills') && !page.url.includes('pending');
    }
    return page.url === href || page.url.startsWith(href + '/');
};

const formatCurrency = (value) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(value ?? 0);
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-amber-900/20 via-slate-950 to-slate-950" />

        <div class="relative flex min-h-screen">
            <aside class="hidden w-72 shrink-0 flex-col border-r border-slate-800/80 bg-slate-900/90 backdrop-blur md:flex md:min-h-screen">
                <div class="shrink-0 border-b border-slate-800 px-6 py-6">
                    <AppLogo />
                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.25em] text-amber-400">Bahuchar Bike Care</p>
                    <p class="mt-1 text-xl font-bold text-white">Garage Manager</p>
                    <div class="mt-4">
                        <TeamSwitcher />
                    </div>
                    <p v-if="messagingFreeMode" class="mt-2 text-xs text-sky-400">Messaging · free mode</p>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <nav class="space-y-1 px-3 py-4">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition"
                            :class="
                                isActive(item.href)
                                    ? 'bg-amber-400/15 text-amber-200 shadow-inner shadow-amber-400/5'
                                    : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                            "
                        >
                            <span class="flex items-center gap-3">
                                <span class="text-base opacity-80">{{ item.icon }}</span>
                                {{ item.name }}
                            </span>
                            <span
                                v-if="item.badge && pending.count > 0"
                                class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white"
                            >
                                {{ pending.count }}
                            </span>
                        </Link>
                    </nav>

                    <div v-if="pending.count > 0" class="mx-3 mb-4 rounded-xl border border-red-500/20 bg-red-500/5 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-red-300">Pending Payments</p>
                        <p class="mt-1 text-lg font-bold text-white">{{ formatCurrency(pending.amount) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ pending.count }} bill(s) awaiting payment</p>
                        <Link href="/bills/pending/list" class="mt-3 inline-block text-xs font-medium text-amber-400 hover:text-amber-300">
                            View all →
                        </Link>
                    </div>
                </div>

                <div class="shrink-0 border-t border-slate-800 px-4 py-4">
                    <p class="truncate text-sm font-medium text-white">{{ user?.name }}</p>
                    <p class="truncate text-xs text-amber-400">{{ isSuperAdmin ? 'Super admin' : 'Staff' }}</p>
                    <p class="truncate text-xs text-slate-400">{{ user?.email }}</p>
                    <button
                        type="button"
                        class="mt-3 w-full rounded-xl border border-slate-700 px-3 py-2 text-sm text-slate-200 transition hover:border-amber-400/50 hover:text-amber-300"
                        @click="router.post('/logout')"
                    >
                        Log out
                    </button>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="border-b border-slate-800/80 bg-slate-900/80 backdrop-blur md:hidden">
                    <div class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center gap-3">
                        <AppLogo icon-class="h-9 w-9" />
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-amber-400">Bahuchar Bike Care</p>
                            <p class="font-bold text-white">Garage Manager</p>
                        </div>
                    </div>
                        <button type="button" class="rounded-lg border border-slate-700 px-3 py-2 text-sm" @click="router.post('/logout')">
                            Log out
                        </button>
                    </div>
                    <div class="px-4 pb-3">
                        <TeamSwitcher />
                    </div>
                    <nav class="flex gap-1 overflow-x-auto px-4 pb-3">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="relative shrink-0 rounded-lg px-3 py-2 text-sm font-medium"
                            :class="isActive(item.href) ? 'bg-amber-400/10 text-amber-300' : 'text-slate-400'"
                        >
                            {{ item.name }}
                            <span
                                v-if="item.badge && pending.count > 0"
                                class="ml-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] text-white"
                            >
                                {{ pending.count }}
                            </span>
                        </Link>
                    </nav>
                </header>

                <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 md:px-8 md:py-8">
                    <FlashMessage />
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
