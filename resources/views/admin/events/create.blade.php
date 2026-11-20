@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Create New Event</h1>
            <p class="text-sm text-slate-400 mt-1">Configure a tournament, championship, or League of Heroes event.</p>
        </div>

        <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            &larr; Back to Events
        </a>
    </div>

    <form method="POST" action="{{ route('admin.events.store') }}" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Event Title <span class="text-rose-400">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. CM #22 Aquarius Cup"
                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Slug <span class="text-slate-500 font-normal lowercase">(leave blank to auto-generate)</span>
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                       placeholder="e.g. cm-22-aquarius-cup"
                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="track_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Track Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" id="track_name" name="track_name" value="{{ old('track_name') }}" required
                           placeholder="e.g. Tokyo 1600m"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label for="distance_meters" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Distance (Meters) <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" id="distance_meters" name="distance_meters" value="{{ old('distance_meters', 1600) }}" required min="800" max="3600"
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="distance_type" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Distance Type <span class="text-rose-400">*</span>
                    </label>
                    <select id="distance_type" name="distance_type" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                        @foreach ($distanceTypes as $type)
                            <option value="{{ $type }}" {{ old('distance_type') === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="surface" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Track Surface <span class="text-rose-400">*</span>
                    </label>
                    <select id="surface" name="surface" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                        @foreach ($surfaces as $surface)
                            <option value="{{ $surface }}" {{ old('surface') === $surface ? 'selected' : '' }}>
                                {{ $surface }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <label class="relative flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                           class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <span class="text-sm font-semibold text-slate-300">Set as Active Default Event</span>
                </label>
                <p class="text-xs text-slate-500 mt-1 pl-7">Setting this active will automatically deactivate any other active tournament.</p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.events.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-400 hover:text-white">
                Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-950/30 transition-all">
                Save Event
            </button>
        </div>
    </form>
</div>
@endsection
