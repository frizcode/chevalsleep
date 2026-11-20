<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Changelog;
use App\Models\Character;
use App\Models\Event;
use App\Models\TierEntry;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TierListController extends Controller
{
    /**
     * Display the public tier list.
     */
    public function index(Request $request): View
    {
        // Ensure all required model and collection classes are registered in PHP memory
        // before any cache unserialization occurs.
        class_exists(EloquentCollection::class);
        class_exists(Event::class);
        class_exists(Character::class);
        class_exists(TierEntry::class);
        class_exists(Changelog::class);

        $events = Cache::remember('chevalsleep:events:all', 3600, function () {
            return Event::orderByDesc('is_active')->orderByDesc('id')->get();
        });

        // Defensive check: heal cache if corrupt or incomplete class returned
        if ($events instanceof \__PHP_Incomplete_Class || ! ($events instanceof Collection)) {
            Cache::forget('chevalsleep:events:all');
            $events = Event::orderByDesc('is_active')->orderByDesc('id')->get();
            Cache::put('chevalsleep:events:all', $events, 3600);
        }

        $eventSlug = $request->query('event');

        if ($eventSlug) {
            $currentEvent = $events->firstWhere('slug', $eventSlug)
                ?? Event::where('slug', $eventSlug)->first();
        } else {
            $currentEvent = $events->firstWhere('is_active', true) ?? $events->first();
        }

        $tierRanks = ['S+', 'S', 'A', 'B', 'C'];
        $entries = collect();
        $changelogs = collect();

        if ($currentEvent) {
            $cachedData = Cache::remember("chevalsleep:event:{$currentEvent->slug}", 3600, function () use ($currentEvent) {
                $eventEntries = TierEntry::with('character')
                    ->where('event_id', $currentEvent->id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

                $eventChangelogs = Changelog::with('character')
                    ->where('event_id', $currentEvent->id)
                    ->latest()
                    ->take(10)
                    ->get();

                return [
                    'entries' => $eventEntries,
                    'changelogs' => $eventChangelogs,
                ];
            });

            // Defensive check: heal per-event cache if corrupt or incomplete class returned
            if (
                $cachedData instanceof \__PHP_Incomplete_Class ||
                ! is_array($cachedData) ||
                ($cachedData['entries'] ?? null) instanceof \__PHP_Incomplete_Class ||
                ($cachedData['changelogs'] ?? null) instanceof \__PHP_Incomplete_Class
            ) {
                Cache::forget("chevalsleep:event:{$currentEvent->slug}");
                $eventEntries = TierEntry::with('character')
                    ->where('event_id', $currentEvent->id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

                $eventChangelogs = Changelog::with('character')
                    ->where('event_id', $currentEvent->id)
                    ->latest()
                    ->take(10)
                    ->get();

                $cachedData = [
                    'entries' => $eventEntries,
                    'changelogs' => $eventChangelogs,
                ];
                Cache::put("chevalsleep:event:{$currentEvent->slug}", $cachedData, 3600);
            }

            $entries = $cachedData['entries'];
            $changelogs = $cachedData['changelogs'];
        }

        return view('public.index', [
            'events' => $events,
            'currentEvent' => $currentEvent,
            'entries' => $entries,
            'changelogs' => $changelogs,
            'tierRanks' => $tierRanks,
        ]);
    }
}
