@extends('layouts.admin')

@section('page-title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Header Breadcrumb / Label -->
    <div class="flex items-center space-x-2 text-xs font-mono font-bold uppercase tracking-wider text-slate-400">
        <span>Admin Dashboard</span>
        <span>&bull;</span>
        <span>Telemetry Command Center</span>
    </div>

    <!-- Hero Banner: Active CM -->
    @if ($activeEvent)
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-sail-200 dark:border-navy-700">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2 font-mono text-[10px]">
                    @if ($activeEvent->cm_number)
                        <span class="px-2 py-0.5 rounded bg-brass-500/15 text-brass-600 dark:text-brass-400 font-bold border border-brass-500/30">
                            CM #{{ $activeEvent->cm_number }}
                        </span>
                    @endif
                    <span class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>LIVE CHAMPIONS MEETING</span>
                    </span>
                </div>
                <h1 class="font-black text-xl sm:text-2xl text-navy-950 dark:text-white">{{ $activeEvent->title }}</h1>
                <div class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                    {{ $activeEvent->track_name }} &bull; {{ $activeEvent->distance_meters }}m ({{ $activeEvent->distance_type }}) &bull; {{ $activeEvent->surface }}
                </div>
            </div>

            <div class="flex items-center gap-2 font-mono text-xs">
                <a href="{{ route('admin.events.tiers.index', $activeEvent) }}"
                   class="px-3.5 py-2 rounded bg-brass-500 hover:bg-brass-400 text-navy-950 font-bold transition-colors">
                    Manage Tier List &rarr;
                </a>
                <a href="{{ route('admin.events.edit', $activeEvent) }}"
                   class="px-3.5 py-2 rounded bg-sail-100 dark:bg-navy-750 hover:bg-sail-200 text-slate-700 dark:text-slate-200 border border-sail-200 dark:border-navy-700 font-semibold transition-colors">
                    Edit Guide
                </a>
            </div>
        </div>

        <!-- Telemetry Strip -->
        <div class="mt-3 grid grid-cols-2 sm:grid-cols-6 divide-y sm:divide-y-0 sm:divide-x divide-sail-200 dark:divide-navy-700 bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded text-center text-xs">
            <div class="p-2">
                <span class="block text-[9px] font-mono uppercase text-slate-400">COURSE</span>
                <span class="font-mono font-bold text-navy-950 dark:text-white">{{ $activeEvent->track_name }}</span>
            </div>
            <div class="p-2">
                <span class="block text-[9px] font-mono uppercase text-slate-400">DISTANCE</span>
                <span class="font-mono font-bold tabular-nums text-navy-950 dark:text-white">{{ $activeEvent->distance_meters }}m</span>
            </div>
            <div class="p-2">
                <span class="block text-[9px] font-mono uppercase text-slate-400">SURFACE</span>
                <span class="font-mono font-bold {{ $activeEvent->surface === 'Turf' ? 'text-emerald-500' : 'text-brass-500' }}">{{ $activeEvent->surface }}</span>
            </div>
            <div class="p-2">
                <span class="block text-[9px] font-mono uppercase text-slate-400">SEASON</span>
                <span class="font-mono font-bold text-navy-950 dark:text-white">{{ $activeEvent->season }}</span>
            </div>
            <div class="p-2">
                <span class="block text-[9px] font-mono uppercase text-slate-400">WEATHER</span>
                <span class="font-mono font-bold text-navy-950 dark:text-white">{{ $activeEvent->weather }}</span>
            </div>
            <div class="p-2">
                <span class="block text-[9px] font-mono uppercase text-slate-400">CONDITION</span>
                <span class="font-mono font-bold text-brass-500">{{ $activeEvent->track_condition }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- 4 KPI Telemetry Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- KPI 1 -->
        <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-4 shadow-sm">
            <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-brass-600 dark:text-brass-400">Active Ranked Umas</div>
            <div class="text-2xl font-bold font-mono text-navy-950 dark:text-white tabular-nums mt-1">{{ $activeEventEntriesCount }}</div>
            <div class="text-[11px] font-mono text-slate-400 mt-1">In active CM tier roster</div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-cerulean-600 dark:text-cerulean-400">Total Characters</span>
                <a href="{{ route('admin.characters.index') }}" class="text-[10px] font-mono text-slate-400 hover:text-navy-950 dark:hover:text-white">View &rarr;</a>
            </div>
            <div class="text-2xl font-bold font-mono text-navy-950 dark:text-white tabular-nums mt-1">{{ $totalCharacters }}</div>
            <div class="text-[11px] font-mono text-slate-400 mt-1">Registered racers</div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">CM Editions</span>
                <a href="{{ route('admin.events.index') }}" class="text-[10px] font-mono text-slate-400 hover:text-navy-950 dark:hover:text-white">View &rarr;</a>
            </div>
            <div class="text-2xl font-bold font-mono text-navy-950 dark:text-white tabular-nums mt-1">{{ $totalEvents }}</div>
            <div class="text-[11px] font-mono text-slate-400 mt-1">All time events</div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-4 shadow-sm">
            <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Meta Changelogs</div>
            <div class="text-2xl font-bold font-mono text-navy-950 dark:text-white tabular-nums mt-1">{{ $totalChangelogs }}</div>
            <div class="text-[11px] font-mono text-slate-400 mt-1">Audited tier adjustments</div>
        </div>
    </div>

    <!-- Workspace Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <!-- Left: Tier Distribution & Quick Actions -->
        <div class="lg:col-span-2 space-y-4">
            @if ($activeEvent)
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-4 space-y-3 font-mono text-xs">
                <div class="font-bold text-sm text-navy-950 dark:text-white">ACTIVE TIER DISTRIBUTION</div>

                @php
                    $tierColors = [
                        'SS' => 'bg-brass-500',
                        'S+' => 'bg-rose-600',
                        'S'  => 'bg-cerulean-500',
                        'A'  => 'bg-emerald-600',
                        'B'  => 'bg-slate-500',
                        'C'  => 'bg-slate-400',
                    ];
                    $totalActive = $activeEventEntriesCount ?: 1;
                @endphp

                @foreach (['SS', 'S+', 'S', 'A', 'B', 'C'] as $tier)
                    @php
                        $cnt = \App\Models\TierEntry::where('event_id', $activeEvent->id)->where('tier_rank', $tier)->count();
                        $pct = $totalActive > 0 ? round(($cnt / $totalActive) * 100) : 0;
                    @endphp
                    <div class="flex items-center space-x-2.5">
                        <span class="w-6 font-bold text-right">{{ $tier }}</span>
                        <div class="flex-1 bg-sail-100 dark:bg-navy-900 rounded-sm h-2 overflow-hidden border border-sail-200 dark:border-navy-700">
                            <div class="{{ $tierColors[$tier] ?? 'bg-slate-500' }} h-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-6 text-right tabular-nums text-slate-400">{{ $cnt }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-3.5 space-y-2 font-mono text-xs">
                <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-2">QUICK OPERATIONS</span>
                <a href="{{ route('admin.events.create') }}"
                   class="flex items-center justify-between p-2 rounded bg-sail-50 dark:bg-navy-750 hover:bg-sail-100 dark:hover:bg-navy-700 text-navy-950 dark:text-slate-200 border border-sail-200 dark:border-navy-700/60 transition-colors">
                    <span>+ New CM Edition</span>
                    <span class="text-brass-500">&rarr;</span>
                </a>
                <a href="{{ route('admin.characters.create') }}"
                   class="flex items-center justify-between p-2 rounded bg-sail-50 dark:bg-navy-750 hover:bg-sail-100 dark:hover:bg-navy-700 text-navy-950 dark:text-slate-200 border border-sail-200 dark:border-navy-700/60 transition-colors">
                    <span>+ Register Uma Character</span>
                    <span class="text-brass-500">&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Right: Recent Changelogs -->
        <div class="lg:col-span-3 bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md overflow-hidden font-mono text-xs">
            <div class="p-3.5 border-b border-sail-200 dark:border-navy-700 flex items-center justify-between">
                <span class="font-bold text-sm text-navy-950 dark:text-white">RECENT TIER MOVEMENTS</span>
                <span class="text-[10px] text-slate-400 uppercase">Audit Trail</span>
            </div>

            @if ($recentChangelogs->isEmpty())
                <div class="p-8 text-center text-slate-400 italic">No changelogs recorded yet.</div>
            @else
                <div class="divide-y divide-sail-100 dark:divide-navy-750">
                    @foreach ($recentChangelogs as $log)
                        <div class="p-3 flex items-center space-x-3 hover:bg-sail-50 dark:hover:bg-navy-750/50 transition-colors">
                            <div class="w-8 h-8 rounded bg-navy-950 border border-slate-700 flex items-center justify-center font-bold text-[10px] text-brass-400 flex-shrink-0 overflow-hidden">
                                @if ($log->character?->avatar_url)
                                    <img src="{{ $log->character->avatar_url }}" alt="{{ $log->character->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($log->character?->name ?? 'UM', 0, 2)) }}
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-navy-950 dark:text-white truncate">{{ $log->character?->full_name ?? 'Character' }}</span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] bg-sail-100 dark:bg-navy-750 border border-sail-200 dark:border-navy-700 text-slate-600 dark:text-slate-300">
                                        {{ $log->previous_tier ?? 'New' }} &rarr; <strong class="text-brass-500">{{ $log->new_tier }}</strong>
                                    </span>
                                </div>
                                @if ($log->reason)
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $log->reason }}</div>
                                @endif
                            </div>

                            <div class="text-[10px] text-slate-400 flex-shrink-0 tabular-nums">
                                {{ $log->created_at->format('M d') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
