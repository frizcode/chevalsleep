<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of tournaments/events.
     */
    public function index(): View
    {
        $events = Event::withCount('tierEntries')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.events.index', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        return view('admin.events.create', [
            'distanceTypes' => ['Sprint', 'Mile', 'Medium', 'Long'],
            'surfaces' => ['Turf', 'Dirt'],
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:events,slug'],
            'track_name' => ['required', 'string', 'max:255'],
            'distance_type' => ['required', Rule::in(['Sprint', 'Mile', 'Medium', 'Long'])],
            'distance_meters' => ['required', 'integer', 'min:800', 'max:3600'],
            'surface' => ['required', Rule::in(['Turf', 'Dirt'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Event::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $isActive = $request->boolean('is_active');
        $validated['is_active'] = false;

        $event = Event::create($validated);

        if ($isActive) {
            $event->activate();
        }

        return redirect()->route('admin.events.index')
            ->with('status', "Event '{$event->title}' created successfully.");
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
            'distanceTypes' => ['Sprint', 'Mile', 'Medium', 'Long'],
            'surfaces' => ['Turf', 'Dirt'],
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('events', 'slug')->ignore($event->id)],
            'track_name' => ['required', 'string', 'max:255'],
            'distance_type' => ['required', Rule::in(['Sprint', 'Mile', 'Medium', 'Long'])],
            'distance_meters' => ['required', 'integer', 'min:800', 'max:3600'],
            'surface' => ['required', Rule::in(['Turf', 'Dirt'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $isActive = $request->boolean('is_active');
        unset($validated['is_active']);

        $event->update($validated);

        if ($isActive && ! $event->is_active) {
            $event->activate();
        }

        return redirect()->route('admin.events.index')
            ->with('status', "Event '{$event->title}' updated successfully.");
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $title = $event->title;
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('status', "Event '{$title}' deleted successfully.");
    }

    /**
     * Activate the event as default.
     */
    public function activate(Event $event): RedirectResponse
    {
        $event->activate();

        return redirect()->back()
            ->with('status', "Event '{$event->title}' is now the active default event.");
    }
}
