@extends('layouts.admin')

@section('content')
<div x-data="{
    // Filter within workspace
    listSearch: '',
    
    // Add Form Live Preview State
    characters: {{ json_encode($characters) }},
    addCharacterId: '{{ old('character_id', '') }}',
    addTierRank: '{{ old('tier_rank', 'S') }}',
    addRole: '{{ old('role', 'Ace') }}',
    addStyle: '{{ old('recommended_style', 'Front Runner') }}',
    addStats: '{{ old('target_stats', '') }}',
    addSkills: '{{ old('mandatory_skills', '') }}',

    getSelectedCharacter() {
        if (!this.addCharacterId) return null;
        return this.characters.find(c => c.id == this.addCharacterId);
    },

    getSkillPills(text) {
        if (!text) return [];
        return text.split(',').map(s => s.trim()).filter(s => s.length > 0);
    },

    // Edit Modal State
    editModalOpen: false,
    editData: {
        id: null,
        character_name: '',
        tier_rank: 'S',
        role: 'Ace',
        recommended_style: 'Front Runner',
        sort_order: 0,
        target_stats: '',
        mandatory_skills: '',
        inherited_uniques: '',
        summary: '',
        changelog_reason: '',
        action_url: ''
    },
    openEdit(entry) {
        this.editData.id = entry.id;
        this.editData.character_name = entry.character ? (entry.character.name + (entry.character.variant_title && entry.character.variant_title !== 'Original' ? ' (' + entry.character.variant_title + ')' : '')) : 'Character';
        this.editData.tier_rank = entry.tier_rank;
        this.editData.role = entry.role;
        this.editData.recommended_style = entry.recommended_style;
        this.editData.sort_order = entry.sort_order || 0;
        
        let notes = entry.build_notes || {};
        this.editData.target_stats = notes.target_stats || '';
        this.editData.mandatory_skills = Array.isArray(notes.mandatory_skills) ? notes.mandatory_skills.join(', ') : (notes.mandatory_skills || '');
        this.editData.inherited_uniques = Array.isArray(notes.inherited_uniques) ? notes.inherited_uniques.join(', ') : (notes.inherited_uniques || '');
        this.editData.summary = notes.summary || '';
        this.editData.changelog_reason = '';
        this.editData.action_url = '{{ url('admin/events/' . $event->id . '/tiers') }}/' + entry.id;
        this.editModalOpen = true;
    }
}" class="space-y-6">

    <!-- Workspace Header & Meta Context -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-amber-400 mb-1">
                <span>Tier List Workspace</span>
                <span>&bull;</span>
                <span class="{{ $event->surface === 'Turf' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $event->surface }}</span>
                <span>&bull;</span>
                <span>{{ $event->distance_type }} ({{ $event->distance_meters }}m)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center space-x-3">
                <span>{{ $event->title }}</span>
                @if ($event->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                        Active
                    </span>
                @endif
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">Racecourse: <strong class="text-slate-200">{{ $event->track_name }}</strong></p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('home', ['event' => $event->slug]) }}" target="_blank"
               class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-slate-800/90 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-700/80 transition-all inline-flex items-center space-x-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>Preview Public Page</span>
            </a>
            <a href="{{ route('admin.events.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold text-slate-400 hover:text-white transition-colors">
                &larr; Back to Events
            </a>
        </div>
    </div>

    <!-- Main Workspace Grid: Tier List (Left) + Add Form with Live Preview (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Tier List Workspace (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Search Inside Tier List Workspace -->
            <div class="relative">
                <input type="text" x-model="listSearch"
                       placeholder="Filter racers in this workspace by name..."
                       class="w-full bg-slate-900 border border-slate-800 rounded-2xl pl-10 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            @php
                $tierColors = [
                    'S+' => 'bg-gradient-to-r from-rose-600 to-amber-500 text-white font-black',
                    'S'  => 'bg-amber-500 text-slate-950 font-black',
                    'A'  => 'bg-violet-600 text-white font-bold',
                    'B'  => 'bg-sky-600 text-white font-bold',
                    'C'  => 'bg-slate-700 text-slate-200 font-semibold',
                ];
            @endphp

            @foreach ($tierRanks as $rank)
                @php
                    $entries = $groupedEntries[$rank] ?? collect();
                @endphp
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-lg">
                    <!-- Tier Header -->
                    <div class="p-3.5 px-5 bg-slate-950/70 border-b border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-10 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm {{ $tierColors[$rank] ?? 'bg-slate-800' }}">
                                {{ $rank }}
                            </span>
                            <span class="text-xs uppercase font-black tracking-wider text-slate-300">
                                Tier {{ $rank }}
                            </span>
                        </div>
                        <span class="text-xs font-mono text-slate-500">
                            {{ $entries->count() }} {{ Str::plural('entry', $entries->count()) }}
                        </span>
                    </div>

                    <!-- Entries in this Tier -->
                    <div class="divide-y divide-slate-800/80">
                        @forelse ($entries as $entry)
                            <div x-show="listSearch === '' || '{{ strtolower($entry->character?->full_name ?? '') }}'.includes(listSearch.toLowerCase())"
                                 class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/40 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <!-- Avatar / Fallback -->
                                    @if ($entry->character?->avatar_url)
                                        <img src="{{ $entry->character->avatar_url }}" alt="{{ $entry->character->name }}"
                                             class="w-11 h-11 rounded-xl object-cover border border-slate-700 bg-slate-950 flex-shrink-0">
                                    @else
                                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-700 flex items-center justify-center font-black text-xs text-amber-400 flex-shrink-0">
                                            {{ strtoupper(substr($entry->character?->name ?? 'UM', 0, 2)) }}
                                        </div>
                                    @endif

                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-extrabold text-white text-sm">
                                                {{ $entry->character?->full_name ?? 'Unknown Character' }}
                                            </span>
                                            <span class="text-[10px] text-slate-500 font-mono">#{{ $entry->sort_order }}</span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <!-- Role Badge -->
                                            @php
                                                $roleClasses = [
                                                    'Ace' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                                    'Parent' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                                    'Debuffer' => 'bg-fuchsia-500/10 text-fuchsia-400 border-fuchsia-500/30',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $roleClasses[$entry->role] ?? 'bg-slate-800' }}">
                                                {{ $entry->role }}
                                            </span>

                                            <!-- Style Pill -->
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                                {{ $entry->recommended_style }}
                                            </span>

                                            @if (!empty($entry->build_notes['target_stats']))
                                                <span class="text-[10px] text-slate-400 font-mono hidden sm:inline truncate max-w-[180px]" title="Target Stats">
                                                    {{ $entry->build_notes['target_stats'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2 self-end sm:self-center flex-shrink-0">
                                    <button type="button"
                                            @click="openEdit({{ json_encode($entry) }})"
                                            class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.events.tiers.destroy', [$event, $entry]) }}" class="inline"
                                          onsubmit="return confirm('Remove this character from {{ $rank }} tier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1 rounded-xl text-rose-400 hover:text-rose-200 hover:bg-rose-950/40 transition-colors"
                                                title="Remove Entry">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-xs text-slate-500 italic">
                                No characters currently in {{ $rank }} tier.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right: Add Form with Live Card Preview (5 Cols - Sticky) -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            
            <!-- Live Card Preview -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-4 shadow-lg">
                <span class="block text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Live Public Preview</span>
                
                <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-700 flex items-center justify-center font-black text-amber-400 text-xs shadow-inner flex-shrink-0">
                        <span x-text="getSelectedCharacter() ? getSelectedCharacter().name.substring(0, 2).toUpperCase() : 'UM'"></span>
                    </div>

                    <div class="min-w-0 flex-1 space-y-1">
                        <div class="flex items-center space-x-1.5">
                            <span class="font-bold text-sm text-white truncate"
                                  x-text="getSelectedCharacter() ? getSelectedCharacter().name : 'Select a Character'"></span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30"
                                  x-text="'Tier ' + addTierRank"></span>
                        </div>
                        
                        <div class="flex items-center gap-1.5 text-[10px]">
                            <span class="px-1.5 py-0.5 rounded font-bold"
                                  :class="{
                                      'bg-rose-500/10 text-rose-400 border border-rose-500/30': addRole === 'Ace',
                                      'bg-blue-500/10 text-blue-400 border border-blue-500/30': addRole === 'Parent',
                                      'bg-fuchsia-500/10 text-fuchsia-400 border border-fuchsia-500/30': addRole === 'Debuffer'
                                  }"
                                  x-text="addRole"></span>
                            
                            <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700"
                                  x-text="addStyle"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Form Container -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-5">
                <div class="border-b border-slate-800 pb-3">
                    <h2 class="text-base font-black text-white flex items-center space-x-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Racer to Tier List</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Assign tier ranking, competitive role, and target stats.</p>
                </div>

                <form method="POST" action="{{ route('admin.events.tiers.store', $event) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="character_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                            Character <span class="text-rose-400">*</span>
                        </label>
                        <select id="character_id" name="character_id" required x-model="addCharacterId"
                                class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">-- Choose Character --</option>
                            @foreach ($characters as $character)
                                <option value="{{ $character->id }}">
                                    {{ $character->full_name }} ({{ $character->default_running_style }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="tier_rank" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Tier Rank <span class="text-rose-400">*</span>
                            </label>
                            <select id="tier_rank" name="tier_rank" required x-model="addTierRank"
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                @foreach ($tierRanks as $rank)
                                    <option value="{{ $rank }}">Tier {{ $rank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Role <span class="text-rose-400">*</span>
                            </label>
                            <select id="role" name="role" required x-model="addRole"
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="recommended_style" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Recommended Style <span class="text-rose-400">*</span>
                            </label>
                            <select id="recommended_style" name="recommended_style" required x-model="addStyle"
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                @foreach ($runningStyles as $style)
                                    <option value="{{ $style }}">{{ $style }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Sort Order
                            </label>
                            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>
                    </div>

                    <!-- Build Notes Inputs -->
                    <div class="pt-2 border-t border-slate-800 space-y-3">
                        <span class="block text-[11px] font-extrabold uppercase tracking-widest text-amber-400">
                            Build Notes & Recommendations
                        </span>

                        <div>
                            <label for="target_stats" class="block text-xs font-medium text-slate-300 mb-1">
                                Target Stats (Spd / Stm / Pwr / Guts / Wit)
                            </label>
                            <input type="text" id="target_stats" name="target_stats" x-model="addStats"
                                   placeholder="e.g. 1200 / 600+ / 1100+ / 450+ / 1000+"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500 font-mono">
                        </div>

                        <div>
                            <label for="mandatory_skills" class="block text-xs font-medium text-slate-300 mb-1">
                                Mandatory Skills <span class="text-slate-500 text-[10px]">(comma-separated)</span>
                            </label>
                            <input type="text" id="mandatory_skills" name="mandatory_skills" x-model="addSkills"
                                   placeholder="e.g. Groundwork, Escape Artist, Dirt Corner◯"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                            
                            <!-- Mini chip preview -->
                            <div class="flex flex-wrap gap-1 mt-1.5" x-show="getSkillPills(addSkills).length > 0">
                                <template x-for="skill in getSkillPills(addSkills)" :key="skill">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300 border border-slate-700" x-text="skill"></span>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label for="inherited_uniques" class="block text-xs font-medium text-slate-300 mb-1">
                                Inherited Unique Skills <span class="text-slate-500 text-[10px]">(comma-separated)</span>
                            </label>
                            <input type="text" id="inherited_uniques" name="inherited_uniques" value="{{ old('inherited_uniques') }}"
                                   placeholder="e.g. Angling x Scheming, Shooting for Victory!"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>

                        <div>
                            <label for="summary" class="block text-xs font-medium text-slate-300 mb-1">
                                Meta Analysis Summary
                            </label>
                            <textarea id="summary" name="summary" rows="3"
                                      placeholder="Summary of track viability, strengths, and role synergy..."
                                      class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">{{ old('summary') }}</textarea>
                        </div>
                    </div>

                    <!-- Changelog Recording -->
                    <div class="pt-2 border-t border-slate-800">
                        <label for="changelog_reason" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                            Changelog Reason <span class="text-slate-500 font-normal lowercase">(optional audit note)</span>
                        </label>
                        <input type="text" id="changelog_reason" name="changelog_reason" value="{{ old('changelog_reason') }}"
                               placeholder="e.g. Placed into S+ tier due to high acceleration consistency"
                               class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-950/30 transition-all">
                        + Add To Tier List
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Entry Modal (Alpine.js) -->
    <div x-show="editModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"
             @click="editModalOpen = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5 text-slate-200"
                 @keydown.escape.window="editModalOpen = false">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-lg font-black text-white">Edit Tier Entry</h3>
                        <p class="text-xs text-amber-400 font-bold" x-text="editData.character_name"></p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <form :action="editData.action_url" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Tier Rank <span class="text-rose-400">*</span>
                            </label>
                            <select name="tier_rank" x-model="editData.tier_rank" required
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                @foreach ($tierRanks as $rank)
                                    <option value="{{ $rank }}">Tier {{ $rank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Role <span class="text-rose-400">*</span>
                            </label>
                            <select name="role" x-model="editData.role" required
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Recommended Style <span class="text-rose-400">*</span>
                            </label>
                            <select name="recommended_style" x-model="editData.recommended_style" required
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                @foreach ($runningStyles as $style)
                                    <option value="{{ $style }}">{{ $style }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Sort Order
                            </label>
                            <input type="number" name="sort_order" x-model="editData.sort_order" min="0"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-800 space-y-3">
                        <span class="block text-[11px] font-extrabold uppercase tracking-widest text-amber-400">
                            Build Notes
                        </span>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Target Stats</label>
                            <input type="text" name="target_stats" x-model="editData.target_stats"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500 font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Mandatory Skills (comma-separated)</label>
                            <input type="text" name="mandatory_skills" x-model="editData.mandatory_skills"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Inherited Unique Skills (comma-separated)</label>
                            <input type="text" name="inherited_uniques" x-model="editData.inherited_uniques"
                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Summary</label>
                            <textarea name="summary" x-model="editData.summary" rows="3"
                                      class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-800">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                            Changelog Reason <span class="text-slate-500 font-normal lowercase">(recorded when tier changes)</span>
                        </label>
                        <input type="text" name="changelog_reason" x-model="editData.changelog_reason"
                               placeholder="e.g. Promoted to S+ after recent balance updates"
                               class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-3">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-2xl text-xs font-semibold text-slate-400 hover:text-white">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-950/30">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
