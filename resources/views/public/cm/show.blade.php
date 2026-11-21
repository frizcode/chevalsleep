@extends('layouts.public')

@section('content')
<div x-data="{
    entries: {{ json_encode($entries) }},
    tierRanks: ['SS', 'S+', 'S', 'A', 'B', 'C'],
    search: '',
    selectedStyle: 'All',
    selectedRole: 'All',
    modalOpen: false,
    selectedEntry: null,
    copiedToast: false,

    resetFilters() {
        this.search = '';
        this.selectedStyle = 'All';
        this.selectedRole = 'All';
    },

    isFiltered() {
        return this.search !== '' || this.selectedStyle !== 'All' || this.selectedRole !== 'All';
    },

    totalFilteredCount() {
        return this.entries.filter(entry => {
            const style = entry.running_style || entry.recommended_style;
            if (this.selectedStyle !== 'All' && style !== this.selectedStyle) return false;
            if (this.selectedRole !== 'All' && entry.role !== this.selectedRole) return false;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                const name = (entry.character?.name || '').toLowerCase();
                const variant = (entry.character?.variant_title || '').toLowerCase();
                const full = (entry.character?.full_name || '').toLowerCase();
                if (!name.includes(q) && !variant.includes(q) && !full.includes(q)) return false;
            }
            return true;
        }).length;
    },

    filteredEntries(rank) {
        return this.entries.filter(entry => {
            if (entry.tier_rank !== rank) return false;
            const style = entry.running_style || entry.recommended_style;
            if (this.selectedStyle !== 'All' && style !== this.selectedStyle) return false;
            if (this.selectedRole !== 'All' && entry.role !== this.selectedRole) return false;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                const name = (entry.character?.name || '').toLowerCase();
                const variant = (entry.character?.variant_title || '').toLowerCase();
                const full = (entry.character?.full_name || '').toLowerCase();
                if (!name.includes(q) && !variant.includes(q) && !full.includes(q)) return false;
            }
            return true;
        });
    },

    openModal(entry) {
        this.selectedEntry = entry;
        this.modalOpen = true;
    },

    closeModal() {
        this.modalOpen = false;
        this.selectedEntry = null;
    },

    copyShareLink() {
        navigator.clipboard.writeText(window.location.href);
        this.copiedToast = true;
        setTimeout(() => { this.copiedToast = false; }, 2500);
    }
}"
@keydown.window.escape="closeModal()"
@keydown.window.slash.prevent="$refs.searchInput.focus()"
class="space-y-8">

    <!-- Toast Feedback for Copy Link -->
    <div x-show="copiedToast" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed bottom-6 right-6 z-50 bg-navy-950 text-brass-400 font-mono text-xs px-4 py-2 rounded-md border border-brass-500/50 shadow-lg flex items-center space-x-2">
        <svg class="w-4 h-4 text-brass-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>Link copied to clipboard</span>
    </div>

    <!-- ============================================================= -->
    <!-- SECTION 1: CM Header & Race Telemetry Strip                  -->
    <!-- ============================================================= -->
    <section class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-5 shadow-sm">
        <!-- Top Toolbar: Breadcrumb + Switcher & Share -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-sail-200 dark:border-navy-700 text-xs font-mono">
            <div class="flex items-center space-x-2 text-slate-500 dark:text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-brass-500 transition-colors flex items-center space-x-1">
                    <span>&larr; ARCHIVE</span>
                </a>
                <span>/</span>
                <span class="text-navy-950 dark:text-slate-200 font-bold uppercase">{{ $event->title }}</span>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Tournament Switcher Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded bg-sail-100 dark:bg-navy-750 text-slate-700 dark:text-slate-200 border border-sail-200 dark:border-navy-700 hover:border-brass-500 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full {{ $event->is_active ? 'bg-emerald-400' : 'bg-slate-400' }}"></span>
                        <span class="truncate max-w-[140px]">Switch Event</span>
                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-1 w-64 bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md shadow-xl py-1 z-50 text-xs font-sans">
                        <!-- Live Section -->
                        <div class="px-3 py-1 font-mono text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-sail-100/60 dark:bg-navy-900/60 border-b border-sail-200 dark:border-navy-700 flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>{{ __('ui.cm.ongoing_cm') }}</span>
                        </div>
                        <div class="divide-y divide-sail-100 dark:divide-navy-700/50">
                            @foreach ($allEvents->where('is_active', true) as $ev)
                                <a href="{{ route('cm.show', $ev) }}"
                                   class="block px-3 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 transition-colors {{ $ev->id === $event->id ? 'bg-sail-100/70 dark:bg-navy-750 text-brass-500 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="truncate">{{ $ev->title }}</span>
                                        <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold uppercase">LIVE</span>
                                    </div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $ev->track_name }} &bull; {{ $ev->distance_meters }}m</div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Past Section -->
                        <div class="px-3 py-1 font-mono text-[9px] font-bold uppercase tracking-wider text-slate-400 bg-sail-100/60 dark:bg-navy-900/60 border-t border-b border-sail-200 dark:border-navy-700">
                            {{ __('ui.cm.past_cm') }}
                        </div>
                        <div class="max-h-52 overflow-y-auto divide-y divide-sail-100 dark:divide-navy-700/50">
                            @foreach ($allEvents->where('is_active', false) as $ev)
                                <a href="{{ route('cm.show', $ev) }}"
                                   class="block px-3 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 transition-colors {{ $ev->id === $event->id ? 'bg-sail-100/70 dark:bg-navy-750 text-brass-500 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="truncate">{{ $ev->title }}</span>
                                        <span class="text-[9px] font-mono text-slate-400">{{ $ev->distance_type }}</span>
                                    </div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $ev->track_name }} &bull; {{ $ev->surface }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @auth
                    <!-- Bookmark Button -->
                    <form method="POST" action="{{ route('cm.bookmark', $event) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-2.5 py-1 rounded {{ $event->isBookmarkedBy(auth()->user()) ? 'bg-brass-500 text-navy-950 font-bold border-brass-500' : 'bg-sail-100 dark:bg-navy-750 text-slate-700 dark:text-slate-200 border-sail-200 dark:border-navy-700 hover:border-brass-500' }} border transition-colors inline-flex items-center space-x-1"
                                title="{{ $event->isBookmarkedBy(auth()->user()) ? __('ui.cm.bookmarked') : __('ui.cm.bookmark_this') }}">
                            <svg class="w-3 h-3 {{ $event->isBookmarkedBy(auth()->user()) ? 'fill-current text-navy-950' : 'text-brass-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>{{ $event->isBookmarkedBy(auth()->user()) ? __('ui.cm.bookmarked') : __('ui.cm.bookmark_this') }}</span>
                        </button>
                    </form>
                @endauth

                <!-- Share Button -->
                <button @click="copyShareLink()" type="button"
                        class="px-2.5 py-1 rounded bg-sail-100 dark:bg-navy-750 text-slate-700 dark:text-slate-200 border border-sail-200 dark:border-navy-700 hover:border-brass-500 transition-colors inline-flex items-center space-x-1"
                        title="Copy CM Meta Link">
                    <svg class="w-3 h-3 text-brass-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    <span>SHARE</span>
                </button>
            </div>
        </div>

        <!-- Title Block -->
        <div class="pt-4">
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                @if ($event->cm_number)
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-brass-500/15 text-brass-600 dark:text-brass-400 border border-brass-500/30 uppercase">
                        CM #{{ $event->cm_number }}
                    </span>
                @endif
                @if ($event->is_active)
                    <span class="inline-flex items-center space-x-1 text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>LIVE PROTOCOL ACTIVE</span>
                    </span>
                @else
                    <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-500 dark:text-slate-400 border border-sail-200 dark:border-navy-700 uppercase">
                        ARCHIVE PROTOCOL
                    </span>
                @endif

                @if ($event->event_date)
                    <span class="text-[11px] font-mono text-slate-400">
                        &bull; {{ $event->event_date->format('F Y') }}
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-4xl font-black text-navy-950 dark:text-sail-50 tracking-tight font-sans">
                {{ $event->title }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Comprehensive Global Server meta analysis, tier rankings, required parent skills, and team composition strategies.
            </p>
        </div>

        <!-- Horizontal Race Telemetry Strip (Single Clean Bar) -->
        <div class="mt-4 bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-y sm:divide-y-0 sm:divide-x divide-sail-200 dark:divide-navy-700 text-center text-xs">
            <div class="p-2.5">
                <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">RACECOURSE</span>
                <span class="font-bold text-navy-950 dark:text-slate-100 font-mono">{{ $event->track_name }}</span>
            </div>
            <div class="p-2.5">
                <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">DISTANCE</span>
                <span class="font-bold text-navy-950 dark:text-slate-100 font-mono tabular-nums">{{ $event->distance_meters }}m <span class="text-slate-400 font-normal">({{ $event->distance_type }})</span></span>
            </div>
            <div class="p-2.5">
                <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">SURFACE</span>
                <span class="font-bold font-mono {{ $event->surface === 'Turf' ? 'text-emerald-600 dark:text-emerald-400' : 'text-brass-600 dark:text-brass-400' }}">{{ $event->surface }}</span>
            </div>
            <div class="p-2.5">
                <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">SEASON</span>
                <span class="font-bold text-navy-950 dark:text-slate-100 font-mono">{{ $event->season ?? 'Winter' }}</span>
            </div>
            <div class="p-2.5">
                <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">WEATHER</span>
                <span class="font-bold text-navy-950 dark:text-slate-100 font-mono">{{ $event->weather ?? 'Sunny' }}</span>
            </div>
            <div class="p-2.5">
                <span class="block text-[9px] font-mono uppercase tracking-wider text-slate-400">CONDITION</span>
                <span class="font-bold font-mono text-brass-600 dark:text-brass-400">{{ $event->track_condition ?? 'Firm' }}</span>
            </div>
        </div>
    </section>

    <!-- ============================================================= -->
    <!-- SECTION 2: Competitive Tier List Board (SS to C)             -->
    <!-- ============================================================= -->
    <section class="space-y-3">
        <!-- Section Title & Counter -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-sail-200 dark:border-navy-700">
            <div>
                <div class="text-[10px] font-mono uppercase tracking-wider text-brass-500 font-bold">SECTION 02 &bull; TIER PROTOCOL</div>
                <h2 class="text-lg font-black text-navy-950 dark:text-sail-50 font-sans uppercase">
                    COMPETITIVE TIER LIST (SS &rarr; C)
                </h2>
            </div>
            <div class="text-xs font-mono text-slate-500 dark:text-slate-400">
                DISPLAYING <span class="font-bold text-navy-950 dark:text-brass-400 tabular-nums" x-text="totalFilteredCount()"></span> / <span class="tabular-nums">{{ count($entries) }}</span> UMAS
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-3 space-y-2.5 text-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-sm">
                    <input type="text" x-ref="searchInput" x-model="search"
                           placeholder="Search racer (press '/' to focus)..."
                           class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded px-3 py-1.5 text-xs text-navy-950 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-brass-500 font-mono">
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[10px] font-mono text-slate-400">
                        /
                    </div>
                </div>

                <!-- Role Filters -->
                <div class="flex items-center space-x-1">
                    <span class="text-[10px] font-mono text-slate-400 uppercase mr-1">ROLE:</span>
                    <template x-for="role in ['All', 'Ace', 'Parent', 'Debuffer']" :key="role">
                        <button type="button" @click="selectedRole = role"
                                class="px-2.5 py-1 rounded text-[11px] font-mono font-semibold transition-colors"
                                :class="selectedRole === role ? 'bg-navy-900 dark:bg-navy-700 text-brass-400 border border-brass-500/40' : 'bg-sail-100 dark:bg-navy-750 text-slate-600 dark:text-slate-300 border border-sail-200 dark:border-navy-700 hover:border-slate-400'">
                            <span x-text="role"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Running Style Tabs -->
            <div class="flex items-center space-x-1 overflow-x-auto pt-2 border-t border-sail-200 dark:border-navy-700/60 text-xs">
                <span class="text-[10px] font-mono text-slate-400 uppercase mr-1 hidden sm:inline">STYLE:</span>
                <template x-for="style in ['All', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer']" :key="style">
                    <button type="button" @click="selectedStyle = style"
                            class="px-2.5 py-1 rounded text-[11px] font-mono transition-colors whitespace-nowrap"
                            :class="selectedStyle === style ? 'bg-brass-500 text-navy-950 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-navy-950 dark:hover:text-white'">
                        <span x-text="style"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Tier List Board Rows -->
        <div class="space-y-2">
            <template x-for="rank in tierRanks" :key="rank">
                <div x-show="filteredEntries(rank).length > 0 || !isFiltered()"
                     class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md overflow-hidden flex flex-col md:flex-row">
                    
                    <!-- Solid Left Rank Column -->
                    <div class="md:w-20 p-2.5 md:p-3 flex flex-row md:flex-col items-center justify-between md:justify-center border-b md:border-b-0 md:border-r border-sail-200 dark:border-navy-700 flex-shrink-0 font-mono"
                         :class="{
                            'bg-brass-500/15 text-brass-600 dark:text-brass-400 border-brass-500/30': rank === 'SS',
                            'bg-rose-900/15 text-rose-600 dark:text-rose-400 border-rose-800/30': rank === 'S+',
                            'bg-cerulean-600/15 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30': rank === 'S',
                            'bg-emerald-900/15 text-emerald-600 dark:text-emerald-400 border-emerald-800/30': rank === 'A',
                            'bg-slate-200/50 dark:bg-navy-750 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700': rank === 'B',
                            'bg-slate-100 dark:bg-navy-900 text-slate-500 dark:text-slate-500': rank === 'C',
                         }">
                        <div class="text-xl md:text-2xl font-black tracking-tight" x-text="rank"></div>
                        <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            <span x-text="filteredEntries(rank).length"></span> UMAS
                        </div>
                    </div>

                    <!-- Compact Uma Tiles Grid -->
                    <div class="flex-1 p-2 sm:p-2.5 min-h-[70px] flex items-center">
                        <template x-if="filteredEntries(rank).length === 0">
                            <div class="text-xs font-mono text-slate-400 italic py-2 pl-2">
                                No contenders in this tier match active filters.
                            </div>
                        </template>

                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 xl:grid-cols-8 gap-2 w-full">
                            <template x-for="entry in filteredEntries(rank)" :key="entry.id">
                                <div @click="openModal(entry)"
                                     class="group cursor-pointer bg-sail-50 dark:bg-navy-900/70 hover:bg-sail-100 dark:hover:bg-navy-750 border border-sail-200 dark:border-navy-700 hover:border-brass-500/50 rounded overflow-hidden transition-all text-center flex flex-col justify-between">
                                    
                                    <!-- Avatar Container -->
                                    <div class="relative w-full aspect-square bg-navy-950 overflow-hidden">
                                        <template x-if="entry.character?.avatar_url">
                                            <img :src="entry.character.avatar_url" :alt="entry.character.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-150">
                                        </template>
                                        <template x-if="!entry.character?.avatar_url">
                                            <div class="w-full h-full flex items-center justify-center font-mono font-bold text-xs text-brass-400">
                                                <span x-text="(entry.character?.name || 'UM').substring(0, 2).toUpperCase()"></span>
                                            </div>
                                        </template>

                                        <!-- Corner Role Tag -->
                                        <div class="absolute top-1 right-1">
                                            <span class="text-[8px] font-mono font-bold uppercase px-1 py-0.2 rounded"
                                                  :class="{
                                                    'bg-rose-600 text-white': entry.role === 'Ace',
                                                    'bg-navy-800 text-brass-300 border border-brass-400/40': entry.role === 'Parent',
                                                    'bg-purple-800 text-purple-200': entry.role === 'Debuffer'
                                                  }"
                                                  x-text="entry.role"></span>
                                        </div>
                                    </div>

                                    <!-- Bottom Plate: Name & Style -->
                                    <div class="p-1.5 bg-white dark:bg-navy-800 border-t border-sail-200 dark:border-navy-700 leading-tight">
                                        <div class="text-[11px] font-bold text-navy-950 dark:text-slate-100 truncate" x-text="entry.character?.name"></div>
                                        <div class="text-[9px] font-mono text-slate-400 truncate mt-0.5" x-text="entry.running_style || entry.recommended_style"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </section>

    <!-- ============================================================= -->
    <!-- SECTION 3: Minimum & Recommended Stats                        -->
    <!-- ============================================================= -->
    @if ($event->stat_recommendations)
        <section class="space-y-3">
            <div class="pb-2 border-b border-sail-200 dark:border-navy-700">
                <div class="text-[10px] font-mono uppercase tracking-wider text-brass-500 font-bold">SECTION 03 &bull; STAT METRICS</div>
                <h2 class="text-lg font-black text-navy-950 dark:text-sail-50 font-sans uppercase">
                    TARGET STAT BENCHMARKS
                </h2>
            </div>

            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md overflow-hidden">
                @php
                    $statList = [
                        'speed' => 'Speed',
                        'stamina' => 'Stamina',
                        'power' => 'Power',
                        'guts' => 'Guts',
                        'wit' => 'Wit',
                    ];
                @endphp

                <!-- 5-Cell Metric Table -->
                <div class="grid grid-cols-2 sm:grid-cols-5 divide-y sm:divide-y-0 sm:divide-x divide-sail-200 dark:divide-navy-700 text-center">
                    @foreach ($statList as $key => $name)
                        @php
                            $minVal = $event->stat_recommendations['minimum'][$key] ?? '—';
                            $recVal = $event->stat_recommendations['recommended'][$key] ?? '—';
                        @endphp
                        <div class="p-3 space-y-1">
                            <span class="text-[10px] font-mono uppercase font-bold text-slate-400 tracking-wider">{{ $name }}</span>
                            <div class="text-sm font-bold font-mono text-navy-950 dark:text-white tabular-nums">
                                {{ $recVal }}
                            </div>
                            <div class="text-[10px] font-mono text-slate-400 tabular-nums">
                                Min: {{ $minVal }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (!empty($event->stat_recommendations['notes']))
                    <div class="p-3 bg-sail-50 dark:bg-navy-900 border-t border-sail-200 dark:border-navy-700 text-xs text-slate-600 dark:text-slate-300 font-mono">
                        <strong class="text-brass-600 dark:text-brass-400 uppercase">Stamina / Guts Note:</strong>
                        <span class="ml-1">{{ $event->stat_recommendations['notes'] }}</span>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- ============================================================= -->
    <!-- SECTION 4: Skill Inheritance (Parent Uniques)                 -->
    <!-- ============================================================= -->
    @if ($event->skill_inheritance)
        <section class="space-y-3">
            <div class="pb-2 border-b border-sail-200 dark:border-navy-700">
                <div class="text-[10px] font-mono uppercase tracking-wider text-brass-500 font-bold">SECTION 04 &bull; INHERITANCE</div>
                <h2 class="text-lg font-black text-navy-950 dark:text-sail-50 font-sans uppercase">
                    PRIORITY INHERITED UNIQUE SKILLS
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach (['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'] as $style)
                    @php $skills = $event->skill_inheritance[$style] ?? []; @endphp
                    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-3.5 space-y-2.5">
                        <div class="flex items-center justify-between pb-2 border-b border-sail-200 dark:border-navy-700">
                            <span class="font-bold text-xs text-navy-950 dark:text-white font-mono">{{ $style }}</span>
                            <span class="text-[10px] font-mono text-slate-400">{{ count($skills) }} skills</span>
                        </div>

                        <div class="space-y-1.5">
                            @forelse ($skills as $item)
                                @php
                                    $skillName = is_array($item) ? ($item['name'] ?? '') : $item;
                                    $skillIcon = is_array($item) ? ($item['icon'] ?? null) : null;
                                @endphp
                                <div class="flex items-center space-x-2 p-1.5 rounded bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700/60">
                                    <div class="w-6 h-6 rounded bg-navy-950 flex items-center justify-center flex-shrink-0 border border-slate-700 overflow-hidden">
                                        @if ($skillIcon)
                                            <img src="{{ Str::startsWith($skillIcon, ['http://', 'https://']) ? $skillIcon : asset('storage/' . ltrim($skillIcon, '/')) }}"
                                                 alt="{{ $skillName }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-3 h-3 text-brass-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-xs font-semibold text-navy-950 dark:text-slate-200 truncate">{{ $skillName }}</span>
                                </div>
                            @empty
                                <div class="text-[11px] font-mono text-slate-400 italic py-1">No specific inheritances.</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ============================================================= -->
    <!-- SECTION 5: Recommended Skills per Running Style              -->
    <!-- ============================================================= -->
    @if ($event->recommended_skills)
        <section class="space-y-3">
            <div class="pb-2 border-b border-sail-200 dark:border-navy-700">
                <div class="text-[10px] font-mono uppercase tracking-wider text-brass-500 font-bold">SECTION 05 &bull; SKILL BLUEPRINT</div>
                <h2 class="text-lg font-black text-navy-950 dark:text-sail-50 font-sans uppercase">
                    RECOMMENDED SKILLS BY RUNNING STYLE
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach (['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'] as $style)
                    @php
                        $styleSkills = $event->recommended_skills[$style] ?? [];
                        $coreAccel = $styleSkills['core_accel'] ?? [];
                        $velocityOther = $styleSkills['velocity_other'] ?? [];
                    @endphp
                    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-3.5 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-sail-200 dark:border-navy-700">
                            <span class="font-bold text-xs font-mono text-navy-950 dark:text-white">{{ $style }}</span>
                            <span class="text-[10px] font-mono text-slate-400 uppercase">Skill Kit</span>
                        </div>

                        <!-- Core Accel -->
                        <div>
                            <span class="block text-[9px] font-mono uppercase font-bold text-rose-500 mb-1.5">Core Acceleration (Mandatory)</span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($coreAccel as $skill)
                                    @php
                                        $sName = is_array($skill) ? ($skill['name'] ?? '') : $skill;
                                        $sIcon = is_array($skill) ? ($skill['icon'] ?? null) : null;
                                    @endphp
                                    <span class="inline-flex items-center space-x-1.5 px-2 py-1 rounded bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-[11px] font-semibold text-rose-700 dark:text-rose-300">
                                        @if ($sIcon)
                                            <img src="{{ Str::startsWith($sIcon, ['http://', 'https://']) ? $sIcon : asset('storage/' . ltrim($sIcon, '/')) }}"
                                                 alt="{{ $sName }}" class="w-3.5 h-3.5 rounded object-cover">
                                        @endif
                                        <span>{{ $sName }}</span>
                                    </span>
                                @empty
                                    <span class="text-[11px] font-mono text-slate-400 italic">None specified.</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Velocity & Support -->
                        <div>
                            <span class="block text-[9px] font-mono uppercase font-bold text-cerulean-500 mb-1.5">Velocity &amp; Positioning</span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($velocityOther as $skill)
                                    @php
                                        $sName = is_array($skill) ? ($skill['name'] ?? '') : $skill;
                                        $sIcon = is_array($skill) ? ($skill['icon'] ?? null) : null;
                                    @endphp
                                    <span class="inline-flex items-center space-x-1.5 px-2 py-1 rounded bg-sail-100 dark:bg-navy-750 border border-sail-200 dark:border-navy-700 text-[11px] font-semibold text-slate-700 dark:text-slate-200">
                                        @if ($sIcon)
                                            <img src="{{ Str::startsWith($sIcon, ['http://', 'https://']) ? $sIcon : asset('storage/' . ltrim($sIcon, '/')) }}"
                                                 alt="{{ $sName }}" class="w-3.5 h-3.5 rounded object-cover">
                                        @endif
                                        <span>{{ $sName }}</span>
                                    </span>
                                @empty
                                    <span class="text-[11px] font-mono text-slate-400 italic">None specified.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ============================================================= -->
    <!-- SECTION 6: Support Card (Supoka) Decks                       -->
    <!-- ============================================================= -->
    @if ($event->supoka_decks)
        <section class="space-y-3">
            <div class="pb-2 border-b border-sail-200 dark:border-navy-700">
                <div class="text-[10px] font-mono uppercase tracking-wider text-brass-500 font-bold">SECTION 06 &bull; DECK ARCHETYPES</div>
                <h2 class="text-lg font-black text-navy-950 dark:text-sail-50 font-sans uppercase">
                    RECOMMENDED SUPPORT CARD DECKS
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                @foreach ($event->supoka_decks as $deck)
                    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-sail-200 dark:border-navy-700">
                            <h3 class="font-bold text-sm text-navy-950 dark:text-sail-100 font-sans">
                                {{ $deck['archetype'] ?? 'Deck Template' }}
                            </h3>
                            <span class="text-[10px] font-mono text-brass-500 font-bold uppercase">Optimal Ratio</span>
                        </div>

                        <!-- 6 Cards Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($deck['cards'] ?? [] as $card)
                                @php
                                    $cardName = is_array($card) ? ($card['name'] ?? '') : $card;
                                    $cardIcon = is_array($card) ? ($card['icon'] ?? null) : null;
                                @endphp
                                <div class="p-1.5 rounded bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700/60 flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded bg-navy-950 flex items-center justify-center flex-shrink-0 border border-slate-700 overflow-hidden">
                                        @if ($cardIcon)
                                            <img src="{{ Str::startsWith($cardIcon, ['http://', 'https://']) ? $cardIcon : asset('storage/' . ltrim($cardIcon, '/')) }}"
                                                 alt="{{ $cardName }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[9px] font-mono font-bold text-brass-400">SSR</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-semibold text-navy-950 dark:text-slate-200 truncate leading-tight" title="{{ $cardName }}">
                                        {{ $cardName }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if (!empty($deck['notes']))
                            <div class="p-2.5 rounded bg-sail-100 dark:bg-navy-750 text-[11px] font-mono text-slate-600 dark:text-slate-300">
                                <span class="text-brass-500 font-bold uppercase mr-1">Notes:</span>
                                {{ $deck['notes'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ============================================================= -->
    <!-- SECTION 7: Recommended Team Compositions                     -->
    <!-- ============================================================= -->
    @if ($event->team_compositions)
        <section class="space-y-3">
            <div class="pb-2 border-b border-sail-200 dark:border-navy-700">
                <div class="text-[10px] font-mono uppercase tracking-wider text-brass-500 font-bold">SECTION 07 &bull; TEAM STRATEGY</div>
                <h2 class="text-lg font-black text-navy-950 dark:text-sail-50 font-sans uppercase">
                    3-UMA TEAM FORMATIONS
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                @foreach ($event->team_compositions as $comp)
                    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-sail-200 dark:border-navy-700">
                            <h3 class="font-bold text-sm text-navy-950 dark:text-sail-100 font-sans">
                                {{ $comp['name'] ?? 'Team Lineup' }}
                            </h3>
                            <span class="text-[10px] font-mono text-emerald-500 uppercase font-bold">3-Racer Synergy</span>
                        </div>

                        <!-- 3 Lineup Slots -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            @foreach ($comp['lineup'] ?? [] as $i => $member)
                                <div class="p-2 rounded bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700/60 text-center">
                                    <div class="text-[9px] font-mono uppercase text-slate-400">SLOT {{ $i + 1 }}</div>
                                    <div class="text-xs font-bold text-navy-950 dark:text-white mt-0.5 truncate">
                                        {{ $member }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if (!empty($comp['strategy']))
                            <div class="p-2.5 rounded bg-sail-100 dark:bg-navy-750 text-[11px] font-mono text-slate-600 dark:text-slate-300">
                                <span class="text-cerulean-500 font-bold uppercase mr-1">Tactical Protocol:</span>
                                {{ $comp['strategy'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ============================================================= -->
    <!-- SECTION 8: Meta Changelog Accordion                          -->
    <!-- ============================================================= -->
    <section x-data="{ open: false }" class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md overflow-hidden">
        <button @click="open = !open" type="button"
                class="w-full p-4 flex items-center justify-between hover:bg-sail-50 dark:hover:bg-navy-750/50 transition-colors text-left font-mono">
            <div>
                <div class="text-[10px] uppercase tracking-wider text-brass-500 font-bold">SECTION 08 &bull; AUDIT LOG</div>
                <h3 class="font-bold text-sm text-navy-950 dark:text-sail-100 font-sans uppercase">
                    META CHANGELOG & TIER MOVEMENTS
                </h3>
            </div>
            <div class="text-xs text-slate-400 flex items-center space-x-2">
                <span>{{ count($changelogs) }} records</span>
                <svg class="w-4 h-4 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </button>

        <div x-show="open" x-cloak class="border-t border-sail-200 dark:border-navy-700 divide-y divide-sail-100 dark:divide-navy-750 text-xs font-mono">
            @forelse ($changelogs as $log)
                <div class="p-3 sm:px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded overflow-hidden bg-navy-950 border border-slate-700 flex items-center justify-center font-bold text-[10px] text-brass-400 flex-shrink-0">
                            @if ($log->character?->avatar_url)
                                <img src="{{ $log->character->avatar_url }}" alt="{{ $log->character->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($log->character?->name ?? 'UM', 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-navy-950 dark:text-white">{{ $log->character?->full_name }}</span>
                                <span class="px-1 py-0.2 rounded text-[9px] bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-300">
                                    {{ $log->previous_tier ?? 'New' }} &rarr; <strong class="text-brass-500">{{ $log->new_tier }}</strong>
                                </span>
                            </div>
                            @if ($log->reason)
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $log->reason }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 tabular-nums">
                        {{ $log->created_at->format('Y-m-d') }}
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-xs text-slate-400 italic">No changelogs recorded for this event.</div>
            @endforelse
        </div>
    </section>

    <!-- ============================================================= -->
    <!-- BUILD NOTES MODAL (High-Density Dossier Modal)               -->
    <!-- ============================================================= -->
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="closeModal()"
             x-show="modalOpen"
             class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg max-w-xl w-full p-5 sm:p-6 space-y-4 shadow-xl relative text-left">
            
            <button @click="closeModal()" type="button"
                    class="absolute top-4 right-4 text-slate-400 hover:text-navy-950 dark:hover:text-white font-mono text-sm">
                [ESC] &times;
            </button>

            <!-- Modal Header -->
            <div class="flex items-center space-x-3.5 pb-3 border-b border-sail-200 dark:border-navy-700">
                <div class="w-14 h-14 rounded overflow-hidden bg-navy-950 border border-sail-200 dark:border-navy-700 flex items-center justify-center flex-shrink-0">
                    <template x-if="selectedEntry?.character?.avatar_url">
                        <img :src="selectedEntry.character.avatar_url" :alt="selectedEntry.character.name" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedEntry?.character?.avatar_url">
                        <span class="font-mono font-black text-brass-400 text-base" x-text="(selectedEntry?.character?.name || 'UM').substring(0, 2).toUpperCase()"></span>
                    </template>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center space-x-1.5 font-mono text-[10px]">
                        <span class="px-1.5 py-0.2 rounded font-bold bg-brass-500/20 text-brass-600 dark:text-brass-400 border border-brass-500/30"
                              x-text="'TIER ' + selectedEntry?.tier_rank"></span>
                        <span class="px-1.5 py-0.2 rounded bg-sail-100 dark:bg-navy-750 text-slate-600 dark:text-slate-300 border border-sail-200 dark:border-navy-700"
                              x-text="selectedEntry?.running_style || selectedEntry?.recommended_style"></span>
                        <span class="px-1.5 py-0.2 rounded bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40"
                              x-text="selectedEntry?.role"></span>
                    </div>

                    <h3 class="font-bold text-lg text-navy-950 dark:text-sail-100 font-sans" x-text="selectedEntry?.character?.full_name"></h3>
                    <div class="text-[11px] font-mono text-slate-400" x-text="selectedEntry?.character?.rarity ? selectedEntry.character.rarity + '★ BASE RARITY' : ''"></div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="space-y-3 text-xs">
                <!-- Target Stats -->
                <div>
                    <span class="block text-[10px] font-mono uppercase font-bold text-slate-400 mb-1">TARGET STAT DISTRIBUTION</span>
                    <div class="p-2.5 rounded bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 font-mono font-bold text-navy-950 dark:text-white tabular-nums text-xs"
                         x-text="selectedEntry?.build_notes?.target_stats || 'Standard track targets'"></div>
                </div>

                <!-- Mandatory Skills -->
                <div>
                    <span class="block text-[10px] font-mono uppercase font-bold text-rose-500 mb-1">MANDATORY ACCEL & SKILLS</span>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="skill in (selectedEntry?.build_notes?.mandatory_skills || [])" :key="skill">
                            <span class="px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50 font-semibold text-[11px]"
                                  x-text="typeof skill === 'object' ? skill.name : skill"></span>
                        </template>
                    </div>
                </div>

                <!-- Inherited Uniques -->
                <div>
                    <span class="block text-[10px] font-mono uppercase font-bold text-brass-500 mb-1">INHERITED UNIQUE SKILLS</span>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="skill in (selectedEntry?.build_notes?.inherited_uniques || [])" :key="skill">
                            <span class="px-2 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-700 dark:text-slate-200 border border-sail-200 dark:border-navy-700 font-semibold text-[11px]"
                                  x-text="typeof skill === 'object' ? skill.name : skill"></span>
                        </template>
                    </div>
                </div>

                <!-- Summary -->
                <div>
                    <span class="block text-[10px] font-mono uppercase font-bold text-cerulean-500 mb-1">TACTICAL ANALYSIS</span>
                    <p class="p-2.5 rounded bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 text-slate-600 dark:text-slate-300 leading-relaxed font-sans text-xs"
                       x-text="selectedEntry?.build_notes?.summary || 'No detailed tactical summary provided.'"></p>
                </div>
            </div>

            <div class="pt-2 text-right border-t border-sail-200 dark:border-navy-700">
                <button @click="closeModal()" type="button"
                        class="px-4 py-1.5 rounded bg-navy-900 dark:bg-navy-700 text-brass-400 hover:text-brass-300 font-mono text-xs font-bold transition-colors">
                    CLOSE [ESC]
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
