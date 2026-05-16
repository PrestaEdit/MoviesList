<?php
namespace Tests\Unit;

use App\Services\TmdbService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TmdbServiceTest extends TestCase
{
    private TmdbService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.tmdb.key' => 'test-key']);
        $this->service = new TmdbService();
    }

    public function test_search_returns_array_of_results(): void
    {
        Http::fake([
            'api.themoviedb.org/3/search/multi*' => Http::response([
                'results' => [
                    ['id' => 1, 'title' => 'Inception', 'media_type' => 'movie'],
                ],
            ]),
        ]);

        $results = $this->service->search('Inception');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('Inception', $results[0]['title']);
    }

    public function test_get_movie_returns_movie_data(): void
    {
        Http::fake([
            'api.themoviedb.org/3/movie/27205*' => Http::response([
                'id' => 27205,
                'title' => 'Inception',
                'runtime' => 148,
            ]),
        ]);

        $movie = $this->service->getMovie(27205);

        $this->assertEquals(27205, $movie['id']);
        $this->assertEquals(148, $movie['runtime']);
    }

    public function test_get_recommendations_returns_array(): void
    {
        Http::fake([
            'api.themoviedb.org/3/movie/27205/recommendations*' => Http::response([
                'results' => [['id' => 2, 'title' => 'Interstellar', 'media_type' => 'movie']],
            ]),
        ]);

        $recs = $this->service->getRecommendations(27205, 'movie');

        $this->assertCount(1, $recs);
        $this->assertEquals('Interstellar', $recs[0]['title']);
    }
}
