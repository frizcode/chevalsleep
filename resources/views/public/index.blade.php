<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChevalSleep') }} — Uma Musume Global Competitive Meta Tier List</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950">

    <!-- Subtle Background Ambient Glows -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/3 -right-20 w-[500px] h-[500px] bg-rose-600/10 rounded-full blur-[160px]"></div>
        <div class="absolute -bottom-20 left-1/3 w-[600px] h-[600px] bg-violet-600/10 rounded-full blur-[180px]"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900/80 border-b border-slate-800/80 sticky top-0 z-40 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 via-rose-500 to-amber-600 flex items-center justify-center shadow-lg shadow-rose-950/50 text-slate-950 font-black text-xl group-hover:scale-105 group-hover:shadow-amber-500/20 transition-all duration-300">
                    CS
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-black text-xl tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">ChevalSleep</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-400 border border-amber-500/40">PRO</span>
                    </div>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400 block -mt-0.5">Uma Musume Global Meta</span>
                </div>
            </a>

            <!-- Event Switcher & Admin Actions -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Tournament Switcher Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-800/90 hover:bg-slate-800 text-slate-200 border border-slate-700/80 hover:border-slate-600 transition-all shadow-sm">
                        <span class="w-2 h-2 rounded-full {{ $currentEvent?->is_active ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]' : 'bg-slate-500' }}"></span>
                        <span class="max-w-[120px] sm:max-w-[180px] truncate">{{ $currentEvent?->title ?? 'Select Event' }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-72 bg-slate-900/95 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl py-2 z-50 overflow-hidden">
                        <div class="px-4 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-800 flex items-center justify-between">
                            <span>Available Tournaments</span>
                            <span class="text-amber-400 font-mono">{{ $events->count() }} Total</span>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-800/40 py-1">
                            @foreach ($events as $event)
                                <a href="{{ route('home', ['event' => $event->slug]) }}"
                                   class="block px-4 py-2.5 text-xs hover:bg-slate-800/80 transition-colors {{ $currentEvent?->id === $event->id ? 'bg-amber-500/10 text-amber-300 font-bold border-l-2 border-amber-500' : 'text-slate-300' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="truncate">{{ $event->title }}</span>
                                        @if ($event->is_active)
                                            <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex-shrink-0">Active</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 mt-1 text-[10px] text-slate-500 font-medium">
                                        <span>{{ $event->track_name }}</span>
                                        <span>&bull;</span>
                                        <span class="{{ $event->surface === 'Turf' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $event->surface }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $event->distance_type }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Admin CMS Button -->
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 transition-all shadow-md shadow-amber-950/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden sm:inline">Admin CMS</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-xs font-semibold text-slate-400 hover:text-white px-2 py-1 transition-colors">
                        Staff Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Alpine.js Main Container -->
    <div x-data="{
        entries: {{ json_encode($entries) }},
        tierRanks: {{ json_encode($tierRanks) }},
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
                if (this.selectedStyle !== 'All' && entry.recommended_style !== this.selectedStyle) return false;
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

        countByStyle(style) {
            if (style === 'All') return this.entries.length;
            return this.entries.filter(e => e.recommended_style === style).length;
        },

        countByRole(role) {
            if (role === 'All') return this.entries.length;
            return this.entries.filter(e => e.role === role).length;
        },

        filteredEntries(rank) {
            return this.entries.filter(entry => {
                if (entry.tier_rank !== rank) return false;
                if (this.selectedStyle !== 'All' && entry.recommended_style !== this.selectedStyle) return false;
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

        parseStats(statsString) {
            if (!statsString) return null;
            const parts = statsString.split('/').map(s => s.trim());
            if (parts.length === 5) {
                return [
                    { label: 'SPD', name: 'Speed', val: parts[0], color: 'from-sky-500 to-cyan-400', text: 'text-sky-400', bg: 'bg-sky-500/10 border-sky-500/30' },
                    { label: 'STM', name: 'Stamina', val: parts[1], color: 'from-rose-500 to-pink-500', text: 'text-rose-400', bg: 'bg-rose-500/10 border-rose-500/30' },
                    { label: 'PWR', name: 'Power', val: parts[2], color: 'from-amber-500 to-yellow-400', text: 'text-amber-400', bg: 'bg-amber-500/10 border-amber-500/30' },
                    { label: 'GUT', name: 'Guts', val: parts[3], color: 'from-emerald-500 to-teal-400', text: 'text-emerald-400', bg: 'bg-emerald-500/10 border-emerald-500/30' },
                    { label: 'WIT', name: 'Wisdom', val: parts[4], color: 'from-purple-500 to-indigo-400', text: 'text-purple-400', bg: 'bg-purple-500/10 border-purple-500/30' },
                ];
            }
            return null;
        }
    }"
    @keydown.window.slash.prevent="$refs.searchInput.focus()"
    class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Toast Feedback for Copy Link -->
        <div x-show="copiedToast" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-slate-950 font-bold px-4 py-2.5 rounded-2xl shadow-xl flex items-center space-x-2 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Tournament link copied to clipboard!</span>
        </div>

        @if ($currentEvent)
            <!-- Event Hero Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-9 shadow-2xl backdrop-blur-xl">
                <!-- Glowing corner gradient accents -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-rose-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-48 -bottom-24 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-widest bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                <span>Global Server Tournament Meta</span>
                            </span>
                            @if ($currentEvent->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                    Current Active Track
                                </span>
                            @endif
                        </div>

                        <div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                                {{ $currentEvent->title }}
                            </h1>
                            <div class="flex items-center space-x-2 text-slate-400 text-sm mt-1">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Racecourse: <strong class="text-slate-100">{{ $currentEvent->track_name }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Track Specs Badges & Share -->
                    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                        <div class="px-4 py-2.5 rounded-2xl bg-slate-950/60 border border-slate-800 text-center shadow-inner">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Distance</span>
                            <span class="text-sm font-extrabold text-white">{{ $currentEvent->distance_type }}</span>
                        </div>

                        <div class="px-4 py-2.5 rounded-2xl bg-slate-950/60 border border-slate-800 text-center shadow-inner">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Length</span>
                            <span class="text-sm font-extrabold text-amber-400 font-mono">{{ $currentEvent->distance_meters }}m</span>
                        </div>

                        <div class="px-4 py-2.5 rounded-2xl border text-center shadow-inner {{ $currentEvent->surface === 'Turf' ? 'bg-emerald-950/30 border-emerald-500/30 text-emerald-400' : 'bg-amber-950/30 border-amber-500/30 text-amber-400' }}">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Surface</span>
                            <span class="text-sm font-extrabold">{{ $currentEvent->surface }}</span>
                        </div>

                        <button @click="copyShareLink()" type="button"
                                class="px-3.5 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition-all flex items-center space-x-1.5 shadow-sm"
                                title="Copy direct link to this tournament">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            <span class="hidden sm:inline">Share</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Interactive Filter Toolbar (Zero Page Reload via Alpine.js) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-4 backdrop-blur-xl">
                <!-- Top Row: Search & Status Count -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-ref="searchInput" x-model="search"
                               placeholder="Search racer name or variant (e.g. Agnes, Alter, Falcon)..."
                               class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl pl-10 pr-16 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                        
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center space-x-1">
                            <button x-show="search.length > 0" @click="search = ''"
                                    class="text-slate-400 hover:text-white p-1 text-sm font-bold">
                                &times;
                            </button>
                            <span class="hidden sm:inline text-[10px] font-mono text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700">/</span>
                        </div>
                    </div>

                    <!-- Counter & Reset Controls -->
                    <div class="flex items-center space-x-3">
                        <div class="text-xs text-slate-400">
                            Showing <span class="font-bold text-amber-400" x-text="totalFilteredCount()"></span> of <span class="font-bold text-white" x-text="entries.length"></span> racers
                        </div>

                        <button x-show="isFiltered()" @click="resetFilters()" x-cloak
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>

                <!-- Bottom Row: Filter Pills with Dynamic Counts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-slate-800/80">
                    <!-- Running Style Filters -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mr-1 flex items-center space-x-1">
                            <span>Style:</span>
                        </span>
                        @foreach (['All', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'] as $style)
                            <button @click="selectedStyle = '{{ $style }}'"
                                    :class="selectedStyle === '{{ $style }}' ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-950/40 border-amber-400' : 'bg-slate-950/60 text-slate-300 hover:text-white hover:bg-slate-800 border-slate-800'"
                                    class="px-2.5 py-1 rounded-xl text-xs font-medium border transition-all flex items-center space-x-1.5">
                                <span>{{ $style }}</span>
                                <span class="text-[10px] opacity-75 font-mono" x-text="'(' + countByStyle('{{ $style }}') + ')'"></span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Role Filters -->
                    <div class="flex flex-wrap items-center gap-1.5 md:justify-end">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mr-1 flex items-center space-x-1">
                            <span>Role:</span>
                        </span>
                        @foreach (['All', 'Ace', 'Parent', 'Debuffer'] as $role)
                            <button @click="selectedRole = '{{ $role }}'"
                                    :class="selectedRole === '{{ $role }}' ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-950/40 border-amber-400' : 'bg-slate-950/60 text-slate-300 hover:text-white hover:bg-slate-800 border-slate-800'"
                                    class="px-2.5 py-1 rounded-xl text-xs font-medium border transition-all flex items-center space-x-1.5">
                                <span>{{ $role }}</span>
                                <span class="text-[10px] opacity-75 font-mono" x-text="'(' + countByRole('{{ $role }}') + ')'"></span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tier List Board (S+, S, A, B, C) -->
            <div class="space-y-6">
                <template x-for="rank in tierRanks" :key="rank">
                    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-xl transition-all"
                         x-show="filteredEntries(rank).length > 0 || !isFiltered()">
                        
                        <!-- Tier Header Bar -->
                        <div class="px-5 py-4 bg-slate-950/80 border-b border-slate-800/90 flex items-center justify-between">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-14 h-11 rounded-2xl flex items-center justify-center text-xl shadow-lg"
                                     :class="{
                                         'bg-gradient-to-tr from-rose-600 via-rose-500 to-amber-500 text-white font-black glow-rose': rank === 'S+',
                                         'bg-amber-500 text-slate-950 font-black glow-amber': rank === 'S',
                                         'bg-violet-600 text-white font-black glow-violet': rank === 'A',
                                         'bg-sky-600 text-white font-black glow-sky': rank === 'B',
                                         'bg-slate-700 text-slate-200 font-bold': rank === 'C'
                                     }">
                                    <span x-text="rank"></span>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h2 class="text-base font-black text-white tracking-wide uppercase" x-text="'Tier ' + rank"></h2>
                                        <span class="text-xs text-slate-500 font-mono" x-text="'(' + filteredEntries(rank).length + ' racers)'"></span>
                                    </div>
                                    <span class="text-xs text-slate-400 font-medium block"
                                          x-text="rank === 'S+' ? 'Apex Meta &bull; Near mandatory for championship contention' : (rank === 'S' ? 'Premier Core &bull; Highly reliable and top-tier consistency' : (rank === 'A' ? 'Viable Tournament Competitors &bull; Strong with proper builds' : (rank === 'B' ? 'Niche & Secondary Options &bull; Role-specific counterpicks' : 'Situational & Off-Meta')))">
                                    </span>
                                </div>
                            </div>

                            <span class="text-xs font-mono text-slate-500 hidden sm:inline">
                                Click card for build & stats
                            </span>
                        </div>

                        <!-- Racers Cards Grid -->
                        <div class="p-4 sm:p-6">
                            <template x-if="filteredEntries(rank).length > 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    <template x-for="entry in filteredEntries(rank)" :key="entry.id">
                                        <div @click="openModal(entry)"
                                             class="group relative bg-slate-950/70 hover:bg-slate-800/80 border border-slate-800/90 hover:border-amber-500/50 rounded-2xl p-4 flex items-center space-x-3.5 cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-amber-950/20">
                                            
                                            <!-- Character Avatar or Initials Fallback -->
                                            <div class="relative flex-shrink-0">
                                                <template x-if="entry.character && entry.character.avatar_url">
                                                    <img :src="entry.character.avatar_url" :alt="entry.character.name"
                                                         class="w-14 h-14 rounded-2xl object-cover bg-slate-900 border-2 transition-colors"
                                                         :class="entry.character.rarity === 3 ? 'border-amber-500/60 shadow-[0_0_10px_rgba(245,158,11,0.2)]' : 'border-slate-700'">
                                                </template>
                                                <template x-if="!entry.character || !entry.character.avatar_url">
                                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 border-2 border-slate-700/80 flex items-center justify-center font-black text-amber-400 text-sm shadow-inner group-hover:border-amber-500/40 transition-colors">
                                                        <span x-text="(entry.character ? entry.character.name : 'UM').substring(0, 2).toUpperCase()"></span>
                                                    </div>
                                                </template>

                                                <!-- Rarity Badge overlay -->
                                                <div class="absolute -bottom-1 -right-1 bg-slate-950/95 border border-slate-800 rounded px-1 text-[9px] font-black text-amber-400 leading-tight shadow">
                                                    <span x-text="(entry.character?.rarity || 3) + '★'"></span>
                                                </div>
                                            </div>

                                            <!-- Character Info -->
                                            <div class="min-w-0 flex-1 space-y-1">
                                                <div class="flex items-center space-x-1.5">
                                                    <h3 class="font-bold text-sm text-white truncate group-hover:text-amber-300 transition-colors"
                                                        x-text="entry.character ? entry.character.name : 'Unknown'"></h3>
                                                </div>

                                                <!-- Variant Title Badge -->
                                                <template x-if="entry.character && entry.character.variant_title && entry.character.variant_title !== 'Original'">
                                                    <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/30"
                                                          x-text="entry.character.variant_title"></span>
                                                </template>

                                                <div class="flex flex-wrap items-center gap-1 pt-0.5">
                                                    <!-- Role Badge with Icon -->
                                                    <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-bold"
                                                          :class="{
                                                              'bg-rose-500/10 text-rose-400 border border-rose-500/30': entry.role === 'Ace',
                                                              'bg-blue-500/10 text-blue-400 border border-blue-500/30': entry.role === 'Parent',
                                                              'bg-fuchsia-500/10 text-fuchsia-400 border border-fuchsia-500/30': entry.role === 'Debuffer'
                                                          }">
                                                        <template x-if="entry.role === 'Ace'">
                                                            <svg class="w-3 h-3 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        </template>
                                                        <span x-text="entry.role"></span>
                                                    </span>

                                                    <!-- Style Pill -->
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-800/90 text-slate-300 border border-slate-700/80"
                                                          x-text="entry.recommended_style">
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Micro Arrow on hover -->
                                            <div class="text-slate-600 group-hover:text-amber-400 transition-colors flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="filteredEntries(rank).length === 0">
                                <div class="py-8 text-center text-xs text-slate-500 italic bg-slate-950/40 rounded-2xl border border-slate-800/40">
                                    No entries matching current filters in Tier <span class="font-bold text-slate-400" x-text="rank"></span>.
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Character Build Notes Modal (Alpine.js) -->
            <div x-show="modalOpen" x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 role="dialog" aria-modal="true">
                <!-- Backdrop with smooth transition -->
                <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"
                     x-show="modalOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeModal()"></div>

                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 text-slate-200"
                         x-show="modalOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @keydown.escape.window="closeModal()">
                        
                        <template x-if="selectedEntry">
                            <div class="space-y-6">
                                <!-- Modal Header -->
                                <div class="flex items-start justify-between border-b border-slate-800 pb-5">
                                    <div class="flex items-center space-x-4">
                                        <!-- Avatar -->
                                        <div class="relative flex-shrink-0">
                                            <template x-if="selectedEntry.character && selectedEntry.character.avatar_url">
                                                <img :src="selectedEntry.character.avatar_url" :alt="selectedEntry.character.name"
                                                     class="w-16 h-16 rounded-2xl object-cover bg-slate-950 border-2 border-amber-500/60 shadow-lg shadow-amber-950/30">
                                            </template>
                                            <template x-if="!selectedEntry.character || !selectedEntry.character.avatar_url">
                                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 border-2 border-slate-700 flex items-center justify-center font-black text-amber-400 text-lg shadow-inner">
                                                    <span x-text="(selectedEntry.character ? selectedEntry.character.name : 'UM').substring(0, 2).toUpperCase()"></span>
                                                </div>
                                            </template>
                                        </div>

                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-2xl font-black text-white" x-text="selectedEntry.character ? selectedEntry.character.name : ''"></h3>
                                                <template x-if="selectedEntry.character && selectedEntry.character.variant_title && selectedEntry.character.variant_title !== 'Original'">
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30"
                                                          x-text="selectedEntry.character.variant_title"></span>
                                                </template>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                <!-- Tier Rank Badge -->
                                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black shadow"
                                                      :class="{
                                                          'bg-gradient-to-tr from-rose-600 to-amber-500 text-white': selectedEntry.tier_rank === 'S+',
                                                          'bg-amber-500 text-slate-950': selectedEntry.tier_rank === 'S',
                                                          'bg-violet-600 text-white': selectedEntry.tier_rank === 'A',
                                                          'bg-sky-600 text-white': selectedEntry.tier_rank === 'B',
                                                          'bg-slate-700 text-slate-200': selectedEntry.tier_rank === 'C'
                                                      }"
                                                      x-text="'Tier ' + selectedEntry.tier_rank"></span>

                                                <!-- Role Badge -->
                                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold"
                                                      :class="{
                                                          'bg-rose-500/10 text-rose-400 border border-rose-500/30': selectedEntry.role === 'Ace',
                                                          'bg-blue-500/10 text-blue-400 border border-blue-500/30': selectedEntry.role === 'Parent',
                                                          'bg-fuchsia-500/10 text-fuchsia-400 border border-fuchsia-500/30': selectedEntry.role === 'Debuffer'
                                                      }"
                                                      x-text="selectedEntry.role"></span>

                                                <!-- Running Style Pill -->
                                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700"
                                                      x-text="selectedEntry.recommended_style"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Target Stats: Pro 5-Stat Visualizer (Speed / Stamina / Power / Guts / Wit) -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-extrabold uppercase tracking-widest text-amber-400 flex items-center space-x-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <span>Target Stat Distribution</span>
                                        </span>
                                        <span class="text-[10px] text-slate-500 uppercase font-mono">Benchmark Values</span>
                                    </div>

                                    <!-- If 5 stats are detected -->
                                    <template x-if="parseStats(selectedEntry.build_notes?.target_stats)">
                                        <div class="grid grid-cols-5 gap-2 bg-slate-950/80 border border-slate-800 rounded-2xl p-3 shadow-inner">
                                            <template x-for="stat in parseStats(selectedEntry.build_notes?.target_stats)" :key="stat.label">
                                                <div class="text-center p-2 rounded-xl border flex flex-col items-center justify-center" :class="stat.bg">
                                                    <span class="text-[10px] font-black tracking-wider uppercase" :class="stat.text" x-text="stat.label"></span>
                                                    <span class="text-xs sm:text-sm font-extrabold text-white font-mono mt-0.5 truncate" x-text="stat.val"></span>
                                                    <span class="text-[9px] text-slate-500 hidden sm:inline" x-text="stat.name"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback single stat string -->
                                    <template x-if="!parseStats(selectedEntry.build_notes?.target_stats)">
                                        <div class="bg-slate-950 border border-amber-500/30 rounded-2xl p-3.5 flex items-center justify-between shadow-inner">
                                            <div class="font-mono text-sm sm:text-base font-bold text-amber-300"
                                                 x-text="selectedEntry.build_notes?.target_stats || 'Not specified'"></div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Skills Recommendations Cloud -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Mandatory Skills -->
                                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 space-y-2.5">
                                        <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span>Mandatory Skills</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-if="selectedEntry.build_notes?.mandatory_skills && selectedEntry.build_notes.mandatory_skills.length > 0">
                                                <template x-for="skill in selectedEntry.build_notes.mandatory_skills" :key="skill">
                                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-800/90 text-slate-200 border border-slate-700/80 shadow-sm">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                        <span x-text="skill"></span>
                                                    </span>
                                                </template>
                                            </template>
                                            <template x-if="!selectedEntry.build_notes?.mandatory_skills || selectedEntry.build_notes.mandatory_skills.length === 0">
                                                <span class="text-xs text-slate-500 italic">None specified</span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Inherited Unique Skills -->
                                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 space-y-2.5">
                                        <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                            <span>Inherited Unique Skills</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-if="selectedEntry.build_notes?.inherited_uniques && selectedEntry.build_notes.inherited_uniques.length > 0">
                                                <template x-for="unique in selectedEntry.build_notes.inherited_uniques" :key="unique">
                                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-purple-950/40 text-purple-300 border border-purple-500/30 shadow-sm">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                                                        <span x-text="unique"></span>
                                                    </span>
                                                </template>
                                            </template>
                                            <template x-if="!selectedEntry.build_notes?.inherited_uniques || selectedEntry.build_notes.inherited_uniques.length === 0">
                                                <span class="text-xs text-slate-500 italic">None specified</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Meta Analysis Summary -->
                                <div class="space-y-1.5">
                                    <span class="text-xs font-extrabold uppercase tracking-widest text-slate-400 flex items-center space-x-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Meta Analysis & Strategy Summary</span>
                                    </span>
                                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 text-sm text-slate-300 leading-relaxed shadow-inner">
                                        <p x-text="selectedEntry.build_notes?.summary || 'No analysis notes available for this character entry.'"></p>
                                    </div>
                                </div>

                                <!-- Modal Footer Actions -->
                                <div class="pt-2 flex items-center justify-between border-t border-slate-800">
                                    <span class="text-[11px] text-slate-500">Press <kbd class="px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 font-mono text-slate-300 text-[10px]">Esc</kbd> to close</span>
                                    
                                    <button type="button" @click="closeModal()"
                                            class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition-colors">
                                        Close Window
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Meta Changelog Feed Section -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 backdrop-blur-xl">
                <div class="border-b border-slate-800 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-xl font-black text-white tracking-tight flex items-center space-x-2.5">
                            <span class="w-3 h-3 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.8)]"></span>
                            <span>Tournament Meta Changelogs</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Audit trail of recent balance shifts, tier promotions, and demotions for {{ $currentEvent->title }}.</p>
                    </div>
                    <span class="text-xs font-mono text-slate-500">{{ $changelogs->count() }} Updates</span>
                </div>

                @if ($changelogs->isEmpty())
                    <div class="py-8 text-center text-xs text-slate-500 italic">
                        No recent changelog records for this tournament.
                    </div>
                @else
                    <div class="divide-y divide-slate-800/80">
                        @foreach ($changelogs as $log)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/30 px-3 rounded-2xl transition-colors">
                                <div class="flex items-center space-x-3.5">
                                    <!-- Character Avatar / Monogram in Changelog -->
                                    @if ($log->character?->avatar_url)
                                        <img src="{{ $log->character->avatar_url }}" alt="{{ $log->character->name }}"
                                             class="w-10 h-10 rounded-xl object-cover bg-slate-950 border border-slate-700 flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-700 flex items-center justify-center font-bold text-xs text-amber-400 flex-shrink-0">
                                            {{ strtoupper(substr($log->character?->name ?? 'UM', 0, 2)) }}
                                        </div>
                                    @endif

                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2.5">
                                            <!-- Movement Pill -->
                                            <div class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-950 border border-slate-700">
                                                <span class="text-slate-400">{{ $log->previous_tier ?? 'New' }}</span>
                                                <span class="text-amber-400">&rarr;</span>
                                                <span class="text-amber-300 font-extrabold">{{ $log->new_tier }}</span>
                                            </div>

                                            <span class="font-extrabold text-sm text-white">
                                                {{ $log->character?->full_name ?? 'Character' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400">{{ $log->reason }}</p>
                                    </div>
                                </div>

                                <div class="text-xs text-slate-500 font-medium self-end sm:self-center flex-shrink-0">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        @else
            <!-- Empty state if no events in database -->
            <div class="py-24 text-center space-y-4">
                <div class="w-16 h-16 rounded-3xl bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-amber-400 font-bold text-2xl shadow-xl">
                    CS
                </div>
                <h2 class="text-2xl font-bold text-white">No Tournaments Configured</h2>
                <p class="text-sm text-slate-400 max-w-md mx-auto">Please initialize tournament events and tier entries through the Admin CMS or database seeder.</p>
                <div class="pt-4">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-amber-500 text-slate-950 hover:bg-amber-400 transition-colors shadow-lg shadow-amber-950/30">
                        Go to Admin CMS
                    </a>
                </div>
            </div>
        @endif

    </div>

    <!-- Public Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950/80 py-8 mt-12 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>
                <strong class="text-slate-300">ChevalSleep</strong> &bull; Global Server Uma Musume Competitive Tier List & Meta Guide
            </div>
            <div>
                Built for Champions Meeting (CM) & League of Heroes tournaments.
            </div>
        </div>
    </footer>

</body>
</html>
