@extends('layouts.admin')

@section('content')
<div x-data="{
    avatarPreview: null,
    rarity: {{ old('rarity', 3) }},
    onAvatarChange(e) {
        if (e.target.files && e.target.files[0]) {
            this.avatarPreview = URL.createObjectURL(e.target.files[0]);
        }
    }
}" class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Add New Character</h1>
            <p class="text-xs text-slate-400 mt-1">Register a racer into the Uma Musume character database.</p>
        </div>

        <a href="{{ route('admin.characters.index') }}" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
            &larr; Back to Characters
        </a>
    </div>

    <form method="POST" action="{{ route('admin.characters.store') }}" enctype="multipart/form-data" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl backdrop-blur-xl">
        @csrf

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Character Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Agnes Digital"
                           class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                </div>

                <div>
                    <label for="variant_title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Variant Title
                    </label>
                    <input type="text" id="variant_title" name="variant_title" value="{{ old('variant_title', 'Original') }}"
                           placeholder="e.g. Original, Alter, Summer, New Year"
                           class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                </div>
            </div>

            <div>
                <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Slug <span class="text-slate-500 font-normal lowercase">(leave blank to auto-generate)</span>
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                       placeholder="e.g. agnes-digital-alter"
                       class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="default_running_style" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Default Running Style <span class="text-rose-400">*</span>
                    </label>
                    <select id="default_running_style" name="default_running_style" required
                            class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-white focus:outline-none focus:border-amber-500">
                        @foreach ($runningStyles as $style)
                            <option value="{{ $style }}" {{ old('default_running_style') === $style ? 'selected' : '' }}>
                                {{ $style }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Base Rarity (Stars) <span class="text-rose-400">*</span>
                    </label>
                    <input type="hidden" name="rarity" :value="rarity">
                    <div class="flex items-center space-x-2 pt-1">
                        <template x-for="star in [1, 2, 3]" :key="star">
                            <button type="button" @click="rarity = star"
                                    class="p-2 rounded-xl border transition-all flex items-center space-x-1"
                                    :class="rarity >= star ? 'bg-amber-500/10 border-amber-500/40 text-amber-400 shadow-sm' : 'bg-slate-950 border-slate-800 text-slate-600'">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-xs font-bold" x-text="star + '★'"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Avatar Upload with Live Image Preview -->
            <div class="space-y-2 pt-2 border-t border-slate-800">
                <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                    Avatar Image <span class="text-slate-500 font-normal lowercase">(webp, png, jpg &bull; max 2MB)</span>
                </label>
                
                <div class="flex items-center space-x-4">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" alt="Preview"
                             class="w-16 h-16 rounded-2xl object-cover bg-slate-950 border-2 border-amber-500/60 shadow-lg shadow-amber-950/30">
                    </template>
                    <template x-if="!avatarPreview">
                        <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-dashed border-slate-700 flex items-center justify-center text-slate-500 text-xs text-center p-1">
                            No image
                        </div>
                    </template>

                    <input type="file" id="avatar" name="avatar" accept="image/webp,image/png,image/jpeg"
                           @change="onAvatarChange($event)"
                           class="flex-1 bg-slate-950 border border-slate-700/80 rounded-2xl px-4 py-2 text-xs text-slate-300 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.characters.index') }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-400 hover:text-white">
                Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-950/30 transition-all">
                Save Character
            </button>
        </div>
    </form>
</div>
@endsection
