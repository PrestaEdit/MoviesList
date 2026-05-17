<?php
namespace App\Livewire;

use App\Models\WatchlistEntry;
use Livewire\Attributes\On;
use Livewire\Component;

class MovieList extends Component
{
    public string $statusFilter = '';
    public string $typeFilter = '';
    public string $genreFilter = '';
    public string $durationFilter = '';

    #[On('movie-added')]
    public function refresh(): void {}

    public function setTypeFilter(string $type): void
    {
        $this->typeFilter = ($this->typeFilter === $type) ? '' : $type;
    }

    public function setDurationFilter(string $duration): void
    {
        $this->durationFilter = ($this->durationFilter === $duration) ? '' : $duration;
    }

    public function toggleGenre(string $genre): void
    {
        $this->genreFilter = ($this->genreFilter === $genre) ? '' : $genre;
    }

    public function resetFilters(): void
    {
        $this->typeFilter = '';
        $this->durationFilter = '';
        $this->genreFilter = '';
    }

    public function render()
    {
        $query = WatchlistEntry::with('movie')
            ->join('movies', 'watchlist_entries.movie_id', '=', 'movies.id')
            ->select('watchlist_entries.*');

        if ($this->statusFilter) {
            $query->where('watchlist_entries.status', $this->statusFilter);
        }
        if ($this->typeFilter) {
            $query->where('movies.type', $this->typeFilter);
        }
        if ($this->genreFilter) {
            $query->whereJsonContains('movies.genres', $this->genreFilter);
        }
        if ($this->durationFilter) {
            match ($this->durationFilter) {
                'short'  => $query->where('movies.duration', '<=', 60),
                'medium' => $query->whereBetween('movies.duration', [61, 120]),
                'long'   => $query->where('movies.duration', '>', 120),
                default  => null,
            };
        }

        $genres = \App\Models\Movie::whereHas('watchlistEntry')
            ->get()
            ->flatMap(fn ($m) => $m->genres ?? [])
            ->unique()
            ->sort()
            ->values();

        return view('livewire.movie-list', [
            'entries' => $query->orderByDesc('watchlist_entries.created_at')->get(),
            'genres'  => $genres,
        ]);
    }
}
