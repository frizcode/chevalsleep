@extends('layouts.public')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-4 text-center space-y-6">
    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-sky-100 dark:bg-sky-950/70 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60">
        <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
        <span>{{ $category ?? 'ChevalSleep Encyclopedia' }}</span>
    </div>

    <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
        {{ $title ?? 'Knowledge Base' }}
    </h1>

    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 max-w-xl mx-auto leading-relaxed">
        {{ $subtitle ?? 'This section is currently being curated with verified Global Server race data, support cards, and mechanics analysis.' }}
    </p>

    <div class="bg-white dark:bg-[#11192E] border border-sky-100 dark:border-sky-900/40 rounded-3xl p-8 shadow-xl max-w-md mx-auto space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-500 mx-auto flex items-center justify-center font-bold text-2xl border border-amber-500/30">
            ★
        </div>
        <div class="text-xs font-bold text-slate-700 dark:text-slate-300">
            Feature in Active Development
        </div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400">
            Our meta analysts and trainers are cataloging data for future release. In the meantime, explore our active Champions Meeting meta guides.
        </p>
        <div class="pt-2">
            <a href="{{ route('home') }}"
               class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-md shadow-sky-900/20 transition-all">
                <span>&larr; Return to Home</span>
            </a>
        </div>
    </div>
</div>
@endsection
