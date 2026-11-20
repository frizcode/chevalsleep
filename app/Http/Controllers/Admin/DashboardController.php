<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Changelog;
use App\Models\Character;
use App\Models\Event;
use App\Models\TierEntry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin CMS Dashboard.
     */
    public function index(): View
    {
        $activeEvent = Event::where('is_active', true)->first();
        $totalCharacters = Character::count();
        $totalEvents = Event::count();
        $activeEventEntriesCount = $activeEvent ? TierEntry::where('event_id', $activeEvent->id)->count() : 0;
        $totalChangelogs = Changelog::count();
        $recentChangelogs = Changelog::with(['event', 'character'])->latest()->take(6)->get();

        return view('admin.dashboard', [
            'activeEvent' => $activeEvent,
            'totalCharacters' => $totalCharacters,
            'totalEvents' => $totalEvents,
            'activeEventEntriesCount' => $activeEventEntriesCount,
            'totalChangelogs' => $totalChangelogs,
            'recentChangelogs' => $recentChangelogs,
        ]);
    }
}
