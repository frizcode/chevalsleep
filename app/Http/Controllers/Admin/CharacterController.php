<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Services\GameToraAssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CharacterController extends Controller
{
    public function __construct(
        protected GameToraAssetService $assetService
    ) {}

    /**
     * Display a listing of the characters.
     */
    public function index(): View
    {
        $characters = Character::withCount('tierEntries')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.characters.index', [
            'characters' => $characters,
        ]);
    }

    /**
     * Show the form for creating a new character.
     */
    public function create(): View
    {
        return view('admin.characters.create', [
            'runningStyles' => ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
        ]);
    }

    /**
     * Store a newly created character in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'variant_title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:characters,slug'],
            'default_running_style' => ['required', Rule::in(['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'])],
            'rarity' => ['required', 'integer', 'min:1', 'max:3'],
            'aptitude_notes' => ['nullable', 'string'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:webp,png,jpg,jpeg', 'max:2048'],
            'gametora_avatar_url' => ['nullable', 'url'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name'] . ($validated['variant_title'] ? '-' . $validated['variant_title'] : ''));
            $slug = $baseSlug;
            $counter = 1;
            while (Character::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Option 1: GameTora image URL import
        if ($request->filled('gametora_avatar_url')) {
            $importedPath = $this->assetService->importFromUrl($request->input('gametora_avatar_url'), 'characters');
            if ($importedPath) {
                $validated['avatar_webp'] = $importedPath;
            }
        }

        // Option 2: Manual file upload (takes precedence if both provided)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = $validated['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('characters', $filename, 'public');
            $validated['avatar_webp'] = $path;
        }

        unset($validated['avatar'], $validated['gametora_avatar_url']);

        $character = Character::create($validated);

        return redirect()->route('admin.characters.index')
            ->with('status', "Character '{$character->full_name}' created successfully.");
    }

    /**
     * Show the form for editing the specified character.
     */
    public function edit(Character $character): View
    {
        return view('admin.characters.edit', [
            'character' => $character,
            'runningStyles' => ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
        ]);
    }

    /**
     * Update the specified character in storage.
     */
    public function update(Request $request, Character $character): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'variant_title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('characters', 'slug')->ignore($character->id)],
            'default_running_style' => ['required', Rule::in(['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'])],
            'rarity' => ['required', 'integer', 'min:1', 'max:3'],
            'aptitude_notes' => ['nullable', 'string'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:webp,png,jpg,jpeg', 'max:2048'],
            'gametora_avatar_url' => ['nullable', 'url'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name'] . ($validated['variant_title'] ? '-' . $validated['variant_title'] : ''));
            $slug = $baseSlug;
            $counter = 1;
            while (Character::where('slug', $slug)->where('id', '!=', $character->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $newAvatarPath = null;

        // Option 1: GameTora image URL import
        if ($request->filled('gametora_avatar_url')) {
            $imported = $this->assetService->importFromUrl($request->input('gametora_avatar_url'), 'characters');
            if ($imported) {
                $newAvatarPath = $imported;
            }
        }

        // Option 2: Manual file upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = $validated['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $newAvatarPath = $file->storeAs('characters', $filename, 'public');
        }

        if ($newAvatarPath) {
            if ($character->avatar_webp && Storage::disk('public')->exists($character->avatar_webp)) {
                Storage::disk('public')->delete($character->avatar_webp);
            }
            $validated['avatar_webp'] = $newAvatarPath;
        }

        unset($validated['avatar'], $validated['gametora_avatar_url']);

        $character->update($validated);

        return redirect()->route('admin.characters.index')
            ->with('status', "Character '{$character->full_name}' updated successfully.");
    }

    /**
     * Remove the specified character from storage.
     */
    public function destroy(Character $character): RedirectResponse
    {
        $name = $character->full_name;

        if ($character->avatar_webp && Storage::disk('public')->exists($character->avatar_webp)) {
            Storage::disk('public')->delete($character->avatar_webp);
        }

        $character->delete();

        return redirect()->route('admin.characters.index')
            ->with('status', "Character '{$name}' deleted successfully.");
    }
}
