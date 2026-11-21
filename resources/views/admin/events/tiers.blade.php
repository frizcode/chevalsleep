@extends('layouts.admin')

@section('page-title', 'Tier Builder &bull; ' . $event->title)

@section('content')
<div x-data="{
    // Filter within workspace
    listSearch: '',
    
    // Add Form Live Preview State
    characters: {{ json_encode($characters) }},
    addCharacterId: '{{ old('character_id', '') }}',
    addTierRank: '{{ old('tier_rank', 'SS') }}',
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
        tier_rank: 'SS',
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
        this.editData.recommended_style = entry.recommended_style || entry.running_style || 'Front Runner';
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
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-sail-200 dark:border-navy-700">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-bold uppercase tracking-wider text-brass-500 mb-1 font-mono">
                <span>Tier List Workspace</span>
                <span>&bull;</span>
                <span class="{{ $event->surface === 'Turf' ? 'text-emerald-500' : 'text-brass-500' }}">{{ $event->surface }}</span>
                <span>&bull;</span>
                <span>{{ $event->distance_type }} ({{ $event->distance_meters }}m)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-navy-900 dark:text-sail-50 tracking-tight font-sans flex items-center space-x-3">
                <span>{{ $event->title }}</span>
                @if ($event->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold font-mono tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        LIVE
                    </span>
                @endif
            </h1>
            <p class="text-xs text-sail-500 dark:text-sail-400 mt-0.5">Racecourse: <strong class="text-navy-900 dark:text-sail-100">{{ $event->track_name }}</strong></p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('cm.show', $event) }}" target="_blank"
               class="px-3 py-1.5 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-700 hover:bg-sail-200 dark:hover:bg-navy-600 text-sail-700 dark:text-sail-200 border border-sail-200 dark:border-navy-600 transition-colors inline-flex items-center space-x-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5 text-brass-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>View Public Guide</span>
            </a>
            <a href="{{ route('admin.events.index') }}" class="px-3 py-1.5 rounded-md text-xs font-semibold text-sail-500 hover:text-navy-900 dark:hover:text-sail-100 transition-colors">
                &larr; Back to Events
            </a>
        </div>
    </div>

    <!-- Main Workspace Grid: Tier List (Left) + Add Form with Live Preview (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Tier List Workspace (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Search Inside Tier List Workspace -->
            <div class="relative">
                <input type="text" x-model="listSearch"
                       placeholder="Filter racers in this workspace by name..."
                       class="w-full bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md pl-9 pr-3.5 py-1.5 text-xs text-navy-900 dark:text-sail-100 placeholder-sail-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sail-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            @php
                $tierBadgeStyles = [
                    'SS' => 'bg-brass-500 text-navy-950 font-black font-mono',
                    'S+' => 'bg-brass-600 text-white font-black font-mono',
                    'S'  => 'bg-navy-700 text-brass-400 border border-brass-500/40 font-bold font-mono',
                    'A'  => 'bg-cerulean-600 text-white font-bold font-mono',
                    'B'  => 'bg-sail-200 dark:bg-navy-700 text-sail-700 dark:text-sail-300 font-semibold font-mono',
                    'C'  => 'bg-sail-100 dark:bg-navy-750 text-sail-500 dark:text-sail-400 font-medium font-mono',
                ];
            @endphp

            @foreach ($tierRanks as $rank)
                @php
                    $entries = $groupedEntries[$rank] ?? collect();
                @endphp
                <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg overflow-hidden shadow-sm">
                    <!-- Tier Header -->
                    <div class="p-2.5 px-4 bg-sail-50 dark:bg-navy-900/60 border-b border-sail-200 dark:border-navy-700 flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-8 h-6 rounded flex items-center justify-center text-xs {{ $tierBadgeStyles[$rank] ?? 'bg-navy-700 text-sail-200 font-mono' }}">
                                {{ $rank }}
                            </span>
                            <span class="text-xs uppercase font-bold tracking-wider text-navy-900 dark:text-sail-100 font-mono">
                                Tier {{ $rank }}
                            </span>
                        </div>
                        <span class="text-xs font-mono tabular-nums text-sail-400">
                            {{ $entries->count() }} {{ Str::plural('entry', $entries->count()) }}
                        </span>
                    </div>

                    <!-- Entries in this Tier -->
                    <div class="divide-y divide-sail-100 dark:divide-navy-750">
                        @forelse ($entries as $entry)
                            <div x-show="listSearch === '' || '{{ strtolower($entry->character?->full_name ?? '') }}'.includes(listSearch.toLowerCase())"
                                 class="p-3 px-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-sail-50/60 dark:hover:bg-navy-750/40 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <!-- Avatar / Fallback -->
                                    <div class="w-9 h-9 rounded-md overflow-hidden bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 flex items-center justify-center flex-shrink-0">
                                        @if ($entry->character?->avatar_url)
                                            <img src="{{ $entry->character->avatar_url }}" alt="{{ $entry->character->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <span class="font-bold text-brass-500 text-xs font-mono">
                                                {{ strtoupper(substr($entry->character?->name ?? 'UM', 0, 2)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-0.5">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-navy-900 dark:text-sail-100 text-xs">
                                                {{ $entry->character?->full_name ?? 'Unknown Character' }}
                                            </span>
                                            <span class="text-[10px] text-sail-400 font-mono">#{{ $entry->sort_order }}</span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <!-- Role Badge -->
                                            @php
                                                $roleClasses = [
                                                    'Ace' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30',
                                                    'Parent' => 'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30',
                                                    'Debuffer' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold border {{ $roleClasses[$entry->role] ?? 'bg-sail-100 dark:bg-navy-700' }}">
                                                {{ $entry->role }}
                                            </span>

                                            <!-- Style Pill -->
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-sail-100 dark:bg-navy-700 text-sail-600 dark:text-sail-300 border border-sail-200 dark:border-navy-600">
                                                {{ $entry->recommended_style ?? $entry->running_style }}
                                            </span>

                                            @if (!empty($entry->build_notes['target_stats']))
                                                <span class="text-[10px] text-sail-400 font-mono hidden sm:inline truncate max-w-[180px] tabular-nums" title="Target Stats">
                                                    {{ $entry->build_notes['target_stats'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-1.5 self-end sm:self-center flex-shrink-0">
                                    <button type="button"
                                            @click="openEdit({{ json_encode($entry) }})"
                                            class="px-2 py-1 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-700 hover:bg-sail-200 dark:hover:bg-navy-600 text-sail-700 dark:text-sail-200 border border-sail-200 dark:border-navy-600 transition-colors">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.events.tiers.destroy', [$event, $entry]) }}" class="inline"
                                          onsubmit="return confirm('Remove this character from {{ $rank }} tier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1 rounded-md text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 transition-colors"
                                                title="Remove Entry">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-xs text-sail-400 italic">
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
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-3.5 shadow-sm">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-sail-400 mb-2 font-mono">Live Public Preview</span>
                
                <div class="bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md p-3 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-md bg-white dark:bg-navy-950 border border-sail-200 dark:border-navy-700 flex items-center justify-center font-bold text-brass-500 text-xs flex-shrink-0 overflow-hidden font-mono">
                        <template x-if="getSelectedCharacter() && getSelectedCharacter().avatar_url">
                            <img :src="getSelectedCharacter().avatar_url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!getSelectedCharacter() || !getSelectedCharacter().avatar_url">
                            <span x-text="getSelectedCharacter() ? getSelectedCharacter().name.substring(0, 2).toUpperCase() : 'UM'"></span>
                        </template>
                    </div>

                    <div class="min-w-0 flex-1 space-y-0.5">
                        <div class="flex items-center space-x-1.5">
                            <span class="font-bold text-xs text-navy-900 dark:text-sail-100 truncate"
                                  x-text="getSelectedCharacter() ? getSelectedCharacter().name : 'Select a Character'"></span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold font-mono bg-brass-500/10 text-brass-600 dark:text-brass-400 border border-brass-500/30"
                                  x-text="'Tier ' + addTierRank"></span>
                        </div>
                        
                        <div class="flex items-center gap-1.5 text-[10px]">
                            <span class="px-1.5 py-0.2 rounded font-semibold border"
                                  :class="{
                                      'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30': addRole === 'Ace',
                                      'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30': addRole === 'Parent',
                                      'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30': addRole === 'Debuffer'
                                  }"
                                  x-text="addRole"></span>
                            
                            <span class="px-1.5 py-0.2 rounded bg-sail-100 dark:bg-navy-700 text-sail-600 dark:text-sail-300 border border-sail-200 dark:border-navy-600"
                                  x-text="addStyle"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Form Container -->
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-4 sm:p-5 shadow-sm space-y-4">
                <div class="border-b border-sail-200 dark:border-navy-750 pb-2.5">
                    <h2 class="text-sm font-bold text-navy-900 dark:text-sail-100 flex items-center space-x-1.5 font-sans">
                        <svg class="w-4 h-4 text-brass-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Racer to Tier List</span>
                    </h2>
                    <p class="text-[11px] text-sail-400 mt-0.5">Assign tier ranking, competitive role, and target stats.</p>
                </div>

                @php
                    $inp = 'w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3 py-1.5 text-xs text-navy-900 dark:text-sail-100 placeholder-sail-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors';
                @endphp

                <form method="POST" action="{{ route('admin.events.tiers.store', $event) }}" class="space-y-3">
                    @csrf

                    <div>
                        <label for="character_id" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                            Character <span class="text-rose-500">*</span>
                        </label>
                        <select id="character_id" name="character_id" required x-model="addCharacterId"
                                class="{{ $inp }}">
                            <option value="">-- Choose Character --</option>
                            @foreach ($characters as $character)
                                <option value="{{ $character->id }}">
                                    {{ $character->full_name }} ({{ $character->default_running_style }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label for="tier_rank" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Tier Rank <span class="text-rose-500">*</span>
                            </label>
                            <select id="tier_rank" name="tier_rank" required x-model="addTierRank"
                                    class="{{ $inp }}">
                                @foreach ($tierRanks as $rank)
                                    <option value="{{ $rank }}">Tier {{ $rank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Role <span class="text-rose-500">*</span>
                            </label>
                            <select id="role" name="role" required x-model="addRole"
                                    class="{{ $inp }}">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label for="recommended_style" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Recommended Style <span class="text-rose-500">*</span>
                            </label>
                            <select id="recommended_style" name="recommended_style" required x-model="addStyle"
                                    class="{{ $inp }}">
                                @foreach ($runningStyles as $style)
                                    <option value="{{ $style }}">{{ $style }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Sort Order
                            </label>
                            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   class="{{ $inp }} font-mono">
                        </div>
                    </div>

                    <!-- Build Notes Inputs -->
                    <div class="pt-2 border-t border-sail-200 dark:border-navy-750 space-y-2.5">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-brass-500 font-mono">
                            Build Notes & Recommendations
                        </span>

                        <div>
                            <label for="target_stats" class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">
                                Target Stats (Spd / Stm / Pwr / Guts / Wit)
                            </label>
                            <input type="text" id="target_stats" name="target_stats" x-model="addStats"
                                   placeholder="e.g. 1200 / 600+ / 1100+ / 450+ / 1000+"
                                   class="{{ $inp }} font-mono">
                        </div>

                        <div>
                            <label for="mandatory_skills" class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">
                                Mandatory Skills <span class="text-sail-400 text-[10px]">(comma-separated)</span>
                            </label>
                            <input type="text" id="mandatory_skills" name="mandatory_skills" x-model="addSkills"
                                   placeholder="e.g. Groundwork, Escape Artist, Dirt Corner◯"
                                   class="{{ $inp }}">
                            
                            <!-- Mini chip preview -->
                            <div class="flex flex-wrap gap-1 mt-1.5" x-show="getSkillPills(addSkills).length > 0">
                                <template x-for="skill in getSkillPills(addSkills)" :key="skill">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-sail-100 dark:bg-navy-700 text-sail-600 dark:text-sail-300 border border-sail-200 dark:border-navy-600" x-text="skill"></span>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label for="inherited_uniques" class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">
                                Inherited Unique Skills <span class="text-sail-400 text-[10px]">(comma-separated)</span>
                            </label>
                            <input type="text" id="inherited_uniques" name="inherited_uniques" value="{{ old('inherited_uniques') }}"
                                   placeholder="e.g. Angling x Scheming, Shooting for Victory!"
                                   class="{{ $inp }}">
                        </div>

                        <div>
                            <label for="summary" class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">
                                Meta Analysis Summary
                            </label>
                            <textarea id="summary" name="summary" rows="2"
                                      placeholder="Summary of track viability, strengths, and role synergy..."
                                      class="{{ $inp }}">{{ old('summary') }}</textarea>
                        </div>
                    </div>

                    <!-- Changelog Recording -->
                    <div class="pt-2 border-t border-sail-200 dark:border-navy-750">
                        <label for="changelog_reason" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                            Changelog Reason <span class="text-sail-400 font-normal lowercase">(optional audit note)</span>
                        </label>
                        <input type="text" id="changelog_reason" name="changelog_reason" value="{{ old('changelog_reason') }}"
                               placeholder="e.g. Placed into SS tier due to high acceleration consistency"
                               class="{{ $inp }}">
                    </div>

                    <button type="submit"
                            class="w-full py-2 rounded-md text-xs font-semibold uppercase tracking-wider bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm">
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
        <div class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm transition-opacity"
             @click="editModalOpen = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-xl bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-5 sm:p-6 shadow-2xl space-y-4 text-sail-700 dark:text-sail-200"
                 @keydown.escape.window="editModalOpen = false">
                
                <div class="flex items-center justify-between border-b border-sail-200 dark:border-navy-750 pb-2.5">
                    <div>
                        <h3 class="text-base font-bold text-navy-900 dark:text-sail-100 font-sans">Edit Tier Entry</h3>
                        <p class="text-xs text-brass-500 font-semibold" x-text="editData.character_name"></p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="text-sail-400 hover:text-navy-900 dark:hover:text-sail-100 text-lg font-bold">&times;</button>
                </div>

                <form :action="editData.action_url" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Tier Rank <span class="text-rose-500">*</span>
                            </label>
                            <select name="tier_rank" x-model="editData.tier_rank" required
                                    class="{{ $inp }}">
                                @foreach ($tierRanks as $rank)
                                    <option value="{{ $rank }}">Tier {{ $rank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Role <span class="text-rose-500">*</span>
                            </label>
                            <select name="role" x-model="editData.role" required
                                    class="{{ $inp }}">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Recommended Style <span class="text-rose-500">*</span>
                            </label>
                            <select name="recommended_style" x-model="editData.recommended_style" required
                                    class="{{ $inp }}">
                                @foreach ($runningStyles as $style)
                                    <option value="{{ $style }}">{{ $style }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                                Sort Order
                            </label>
                            <input type="number" name="sort_order" x-model="editData.sort_order" min="0"
                                   class="{{ $inp }} font-mono">
                        </div>
                    </div>

                    <div class="pt-2 border-t border-sail-200 dark:border-navy-750 space-y-2.5">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-brass-500 font-mono">
                            Build Notes
                        </span>

                        <div>
                            <label class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">Target Stats</label>
                            <input type="text" name="target_stats" x-model="editData.target_stats"
                                   class="{{ $inp }} font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">Mandatory Skills (comma-separated)</label>
                            <input type="text" name="mandatory_skills" x-model="editData.mandatory_skills"
                                   class="{{ $inp }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">Inherited Unique Skills (comma-separated)</label>
                            <input type="text" name="inherited_uniques" x-model="editData.inherited_uniques"
                                   class="{{ $inp }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-sail-600 dark:text-sail-300 mb-1">Summary</label>
                            <textarea name="summary" x-model="editData.summary" rows="2"
                                      class="{{ $inp }}"></textarea>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-sail-200 dark:border-navy-750">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1 font-mono">
                            Changelog Reason <span class="text-sail-400 font-normal lowercase">(recorded when tier changes)</span>
                        </label>
                        <input type="text" name="changelog_reason" x-model="editData.changelog_reason"
                               placeholder="e.g. Promoted to SS after recent balance updates"
                               class="{{ $inp }}">
                    </div>

                    <div class="pt-3 border-t border-sail-200 dark:border-navy-750 flex items-center justify-end space-x-2">
                        <button type="button" @click="editModalOpen = false" class="px-3 py-1.5 rounded-md text-xs font-semibold text-sail-500 hover:text-navy-900 dark:hover:text-sail-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-1.5 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
