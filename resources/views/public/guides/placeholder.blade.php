@extends('layouts.public')

@section('content')
<div class="max-w-xl mx-auto py-16 px-4 text-center space-y-5">
    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-widest bg-sail-100 dark:bg-navy-750 text-slate-600 dark:text-brass-400 border border-sail-200 dark:border-navy-700">
        <span class="w-1.5 h-1.5 rounded-full bg-brass-400 animate-pulse"></span>
        <span>{{ $category ?? 'COMPENDIUM' }}</span>
    </div>

    <h1 class="font-sans text-2xl sm:text-4xl font-bold text-navy-950 dark:text-white tracking-tight">
        {{ $title ?? 'Knowledge Base' }}
    </h1>

    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
        {{ $subtitle ?? 'This section is currently being curated with verified Global Server race data, support cards, and mechanics analysis.' }}
    </p>

    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-6 max-w-sm mx-auto space-y-3 shadow-sm text-center">
        <div class="w-10 h-10 rounded-md bg-navy-900 border border-brass-500/40 text-brass-400 mx-auto flex items-center justify-center font-sans text-lg font-bold">
            ⚓
        </div>
        <div class="text-xs font-mono font-bold uppercase text-navy-950 dark:text-slate-200">
            Dossier Under Compilation
        </div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-sans">
            Data matrices are currently being verified against live racing telemetry. In the meantime, explore our active Champions Meeting meta guides.
        </p>
        <div class="pt-2">
            <a href="{{ route('home') }}"
               class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-md text-xs font-mono font-bold uppercase bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors">
                <span>&larr; RETURN TO META</span>
            </a>
        </div>
    </div>
</div>
@endsection
