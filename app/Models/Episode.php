<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $table = 'donghua_episodes';

    protected $fillable = [
        'donghua_id',
        'title',
        'episode_number',
        'stream_id',
        'stream_url',
        'video_source_url',
        'notes',
        'is_an_update',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'video_source_url' => 'array',
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
