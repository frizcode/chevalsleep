@extends('layouts.public')

@section('page-title', __('ui.cm.past_editions') . ' — ChevalSleep')

@section('content')
<div x-data="{
    events: {{ json_encode($events) }},
    search: '',
    filterDistance: 'All',
    filterSurface: 'All',
    filterStatus: 'All',

    filteredEvents() {
        return this.events.filter(event => {
            if (this.filterDistance !== 'All' && event.distance_type !== this.filterDistance) return false;
            if (this.filterSurface !== 'All' && event.surface !== this.filterSurface) return false;
            if (this.filterStatus === 'Live' && !event.is_active) return false;
            if (this.filterStatus === 'Archived' && event.is_active) return false;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                const title = (event.title || '').toLowerCase();
                const track = (event.track_name || '').toLowerCase();
                const cmNum = event.cm_number ? event.cm_number.toString() : '';
                if (!title.includes(q) && !track.includes(q) && !cmNum.includes(q)) return false;
            }
            return true;
        });
    },

    resetFilters() {
        this.search = '';
        this.filterDistance = 'All';
        this.filterSurface = 'All';
        this.filterStatus = 'All';
    }
}" class="space-y-6">

    <!-- Header & Hero Strip -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-sail-200 dark:border-navy-700">
            <div>
                <div class="flex items-center space-x-2 text-xs font-mono text-slate-400 mb-1">
                    <a href="{{ route('home') }}" class="hover:text-brass-500 transition-colors">&larr; {{ __('ui.nav.home') }}</a>
                    <span>/</span>
                    <span class="text-navy-950 dark:text-sail-100 font-bold uppercase">{{ __('ui.cm.past_editions') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-navy-950 dark:text-sail-50 tracking-tight font-sans">
                    {{ __('ui.cm.past_editions') }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Historical tournament tracks, past tier lists, training benchmarks, and winning racing strategies.
                </p>
            </div>

            <div class="flex items-center space-x-2 text-xs font-mono">
                <span class="px-2.5 py-1 rounded bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 text-slate-600 dark:text-slate-300">
                    Total: <strong class="text-brass-500" x-text="events.length"></strong> Editions
                </span>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-3 text-xs">
            <!-- Search -->
            <div class="md:col-span-5">
                <input type="text" x-model="search"
                       placeholder="{{ __('ui.cm.search_placeholder') }}"
                       class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-navy-950 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-brass-500 font-mono text-xs">
            </div>

            <!-- Distance Filter -->
            <div class="md:col-span-3">
                <select x-model="filterDistance"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-2 text-navy-950 dark:text-sail-100 focus:outline-none focus:border-brass-500 font-mono text-xs">
                    <option value="All">{{ __('ui.database.all_types') }} (Distance)</option>
                    @foreach ($distanceTypes as $dt)
                        @if ($dt !== 'All')
                            <option value="{{ $dt }}">{{ $dt }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Surface Filter -->
            <div class="md:col-span-2">
                <select x-model="filterSurface"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-2 text-navy-950 dark:text-sail-100 focus:outline-none focus:border-brass-500 font-mono text-xs">
                    <option value="All">All Surfaces</option>
                    @foreach ($surfaces as $sf)
                        @if ($sf !== 'All')
                            <option value="{{ $sf }}">{{ $sf }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="md:col-span-2">
                <select x-model="filterStatus"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-2 text-navy-950 dark:text-sail-100 focus:outline-none focus:border-brass-500 font-mono text-xs">
                    <option value="All">All Status</option>
                    <option value="Live">Live Active</option>
                    <option value="Archived">Archived</option>
                </select>
            </div>
        </div>
    </div>

    <!-- CM Archive Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="event in filteredEvents()" :key="event.id">
            <a :href="'/cm/' + event.slug"
               class="group bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 hover:border-brass-500/60 dark:hover:border-brass-500/60 rounded-lg p-5 flex flex-col justify-between transition-all duration-150 shadow-sm hover:shadow-md">
                
                <div class="space-y-3">
                    <!-- Top Strip -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-sail-200 dark:border-navy-700/60 text-[10px] font-mono">
                        <span class="font-bold text-brass-600 dark:text-brass-400" x-text="event.cm_number ? 'CM #' + event.cm_number : 'TOURNAMENT'"></span>
                        
                        <template x-if="event.is_active">
                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/30 uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>LIVE</span>
                            </span>
                        </template>
                        <template x-if="!event.is_active">
                            <span class="px-2 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-500 dark:text-slate-400 border border-sail-200 dark:border-navy-700 uppercase">
                                ARCHIVE
                            </span>
                        </template>
                    </div>

                    <!-- Title & Track -->
                    <div>
                        <h2 class="font-bold text-base text-navy-950 dark:text-sail-100 group-hover:text-brass-500 transition-colors truncate" x-text="event.title"></h2>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-mono" x-text="event.track_name"></div>
                    </div>

                    <!-- Telemetry Badges -->
                    <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-mono">
                        <span class="px-2 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-700 dark:text-slate-300 border border-sail-200 dark:border-navy-700 tabular-nums"
                              x-text="event.distance_type + ' ' + event.distance_meters + 'm'"></span>
                        <span class="px-2 py-0.5 rounded border font-bold"
                              :class="event.surface === 'Turf' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-brass-50 dark:bg-brass-950/30 text-brass-600 dark:text-brass-400 border-brass-200 dark:border-brass-800'"
                              x-text="event.surface"></span>
                        <span class="px-2 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-500 dark:text-slate-400 border border-sail-200 dark:border-navy-700"
                              x-text="event.season || 'Standard'"></span>
                        <template x-if="event.weather">
                            <span class="px-2 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-500 dark:text-slate-400 border border-sail-200 dark:border-navy-700"
                                  x-text="event.weather"></span>
                        </template>
                    </div>
                </div>

                <!-- Footer Strip -->
                <div class="mt-4 pt-3 border-t border-sail-200 dark:border-navy-700/60 flex items-center justify-between text-xs font-mono">
                    <span class="text-slate-400">
                        <strong class="text-navy-950 dark:text-sail-100" x-text="event.tier_entries_count || 0"></strong> {{ __('ui.cm.edition') }}
                    </span>
                    <span class="text-brass-500 group-hover:translate-x-0.5 transition-transform font-bold inline-flex items-center space-x-1">
                        <span>{{ __('ui.cm.view_guide') }}</span>
                        <span>&rarr;</span>
                    </span>
                </div>
            </a>
        </template>
    </div>

    <!-- Empty State -->
    <template x-if="filteredEvents().length === 0">
        <div class="p-12 text-center bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg space-y-3">
            <div class="w-10 h-10 rounded-full bg-sail-100 dark:bg-navy-750 border border-brass-500/30 text-brass-500 flex items-center justify-center mx-auto font-mono text-sm">
                !
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ __('ui.cm.no_events_found') }}</p>
            <button @click="resetFilters()" type="button" class="text-xs font-mono text-brass-500 hover:underline">
                Reset all filters
            </button>
        </div>
    </template>

</div>
@endsection
