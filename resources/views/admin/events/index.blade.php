@extends('layouts.admin')

@section('page-title', 'Events & CM Guides')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Events &amp; CM Guides</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage Champions Meeting editions, meta guides, and tier data.</p>
        </div>

        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-900/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New CM Edition</span>
        </a>
    </div>

    <!-- Events Table -->
    <div class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-5 font-bold">CM Edition</th>
                        <th class="py-3.5 px-4 font-bold">Track & Conditions</th>
                        <th class="py-3.5 px-4 font-bold">Entries</th>
                        <th class="py-3.5 px-4 font-bold">Status</th>
                        <th class="py-3.5 px-5 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse ($events as $event)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    @if ($event->cm_number)
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/20 border border-amber-500/30 flex items-center justify-center font-black text-xs text-amber-600 dark:text-amber-400 flex-shrink-0">
                                            #{{ $event->cm_number }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $event->title }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">{{ $event->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $event->track_name }}</div>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-mono">
                                        {{ $event->distance_type }} &bull; {{ $event->distance_meters }}m
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold border {{ $event->surface === 'Turf' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/60' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800/60' }}">
                                        {{ $event->surface }}
                                    </span>
                                    @if ($event->season)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60">
                                            {{ $event->season }}
                                        </span>
                                    @endif
                                    @if ($event->track_condition)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            {{ $event->track_condition }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-black text-slate-900 dark:text-white font-mono text-lg">{{ $event->tier_entries_count }}</span>
                                <span class="text-xs text-slate-400 ml-1">umas</span>
                            </td>
                            <td class="py-4 px-4">
                                @if ($event->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                        LIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Archive
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end flex-wrap gap-2">
                                    @if (!$event->is_active)
                                        <form method="POST" action="{{ route('admin.events.activate', $event) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-xl text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700/60 transition-colors">
                                                Set Active
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.events.tiers.index', $event) }}"
                                       class="px-2.5 py-1.5 rounded-xl text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-700/60 transition-colors">
                                        Tiers
                                    </a>

                                    <a href="{{ route('admin.events.edit', $event) }}"
                                       class="px-2.5 py-1.5 rounded-xl text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors">
                                        Meta Guide
                                    </a>

                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline"
                                          onsubmit="return confirm('Delete this event and all associated tier entries?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-xl text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-sm text-slate-400 italic">
                                No CM editions created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
