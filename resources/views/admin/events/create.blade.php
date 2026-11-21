@php
/* Form for Create CM Meta Guide */
$styles = ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'];
@endphp
@extends('layouts.admin')

@section('page-title', 'Create New CM Edition')

@section('content')
<div x-data="{
    activeTab: 'basics',
    tabs: ['basics', 'conditions', 'stats', 'inheritance', 'skills', 'decks', 'teams'],

    /* Master Skills Dictionary for Auto-fill */
    masterSkills: {{ json_encode($skillsList->map(fn($s) => ['name' => $s->name, 'icon' => $s->icon_url ?? ''])->keyBy('name')) }},

    /* Master Support Cards Dictionary for Auto-fill */
    masterCards: {{ json_encode($supportCardsList->map(fn($c) => ['name' => $c->name, 'title' => $c->full_title, 'icon' => $c->card_url ?? ''])->keyBy('name')) }},

    onSkillNameChange(item) {
        if (item.name && this.masterSkills[item.name] && this.masterSkills[item.name].icon) {
            item.icon = this.masterSkills[item.name].icon;
        }
    },

    onCardNameChange(item) {
        if (item.name && this.masterCards[item.name] && this.masterCards[item.name].icon) {
            item.icon = this.masterCards[item.name].icon;
        }
    },

    /* Supoka Decks repeater */
    decks: {{ json_encode(old('decks', [['archetype' => '', 'cards' => [['name' => '', 'icon' => '']], 'notes' => '']])) }},

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
    teams: {{ json_encode(old('teams', [['name' => '', 'lineup' => ['', '', ''], 'strategy' => '']])) }},

    addTeam() {
        this.teams.push({ name: '', lineup: ['', '', ''], strategy: '' });
    },
    removeTeam(i) {
        this.teams.splice(i, 1);
    },

    /* Skill Inheritance per style */
    inheritance: {{ json_encode(old('inheritance', ['Front Runner' => [], 'Pace Chaser' => [], 'Late Surger' => [], 'End Closer' => []])) }},

    addInheritSkill(style) {
        if (!this.inheritance[style]) this.inheritance[style] = [];
        this.inheritance[style].push({ name: '', icon: '' });
    },
    removeInheritSkill(style, i) {
        this.inheritance[style].splice(i, 1);
    },

    /* Recommended Skills per style */
    recSkills: {{ json_encode(old('rec_skills', [
        'Front Runner' => ['core_accel' => [], 'velocity_other' => []],
        'Pace Chaser'  => ['core_accel' => [], 'velocity_other' => []],
        'Late Surger'  => ['core_accel' => [], 'velocity_other' => []],
        'End Closer'   => ['core_accel' => [], 'velocity_other' => []]
    ])) }},

    addRecSkill(style, group) {
        if (!this.recSkills[style]) this.recSkills[style] = { core_accel: [], velocity_other: [] };
        this.recSkills[style][group].push({ name: '', icon: '' });
    },
    removeRecSkill(style, group, i) {
        this.recSkills[style][group].splice(i, 1);
    }
}" class="max-w-5xl mx-auto space-y-6">

    <!-- Datalist Providers for Master Database Auto-fill -->
    <datalist id="master-skills-datalist">
        @foreach ($skillsList as $sk)
            <option value="{{ $sk->name }}">{{ $sk->rarity }} &bull; {{ $sk->skill_type }}</option>
        @endforeach
    </datalist>

    <datalist id="master-cards-datalist">
        @foreach ($supportCardsList as $sc)
            <option value="{{ $sc->name }}">{{ $sc->rarity }} &bull; {{ $sc->card_type }} ({{ $sc->epithet }})</option>
        @endforeach
    </datalist>

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-navy-950 dark:text-sail-50 tracking-tight font-sans">
                Create New Champions Meeting
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure complete 7-section racing telemetry and meta benchmarks.</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold text-slate-500 hover:text-navy-950 dark:hover:text-sail-100 transition-colors font-mono">
            &larr; Back to CM List
        </a>
    </div>

    <!-- Error Validation Banner -->
    @if ($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 rounded-lg space-y-1.5 text-xs text-rose-700 dark:text-rose-300 font-mono">
            <div class="font-bold uppercase tracking-wider flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>Please resolve the following errors:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tab Switcher Navigation -->
    <div class="flex flex-wrap gap-1 bg-sail-100 dark:bg-navy-900/60 border border-sail-200 dark:border-navy-700 rounded-md p-1">
        @php
            $tabLabels = [
                'basics'      => '1. Basics',
                'conditions'  => '2. Conditions',
                'stats'       => '3. Benchmarks',
                'inheritance' => '4. Inheritance',
                'skills'      => '5. Skills',
                'decks'       => '6. Decks',
                'teams'       => '7. Teams',
            ];
        @endphp
        @foreach ($tabLabels as $tab => $label)
            <button type="button" @click="activeTab = '{{ $tab }}'"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors font-mono"
                    :class="activeTab === '{{ $tab }}'
                        ? 'bg-white dark:bg-navy-800 text-navy-950 dark:text-sail-100 shadow-sm border border-sail-200 dark:border-navy-700'
                        : 'text-slate-500 dark:text-slate-400 hover:text-navy-950 dark:hover:text-sail-100'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Form with novalidate to allow multi-tab submission -->
    <form method="POST" action="{{ route('admin.events.store') }}" id="cm-meta-form" novalidate
          class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-5 sm:p-6 shadow-sm space-y-5">
        @csrf

        @php $inp = 'w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-brass-500 transition-colors font-mono'; @endphp

        <!-- ===================== TAB 1: BASICS ===================== -->
        <div x-show="activeTab === 'basics'" class="space-y-4">
            <h2 class="text-sm font-bold text-navy-950 dark:text-sail-100 border-b border-sail-200 dark:border-navy-750 pb-2.5 font-sans">Basic CM Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                        Event Title <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. CM #23 Taurus Cup" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                        Slug <span class="text-slate-400 font-normal lowercase">(auto-generated if empty)</span>
                    </label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. cm-23-taurus-cup" class="{{ $inp }}">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">CM Number</label>
                    <input type="number" name="cm_number" value="{{ old('cm_number') }}" min="1" max="999" placeholder="23" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                        Track Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="track_name" value="{{ old('track_name') }}" required placeholder="e.g. Tokyo 2400m" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">Event Date</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}" class="{{ $inp }}">
                </div>
            </div>

            <div class="pt-2 flex items-center space-x-2.5">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                       class="w-4 h-4 rounded bg-sail-50 dark:bg-navy-900 border-sail-300 dark:border-navy-700 text-brass-500 focus:ring-brass-500">
                <label for="is_active" class="text-xs font-semibold text-navy-900 dark:text-sail-100 cursor-pointer">
                    Set as Active (Live) Champions Meeting Protocol
                </label>
            </div>
        </div>

        <!-- ===================== TAB 2: TRACK CONDITIONS ===================== -->
        <div x-show="activeTab === 'conditions'" class="space-y-4">
            <h2 class="text-sm font-bold text-navy-950 dark:text-sail-100 border-b border-sail-200 dark:border-navy-750 pb-2.5 font-sans">Track Conditions</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                        Distance Type <span class="text-rose-500">*</span>
                    </label>
                    <select name="distance_type" class="{{ $inp }}">
                        @foreach ($distanceTypes as $t)
                            <option value="{{ $t }}" {{ old('distance_type', 'Medium') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                        Distance (m) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="distance_meters" value="{{ old('distance_meters', 2400) }}" min="800" max="3600" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                        Surface <span class="text-rose-500">*</span>
                    </label>
                    <select name="surface" class="{{ $inp }}">
                        @foreach ($surfaces as $s)
                            <option value="{{ $s }}" {{ old('surface', 'Turf') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">Season</label>
                    <select name="season" class="{{ $inp }}">
                        @foreach ($seasons as $s)
                            <option value="{{ $s }}" {{ old('season', 'Spring') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">Weather</label>
                    <select name="weather" class="{{ $inp }}">
                        @foreach ($weathers as $w)
                            <option value="{{ $w }}" {{ old('weather', 'Sunny') === $w ? 'selected' : '' }}>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">Track Condition</label>
                    <select name="track_condition" class="{{ $inp }}">
                        @foreach ($trackConditions as $tc)
                            <option value="{{ $tc }}" {{ old('track_condition', 'Firm') === $tc ? 'selected' : '' }}>{{ $tc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- ===================== TAB 3: STAT BENCHMARKS ===================== -->
        <div x-show="activeTab === 'stats'" class="space-y-4">
            <h2 class="text-sm font-bold text-navy-950 dark:text-sail-100 border-b border-sail-200 dark:border-navy-750 pb-2.5 font-sans">Minimum & Recommended Stat Benchmarks</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Set target stats for all 5 categories. Minimum = baseline survivability, Recommended = competitive threshold.</p>

            @php
                $statFields = [
                    'speed'   => ['label' => 'Speed',   'color' => 'text-cerulean-600 dark:text-cerulean-400'],
                    'stamina' => ['label' => 'Stamina', 'color' => 'text-rose-500 dark:text-rose-400'],
                    'power'   => ['label' => 'Power',   'color' => 'text-brass-500 dark:text-brass-400'],
                    'guts'    => ['label' => 'Guts',    'color' => 'text-emerald-600 dark:text-emerald-400'],
                    'wit'     => ['label' => 'Wit',     'color' => 'text-purple-600 dark:text-purple-400'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                @foreach ($statFields as $key => $cfg)
                    <div class="space-y-2 bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 rounded-md p-3">
                        <div class="text-xs font-bold uppercase tracking-wider {{ $cfg['color'] }} font-mono">{{ $cfg['label'] }}</div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 uppercase mb-0.5 font-mono">Min Target</label>
                            <input type="text" name="stat_min_{{ $key }}" value="{{ old('stat_min_' . $key) }}" placeholder="e.g. 1150" class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-brass-500 uppercase mb-0.5 font-mono">Recommended</label>
                            <input type="text" name="stat_rec_{{ $key }}" value="{{ old('stat_rec_' . $key) }}" placeholder="e.g. 1200+" class="{{ $inp }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">Stamina / Guts Notes</label>
                <input type="text" name="stat_notes" value="{{ old('stat_notes') }}" placeholder="e.g. Requires 1 gold recovery or 700+ Stamina for medium Tokyo course." class="{{ $inp }}">
            </div>
        </div>

        <!-- ===================== TAB 4: SKILL INHERITANCE ===================== -->
        <div x-show="activeTab === 'inheritance'" class="space-y-4">
            <div class="flex items-center justify-between border-b border-sail-200 dark:border-navy-750 pb-2.5">
                <div>
                    <h2 class="text-sm font-bold text-navy-950 dark:text-sail-100 font-sans">Skill Inheritance (Parent Unique Skills)</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pick from Master Database skills (auto-fills GameTora icon) or enter custom skills.</p>
                </div>
            </div>

            @foreach ($styles as $style)
                <div class="bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 rounded-md p-3.5 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-navy-950 dark:text-sail-100 font-mono">{{ $style }}</span>
                        <button type="button" @click="addInheritSkill('{{ $style }}')"
                                class="text-[10px] font-semibold text-brass-600 dark:text-brass-400 hover:text-brass-500 font-mono">+ Add Skill</button>
                    </div>

                    <template x-for="(skill, si) in (inheritance['{{ $style }}'] || [])" :key="si">
                        <div class="flex items-center space-x-2">
                            <!-- Skill Icon Preview -->
                            <div class="w-8 h-8 rounded bg-white dark:bg-navy-950 border border-sail-200 dark:border-navy-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                <template x-if="skill.icon">
                                    <img :src="skill.icon" alt="icon" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!skill.icon">
                                    <span class="text-[9px] font-mono text-slate-400">SK</span>
                                </template>
                            </div>

                            <input type="hidden" :name="'inheritance[{{ $style }}][' + si + '][icon]'" x-model="skill.icon">
                            
                            <!-- Name with datalist auto-fill -->
                            <input type="text" :name="'inheritance[{{ $style }}][' + si + '][name]'" x-model="skill.name"
                                   @input="onSkillNameChange(skill)"
                                   list="master-skills-datalist"
                                   placeholder="Skill name (auto-fills icon from Master DB)"
                                   class="flex-1 {{ $inp }}">
                            
                            <!-- Custom Icon URL -->
                            <input type="text" x-model="skill.icon" placeholder="Icon URL (optional override)"
                                   class="flex-1 {{ $inp }}">
                            
                            <button type="button" @click="removeInheritSkill('{{ $style }}', si)"
                                    class="p-1 rounded-md text-slate-400 hover:text-rose-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>

        <!-- ===================== TAB 5: RECOMMENDED SKILLS ===================== -->
        <div x-show="activeTab === 'skills'" class="space-y-4">
            <h2 class="text-sm font-bold text-navy-950 dark:text-sail-100 border-b border-sail-200 dark:border-navy-750 pb-2.5 font-sans">Recommended Skills by Running Style</h2>

            @foreach ($styles as $style)
                <div class="bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 rounded-md p-3.5 space-y-3">
                    <span class="text-xs font-bold text-navy-950 dark:text-sail-100 font-mono">{{ $style }}</span>

                    @foreach (['core_accel' => ['label' => 'Core Acceleration (Mandatory)', 'color' => 'text-rose-500'], 'velocity_other' => ['label' => 'Velocity & Positioning', 'color' => 'text-cerulean-600 dark:text-cerulean-400']] as $group => $gcfg)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider {{ $gcfg['color'] }} font-mono">{{ $gcfg['label'] }}</span>
                                <button type="button" @click="addRecSkill('{{ $style }}', '{{ $group }}')"
                                        class="text-[10px] font-semibold text-brass-600 dark:text-brass-400 hover:text-brass-500 font-mono">+ Add Skill</button>
                            </div>

                            <template x-for="(skill, si) in ((recSkills['{{ $style }}'] || {})['{{ $group }}'] || [])" :key="si">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded bg-white dark:bg-navy-950 border border-sail-200 dark:border-navy-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        <template x-if="skill.icon">
                                            <img :src="skill.icon" alt="icon" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!skill.icon">
                                            <span class="text-[9px] font-mono text-slate-400">SK</span>
                                        </template>
                                    </div>

                                    <input type="hidden" :name="'rec_skills[{{ $style }}][{{ $group }}][' + si + '][icon]'" x-model="skill.icon">
                                    
                                    <input type="text" :name="'rec_skills[{{ $style }}][{{ $group }}][' + si + '][name]'" x-model="skill.name"
                                           @input="onSkillNameChange(skill)"
                                           list="master-skills-datalist"
                                           placeholder="Skill name"
                                           class="flex-1 {{ $inp }}">
                                    
                                    <input type="text" x-model="skill.icon" placeholder="Icon URL (optional)"
                                           class="flex-1 {{ $inp }}">
                                    
                                    <button type="button" @click="removeRecSkill('{{ $style }}', '{{ $group }}', si)"
                                            class="p-1 rounded-md text-slate-400 hover:text-rose-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- ===================== TAB 6: SUPOKA DECKS ===================== -->
        <div x-show="activeTab === 'decks'" class="space-y-4">
            <div class="flex items-center justify-between border-b border-sail-200 dark:border-navy-750 pb-2.5">
                <div>
                    <h2 class="text-sm font-bold text-navy-950 dark:text-sail-100 font-sans">Recommended Support Card (Supoka) Decks</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Configure optimal card ratio archetypes and pick cards with auto-fill.</p>
                </div>
                <button type="button" @click="addDeck()"
                        class="text-xs font-semibold text-brass-600 dark:text-brass-400 hover:text-brass-500 font-mono">+ Add Deck</button>
            </div>

            <template x-for="(deck, di) in decks" :key="di">
                <div class="bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 rounded-md p-3.5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-navy-950 dark:text-sail-100 font-mono">Deck <span x-text="di + 1"></span></span>
                        <button type="button" @click="removeDeck(di)" class="text-[10px] font-semibold text-rose-500 hover:underline font-mono">Remove Deck</button>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1 font-mono">Archetype / Ratio Name</label>
                        <input type="text" :name="'decks[' + di + '][archetype]'" x-model="deck.archetype"
                               placeholder="e.g. 2 Speed / 2 Power / 2 Wit (Standard Ace)"
                               class="{{ $inp }}">
                    </div>

                    <!-- 6 Card Slots -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 font-mono">Support Cards (up to 6)</label>
                            <button type="button" @click="addCard(di)" class="text-[10px] font-semibold text-cerulean-600 dark:text-cerulean-400 font-mono">+ Add Card</button>
                        </div>
                        <template x-for="(card, ci) in deck.cards" :key="ci">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded bg-white dark:bg-navy-950 border border-sail-200 dark:border-navy-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    <template x-if="card.icon">
                                        <img :src="card.icon" alt="card" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!card.icon">
                                        <span class="text-[9px] font-mono text-slate-400">CRD</span>
                                    </template>
                                </div>

                                <input type="text" :name="'decks[' + di + '][cards][' + ci + '][name]'" x-model="card.name"
                                       @input="onCardNameChange(card)"
                                       list="master-cards-datalist"
                                       placeholder="Card name (e.g. Kitasan Black)"
                                       class="flex-1 {{ $inp }}">
                                
                                <input type="text" x-model="card.icon" :name="'decks[' + di + '][cards][' + ci + '][icon]'"
                                       placeholder="Artwork / Icon URL (auto-fills)"
                                       class="flex-1 {{ $inp }}">
                                
                                <button type="button" @click="removeCard(di, ci)"
                                        class="p-1 rounded-md text-slate-400 hover:text-rose-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1 font-mono">Deck Building Notes</label>
                        <input type="text" :name="'decks[' + di + '][notes]'" x-model="deck.notes"
                               placeholder="Building strategy notes..."
                               class="{{ $inp }}">
                    </div>
                </div>
            </template>
        </div>

        <!-- ===================== TAB 7: TEAM COMPOSITIONS ===================== -->
        <div x-show="activeTab === 'teams'" class="space-y-4">
            <div class="flex items-center justify-between border-b border-sail-200 dark:border-navy-750 pb-2.5">
                <h2 class="text-sm font-bold text-navy-950 dark:text-sail-100 font-sans">3-Uma Team Compositions</h2>
                <button type="button" @click="addTeam()"
                        class="text-xs font-semibold text-brass-600 dark:text-brass-400 hover:text-brass-500 font-mono">+ Add Team</button>
            </div>

            <template x-for="(team, ti) in teams" :key="ti">
                <div class="bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 rounded-md p-3.5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-navy-950 dark:text-sail-100 font-mono">Team Comp <span x-text="ti + 1"></span></span>
                        <button type="button" @click="removeTeam(ti)" class="text-[10px] font-semibold text-rose-500 hover:underline font-mono">Remove</button>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1 font-mono">Team Archetype Name</label>
                        <input type="text" :name="'teams[' + ti + '][name]'" x-model="team.name"
                               placeholder="e.g. Dual Front Runners + Late Ace"
                               class="{{ $inp }}">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <template x-for="(_, mi) in [0, 1, 2]" :key="mi">
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1 font-mono">Slot <span x-text="mi + 1"></span></label>
                                <input type="text" :name="'teams[' + ti + '][lineup][' + mi + ']'" x-model="team.lineup[mi]"
                                       placeholder="e.g. Seiun Sky (Lead)"
                                       class="{{ $inp }}">
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1 font-mono">Tactical Win Condition</label>
                        <textarea :name="'teams[' + ti + '][strategy]'" x-model="team.strategy"
                                  placeholder="Describe tactical pacing and win condition synergy..."
                                  rows="2"
                                  class="{{ $inp }}"></textarea>
                    </div>
                </div>
            </template>
        </div>

        <!-- ===================== FORM FOOTER ===================== -->
        <div class="pt-4 border-t border-sail-200 dark:border-navy-750 flex flex-wrap items-center justify-between gap-3">
            <div></div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.events.index') }}" class="px-3.5 py-2 rounded-md text-xs font-semibold text-slate-500 hover:text-navy-950 dark:hover:text-sail-100 transition-colors font-mono">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2 rounded-md text-xs font-bold uppercase tracking-wider bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm font-mono">
                    Create CM Edition
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
