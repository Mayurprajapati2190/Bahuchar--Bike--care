<script setup>
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Why us" />

    <MarketingLayout>
        <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8 lg:py-20">
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-400">Why riders choose us</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-black tracking-tight text-white sm:text-5xl">Service built around trust.</h1>
            <p class="mt-5 max-w-2xl leading-7 text-slate-400">No shortcuts. No surprises. Just dependable bike care for motorcycles, scooters and electric two-wheelers.</p>

            <div class="mt-12 grid items-start gap-10 lg:grid-cols-2 lg:gap-14">
                <div class="relative">
                    <div class="absolute -inset-5 rounded-[2.5rem] bg-gradient-to-br from-amber-400/15 to-transparent blur-xl" />
                    <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-800 to-[#080b11] p-8 sm:p-10">
                        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full border-[38px] border-amber-400/5" />
                        <p class="text-xs font-bold uppercase tracking-[.25em] text-amber-400">The Bahuchar promise</p>
                        <p class="mt-6 max-w-md text-3xl font-black leading-tight text-white">Careful work, clear communication, confident delivery.</p>
                        <div class="mt-10 grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">01</p><p class="mt-2 text-sm text-slate-300">Inspect carefully</p></div>
                            <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">02</p><p class="mt-2 text-sm text-slate-300">Explain clearly</p></div>
                            <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">03</p><p class="mt-2 text-sm text-slate-300">Repair precisely</p></div>
                            <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">04</p><p class="mt-2 text-sm text-slate-300">Deliver confidently</p></div>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div v-for="item in [
                        ['Skilled hands', 'Careful workmanship for all major two-wheeler brands.'],
                        ['Transparent estimates', 'Understand the work and cost before repairs begin.'],
                        ['Service history', 'Organised digital records make future maintenance easier.'],
                        ['Quality-focused care', 'The right products, parts and process for your ride.'],
                    ]" :key="item[0]" class="flex gap-4 rounded-2xl border border-white/10 bg-[#0b1019] p-5">
                        <span class="mt-1 grid h-7 w-7 shrink-0 place-items-center rounded-full bg-amber-400 text-xs font-black text-slate-950">✓</span>
                        <div>
                            <h2 class="font-bold text-white">{{ item[0] }}</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-400">{{ item[1] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-14 flex flex-wrap gap-3">
                <Link href="/our-services" class="rounded-full border border-white/10 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:border-amber-400/40">See our services</Link>
                <Link href="/contact" class="rounded-full bg-amber-400 px-6 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-amber-300">Visit or call us</Link>
            </div>
        </section>
    </MarketingLayout>
</template>
