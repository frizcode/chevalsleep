@extends('layouts.admin')

@section('page-title', 'Skills Master Database')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-navy-950 dark:text-sail-50 tracking-tight">Skills Master Database</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage racing skills, GameTora icons, rarity tiers, and activation triggers.</p>
        </div>

        <a href="{{ route('admin.skills.create') }}"
           class="inline-flex items-center space-x-2 px-4 py-2 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm font-mono">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Skill</span>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <form method="GET" action="{{ route('admin.skills.index') }}"
          class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-3.5 shadow-sm space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Search Bar -->
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search skill name or trigger condition..."
                       class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md pl-3 pr-3 py-1.5 text-xs text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
            </div>

            <!-- Rarity Filter -->
            <div>
                <select name="rarity" onchange="this.form.submit()"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-1.5 text-xs text-navy-900 dark:text-sail-100 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500">
                    <option value="All">All Rarities</option>
                    @foreach ($rarities as $r)
                        <option value="{{ $r }}" {{ request('rarity') === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <select name="type" onchange="this.form.submit()"
                        class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-1.5 text-xs text-navy-900 dark:text-sail-100 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500">
                    <option value="All">All Types</option>
                    @foreach ($skillTypes as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Skills Table -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-sail-50 dark:bg-navy-900/60 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-sail-200 dark:border-navy-700 font-mono">
                    <tr>
                        <th class="py-3 px-5 font-bold">Skill</th>
                        <th class="py-3 px-4 font-bold">Rarity</th>
                        <th class="py-3 px-4 font-bold">Type</th>
                        <th class="py-3 px-4 font-bold">Conditions</th>
                        <th class="py-3 px-5 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sail-100 dark:divide-navy-750">
                    @forelse ($skills as $skill)
                        <tr class="hover:bg-sail-50/60 dark:hover:bg-navy-750/40 transition-colors">
                            <td class="py-3 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-md overflow-hidden bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 flex items-center justify-center flex-shrink-0">
                                        @if ($skill->icon_url)
                                            <img src="{{ $skill->icon_url }}" alt="{{ $skill->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xs font-bold text-brass-500 font-mono">SK</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-navy-950 dark:text-sail-100 text-xs">{{ $skill->name }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">{{ $skill->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold border
                                    {{ $skill->rarity === 'Unique' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30' : '' }}
                                    {{ $skill->rarity === 'Rare' ? 'bg-brass-500/15 text-brass-600 dark:text-brass-400 border-brass-500/40' : '' }}
                                    {{ $skill->rarity === 'Inherited' ? 'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30' : '' }}
                                    {{ $skill->rarity === 'Normal' ? 'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-300 border-sail-200 dark:border-navy-600' : '' }}">
                                    {{ $skill->rarity }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold border
                                    {{ $skill->skill_type === 'Acceleration' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30' : '' }}
                                    {{ $skill->skill_type === 'Velocity' ? 'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30' : '' }}
                                    {{ $skill->skill_type === 'Recovery' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : '' }}
                                    {{ $skill->skill_type === 'Debuff' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30' : '' }}
                                    {{ $skill->skill_type === 'Passive' ? 'bg-brass-500/10 text-brass-600 dark:text-brass-400 border-brass-500/30' : '' }}">
                                    {{ $skill->skill_type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs font-mono text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $skill->activation_condition }}">
                                {{ $skill->activation_condition ?? '—' }}
                            </td>
                            <td class="py-3 px-5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('admin.skills.edit', $skill) }}"
                                       class="px-2.5 py-1 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-700 hover:bg-sail-200 dark:hover:bg-navy-600 text-slate-700 dark:text-slate-200 border border-sail-200 dark:border-navy-600 transition-colors font-mono">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete skill {{ $skill->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-md text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 italic">No skills registered in database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($skills->hasPages())
            <div class="p-4 border-t border-sail-200 dark:border-navy-700">
                {{ $skills->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
