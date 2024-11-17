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

    protected function getRoleId($role_key, $role_value)
    {
        return SettingAttribute::where('name', $role_key)
                    ->first()
                    ?->settingAttributeValue()
                    ->where('value', $role_value)
                    ->value('id') ?? '';
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
        return $this->companies()->withPivotValue('role', $this->getRoleId('anime_company_role', 'producers'));
    }

    public function licensors()
    {
        return $this->companies()->withPivotValue('role', $this->getRoleId('anime_company_role', 'licensors'));
    }

    public function studios()
    {
        return $this->companies()->withPivotValue('role', $this->getRoleId('anime_company_role', 'studios'));
    }

    public function genres_list()
    {
        return $this->genres()->withPivotValue('role', $this->getRoleId('anime_genre_role', 'genres'));
    }
    public function explicitGenres()
    {
        return $this->genres()->withPivotValue('role', $this->getRoleId('anime_genre_role', 'explicit_genres'));
    }

    public function themes()
    {
        return $this->genres()->withPivotValue('role', $this->getRoleId('anime_genre_role', 'themes'));
    }
    public function demographics()
    {
        return $this->genres()->withPivotValue('role', $this->getRoleId('anime_genre_role', 'demographics'));
    }

}
