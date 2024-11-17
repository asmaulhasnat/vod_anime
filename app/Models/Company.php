<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [ 'name', 'type', 'slug'];
    protected $primaryKey = 'mal_id';

    protected static function booted()
    {
        static::creating(function ($anime) {
            $slug = Str::replace(' ', '_', $anime->name);
            $slug = Str::replace(':', '__', $slug);
            $anime->slug = $slug;
        });
    }
}
