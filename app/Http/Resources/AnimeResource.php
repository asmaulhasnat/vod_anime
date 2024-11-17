<?php

namespace App\Http\Resources;

use App\Models\SettingAttributeValue;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class AnimeResource extends JsonResource
{
    public function toArray($request)
    {

        $titles = $this->manageTitle($this->titles);

        return [
            'mal_id' => $this->mal_id,
            'title' => $this->title,
            'url' => url('api/anime', $this->slug),
            'images' => $this->mangeAnimeImage($this->images),
            'trailer' => $this->mangeTrailer($this->trailer),
            'approved' => $this->approved,
            'titles' => $titles['all'],
            'title' => $this->title,
            'title_english' => $titles['english'],
            'title_japanese' => $titles['japanese'],
            'title_synonyms' => $titles['synonym'],
            'type' => $this->animeType->value ?? null,
            'source' => $this->source,
            'episodes' => $this->episodes,
            'status' =>  $this->animeStatus->value ?? null,
            'airing' =>  $this->airing,
            'aired' =>   $this->manageAired($this->aired_from, $this->aired_to, $this->aired_to),
            'score' => $this->score,
            'duration' => $this->duration,
            'rating' => $this->rating,
            'score' => $this->score,
            'scored_by' => $this->scored_by,
            'rank' => $this->rank,
            'popularity' => $this->popularity,
            'members' => $this->members,
            'favorites' => $this->favorites,
            'synopsis' => $this->synopsis,
            'background' => $this->background,
            'season' => $this->season,
            'year' => $this->year,
            'broadcast' => collect($this->broadcast)->except(['mal_id','anime_id','time_string','created_at','updated_at'])->merge(collect(['string' => $this->broadcast->time_string])),
            'producers' => $this->manageCompanyWithRole($this->producers),
            'licensors' => $this->manageCompanyWithRole($this->licensors),
            'studios' => $this->manageCompanyWithRole($this->studios),
            'genres' => $this->manageGenreWithRole($this->genres_list),
            'explicit_genres' => $this->manageGenreWithRole($this->explicitGenres),
            'themes' => $this->manageGenreWithRole($this->themes),
            'demographics' => $this->manageGenreWithRole($this->themes),
        ];
    }
    public function manageAired($aired_from, $aired_to): array
    {

        return ['from' => $aired_from,'to' => $aired_to,];
    }

    public function manageTitle($titles): array
    {
        $new_titles = [];
        $new_title_by_english = '';
        $new_title_by_japanese = '';
        $new_title_by_synonym = [];

        foreach ($titles as $title) {
            $type = $this->getSettingValueByKey($title->type);
            $new_titles[] = ['type' => $type,'title' => $title->title];

            if ($type == 'English') {
                $new_title_by_english = $title->title;
            }

            if ($type == 'Japanese') {
                $new_title_by_japanese = $title->title;
            }
            if ($type == 'Synonym') {
                $new_title_by_synonym[] = $title->title;
            }

        }

        return ['all' => $new_titles,'english' => $new_title_by_english,'japanese' => $new_title_by_japanese,'synonym' => $new_title_by_synonym];
    }

    public function mangeAnimeImage($images): array
    {
        $new_image = [];

        foreach ($images as $image) {

            if ($quality = $this->getSettingValueByKey($image->quality)) {
                $new_image[$image->image_extention][$quality] = $image->url;
            }

        }

        return  $new_image;
    }

    public function mangeTrailerImage($images): array
    {
        $new_image = [];

        foreach ($images as $image) {

            if ($quality = $this->getSettingValueByKey($image->quality)) {
                $new_image[$quality] = $image->url;
            }

        }

        return  $new_image;
    }

    public function mangeTrailer($trailer): array
    {
        $trailer_data = [
            'youtube_id' => $trailer->youtube_id ?? '',
            'url' => $trailer->url ?? '',
            'embed_url' => $trailer->embed_url ?? '',
            'images' => $this->mangeTrailerImage($trailer->images ?? [])
        ];
        return $trailer_data;
    }

    public function getSettingValueByKey(string $key): ?string
    {

        $values = [];

        if (sizeof($values) == 0) {
            $attribute_value = SettingAttributeValue::all();

            foreach ($attribute_value as $value) {
                $values[$value->id] = $value->value;
            }
        }


        return $values[$key] ?? null;
    }

    public function manageCompanyWithRole($companies)
    {
        $new_companies = [];

        if ($companies) {

            foreach ($companies as $company) {

                $new_companies[] = ['mal_id' => $company->mal_id,'type' => $this->getSettingValueByKey($company->type),'name' => $company->name, 'url' => url('api/anime/producer/'.$company->mal_id.'/'.$company->name)];
            }

        }


        return  $new_companies;
    }

    public function manageGenreWithRole($genres)
    {
        $new_genres = [];

        if ($genres) {

            foreach ($genres as $genre) {
                $type = $this->getSettingValueByKey($genre->type);
                $new_genres[] = ['mal_id' => $genre->mal_id,'type' =>  $type,'name' => $genre->name,'url' => url('api/anime/genre/'.$genre->mal_id.'/'.$genre->name)];
            }
        }

        return  $new_genres;
    }


}
