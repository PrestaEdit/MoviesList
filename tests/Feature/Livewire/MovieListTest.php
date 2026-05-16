<?php
namespace Tests\Feature\Livewire;

use App\Livewire\MovieList;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MovieListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_shows_all_watchlist_entries(): void
    {
        $movie = Movie::factory()->create(['title' => 'Inception']);
        WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'status' => 'watched']);

        Livewire::test(MovieList::class)->assertSee('Inception');
    }

    public function test_filter_by_status_to_watch(): void
    {
        $m1 = Movie::factory()->create(['title' => 'Film A']);
        $m2 = Movie::factory()->create(['title' => 'Film B']);
        WatchlistEntry::factory()->create(['movie_id' => $m1->id, 'status' => 'to_watch']);
        WatchlistEntry::factory()->create(['movie_id' => $m2->id, 'status' => 'watched']);

        Livewire::test(MovieList::class)
            ->set('statusFilter', 'to_watch')
            ->assertSee('Film A')
            ->assertDontSee('Film B');
    }

    public function test_filter_by_type(): void
    {
        $m1 = Movie::factory()->create(['title' => 'Un Film', 'type' => 'movie']);
        $m2 = Movie::factory()->create(['title' => 'Une Série', 'type' => 'tv']);
        WatchlistEntry::factory()->create(['movie_id' => $m1->id]);
        WatchlistEntry::factory()->create(['movie_id' => $m2->id]);

        Livewire::test(MovieList::class)
            ->set('typeFilter', 'tv')
            ->assertSee('Une Série')
            ->assertDontSee('Un Film');
    }

    public function test_listens_for_movie_added_event(): void
    {
        Livewire::test(MovieList::class)
            ->dispatch('movie-added')
            ->assertStatus(200);
    }
}
