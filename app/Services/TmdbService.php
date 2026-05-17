<?php
namespace App\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url', 'https://api.themoviedb.org/3');
    }

    private function apiKey(): string
    {
        return Profile::first()?->tmdb_api_key
            ?: config('services.tmdb.key', '');
    }

    private function defaultParams(): array
    {
        return [
            'api_key' => $this->apiKey(),
            'language' => 'fr-FR',
            'region' => 'FR',
        ];
    }

    public function search(string $query): array
    {
        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/search/multi", array_merge($this->defaultParams(), [
                'query' => $query,
                'include_adult' => false,
            ]));

        return $response->json('results', []);
    }

    public function getMovie(int $tmdbId): array
    {
        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/movie/{$tmdbId}", array_merge($this->defaultParams(), [
                'append_to_response' => 'genres',
            ]));

        return $response->json() ?? [];
    }

    public function getTvShow(int $tmdbId): array
    {
        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/tv/{$tmdbId}", $this->defaultParams());

        return $response->json() ?? [];
    }

    public function getRecommendations(int $tmdbId, string $type): array
    {
        $endpoint = $type === 'tv' ? "tv/{$tmdbId}" : "movie/{$tmdbId}";

        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/{$endpoint}/recommendations", $this->defaultParams());

        return $response->json('results', []);
    }
}
