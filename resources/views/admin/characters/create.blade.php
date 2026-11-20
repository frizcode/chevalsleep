@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Add New Character</h1>
            <p class="text-sm text-slate-400 mt-1">Register an Uma Musume racer to the character database.</p>
        </div>

        <a href="{{ route('admin.characters.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            &larr; Back to Characters
        </a>
    </div>

    <form method="POST" action="{{ route('admin.characters.store') }}" enctype="multipart/form-data" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm">
        @csrf

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Character Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Agnes Digital"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label for="variant_title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Variant Title
                    </label>
                    <input type="text" id="variant_title" name="variant_title" value="{{ old('variant_title', 'Original') }}"
                           placeholder="e.g. Original, Alter, Summer, New Year"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Slug <span class="text-slate-500 font-normal lowercase">(leave blank to auto-generate)</span>
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                       placeholder="e.g. agnes-digital-alter"
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
                            <option value="{{ $style }}" {{ old('default_running_style') === $style ? 'selected' : '' }}>
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
                        <option value="3" {{ old('rarity', 3) == 3 ? 'selected' : '' }}>3★ (Three Stars)</option>
                        <option value="2" {{ old('rarity') == 2 ? 'selected' : '' }}>2★ (Two Stars)</option>
                        <option value="1" {{ old('rarity') == 1 ? 'selected' : '' }}>1★ (One Star)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Avatar Image <span class="text-slate-500 font-normal lowercase">(webp, png, jpg &bull; max 2MB)</span>
                </label>
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
                Save Character
            </button>
        </div>
    </form>
</div>
@endsection
