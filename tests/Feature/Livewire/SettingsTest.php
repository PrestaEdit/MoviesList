<?php
namespace Tests\Feature\Livewire;

use App\Livewire\Settings;
use App\Models\CoWatcher;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_renders_with_profile(): void
    {
        Profile::create(['name' => 'Jonathan']);
        Livewire::test(Settings::class)->assertSee('Jonathan');
    }

    public function test_can_update_name(): void
    {
        $profile = Profile::create(['name' => 'Jonathan']);

        Livewire::test(Settings::class)
            ->set('name', 'Marie')
            ->call('updateName');

        $this->assertEquals('Marie', $profile->fresh()->name);
    }

    public function test_can_add_co_watcher(): void
    {
        Profile::create(['name' => 'Jonathan']);

        Livewire::test(Settings::class)
            ->set('newCoWatcherName', 'Paul')
            ->call('addCoWatcher')
            ->assertSee('Paul');

        $this->assertDatabaseHas('co_watchers', ['name' => 'Paul']);
    }

    public function test_can_delete_co_watcher(): void
    {
        Profile::create(['name' => 'Jonathan']);
        $cw = CoWatcher::create(['name' => 'Paul']);

        Livewire::test(Settings::class)
            ->call('deleteCoWatcher', $cw->id);

        $this->assertDatabaseMissing('co_watchers', ['id' => $cw->id]);
    }
}
