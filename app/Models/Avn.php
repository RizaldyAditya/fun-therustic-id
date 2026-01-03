<?php
namespace App\Models;

use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avn extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title',
        'slug',
        'developer',
        'version',
        'status_id',
        'description',
        'itch_io_url',
        'cover_image',
        'last_updated_on_itch',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
