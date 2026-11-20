@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Admin Dashboard</h1>
            <p class="text-sm text-slate-400 mt-1">Overview of active tournaments, character pool, and tier movements.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if ($activeEvent)
                <a href="{{ route('admin.events.tiers.index', $activeEvent) }}"
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-lg shadow-amber-950/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Manage Active Event Tiers</span>
                </a>
            @endif
            <a href="{{ route('admin.characters.create') }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Character</span>
            </a>
            <a href="{{ route('admin.events.create') }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Event</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Active Tournament -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/5 rounded-full blur-xl group-hover:bg-amber-500/10 transition-colors"></div>
            <div class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-2 flex items-center justify-between">
                <span>Active Tournament</span>
                <span class="inline-flex w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <div class="text-lg font-extrabold text-white truncate" title="{{ $activeEvent?->title ?? 'None' }}">
                {{ $activeEvent?->title ?? 'None Active' }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                @if ($activeEvent)
                    {{ $activeEvent->track_name }} &bull; {{ $activeEvent->surface }}
                @else
                    No active event selected
                @endif
            </div>
        </div>

        <!-- Card 2: Total Characters -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 relative overflow-hidden group">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Total Characters</div>
            <div class="text-3xl font-black text-white">{{ $totalCharacters }}</div>
            <div class="text-xs text-slate-500 mt-1">Available in database pool</div>
        </div>

        <!-- Card 3: Tier Entries in Active Event -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 relative overflow-hidden group">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Active Event Entries</div>
            <div class="text-3xl font-black text-amber-400">{{ $activeEventEntriesCount }}</div>
            <div class="text-xs text-slate-500 mt-1">Ranked racers in active meta</div>
        </div>

        <!-- Card 4: Total Changelogs -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 relative overflow-hidden group">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Total Meta Changelogs</div>
            <div class="text-3xl font-black text-white">{{ $totalChangelogs }}</div>
            <div class="text-xs text-slate-500 mt-1">Recorded tier adjustments</div>
        </div>
    </div>

    <!-- Recent Changelog Activity Section -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">Recent Meta Shifts & Changelogs</h2>
                <p class="text-xs text-slate-400 mt-0.5">Audit log of recent balance and tier placements.</p>
            </div>
        </div>

        @if ($recentChangelogs->isEmpty())
            <div class="p-8 text-center text-sm text-slate-500">
                No changelogs recorded yet.
            </div>
        @else
            <div class="divide-y divide-slate-800/80">
                @foreach ($recentChangelogs as $log)
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/30 transition-colors">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2 text-xs">
                                <span class="font-bold text-slate-300">{{ $log->event?->title ?? 'Tournament' }}</span>
                                <span class="text-slate-600">&bull;</span>
                                <span class="text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="font-bold text-sm text-white">
                                    {{ $log->character?->full_name ?? 'Character' }}
                                </div>
                                <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-800 border border-slate-700">
                                    <span class="text-slate-400">{{ $log->previous_tier ?? 'New' }}</span>
                                    <span class="text-amber-400">&rarr;</span>
                                    <span class="text-amber-300 font-extrabold">{{ $log->new_tier }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400">{{ $log->reason }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
