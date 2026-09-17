<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime7Rss extends Model
{
    protected $table = 'anime7_rss';

    protected $fillable = [
        'title',
        'post_date',
        'url',
        'cover_image_url',
        'content',
    ];

    protected $casts = [
        'post_date' => 'datetime',
    ];
}
