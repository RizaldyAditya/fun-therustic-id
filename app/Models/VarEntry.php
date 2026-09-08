<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VarEntry extends Model
{
    protected $table = 'vars';

    protected $fillable = ['name', 'value', 'group'];
}
