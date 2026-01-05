<?php
namespace App\Models;

use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'last_played_version'
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'avn_genre');
    }

    public function gallery()
    {
        return $this->hasMany(AvnGallery::class);
    }

    public function saves()
    {
        return $this->hasMany(AvnSave::class);
    }

    protected static function booted()
    {
        // Handle file deletion when the entire AVN record is deleted
        static::deleted(function ($avn) {
            if ($avn->cover_image) {
                Storage::disk('public')->delete($avn->cover_image);
            }
        });

        // Handle file deletion when the image is updated or removed in the form
        static::updating(function ($avn) {
            if ($avn->isDirty('cover_image') && $avn->getOriginal('cover_image')) {
                Storage::disk('public')->delete($avn->getOriginal('cover_image'));
            }
        });
    }
}
