@extends('layouts.admin')

@section('page-title', 'Add New Character')

@section('content')
<div x-data="{
    avatarPreview: null,
    gametoraUrl: '{{ old('gametora_avatar_url', '') }}',
    rarity: {{ old('rarity', 3) }},
    name: '{{ old('name', '') }}',
    variant: '{{ old('variant_title', 'Original') }}',
    style: '{{ old('default_running_style', 'Front Runner') }}',

    init() {
        if (this.gametoraUrl) {
            this.avatarPreview = this.gametoraUrl;
        }
    },

    onAvatarChange(e) {
        if (e.target.files && e.target.files[0]) {
            this.avatarPreview = URL.createObjectURL(e.target.files[0]);
        }
    },

    onUrlChange() {
        if (this.gametoraUrl.trim().startsWith('http')) {
            this.avatarPreview = this.gametoraUrl.trim();
        }
    }
}" class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-navy-900 dark:text-sail-50 tracking-tight font-sans">Add New Character</h1>
            <p class="text-xs text-sail-500 dark:text-sail-400 mt-1">Register a racer into the Uma Musume character database.</p>
        </div>

        <a href="{{ route('admin.characters.index') }}" class="text-xs font-semibold text-sail-500 hover:text-navy-900 dark:hover:text-sail-100 transition-colors">
            &larr; Back to Characters
        </a>
    </div>

    <form method="POST" action="{{ route('admin.characters.store') }}" enctype="multipart/form-data"
          class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-6 sm:p-8 space-y-6 shadow-sm">
        @csrf

        @php
            $inp = 'w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-sail-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors';
        @endphp

        <div class="space-y-5">
            <!-- Name & Variant -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1.5 font-mono">
                        Character Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" x-model="name" value="{{ old('name') }}" required
                           placeholder="e.g. Agnes Digital"
                           class="{{ $inp }}">
                </div>

                <div>
                    <label for="variant_title" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1.5 font-mono">
                        Variant Title
                    </label>
                    <input type="text" id="variant_title" name="variant_title" x-model="variant" value="{{ old('variant_title', 'Original') }}"
                           placeholder="e.g. Original, Alter, Summer, New Year"
                           class="{{ $inp }}">
                </div>
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1.5 font-mono">
                    Slug <span class="text-sail-400 font-normal lowercase">(leave blank to auto-generate)</span>
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                       placeholder="e.g. agnes-digital"
                       class="{{ $inp }}">
            </div>

            <!-- Running Style & Rarity -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="default_running_style" class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1.5 font-mono">
                        Default Running Style <span class="text-rose-500">*</span>
                    </label>
                    <select id="default_running_style" name="default_running_style" x-model="style" required
                            class="{{ $inp }}">
                        @foreach ($runningStyles as $st)
                            <option value="{{ $st }}" {{ old('default_running_style') === $st ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-sail-600 dark:text-sail-400 mb-1.5 font-mono">
                        Base Rarity (Stars) <span class="text-rose-500">*</span>
                    </label>
                    <input type="hidden" name="rarity" :value="rarity">
                    <div class="flex items-center space-x-2 pt-0.5">
                        <template x-for="star in [1, 2, 3]" :key="star">
                            <button type="button" @click="rarity = star"
                                    class="p-2 rounded-md border transition-colors flex items-center space-x-1"
                                    :class="rarity >= star ? 'bg-brass-500/10 border-brass-500/40 text-brass-500' : 'bg-sail-50 dark:bg-navy-900 border-sail-200 dark:border-navy-700 text-sail-400'">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-xs font-semibold font-mono" x-text="star + '★'"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Avatar: GameTora Asset Importer OR Manual Upload -->
            <div class="pt-4 border-t border-sail-200 dark:border-navy-750 space-y-4">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-navy-900 dark:text-sail-100 font-mono">
                        Avatar Image Sourcing
                    </h3>
                    <p class="text-[11px] text-sail-500 dark:text-sail-400">Choose either GameTora Image URL or upload a file directly.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-start">
                    <!-- Live Preview Card -->
                    <div class="sm:col-span-4 bg-sail-50 dark:bg-navy-900/60 border border-sail-200 dark:border-navy-700 rounded-lg p-4 text-center space-y-2">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-sail-400 block font-mono">Preview</span>
                        <div class="w-16 h-16 mx-auto rounded-md overflow-hidden bg-white dark:bg-navy-950 border border-brass-500/50 flex items-center justify-center">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!avatarPreview">
                                <div class="text-brass-500 font-bold text-sm font-mono">
                                    <span x-text="name ? name.substring(0, 2).toUpperCase() : 'UM'"></span>
                                </div>
                            </template>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-navy-900 dark:text-sail-100 truncate" x-text="name || 'Character Name'"></div>
                            <div class="text-[10px] text-sail-400" x-text="variant || 'Original'"></div>
                            <div class="text-[10px] font-semibold text-brass-500 font-mono mt-1" x-text="rarity + '★ &bull; ' + style"></div>
                        </div>
                    </div>

                    <!-- Dual Inputs (Options 1 & 2) -->
                    <div class="sm:col-span-8 space-y-3">
                        <!-- Option 1: GameTora URL -->
                        <div class="p-3.5 rounded-md bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 space-y-1.5">
                            <label for="gametora_avatar_url" class="block text-xs font-semibold text-cerulean-600 dark:text-cerulean-400 font-mono">
                                Option 1 &bull; Paste GameTora Image URL
                            </label>
                            <input type="url" id="gametora_avatar_url" name="gametora_avatar_url"
                                   x-model="gametoraUrl" @input="onUrlChange()"
                                   placeholder="https://gametora.com/images/umamusume/characters/..."
                                   class="{{ $inp }}">
                            <p class="text-[10px] text-sail-500 dark:text-sail-400">Auto-downloads to local storage via GameTora asset service.</p>
                        </div>

                        <!-- Option 2: File Upload -->
                        <div class="p-3.5 rounded-md bg-sail-50/70 dark:bg-navy-900/40 border border-sail-200 dark:border-navy-700 space-y-1.5">
                            <label for="avatar" class="block text-xs font-semibold text-sail-700 dark:text-sail-300 font-mono">
                                Option 2 &bull; Manual File Upload
                            </label>
                            <input type="file" id="avatar" name="avatar" accept="image/webp,image/png,image/jpeg"
                                   @change="onAvatarChange($event)"
                                   class="w-full text-xs text-sail-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-sail-200 dark:file:bg-navy-700 file:text-sail-800 dark:file:text-sail-200 hover:file:bg-sail-300 transition-colors">
                            <p class="text-[10px] text-sail-500 dark:text-sail-400">WebP, PNG, JPG (max 2MB).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-sail-200 dark:border-navy-750 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.characters.index') }}" class="px-3.5 py-2 rounded-md text-xs font-semibold text-sail-500 hover:text-navy-900 dark:hover:text-sail-100 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm">
                Save Character
            </button>
        </div>
    </form>
</div>
@endsection
