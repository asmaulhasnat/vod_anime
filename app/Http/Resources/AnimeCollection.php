<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnimeCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [

            'data' => AnimeResource::collection($this->collection),
        ];
    }
}
