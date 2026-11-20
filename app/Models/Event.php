<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'track_name',
        'distance_type',
        'distance_meters',
        'surface',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
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
