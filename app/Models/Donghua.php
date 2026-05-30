<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Donghua extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_zh',
        'synopsis',
        'external_titles',
        'season',
        'episode_latest',
        'episode_watched',
        'episode_watched_seasonal',
        'episode_total',
        'episode_dl',
        'local_download_path',
        'status_id',
        'is_airing',
        'myanimelist',
        'image_cover',
        'mc_name',
        'mc_wikia',
        'studio_id',
        'source_id',
        'is_hot',
        'trending_sort',
        'is_active',
    ];

    protected $casts = [
        'external_titles' => 'array',
        'is_active' => 'boolean',
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

    protected static function booted()
    {
        static::deleted(function ($donghua) {
            if ($donghua->image_cover) {
                Storage::disk('public')->delete($donghua->image_cover);
            }
        });

        static::updating(function ($donghua) {
            if ($donghua->isDirty('image_cover')) {
                $oldFile = $donghua->getOriginal('image_cover');
                if ($oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }
        });
    }
}
