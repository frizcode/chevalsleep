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

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900/90 border-b border-slate-800/80 sticky top-0 z-40 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 via-rose-500 to-amber-600 flex items-center justify-center shadow-lg shadow-rose-950/50 text-slate-950 font-black text-xl group-hover:scale-105 transition-transform">
                    CS
                </div>
                <div>
                    <span class="font-black text-xl tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">ChevalSleep</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-amber-400 block -mt-1">Uma Musume Global Meta</span>
                </div>
            </a>

            <!-- Event Switcher & Admin Actions -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Tournament / Event Switcher Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-800/90 hover:bg-slate-800 text-slate-200 border border-slate-700 transition-colors shadow-sm">
                        <span class="w-2 h-2 rounded-full {{ $currentEvent?->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500' }}"></span>
                        <span class="max-w-[130px] sm:max-w-[180px] truncate">{{ $currentEvent?->title ?? 'Select Event' }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-64 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl py-1.5 z-50 overflow-hidden">
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            Available Tournaments
                        </div>
                        <div class="max-h-60 overflow-y-auto divide-y divide-slate-800/40">
                            @foreach ($events as $event)
                                <a href="{{ route('home', ['event' => $event->slug]) }}"
                                   class="block px-3 py-2 text-xs hover:bg-slate-800 transition-colors {{ $currentEvent?->id === $event->id ? 'bg-slate-800/80 text-amber-400 font-bold' : 'text-slate-300' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="truncate">{{ $event->title }}</span>
                                        @if ($event->is_active)
                                            <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Active</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">{{ $event->track_name }} &bull; {{ $event->surface }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Admin Link -->
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition-colors shadow-sm">
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

        resetFilters() {
            this.search = '';
            this.selectedStyle = 'All';
            this.selectedRole = 'All';
        },

        isFiltered() {
            return this.search !== '' || this.selectedStyle !== 'All' || this.selectedRole !== 'All';
        },

        filteredEntries(rank) {
            return this.entries.filter(entry => {
                if (entry.tier_rank !== rank) return false;

                if (this.selectedStyle !== 'All' && entry.recommended_style !== this.selectedStyle) {
                    return false;
                }

                if (this.selectedRole !== 'All' && entry.role !== this.selectedRole) {
                    return false;
                }

                if (this.search.trim() !== '') {
                    const q = this.search.toLowerCase();
                    const name = (entry.character?.name || '').toLowerCase();
                    const variant = (entry.character?.variant_title || '').toLowerCase();
                    const full = (entry.character?.full_name || '').toLowerCase();
                    if (!name.includes(q) && !variant.includes(q) && !full.includes(q)) {
                        return false;
                    }
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
        }
    }" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        @if ($currentEvent)
            <!-- Event Hero Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-slate-950 border border-slate-800 p-6 sm:p-8 shadow-2xl">
                <!-- Background decorative glow -->
                <div class="absolute -right-16 -top-16 w-80 h-80 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-40 -bottom-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-widest bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                Global Tournament Meta
                            </span>
                            @if ($currentEvent->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                    Current Meta Track
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                            {{ $currentEvent->title }}
                        </h1>

                        <p class="text-sm sm:text-base text-slate-400">
                            Track: <span class="text-slate-100 font-bold">{{ $currentEvent->track_name }}</span>
                        </p>
                    </div>

                    <!-- Track Specs Pills -->
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <div class="px-4 py-2.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm text-center">
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Distance Type</span>
                            <span class="text-sm font-extrabold text-white">{{ $currentEvent->distance_type }}</span>
                        </div>

                        <div class="px-4 py-2.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm text-center">
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Distance</span>
                            <span class="text-sm font-extrabold text-amber-400">{{ $currentEvent->distance_meters }}m</span>
                        </div>

                        <div class="px-4 py-2.5 rounded-2xl {{ $currentEvent->surface === 'Turf' ? 'bg-emerald-950/40 border-emerald-500/30 text-emerald-400' : 'bg-amber-950/40 border-amber-500/30 text-amber-400' }} border backdrop-blur-sm text-center">
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Surface</span>
                            <span class="text-sm font-extrabold">{{ $currentEvent->surface }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Filter Toolbar (Zero Page Reload via Alpine.js) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-5 shadow-lg space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="search"
                               placeholder="Search character or variant (e.g. Agnes, Alter, Falcon)..."
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-10 pr-4 py-2 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                        <button x-show="search.length > 0" @click="search = ''"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            &times;
                        </button>
                    </div>

                    <!-- Reset Filters Button -->
                    <div x-show="isFiltered()" x-cloak>
                        <button @click="resetFilters()"
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reset Filters</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-3 border-t border-slate-800/80">
                    <!-- Running Style Filter Pills -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Style:</span>
                        @foreach (['All', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'] as $style)
                            <button @click="selectedStyle = '{{ $style }}'"
                                    :class="selectedStyle === '{{ $style }}' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-800 border-slate-700'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium border transition-colors">
                                {{ $style }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Role Filter Pills -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Role:</span>
                        @foreach (['All', 'Ace', 'Parent', 'Debuffer'] as $role)
                            <button @click="selectedRole = '{{ $role }}'"
                                    :class="selectedRole === '{{ $role }}' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-800 border-slate-700'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium border transition-colors">
                                {{ $role }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tier List Board (S+, S, A, B, C) -->
            <div class="space-y-6">
                @php
                    $tierBadgeStyles = [
                        'S+' => 'bg-gradient-to-tr from-rose-600 via-rose-500 to-amber-500 text-white font-black shadow-rose-950/50',
                        'S'  => 'bg-amber-500 text-slate-950 font-black shadow-amber-950/40',
                        'A'  => 'bg-violet-600 text-white font-black shadow-violet-950/40',
                        'B'  => 'bg-sky-600 text-white font-black shadow-sky-950/40',
                        'C'  => 'bg-slate-700 text-slate-200 font-bold shadow-slate-950/40',
                    ];

                    $tierLabels = [
                        'S+' => 'Apex Meta / Must-Run',
                        'S'  => 'Premier Core Contenders',
                        'A'  => 'Strong Tournament Viable',
                        'B'  => 'Viable Niche & Counterpicks',
                        'C'  => 'Situational / Off-Meta',
                    ];
                @endphp

                <template x-for="rank in tierRanks" :key="rank">
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-lg transition-all"
                         x-show="filteredEntries(rank).length > 0 || !isFiltered()">
                        
                        <!-- Tier Header Bar -->
                        <div class="px-5 py-3.5 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-10 rounded-xl flex items-center justify-center text-lg shadow-md"
                                     :class="{
                                         'bg-gradient-to-tr from-rose-600 via-rose-500 to-amber-500 text-white font-black': rank === 'S+',
                                         'bg-amber-500 text-slate-950 font-black': rank === 'S',
                                         'bg-violet-600 text-white font-black': rank === 'A',
                                         'bg-sky-600 text-white font-black': rank === 'B',
                                         'bg-slate-700 text-slate-200 font-bold': rank === 'C'
                                     }">
                                    <span x-text="rank"></span>
                                </div>
                                <div>
                                    <h2 class="text-sm font-extrabold text-white tracking-wide uppercase" x-text="'Tier ' + rank"></h2>
                                    <span class="text-[11px] text-slate-400 font-medium"
                                          x-text="rank === 'S+' ? 'Apex Meta / Must-Run' : (rank === 'S' ? 'Premier Core Contenders' : (rank === 'A' ? 'Strong Tournament Viable' : (rank === 'B' ? 'Viable Niche & Counterpicks' : 'Situational / Off-Meta')))">
                                    </span>
                                </div>
                            </div>

                            <span class="text-xs font-mono text-slate-400 font-semibold"
                                  x-text="filteredEntries(rank).length + ' ' + (filteredEntries(rank).length === 1 ? 'racer' : 'racers')">
                            </span>
                        </div>

                        <!-- Racers Grid -->
                        <div class="p-4 sm:p-5">
                            <template x-if="filteredEntries(rank).length > 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    <template x-for="entry in filteredEntries(rank)" :key="entry.id">
                                        <div @click="openModal(entry)"
                                             class="group relative bg-slate-950/70 hover:bg-slate-800/80 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-3.5 flex items-center space-x-3.5 cursor-pointer transition-all duration-200 hover:scale-[1.02] hover:shadow-xl hover:shadow-amber-950/20">
                                            
                                            <!-- Character Avatar or Initials Fallback -->
                                            <div class="relative flex-shrink-0">
                                                <template x-if="entry.character && entry.character.avatar_url">
                                                    <img :src="entry.character.avatar_url" :alt="entry.character.name"
                                                         class="w-14 h-14 rounded-2xl object-cover bg-slate-900 border border-slate-700/80 group-hover:border-amber-500/40 transition-colors">
                                                </template>
                                                <template x-if="!entry.character || !entry.character.avatar_url">
                                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-700/80 flex items-center justify-center font-black text-amber-400 text-sm shadow-inner group-hover:border-amber-500/40 transition-colors">
                                                        <span x-text="(entry.character ? entry.character.name : 'UM').substring(0, 2).toUpperCase()"></span>
                                                    </div>
                                                </template>

                                                <!-- Rarity Badge overlay -->
                                                <div class="absolute -bottom-1 -right-1 bg-slate-950/90 border border-slate-800 rounded px-1 text-[9px] font-extrabold text-amber-400 leading-tight">
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
                                                    <!-- Role Badge -->
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold"
                                                          :class="{
                                                              'bg-rose-500/10 text-rose-400 border border-rose-500/30': entry.role === 'Ace',
                                                              'bg-blue-500/10 text-blue-400 border border-blue-500/30': entry.role === 'Parent',
                                                              'bg-fuchsia-500/10 text-fuchsia-400 border border-fuchsia-500/30': entry.role === 'Debuffer'
                                                          }"
                                                          x-text="entry.role">
                                                    </span>

                                                    <!-- Style Pill -->
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700/80"
                                                          x-text="entry.recommended_style">
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Arrow prompt on hover -->
                                            <div class="text-slate-600 group-hover:text-amber-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="filteredEntries(rank).length === 0">
                                <div class="py-6 text-center text-xs text-slate-500 italic">
                                    No entries matching current filters in Tier <span x-text="rank"></span>.
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
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"
                     @click="closeModal()"></div>

                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 text-slate-200"
                         @keydown.escape.window="closeModal()">
                        
                        <template x-if="selectedEntry">
                            <div class="space-y-6">
                                <!-- Modal Header -->
                                <div class="flex items-start justify-between border-b border-slate-800 pb-5">
                                    <div class="flex items-center space-x-4">
                                        <!-- Avatar -->
                                        <template x-if="selectedEntry.character && selectedEntry.character.avatar_url">
                                            <img :src="selectedEntry.character.avatar_url" :alt="selectedEntry.character.name"
                                                 class="w-16 h-16 rounded-2xl object-cover bg-slate-950 border border-slate-700">
                                        </template>
                                        <template x-if="!selectedEntry.character || !selectedEntry.character.avatar_url">
                                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-700 flex items-center justify-center font-black text-amber-400 text-lg shadow-inner">
                                                <span x-text="(selectedEntry.character ? selectedEntry.character.name : 'UM').substring(0, 2).toUpperCase()"></span>
                                            </div>
                                        </template>

                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-2xl font-black text-white" x-text="selectedEntry.character ? selectedEntry.character.name : ''"></h3>
                                                <template x-if="selectedEntry.character && selectedEntry.character.variant_title && selectedEntry.character.variant_title !== 'Original'">
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30"
                                                          x-text="selectedEntry.character.variant_title"></span>
                                                </template>
                                            </div>

                                            <div class="flex items-center space-x-2 mt-1.5">
                                                <!-- Tier Rank Badge -->
                                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black"
                                                      :class="{
                                                          'bg-gradient-to-tr from-rose-600 to-amber-500 text-white': selectedEntry.tier_rank === 'S+',
                                                          'bg-amber-500 text-slate-950': selectedEntry.tier_rank === 'S',
                                                          'bg-violet-600 text-white': selectedEntry.tier_rank === 'A',
                                                          'bg-sky-600 text-white': selectedEntry.tier_rank === 'B',
                                                          'bg-slate-700 text-slate-200': selectedEntry.tier_rank === 'C'
                                                      }"
                                                      x-text="'Tier ' + selectedEntry.tier_rank"></span>

                                                <!-- Role Badge -->
                                                <span class="px-2 py-0.5 rounded-lg text-xs font-bold"
                                                      :class="{
                                                          'bg-rose-500/10 text-rose-400 border border-rose-500/30': selectedEntry.role === 'Ace',
                                                          'bg-blue-500/10 text-blue-400 border border-blue-500/30': selectedEntry.role === 'Parent',
                                                          'bg-fuchsia-500/10 text-fuchsia-400 border border-fuchsia-500/30': selectedEntry.role === 'Debuffer'
                                                      }"
                                                      x-text="selectedEntry.role"></span>

                                                <!-- Running Style Pill -->
                                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700"
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

                                <!-- Target Stats Bar -->
                                <div class="space-y-1.5">
                                    <span class="text-xs font-extrabold uppercase tracking-widest text-amber-400">Target Stats (Spd / Stm / Pwr / Guts / Wit)</span>
                                    <div class="bg-slate-950 border border-amber-500/30 rounded-2xl p-4 flex items-center justify-between shadow-inner">
                                        <div class="font-mono text-base sm:text-lg font-bold text-amber-300"
                                             x-text="selectedEntry.build_notes?.target_stats || 'Not specified'"></div>
                                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Recommended Benchmark</span>
                                    </div>
                                </div>

                                <!-- Skills Recommendations -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Mandatory Skills -->
                                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 space-y-2">
                                        <div class="flex items-center space-x-1.5 text-xs font-bold uppercase tracking-wider text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span>Mandatory Skills</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                            <template x-if="selectedEntry.build_notes?.mandatory_skills && selectedEntry.build_notes.mandatory_skills.length > 0">
                                                <template x-for="skill in selectedEntry.build_notes.mandatory_skills" :key="skill">
                                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-800 text-slate-200 border border-slate-700"
                                                          x-text="skill"></span>
                                                </template>
                                            </template>
                                            <template x-if="!selectedEntry.build_notes?.mandatory_skills || selectedEntry.build_notes.mandatory_skills.length === 0">
                                                <span class="text-xs text-slate-500 italic">None specified</span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Inherited Unique Skills -->
                                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 space-y-2">
                                        <div class="flex items-center space-x-1.5 text-xs font-bold uppercase tracking-wider text-slate-300">
                                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                            <span>Inherited Unique Skills</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                            <template x-if="selectedEntry.build_notes?.inherited_uniques && selectedEntry.build_notes.inherited_uniques.length > 0">
                                                <template x-for="unique in selectedEntry.build_notes.inherited_uniques" :key="unique">
                                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-950/40 text-purple-300 border border-purple-500/30"
                                                          x-text="unique"></span>
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
                                    <span class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Meta Analysis & Strategy Summary</span>
                                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 text-sm text-slate-300 leading-relaxed">
                                        <p x-text="selectedEntry.build_notes?.summary || 'No analysis notes available for this character entry.'"></p>
                                    </div>
                                </div>

                                <!-- Close Button -->
                                <div class="pt-2 flex justify-end">
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
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black text-white tracking-tight flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Tournament Meta Changelogs</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Audit trail of recent balance changes, tier promotions, and demotions for {{ $currentEvent->title }}.</p>
                    </div>
                </div>

                @if ($changelogs->isEmpty())
                    <div class="py-8 text-center text-xs text-slate-500 italic">
                        No recent changelog records for this tournament.
                    </div>
                @else
                    <div class="divide-y divide-slate-800/80">
                        @foreach ($changelogs as $log)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/30 px-3 rounded-xl transition-colors">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-950 border border-slate-700">
                                            <span class="text-slate-400">{{ $log->previous_tier ?? 'New' }}</span>
                                            <span class="text-amber-400">&rarr;</span>
                                            <span class="text-amber-300 font-extrabold">{{ $log->new_tier }}</span>
                                        </div>

                                        <span class="font-extrabold text-sm text-white">
                                            {{ $log->character?->full_name ?? 'Character' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 pl-0.5">{{ $log->reason }}</p>
                                </div>

                                <div class="text-xs text-slate-500 font-medium self-end sm:self-center">
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
                <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-amber-400 font-bold text-2xl">
                    CS
                </div>
                <h2 class="text-2xl font-bold text-white">No Tournaments Configured</h2>
                <p class="text-sm text-slate-400 max-w-md mx-auto">Please initialize tournament events and tier entries through the Admin CMS or database seeder.</p>
                <div class="pt-4">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-amber-500 text-slate-950 hover:bg-amber-400 transition-colors">
                        Go to Admin CMS
                    </a>
                </div>
            </div>
        @endif

    </div>

    <!-- Public Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950/80 py-8 mt-12">
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
