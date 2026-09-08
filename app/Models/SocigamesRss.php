<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocigamesRss extends Model
{
    protected $table = 'socigames_rss';

    protected $fillable = [
        'title',
        'url',
        'version',
        'cover_image',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];
}
