@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Characters Database</h1>
            <p class="text-xs text-slate-400 mt-1">Manage Uma Musume racers, variants, styles, and avatars.</p>
        </div>

        <a href="{{ route('admin.characters.create') }}"
           class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-950/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Character</span>
        </a>
    </div>

    <!-- Character Table Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/70 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-5 font-bold">Racer Info</th>
                        <th class="py-3.5 px-4 font-bold">Running Style</th>
                        <th class="py-3.5 px-4 font-bold">Base Rarity</th>
                        <th class="py-3.5 px-4 font-bold">Tier Usage</th>
                        <th class="py-3.5 px-5 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse ($characters as $character)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3.5">
                                    <!-- Avatar or Initials Fallback -->
                                    @if ($character->avatar_url)
                                        <img src="{{ $character->avatar_url }}" alt="{{ $character->name }}"
                                             class="w-11 h-11 rounded-2xl object-cover border-2 bg-slate-950 {{ $character->rarity === 3 ? 'border-amber-500/60 shadow-[0_0_8px_rgba(245,158,11,0.2)]' : 'border-slate-700' }}">
                                    @else
                                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 border-2 border-slate-700 flex items-center justify-center font-black text-xs text-amber-400 shadow-inner">
                                            {{ strtoupper(substr($character->name, 0, 2)) }}
                                        </div>
                                    @endif

                                    <div>
                                        <div class="font-bold text-white text-sm flex items-center space-x-2">
                                            <span>{{ $character->name }}</span>
                                            @if ($character->variant_title && $character->variant_title !== 'Original')
                                                <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                                    {{ $character->variant_title }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-mono text-slate-500">{{ $character->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $styleColors = [
                                        'Front Runner' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30',
                                        'Pace Chaser'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'Late Surger'  => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'End Closer'   => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                    ];
                                    $colorClass = $styleColors[$character->default_running_style] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-semibold border {{ $colorClass }}">
                                    {{ $character->default_running_style }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center text-amber-400">
                                    @for ($i = 0; $i < $character->rarity; $i++)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                    <span class="text-xs text-slate-400 ml-1.5 font-bold font-mono">{{ $character->rarity }}★</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-extrabold text-white font-mono">{{ $character->tier_entries_count }}</span>
                                <span class="text-xs text-slate-500">tiers</span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.characters.edit', $character) }}"
                                       class="px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-colors"
                                       title="Edit Character">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.characters.destroy', $character) }}" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete {{ $character->full_name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-xl text-rose-400 hover:text-rose-200 hover:bg-rose-950/40 transition-colors"
                                                title="Delete Character">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-500 italic">
                                No characters registered in database pool.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($characters->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $characters->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
