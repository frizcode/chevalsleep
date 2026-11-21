<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'epithet',
        'slug',
        'card_webp',
        'rarity',
        'card_type',
        'specialty_priority',
        'key_skills',
        'summary',
        'source_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'specialty_priority' => 'integer',
        'key_skills' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'full_title',
        'card_url',
        'gametora_url',
    ];

    /**
     * Get the card's full title with epithet and rarity.
     */
    protected function fullTitle(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->epithet) {
                return "[{$this->epithet}] {$this->name} ({$this->rarity})";
            }

            return "{$this->rarity} {$this->name}";
        });
    }

    /**
     * Get the full public URL for the card artwork.
     */
    protected function cardUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->card_webp) {
                return null;
            }

            if (filter_var($this->card_webp, FILTER_VALIDATE_URL)) {
                return $this->card_webp;
            }

            return Storage::disk('public')->url($this->card_webp);
        });
    }

    /**
     * Get the GameTora URL for this support card.
     */
    protected function gametoraUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if (! empty($this->source_url)) {
                return $this->source_url;
            }

            $targetSlug = $this->slug ?: Str::slug($this->name);

            return "https://gametora.com/umamusume/supports/{$targetSlug}";
        });
    }
}
