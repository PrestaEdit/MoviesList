<?php
namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use App\Services\TmdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_shows_favorites(): void
    {
        $movie = Movie::factory()->create(['title' => 'Inception']);
        WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'is_favorite' => true]);

        Livewire::test(Dashboard::class)->assertSee('Inception');
    }

    public function test_random_pick_comes_from_to_watch(): void
    {
        $movie = Movie::factory()->create(['title' => 'Film à voir']);
        WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'status' => 'to_watch']);

        Livewire::test(Dashboard::class)->assertSee('Film à voir');
    }

    public function test_repick_changes_random_movie(): void
    {
        Movie::factory()->count(5)->create()->each(function ($m) {
            WatchlistEntry::factory()->create(['movie_id' => $m->id, 'status' => 'to_watch']);
        });

        Livewire::test(Dashboard::class)
            ->call('repick')
            ->assertStatus(200);
    }

    public function test_tmdb_recommendations_section(): void
    {
        $movie = Movie::factory()->create(['tmdb_id' => 27205, 'type' => 'movie']);
        WatchlistEntry::factory()->create([
            'movie_id' => $movie->id,
            'status' => 'watched',
            'watched_at' => now(),
        ]);

        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('getRecommendations')
            ->once()
            ->andReturn([
                ['id' => 999, 'title' => 'Interstellar', 'poster_path' => null, 'media_type' => 'movie', 'release_date' => '2014-11-05'],
            ]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(Dashboard::class)->assertSee('Interstellar');
    }
}
