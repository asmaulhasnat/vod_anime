<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Anime extends Model
{
    protected $fillable = ['mal_id', 'title', 'url', 'type', 'status', 'approved', 'source', 'episodes', 'airing', 'aired_from', 'aired_to', 'duration', 'rating', 'score', 'scored_by', 'rank', 'popularity', 'members', 'favorites', 'synopsis', 'background', 'season', 'year'];
    protected $primaryKey = 'mal_id';

    protected static function booted()
    {
        static::creating(function ($anime) {
            $slug = Str::replace(' ', '_', $anime->title);
            $slug = Str::replace(':', '__', $slug);
            $anime->slug = $slug;
        });
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function titles(): HasMany
    {
        return $this->HasMany(Title::class, 'anime_id', 'mal_id');
    }

    public function trailer(): HasOne
    {
        return $this->hasOne(Trailer::class, 'anime_id', 'mal_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'anime_company', 'anime_id', 'company_id')
                    ->withPivot('role')->withTimestamps();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'anime_genres', 'anime_id', 'genre_id')
                    ->withPivot('role')->withTimestamps();
    }


    public function broadcast(): HasOne
    {
        return $this->hasOne(Broadcast::class, 'anime_id', 'mal_id');
    }

    public function animeStatus(): HasOne
    {
        return $this->hasOne(SettingAttributeValue::class, 'id', 'status');
    }

    public function animeType(): HasOne
    {
        return $this->hasOne(SettingAttributeValue::class, 'id', 'type');
    }

    public function producers()
    {
        $producer_attribute = SettingAttribute::where('name', 'anime_company_role')->first();
        $value = SettingAttributeValue::where('setting_attribute_id', $producer_attribute->id)->where('value', 'producers')->first();
        return $this->companies()->withPivotValue('role', $value->id ?? '');
    }

    public function licensors()
    {
        $producer_attribute = SettingAttribute::where('name', 'anime_company_role')->first();
        $value = SettingAttributeValue::where('setting_attribute_id', $producer_attribute->id)->where('value', 'producers')->first();
        return $this->companies()->withPivotValue('role', $value->id ?? '');
    }

    public function studios()
    {
        $producer_attribute = SettingAttribute::where('name', 'anime_company_role')->first();
        $value = SettingAttributeValue::where('setting_attribute_id', $producer_attribute->id)->where('value', 'studios')->first();
        return $this->companies()->withPivotValue('role', $value->id ?? '');
    }

    public function genres_list()
    {
        $producer_attribute = SettingAttribute::where('name', 'anime_genre_role')->first();
        $value = SettingAttributeValue::where('setting_attribute_id', $producer_attribute->id)->where('value', 'genres')->first();
        return $this->genres()->withPivotValue('role', $value->id ?? '');
    }
    public function explicit_genres()
    {
        $producer_attribute = SettingAttribute::where('name', 'anime_genre_role')->first();
        $value = SettingAttributeValue::where('setting_attribute_id', $producer_attribute->id)->where('value', 'explicit_genres')->first();
        return $this->genres()->withPivotValue('role', $value->id ?? '');
    }

}
