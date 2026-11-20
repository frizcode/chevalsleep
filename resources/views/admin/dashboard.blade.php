@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-7">

    <!-- Hero Banner: Active CM -->
    @if ($activeEvent)
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-900 via-[#0A192F] to-slate-900 dark:from-[#0A192F] dark:via-[#060C18] dark:to-slate-900 border border-sky-700/40 dark:border-sky-900/60 p-7 shadow-2xl">
        <div class="absolute -right-16 -top-16 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 bottom-0 w-64 h-64 bg-sky-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if ($activeEvent->cm_number)
                        <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/40">CM #{{ $activeEvent->cm_number }}</span>
                    @endif
                    <span class="inline-flex items-center text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                        LIVE — Active Champions Meeting
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $activeEvent->title }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span class="text-xs px-2.5 py-1 rounded-xl bg-white/10 text-slate-200 font-semibold">{{ $activeEvent->track_name }}</span>
                    <span class="text-xs px-2.5 py-1 rounded-xl bg-white/10 text-slate-200 font-semibold">{{ $activeEvent->distance_type }} &bull; {{ $activeEvent->distance_meters }}m</span>
                    <span class="text-xs px-2.5 py-1 rounded-xl {{ $activeEvent->surface === 'Turf' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }} font-bold">{{ $activeEvent->surface }}</span>
                    @if($activeEvent->season) <span class="text-xs px-2.5 py-1 rounded-xl bg-white/10 text-slate-200 font-semibold">{{ $activeEvent->season }}</span> @endif
                    @if($activeEvent->weather) <span class="text-xs px-2.5 py-1 rounded-xl bg-white/10 text-slate-200 font-semibold">{{ $activeEvent->weather }}</span> @endif
                    @if($activeEvent->track_condition) <span class="text-xs px-2.5 py-1 rounded-xl bg-amber-500/20 text-amber-300 font-bold">{{ $activeEvent->track_condition }}</span> @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.events.tiers.index', $activeEvent) }}"
                   class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl text-xs font-extrabold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-900/40 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Manage Tier List</span>
                </a>
                <a href="{{ route('admin.events.edit', $activeEvent) }}"
                   class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit CM Meta Guide</span>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- 4 KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- KPI 1: Active CM Ranked Umas -->
        <div class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/5 dark:bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/10 dark:group-hover:bg-amber-500/15 transition-all"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400">Active Ranked Umas</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/15 dark:bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $activeEventEntriesCount }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-500 mt-1">In active CM tier list</div>
        </div>

        <!-- KPI 2: Total Characters -->
        <div class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl p-5 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-sky-600 dark:text-sky-400">Uma Database</span>
                <div class="w-8 h-8 rounded-xl bg-sky-500/15 dark:bg-sky-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $totalCharacters }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-500 mt-1 flex items-center justify-between">
                <span>Total registered racers</span>
                <a href="{{ route('admin.characters.index') }}" class="text-sky-500 font-bold hover:text-sky-600 dark:hover:text-sky-300 text-[10px]">View &rarr;</a>
            </div>
        </div>

        <!-- KPI 3: Total CM Editions -->
        <div class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl p-5 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">CM Editions</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/15 dark:bg-emerald-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $totalEvents }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-500 mt-1 flex items-center justify-between">
                <span>All time tournaments</span>
                <a href="{{ route('admin.events.index') }}" class="text-emerald-500 font-bold hover:text-emerald-600 dark:hover:text-emerald-300 text-[10px]">View &rarr;</a>
            </div>
        </div>

        <!-- KPI 4: Total Meta Changelogs -->
        <div class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl p-5 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-600 dark:text-purple-400">Meta Changelogs</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/15 dark:bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $totalChangelogs }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-500 mt-1">Recorded tier adjustments</div>
        </div>
    </div>

    <!-- Two-Column Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <!-- Left: Tier Distribution + Quick Actions -->
        <div class="lg:col-span-2 space-y-5">
            @if ($activeEvent)
            <div class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Active CM Tier Distribution</h2>

                @php
                    $tierColors = [
                        'SS' => 'bg-gradient-to-r from-amber-500 to-rose-500',
                        'S+' => 'bg-amber-500',
                        'S'  => 'bg-sky-500',
                        'A'  => 'bg-emerald-500',
                        'B'  => 'bg-indigo-500',
                        'C'  => 'bg-slate-400',
                    ];
                    $tierTextColors = [
                        'SS' => 'text-amber-600 dark:text-amber-400',
                        'S+' => 'text-amber-600 dark:text-amber-400',
                        'S'  => 'text-sky-600 dark:text-sky-400',
                        'A'  => 'text-emerald-600 dark:text-emerald-400',
                        'B'  => 'text-indigo-600 dark:text-indigo-400',
                        'C'  => 'text-slate-500 dark:text-slate-400',
                    ];
                    $totalActive = $activeEventEntriesCount ?: 1;
                @endphp

                @foreach (['SS', 'S+', 'S', 'A', 'B', 'C'] as $tier)
                    @php
                        $cnt = \App\Models\TierEntry::where('event_id', $activeEvent->id)->where('tier_rank', $tier)->count();
                        $pct = $totalActive > 0 ? round(($cnt / $totalActive) * 100) : 0;
                    @endphp
                    <div class="flex items-center space-x-3">
                        <span class="w-7 text-xs font-black {{ $tierTextColors[$tier] ?? 'text-slate-400' }} text-right">{{ $tier }}</span>
                        <div class="flex-1 bg-slate-100 dark:bg-slate-900/60 rounded-full h-2.5 overflow-hidden">
                            <div class="{{ $tierColors[$tier] ?? 'bg-slate-500' }} h-full rounded-full transition-all duration-500"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400 w-5 text-right">{{ $cnt }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl p-5 shadow-sm space-y-2">
                <h2 class="text-sm font-black text-slate-900 dark:text-white mb-3">Quick Actions</h2>
                <a href="{{ route('admin.events.create') }}"
                   class="flex items-center space-x-3 p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors group">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/15 dark:bg-sky-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">New CM Edition</div>
                        <div class="text-[10px] text-slate-400">Create a new Champions Meeting event</div>
                    </div>
                </a>
                <a href="{{ route('admin.characters.create') }}"
                   class="flex items-center space-x-3 p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors group">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/15 dark:bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Add Uma Character</div>
                        <div class="text-[10px] text-slate-400">Register a new racer to the database</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Right: Recent Meta Changelogs Feed -->
        <div class="lg:col-span-3 bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white">Recent Meta Shifts</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Latest tier adjustments and balance notes</p>
                </div>
            </div>

            @if ($recentChangelogs->isEmpty())
                <div class="p-10 text-center text-xs text-slate-400 italic">No changelogs recorded yet.</div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach ($recentChangelogs as $log)
                        <div class="p-4 flex items-center space-x-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <div class="w-9 h-9 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-center font-bold text-xs text-amber-500 flex-shrink-0">
                                @if ($log->character?->avatar_url)
                                    <img src="{{ $log->character->avatar_url }}" alt="{{ $log->character->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($log->character?->name ?? 'UM', 0, 2)) }}
                                @endif
                            </div>

                            <div class="flex-1 min-w-0 space-y-0.5">
                                <div class="flex items-center space-x-2 text-xs">
                                    <span class="font-bold text-slate-900 dark:text-white truncate">{{ $log->character?->full_name ?? 'Character' }}</span>
                                    <div class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex-shrink-0">
                                        <span class="text-slate-400">{{ $log->previous_tier ?? 'New' }}</span>
                                        <span class="text-amber-500">&rarr;</span>
                                        <span class="text-amber-600 dark:text-amber-400 font-extrabold">{{ $log->new_tier }}</span>
                                    </div>
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ $log->event?->title ?? 'Tournament' }} &bull; {{ $log->created_at->diffForHumans() }}
                                </div>
                                @if ($log->reason)
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $log->reason }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
