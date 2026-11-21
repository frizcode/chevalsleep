@extends('layouts.admin')

@section('page-title', 'Events & CM Guides')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-navy-900 dark:text-sail-50 tracking-tight font-sans">Events &amp; CM Guides</h1>
            <p class="text-xs text-sail-500 dark:text-sail-400 mt-1">Manage Champions Meeting editions, meta guides, and tier data.</p>
        </div>

        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center space-x-2 px-4 py-2 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New CM Edition</span>
        </a>
    </div>

    <!-- Events Table -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-sail-50 dark:bg-navy-900/60 text-[11px] uppercase tracking-wider text-sail-500 dark:text-sail-400 border-b border-sail-200 dark:border-navy-700">
                    <tr>
                        <th class="py-3.5 px-5 font-bold">CM Edition</th>
                        <th class="py-3.5 px-4 font-bold">Track & Conditions</th>
                        <th class="py-3.5 px-4 font-bold">Entries</th>
                        <th class="py-3.5 px-4 font-bold">Status</th>
                        <th class="py-3.5 px-5 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sail-100 dark:divide-navy-750">
                    @forelse ($events as $event)
                        <tr class="hover:bg-sail-50/60 dark:hover:bg-navy-750/40 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    @if ($event->cm_number)
                                        <div class="w-8 h-8 rounded-md bg-brass-500/10 border border-brass-500/30 flex items-center justify-center font-bold text-xs text-brass-500 font-mono flex-shrink-0">
                                            #{{ $event->cm_number }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-navy-900 dark:text-sail-100 text-sm">{{ $event->title }}</div>
                                        <div class="text-[10px] font-mono text-sail-400 dark:text-sail-500">{{ $event->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-navy-800 dark:text-sail-200 text-sm">{{ $event->track_name }}</div>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold bg-sail-100 dark:bg-navy-700 text-sail-700 dark:text-sail-300 border border-sail-200 dark:border-navy-600 font-mono">
                                        {{ $event->distance_type }} &bull; {{ $event->distance_meters }}m
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold border {{ $event->surface === 'Turf' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : 'bg-brass-500/10 text-brass-600 dark:text-brass-400 border-brass-500/30' }}">
                                        {{ $event->surface }}
                                    </span>
                                    @if ($event->season)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border border-cerulean-500/30 font-mono">
                                            {{ $event->season }}
                                        </span>
                                    @endif
                                    @if ($event->track_condition)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold bg-sail-100 dark:bg-navy-700 text-sail-600 dark:text-sail-400 border border-sail-200 dark:border-navy-600 font-mono">
                                            {{ $event->track_condition }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-navy-900 dark:text-sail-100 font-mono text-base tabular-nums">{{ $event->tier_entries_count }}</span>
                                <span class="text-xs text-sail-400 ml-1">umas</span>
                            </td>
                            <td class="py-4 px-4">
                                @if ($event->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold font-mono tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                        LIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-semibold font-mono bg-sail-100 dark:bg-navy-750 text-sail-500 dark:text-sail-400 border border-sail-200 dark:border-navy-700">
                                        ARCHIVE
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end flex-wrap gap-2">
                                    @if (!$event->is_active)
                                        <form method="POST" action="{{ route('admin.events.activate', $event) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-md text-[10px] font-semibold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 transition-colors">
                                                Set Active
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.events.tiers.index', $event) }}"
                                       class="px-2.5 py-1.5 rounded-md text-[10px] font-semibold bg-brass-500/10 hover:bg-brass-500/20 text-brass-600 dark:text-brass-400 border border-brass-500/30 transition-colors">
                                        Tiers
                                    </a>

                                    <a href="{{ route('admin.events.edit', $event) }}"
                                       class="px-2.5 py-1.5 rounded-md text-[10px] font-semibold bg-sail-100 dark:bg-navy-700 hover:bg-sail-200 dark:hover:bg-navy-600 text-sail-700 dark:text-sail-300 border border-sail-200 dark:border-navy-600 transition-colors">
                                        Meta Guide
                                    </a>

                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline"
                                          onsubmit="return confirm('Delete this event and all associated tier entries?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-md text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 transition-colors">
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
                            <td colspan="5" class="py-10 text-center text-sm text-sail-400 dark:text-sail-500 italic">
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
