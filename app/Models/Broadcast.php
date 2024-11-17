<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    protected $fillable = [ 'day', 'time', 'timezone', 'time_string'];
    protected $primaryKey = 'mal_id';

}
