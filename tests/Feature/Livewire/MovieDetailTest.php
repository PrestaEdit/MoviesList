<?php
namespace Tests\Feature\Livewire;

use App\Livewire\MovieDetail;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MovieDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_renders_movie_title(): void
    {
        $movie = Movie::factory()->create(['title' => 'Inception']);
        $entry = WatchlistEntry::factory()->create(['movie_id' => $movie->id]);

        Livewire::test(MovieDetail::class, ['entry' => $entry])
            ->assertSee('Inception');
    }

    public function test_toggle_favorite(): void
    {
        $movie = Movie::factory()->create();
        $entry = WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'is_favorite' => false]);

        Livewire::test(MovieDetail::class, ['entry' => $entry])
            ->call('toggleFavorite');

        $this->assertTrue($entry->fresh()->is_favorite);
    }

    public function test_update_entry(): void
    {
        $movie = Movie::factory()->create();
        $entry = WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'status' => 'to_watch']);

        Livewire::test(MovieDetail::class, ['entry' => $entry])
            ->set('status', 'watched')
            ->set('rating', 8)
            ->set('comment', 'Super film')
            ->call('save');

        $this->assertEquals('watched', $entry->fresh()->status);
        $this->assertEquals(8, $entry->fresh()->rating);
    }
}
