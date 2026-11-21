@extends('layouts.public')

@section('content')
<div x-data="{
    search: '',
    selectedRarity: 'All',
    selectedType: 'All',
    selectedStyle: 'All',
    skills: {{ json_encode($skills) }},

    filteredSkills() {
        return this.skills.filter(s => {
            if (this.selectedRarity !== 'All' && s.rarity !== this.selectedRarity) return false;
            if (this.selectedType !== 'All' && s.skill_type !== this.selectedType) return false;
            if (this.selectedStyle !== 'All' && s.running_style !== 'Universal' && s.running_style !== this.selectedStyle) return false;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                const name = (s.name || '').toLowerCase();
                const cond = (s.activation_condition || '').toLowerCase();
                const desc = (s.description || '').toLowerCase();
                if (!name.includes(q) && !cond.includes(q) && !desc.includes(q)) return false;
            }
            return true;
        });
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sail-200 dark:border-navy-700">
        <div>
            <div class="flex items-center space-x-2 text-xs font-mono font-bold uppercase tracking-wider text-cerulean-600 dark:text-cerulean-400 mb-1">
                <span>{{ __('ui.nav.encyclopedia') }}</span>
                <span>&bull;</span>
                <span>Race Mechanics</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-navy-950 dark:text-white tracking-tight">
                {{ __('ui.database.skills_title') }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ __('ui.database.skills_desc') }}
            </p>
        </div>

        <div class="text-xs font-mono text-slate-400">
            <span class="font-bold text-navy-950 dark:text-sail-100 tabular-nums" x-text="filteredSkills().length"></span> / {{ $skills->count() }} skills
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-3.5 shadow-sm space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search"
                       placeholder="{{ __('ui.database.search_skills') }}"
                       class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md pl-9 pr-3.5 py-1.5 text-xs text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
            </div>

            <!-- Skill Type Filter Pills -->
            <div class="flex items-center space-x-1 overflow-x-auto pb-1 md:pb-0 text-xs">
                <button type="button" @click="selectedType = 'All'"
                        class="px-2.5 py-1 rounded-md font-semibold transition-colors whitespace-nowrap"
                        :class="selectedType === 'All' ? 'bg-navy-900 dark:bg-brass-500 text-white dark:text-navy-950 shadow-sm' : 'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-400 hover:text-navy-900 dark:hover:text-sail-100'">
                    {{ __('ui.database.all_types') }}
                </button>
                <template x-for="t in ['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive']" :key="t">
                    <button type="button" @click="selectedType = t"
                            class="px-2.5 py-1 rounded-md font-semibold transition-colors whitespace-nowrap"
                            :class="selectedType === t ? 'bg-navy-900 dark:bg-brass-500 text-white dark:text-navy-950 shadow-sm' : 'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-400 hover:text-navy-900 dark:hover:text-sail-100'">
                        <span x-text="t"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Rarity & Running Style Row -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-sail-100 dark:border-navy-750 text-xs font-mono">
            <!-- Rarity Pills -->
            <div class="flex items-center space-x-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Rarity:</span>
                <button type="button" @click="selectedRarity = 'All'"
                        class="px-2 py-0.5 rounded text-[11px] font-semibold"
                        :class="selectedRarity === 'All' ? 'bg-sail-200 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold' : 'text-slate-500 hover:text-navy-900 dark:hover:text-white'">
                    All
                </button>
                <template x-for="r in ['Unique', 'Rare', 'Inherited', 'Normal']" :key="r">
                    <button type="button" @click="selectedRarity = r"
                            class="px-2 py-0.5 rounded text-[11px] font-semibold"
                            :class="selectedRarity === r ? 'bg-navy-900 dark:bg-brass-500/20 text-brass-500 border border-brass-500/40 font-bold' : 'hover:bg-sail-100 dark:hover:bg-navy-700 text-slate-400'">
                        <span x-text="r"></span>
                    </button>
                </template>
            </div>

            <!-- Running Style Pills -->
            <div class="flex items-center space-x-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Style:</span>
                <button type="button" @click="selectedStyle = 'All'"
                        class="px-2 py-0.5 rounded text-[11px] font-semibold"
                        :class="selectedStyle === 'All' ? 'bg-sail-200 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold' : 'text-slate-500 hover:text-navy-900 dark:hover:text-white'">
                    All
                </button>
                <template x-for="s in ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer']" :key="s">
                    <button type="button" @click="selectedStyle = s"
                            class="px-2 py-0.5 rounded text-[11px] font-semibold"
                            :class="selectedStyle === s ? 'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border border-cerulean-500/30 font-bold' : 'hover:bg-sail-100 dark:hover:bg-navy-700 text-slate-400'">
                        <span x-text="s"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Skills List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <template x-for="skill in filteredSkills()" :key="skill.id">
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-3.5 flex flex-col justify-between space-y-2.5 shadow-sm hover:border-cerulean-500/60 dark:hover:border-brass-400/60 transition-colors">
                
                <div class="space-y-2">
                    <!-- Icon, Name, and Badges -->
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-md bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 overflow-hidden flex items-center justify-center flex-shrink-0">
                            <template x-if="skill.icon_url">
                                <img :src="skill.icon_url" :alt="skill.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!skill.icon_url">
                                <svg class="w-5 h-5 text-brass-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </template>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                                <h3 class="font-bold text-xs text-navy-950 dark:text-sail-100 leading-snug" x-text="skill.name"></h3>
                                
                                <!-- Rarity Badge -->
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold border"
                                      :class="{
                                          'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30': skill.rarity === 'Unique',
                                          'bg-brass-500/15 text-brass-600 dark:text-brass-400 border-brass-500/40': skill.rarity === 'Rare',
                                          'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30': skill.rarity === 'Inherited',
                                          'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-300 border-sail-200 dark:border-navy-600': skill.rarity === 'Normal'
                                      }"
                                      x-text="skill.rarity"></span>
                            </div>

                            <!-- Type and Style Pills -->
                            <div class="flex flex-wrap items-center gap-1 mt-1 text-[9px] font-mono">
                                <span class="px-1.5 py-0.2 rounded font-semibold border"
                                      :class="{
                                          'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30': skill.skill_type === 'Acceleration',
                                          'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30': skill.skill_type === 'Velocity',
                                          'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30': skill.skill_type === 'Recovery',
                                          'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30': skill.skill_type === 'Debuff',
                                          'bg-brass-500/10 text-brass-600 dark:text-brass-400 border-brass-500/30': skill.skill_type === 'Passive'
                                      }"
                                      x-text="skill.skill_type"></span>

                                <template x-if="skill.running_style && skill.running_style !== 'Universal'">
                                    <span class="px-1.5 py-0.2 rounded bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-300 border border-sail-200 dark:border-navy-600"
                                          x-text="skill.running_style"></span>
                                </template>

                                <template x-if="skill.distance_type && skill.distance_type !== 'Universal'">
                                    <span class="px-1.5 py-0.2 rounded bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-300 border border-sail-200 dark:border-navy-600"
                                          x-text="skill.distance_type"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Activation Condition -->
                    <template x-if="skill.activation_condition">
                        <div class="p-2 rounded bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 text-[11px] space-y-0.5">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 font-mono block">Trigger Condition:</span>
                            <div class="text-slate-700 dark:text-slate-300 leading-relaxed font-mono text-[10px]" x-text="skill.activation_condition"></div>
                        </div>
                    </template>
                </div>

                <!-- Description / Effect -->
                <template x-if="skill.description">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug pt-1 border-t border-sail-100 dark:border-navy-750" x-text="skill.description"></p>
                </template>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filteredSkills().length === 0" class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-10 text-center text-xs text-slate-400 italic">
        {{ __('ui.database.no_results') }}
    </div>

</div>
@endsection
