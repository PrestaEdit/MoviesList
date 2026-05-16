<?php
namespace App\Livewire;

use App\Models\Movie;
use App\Models\WatchlistEntry;
use App\Services\TmdbService;
use Livewire\Component;

class Dashboard extends Component
{
    public ?WatchlistEntry $randomPick = null;

    public function mount(): void
    {
        $this->randomPick = $this->getRandomPick();
    }

    public function repick(): void
    {
        $this->randomPick = $this->getRandomPick();
    }

    private function getRandomPick(): ?WatchlistEntry
    {
        return WatchlistEntry::with('movie')
            ->where('status', 'to_watch')
            ->inRandomOrder()
            ->first();
    }

    public function render()
    {
        $favorites = WatchlistEntry::with('movie')
            ->where('is_favorite', true)
            ->latest()
            ->take(10)
            ->get();

        $recent = WatchlistEntry::with('movie')
            ->where('status', 'watched')
            ->orderByDesc('watched_at')
            ->take(10)
            ->get();

        $recommendations = $this->getRecommendations();

        return view('livewire.dashboard', compact('favorites', 'recent', 'recommendations'));
    }

    private function getRecommendations(): array
    {
        $recentEntries = WatchlistEntry::with('movie')
            ->where('status', 'watched')
            ->orderByDesc('watched_at')
            ->take(3)
            ->get();

        if ($recentEntries->isEmpty()) {
            return [];
        }

        $existingTmdbIds = Movie::whereHas('watchlistEntry')->pluck('tmdb_id')->toArray();
        $tmdb = app(TmdbService::class);
        $recommendations = [];

        foreach ($recentEntries as $entry) {
            $recs = $tmdb->getRecommendations($entry->movie->tmdb_id, $entry->movie->type);
            foreach ($recs as $rec) {
                if (!in_array($rec['id'], $existingTmdbIds)) {
                    $recommendations[$rec['id']] = $rec;
                }
            }
        }

        return array_values(array_slice($recommendations, 0, 6));
    }
}
