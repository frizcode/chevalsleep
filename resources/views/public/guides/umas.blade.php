@extends('layouts.public')

@section('content')
<div x-data="{
    search: '',
    selectedStyle: 'All',
    selectedRarity: 'All',
    characters: {{ json_encode($characters) }},

    filteredCharacters() {
        return this.characters.filter(c => {
            if (this.selectedStyle !== 'All' && c.default_running_style !== this.selectedStyle) return false;
            if (this.selectedRarity !== 'All' && c.rarity != this.selectedRarity) return false;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                const name = (c.name || '').toLowerCase();
                const variant = (c.variant_title || '').toLowerCase();
                const slug = (c.slug || '').toLowerCase();
                const notes = (c.aptitude_notes || '').toLowerCase();
                if (!name.includes(q) && !variant.includes(q) && !slug.includes(q) && !notes.includes(q)) return false;
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
                <span>Global Roster</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-navy-950 dark:text-white tracking-tight">
                {{ __('ui.database.umas_title') }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ __('ui.database.umas_desc') }}
            </p>
        </div>

        <div class="text-xs font-mono text-slate-400">
            <span class="font-bold text-navy-950 dark:text-sail-100 tabular-nums" x-text="filteredCharacters().length"></span> / {{ $characters->count() }} racers
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
                       placeholder="{{ __('ui.database.search_umas') }}"
                       class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md pl-9 pr-3.5 py-1.5 text-xs text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
            </div>

            <!-- Running Style Pills Filter (Always English Global terms) -->
            <div class="flex items-center space-x-1 overflow-x-auto pb-1 md:pb-0 text-xs">
                <button type="button" @click="selectedStyle = 'All'"
                        class="px-2.5 py-1 rounded-md font-semibold transition-colors whitespace-nowrap"
                        :class="selectedStyle === 'All' ? 'bg-navy-900 dark:bg-brass-500 text-white dark:text-navy-950 shadow-sm' : 'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-400 hover:text-navy-900 dark:hover:text-sail-100'">
                    {{ __('ui.database.all_styles') }}
                </button>
                <template x-for="st in ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer']" :key="st">
                    <button type="button" @click="selectedStyle = st"
                            class="px-2.5 py-1 rounded-md font-semibold transition-colors whitespace-nowrap"
                            :class="selectedStyle === st ? 'bg-navy-900 dark:bg-brass-500 text-white dark:text-navy-950 shadow-sm' : 'bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-400 hover:text-navy-900 dark:hover:text-sail-100'">
                        <span x-text="st"></span>
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
            <template x-for="r in [3, 2, 1]" :key="r">
                <button type="button" @click="selectedRarity = r"
                        class="px-2 py-0.5 rounded text-[11px] font-semibold text-brass-500"
                        :class="selectedRarity == r ? 'bg-brass-500/10 border border-brass-500/40 font-bold' : 'hover:bg-sail-100 dark:hover:bg-navy-700'">
                    <span x-text="r + '★'"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Racers Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <template x-for="character in filteredCharacters()" :key="character.id">
            <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-2.5 flex flex-col justify-between space-y-2 shadow-sm hover:border-cerulean-500/60 dark:hover:border-brass-400/60 transition-colors">
                
                <div class="space-y-2">
                    <!-- Square Avatar Box with Rarity stars -->
                    <div class="relative w-full aspect-square rounded-md overflow-hidden bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 flex items-center justify-center">
                        <template x-if="character.avatar_url">
                            <img :src="character.avatar_url" :alt="character.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!character.avatar_url">
                            <span class="font-bold text-brass-500 text-lg font-mono" x-text="character.name.substring(0, 2).toUpperCase()"></span>
                        </template>

                        <!-- Star Rating Overlay -->
                        <div class="absolute top-1 right-1 px-1 py-0.2 rounded bg-navy-950/80 backdrop-blur-sm border border-brass-500/40 text-[9px] font-mono font-bold text-brass-400">
                            <span x-text="character.rarity + '★'"></span>
                        </div>
                    </div>

                    <!-- Racer Info -->
                    <div>
                        <div class="font-bold text-xs text-navy-950 dark:text-sail-100 leading-tight truncate" :title="character.name" x-text="character.name"></div>
                        <template x-if="character.variant_title && character.variant_title !== 'Original'">
                            <div class="text-[10px] text-brass-600 dark:text-brass-400 font-semibold truncate" x-text="character.variant_title"></div>
                        </template>
                    </div>
                </div>

                <!-- Footer Style & Stats -->
                <div class="pt-1.5 border-t border-sail-100 dark:border-navy-750 flex items-center justify-between text-[9px] font-mono">
                    <span class="px-1.5 py-0.2 rounded font-semibold border"
                          :class="{
                              'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30': character.default_running_style === 'Front Runner',
                              'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30': character.default_running_style === 'Pace Chaser',
                              'bg-brass-500/10 text-brass-600 dark:text-brass-400 border-brass-500/30': character.default_running_style === 'Late Surger',
                              'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30': character.default_running_style === 'End Closer'
                          }"
                          x-text="character.default_running_style"></span>

                    <span class="text-slate-400" :title="'Tier entries: ' + (character.tier_entries_count || 0)">
                        <span class="font-bold text-navy-900 dark:text-sail-100" x-text="character.tier_entries_count || 0"></span> tiers
                    </span>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty Message -->
    <div x-show="filteredCharacters().length === 0" class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-10 text-center text-xs text-slate-400 italic">
        {{ __('ui.database.no_results') }}
    </div>

</div>
@endsection
