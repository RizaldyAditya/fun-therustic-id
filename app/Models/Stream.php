<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stream extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'homepage_url',
        'logo',
        'is_crawlable',
        'is_active'
    ];

    protected $casts = [
        'is_crawlable' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
}
