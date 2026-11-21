<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Character extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'variant_title',
        'slug',
        'avatar_webp',
        'default_running_style',
        'rarity',
        'aptitude_notes',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'full_name',
        'avatar_url',
    ];

    /**
     * @return HasMany<TierEntry, $this>
     */
    public function tierEntries(): HasMany
    {
        return $this->hasMany(TierEntry::class);
    }

    /**
     * @return HasMany<Changelog, $this>
     */
    public function changelogs(): HasMany
    {
        return $this->hasMany(Changelog::class);
    }

    /**
     * Get the character's full name with variant.
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->variant_title && $this->variant_title !== 'Original') {
                return "{$this->name} ({$this->variant_title})";
            }

            return $this->name;
        });
    }

    /**
     * Get the full public URL for the character's avatar.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->avatar_webp) {
                return null;
            }

            if (filter_var($this->avatar_webp, FILTER_VALIDATE_URL)) {
                return $this->avatar_webp;
            }

            return Storage::disk('public')->url($this->avatar_webp);
        });
    }
}
