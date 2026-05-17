<?php
namespace App\Livewire;

use App\Models\CoWatcher;
use App\Models\WatchlistEntry;
use Livewire\Component;

class MovieDetail extends Component
{
    public WatchlistEntry $entry;
    public bool $editing = false;

    public string $status = '';
    public ?int $rating = null;
    public string $comment = '';
    public string $watchedAt = '';
    public array $selectedCoWatcherIds = [];

    public function mount(WatchlistEntry $entry): void
    {
        $this->entry = $entry->load(['movie', 'coWatchers']);
        $this->status = $entry->status;
        $this->rating = $entry->rating;
        $this->comment = $entry->comment ?? '';
        $this->watchedAt = $entry->watched_at?->format('Y-m-d') ?? '';
        $this->selectedCoWatcherIds = $entry->coWatchers->pluck('id')->toArray();
    }

    public function toggleFavorite(): void
    {
        $this->entry->update(['is_favorite' => !$this->entry->is_favorite]);
        $this->entry->refresh();
    }

    public function toggleCoWatcher(int $id): void
    {
        if (in_array($id, $this->selectedCoWatcherIds)) {
            $this->selectedCoWatcherIds = array_values(
                array_filter($this->selectedCoWatcherIds, fn($cwId) => $cwId !== $id)
            );
        } else {
            $this->selectedCoWatcherIds[] = $id;
        }
    }

    public function save(): void
    {
        $this->validate([
            'status' => 'required|in:to_watch,watched',
            'rating' => 'nullable|integer|min:1|max:10',
            'watchedAt' => 'nullable|date',
        ]);

        $this->entry->update([
            'status' => $this->status,
            'rating' => $this->rating,
            'comment' => $this->comment ?: null,
            'watched_at' => $this->watchedAt ?: null,
        ]);

        $this->entry->coWatchers()->sync($this->selectedCoWatcherIds);
        $this->entry->refresh();
        $this->editing = false;
    }

    public function render()
    {
        return view('livewire.movie-detail', [
            'coWatchers' => CoWatcher::orderBy('name')->get(),
        ]);
    }
}
