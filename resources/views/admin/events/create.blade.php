@php
/* Shared form macro for create & edit CM Meta Guide */
$styles = ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'];
$isEdit = isset($event);
$formAction = $isEdit ? route('admin.events.update', $event) : route('admin.events.store');
@endphp
@extends('layouts.admin')

@section('page-title', isset($event) ? 'Edit CM Meta Guide' : 'New CM Edition')

@section('content')
<div x-data="{
    activeTab: 'basics',
    tabs: ['basics', 'conditions', 'stats', 'inheritance', 'skills', 'decks', 'teams'],

    /* Supoka Decks repeater */
    decks: {{ json_encode(old('decks', $event->supoka_decks ?? [['archetype'=>'','cards':[['name'=>'','icon'=>'']], 'notes'=>'']])) }},

    addDeck() {
        this.decks.push({ archetype: '', cards: [{ name: '', icon: '' }], notes: '' });
    },
    removeDeck(i) {
        this.decks.splice(i, 1);
    },
    addCard(di) {
        this.decks[di].cards.push({ name: '', icon: '' });
    },
    removeCard(di, ci) {
        this.decks[di].cards.splice(ci, 1);
    },

    /* Team Compositions repeater */
    teams: {{ json_encode(old('teams', $event->team_compositions ?? [['name'=>'','lineup'=>['','',''],'strategy'=>'']])) }},

    addTeam() {
        this.teams.push({ name: '', lineup: ['', '', ''], strategy: '' });
    },
    removeTeam(i) {
        this.teams.splice(i, 1);
    },

    /* Skill Inheritance per style */
    inheritance: {{ json_encode(old('inheritance', $event->skill_inheritance ?? ['Front Runner'=>[], 'Pace Chaser'=>[], 'Late Surger'=>[], 'End Closer'=>[]])) }},

    addInheritSkill(style) {
        if (!this.inheritance[style]) this.inheritance[style] = [];
        this.inheritance[style].push({ name: '', icon: '' });
    },
    removeInheritSkill(style, i) {
        this.inheritance[style].splice(i, 1);
    },

    /* Recommended Skills per style */
    recSkills: {{ json_encode(old('rec_skills', $event->recommended_skills ?? ['Front Runner'=>['core_accel'=>[],'velocity_other'=>[]], 'Pace Chaser'=>['core_accel'=>[],'velocity_other'=>[]], 'Late Surger'=>['core_accel'=>[],'velocity_other'=>[]], 'End Closer'=>['core_accel'=>[],'velocity_other'=>[]]])) }},

    addRecSkill(style, group) {
        if (!this.recSkills[style]) this.recSkills[style] = { core_accel: [], velocity_other: [] };
        this.recSkills[style][group].push({ name: '', icon: '' });
    },
    removeRecSkill(style, group, i) {
        this.recSkills[style][group].splice(i, 1);
    }
}" class="max-w-5xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ isset($event) ? 'Edit CM Meta Guide: ' . $event->title : 'Create New CM Edition' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Complete the 7-section meta guide for Champions Meeting.</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors">
            &larr; Back to Events
        </a>
    </div>

    <!-- Tab Switcher -->
    <div class="flex flex-wrap gap-1.5 bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-1.5">
        @php
            $tabLabels = [
                'basics'      => '1. Basics',
                'conditions'  => '2. Track Conditions',
                'stats'       => '3. Stat Benchmarks',
                'inheritance' => '4. Skill Inheritance',
                'skills'      => '5. Recommended Skills',
                'decks'       => '6. Supoka Decks',
                'teams'       => '7. Team Comps',
            ];
        @endphp
        @foreach ($tabLabels as $tab => $label)
            <button type="button" @click="activeTab = '{{ $tab }}'"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="activeTab === '{{ $tab }}'
                        ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm border border-slate-200 dark:border-slate-700'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <form method="POST" action="{{ $formAction }}" id="cm-meta-form"
          class="bg-white dark:bg-[#0E1726] border border-slate-200 dark:border-slate-800/80 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        @csrf
        @if(isset($event)) @method('PUT') @endif

        @php $inp = 'w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700/80 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:border-sky-500 dark:focus:border-amber-500 focus:ring-2 focus:ring-sky-500/20 dark:focus:ring-amber-500/20'; @endphp

        <!-- ===================== TAB 1: BASICS ===================== -->
        <div x-show="activeTab === 'basics'" class="space-y-5">
            <h2 class="text-sm font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Basic CM Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Event Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" required placeholder="e.g. CM #22 Aquarius Cup" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Slug <span class="text-slate-400 font-normal">(auto-generated)</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $event->slug ?? '') }}" placeholder="e.g. cm-22-aquarius-cup" class="{{ $inp }}">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">CM Number</label>
                    <input type="number" name="cm_number" value="{{ old('cm_number', $event->cm_number ?? '') }}" min="1" max="999" placeholder="22" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Track Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="track_name" value="{{ old('track_name', $event->track_name ?? '') }}" required placeholder="e.g. Tokyo 1600m" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Event Date</label>
                    <input type="date" name="event_date" value="{{ old('event_date', isset($event->event_date) ? $event->event_date->format('Y-m-d') : '') }}" class="{{ $inp }}">
                </div>
            </div>

            <div class="pt-2 flex items-center space-x-3">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $event->is_active ?? false) ? 'checked' : '' }}
                       class="w-4 h-4 rounded bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-amber-500 focus:ring-amber-500">
                <label for="is_active" class="text-xs font-bold text-slate-800 dark:text-slate-200 cursor-pointer">Set as Active (Live) Champions Meeting</label>
            </div>
        </div>

        <!-- ===================== TAB 2: TRACK CONDITIONS ===================== -->
        <div x-show="activeTab === 'conditions'" class="space-y-5">
            <h2 class="text-sm font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Track Conditions</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Distance Type <span class="text-rose-500">*</span></label>
                    <select name="distance_type" required class="{{ $inp }}">
                        @foreach ($distanceTypes as $t)
                            <option value="{{ $t }}" {{ old('distance_type', $event->distance_type ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Distance (m) <span class="text-rose-500">*</span></label>
                    <input type="number" name="distance_meters" value="{{ old('distance_meters', $event->distance_meters ?? 1600) }}" required min="800" max="3600" class="{{ $inp }} font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Surface <span class="text-rose-500">*</span></label>
                    <select name="surface" required class="{{ $inp }}">
                        @foreach ($surfaces as $s)
                            <option value="{{ $s }}" {{ old('surface', $event->surface ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Season <span class="text-rose-500">*</span></label>
                    <select name="season" required class="{{ $inp }}">
                        @foreach ($seasons as $s)
                            <option value="{{ $s }}" {{ old('season', $event->season ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Weather <span class="text-rose-500">*</span></label>
                    <select name="weather" required class="{{ $inp }}">
                        @foreach ($weathers as $w)
                            <option value="{{ $w }}" {{ old('weather', $event->weather ?? '') === $w ? 'selected' : '' }}>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Track Condition <span class="text-rose-500">*</span></label>
                    <select name="track_condition" required class="{{ $inp }}">
                        @foreach ($trackConditions as $tc)
                            <option value="{{ $tc }}" {{ old('track_condition', $event->track_condition ?? '') === $tc ? 'selected' : '' }}>{{ $tc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- ===================== TAB 3: STAT BENCHMARKS ===================== -->
        <div x-show="activeTab === 'stats'" class="space-y-5">
            <h2 class="text-sm font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Minimum & Recommended Stat Benchmarks</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Set target stats for all 5 categories. Minimum = survivable, Recommended = competitive.</p>

            @php
                $statFields = [
                    'speed'   => ['label' => 'Speed',   'color' => 'text-sky-600 dark:text-sky-400'],
                    'stamina' => ['label' => 'Stamina', 'color' => 'text-rose-500 dark:text-rose-400'],
                    'power'   => ['label' => 'Power',   'color' => 'text-amber-600 dark:text-amber-400'],
                    'guts'    => ['label' => 'Guts',    'color' => 'text-emerald-600 dark:text-emerald-400'],
                    'wit'     => ['label' => 'Wit',     'color' => 'text-indigo-600 dark:text-indigo-400'],
                ];
                $statRec = $event->stat_recommendations ?? [];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                @foreach ($statFields as $key => $cfg)
                    <div class="space-y-3">
                        <div class="text-xs font-black uppercase tracking-wider {{ $cfg['color'] }}">{{ $cfg['label'] }}</div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Min Target</label>
                            <input type="text" name="stat_min_{{ $key }}" value="{{ old('stat_min_' . $key, $statRec['minimum'][$key] ?? '') }}" placeholder="e.g. 1150" class="{{ $inp }} font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-amber-500 uppercase mb-1">Recommended</label>
                            <input type="text" name="stat_rec_{{ $key }}" value="{{ old('stat_rec_' . $key, $statRec['recommended'][$key] ?? '') }}" placeholder="e.g. 1200+" class="{{ $inp }} font-mono">
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Stamina / Notes</label>
                <input type="text" name="stat_notes" value="{{ old('stat_notes', $statRec['notes'] ?? '') }}" placeholder="e.g. Requires 650+ Stamina or gold recovery skill on this track." class="{{ $inp }}">
            </div>
        </div>

        <!-- ===================== TAB 4: SKILL INHERITANCE ===================== -->
        <div x-show="activeTab === 'inheritance'" class="space-y-5">
            <h2 class="text-sm font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Skill Inheritance (Parent Unique Skills)</h2>

            @foreach ($styles as $style)
                <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $style }}</span>
                        <button type="button" @click="addInheritSkill('{{ $style }}')"
                                class="text-[10px] font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300">+ Add Skill</button>
                    </div>

                    <template x-for="(skill, si) in (inheritance['{{ $style }}'] || [])" :key="si">
                        <div class="flex items-center space-x-2">
                            <input type="hidden" :name="'inheritance[{{ $style }}][' + si + '][icon]'" x-model="skill.icon">
                            <input type="text" :name="'inheritance[{{ $style }}][' + si + '][name]'" x-model="skill.name"
                                   placeholder="Skill name (e.g. Angling and Scheming)"
                                   class="flex-1 {{ $inp }}">
                            <input type="text" x-model="skill.icon" placeholder="GameTora icon URL (optional)"
                                   class="flex-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700/80 rounded-2xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-sky-500 dark:focus:border-amber-500">
                            <button type="button" @click="removeInheritSkill('{{ $style }}', si)"
                                    class="p-1.5 rounded-xl text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>

        <!-- ===================== TAB 5: RECOMMENDED SKILLS ===================== -->
        <div x-show="activeTab === 'skills'" class="space-y-5">
            <h2 class="text-sm font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Recommended Skills by Running Style</h2>

            @foreach ($styles as $style)
                <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 space-y-4">
                    <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $style }}</span>

                    @foreach (['core_accel' => ['label' => 'Core Acceleration', 'color' => 'text-rose-500'], 'velocity_other' => ['label' => 'Velocity & Support', 'color' => 'text-sky-600 dark:text-sky-400']] as $group => $gcfg)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $gcfg['color'] }}">{{ $gcfg['label'] }}</span>
                                <button type="button" @click="addRecSkill('{{ $style }}', '{{ $group }}')"
                                        class="text-[10px] font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700">+ Add</button>
                            </div>

                            <template x-for="(skill, si) in ((recSkills['{{ $style }}'] || {})['{{ $group }}'] || [])" :key="si">
                                <div class="flex items-center space-x-2">
                                    <input type="hidden" :name="'rec_skills[{{ $style }}][{{ $group }}][' + si + '][icon]'" x-model="skill.icon">
                                    <input type="text" :name="'rec_skills[{{ $style }}][{{ $group }}][' + si + '][name]'" x-model="skill.name"
                                           placeholder="Skill name"
                                           class="flex-1 {{ $inp }}">
                                    <input type="text" x-model="skill.icon" placeholder="GameTora icon URL (opt)"
                                           class="flex-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700/80 rounded-2xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-sky-500 dark:focus:border-amber-500">
                                    <button type="button" @click="removeRecSkill('{{ $style }}', '{{ $group }}', si)"
                                            class="p-1.5 rounded-xl text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- ===================== TAB 6: SUPOKA DECKS ===================== -->
        <div x-show="activeTab === 'decks'" class="space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Recommended Support Card (Supoka) Decks</h2>
                <button type="button" @click="addDeck()"
                        class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300">+ Add Deck</button>
            </div>

            <template x-for="(deck, di) in decks" :key="di">
                <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200">Deck <span x-text="di + 1"></span></span>
                        <button type="button" @click="removeDeck(di)" class="text-[10px] font-bold text-rose-400 hover:text-rose-600">Remove</button>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Archetype / Name</label>
                        <input type="text" :name="'decks[' + di + '][archetype]'" x-model="deck.archetype"
                               placeholder="e.g. 2 Speed / 2 Power / 2 Wit (Standard Ace)"
                               class="{{ $inp }}">
                    </div>

                    <!-- 6 Card Slots -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Support Cards (up to 6)</label>
                            <button type="button" @click="addCard(di)" class="text-[10px] font-bold text-sky-600 dark:text-sky-400">+ Card</button>
                        </div>
                        <template x-for="(card, ci) in deck.cards" :key="ci">
                            <div class="flex items-center space-x-2">
                                <input type="text" :name="'decks[' + di + '][cards][' + ci + '][name]'" x-model="card.name"
                                       placeholder="e.g. SSR Kitasan Black (Speed)"
                                       class="flex-1 {{ $inp }}">
                                <input type="text" x-model="card.icon" :name="'decks[' + di + '][cards][' + ci + '][icon]'"
                                       placeholder="GameTora card icon URL"
                                       class="flex-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700/80 rounded-2xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-sky-500 dark:focus:border-amber-500">
                                <button type="button" @click="removeCard(di, ci)"
                                        class="p-1.5 rounded-xl text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Deck Notes</label>
                        <input type="text" :name="'decks[' + di + '][notes]'" x-model="deck.notes"
                               placeholder="Building strategy notes..."
                               class="{{ $inp }}">
                    </div>
                </div>
            </template>
        </div>

        <!-- ===================== TAB 7: TEAM COMPOSITIONS ===================== -->
        <div x-show="activeTab === 'teams'" class="space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-sm font-black text-slate-900 dark:text-white">3-Uma Team Compositions</h2>
                <button type="button" @click="addTeam()"
                        class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700">+ Add Team</button>
            </div>

            <template x-for="(team, ti) in teams" :key="ti">
                <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200">Team <span x-text="ti + 1"></span></span>
                        <button type="button" @click="removeTeam(ti)" class="text-[10px] font-bold text-rose-400 hover:text-rose-600">Remove</button>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Team Name / Archetype</label>
                        <input type="text" :name="'teams[' + ti + '][name]'" x-model="team.name"
                               placeholder="e.g. Double Front + Late Ace"
                               class="{{ $inp }}">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <template x-for="(_, mi) in [0, 1, 2]" :key="mi">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Slot <span x-text="mi + 1"></span></label>
                                <input type="text" :name="'teams[' + ti + '][lineup][' + mi + ']'" x-model="team.lineup[mi]"
                                       placeholder="e.g. Smart Falcon (Front Ace)"
                                       class="{{ $inp }}">
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Strategy / Win Condition</label>
                        <textarea :name="'teams[' + ti + '][strategy]'" x-model="team.strategy"
                                  placeholder="Describe tactical approach and win conditions..."
                                  rows="2"
                                  class="{{ $inp }}"></textarea>
                    </div>
                </div>
            </template>
        </div>

        <!-- ===================== FORM FOOTER ===================== -->
        <div class="pt-5 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
            @if (isset($event))
                <a href="{{ route('admin.events.tiers.index', $event) }}"
                   class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 inline-flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <span>Open Tier Builder</span>
                </a>
            @else
                <div></div>
            @endif

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-2xl text-xs font-extrabold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-900/30 transition-all">
                    {{ isset($event) ? 'Save Meta Guide' : 'Create CM Edition' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
