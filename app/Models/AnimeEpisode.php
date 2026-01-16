<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimeEpisode extends Model
{
    protected $fillable = [
        'anime_id',
        'episode_number',
        'title',
        'stream_id',
        'stream_url',
        'video_url',
        'subtitle_lang',
        'notes',
        'is_active'
    ];

    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }
}
