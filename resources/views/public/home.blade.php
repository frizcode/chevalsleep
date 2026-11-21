@extends('layouts.public')

@section('content')
<div class="space-y-8">

    <!-- 1. Hero Spotlight: Active Champions Meeting (If Active) -->
    @if ($activeEvent)
        <section class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-5 sm:p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-5 border-b border-sail-200 dark:border-navy-700">
                <div class="space-y-2">
                    <!-- Status & Telemetry Protocol -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($activeEvent->cm_number)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-brass-500/15 text-brass-600 dark:text-brass-400 border border-brass-500/30 uppercase">
                                CM #{{ $activeEvent->cm_number }}
                            </span>
                        @endif
                        @if ($activeEvent->is_active)
                            <span class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>{{ __('ui.cm.live_now') }}</span>
                            </span>
                        @endif
                        <span class="text-[10px] font-mono text-slate-400 uppercase">
                            Global Meta Benchmark
                        </span>
                    </div>

                    <!-- Title & Subtitle -->
                    <div>
                        <h1 class="text-2xl sm:text-4xl font-black text-navy-950 dark:text-sail-50 tracking-tight leading-tight font-sans">
                            {{ $activeEvent->title }}
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                            Complete racing telemetry, tier rankings, required spurt skills, and 3-Uma lineup synergy.
                        </p>
                    </div>
                </div>

                <!-- Primary CTA & Bookmark -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    @auth
                        <form method="POST" action="{{ route('cm.bookmark', $activeEvent) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center space-x-1.5 px-3 py-2.5 rounded-md text-xs font-mono font-semibold border transition-colors {{ $activeEvent->isBookmarkedBy(auth()->user()) ? 'bg-brass-500 text-navy-950 border-brass-500' : 'bg-sail-100 dark:bg-navy-750 text-slate-700 dark:text-slate-200 border-sail-200 dark:border-navy-700 hover:border-brass-500' }}"
                                    title="{{ $activeEvent->isBookmarkedBy(auth()->user()) ? __('ui.cm.bookmarked') : __('ui.cm.bookmark_this') }}">
                                <svg class="w-3.5 h-3.5 {{ $activeEvent->isBookmarkedBy(auth()->user()) ? 'fill-current text-navy-950' : 'text-brass-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                                <span>{{ $activeEvent->isBookmarkedBy(auth()->user()) ? __('ui.cm.bookmarked') : __('ui.cm.bookmark_this') }}</span>
                            </button>
                        </form>
                    @endauth

                    <a href="{{ route('cm.show', $activeEvent) }}"
                       class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-md text-xs font-mono font-bold uppercase tracking-wider bg-brass-500 hover:bg-brass-400 text-navy-950 shadow-sm transition-all duration-150">
                        <span>{{ __('ui.cm.view_guide') }} &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Race Telemetry Strip (Integrated Single Horizontal Bar) -->
            <div class="mt-4 bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-y sm:divide-y-0 sm:divide-x divide-sail-200 dark:divide-navy-700 text-center text-xs">
                <div class="p-2.5">
                    <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">{{ __('ui.cm.track') }}</span>
                    <span class="font-bold text-navy-950 dark:text-slate-100 font-mono">{{ $activeEvent->track_name }}</span>
                </div>
                <div class="p-2.5">
                    <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">{{ __('ui.cm.distance') }}</span>
                    <span class="font-bold text-navy-950 dark:text-slate-100 font-mono tabular-nums">{{ $activeEvent->distance_meters }}m <span class="text-slate-400 font-normal">({{ $activeEvent->distance_type }})</span></span>
                </div>
                <div class="p-2.5">
                    <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">{{ __('ui.cm.surface') }}</span>
                    <span class="font-bold font-mono {{ $activeEvent->surface === 'Turf' ? 'text-emerald-600 dark:text-emerald-400' : 'text-brass-600 dark:text-brass-400' }}">{{ $activeEvent->surface }}</span>
                </div>
                <div class="p-2.5">
                    <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">{{ __('ui.cm.season') }}</span>
                    <span class="font-bold text-navy-950 dark:text-slate-100 font-mono">{{ $activeEvent->season ?? 'Winter' }}</span>
                </div>
                <div class="p-2.5">
                    <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">{{ __('ui.cm.weather') }}</span>
                    <span class="font-bold text-navy-950 dark:text-slate-100 font-mono">{{ $activeEvent->weather ?? 'Sunny' }}</span>
                </div>
                <div class="p-2.5">
                    <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">{{ __('ui.cm.condition') }}</span>
                    <span class="font-bold font-mono text-brass-600 dark:text-brass-400">{{ $activeEvent->track_condition ?? 'Firm' }}</span>
                </div>
            </div>

            <!-- Top Contender Strip -->
            @if ($topRacers->isNotEmpty())
                <div class="mt-4 pt-3 border-t border-sail-200 dark:border-navy-700/60 flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 mr-1">Apex Contenders:</span>
                    @foreach ($topRacers as $racer)
                        <div class="inline-flex items-center space-x-2 bg-sail-100/70 dark:bg-navy-750/60 border border-sail-200 dark:border-navy-700 rounded px-2 py-1">
                            <div class="w-5 h-5 rounded overflow-hidden bg-navy-900 border border-slate-700 flex items-center justify-center flex-shrink-0">
                                @if ($racer->character?->avatar_url)
                                    <img src="{{ $racer->character->avatar_url }}" alt="{{ $racer->character->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-[9px] font-mono font-black text-brass-400">{{ strtoupper(substr($racer->character?->name ?? 'UM', 0, 2)) }}</span>
                                @endif
                            </div>
                            <span class="text-xs font-semibold text-navy-950 dark:text-slate-200">{{ $racer->character?->name }}</span>
                            <span class="text-[9px] font-mono font-black px-1 rounded {{ $racer->tier_rank === 'SS' ? 'bg-brass-500/20 text-brass-600 dark:text-brass-400 border border-brass-500/40' : 'bg-slate-200 dark:bg-navy-600 text-slate-700 dark:text-slate-300' }}">
                                {{ $racer->tier_rank }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    <!-- 2. Main Navigation Hub (Uma Musume, Support Cards, Skills, & CM Archives) -->
    <section class="space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-sail-200 dark:border-navy-700">
            <div>
                <h2 class="text-xl font-black text-navy-950 dark:text-sail-50 tracking-tight font-sans uppercase">
                    CHEVAL GRAND META COMMAND HUB
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Direct access to official Global Uma Musume databases, deck guides, skill calculations, and tournament archives.
                </p>
            </div>
            <span class="hidden sm:inline-block text-[10px] font-mono uppercase tracking-widest text-brass-500 font-bold px-2 py-1 rounded bg-brass-500/10 border border-brass-500/20">
                Core Nav Protocol
            </span>
        </div>

        <!-- 4 Core Navigation Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Hub Card 1: Uma Musume Database -->
            <a href="{{ route('guides.umas') }}"
               class="group bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 hover:border-brass-500/60 dark:hover:border-brass-500/60 rounded-lg p-5 flex flex-col justify-between transition-all duration-150 shadow-sm hover:shadow-md">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-md bg-brass-500/15 border border-brass-500/30 flex items-center justify-center text-brass-500 group-hover:bg-brass-500 group-hover:text-navy-950 transition-colors">
                            <!-- Horse / Uma Crown Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-brass-600 dark:text-brass-400 bg-brass-500/10 px-2 py-0.5 rounded border border-brass-500/20">
                            Uma Roster
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-navy-950 dark:text-sail-100 group-hover:text-brass-500 transition-colors font-sans">
                            {{ __('ui.database.umas_title') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            {{ __('ui.database.umas_desc') }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-sail-100 dark:border-navy-750 flex items-center justify-between text-[11px] font-mono text-slate-400">
                        <span>Aptitude &bull; Styles</span>
                        <span class="text-brass-500 font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- Hub Card 2: Support Cards (Supoka) -->
            <a href="{{ route('guides.supoka') }}"
               class="group bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 hover:border-cerulean-500/60 dark:hover:border-cerulean-500/60 rounded-lg p-5 flex flex-col justify-between transition-all duration-150 shadow-sm hover:shadow-md">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-md bg-cerulean-500/15 border border-cerulean-500/30 flex items-center justify-center text-cerulean-500 group-hover:bg-cerulean-500 group-hover:text-white transition-colors">
                            <!-- Cards Stack Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-cerulean-600 dark:text-cerulean-400 bg-cerulean-500/10 px-2 py-0.5 rounded border border-cerulean-500/20">
                            Supoka Decks
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-navy-950 dark:text-sail-100 group-hover:text-cerulean-500 transition-colors font-sans">
                            {{ __('ui.database.supoka_title') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            {{ __('ui.database.supoka_desc') }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-sail-100 dark:border-navy-750 flex items-center justify-between text-[11px] font-mono text-slate-400">
                        <span>SSR/SR &bull; Hint Pool</span>
                        <span class="text-cerulean-500 font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- Hub Card 3: Skills Encyclopedia -->
            <a href="{{ route('guides.skills') }}"
               class="group bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 hover:border-emerald-500/60 dark:hover:border-emerald-500/60 rounded-lg p-5 flex flex-col justify-between transition-all duration-150 shadow-sm hover:shadow-md">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-md bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                            <!-- Lightning / Trigger Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                            Mechanics
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-navy-950 dark:text-sail-100 group-hover:text-emerald-500 transition-colors font-sans">
                            {{ __('ui.database.skills_title') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            {{ __('ui.database.skills_desc') }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-sail-100 dark:border-navy-750 flex items-center justify-between text-[11px] font-mono text-slate-400">
                        <span>Accel &bull; Velo &bull; Heal</span>
                        <span class="text-emerald-500 font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- Hub Card 4: Champions Meeting Archive (Dedicated Page) -->
            <a href="{{ route('cm.archive') }}"
               class="group bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 hover:border-brass-500/60 dark:hover:border-brass-500/60 rounded-lg p-5 flex flex-col justify-between transition-all duration-150 shadow-sm hover:shadow-md">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-md bg-brass-500/15 border border-brass-500/30 flex items-center justify-center text-brass-500 group-hover:bg-brass-500 group-hover:text-navy-950 transition-colors">
                            <!-- Trophy / Tournament Archive Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4m-5-8a5 5 0 0110 0v1a2 2 0 01-2 2H9a2 2 0 01-2-2v-1zm-3-4a3 3 0 013-3h1m8 0h1a3 3 0 013 3v2a3 3 0 01-3 3h-1m-8 0H6a3 3 0 01-3-3v-2z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-brass-600 dark:text-brass-400 bg-brass-500/10 px-2 py-0.5 rounded border border-brass-500/20">
                            Tournament Archive
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-navy-950 dark:text-sail-100 group-hover:text-brass-500 transition-colors font-sans">
                            {{ __('ui.cm.past_editions') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            Comprehensive records of historical Champions Meetings, track telemetry, and archival tier lists.
                        </p>
                    </div>

                    <div class="pt-2 border-t border-sail-100 dark:border-navy-750 flex items-center justify-between text-[11px] font-mono text-slate-400">
                        <span>Past Editions &bull; Tiers</span>
                        <span class="text-brass-500 font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </div>
                </div>
            </a>

        </div>
    </section>

</div>
@endsection
