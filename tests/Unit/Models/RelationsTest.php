<?php

namespace Tests\Unit\Models;

use App\Models\CoWatcher;
use App\Models\Movie;
use App\Models\WatchlistEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_movie_has_one_watchlist_entry(): void
    {
        $movie = Movie::factory()->create();
        $entry = WatchlistEntry::factory()->create(['movie_id' => $movie->id]);

        $this->assertTrue($movie->watchlistEntry->is($entry));
    }

    public function test_watchlist_entry_belongs_to_many_co_watchers(): void
    {
        $entry = WatchlistEntry::factory()->create();
        $cw = CoWatcher::factory()->create();
        $entry->coWatchers()->attach($cw->id);

        $this->assertTrue($entry->coWatchers->contains($cw));
    }

    public function test_movie_poster_url_returns_tmdb_url(): void
    {
        $movie = Movie::factory()->create(['poster_path' => '/abc.jpg']);
        $this->assertStringContainsString('/abc.jpg', $movie->posterUrl());
    }
}
