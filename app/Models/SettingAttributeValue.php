<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingAttributeValue extends Model
{
    protected $fillable = ['value', 'setting_attribute_id'];


}
