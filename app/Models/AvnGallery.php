<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AvnGallery extends Model
{
    protected $fillable = [
        'avn_id',
        'image_url',
        'description',
        'sort_order',
    ];

    protected static function booted()
    {
        static::deleted(function ($gallery) {
            if ($gallery->image_url) {
                Storage::disk('public')->delete($gallery->image_url);
            }
        });
    }
}
