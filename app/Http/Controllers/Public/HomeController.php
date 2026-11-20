<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the ChevalSleep homepage with latest CM spotlight & archives.
     */
    public function index(Request $request): View
    {
        class_exists(EloquentCollection::class);
        class_exists(Event::class);

        $events = Cache::remember('chevalsleep:events:all', 3600, function () {
            return Event::withCount('tierEntries')
                ->orderByDesc('cm_number')
                ->orderByDesc('is_active')
                ->orderByDesc('id')
                ->get();
        });

        // Defensive cache heal
        if ($events instanceof \__PHP_Incomplete_Class || ! ($events instanceof \Illuminate\Support\Collection)) {
            Cache::forget('chevalsleep:events:all');
            $events = Event::withCount('tierEntries')
                ->orderByDesc('cm_number')
                ->orderByDesc('is_active')
                ->orderByDesc('id')
                ->get();
            Cache::put('chevalsleep:events:all', $events, 3600);
        }

        $activeEvent = $events->firstWhere('is_active', true) ?? $events->first();

        // Top Uma previews for the spotlight card
        $topRacers = collect();
        if ($activeEvent) {
            $topRacers = $activeEvent->tierEntries()
                ->with('character')
                ->whereIn('tier_rank', ['SS', 'S+'])
                ->orderBy('sort_order')
                ->take(4)
                ->get();
        }

        return view('public.home', [
            'events' => $events,
            'activeEvent' => $activeEvent,
            'topRacers' => $topRacers,
        ]);
    }
}
