<?php
namespace App\Models;

use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'saves_file_url',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'avn_genre');
    }
}
