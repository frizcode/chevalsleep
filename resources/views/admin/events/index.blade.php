@extends('layouts.admin')

@section('page-title', 'Champions Meeting Management')

@section('content')
<div class="space-y-6">

    <!-- Header & Metric Cards Strip -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-navy-950 dark:text-sail-50 tracking-tight font-sans">
                    Champions Meeting Command Center
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Manage tournament editions, track telemetry, 7-section meta guides, and live active protocols.
                </p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.events.create') }}"
                   class="inline-flex items-center space-x-2 px-4 py-2 rounded-md text-xs font-bold font-mono uppercase tracking-wider bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ New CM Edition</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold">Total CM Editions</span>
                    <div class="text-2xl font-black font-mono text-navy-950 dark:text-sail-100 tabular-nums">{{ $totalEvents }}</div>
                </div>
                <div class="w-10 h-10 rounded-md bg-sail-100 dark:bg-navy-750 flex items-center justify-center text-slate-500 dark:text-slate-400 font-mono text-sm font-bold">
                    #
                </div>
            </div>

            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Active Protocol</span>
                    </span>
                    <div class="text-base font-bold text-navy-950 dark:text-sail-100 truncate max-w-[200px] mt-0.5 font-sans">
                        {{ $activeEvent ? $activeEvent->title : 'No active CM' }}
                    </div>
                </div>
                @if ($activeEvent)
                    <a href="{{ route('cm.show', $activeEvent) }}" target="_blank"
                       class="text-[11px] font-mono text-brass-500 hover:text-brass-400 font-bold">
                        View ↗
                    </a>
                @endif
            </div>

            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold">Ranked Umas in Meta</span>
                    <div class="text-2xl font-black font-mono text-brass-500 tabular-nums">{{ $totalRankedUmas }}</div>
                </div>
                <div class="w-10 h-10 rounded-md bg-brass-500/10 border border-brass-500/30 flex items-center justify-center text-brass-500 font-mono text-sm font-bold">
                    ★
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.events.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <!-- Search Input -->
            <div class="sm:col-span-5">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search CM title, track, edition #, or slug..."
                       class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-brass-500 font-mono text-xs">
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
                <select name="status"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-2 text-navy-900 dark:text-sail-100 focus:outline-none focus:border-brass-500 font-mono text-xs">
                    <option value="All" {{ request('status') === 'All' ? 'selected' : '' }}>All Status</option>
                    <option value="Live" {{ request('status') === 'Live' ? 'selected' : '' }}>Live Active</option>
                    <option value="Archived" {{ request('status') === 'Archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <!-- Distance Filter -->
            <div class="sm:col-span-2">
                <select name="distance"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-2 text-navy-900 dark:text-sail-100 focus:outline-none focus:border-brass-500 font-mono text-xs">
                    <option value="All" {{ request('distance') === 'All' ? 'selected' : '' }}>All Distances</option>
                    @foreach ($distanceTypes as $dt)
                        <option value="{{ $dt }}" {{ request('distance') === $dt ? 'selected' : '' }}>{{ $dt }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Surface Filter -->
            <div class="sm:col-span-2">
                <select name="surface"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-2 text-navy-900 dark:text-sail-100 focus:outline-none focus:border-brass-500 font-mono text-xs">
                    <option value="All" {{ request('surface') === 'All' ? 'selected' : '' }}>All Surfaces</option>
                    @foreach ($surfaces as $sf)
                        <option value="{{ $sf }}" {{ request('surface') === $sf ? 'selected' : '' }}>{{ $sf }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit / Reset -->
            <div class="sm:col-span-1 flex items-center space-x-1">
                <button type="submit"
                        class="w-full h-full py-2 bg-sail-100 dark:bg-navy-700 hover:bg-sail-200 dark:hover:bg-navy-600 text-navy-950 dark:text-sail-100 rounded-md font-mono font-bold transition-colors text-xs text-center">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Events Table -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-sail-50 dark:bg-navy-900/60 text-[10px] uppercase font-mono tracking-wider text-slate-400 border-b border-sail-200 dark:border-navy-700">
                    <tr>
                        <th class="py-3 px-4 font-bold">Status</th>
                        <th class="py-3 px-4 font-bold">CM Edition</th>
                        <th class="py-3 px-4 font-bold">Race Telemetry</th>
                        <th class="py-3 px-3 font-bold text-center">Umas</th>
                        <th class="py-3 px-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sail-100 dark:divide-navy-750">
                    @forelse ($events as $event)
                        <tr class="hover:bg-sail-50/60 dark:hover:bg-navy-750/40 transition-colors">
                            <!-- Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if ($event->is_active)
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>LIVE</span>
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono text-slate-400 bg-sail-100 dark:bg-navy-750 border border-sail-200 dark:border-navy-700 uppercase">
                                        ARCHIVE
                                    </span>
                                @endif
                            </td>

                            <!-- CM Title & Edition -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 flex items-center justify-center font-mono font-bold text-xs text-brass-500 flex-shrink-0">
                                        {{ $event->cm_number ? '#' . $event->cm_number : 'CM' }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-navy-950 dark:text-sail-100 text-sm font-sans flex items-center space-x-1.5">
                                            <span>{{ $event->title }}</span>
                                            <a href="{{ route('cm.show', $event) }}" target="_blank" class="text-slate-400 hover:text-brass-500 transition-colors" title="Open Public Guide">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                            </a>
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $event->slug }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Track & Telemetry -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-navy-900 dark:text-sail-200 font-mono">{{ $event->track_name }}</div>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1 font-mono text-[10px]">
                                    <span class="px-1.5 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-700 dark:text-slate-300 border border-sail-200 dark:border-navy-700 tabular-nums">
                                        {{ $event->distance_type }} {{ $event->distance_meters }}m
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded border font-bold {{ $event->surface === 'Turf' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : 'bg-brass-500/10 text-brass-600 dark:text-brass-400 border-brass-500/30' }}">
                                        {{ $event->surface }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded bg-sail-100 dark:bg-navy-750 text-slate-500 dark:text-slate-400 border border-sail-200 dark:border-navy-700">
                                        {{ $event->season ?? 'Winter' }} &bull; {{ $event->track_condition ?? 'Firm' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Ranked Entries -->
                            <td class="py-3.5 px-3 text-center">
                                <span class="font-mono font-bold text-sm text-navy-950 dark:text-sail-100 tabular-nums">{{ $event->tier_entries_count }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-1.5 font-mono text-[10px]">
                                    <!-- Toggle Live / Archive -->
                                    @if (!$event->is_active)
                                        <form method="POST" action="{{ route('admin.events.activate', $event) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-bold transition-colors"
                                                    title="Set as currently active live CM">
                                                Set Live
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.events.deactivate', $event) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded bg-sail-100 dark:bg-navy-750 hover:bg-sail-200 text-slate-600 dark:text-slate-300 border border-sail-200 dark:border-navy-700 font-semibold transition-colors"
                                                    title="Demote to archived edition">
                                                Archive
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tier List Builder -->
                                    <a href="{{ route('admin.events.tiers.index', $event) }}"
                                       class="px-2.5 py-1 rounded bg-brass-500/15 hover:bg-brass-500/25 text-brass-600 dark:text-brass-400 border border-brass-500/30 font-bold transition-colors">
                                        Manage Tiers
                                    </a>

                                    <!-- Edit Guide -->
                                    <a href="{{ route('admin.events.edit', $event) }}"
                                       class="px-2.5 py-1 rounded bg-sail-100 dark:bg-navy-750 hover:bg-sail-200 text-slate-700 dark:text-slate-300 border border-sail-200 dark:border-navy-700 font-semibold transition-colors">
                                        Edit
                                    </a>

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline"
                                          onsubmit="return confirm('Delete tournament {{ $event->title }} and all its tier entries?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1 rounded text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 transition-colors"
                                                title="Delete event">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-slate-400 font-mono italic">
                                No Champions Meeting editions found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($events->hasPages())
            <div class="p-3 border-t border-sail-200 dark:border-navy-700">
                {{ $events->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
