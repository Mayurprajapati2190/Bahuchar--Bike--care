<script setup>
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    services: Array,
});

const shop = computed(() => usePage().props.shop ?? {});

const phoneHref = computed(() => {
    const phone = shop.value.phone?.replace(/\D/g, '') ?? '';
    return phone ? `tel:+91${phone.replace(/^91/, '')}` : null;
});

const mapHref = computed(() => `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(shop.value.address ?? '')}`);
</script>

<template>
    <Head :title="shop.name ?? 'Home'" />

    <MarketingLayout>
        <section class="relative mx-auto min-h-[calc(100vh-72px)] max-w-7xl px-4 py-16 md:px-8 md:py-24">
            <div class="hero-grid absolute inset-0 opacity-30" />
            <div class="relative grid items-center gap-16 lg:grid-cols-[1.05fr_.95fr]">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/20 bg-amber-400/5 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-amber-300">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-amber-400" />
                        Professional two-wheeler care
                    </div>
                    <h1 class="mt-7 max-w-3xl text-5xl font-black leading-[0.95] tracking-[-0.045em] text-white sm:text-6xl lg:text-7xl">
                        Give your ride the
                        <span class="block bg-gradient-to-r from-amber-300 via-amber-500 to-orange-500 bg-clip-text text-transparent">care it deserves.</span>
                    </h1>
                    <p class="mt-7 max-w-xl text-base leading-7 text-slate-400 sm:text-lg">
                        Reliable service, precise repairs and genuine care for motorcycles, scooters and electric two-wheelers in Gota, Ahmedabad.
                    </p>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a v-if="phoneHref" :href="phoneHref" class="rounded-full bg-amber-400 px-7 py-3.5 text-sm font-extrabold text-slate-950 shadow-xl shadow-amber-500/15 transition hover:-translate-y-1 hover:bg-amber-300">
                            Book a service
                        </a>
                        <a href="#services" class="rounded-full border border-white/10 bg-white/5 px-7 py-3.5 text-sm font-bold text-white transition hover:border-amber-400/40 hover:bg-amber-400/5">
                            Explore services ↓
                        </a>
                    </div>
                    <dl class="mt-12 grid max-w-xl grid-cols-3 border-y border-white/10 py-6">
                        <div><dt class="text-lg font-black text-white">All brands</dt><dd class="mt-1 text-xs text-slate-500">Expert attention</dd></div>
                        <div class="border-x border-white/10 px-5"><dt class="text-lg font-black text-white">Quality</dt><dd class="mt-1 text-xs text-slate-500">Parts & products</dd></div>
                        <div class="pl-5"><dt class="text-lg font-black text-white">Honest</dt><dd class="mt-1 text-xs text-slate-500">Clear estimates</dd></div>
                    </dl>
                </div>

                <div class="relative mx-auto w-full max-w-xl [perspective:1100px]">
                    <div class="bike-glow absolute inset-10 rounded-full bg-amber-500/20 blur-3xl" />
                    <div class="bike-scene relative aspect-square [transform-style:preserve-3d]">
                        <div class="absolute inset-[8%] rounded-[3rem] border border-white/10 bg-gradient-to-br from-white/10 via-slate-900/80 to-black shadow-2xl shadow-black/60 [transform:rotateY(-10deg)_rotateX(5deg)]">
                            <div class="absolute left-6 top-6 text-[10px] font-bold uppercase tracking-[.3em] text-amber-400">Precision service</div>
                            <div class="absolute right-6 top-6 h-2 w-2 rounded-full bg-amber-400 shadow-[0_0_18px_#f59e0b]" />
                        </div>
                        <div class="bike-float absolute inset-[13%] grid place-items-center [transform:translateZ(70px)]">
                            <svg viewBox="0 0 520 330" class="w-full drop-shadow-[0_28px_20px_rgba(0,0,0,.7)]" aria-label="Stylized motorcycle illustration">
                                <defs>
                                    <linearGradient id="bikeGold" x1="0" x2="1"><stop stop-color="#fde68a"/><stop offset=".5" stop-color="#f59e0b"/><stop offset="1" stop-color="#ea580c"/></linearGradient>
                                    <linearGradient id="bikeDark" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#475569"/><stop offset="1" stop-color="#0f172a"/></linearGradient>
                                </defs>
                                <ellipse cx="260" cy="292" rx="205" ry="18" fill="#000" opacity=".45"/>
                                <g class="wheel-left"><circle cx="115" cy="235" r="72" fill="#080b10" stroke="#334155" stroke-width="8"/><circle cx="115" cy="235" r="46" fill="none" stroke="#64748b" stroke-width="3"/><circle cx="115" cy="235" r="10" fill="#f59e0b"/><path d="M115 190v90M70 235h90M83 203l64 64M147 203l-64 64" stroke="#475569" stroke-width="3"/></g>
                                <g class="wheel-right"><circle cx="405" cy="235" r="72" fill="#080b10" stroke="#334155" stroke-width="8"/><circle cx="405" cy="235" r="46" fill="none" stroke="#64748b" stroke-width="3"/><circle cx="405" cy="235" r="10" fill="#f59e0b"/><path d="M405 190v90M360 235h90M373 203l64 64M437 203l-64 64" stroke="#475569" stroke-width="3"/></g>
                                <path d="M115 235l85-10 72-86 85 18 48 78M198 225h112l47-68" fill="none" stroke="url(#bikeGold)" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M187 128c28-25 89-31 135-2l-39 67h-99c-18-19-15-45 3-65Z" fill="url(#bikeGold)"/>
                                <path d="M199 134c29-15 63-17 94-4l-21 23h-78Z" fill="#111827" opacity=".8"/>
                                <path d="M302 126l65-17 27 15-76 25Z" fill="url(#bikeDark)"/>
                                <path d="M355 113l23-39M371 79h38" fill="none" stroke="#cbd5e1" stroke-width="8" stroke-linecap="round"/>
                                <path d="M378 85l27 150" stroke="#f59e0b" stroke-width="8" stroke-linecap="round"/>
                                <path d="M179 127l-43-9-20 19" fill="none" stroke="#cbd5e1" stroke-width="8" stroke-linecap="round"/>
                                <rect x="223" y="184" width="78" height="55" rx="20" fill="url(#bikeDark)" stroke="#64748b" stroke-width="4"/>
                                <circle cx="262" cy="211" r="21" fill="#0f172a" stroke="#f59e0b" stroke-width="4"/>
                                <path d="M285 238l35 20h42" fill="none" stroke="#94a3b8" stroke-width="8" stroke-linecap="round"/>
                                <circle cx="391" cy="116" r="17" fill="#fef3c7" stroke="#f59e0b" stroke-width="5" class="headlight"/>
                            </svg>
                        </div>
                        <div class="absolute bottom-[5%] left-[12%] rounded-2xl border border-white/10 bg-[#0b1019]/90 px-4 py-3 shadow-xl backdrop-blur [transform:translateZ(100px)]">
                            <p class="text-[10px] uppercase tracking-[.2em] text-slate-500">Call for service</p>
                            <p class="mt-1 font-extrabold text-white">+91 {{ shop.phone }}</p>
                        </div>
                        <div class="absolute right-[7%] top-[18%] rounded-full border border-amber-400/20 bg-amber-400/10 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-amber-300 [transform:translateZ(120px)]">Open today</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="relative border-y border-white/5 bg-white/[0.025] py-20">
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <div class="grid gap-8 lg:grid-cols-[.7fr_1.3fr]">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-400">What we do</p>
                        <h2 class="mt-4 text-4xl font-black leading-tight tracking-tight text-white">Everything your bike needs, under one roof.</h2>
                        <p class="mt-5 leading-7 text-slate-400">From everyday maintenance to detailed repairs, every job receives focused attention.</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <article v-for="(service, index) in services" :key="service.title" class="service-card group relative overflow-hidden rounded-2xl border border-white/8 bg-[#0b1019] p-6 transition duration-300 hover:-translate-y-1 hover:border-amber-400/30">
                            <span class="absolute right-5 top-3 text-5xl font-black text-white/[0.03]">0{{ index + 1 }}</span>
                            <span class="grid h-11 w-11 place-items-center rounded-xl border border-amber-400/15 bg-amber-400/8 text-xl transition group-hover:rotate-6 group-hover:scale-110">{{ service.icon }}</span>
                            <h3 class="mt-5 text-lg font-bold text-white">{{ service.title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-400">{{ service.description }}</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="why-us" class="py-24">
            <div class="mx-auto max-w-7xl px-4 md:px-8">
                <div class="grid items-center gap-14 lg:grid-cols-2">
                    <div class="relative">
                        <div class="absolute -inset-5 rounded-[2.5rem] bg-gradient-to-br from-amber-400/15 to-transparent blur-xl" />
                        <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-800 to-[#080b11] p-8 sm:p-10">
                            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full border-[38px] border-amber-400/5" />
                            <p class="text-xs font-bold uppercase tracking-[.25em] text-amber-400">The Bahuchar promise</p>
                            <p class="mt-6 max-w-md text-3xl font-black leading-tight text-white">No shortcuts. No surprises. Just dependable bike care.</p>
                            <div class="mt-10 grid grid-cols-2 gap-4">
                                <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">01</p><p class="mt-2 text-sm text-slate-300">Inspect carefully</p></div>
                                <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">02</p><p class="mt-2 text-sm text-slate-300">Explain clearly</p></div>
                                <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">03</p><p class="mt-2 text-sm text-slate-300">Repair precisely</p></div>
                                <div class="rounded-2xl bg-white/5 p-4"><p class="text-2xl font-black text-amber-400">04</p><p class="mt-2 text-sm text-slate-300">Deliver confidently</p></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-amber-400">Why riders choose us</p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight text-white">Service built around trust.</h2>
                        <div class="mt-8 space-y-6">
                            <div v-for="item in [
                                ['Skilled hands', 'Careful workmanship for all major two-wheeler brands.'],
                                ['Transparent estimates', 'Understand the work and cost before repairs begin.'],
                                ['Service history', 'Organised digital records make future maintenance easier.'],
                                ['Quality-focused care', 'The right products, parts and process for your ride.'],
                            ]" :key="item[0]" class="flex gap-4">
                                <span class="mt-1 grid h-7 w-7 shrink-0 place-items-center rounded-full bg-amber-400 text-xs font-black text-slate-950">✓</span>
                                <div><h3 class="font-bold text-white">{{ item[0] }}</h3><p class="mt-1 text-sm leading-6 text-slate-400">{{ item[1] }}</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-4 pb-24 md:px-8">
            <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-amber-400 px-6 py-12 text-slate-950 sm:px-12">
                <div class="absolute -right-12 -top-32 h-80 w-80 rounded-full border-[55px] border-slate-950/5" />
                <div class="relative grid items-center gap-8 lg:grid-cols-[1fr_auto]">
                    <div><p class="text-xs font-black uppercase tracking-[.25em]">Visit Bahuchar Auto Care</p><h2 class="mt-3 max-w-2xl text-3xl font-black tracking-tight sm:text-4xl">Ready for a smoother, safer ride?</h2><p class="mt-3 max-w-xl text-sm font-medium text-slate-800">Walk in during business hours or call us to plan your service.</p></div>
                    <div class="flex flex-wrap gap-3">
                        <a :href="phoneHref" class="rounded-full bg-slate-950 px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-1">Call {{ shop.phone }}</a>
                        <a :href="mapHref" target="_blank" rel="noopener" class="rounded-full border border-slate-950/20 px-6 py-3 text-sm font-bold transition hover:bg-slate-950/10">Get directions ↗</a>
                    </div>
                </div>
            </div>
        </section>
    </MarketingLayout>
</template>

<style scoped>
.hero-grid {
    background-image: linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
    background-size: 54px 54px;
    mask-image: linear-gradient(to bottom, black, transparent 85%);
}
.bike-scene { animation: scene-tilt 8s ease-in-out infinite; }
.bike-float { animation: bike-float 4s ease-in-out infinite; }
.bike-glow { animation: glow-pulse 4s ease-in-out infinite; }
.wheel-left, .wheel-right { transform-origin: center; transform-box: fill-box; animation: wheel-turn 12s linear infinite; }
.headlight { animation: light-pulse 2.2s ease-in-out infinite; }
.service-card::after { content: ''; position: absolute; inset: auto 0 0; height: 2px; background: linear-gradient(90deg, transparent, #f59e0b, transparent); transform: scaleX(0); transition: transform .3s ease; }
.service-card:hover::after { transform: scaleX(1); }
@keyframes scene-tilt { 0%,100% { transform: rotateY(-2deg) rotateX(1deg); } 50% { transform: rotateY(3deg) rotateX(-1deg); } }
@keyframes bike-float { 0%,100% { translate: 0 0; } 50% { translate: 0 -12px; } }
@keyframes glow-pulse { 0%,100% { opacity: .55; scale: .9; } 50% { opacity: .9; scale: 1.08; } }
@keyframes wheel-turn { to { transform: rotate(360deg); } }
@keyframes light-pulse { 0%,100% { filter: drop-shadow(0 0 4px #f59e0b); } 50% { filter: drop-shadow(0 0 16px #fde68a); } }
@media (prefers-reduced-motion: reduce) {
    .bike-scene, .bike-float, .bike-glow, .wheel-left, .wheel-right, .headlight { animation: none; }
}
</style>
