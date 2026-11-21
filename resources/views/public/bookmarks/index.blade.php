@extends('layouts.public')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sail-200 dark:border-navy-700">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono font-bold uppercase tracking-wider text-cerulean-600 dark:text-cerulean-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <span>{{ __('ui.nav.my_bookmarks') }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-navy-950 dark:text-white tracking-tight">
                {{ __('ui.bookmarks.title') }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ __('ui.bookmarks.subtitle') }}
            </p>
        </div>

        <a href="{{ route('home') }}"
           class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-750 hover:bg-sail-200 dark:hover:bg-navy-700 text-slate-700 dark:text-slate-200 border border-sail-200 dark:border-navy-600 transition-colors">
            <span>&larr;</span>
            <span>{{ __('ui.bookmarks.browse') }}</span>
        </a>
    </div>

    @if ($events->isEmpty())
        <!-- Empty State -->
        <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-10 text-center space-y-4 shadow-sm">
            <div class="w-12 h-12 rounded-full bg-sail-100 dark:bg-navy-750 border border-sail-200 dark:border-navy-600 flex items-center justify-center mx-auto text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
            </div>
            <div class="max-w-md mx-auto space-y-1">
                <h3 class="text-sm font-bold text-navy-950 dark:text-white">No Bookmarks Saved</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('ui.bookmarks.empty') }}</p>
            </div>
            <a href="{{ route('home') }}"
               class="inline-flex items-center px-4 py-2 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm font-mono">
                {{ __('ui.bookmarks.browse') }}
            </a>
        </div>
    @else
        <!-- Bookmarked CM Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($events as $event)
                <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-4 space-y-3 shadow-sm hover:border-cerulean-500/50 transition-colors flex flex-col justify-between">
                    <div class="space-y-2.5">
                        <!-- Top Meta Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                @if ($event->cm_number)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-navy-900 dark:bg-navy-750 text-brass-400 border border-brass-500/30">
                                        #{{ $event->cm_number }}
                                    </span>
                                @endif
                                <span class="text-[10px] font-mono font-bold {{ $event->is_active ? 'text-emerald-500' : 'text-slate-400' }}">
                                    {{ $event->is_active ? __('ui.cm.live_now') : __('ui.cm.archive') }}
                                </span>
                            </div>

                            <!-- Remove Bookmark Form -->
                            <form method="POST" action="{{ route('cm.bookmark', $event) }}">
                                @csrf
                                <button type="submit"
                                        class="p-1 rounded text-brass-500 hover:text-rose-500 transition-colors"
                                        title="Remove from bookmarks">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Event Title & Track -->
                        <div>
                            <h2 class="text-sm font-bold text-navy-950 dark:text-white leading-snug">
                                <a href="{{ route('cm.show', $event) }}" class="hover:text-cerulean-600 dark:hover:text-brass-400 transition-colors">
                                    {{ $event->title }}
                                </a>
                            </h2>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                                {{ $event->track_name }}
                            </div>
                        </div>

                        <!-- Telemetry Badges Strip -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-1 text-[10px] font-mono">
                            <span class="px-2 py-0.5 rounded bg-sail-100 dark:bg-navy-700 text-slate-700 dark:text-slate-200 border border-sail-200 dark:border-navy-600">
                                {{ $event->distance_type }} &bull; {{ $event->distance_meters }}m
                            </span>
                            <span class="px-2 py-0.5 rounded border {{ $event->surface === 'Turf' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : 'bg-brass-500/10 text-brass-600 dark:text-brass-400 border-brass-500/30' }}">
                                {{ $event->surface }}
                            </span>
                            @if ($event->season)
                                <span class="px-2 py-0.5 rounded bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border border-cerulean-500/30">
                                    {{ $event->season }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Action Footer -->
                    <div class="pt-3 border-t border-sail-200 dark:border-navy-750 flex items-center justify-between">
                        <span class="text-[11px] font-mono text-slate-400">
                            {{ $event->tier_entries_count }} {{ __('ui.common.entries') }}
                        </span>
                        <a href="{{ route('cm.show', $event) }}"
                           class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-700 hover:bg-sail-200 dark:hover:bg-navy-600 text-navy-950 dark:text-sail-100 border border-sail-200 dark:border-navy-600 transition-colors">
                            <span>{{ __('ui.cm.view_guide') }}</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
