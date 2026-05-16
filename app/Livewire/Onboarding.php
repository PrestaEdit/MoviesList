<?php
namespace App\Livewire;

use App\Models\Profile;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Onboarding extends Component
{
    #[Rule('required|string|max:50')]
    public string $name = '';

    public function save(): void
    {
        $this->validate();
        Profile::create(['name' => $this->name]);
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.onboarding')
            ->layout('layouts.guest');
    }
}
