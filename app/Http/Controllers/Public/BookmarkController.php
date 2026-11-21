<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    /**
     * Display the user's bookmarked CM guides.
     */
    public function index(Request $request): View
    {
        $events = $request->user()
            ->bookmarkedEvents()
            ->withCount('tierEntries')
            ->orderBy('is_active', 'desc')
            ->orderBy('cm_number', 'desc')
            ->get();

        return view('public.bookmarks.index', [
            'events' => $events,
        ]);
    }

    /**
     * Toggle bookmark for a Champions Meeting event.
     */
    public function toggle(Request $request, Event $event): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        $changes = $user->bookmarkedEvents()->toggle($event->id);
        $isBookmarked = count($changes['attached']) > 0;

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'bookmarked' => $isBookmarked,
                'message' => $isBookmarked ? 'Event pinned to your bookmarks.' : 'Event removed from your bookmarks.',
            ]);
        }

        return redirect()->back()->with(
            'status',
            $isBookmarked ? "CM '{$event->title}' bookmarked successfully." : "CM '{$event->title}' removed from bookmarks."
        );
    }
}
