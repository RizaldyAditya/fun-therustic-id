<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Studio extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'url',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
