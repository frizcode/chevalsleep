<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Services\GameToraAssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function __construct(
        protected GameToraAssetService $assetService
    ) {}

    /**
     * Display a listing of skills in Admin Master Database.
     */
    public function index(Request $request): View
    {
        $skills = Skill::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = strtolower($request->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('activation_condition', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('rarity') && $request->rarity !== 'All', function ($q) use ($request) {
                $q->where('rarity', $request->rarity);
            })
            ->when($request->filled('type') && $request->type !== 'All', function ($q) use ($request) {
                $q->where('skill_type', $request->type);
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.skills.index', [
            'skills' => $skills,
            'rarities' => ['Normal', 'Rare', 'Unique', 'Inherited'],
            'skillTypes' => ['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive'],
            'runningStyles' => ['Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
            'distanceTypes' => ['Universal', 'Sprint', 'Mile', 'Medium', 'Long'],
        ]);
    }

    /**
     * Show form for creating a new skill.
     */
    public function create(): View
    {
        return view('admin.skills.create', [
            'rarities' => ['Normal', 'Rare', 'Unique', 'Inherited'],
            'skillTypes' => ['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive'],
            'runningStyles' => ['Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
            'distanceTypes' => ['Universal', 'Sprint', 'Mile', 'Medium', 'Long'],
        ]);
    }

    /**
     * Store a newly created skill.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:skills,slug'],
            'rarity' => ['required', Rule::in(['Normal', 'Rare', 'Unique', 'Inherited'])],
            'skill_type' => ['required', Rule::in(['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive'])],
            'running_style' => ['required', Rule::in(['Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'])],
            'distance_type' => ['required', Rule::in(['Universal', 'Sprint', 'Mile', 'Medium', 'Long'])],
            'activation_condition' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'image', 'mimes:webp,png,jpg,jpeg,svg', 'max:2048'],
            'gametora_icon_url' => ['nullable', 'url'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Skill::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Option 1: GameTora icon URL import
        if ($request->filled('gametora_icon_url')) {
            $importedPath = $this->assetService->importFromUrl($request->input('gametora_icon_url'), 'skills');
            if ($importedPath) {
                $validated['icon_webp'] = $importedPath;
            }
        }

        // Option 2: Manual file upload
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = $validated['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('skills', $filename, 'public');
            $validated['icon_webp'] = $path;
        }

        unset($validated['icon'], $validated['gametora_icon_url']);

        $skill = Skill::create($validated);

        return redirect()->route('admin.skills.index')
            ->with('status', "Skill '{$skill->name}' created successfully.");
    }

    /**
     * Show form for editing an existing skill.
     */
    public function edit(Skill $skill): View
    {
        return view('admin.skills.edit', [
            'skill' => $skill,
            'rarities' => ['Normal', 'Rare', 'Unique', 'Inherited'],
            'skillTypes' => ['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive'],
            'runningStyles' => ['Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
            'distanceTypes' => ['Universal', 'Sprint', 'Mile', 'Medium', 'Long'],
        ]);
    }

    /**
     * Update an existing skill.
     */
    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('skills', 'slug')->ignore($skill->id)],
            'rarity' => ['required', Rule::in(['Normal', 'Rare', 'Unique', 'Inherited'])],
            'skill_type' => ['required', Rule::in(['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive'])],
            'running_style' => ['required', Rule::in(['Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'])],
            'distance_type' => ['required', Rule::in(['Universal', 'Sprint', 'Mile', 'Medium', 'Long'])],
            'activation_condition' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'image', 'mimes:webp,png,jpg,jpeg,svg', 'max:2048'],
            'gametora_icon_url' => ['nullable', 'url'],
        ]);

        if ($request->filled('gametora_icon_url')) {
            $importedPath = $this->assetService->importFromUrl($request->input('gametora_icon_url'), 'skills');
            if ($importedPath) {
                if ($skill->icon_webp && ! filter_var($skill->icon_webp, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($skill->icon_webp);
                }
                $validated['icon_webp'] = $importedPath;
            }
        }

        if ($request->hasFile('icon')) {
            if ($skill->icon_webp && ! filter_var($skill->icon_webp, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($skill->icon_webp);
            }
            $file = $request->file('icon');
            $filename = $validated['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('skills', $filename, 'public');
            $validated['icon_webp'] = $path;
        }

        unset($validated['icon'], $validated['gametora_icon_url']);

        $skill->update($validated);

        return redirect()->route('admin.skills.index')
            ->with('status', "Skill '{$skill->name}' updated successfully.");
    }

    /**
     * Remove the specified skill.
     */
    public function destroy(Skill $skill): RedirectResponse
    {
        if ($skill->icon_webp && ! filter_var($skill->icon_webp, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($skill->icon_webp);
        }

        $name = $skill->name;
        $skill->delete();

        return redirect()->route('admin.skills.index')
            ->with('status', "Skill '{$name}' deleted successfully.");
    }
}
