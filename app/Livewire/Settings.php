<?php
namespace App\Livewire;

use App\Models\CoWatcher;
use App\Models\Profile;
use Livewire\Component;

class Settings extends Component
{
    public string $name = '';
    public string $newCoWatcherName = '';

    public function mount(): void
    {
        $this->name = Profile::first()?->name ?? '';
    }

    public function updateName(): void
    {
        $this->validate(['name' => 'required|string|max:50']);
        Profile::first()?->update(['name' => $this->name]);
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
