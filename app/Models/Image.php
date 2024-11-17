<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Image extends Model
{
    protected $fillable = [ 'url', 'mageable_id','imageable_type',	'quality',	'image_extention'];
    protected $primaryKey = 'mal_id';

    public function size(): HasOne
    {
        return $this->hasOne(SettingAttributeValue::class, 'image_id')->whereHas('attribute', function ($query) {
            $query->where('name', 'size');
        });
    }



}
