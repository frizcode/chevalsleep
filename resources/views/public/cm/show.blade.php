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
    },

    resolveAssetUrl(icon) {
        if (!icon) return null;
        if (icon.startsWith('http://') || icon.startsWith('https://')) return icon;
        return '/storage/' + icon.replace(/^\/+/, '');
    }
}"
@keydown.window.escape="closeModal()"
@keydown.window.slash.prevent="$refs.searchInput.focus()"
class="space-y-12">

    <!-- Toast Feedback for Copy Link -->
    <div x-show="copiedToast" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed bottom-6 right-6 z-50 bg-amber-500 text-slate-950 font-bold px-4 py-2.5 rounded-2xl shadow-xl flex items-center space-x-2 text-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>Tournament meta link copied to clipboard!</span>
    </div>

    <!-- ========================================== -->
    <!-- SECTION 1: CM Header & Track Conditions   -->
    <!-- ========================================== -->
    <section class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 p-6 sm:p-9 shadow-xl transition-colors duration-200">
        <!-- Glows -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-48 -bottom-24 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-6">
            <!-- Top bar: Back breadcrumb + Switcher & Actions -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-sky-100 dark:border-sky-900/40 pb-4">
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <a href="{{ route('home') }}" class="hover:text-sky-600 dark:hover:text-amber-400 transition-colors flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>CM Archive</span>
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $event->title }}</span>
                </div>

                <!-- Event Switcher Dropdown & Share Button -->
                <div class="flex items-center space-x-3">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                                class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:border-sky-500 transition-all">
                            <span class="w-2 h-2 rounded-full {{ $event->is_active ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]' : 'bg-slate-400' }}"></span>
                            <span class="max-w-[140px] truncate">Switch Tournament</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-72 bg-white dark:bg-[#11192E] border border-sky-100 dark:border-sky-900/60 rounded-2xl shadow-2xl py-2 z-50 overflow-hidden text-xs">
                            <div class="px-4 py-1.5 font-bold uppercase tracking-wider text-slate-400 text-[10px] border-b border-sky-100 dark:border-sky-900/40">
                                All Tournaments
                            </div>
                            <div class="max-h-60 overflow-y-auto divide-y divide-sky-100 dark:divide-sky-900/30">
                                @foreach ($allEvents as $ev)
                                    <a href="{{ route('cm.show', $ev) }}"
                                       class="block px-4 py-2 hover:bg-sky-50 dark:hover:bg-slate-800 transition-colors {{ $ev->id === $event->id ? 'bg-sky-50/70 dark:bg-sky-950/40 text-sky-700 dark:text-amber-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="truncate">{{ $ev->title }}</span>
                                            @if ($ev->is_active)
                                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-extrabold uppercase">Live</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <button @click="copyShareLink()" type="button"
                            class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-all flex items-center space-x-1.5"
                            title="Share CM Meta Link">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        <span>Share</span>
                    </button>
                </div>
            </div>

            <!-- Title & Status -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @if ($event->cm_number)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-gradient-to-r from-amber-500/20 to-amber-600/20 text-amber-600 dark:text-amber-400 border border-amber-500/40">
                                CM #{{ $event->cm_number }}
                            </span>
                        @endif

                        @if ($event->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/40">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                ACTIVE CHAMPIONS MEETING
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                ARCHIVED META
                            </span>
                        @endif

                        @if ($event->event_date)
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                &bull; {{ $event->event_date->format('F Y') }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $event->title }}
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Comprehensive Global Server Champions Meeting meta breakdown, tier rankings, optimal skill inherits, and team lineups.
                    </p>
                </div>
            </div>

            <!-- Track Condition Pills Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-2">
                <!-- 1. Racecourse -->
                <div class="bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 text-center">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Racecourse</span>
                    <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">{{ $event->track_name }}</span>
                </div>

                <!-- 2. Distance -->
                <div class="bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 text-center">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Distance</span>
                    <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $event->distance_type }} <span class="font-mono text-sky-600 dark:text-sky-400">({{ $event->distance_meters }}m)</span>
                    </span>
                </div>

                <!-- 3. Surface -->
                <div class="bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 text-center">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Surface</span>
                    <span class="text-xs sm:text-sm font-black {{ $event->surface === 'Turf' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ $event->surface }}
                    </span>
                </div>

                <!-- 4. Season -->
                <div class="bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 text-center">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Season</span>
                    <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">{{ $event->season ?? 'Winter' }}</span>
                </div>

                <!-- 5. Weather -->
                <div class="bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 text-center">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Weather</span>
                    <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">{{ $event->weather ?? 'Sunny' }}</span>
                </div>

                <!-- 6. Track Condition -->
                <div class="bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 text-center">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Condition</span>
                    <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">{{ $event->track_condition ?? 'Firm' }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 2: Tier List by Running Style (SS to C) -->
    <!-- ========================================== -->
    <section class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-1">
                    <span>Section 2</span>
                    <span>&bull;</span>
                    <span>Competitive Tier List</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    Meta Tier List (SS &rarr; C)
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Click any racer card to open the complete individual build modal (stat targets, mandatory skills, inherited uniques, and strategies).
                </p>
            </div>

            <!-- Stats counter badge -->
            <div class="flex items-center space-x-2 text-xs">
                <span class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold border border-slate-200 dark:border-slate-700">
                    Showing <span class="text-sky-600 dark:text-amber-400 font-mono" x-text="totalFilteredCount()"></span> / <span class="font-mono">{{ count($entries) }}</span> Umas
                </span>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 rounded-3xl p-5 shadow-lg space-y-4 transition-colors">
            <!-- Top row: Search input -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-ref="searchInput" x-model="search"
                           placeholder="Search racer name or variant (e.g. Agnes, Alter, Falcon)..."
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl pl-10 pr-10 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-sky-500 dark:focus:border-amber-500 transition-all">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <template x-if="search">
                            <button @click="search = ''" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs font-bold">&times;</button>
                        </template>
                    </div>
                </div>

                <!-- Role Filter Buttons -->
                <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1 hidden sm:inline">Role:</span>
                    <template x-for="role in ['All', 'Ace', 'Parent', 'Debuffer']" :key="role">
                        <button type="button" @click="selectedRole = role"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                :class="selectedRole === role ? 'bg-sky-600 dark:bg-amber-500 text-white dark:text-slate-950 shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                            <span x-text="role"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Bottom row: Running Style Tabs -->
            <div class="flex items-center space-x-1.5 overflow-x-auto pt-2 border-t border-sky-100 dark:border-sky-900/40">
                <template x-for="style in ['All', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer']" :key="style">
                    <button type="button" @click="selectedStyle = style"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap"
                            :class="selectedStyle === style ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 shadow-sm' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                        <span x-text="style"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Tier Board Rows: SS, S+, S, A, B, C -->
        <div class="space-y-4">
            <template x-for="rank in tierRanks" :key="rank">
                <div x-show="filteredEntries(rank).length > 0 || !isFiltered()"
                     class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/50 rounded-3xl overflow-hidden shadow-md transition-colors flex flex-col md:flex-row">
                    
                    <!-- Rank Label Box -->
                    <div class="md:w-28 p-4 md:p-5 flex flex-row md:flex-col items-center justify-between md:justify-center border-b md:border-b-0 md:border-r border-sky-100 dark:border-sky-900/50 flex-shrink-0"
                         :class="{
                            'bg-gradient-to-br from-amber-500/20 to-rose-600/20 text-amber-500': rank === 'SS',
                            'bg-gradient-to-br from-amber-500/15 to-amber-600/15 text-amber-500': rank === 'S+',
                            'bg-gradient-to-br from-sky-500/15 to-blue-600/15 text-sky-500': rank === 'S',
                            'bg-gradient-to-br from-emerald-500/15 to-teal-600/15 text-emerald-500': rank === 'A',
                            'bg-gradient-to-br from-indigo-500/15 to-purple-600/15 text-indigo-400': rank === 'B',
                            'bg-gradient-to-br from-slate-500/15 to-slate-600/15 text-slate-400': rank === 'C',
                         }">
                        <div class="text-2xl md:text-3xl font-black font-mono tracking-tight" x-text="rank"></div>
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 mt-0.5">
                            <span x-text="filteredEntries(rank).length"></span> Umas
                        </div>
                    </div>

                    <!-- Entries Grid for this Rank -->
                    <div class="flex-1 p-4 md:p-5 min-h-[100px] flex items-center">
                        <template x-if="filteredEntries(rank).length === 0">
                            <div class="text-xs text-slate-400 dark:text-slate-500 italic py-2">
                                No racers in this tier match current filters.
                            </div>
                        </template>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5 w-full">
                            <template x-for="entry in filteredEntries(rank)" :key="entry.id">
                                <div @click="openModal(entry)"
                                     class="group cursor-pointer bg-slate-50 dark:bg-slate-900/80 hover:bg-sky-50/70 dark:hover:bg-slate-800/90 border border-slate-200 dark:border-slate-800 hover:border-sky-300 dark:hover:border-sky-700 rounded-2xl p-2.5 transition-all duration-200 hover:-translate-y-0.5 shadow-sm hover:shadow-md flex flex-col items-center text-center relative overflow-hidden">
                                    
                                    <!-- Role Pill Badge -->
                                    <div class="absolute top-1.5 right-1.5">
                                        <span class="text-[8px] font-extrabold uppercase px-1.5 py-0.2 rounded"
                                              :class="{
                                                'bg-rose-500/20 text-rose-500 border border-rose-500/40': entry.role === 'Ace',
                                                'bg-amber-500/20 text-amber-500 border border-amber-500/40': entry.role === 'Parent',
                                                'bg-purple-500/20 text-purple-400 border border-purple-500/40': entry.role === 'Debuffer'
                                              }"
                                              x-text="entry.role"></span>
                                    </div>

                                    <!-- Avatar -->
                                    <div class="relative w-16 h-16 sm:w-18 sm:h-18 rounded-2xl overflow-hidden bg-slate-200 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 my-1 group-hover:scale-105 transition-transform duration-200">
                                        <template x-if="entry.character?.avatar_url">
                                            <img :src="entry.character.avatar_url" :alt="entry.character.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!entry.character?.avatar_url">
                                            <div class="w-full h-full flex items-center justify-center font-black text-sm text-amber-500">
                                                <span x-text="(entry.character?.name || 'UM').substring(0, 2).toUpperCase()"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Character Name -->
                                    <div class="w-full mt-1">
                                        <div class="text-xs font-black text-slate-900 dark:text-white truncate" x-text="entry.character?.name"></div>
                                        <div class="text-[10px] text-slate-400 truncate" x-text="entry.character?.variant_title || 'Original'"></div>
                                    </div>

                                    <!-- Running Style Badge -->
                                    <div class="mt-2 w-full">
                                        <span class="block w-full text-[9px] font-bold py-0.5 rounded bg-slate-200/60 dark:bg-slate-950 text-slate-600 dark:text-slate-400 truncate border border-slate-300/40 dark:border-slate-800"
                                              x-text="entry.running_style || entry.recommended_style"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 3: Minimum & Recommended Stats     -->
    <!-- ========================================== -->
    @if ($event->stat_recommendations)
        <section class="space-y-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-1">
                    <span>Section 3</span>
                    <span>&bull;</span>
                    <span>Stat Benchmarks</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    Target Stat Benchmarks
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Recommended target stats to ensure top survival rate and competitive speed on this track.
                </p>
            </div>

            <div class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 rounded-3xl p-6 shadow-xl space-y-6 transition-colors">
                <!-- 5 Stats Comparison Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3.5">
                    @php
                        $statConfigs = [
                            'speed' => ['name' => 'Speed', 'color' => 'text-sky-500 dark:text-sky-400', 'bg' => 'bg-sky-50 dark:bg-sky-950/40 border-sky-200 dark:border-sky-800/60'],
                            'stamina' => ['name' => 'Stamina', 'color' => 'text-rose-500 dark:text-rose-400', 'bg' => 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/60'],
                            'power' => ['name' => 'Power', 'color' => 'text-amber-500 dark:text-amber-400', 'bg' => 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/60'],
                            'guts' => ['name' => 'Guts', 'color' => 'text-emerald-500 dark:text-emerald-400', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/60'],
                            'wit' => ['name' => 'Wit', 'color' => 'text-indigo-500 dark:text-indigo-400', 'bg' => 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800/60'],
                        ];
                    @endphp

                    @foreach ($statConfigs as $key => $cfg)
                        @php
                            $minVal = $event->stat_recommendations['minimum'][$key] ?? '—';
                            $recVal = $event->stat_recommendations['recommended'][$key] ?? '—';
                        @endphp
                        <div class="rounded-2xl border p-4 text-center {{ $cfg['bg'] }}">
                            <span class="block text-xs font-bold uppercase tracking-wider {{ $cfg['color'] }}">{{ $cfg['name'] }}</span>
                            
                            <div class="mt-2 space-y-1">
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Min Target</div>
                                <div class="text-base font-black font-mono text-slate-700 dark:text-slate-300">{{ $minVal }}</div>
                            </div>

                            <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-800 space-y-1">
                                <div class="text-[10px] text-amber-600 dark:text-amber-400 uppercase font-bold">Recommended</div>
                                <div class="text-lg font-black font-mono text-slate-900 dark:text-white">{{ $recVal }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Notes / Stamina Requirements -->
                @if (!empty($event->stat_recommendations['notes']))
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white">Track Stat Notes:</span>
                            <p class="mt-0.5">{{ $event->stat_recommendations['notes'] }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 4: Skill Inheritance (Parent Uniques) -->
    <!-- ========================================== -->
    @if ($event->skill_inheritance)
        <section class="space-y-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-1">
                    <span>Section 4</span>
                    <span>&bull;</span>
                    <span>Inheritance Priority</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    Skill Inheritance & Parent Uniques
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Essential parent unique skills to inherit per running style to guarantee optimal spurt acceleration timing.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach (['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'] as $style)
                    @php
                        $skills = $event->skill_inheritance[$style] ?? [];
                    @endphp
                    <div class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 rounded-3xl p-5 shadow-lg flex flex-col transition-colors">
                        <div class="flex items-center justify-between pb-3 border-b border-sky-100 dark:border-sky-900/40">
                            <h3 class="font-black text-sm text-slate-900 dark:text-white">{{ $style }}</h3>
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                                {{ count($skills) }} Priority
                            </span>
                        </div>

                        <div class="mt-4 space-y-2.5 flex-1">
                            @forelse ($skills as $item)
                                @php
                                    $skillName = is_array($item) ? ($item['name'] ?? '') : $item;
                                    $skillIcon = is_array($item) ? ($item['icon'] ?? null) : null;
                                @endphp
                                <div class="flex items-center space-x-2.5 p-2 rounded-xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800/80">
                                    <div class="w-7 h-7 rounded-lg overflow-hidden bg-slate-200 dark:bg-slate-950 flex items-center justify-center flex-shrink-0 border border-slate-300 dark:border-slate-800">
                                        @if ($skillIcon)
                                            <img src="{{ Str::startsWith($skillIcon, ['http://', 'https://']) ? $skillIcon : asset('storage/' . ltrim($skillIcon, '/')) }}"
                                                 alt="{{ $skillName }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $skillName }}</span>
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 dark:text-slate-500 italic py-3 text-center">
                                    No specific inheritance defined.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 5: Recommended Skills per Style    -->
    <!-- ========================================== -->
    @if ($event->recommended_skills)
        <section class="space-y-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-1">
                    <span>Section 5</span>
                    <span>&bull;</span>
                    <span>Skill Hierarchy</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    Recommended Skills by Running Style
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Separating core acceleration triggers from mid-leg velocity and stamina recovery skills.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach (['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'] as $style)
                    @php
                        $styleSkills = $event->recommended_skills[$style] ?? [];
                        $coreAccel = $styleSkills['core_accel'] ?? [];
                        $velocityOther = $styleSkills['velocity_other'] ?? [];
                    @endphp
                    <div class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 rounded-3xl p-5 sm:p-6 shadow-lg space-y-4 transition-colors">
                        <div class="flex items-center justify-between pb-3 border-b border-sky-100 dark:border-sky-900/40">
                            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                <span>{{ $style }}</span>
                            </h3>
                            <span class="text-xs font-bold text-slate-400 uppercase">Skill Kit</span>
                        </div>

                        <!-- Core Accel Sub-Block -->
                        <div class="space-y-2">
                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-rose-500 flex items-center space-x-1">
                                <span>Core Acceleration (Mandatory)</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($coreAccel as $skill)
                                    @php
                                        $sName = is_array($skill) ? ($skill['name'] ?? '') : $skill;
                                        $sIcon = is_array($skill) ? ($skill['icon'] ?? null) : null;
                                    @endphp
                                    <div class="inline-flex items-center space-x-1.5 px-2.5 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-xs font-bold text-rose-700 dark:text-rose-300">
                                        @if ($sIcon)
                                            <img src="{{ Str::startsWith($sIcon, ['http://', 'https://']) ? $sIcon : asset('storage/' . ltrim($sIcon, '/')) }}"
                                                 alt="{{ $sName }}" class="w-4 h-4 rounded object-cover">
                                        @else
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        @endif
                                        <span>{{ $sName }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-slate-400 italic">None specified.</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Velocity & Recovery Sub-Block -->
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-sky-600 dark:text-sky-400 flex items-center space-x-1">
                                <span>Velocity & Support</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($velocityOther as $skill)
                                    @php
                                        $sName = is_array($skill) ? ($skill['name'] ?? '') : $skill;
                                        $sIcon = is_array($skill) ? ($skill['icon'] ?? null) : null;
                                    @endphp
                                    <div class="inline-flex items-center space-x-1.5 px-2.5 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-900/50 text-xs font-bold text-sky-700 dark:text-sky-300">
                                        @if ($sIcon)
                                            <img src="{{ Str::startsWith($sIcon, ['http://', 'https://']) ? $sIcon : asset('storage/' . ltrim($sIcon, '/')) }}"
                                                 alt="{{ $sName }}" class="w-4 h-4 rounded object-cover">
                                        @else
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        @endif
                                        <span>{{ $sName }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-slate-400 italic">None specified.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 6: Recommended Support Card Decks  -->
    <!-- ========================================== -->
    @if ($event->supoka_decks)
        <section class="space-y-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-1">
                    <span>Section 6</span>
                    <span>&bull;</span>
                    <span>Deck Building</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    Recommended Support Card (Supoka) Decks
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Tested training deck templates with card synergies and stat distribution targets.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach ($event->supoka_decks as $deck)
                    <div class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 rounded-3xl p-6 shadow-lg space-y-4 transition-colors">
                        <div class="flex items-center justify-between pb-3 border-b border-sky-100 dark:border-sky-900/40">
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-white">
                                    {{ $deck['archetype'] ?? 'Deck Archetype' }}
                                </h3>
                                <span class="text-[11px] font-bold text-amber-500 uppercase">Optimal Training Setup</span>
                            </div>
                        </div>

                        <!-- 6 Cards Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            @foreach ($deck['cards'] ?? [] as $card)
                                @php
                                    $cardName = is_array($card) ? ($card['name'] ?? '') : $card;
                                    $cardIcon = is_array($card) ? ($card['icon'] ?? null) : null;
                                @endphp
                                <div class="p-2 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-950 flex items-center justify-center flex-shrink-0 border border-slate-300 dark:border-slate-700">
                                        @if ($cardIcon)
                                            <img src="{{ Str::startsWith($cardIcon, ['http://', 'https://']) ? $cardIcon : asset('storage/' . ltrim($cardIcon, '/')) }}"
                                                 alt="{{ $cardName }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[9px] font-black text-amber-500">SSR</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate leading-tight" title="{{ $cardName }}">
                                        {{ $cardName }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Deck Notes -->
                        @if (!empty($deck['notes']))
                            <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-xs text-amber-800 dark:text-amber-300 flex items-start space-x-2">
                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $deck['notes'] }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 7: Recommended Team Compositions   -->
    <!-- ========================================== -->
    @if ($event->team_compositions)
        <section class="space-y-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-1">
                    <span>Section 7</span>
                    <span>&bull;</span>
                    <span>Tactical Strategy</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    Recommended 3-Uma Team Compositions
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Synergistic 3-member lineups and strategic win conditions against opposing Champions Meeting teams.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach ($event->team_compositions as $comp)
                    <div class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 rounded-3xl p-6 shadow-lg space-y-4 transition-colors">
                        <div class="flex items-center justify-between pb-3 border-b border-sky-100 dark:border-sky-900/40">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                {{ $comp['name'] ?? 'Team Composition' }}
                            </h3>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                3-Uma Lineup
                            </span>
                        </div>

                        <!-- 3-Uma Lineup Pills -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            @foreach ($comp['lineup'] ?? [] as $member)
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 text-center">
                                    <div class="text-[10px] uppercase font-bold text-slate-400">Racer Slot</div>
                                    <div class="text-xs sm:text-sm font-black text-slate-900 dark:text-white mt-0.5">
                                        {{ $member }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Tactical Win Condition Strategy -->
                        @if (!empty($comp['strategy']))
                            <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-900/50 text-xs text-slate-700 dark:text-slate-300 space-y-1">
                                <div class="font-bold text-sky-700 dark:text-sky-400 flex items-center space-x-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>Tactical Strategy & Win Condition:</span>
                                </div>
                                <p class="leading-relaxed pl-5">{{ $comp['strategy'] }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 8: Meta Changelog Accordion        -->
    <!-- ========================================== -->
    <section x-data="{ open: false }" class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/60 rounded-3xl overflow-hidden shadow-lg transition-colors">
        <button @click="open = !open" type="button"
                class="w-full p-5 sm:p-6 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors text-left">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-0.5">
                    <span>Section 8</span>
                    <span>&bull;</span>
                    <span>Meta Audit History</span>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">
                    Meta Changelog & Tier Movements
                </h3>
                <span class="text-xs text-slate-400">{{ count($changelogs) }} documented balance adjustments for this tournament.</span>
            </div>

            <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </button>

        <div x-show="open" x-cloak class="border-t border-sky-100 dark:border-sky-900/40 divide-y divide-sky-100 dark:divide-sky-900/30">
            @forelse ($changelogs as $log)
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 flex items-center justify-center font-bold text-amber-500 flex-shrink-0">
                            @if ($log->character?->avatar_url)
                                <img src="{{ $log->character->avatar_url }}" alt="{{ $log->character->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($log->character?->name ?? 'UM', 0, 2)) }}
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-black text-slate-900 dark:text-white">{{ $log->character?->full_name }}</span>
                                <div class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                                    <span class="text-slate-400">{{ $log->previous_tier ?? 'New' }}</span>
                                    <span class="text-amber-500">&rarr;</span>
                                    <span class="text-amber-600 dark:text-amber-400 font-extrabold">{{ $log->new_tier }}</span>
                                </div>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 mt-0.5">{{ $log->reason }}</p>
                        </div>
                    </div>

                    <div class="text-[11px] font-medium text-slate-400 dark:text-slate-500 flex-shrink-0">
                        {{ $log->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400 italic">
                    No changelogs recorded yet for this tournament.
                </div>
            @endforelse
        </div>
    </section>

    <!-- ========================================== -->
    <!-- BUILD NOTES MODAL (Individual Uma Details) -->
    <!-- ========================================== -->
    <div x-show="modalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4">
        
        <div @click.away="closeModal()"
             x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-[#0E1726] border border-sky-100 dark:border-sky-900/80 rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl relative text-left">
            
            <button @click="closeModal()" type="button"
                    class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Header with Character Details -->
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-center flex-shrink-0">
                    <template x-if="selectedEntry?.character?.avatar_url">
                        <img :src="selectedEntry.character.avatar_url" :alt="selectedEntry.character.name" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedEntry?.character?.avatar_url">
                        <span class="font-black text-amber-500 text-lg" x-text="(selectedEntry?.character?.name || 'UM').substring(0, 2).toUpperCase()"></span>
                    </template>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-black px-2 py-0.5 rounded-lg bg-amber-500/20 text-amber-500 border border-amber-500/40 font-mono"
                              x-text="'Tier ' + selectedEntry?.tier_rank"></span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800"
                              x-text="selectedEntry?.running_style || selectedEntry?.recommended_style"></span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-rose-500/15 text-rose-500 border border-rose-500/30"
                              x-text="selectedEntry?.role"></span>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white" x-text="selectedEntry?.character?.full_name"></h3>
                    <div class="text-xs text-slate-400" x-text="selectedEntry?.character?.rarity ? selectedEntry.character.rarity + '★ Base Rarity' : ''"></div>
                </div>
            </div>

            <!-- Modal Body: Build Specifications -->
            <div class="space-y-4 pt-2 border-t border-sky-100 dark:border-sky-900/40 text-xs text-slate-700 dark:text-slate-300">
                <!-- Target Stats -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-slate-400 text-[10px] mb-1">Target Stat Distribution</h4>
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 font-mono font-bold text-slate-900 dark:text-white text-sm"
                         x-text="selectedEntry?.build_notes?.target_stats || 'Not specified'"></div>
                </div>

                <!-- Mandatory Skills -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-rose-500 text-[10px] mb-1">Mandatory Skills</h4>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="skill in (selectedEntry?.build_notes?.mandatory_skills || [])" :key="skill">
                            <span class="px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900/60 font-bold"
                                  x-text="typeof skill === 'object' ? skill.name : skill"></span>
                        </template>
                    </div>
                </div>

                <!-- Inherited Uniques -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-amber-500 text-[10px] mb-1">Inherited Unique Skills</h4>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="skill in (selectedEntry?.build_notes?.inherited_uniques || [])" :key="skill">
                            <span class="px-2.5 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/60 font-bold"
                                  x-text="typeof skill === 'object' ? skill.name : skill"></span>
                        </template>
                    </div>
                </div>

                <!-- Strategic Summary -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-sky-500 text-[10px] mb-1">Tactical Analysis & Build Strategy</h4>
                    <p class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 leading-relaxed text-slate-700 dark:text-slate-300 text-xs"
                       x-text="selectedEntry?.build_notes?.summary || 'No detailed tactical summary provided.'"></p>
                </div>
            </div>

            <div class="pt-2 text-right">
                <button @click="closeModal()" type="button"
                        class="px-5 py-2.5 rounded-2xl bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-white text-white dark:text-slate-900 font-bold text-xs transition-colors">
                    Close Build Guide
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
