<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'donghua_id',
        'title',
        'episode_number',
        'stream_id',
        'stream_url',
        'video_source_url',
    ];

    public function donghua(): BelongsTo
    {
        return $this->belongsTo(Donghua::class);
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }
}
