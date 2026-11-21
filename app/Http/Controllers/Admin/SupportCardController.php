<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportCard;
use App\Services\GameToraAssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportCardController extends Controller
{
    public function __construct(
        protected GameToraAssetService $assetService
    ) {}

    /**
     * Display a listing of Support Cards in Admin Master Database.
     */
    public function index(Request $request): View
    {
        $cards = SupportCard::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = strtolower($request->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('epithet', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('rarity') && $request->rarity !== 'All', function ($q) use ($request) {
                $q->where('rarity', $request->rarity);
            })
            ->when($request->filled('type') && $request->type !== 'All', function ($q) use ($request) {
                $q->where('card_type', $request->type);
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.support-cards.index', [
            'cards' => $cards,
            'rarities' => ['SSR', 'SR', 'R'],
            'cardTypes' => ['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group'],
        ]);
    }

    /**
     * Show form for creating a new support card.
     */
    public function create(): View
    {
        return view('admin.support-cards.create', [
            'rarities' => ['SSR', 'SR', 'R'],
            'cardTypes' => ['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group'],
        ]);
    }

    /**
     * Store a newly created support card.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'epithet' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:support_cards,slug'],
            'rarity' => ['required', Rule::in(['SSR', 'SR', 'R'])],
            'card_type' => ['required', Rule::in(['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group'])],
            'specialty_priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'key_skills' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'card_image' => ['nullable', 'image', 'mimes:webp,png,jpg,jpeg', 'max:2048'],
            'gametora_card_url' => ['nullable', 'url'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['rarity'] . '-' . $validated['name'] . ($validated['epithet'] ? '-' . $validated['epithet'] : ''));
            $slug = $baseSlug;
            $counter = 1;
            while (SupportCard::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Process key_skills comma-separated string to array
        if (! empty($validated['key_skills'])) {
            $validated['key_skills'] = array_values(array_filter(array_map('trim', explode(',', $validated['key_skills']))));
        } else {
            $validated['key_skills'] = [];
        }

        // Option 1: GameTora card image import
        if ($request->filled('gametora_card_url')) {
            $importedPath = $this->assetService->importFromUrl($request->input('gametora_card_url'), 'supoka');
            if ($importedPath) {
                $validated['card_webp'] = $importedPath;
            }
        }

        // Option 2: Manual file upload
        if ($request->hasFile('card_image')) {
            $file = $request->file('card_image');
            $filename = $validated['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('supoka', $filename, 'public');
            $validated['card_webp'] = $path;
        }

        unset($validated['card_image'], $validated['gametora_card_url']);

        $card = SupportCard::create($validated);

        return redirect()->route('admin.support-cards.index')
            ->with('status', "Support Card '{$card->full_title}' created successfully.");
    }

    /**
     * Show form for editing an existing support card.
     */
    public function edit(SupportCard $support_card): View
    {
        return view('admin.support-cards.edit', [
            'card' => $support_card,
            'rarities' => ['SSR', 'SR', 'R'],
            'cardTypes' => ['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group'],
        ]);
    }

    /**
     * Update an existing support card.
     */
    public function update(Request $request, SupportCard $support_card): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'epithet' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('support_cards', 'slug')->ignore($support_card->id)],
            'rarity' => ['required', Rule::in(['SSR', 'SR', 'R'])],
            'card_type' => ['required', Rule::in(['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group'])],
            'specialty_priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'key_skills' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'card_image' => ['nullable', 'image', 'mimes:webp,png,jpg,jpeg', 'max:2048'],
            'gametora_card_url' => ['nullable', 'url'],
        ]);

        if (! empty($validated['key_skills'])) {
            $validated['key_skills'] = array_values(array_filter(array_map('trim', explode(',', $validated['key_skills']))));
        } else {
            $validated['key_skills'] = [];
        }

        if ($request->filled('gametora_card_url')) {
            $importedPath = $this->assetService->importFromUrl($request->input('gametora_card_url'), 'supoka');
            if ($importedPath) {
                if ($support_card->card_webp && ! filter_var($support_card->card_webp, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($support_card->card_webp);
                }
                $validated['card_webp'] = $importedPath;
            }
        }

        if ($request->hasFile('card_image')) {
            if ($support_card->card_webp && ! filter_var($support_card->card_webp, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($support_card->card_webp);
            }
            $file = $request->file('card_image');
            $filename = $validated['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('supoka', $filename, 'public');
            $validated['card_webp'] = $path;
        }

        unset($validated['card_image'], $validated['gametora_card_url']);

        $support_card->update($validated);

        return redirect()->route('admin.support-cards.index')
            ->with('status', "Support Card '{$support_card->full_title}' updated successfully.");
    }

    /**
     * Remove the specified support card.
     */
    public function destroy(SupportCard $support_card): RedirectResponse
    {
        if ($support_card->card_webp && ! filter_var($support_card->card_webp, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($support_card->card_webp);
        }

        $title = $support_card->full_title;
        $support_card->delete();

        return redirect()->route('admin.support-cards.index')
            ->with('status', "Support Card '{$title}' deleted successfully.");
    }
}
