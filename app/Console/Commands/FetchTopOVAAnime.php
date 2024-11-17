<?php

// app/Console/Commands/FetchTopOVAAnime.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnimeService;

class FetchTopOVAAnime extends Command
{
    protected $signature = 'fetch:top-ova-anime';
    protected $description = 'Fetch top OVA anime from Jikan API and store in the database';

    protected $animeService;

    public function __construct(AnimeService $animeService)
    {
        parent::__construct();
        $this->animeService = $animeService;
    }

    public function handle()
    {
        // Fetch data from the service
        $animeData = $this->animeService->fetchTopOVAAnime();

        if ($animeData) {
            // Store data using the service
            $this->animeService->storeAnimeData($animeData);
            $this->info('Top OVA anime data fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
