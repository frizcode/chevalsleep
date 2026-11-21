@extends('layouts.admin')

@section('page-title', 'Add New Skill')

@section('content')
<div x-data="{
    iconPreview: null,
    gametoraUrl: '{{ old('gametora_icon_url', '') }}',
    name: '{{ old('name', '') }}',
    rarity: '{{ old('rarity', 'Rare') }}',
    type: '{{ old('skill_type', 'Velocity') }}',

    init() {
        if (this.gametoraUrl) {
            this.iconPreview = this.gametoraUrl;
        }
    },

    onIconChange(e) {
        if (e.target.files && e.target.files[0]) {
            this.iconPreview = URL.createObjectURL(e.target.files[0]);
        }
    },

    onUrlChange() {
        if (this.gametoraUrl.trim().startsWith('http')) {
            this.iconPreview = this.gametoraUrl.trim();
        }
    }
}" class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-navy-950 dark:text-sail-50 tracking-tight">Add New Skill</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Register a skill in the Uma Musume Master Database.</p>
        </div>

        <a href="{{ route('admin.skills.index') }}" class="text-xs font-semibold text-slate-500 hover:text-navy-950 dark:hover:text-sail-100 transition-colors">
            &larr; Back to Skills
        </a>
    </div>

    <form method="POST" action="{{ route('admin.skills.store') }}" enctype="multipart/form-data"
          class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-6 sm:p-8 space-y-6 shadow-sm">
        @csrf

        @php
            $inp = 'w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors';
        @endphp

        <div class="space-y-4">
            <!-- Name & Slug -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Skill Name (Global English) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" x-model="name" value="{{ old('name') }}" required
                           placeholder="e.g. No Stopping Me!" class="{{ $inp }}">
                </div>

                <div>
                    <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Slug <span class="text-slate-400 font-normal lowercase">(auto-generated)</span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                           placeholder="e.g. no-stopping-me" class="{{ $inp }}">
                </div>
            </div>

            <!-- Rarity & Type -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="rarity" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Rarity Tier <span class="text-rose-500">*</span>
                    </label>
                    <select id="rarity" name="rarity" x-model="rarity" required class="{{ $inp }}">
                        @foreach ($rarities as $r)
                            <option value="{{ $r }}" {{ old('rarity', 'Rare') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="skill_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Skill Type <span class="text-rose-500">*</span>
                    </label>
                    <select id="skill_type" name="skill_type" x-model="type" required class="{{ $inp }}">
                        @foreach ($skillTypes as $t)
                            <option value="{{ $t }}" {{ old('skill_type', 'Velocity') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Style & Distance -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="running_style" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Running Style Requirement <span class="text-rose-500">*</span>
                    </label>
                    <select id="running_style" name="running_style" required class="{{ $inp }}">
                        @foreach ($runningStyles as $st)
                            <option value="{{ $st }}" {{ old('running_style', 'Universal') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="distance_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                        Distance Requirement <span class="text-rose-500">*</span>
                    </label>
                    <select id="distance_type" name="distance_type" required class="{{ $inp }}">
                        @foreach ($distanceTypes as $dt)
                            <option value="{{ $dt }}" {{ old('distance_type', 'Universal') === $dt ? 'selected' : '' }}>{{ $dt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Activation Condition -->
            <div>
                <label for="activation_condition" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                    Activation Condition Trigger
                </label>
                <input type="text" id="activation_condition" name="activation_condition" value="{{ old('activation_condition') }}"
                       placeholder="e.g. Final corner, position 4th-9th (Pace Chaser, Late Surger)" class="{{ $inp }}">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                    Description & Effect Formula
                </label>
                <textarea id="description" name="description" rows="2"
                          placeholder="e.g. Increases acceleration by 0.4 m/s² for 3 seconds." class="{{ $inp }}">{{ old('description') }}</textarea>
            </div>

            <!-- Icon Sourcing (Dual inputs) -->
            <div class="pt-4 border-t border-sail-200 dark:border-navy-750 space-y-4">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-navy-950 dark:text-sail-100 font-mono">
                        Skill Icon Sourcing
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Choose either GameTora Icon URL or upload a file directly.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-start">
                    <!-- Live Preview Card -->
                    <div class="sm:col-span-4 bg-sail-50 dark:bg-navy-900/60 border border-sail-200 dark:border-navy-700 rounded-md p-4 text-center space-y-2">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block font-mono">Icon Preview</span>
                        <div class="w-14 h-14 mx-auto rounded-md overflow-hidden bg-white dark:bg-navy-950 border border-brass-500/50 flex items-center justify-center">
                            <template x-if="iconPreview">
                                <img :src="iconPreview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!iconPreview">
                                <span class="font-bold text-brass-500 text-xs font-mono">SKILL</span>
                            </template>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-navy-950 dark:text-sail-100 truncate" x-text="name || 'Skill Name'"></div>
                            <div class="text-[10px] text-brass-500 font-mono mt-0.5" x-text="rarity + ' &bull; ' + type"></div>
                        </div>
                    </div>

                    <!-- Dual Inputs -->
                    <div class="sm:col-span-8 space-y-3">
                        <div class="p-3.5 rounded-md bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 space-y-1.5">
                            <label for="gametora_icon_url" class="block text-xs font-semibold text-cerulean-600 dark:text-cerulean-400 font-mono">
                                Option 1 &bull; Paste GameTora Skill Icon URL
                            </label>
                            <input type="url" id="gametora_icon_url" name="gametora_icon_url"
                                   x-model="gametoraUrl" @input="onUrlChange()"
                                   placeholder="https://gametora.com/images/umamusume/skills/..."
                                   class="{{ $inp }}">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Auto-downloads to local storage via GameTora asset service.</p>
                        </div>

                        <div class="p-3.5 rounded-md bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 space-y-1.5">
                            <label for="icon" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 font-mono">
                                Option 2 &bull; Manual File Upload
                            </label>
                            <input type="file" id="icon" name="icon" accept="image/webp,image/png,image/jpeg,image/svg+xml"
                                   @change="onIconChange($event)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-sail-200 dark:file:bg-navy-700 file:text-slate-800 dark:file:text-slate-200 hover:file:bg-sail-300 transition-colors">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">WebP, PNG, SVG (max 2MB).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-sail-200 dark:border-navy-750 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.skills.index') }}" class="px-3.5 py-2 rounded-md text-xs font-semibold text-slate-500 hover:text-navy-950 dark:hover:text-sail-100 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm font-mono">
                Save Skill
            </button>
        </div>
    </form>
</div>
@endsection
