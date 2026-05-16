<?php
namespace Tests\Feature\Livewire;

use App\Livewire\MovieSearch;
use App\Models\CoWatcher;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use App\Services\TmdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class MovieSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_search_calls_tmdb_service(): void
    {
        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('search')
            ->once()
            ->with('Inception')
            ->andReturn([['id' => 1, 'title' => 'Inception', 'media_type' => 'movie', 'poster_path' => null, 'release_date' => '2010-07-16']]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(MovieSearch::class)
            ->set('query', 'Inception')
            ->assertSee('Inception');
    }

    public function test_add_movie_creates_watchlist_entry(): void
    {
        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('getMovie')->once()->with(27205)->andReturn([
            'id' => 27205, 'title' => 'Inception', 'original_title' => 'Inception',
            'poster_path' => '/poster.jpg', 'backdrop_path' => null,
            'overview' => 'Un rêve...', 'release_date' => '2010-07-16',
            'runtime' => 148, 'genres' => [['name' => 'Action']],
        ]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(MovieSearch::class)
            ->call('selectResult', 27205, 'movie')
            ->set('status', 'watched')
            ->set('rating', 9)
            ->set('comment', 'Excellent')
            ->set('watchedAt', '2026-05-01')
            ->call('addToWatchlist')
            ->assertDispatched('movie-added');

        $this->assertDatabaseHas('movies', ['tmdb_id' => 27205]);
        $this->assertDatabaseHas('watchlist_entries', ['status' => 'watched', 'rating' => 9]);
    }

    public function test_co_watchers_are_attached(): void
    {
        $cw = CoWatcher::create(['name' => 'Marie']);
        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('getMovie')->once()->with(1)->andReturn([
            'id' => 1, 'title' => 'Test', 'original_title' => 'Test',
            'poster_path' => null, 'backdrop_path' => null,
            'overview' => '', 'release_date' => '2024-01-01',
            'runtime' => 90, 'genres' => [],
        ]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(MovieSearch::class)
            ->call('selectResult', 1, 'movie')
            ->set('status', 'watched')
            ->set('selectedCoWatcherIds', [$cw->id])
            ->call('addToWatchlist');

        $entry = WatchlistEntry::first();
        $this->assertTrue($entry->coWatchers->contains($cw));
    }
}
