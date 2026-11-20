<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Changelog;
use App\Models\Character;
use App\Models\Event;
use App\Models\TierEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TierBuilderController extends Controller
{
    /**
     * Display the tier builder workspace for a specific event.
     */
    public function index(Event $event): View
    {
        $event->load(['tierEntries.character']);

        $characters = Character::orderBy('name')->get();
        $tierRanks = ['S+', 'S', 'A', 'B', 'C'];
        $roles = ['Ace', 'Parent', 'Debuffer'];
        $runningStyles = ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'];

        // Group existing entries by tier rank
        $groupedEntries = collect($tierRanks)->mapWithKeys(function ($rank) use ($event) {
            return [$rank => $event->tierEntries->where('tier_rank', $rank)->values()];
        });

        return view('admin.events.tiers', [
            'event' => $event,
            'groupedEntries' => $groupedEntries,
            'characters' => $characters,
            'tierRanks' => $tierRanks,
            'roles' => $roles,
            'runningStyles' => $runningStyles,
        ]);
    }

    /**
     * Store a newly created tier entry and record changelog.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'character_id' => [
                'required',
                'exists:characters,id',
                Rule::unique('tier_entries')->where(function ($query) use ($event, $request) {
                    return $query->where('event_id', $event->id)
                        ->where('role', $request->input('role'));
                }),
            ],
            'tier_rank' => ['required', Rule::in(['S+', 'S', 'A', 'B', 'C'])],
            'role' => ['required', Rule::in(['Ace', 'Parent', 'Debuffer'])],
            'recommended_style' => ['required', Rule::in(['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'target_stats' => ['nullable', 'string', 'max:255'],
            'mandatory_skills' => ['nullable', 'string'],
            'inherited_uniques' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'changelog_reason' => ['nullable', 'string', 'max:500'],
        ], [
            'character_id.unique' => 'This character already has an entry with this role for this event.',
        ]);

        $mandatorySkills = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('mandatory_skills', '')))));
        $inheritedUniques = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('inherited_uniques', '')))));

        $buildNotes = [
            'target_stats' => $request->input('target_stats') ?: '',
            'mandatory_skills' => $mandatorySkills,
            'inherited_uniques' => $inheritedUniques,
            'summary' => $request->input('summary') ?: '',
        ];

        $tierEntry = TierEntry::create([
            'event_id' => $event->id,
            'character_id' => $validated['character_id'],
            'tier_rank' => $validated['tier_rank'],
            'role' => $validated['role'],
            'recommended_style' => $validated['recommended_style'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'build_notes' => $buildNotes,
        ]);

        // Automatic Changelog Recording
        $reason = $request->filled('changelog_reason')
            ? $request->input('changelog_reason')
            : "Added to {$tierEntry->tier_rank} tier";

        Changelog::create([
            'event_id' => $event->id,
            'character_id' => $tierEntry->character_id,
            'previous_tier' => null,
            'new_tier' => $tierEntry->tier_rank,
            'reason' => $reason,
        ]);

        return redirect()->route('admin.events.tiers.index', $event)
            ->with('status', 'Character added to tier list successfully.');
    }

    /**
     * Update the specified tier entry and record changelog if changed.
     */
    public function update(Request $request, Event $event, TierEntry $tierEntry): RedirectResponse
    {
        $validated = $request->validate([
            'tier_rank' => ['required', Rule::in(['S+', 'S', 'A', 'B', 'C'])],
            'role' => [
                'required',
                Rule::in(['Ace', 'Parent', 'Debuffer']),
                Rule::unique('tier_entries')->where(function ($query) use ($event, $tierEntry) {
                    return $query->where('event_id', $event->id)
                        ->where('character_id', $tierEntry->character_id);
                })->ignore($tierEntry->id),
            ],
            'recommended_style' => ['required', Rule::in(['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'target_stats' => ['nullable', 'string', 'max:255'],
            'mandatory_skills' => ['nullable', 'string'],
            'inherited_uniques' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'changelog_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $mandatorySkills = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('mandatory_skills', '')))));
        $inheritedUniques = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('inherited_uniques', '')))));

        $buildNotes = [
            'target_stats' => $request->input('target_stats') ?: '',
            'mandatory_skills' => $mandatorySkills,
            'inherited_uniques' => $inheritedUniques,
            'summary' => $request->input('summary') ?: '',
        ];

        $previousTier = $tierEntry->tier_rank;

        $tierEntry->update([
            'tier_rank' => $validated['tier_rank'],
            'role' => $validated['role'],
            'recommended_style' => $validated['recommended_style'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'build_notes' => $buildNotes,
        ]);

        // Automatic Changelog Recording when tier changes or reason provided
        if ($previousTier !== $tierEntry->tier_rank || $request->filled('changelog_reason')) {
            $reason = $request->filled('changelog_reason')
                ? $request->input('changelog_reason')
                : "Tier adjusted from {$previousTier} to {$tierEntry->tier_rank}";

            Changelog::create([
                'event_id' => $event->id,
                'character_id' => $tierEntry->character_id,
                'previous_tier' => $previousTier,
                'new_tier' => $tierEntry->tier_rank,
                'reason' => $reason,
            ]);
        }

        return redirect()->route('admin.events.tiers.index', $event)
            ->with('status', 'Tier entry updated successfully.');
    }

    /**
     * Remove the specified tier entry from storage.
     */
    public function destroy(Event $event, TierEntry $tierEntry): RedirectResponse
    {
        $characterName = $tierEntry->character?->full_name ?? 'Character';
        $tierEntry->delete();

        return redirect()->route('admin.events.tiers.index', $event)
            ->with('status', "Removed {$characterName} from tier list.");
    }
}
