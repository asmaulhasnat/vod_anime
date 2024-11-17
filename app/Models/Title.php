<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = [ 'type', 'title'];
    protected $primaryKey = 'mal_id';
}
