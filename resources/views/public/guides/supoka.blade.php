@extends('layouts.public')

@section('content')
<div x-data="{
    search: '',
    selectedRarity: 'All',
    selectedType: 'All',
    cards: {{ json_encode($cards) }},

    filteredCards() {
        return this.cards.filter(c => {
            if (this.selectedRarity !== 'All' && c.rarity !== this.selectedRarity) return false;
            if (this.selectedType !== 'All' && c.card_type !== this.selectedType) return false;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                const name = (c.name || '').toLowerCase();
                const epithet = (c.epithet || '').toLowerCase();
                const slug = (c.slug || '').toLowerCase();
                const summary = (c.summary || '').toLowerCase();
                const skills = Array.isArray(c.key_skills) ? c.key_skills.join(' ').toLowerCase() : '';
                if (!name.includes(q) && !epithet.includes(q) && !slug.includes(q) && !summary.includes(q) && !skills.includes(q)) return false;
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
                <span>Training Support</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-navy-950 dark:text-white tracking-tight">
                {{ __('ui.database.supoka_title') }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ __('ui.database.supoka_desc') }}
            </p>
        </div>

        <div class="text-xs font-mono text-slate-400">
            <span class="font-bold text-navy-950 dark:text-sail-100 tabular-nums" x-text="filteredCards().length"></span> / {{ $cards->count() }} cards
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
                       placeholder="{{ __('ui.database.search_supoka') }}"
                       class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md pl-9 pr-3.5 py-1.5 text-xs text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
            </div>

            <!-- Card Type Pills Filter -->
            <div class="flex items-center space-x-1 overflow-x-auto pb-1 md:pb-0 text-xs">
                <button type="button" @click="selectedType = 'All'"
                        class="px-2.5 py-1 rounded-md font-semibold transition-colors whitespace-nowrap"
                        :class="selectedType === 'All' ? 'bg-navy-900 dark:bg-brass-500 text-white dark:text-navy-950 shadow-sm' : 'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-400 hover:text-navy-900 dark:hover:text-sail-100'">
                    {{ __('ui.database.all_types') }}
                </button>
                <template x-for="t in ['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group']" :key="t">
                    <button type="button" @click="selectedType = t"
                            class="px-2.5 py-1 rounded-md font-semibold transition-colors whitespace-nowrap"
                            :class="selectedType === t ? 'bg-navy-900 dark:bg-brass-500 text-white dark:text-navy-950 shadow-sm' : 'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-400 hover:text-navy-900 dark:hover:text-sail-100'">
                        <span x-text="t"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Rarity Row -->
        <div class="flex items-center space-x-2 pt-2 border-t border-sail-100 dark:border-navy-750 text-xs font-mono">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Rarity:</span>
            <button type="button" @click="selectedRarity = 'All'"
                    class="px-2 py-0.5 rounded text-[11px] font-semibold"
                    :class="selectedRarity === 'All' ? 'bg-sail-200 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold' : 'text-slate-500 hover:text-navy-900 dark:hover:text-white'">
                All
            </button>
            <template x-for="r in ['SSR', 'SR', 'R']" :key="r">
                <button type="button" @click="selectedRarity = r"
                        class="px-2 py-0.5 rounded text-[11px] font-semibold"
                        :class="{
                            'bg-brass-500/15 text-brass-600 dark:text-brass-400 border border-brass-500/40 font-bold': selectedRarity === r && r === 'SSR',
                            'bg-cerulean-500/15 text-cerulean-600 dark:text-cerulean-400 border border-cerulean-500/40 font-bold': selectedRarity === r && r === 'SR',
                            'bg-sail-200 dark:bg-navy-750 text-slate-400 border border-sail-300 dark:border-navy-600 font-bold': selectedRarity === r && r === 'R',
                            'hover:bg-sail-100 dark:hover:bg-navy-700 text-slate-400': selectedRarity !== r
                        }">
                    <span x-text="r"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Support Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        <template x-for="card in filteredCards()" :key="card.id">
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-2.5 flex flex-col justify-between space-y-2 shadow-sm hover:border-cerulean-500/60 dark:hover:border-brass-400/60 transition-colors">
                
                <div class="space-y-2">
                    <!-- Portrait Card Image Container -->
                    <div class="relative w-full aspect-[3/4] rounded-md overflow-hidden bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 flex items-center justify-center">
                        <template x-if="card.card_url">
                            <img :src="card.card_url" :alt="card.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!card.card_url">
                            <div class="text-center p-3">
                                <span class="font-bold text-brass-500 text-sm font-mono block" x-text="card.rarity"></span>
                                <span class="text-[10px] text-slate-400 font-mono block mt-1" x-text="card.card_type"></span>
                            </div>
                        </template>

                        <!-- Rarity Pill Overlay -->
                        <div class="absolute top-1.5 left-1.5 px-1.5 py-0.2 rounded font-mono font-bold text-[9px] shadow-sm border"
                             :class="{
                                 'bg-brass-500 text-navy-950 border-brass-400': card.rarity === 'SSR',
                                 'bg-cerulean-600 text-white border-cerulean-400': card.rarity === 'SR',
                                 'bg-slate-700 text-slate-200 border-slate-600': card.rarity === 'R'
                             }"
                             x-text="card.rarity"></div>

                        <!-- Card Type Pill Overlay -->
                        <div class="absolute top-1.5 right-1.5 px-1.5 py-0.2 rounded font-mono font-bold text-[9px] shadow-sm bg-navy-950/80 backdrop-blur-sm border border-sail-200/40 text-sail-100"
                             x-text="card.card_type"></div>
                    </div>

                    <!-- Title & Epithet -->
                    <div>
                        <template x-if="card.epithet">
                            <div class="text-[10px] text-brass-600 dark:text-brass-400 font-semibold truncate" :title="card.epithet" x-text="'[' + card.epithet + ']'"></div>
                        </template>
                        <div class="font-bold text-xs text-navy-950 dark:text-sail-100 leading-tight truncate" :title="card.name" x-text="card.name"></div>
                    </div>

                    <!-- Key Gold / Core Skills -->
                    <template x-if="card.key_skills && card.key_skills.length > 0">
                        <div class="flex flex-wrap gap-1 pt-1 border-t border-sail-100 dark:border-navy-750">
                            <template x-for="skill in card.key_skills.slice(0, 2)" :key="skill">
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-sail-100 dark:bg-navy-900 text-cerulean-700 dark:text-cerulean-400 border border-sail-200 dark:border-navy-700 truncate max-w-full"
                                      x-text="skill"></span>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer Specialty Priority -->
                <div class="pt-1.5 border-t border-sail-100 dark:border-navy-750 flex items-center justify-between text-[9px] font-mono text-slate-400">
                    <span>Priority:</span>
                    <span class="font-bold text-navy-950 dark:text-sail-100 tabular-nums" x-text="(card.specialty_priority || 50) + '%'"></span>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filteredCards().length === 0" class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-10 text-center text-xs text-slate-400 italic">
        {{ __('ui.database.no_results') }}
    </div>

</div>
@endsection
