<?php
namespace Tests\Feature\Livewire;

use App\Livewire\Onboarding;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboarding_renders(): void
    {
        Livewire::test(Onboarding::class)
            ->assertStatus(200);
    }

    public function test_saves_profile_and_redirects(): void
    {
        Livewire::test(Onboarding::class)
            ->set('name', 'Jonathan')
            ->call('save')
            ->assertRedirect('/');

        $this->assertDatabaseHas('profiles', ['name' => 'Jonathan']);
    }

    public function test_name_is_required(): void
    {
        Livewire::test(Onboarding::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }
}
