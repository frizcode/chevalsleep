<?php

namespace App\Providers;

use App\Models\Changelog;
use App\Models\Character;
use App\Models\Event;
use App\Models\TierEntry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $clearEventCache = function (?string $slug = null): void {
            Cache::forget('chevalsleep:events:all');
            if ($slug) {
                Cache::forget("chevalsleep:event:{$slug}");
            } else {
                foreach (Event::pluck('slug') as $eventSlug) {
                    Cache::forget("chevalsleep:event:{$eventSlug}");
                }
            }
        };

        Event::saved(function (Event $event) use ($clearEventCache): void {
            $clearEventCache($event->slug);
        });

        Event::deleted(function (Event $event) use ($clearEventCache): void {
            $clearEventCache($event->slug);
        });

        Character::saved(function () use ($clearEventCache): void {
            $clearEventCache();
        });

        Character::deleted(function () use ($clearEventCache): void {
            $clearEventCache();
        });

        TierEntry::saved(function (TierEntry $entry) use ($clearEventCache): void {
            $eventSlug = $entry->event?->slug ?? Event::where('id', $entry->event_id)->value('slug');
            $clearEventCache($eventSlug);
        });

        TierEntry::deleted(function (TierEntry $entry) use ($clearEventCache): void {
            $eventSlug = $entry->event?->slug ?? Event::where('id', $entry->event_id)->value('slug');
            $clearEventCache($eventSlug);
        });

        Changelog::saved(function (Changelog $changelog) use ($clearEventCache): void {
            $eventSlug = $changelog->event?->slug ?? Event::where('id', $changelog->event_id)->value('slug');
            $clearEventCache($eventSlug);
        });

        Changelog::deleted(function (Changelog $changelog) use ($clearEventCache): void {
            $eventSlug = $changelog->event?->slug ?? Event::where('id', $changelog->event_id)->value('slug');
            $clearEventCache($eventSlug);
        });
    }
}
