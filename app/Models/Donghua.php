<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donghua extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_zh',
        'external_titles',
        'season',
        'episode_latest',
        'episode_watched',
        'episode_watched_seasonal',
        'episode_total',
        'status_id',
        'airing',
        'myanimelist',
        'image_cover',
        'mc_name',
        'mc_wikia',
        'studio_id',
        'source_id',
        'is_observed',
        'is_active'
    ];

    protected $casts = [
        'external_titles' => 'array',
        'is_active' => 'boolean'
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
