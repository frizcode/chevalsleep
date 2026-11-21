@extends('layouts.admin')

@section('page-title', 'Uma Characters Database')

@section('content')
<div x-data="{
    search: '',
    selectedStyle: 'All',
    characters: {{ json_encode($characters->items()) }},

    filteredCharacters() {
        return this.characters.filter(c => {
            if (this.selectedStyle !== 'All' && c.default_running_style !== this.selectedStyle) return false;
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                const name = (c.name || '').toLowerCase();
                const variant = (c.variant_title || '').toLowerCase();
                const slug = (c.slug || '').toLowerCase();
                if (!name.includes(q) && !variant.includes(q) && !slug.includes(q)) return false;
            }
            return true;
        });
    }
}" class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-navy-900 dark:text-sail-50 tracking-tight font-sans">Characters Database</h1>
            <p class="text-xs text-sail-500 dark:text-sail-400 mt-1">Manage Uma Musume racers, variants, default styles, and GameTora avatars.</p>
        </div>

        <a href="{{ route('admin.characters.create') }}"
           class="inline-flex items-center space-x-2 px-4 py-2 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Character</span>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-3.5 shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sail-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search"
                       placeholder="Search character name, variant, or slug..."
                       class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md pl-9 pr-3.5 py-1.5 text-xs text-navy-900 dark:text-sail-100 placeholder-sail-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
            </div>

            <!-- Style Pills Filter -->
            <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 sm:pb-0">
                <template x-for="style in ['All', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer']" :key="style">
                    <button type="button" @click="selectedStyle = style"
                            class="px-2.5 py-1.5 rounded-md text-xs font-semibold transition-colors whitespace-nowrap"
                            :class="selectedStyle === style ? 'bg-navy-900 dark:bg-brass-500 text-white dark:text-navy-950 shadow-sm' : 'bg-sail-100 dark:bg-navy-700 text-sail-600 dark:text-sail-400 hover:text-navy-900 dark:hover:text-sail-100'">
                        <span x-text="style"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Characters Table Card -->
    <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-sail-600 dark:text-sail-300">
                <thead class="bg-sail-50 dark:bg-navy-900/60 text-[11px] uppercase tracking-wider text-sail-500 dark:text-sail-400 border-b border-sail-200 dark:border-navy-700">
                    <tr>
                        <th class="py-3 px-5 font-bold">Racer Info</th>
                        <th class="py-3 px-4 font-bold">Running Style</th>
                        <th class="py-3 px-4 font-bold">Base Rarity</th>
                        <th class="py-3 px-4 font-bold">Tier Usage</th>
                        <th class="py-3 px-5 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sail-100 dark:divide-navy-750">
                    <template x-for="character in filteredCharacters()" :key="character.id">
                        <tr class="hover:bg-sail-50/60 dark:hover:bg-navy-750/40 transition-colors">
                            <!-- Racer Info -->
                            <td class="py-3 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-md overflow-hidden bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 flex items-center justify-center flex-shrink-0"
                                         :class="character.rarity === 3 ? 'border-brass-400/50' : ''">
                                        <template x-if="character.avatar_url">
                                            <img :src="character.avatar_url" :alt="character.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!character.avatar_url">
                                            <span class="font-bold text-brass-500 text-xs font-mono" x-text="character.name.substring(0, 2).toUpperCase()"></span>
                                        </template>
                                    </div>

                                    <div>
                                        <div class="font-bold text-navy-900 dark:text-sail-100 text-sm flex items-center space-x-2">
                                            <span x-text="character.name"></span>
                                            <template x-if="character.variant_title && character.variant_title !== 'Original'">
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-brass-500/10 text-brass-600 dark:text-brass-400 border border-brass-500/30"
                                                      x-text="character.variant_title"></span>
                                            </template>
                                        </div>
                                        <div class="text-[11px] font-mono text-sail-400 dark:text-sail-500" x-text="character.slug"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Style -->
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border"
                                      :class="{
                                        'bg-cerulean-500/10 text-cerulean-600 dark:text-cerulean-400 border-cerulean-500/30': character.default_running_style === 'Front Runner',
                                        'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30': character.default_running_style === 'Pace Chaser',
                                        'bg-brass-500/10 text-brass-600 dark:text-brass-400 border-brass-500/30': character.default_running_style === 'Late Surger',
                                        'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/30': character.default_running_style === 'End Closer'
                                      }"
                                      x-text="character.default_running_style"></span>
                            </td>

                            <!-- Rarity -->
                            <td class="py-3 px-4">
                                <div class="flex items-center text-brass-500">
                                    <template x-for="star in character.rarity" :key="star">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                    <span class="text-xs text-sail-400 dark:text-sail-500 ml-1.5 font-bold font-mono" x-text="character.rarity + '★'"></span>
                                </div>
                            </td>

                            <!-- Usage -->
                            <td class="py-3 px-4">
                                <span class="font-bold text-navy-900 dark:text-sail-100 font-mono text-sm tabular-nums" x-text="character.tier_entries_count || 0"></span>
                                <span class="text-xs text-sail-400 ml-0.5">tiers</span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a :href="'/admin/characters/' + character.id + '/edit'"
                                       class="px-2.5 py-1 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-700 hover:bg-sail-200 dark:hover:bg-navy-600 text-sail-700 dark:text-sail-300 border border-sail-200 dark:border-navy-600 transition-colors">
                                        Edit
                                    </a>

                                    <form :action="'/admin/characters/' + character.id" method="POST" class="inline"
                                          @submit="return confirm('Delete character ' + character.name + '?');">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit"
                                                class="p-1.5 rounded-md text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredCharacters().length === 0">
                        <td colspan="5" class="py-10 text-center text-sail-400 dark:text-sail-500 italic">
                            No characters match the current filter or search criteria.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($characters->hasPages())
            <div class="p-4 border-t border-sail-200 dark:border-navy-700">
                {{ $characters->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
