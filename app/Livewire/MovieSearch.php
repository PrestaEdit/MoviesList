<?php
namespace App\Livewire;

use App\Models\CoWatcher;
use App\Models\Movie;
use App\Models\WatchlistEntry;
use App\Services\TmdbService;
use Livewire\Component;

class MovieSearch extends Component
{
    public bool $open = false;
    public string $query = '';
    public array $results = [];
    public ?array $selectedMovie = null;
    public string $searchError = '';

    public string $status = 'to_watch';
    public ?int $rating = null;
    public string $comment = '';
    public string $watchedAt = '';
    public array $selectedCoWatcherIds = [];

    public function updatedQuery(): void
    {
        $this->searchError = '';
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }
        try {
            $this->results = app(TmdbService::class)->search($this->query);
        } catch (\Throwable $e) {
            $this->results = [];
            $this->searchError = 'Impossible de contacter TMDB. Vérifie ta clé API.';
        }
    }

    public function selectResult(int $tmdbId, string $type): void
    {
        $data = $type === 'tv'
            ? app(TmdbService::class)->getTvShow($tmdbId)
            : app(TmdbService::class)->getMovie($tmdbId);

        $this->selectedMovie = array_merge($data, ['media_type' => $type]);
        $this->results = [];
        $this->query = '';
    }

    public function addToWatchlist(): void
    {
        $this->validate([
            'status' => 'required|in:to_watch,watched',
            'rating' => 'nullable|integer|min:1|max:10',
            'watchedAt' => 'nullable|date',
        ]);

        $data = $this->selectedMovie;
        $movie = Movie::firstOrCreate(
            ['tmdb_id' => $data['id']],
            [
                'type' => $data['media_type'],
                'title' => $data['title'] ?? $data['name'] ?? '',
                'original_title' => $data['original_title'] ?? $data['original_name'] ?? null,
                'poster_path' => $data['poster_path'] ?? null,
                'backdrop_path' => $data['backdrop_path'] ?? null,
                'synopsis' => $data['overview'] ?? null,
                'release_date' => $data['release_date'] ?? $data['first_air_date'] ?? null,
                'duration' => $data['runtime'] ?? ($data['episode_run_time'][0] ?? null),
                'genres' => array_column($data['genres'] ?? [], 'name'),
                'tmdb_data' => $data,
            ]
        );

        $entry = WatchlistEntry::create([
            'movie_id' => $movie->id,
            'status' => $this->status,
            'rating' => $this->rating,
            'comment' => $this->comment ?: null,
            'watched_at' => $this->watchedAt ?: null,
        ]);

        if ($this->selectedCoWatcherIds) {
            $entry->coWatchers()->attach($this->selectedCoWatcherIds);
        }

        $this->reset(['selectedMovie', 'status', 'rating', 'comment', 'watchedAt', 'selectedCoWatcherIds', 'open']);
        $this->dispatch('movie-added');
    }

    public function render()
    {
        return view('livewire.movie-search', [
            'coWatchers' => CoWatcher::orderBy('name')->get(),
        ]);
    }
}
