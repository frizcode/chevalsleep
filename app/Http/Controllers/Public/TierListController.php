<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Changelog;
use App\Models\Event;
use App\Models\TierEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TierListController extends Controller
{
    /**
     * Display the public tier list.
     */
    public function index(Request $request): View
    {
        $events = Cache::remember('chevalsleep:events:all', 3600, function () {
            return Event::orderByDesc('is_active')->orderByDesc('id')->get();
        });

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
