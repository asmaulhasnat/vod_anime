<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Trailer extends Model
{
    protected $fillable = [ 'youtube_id', 'url', 'embed_url'];
    protected $primaryKey = 'mal_id';

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
