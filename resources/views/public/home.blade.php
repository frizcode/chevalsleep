@extends('layouts.public')

@section('content')
<div class="space-y-12">

    <!-- 1. Hero Spotlight: Latest / Active CM -->
    @if ($activeEvent)
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-white via-slate-50 to-sky-50 dark:from-[#0E172B] dark:via-[#11192E] dark:to-[#090E1A] border border-sky-200/80 dark:border-sky-900/50 p-6 sm:p-10 shadow-2xl transition-all duration-300">
            <!-- Ambient Decorative Elements -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-24 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="space-y-4 max-w-2xl">
                    <!-- Event Edition & Status Pill -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                            <span class="w-2 h-2 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.8)]"></span>
                            <span>Featured Tournament Spotlight</span>
                        </span>
                        @if ($activeEvent->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                Active Meta Track
                            </span>
                        @endif
                    </div>

                    <!-- CM Title & Headline -->
                    <div>
                        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                            {{ $activeEvent->title }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 font-medium">
                            Comprehensive Global Meta Guide &bull; Track Analysis, Stat Targets, Skills & Supoka Decks
                        </p>
                    </div>

                    <!-- Structured Track Condition Pills -->
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 shadow-sm">
                            📍 {{ $activeEvent->track_name }}
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 shadow-sm">
                            🏃 {{ $activeEvent->distance_type }} ({{ $activeEvent->distance_meters }}m)
                        </span>
                        <span class="px-3 py-1.5 rounded-xl text-xs font-extrabold border shadow-sm {{ $activeEvent->surface === 'Turf' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/30' }}">
                            🌱 {{ $activeEvent->surface }}
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300 shadow-sm">
                            ❄️ {{ $activeEvent->season }} &bull; ☀️ {{ $activeEvent->weather }} &bull; {{ $activeEvent->track_condition }}
                        </span>
                    </div>

                    <!-- Top Uma Preview Badges -->
                    @if ($topRacers->isNotEmpty())
                        <div class="pt-2">
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Top Contender Previews:</span>
                            <div class="flex flex-wrap items-center gap-3">
                                @foreach ($topRacers as $racer)
                                    <div class="flex items-center space-x-2 bg-white/80 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 rounded-2xl p-1.5 pr-3 shadow-sm">
                                        @if ($racer->character?->avatar_url)
                                            <img src="{{ $racer->character->avatar_url }}" alt="{{ $racer->character->name }}"
                                                 class="w-7 h-7 rounded-xl object-cover bg-slate-950 border border-amber-500/40">
                                        @else
                                            <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-slate-700 to-slate-800 flex items-center justify-center font-bold text-[10px] text-amber-400">
                                                {{ strtoupper(substr($racer->character?->name ?? 'UM', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="text-xs font-bold text-slate-900 dark:text-white">
                                            {{ $racer->character?->name }}
                                        </div>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-black {{ $racer->tier_rank === 'SS' ? 'bg-gradient-to-r from-amber-400 to-rose-400 text-slate-950' : 'bg-rose-500 text-white' }}">
                                            {{ $racer->tier_rank }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Primary Action CTA Button -->
                <div class="flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center gap-3 flex-shrink-0">
                    <a href="{{ route('cm.show', $activeEvent) }}"
                       class="inline-flex items-center justify-center space-x-2 px-6 py-4 rounded-2xl text-sm font-black bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 shadow-xl shadow-amber-500/20 hover:scale-[1.02] transition-all duration-200 text-center">
                        <span>Open Full CM Meta Guide</span>
                        <svg class="w-4 h-4 text-slate-950 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>

                    <span class="text-[11px] text-slate-500 dark:text-slate-400 text-center font-medium">
                        Updated with verified Global builds
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. CM Archive Grid (Ordered Newest to Oldest) -->
    <div x-data="{
        events: {{ json_encode($events) }},
        filterDistance: 'All',
        filterSurface: 'All',

        filteredEvents() {
            return this.events.filter(event => {
                if (this.filterDistance !== 'All' && event.distance_type !== this.filterDistance) return false;
                if (this.filterSurface !== 'All' && event.surface !== this.filterSurface) return false;
                return true;
            });
        }
    }" class="space-y-6">
        
        <!-- Archive Header & Filters -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-sky-100 dark:border-sky-900/40 pb-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center space-x-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                    <span>Champions Meeting Tournaments Archive</span>
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Explore tier lists, builds, and strategies across all historical CM editions.</p>
            </div>

            <!-- Client-side Alpine Filter Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Distance Filter -->
                <div class="flex items-center space-x-1 bg-white dark:bg-[#11192E] p-1 rounded-2xl border border-sky-100 dark:border-sky-900/40 text-xs shadow-sm">
                    @foreach (['All', 'Sprint', 'Mile', 'Medium', 'Long'] as $dist)
                        <button type="button" @click="filterDistance = '{{ $dist }}'"
                                :class="filterDistance === '{{ $dist }}' ? 'bg-sky-600 text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                                class="px-2.5 py-1 rounded-xl transition-colors">
                            {{ $dist }}
                        </button>
                    @endforeach
                </div>

                <!-- Surface Filter -->
                <div class="flex items-center space-x-1 bg-white dark:bg-[#11192E] p-1 rounded-2xl border border-sky-100 dark:border-sky-900/40 text-xs shadow-sm">
                    @foreach (['All', 'Turf', 'Dirt'] as $surf)
                        <button type="button" @click="filterSurface = '{{ $surf }}'"
                                :class="filterSurface === '{{ $surf }}' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                                class="px-2.5 py-1 rounded-xl transition-colors">
                            {{ $surf }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="event in filteredEvents()" :key="event.id">
                <a :href="'/cm/' + event.slug"
                   class="group bg-white dark:bg-[#11192E] hover:bg-sky-50/50 dark:hover:bg-[#16223D] border border-sky-100 dark:border-sky-900/40 hover:border-amber-500/40 rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-200 flex flex-col justify-between space-y-4 hover:-translate-y-0.5">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black font-mono uppercase px-2.5 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60"
                                  x-text="event.cm_number ? 'CM #' + event.cm_number : 'Tournament'"></span>

                            <template x-if="event.is_active">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span>
                                    Active CM
                                </span>
                            </template>
                            <template x-if="!event.is_active">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700">
                                    Archive
                                </span>
                            </template>
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors"
                                x-text="event.title"></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium" x-text="event.track_name"></p>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800"
                                  x-text="event.distance_type + ' (' + event.distance_meters + 'm)'"></span>
                            <span class="px-2 py-0.5 rounded border"
                                  :class="event.surface === 'Turf' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/30'"
                                  x-text="event.surface"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-sky-50 dark:border-sky-900/30 flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-mono" x-text="(event.tier_entries_count || 0) + ' ranked Umas'"></span>
                        <span class="font-bold text-sky-600 dark:text-amber-400 group-hover:translate-x-1 transition-transform inline-flex items-center space-x-1">
                            <span>View Guide</span>
                            <span>&rarr;</span>
                        </span>
                    </div>
                </a>
            </template>
        </div>
    </div>

    <!-- 3. Future Encyclopedia Teaser Grid -->
    <div class="space-y-6 pt-4">
        <div class="border-b border-sky-100 dark:border-sky-900/40 pb-3">
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center space-x-2">
                <span class="text-amber-500">✦</span>
                <span>Uma Musume Knowledge Base & Encyclopedia</span>
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Complementary guides to master training loops, support decks, and race math.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Card 1: Uma Database -->
            <a href="{{ route('guides.umas') }}"
               class="group bg-white dark:bg-[#11192E] border border-sky-100 dark:border-sky-900/40 hover:border-sky-300 dark:hover:border-sky-700 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-xl group-hover:scale-105 transition-transform">
                    🏇
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">
                        Uma Musume Database
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        Growth rate distributions, hidden race bonuses, and base aptitude charts for every racer.
                    </p>
                </div>
                <span class="inline-block text-[11px] font-bold text-sky-600 dark:text-sky-400">Browse Encyclopedia &rarr;</span>
            </a>

            <!-- Card 2: Support Cards -->
            <a href="{{ route('guides.supoka') }}"
               class="group bg-white dark:bg-[#11192E] border border-sky-100 dark:border-sky-900/40 hover:border-amber-300 dark:hover:border-amber-700 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xl group-hover:scale-105 transition-transform">
                    🎴
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        Support Card Compendium
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        SSR / SR card evaluation, training event choice trees, friendship bonuses, and hint skill lists.
                    </p>
                </div>
                <span class="inline-block text-[11px] font-bold text-amber-600 dark:text-amber-400">Inspect Cards &rarr;</span>
            </a>

            <!-- Card 3: Skills & Mechanics -->
            <a href="{{ route('guides.skills') }}"
               class="group bg-white dark:bg-[#11192E] border border-sky-100 dark:border-sky-900/40 hover:border-indigo-300 dark:hover:border-indigo-700 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xl group-hover:scale-105 transition-transform">
                    ⚡
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        Skills & Race Mechanics
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        Acceleration vs target speed, final stretch phase timing, stamina calculation, and guts thresholds.
                    </p>
                </div>
                <span class="inline-block text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Learn Mechanics &rarr;</span>
            </a>
        </div>
    </div>

</div>
@endsection
