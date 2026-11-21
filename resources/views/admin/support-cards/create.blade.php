@extends('layouts.admin')

@section('page-title', 'Add New Support Card')

@section('content')
<div x-data="{
    cardPreview: null,
    gametoraUrl: '{{ old('gametora_card_url', '') }}',
    name: '{{ old('name', '') }}',
    epithet: '{{ old('epithet', '') }}',
    rarity: '{{ old('rarity', 'SSR') }}',
    cardType: '{{ old('card_type', 'Speed') }}',

    init() {
        if (this.gametoraUrl) {
            this.cardPreview = this.gametoraUrl;
        }
    },

    onCardChange(e) {
        if (e.target.files && e.target.files[0]) {
            this.cardPreview = URL.createObjectURL(e.target.files[0]);
        }
    },

    onUrlChange() {
        if (this.gametoraUrl.trim().startsWith('http')) {
            this.cardPreview = this.gametoraUrl.trim();
        }
    }
}" class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-navy-950 dark:text-sail-50 tracking-tight">Add New Support Card</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Register a support card in the Uma Musume Master Database.</p>
        </div>

        <a href="{{ route('admin.support-cards.index') }}" class="text-xs font-semibold text-slate-500 hover:text-navy-950 dark:hover:text-sail-100 transition-colors">
            &larr; Back to Support Cards
        </a>
    </div>

    <form method="POST" action="{{ route('admin.support-cards.store') }}" enctype="multipart/form-data"
          class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-6 sm:p-8 space-y-6 shadow-sm">
        @csrf

        @php
            $inp = 'w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors';
        @endphp

        <div class="space-y-4">
            <!-- Name & Epithet -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Card Name (Global English) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" x-model="name" value="{{ old('name') }}" required
                           placeholder="e.g. Kitasan Black" class="{{ $inp }}">
                </div>

                <div>
                    <label for="epithet" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Card Epithet / Subtitle
                    </label>
                    <input type="text" id="epithet" name="epithet" x-model="epithet" value="{{ old('epithet') }}"
                           placeholder="e.g. Fire at My Heels" class="{{ $inp }}">
                </div>
            </div>

            <!-- Slug & Specialty Priority -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Slug <span class="text-slate-400 font-normal lowercase">(auto-generated)</span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                           placeholder="e.g. ssr-kitasan-black" class="{{ $inp }}">
                </div>

                <div>
                    <label for="specialty_priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Specialty Priority (0 - 100)
                    </label>
                    <input type="number" id="specialty_priority" name="specialty_priority" min="0" max="100" value="{{ old('specialty_priority') }}"
                           placeholder="e.g. 80" class="{{ $inp }}">
                </div>
            </div>

            <!-- Rarity & Card Type -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="rarity" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Rarity Tier <span class="text-rose-500">*</span>
                    </label>
                    <select id="rarity" name="rarity" x-model="rarity" required class="{{ $inp }}">
                        @foreach ($rarities as $r)
                            <option value="{{ $r }}" {{ old('rarity', 'SSR') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="card_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Card Type (Stat Training) <span class="text-rose-500">*</span>
                    </label>
                    <select id="card_type" name="card_type" x-model="cardType" required class="{{ $inp }}">
                        @foreach ($cardTypes as $t)
                            <option value="{{ $t }}" {{ old('card_type', 'Speed') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Key Skills (Comma separated) -->
            <div>
                <label for="key_skills" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                    Key Gold / Hint Skills <span class="text-slate-400 font-normal lowercase">(comma-separated)</span>
                </label>
                <input type="text" id="key_skills" name="key_skills" value="{{ old('key_skills') }}"
                       placeholder="e.g. Professor of Curvature, Corner Specialist, Slipstream" class="{{ $inp }}">
            </div>

            <!-- Summary / Tactical Evaluation -->
            <div>
                <label for="summary" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                    Tactical Summary & Tier Evaluation
                </label>
                <textarea id="summary" name="summary" rows="3"
                          placeholder="e.g. Premier Speed support card with top-tier specialty rate and universally powerful gold corner velocity skill." class="{{ $inp }}">{{ old('summary') }}</textarea>
            </div>

            <!-- Source Reference URL (GameTora) -->
            <div>
                <label for="source_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                    Source URL (GameTora)
                </label>
                <input type="url" id="source_url" name="source_url" value="{{ old('source_url') }}"
                       placeholder="https://gametora.com/umamusume/supports/..." class="{{ $inp }}">
                <p class="text-[10px] text-slate-400 font-mono mt-1">Direct GameTora support card link. Leave empty to auto-link to https://gametora.com/umamusume/supports/{slug}.</p>
            </div>

            <!-- Card Artwork Sourcing (Dual inputs) -->
            <div class="pt-4 border-t border-sail-200 dark:border-navy-750 space-y-4">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-navy-950 dark:text-sail-100 font-mono">
                        Support Card Artwork Sourcing
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Choose either GameTora Card URL or upload a file directly.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-start">
                    <!-- Live Preview Card -->
                    <div class="sm:col-span-4 bg-sail-50 dark:bg-navy-900/60 border border-sail-200 dark:border-navy-700 rounded-md p-4 text-center space-y-2">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block font-mono">Card Preview</span>
                        <div class="w-24 h-32 mx-auto rounded-md overflow-hidden bg-white dark:bg-navy-950 border border-brass-500/50 flex items-center justify-center shadow-inner">
                            <template x-if="cardPreview">
                                <img :src="cardPreview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!cardPreview">
                                <span class="font-bold text-brass-500 text-xs font-mono">CARD ART</span>
                            </template>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-navy-950 dark:text-sail-100 truncate" x-text="name || 'Card Name'"></div>
                            <div class="text-[10px] text-brass-500 font-mono mt-0.5" x-text="rarity + ' &bull; ' + cardType"></div>
                        </div>
                    </div>

                    <!-- Dual Inputs -->
                    <div class="sm:col-span-8 space-y-3">
                        <div class="p-3.5 rounded-md bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 space-y-1.5">
                            <label for="gametora_card_url" class="block text-xs font-semibold text-cerulean-600 dark:text-cerulean-400 font-mono">
                                Option 1 &bull; Paste GameTora Support Card Image URL
                            </label>
                            <input type="url" id="gametora_card_url" name="gametora_card_url"
                                   x-model="gametoraUrl" @input="onUrlChange()"
                                   placeholder="https://gametora.com/images/umamusume/supports/..."
                                   class="{{ $inp }}">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Auto-downloads to local storage via GameTora asset service.</p>
                        </div>

                        <div class="p-3.5 rounded-md bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 space-y-1.5">
                            <label for="card_image" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 font-mono">
                                Option 2 &bull; Manual File Upload
                            </label>
                            <input type="file" id="card_image" name="card_image" accept="image/webp,image/png,image/jpeg"
                                   @change="onCardChange($event)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-sail-200 dark:file:bg-navy-700 file:text-slate-800 dark:file:text-slate-200 hover:file:bg-sail-300 transition-colors">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">WebP, PNG, JPG (max 2MB).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-sail-200 dark:border-navy-750 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.support-cards.index') }}" class="px-3.5 py-2 rounded-md text-xs font-semibold text-slate-500 hover:text-navy-950 dark:hover:text-sail-100 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm font-mono">
                Save Support Card
            </button>
        </div>
    </form>
</div>
@endsection
