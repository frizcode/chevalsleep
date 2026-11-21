@extends('layouts.public')

@section('page-title', __('ui.nav.profile') . ' — ChevalSleep')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Profile Header Banner -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <!-- Naval Crest Avatar / Initial -->
                <div class="w-12 h-12 rounded-lg bg-navy-900 border border-brass-500/50 flex items-center justify-center text-brass-400 font-mono font-black text-lg flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-xl sm:text-2xl font-black text-navy-950 dark:text-sail-50 font-sans tracking-tight">
                            {{ auth()->user()->name }}
                        </h1>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded {{ auth()->user()->isAdmin() ? 'bg-brass-500/15 text-brass-600 dark:text-brass-400 border border-brass-500/30' : 'bg-sail-100 dark:bg-navy-750 text-cerulean-600 dark:text-cerulean-400 border border-sail-200 dark:border-navy-700' }} uppercase">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                        {{ auth()->user()->email }} &bull; Member since {{ auth()->user()->created_at->format('M Y') }}
                    </p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="flex items-center space-x-2 text-xs font-mono">
                <a href="{{ route('bookmarks.index') }}" class="px-3 py-1.5 rounded bg-sail-100 dark:bg-navy-750 border border-sail-200 dark:border-navy-700 text-slate-700 dark:text-slate-200 hover:border-brass-500 transition-colors">
                    {{ __('ui.nav.my_bookmarks') }}
                </a>
            </div>
        </div>
    </div>

    <!-- 1. Profile Information Panel -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-6 sm:p-8 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- 2. Security / Password Update Panel -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-6 sm:p-8 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- 3. Danger Zone Panel -->
    <div class="bg-white dark:bg-navy-800 border border-rose-200 dark:border-rose-900/40 rounded-lg p-6 sm:p-8 shadow-sm">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
