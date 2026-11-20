@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Edit Character: {{ $character->full_name }}</h1>
            <p class="text-sm text-slate-400 mt-1">Update racer details and profile image.</p>
        </div>

        <a href="{{ route('admin.characters.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            &larr; Back to Characters
        </a>
    </div>

    <form method="POST" action="{{ route('admin.characters.update', $character) }}" enctype="multipart/form-data" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Character Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $character->name) }}" required
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label for="variant_title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Variant Title
                    </label>
                    <input type="text" id="variant_title" name="variant_title" value="{{ old('variant_title', $character->variant_title) }}"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Slug <span class="text-slate-500 font-normal lowercase">(unique identifier)</span>
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $character->slug) }}"
                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="default_running_style" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Default Running Style <span class="text-rose-400">*</span>
                    </label>
                    <select id="default_running_style" name="default_running_style" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                        @foreach ($runningStyles as $style)
                            <option value="{{ $style }}" {{ old('default_running_style', $character->default_running_style) === $style ? 'selected' : '' }}>
                                {{ $style }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rarity" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Base Rarity (Stars) <span class="text-rose-400">*</span>
                    </label>
                    <select id="rarity" name="rarity" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                        <option value="3" {{ old('rarity', $character->rarity) == 3 ? 'selected' : '' }}>3★ (Three Stars)</option>
                        <option value="2" {{ old('rarity', $character->rarity) == 2 ? 'selected' : '' }}>2★ (Two Stars)</option>
                        <option value="1" {{ old('rarity', $character->rarity) == 1 ? 'selected' : '' }}>1★ (One Star)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Replace Avatar Image <span class="text-slate-500 font-normal lowercase">(optional &bull; webp, png, jpg)</span>
                </label>
                @if ($character->avatar_url)
                    <div class="flex items-center space-x-3 mb-2">
                        <img src="{{ $character->avatar_url }}" alt="{{ $character->name }}"
                             class="w-12 h-12 rounded-xl object-cover border border-slate-700">
                        <span class="text-xs text-slate-400">Current avatar will be replaced if a new file is uploaded.</span>
                    </div>
                @endif
                <input type="file" id="avatar" name="avatar" accept="image/webp,image/png,image/jpeg"
                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2 text-sm text-slate-300 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.characters.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-400 hover:text-white">
                Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-950/30 transition-all">
                Update Character
            </button>
        </div>
    </form>
</div>
@endsection
