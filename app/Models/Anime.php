<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anime extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'title_jp',
        'synopsis',
        'poster',
        'type',
        'genre_id',
        'studio_id',
        'source_id',
        'status_id',
        'season',
        'year',
        'broadcast_day',
        'episode_total',
        'episode_watched',
        'episode_downloaded',
        'myanimelist_url',
        'myanimelist_score',
        'air_date',
        'attributes',
        'is_hot',
        'is_airing',
        'is_active',
    ];

    protected $casts = [
        'attributes' => 'array',
        'air_date' => 'date',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function genre(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function episode(): HasMany
    {
        return $this->hasMany(AnimeEpisode::class);
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
