<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    protected array $commonData = [
        'distanceTypes'  => ['Sprint', 'Mile', 'Medium', 'Long'],
        'surfaces'       => ['Turf', 'Dirt'],
        'seasons'        => ['Spring', 'Summer', 'Autumn', 'Winter'],
        'weathers'       => ['Sunny', 'Cloudy', 'Rainy', 'Snowy'],
        'trackConditions' => ['Firm', 'Good', 'Soft', 'Heavy'],
        'runningStyles'  => ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
    ];

    public function index(): View
    {
        $events = Event::withCount('tierEntries')
            ->orderByDesc('cm_number')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.events.index', ['events' => $events]);
    }

    public function create(): View
    {
        return view('admin.events.create', $this->commonData);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEventRequest($request);

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        }

        $isActive = $request->boolean('is_active');
        $validated['is_active'] = false;

        // Build JSON meta fields from form inputs
        $validated['stat_recommendations'] = $this->buildStatRecommendations($request);
        $validated['skill_inheritance']     = $this->buildInheritance($request);
        $validated['recommended_skills']    = $this->buildRecommendedSkills($request);
        $validated['supoka_decks']          = $this->cleanArray($request->input('decks', []));
        $validated['team_compositions']     = $this->cleanArray($request->input('teams', []));

        $event = Event::create($validated);

        if ($isActive) {
            $event->activate();
        }

        $this->flushEventCaches();

        return redirect()->route('admin.events.index')
            ->with('status', "CM '{$event->title}' created successfully.");
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', array_merge($this->commonData, ['event' => $event]));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $this->validateEventRequest($request, $event->id);

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $event->id);
        }

        $isActive = $request->boolean('is_active');
        unset($validated['is_active']);

        // Rebuild JSON meta fields from form inputs
        $validated['stat_recommendations'] = $this->buildStatRecommendations($request);
        $validated['skill_inheritance']     = $this->buildInheritance($request);
        $validated['recommended_skills']    = $this->buildRecommendedSkills($request);
        $validated['supoka_decks']          = $this->cleanArray($request->input('decks', []));
        $validated['team_compositions']     = $this->cleanArray($request->input('teams', []));

        $event->update($validated);

        if ($isActive && ! $event->is_active) {
            $event->activate();
        } elseif (! $isActive && $event->is_active) {
            $event->update(['is_active' => false]);
        }

        $this->flushEventCaches();

        return redirect()->route('admin.events.index')
            ->with('status', "CM '{$event->title}' updated successfully.");
    }

    public function destroy(Event $event): RedirectResponse
    {
        $title = $event->title;
        $event->delete();
        $this->flushEventCaches();

        return redirect()->route('admin.events.index')
            ->with('status', "Tournament '{$title}' deleted.");
    }

    public function activate(Event $event): RedirectResponse
    {
        $event->activate();
        $this->flushEventCaches();

        return redirect()->back()
            ->with('status', "'{$event->title}' is now the active default CM.");
    }

    // ─────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────

    private function validateEventRequest(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        $slugRule[] = $ignoreId
            ? Rule::unique('events', 'slug')->ignore($ignoreId)
            : 'unique:events,slug';

        return $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => $slugRule,
            'cm_number'      => ['nullable', 'integer', 'min:1', 'max:999'],
            'event_date'     => ['nullable', 'date'],
            'track_name'     => ['required', 'string', 'max:255'],
            'distance_type'  => ['required', Rule::in(['Sprint', 'Mile', 'Medium', 'Long'])],
            'distance_meters'=> ['required', 'integer', 'min:800', 'max:3600'],
            'surface'        => ['required', Rule::in(['Turf', 'Dirt'])],
            'season'         => ['required', Rule::in(['Spring', 'Summer', 'Autumn', 'Winter'])],
            'weather'        => ['required', Rule::in(['Sunny', 'Cloudy', 'Rainy', 'Snowy'])],
            'track_condition'=> ['required', Rule::in(['Firm', 'Good', 'Soft', 'Heavy'])],
            'is_active'      => ['nullable', 'boolean'],
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (Event::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /**
     * Build stat_recommendations JSON from flat form fields (stat_min_*, stat_rec_*).
     */
    private function buildStatRecommendations(Request $request): ?array
    {
        $stats = ['speed', 'stamina', 'power', 'guts', 'wit'];
        $min = [];
        $rec = [];
        $hasAny = false;

        foreach ($stats as $stat) {
            $minVal = $request->input("stat_min_{$stat}");
            $recVal = $request->input("stat_rec_{$stat}");
            if ($minVal || $recVal) {
                $hasAny = true;
            }
            $min[$stat] = $minVal ?: null;
            $rec[$stat] = $recVal ?: null;
        }

        if (! $hasAny) {
            return null;
        }

        return [
            'minimum'     => $min,
            'recommended' => $rec,
            'notes'       => $request->input('stat_notes') ?: null,
        ];
    }

    /**
     * Build skill_inheritance JSON from Alpine.js repeater POST data.
     * Input: inheritance[Front Runner][0][name], inheritance[Front Runner][0][icon]
     */
    private function buildInheritance(Request $request): ?array
    {
        $raw = $request->input('inheritance', []);
        if (empty($raw)) return null;

        $result = [];
        foreach ($raw as $style => $skills) {
            $result[$style] = array_values(array_filter($skills, fn($s) => !empty($s['name'])));
        }

        return array_filter($result, fn($v) => !empty($v)) ?: null;
    }

    /**
     * Build recommended_skills JSON from Alpine.js repeater POST data.
     */
    private function buildRecommendedSkills(Request $request): ?array
    {
        $raw = $request->input('rec_skills', []);
        if (empty($raw)) return null;

        $result = [];
        foreach ($raw as $style => $groups) {
            $result[$style] = [
                'core_accel'    => array_values(array_filter($groups['core_accel'] ?? [], fn($s) => !empty($s['name']))),
                'velocity_other' => array_values(array_filter($groups['velocity_other'] ?? [], fn($s) => !empty($s['name']))),
            ];
        }

        return array_filter($result, fn($v) => !empty($v['core_accel']) || !empty($v['velocity_other'])) ?: null;
    }

    /**
     * Clean generic array repeater (decks / teams) — remove empty entries.
     */
    private function cleanArray(array $data): ?array
    {
        $result = array_values(array_filter($data, fn($item) => !empty($item['name'] ?? $item['archetype'] ?? null)));
        return $result ?: null;
    }

    private function flushEventCaches(): void
    {
        Cache::forget('chevalsleep:events:all');
        Cache::forget('chevalsleep:home');
    }
}
