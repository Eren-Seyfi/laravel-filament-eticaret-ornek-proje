<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = ['host', 'is_active', 'is_canonical'];

    public function getHostAttribute($value): string
    {
        return strtolower($value);
    }
}