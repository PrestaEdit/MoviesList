<?php
namespace App\Livewire;

use App\Models\CoWatcher;
use App\Models\Profile;
use Livewire\Component;

class Settings extends Component
{
    public string $name = '';
    public string $newCoWatcherName = '';
    public string $theme = 'dark';
    public string $tmdbApiKey = '';
    public bool $tmdbApiKeyVisible = false;
    public bool $tmdbApiKeySaved = false;

    public function mount(): void
    {
        $profile = Profile::first();
        $this->name = $profile?->name ?? '';
        $this->theme = $profile?->theme ?? 'dark';
        $this->tmdbApiKey = $profile?->tmdb_api_key ?? '';
    }

    public function setTheme(string $theme): void
    {
        if (!in_array($theme, ['dark', 'light'])) return;
        Profile::first()?->update(['theme' => $theme]);
        $this->theme = $theme;
        $this->dispatch('theme-changed', theme: $theme);
    }

    public function updateName(): void
    {
        $this->validate(['name' => 'required|string|max:50']);
        Profile::first()?->update(['name' => $this->name]);
    }

    public function saveTmdbApiKey(): void
    {
        $this->validate(['tmdbApiKey' => 'nullable|string|max:255']);
        Profile::first()?->update(['tmdb_api_key' => $this->tmdbApiKey ?: null]);
        $this->tmdbApiKeySaved = true;
        $this->js('setTimeout(() => $wire.set("tmdbApiKeySaved", false), 2000)');
    }

    public function toggleTmdbApiKeyVisibility(): void
    {
        $this->tmdbApiKeyVisible = !$this->tmdbApiKeyVisible;
    }

    public function addCoWatcher(): void
    {
        $this->validate(['newCoWatcherName' => 'required|string|max:50']);
        CoWatcher::create(['name' => $this->newCoWatcherName]);
        $this->newCoWatcherName = '';
    }

    public function deleteCoWatcher(int $id): void
    {
        CoWatcher::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.settings', [
            'coWatchers' => CoWatcher::orderBy('name')->get(),
        ]);
    }
}
