<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Changelog;
use App\Models\Character;
use App\Models\Event;
use App\Models\TierEntry;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CmController extends Controller
{
    /**
     * Redirect to the currently active CM guide.
     */
    public function latest(): RedirectResponse
    {
        $activeEvent = Event::where('is_active', true)->first()
            ?? Event::orderByDesc('cm_number')->first();

        if (! $activeEvent) {
            return redirect()->route('home');
        }

        return redirect()->route('cm.show', $activeEvent);
    }

    /**
     * Display the full 7-section Champions Meeting meta guide.
     */
    public function show(Event $event): View
    {
        class_exists(EloquentCollection::class);
        class_exists(Event::class);
        class_exists(Character::class);
        class_exists(TierEntry::class);
        class_exists(Changelog::class);

        $allEvents = Cache::remember('chevalsleep:events:all', 3600, function () {
            return Event::withCount('tierEntries')
                ->orderByDesc('cm_number')
                ->orderByDesc('is_active')
                ->orderByDesc('id')
                ->get();
        });

        if ($allEvents instanceof \__PHP_Incomplete_Class || ! ($allEvents instanceof \Illuminate\Support\Collection)) {
            Cache::forget('chevalsleep:events:all');
            $allEvents = Event::withCount('tierEntries')
                ->orderByDesc('cm_number')
                ->orderByDesc('is_active')
                ->orderByDesc('id')
                ->get();
            Cache::put('chevalsleep:events:all', $allEvents, 3600);
        }

        // Cache per-event tier entries & changelogs
        $cachedData = Cache::remember("chevalsleep:event:{$event->slug}", 3600, function () use ($event) {
            $entries = TierEntry::with('character')
                ->where('event_id', $event->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $changelogs = Changelog::with('character')
                ->where('event_id', $event->id)
                ->latest()
                ->take(15)
                ->get();

            return [
                'entries' => $entries,
                'changelogs' => $changelogs,
            ];
        });

        if (
            $cachedData instanceof \__PHP_Incomplete_Class ||
            ! is_array($cachedData) ||
            ($cachedData['entries'] ?? null) instanceof \__PHP_Incomplete_Class
        ) {
            Cache::forget("chevalsleep:event:{$event->slug}");
            $entries = TierEntry::with('character')
                ->where('event_id', $event->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $changelogs = Changelog::with('character')
                ->where('event_id', $event->id)
                ->latest()
                ->take(15)
                ->get();

            $cachedData = [
                'entries' => $entries,
                'changelogs' => $changelogs,
            ];
            Cache::put("chevalsleep:event:{$event->slug}", $cachedData, 3600);
        }

        $tierRanks = ['SS', 'S+', 'S', 'A', 'B', 'C'];

        return view('public.cm.show', [
            'event' => $event,
            'allEvents' => $allEvents,
            'entries' => $cachedData['entries'],
            'changelogs' => $cachedData['changelogs'],
            'tierRanks' => $tierRanks,
        ]);
    }
}
