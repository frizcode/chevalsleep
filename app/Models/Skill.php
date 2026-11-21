<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon_webp',
        'rarity',
        'skill_type',
        'running_style',
        'distance_type',
        'activation_condition',
        'description',
        'source_url',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'icon_url',
        'wiki_url',
    ];

    /**
     * Get the full public URL for the skill icon.
     */
    protected function iconUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->icon_webp) {
                return null;
            }

            if (filter_var($this->icon_webp, FILTER_VALIDATE_URL)) {
                return $this->icon_webp;
            }

            return Storage::disk('public')->url($this->icon_webp);
        });
    }

    /**
     * Get the umamusu.wiki documentation URL for this skill.
     */
    protected function wikiUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if (! empty($this->source_url)) {
                return $this->source_url;
            }

            if (! empty($this->name)) {
                return 'https://umamusu.wiki/index.php?search=' . urlencode($this->name);
            }

            return 'https://umamusu.wiki/';
        });
    }
}
