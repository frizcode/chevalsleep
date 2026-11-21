<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'cm_number',
        'event_date',
        'track_name',
        'distance_type',
        'distance_meters',
        'surface',
        'season',
        'weather',
        'track_condition',
        'is_active',
        'stat_recommendations',
        'skill_inheritance',
        'recommended_skills',
        'supoka_decks',
        'team_compositions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'cm_number' => 'integer',
        'distance_meters' => 'integer',
        'event_date' => 'date',
        'stat_recommendations' => 'array',
        'skill_inheritance' => 'array',
        'recommended_skills' => 'array',
        'supoka_decks' => 'array',
        'team_compositions' => 'array',
    ];

    /**
     * @return HasMany<TierEntry, $this>
     */
    public function tierEntries(): HasMany
    {
        return $this->hasMany(TierEntry::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<Changelog, $this>
     */
    public function changelogs(): HasMany
    {
        return $this->hasMany(Changelog::class)->latest();
    }

    /**
     * Users who have bookmarked this event.
     *
     * @return BelongsToMany<User, $this>
     */
    public function bookmarkedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withTimestamps();
    }

    /**
     * Determine if a given user bookmarked this event.
     */
    public function isBookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarkedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Activate this event as the default event and deactivate all others.
     */
    public function activate(): void
    {
        DB::transaction(function (): void {
            static::where('id', '!=', $this->id)->update(['is_active' => false]);
            $this->update(['is_active' => true]);

            Cache::forget('chevalsleep:events:all');
            Cache::forget("chevalsleep:event:{$this->slug}");
        });
    }
}
