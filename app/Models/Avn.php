<?php
namespace App\Models;

use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Avn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'developer',
        'version',
        'status_id',
        'description',
        'rating',
        'itch_io_url',
        'cover_image',
        'genre_id',
        'last_updated_on_itch',
        'last_played_version',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'avn_genre');
    }

    public function galleries()
    {
        return $this->hasMany(AvnGallery::class);
    }

    public function saves()
    {
        return $this->hasMany(AvnSave::class)->orderBy('sort', 'asc');
    }

    protected static function booted()
    {
        static::deleting(function ($avn) {
            $avn->saves->each->delete();
        });

        static::deleted(function ($avn) {
            if ($avn->cover_image) {
                Storage::disk('public')->delete($avn->cover_image);
            }
        });

        static::updating(function ($avn) {
            if ($avn->isDirty('cover_image') && $avn->getOriginal('cover_image')) {
                Storage::disk('public')->delete($avn->getOriginal('cover_image'));
            }
        });
    }
}
