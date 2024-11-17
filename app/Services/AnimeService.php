<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Anime;
use App\Models\Genre;
use App\Models\Company;
use App\Models\SettingAttribute;
use Illuminate\Support\Facades\Http;

class AnimeService
{
    protected $apiUrl = env('ANIME_API_PATH', 'https://api.jikan.moe/v4/top/anime?type=ova');

    /**
     * Fetch data from Jikan API.
     *
     * @return array|null
     */
    public function fetchTopOVAAnime()
    {
        try {
            $page = $this->get_next_page('page');
            $response = Http::get($this->apiUrl, ['page' => $page]);

            if ($response->successful()) {
                $page = $this->get_next_page('page', $page + 1);
                return $response->json('data');
            } else {
                throw new Exception('Failed to fetch data from the API.');
            }
        } catch (Exception $e) {
            // Log or handle the exception as needed
            return null;
        }
    }

    /**
     * Store anime data in the database.
     *
     * @param array $animeData
     */
    public function storeAnimeData(array $animeData): void
    {
        foreach ($animeData as $anime) {
            $aired_from = null;
            $aired_to = null;
            $timezone_offset = null;

            if (isset($anime['aired']['from']) && isset($anime['aired']['to'])) {
                $aired_from = $this->perseTimeToUtc($anime['aired']['from'])['utc_time'];
                $aired_to_time = $this->perseTimeToUtc($anime['aired']['to']);
                $aired_to = $aired_to_time['utc_time'];
                $timezone_offset = $aired_to_time['offset'];
            }

            $anime_response = Anime::updateOrCreate(
                ['mal_id' => $anime['mal_id']],
                [
                    'mal_id' => $anime['mal_id'],
                    'title' => $anime['title'],
                    'type' => $this->storeSettingAttributeValueByKey('anime_visual_category', $anime['type']),
                    'status' => $this->storeSettingAttributeValueByKey('anime_status', $anime['status']),
                    'approved' => $anime['approved'] ?? false,
                    'source' => $anime['source'] ?? null,
                    'source' => $anime['source'] ?? null,
                    'episodes' => $anime['episodes'] ?? null,
                    'airing' => $anime['airing'] ?? null,
                    'aired_from' => $aired_from,
                    'aired_to' => $aired_to,
                    'aired_timezone_offset' => $timezone_offset,
                    'duration' => $anime['duration'] ?? null,
                    'rating' => $anime['rating'] ?? null,
                    'score' => $anime['score'] ?? null,
                    'scored_by' => $anime['scored_by'] ?? null,
                    'rank' => $anime['rank'] ?? null,
                    'popularity' => $anime['popularity'] ?? null,
                    'members' => $anime['members'] ?? null,
                    'favorites' => $anime['favorites'] ?? null,
                    'synopsis' => $anime['synopsis'] ?? null,
                    'season' => $anime['season'] ?? null,
                    'year' => $anime['year'] ?? null,

                ],
            );

            if ($anime_response) {
                if (isset($anime['images'])) {
                    $this->storeAnimeImages($anime['images'], $anime_response);
                }
            }

            if ($anime_response) {
                if (isset($anime['titles'])) {
                    $this->storeTitles($anime['titles'], $anime_response);
                }
            }

            if ($anime_response) {
                if (isset($anime['trailer'])) {
                    $this->storeTrailer($anime['trailer'], $anime_response);
                }
            }

            if ($anime_response) {
                if (isset($anime['producers'])) {
                    $this->storeCompanyWithRole($anime['producers'], $anime_response, 'producers');
                }
            }

            if ($anime_response) {
                if (isset($anime['licensors'])) {
                    $this->storeCompanyWithRole($anime['licensors'], $anime_response, 'licensors');
                }
            }

            if ($anime_response) {
                if (isset($anime['studios'])) {
                    $this->storeCompanyWithRole($anime['studios'], $anime_response, 'studios');
                }
            }

            if ($anime_response) {
                if (isset($anime['genres'])) {
                    $this->storeGenreWithRole($anime['genres'], $anime_response, 'genres');
                }
            }

            if ($anime_response) {
                if (isset($anime['explicit_genres'])) {
                    $this->storeGenreWithRole($anime['explicit_genres'], $anime_response, 'explicit_genres');
                }
            }

            if ($anime_response) {
                if (isset($anime['themes'])) {
                    $this->storeGenreWithRole($anime['themes'], $anime_response, 'themes');
                }
            }

            if ($anime_response) {
                if (isset($anime['demographics'])) {
                    $this->storeGenreWithRole($anime['demographics'], $anime_response, 'demographics');
                }
            }

            if ($anime_response) {
                if (isset($anime['broadcast'])) {
                    $this->storeBroadcast($anime['broadcast'], $anime_response);
                }
            }
        }
    }

