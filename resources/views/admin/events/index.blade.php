@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Tournaments & Events</h1>
            <p class="text-sm text-slate-400 mt-1">Manage Champions Meeting (CM) and League of Heroes tournament tracks.</p>
        </div>

        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-950/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create New Event</span>
        </a>
    </div>

    <!-- Events Table Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Event Title</th>
                        <th class="py-3.5 px-4 font-semibold">Track Specs</th>
                        <th class="py-3.5 px-4 font-semibold">Tier Entries</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($events as $event)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4">
                                <div class="font-bold text-white text-base">{{ $event->title }}</div>
                                <div class="text-xs font-mono text-slate-400">{{ $event->slug }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-medium text-slate-200">{{ $event->track_name }}</div>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ $event->distance_type }} ({{ $event->distance_meters }}m)
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $event->surface === 'Turf' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30' }}">
                                        {{ $event->surface }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-white">{{ $event->tier_entries_count }}</span>
                                <span class="text-xs text-slate-500">entries</span>
                            </td>
                            <td class="py-4 px-4">
                                @if ($event->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                        Active Default
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-400 border border-slate-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if (! $event->is_active)
                                        <form method="POST" action="{{ route('admin.events.activate', $event) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-950/80 hover:bg-emerald-900 text-emerald-300 border border-emerald-700/50 transition-colors"
                                                    title="Set as active event shown on homepage">
                                                Set Active
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.events.tiers.index', $event) }}"
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition-colors">
                                        Manage Tiers
                                    </a>

                                    <a href="{{ route('admin.events.edit', $event) }}"
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                                       title="Edit Event">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this event and all its tier entries?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-lg text-rose-400 hover:text-rose-200 hover:bg-rose-950/40 transition-colors"
                                                title="Delete Event">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                No events found. Click "Create New Event" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($events->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