    public function get_next_page($key, $value = null): ?int
    {
        $response = null;
        $settingAttribute = SettingAttribute::firstOrCreate(['name' => $key]);
        $existingValue = $settingAttribute->settingAttributeValue()->first();
        $response = $settingAttribute->settingAttributeValue()->updateOrCreate(['id' => $existingValue->id ?? null], ['value' => $value ?? 1]);
        return $response ? $response->value : null;
    }

    public function storeSettingAttributeValueByKey($key, $value): ?int
    {
        $response = null;

        $settingAttribute = SettingAttribute::firstOrCreate(['name' => $key]);
        $existingValue = $settingAttribute->settingAttributeValue()->where('value', $value)->first();

        if (!$existingValue) {
            $response = $settingAttribute->settingAttributeValue()->create(['value' => $value]);
        } else {
            $response = $existingValue;
        }

        return $response ? $response->id : null;
    }


    public function storeAnimeImages($data, $anime_response): void
    {
        if ($anime_response) {
            foreach ($data as $key => $value) {
                foreach ($value as $url_key => $url) {
                    if (!empty($url)) {
                        $anime_response->images()->updateOrCreate(['url' => $url], ['quality' => $this->storeSettingAttributeValueByKey('image_quality', $url_key),'image_extention' => $key,'url' => $url]);
                    }

                }
            }
        }

    }


    public function storeTrailer($data, $anime_response): void
    {
        if ($anime_response) {
            $trailler_response = $anime_response->trailer()->updateOrCreate(['youtube_id' => $data['youtube_id'] ?? '' ,'url' => $data['url'] ?? '', 'embed_url' => $data['embed_url']], ['youtube_id' => $data['youtube_id'] ?? '','url' => $data['url'] ?? '' ,'embed_url' => $data['embed_url'] ?? '']);

            if (isset($data['images']) && is_array($data['images']) && $trailler_response) {

                $this->storeAnimeImages(['trailer' => $data['images']], $trailler_response);
            }

        }

    }

    public function storeTitles($data, $anime_response): void
    {
        if ($anime_response) {
            foreach ($data as $key => $value) {
                $is_default = 0;

                if ($value['type'] == 'Default') {
                    $is_default = 1;
                }

                $type = $this->storeSettingAttributeValueByKey('anime_title_type', $value['type']);
                $anime_response->titles()->updateOrCreate(['type' => $type,'title' => $value['title'],'is_default' => $is_default], ['type' => $type,'title' => $value['title'],'is_default' => $is_default]);
            }
        }

    }

    public function storeCompanyWithRole($data, $anime_response, $role): void
    {
        if ($anime_response) {
            $attach_data = [];

            foreach ($data as $key => $value) {
                $company = $this->storeCompany($value);
                $attach_data[$company['mal_id']] = ['role' => $this->storeSettingAttributeValueByKey('anime_company_role', $role)];
            }
            $anime_response->companies()->attach($attach_data);
        }

    }

    public function storeCompany($data): Company
    {
        $type = $this->storeSettingAttributeValueByKey('anime_company_type', $data['type']);
        return Company::updateOrCreate(['mal_id' => $data['mal_id'],'type' => $type,'name' => $data['name']], ['mal_id' => $data['mal_id'],'type' => $type,'name' => $data['name']]);

    }


    public function storeGenreWithRole($data, $anime_response, $role): void
    {
        if ($anime_response) {
            $attach_data = [];

            foreach ($data as $key => $value) {
                $company = $this->storeGenre($value);
                $attach_data[$company['mal_id']] = ['role' => $this->storeSettingAttributeValueByKey('anime_genre_role', $role)];
            }
            $anime_response->genres()->attach($attach_data);
        }

    }

    public function storeGenre($data): Genre
    {
        $type = $this->storeSettingAttributeValueByKey('anime_genre_type', $data['type']);
        return Genre::updateOrCreate(['mal_id' => $data['mal_id'],'type' => $type,'name' => $data['name']], ['mal_id' => $data['mal_id'],'type' => $type,'name' => $data['name']]);

    }

    public function storeBroadcast($data, $anime_response): void
    {
        $anime_response->broadcast()->updateOrCreate(['day' => $data['day'],'time' => $data['time'],'timezone' => $data['timezone'],'time_string' => $data['string']], ['day' => $data['day'],'time' => $data['time'],'timezone' => $data['timezone'],'time_string' => $data['string']]);
    }

    public function perseTimeToUtc($datetime): array
    {
        $carbonDate = Carbon::parse($datetime);
        $utcDatetime = $carbonDate->setTimezone('UTC')->format('Y-m-d H:i:s');
        $timezoneOffset = $carbonDate->format('P');

        return ['utc_time' => $utcDatetime,'offset' => $timezoneOffset];
    }


}
