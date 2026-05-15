# MoviesList Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers-extended-cc:subagent-driven-development (recommended) or superpowers-extended-cc:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Construire une application mobile Android (NativePHP) de gestion de liste de films/séries avec intégration TMDB en français.

**Architecture:** Laravel 11 + NativePHP Mobile, UI en Livewire 3 + Alpine.js + Preline UI (Tailwind v4), données stockées localement en SQLite. Un `TmdbService` centralise tous les appels API.

**Tech Stack:** Laravel 11, NativePHP Mobile, Livewire 3, Alpine.js, Preline UI, Tailwind CSS v4, SQLite, TMDB API v3

---

## Structure des fichiers

```
app/
  Models/
    Profile.php
    Movie.php
    WatchlistEntry.php
    CoWatcher.php
  Services/
    TmdbService.php
  Livewire/
    Onboarding.php
    Dashboard.php
    MovieList.php
    MovieSearch.php
    MovieDetail.php
    Settings.php
resources/views/
  layouts/
    app.blade.php        ← layout principal (tabs + avatar)
    guest.blade.php      ← layout onboarding (sans tabs)
  livewire/
    onboarding.blade.php
    dashboard.blade.php
    movie-list.blade.php
    movie-search.blade.php
    movie-detail.blade.php
    settings.blade.php
database/migrations/
  *_create_profiles_table.php
  *_create_movies_table.php
  *_create_watchlist_entries_table.php
  *_create_co_watchers_table.php
  *_create_watchlist_entry_co_watcher_table.php
routes/web.php
tests/
  Feature/
    Livewire/
      OnboardingTest.php
      DashboardTest.php
      MovieListTest.php
      MovieSearchTest.php
      MovieDetailTest.php
      SettingsTest.php
  Unit/
    TmdbServiceTest.php
```

---

## Task 0: Scaffold du projet

**Goal:** Installer Laravel 11 + NativePHP Mobile + Livewire 3 + Preline/Tailwind v4 et configurer l'environnement de base.

**Files:**
- Create: `composer.json`, `package.json`, `.env`, `config/nativephp.php`
- Modify: `config/app.php`, `vite.config.js`, `tailwind.config.js`

**Acceptance Criteria:**
- [ ] `php artisan serve` démarre sans erreur
- [ ] `npm run build` compile sans erreur
- [ ] `php artisan native:run android` lance l'app sur émulateur

**Steps:**

- [ ] **Step 1 : Créer le projet Laravel 11**

```bash
composer create-project laravel/laravel:^11.0 .
```

- [ ] **Step 2 : Installer NativePHP Mobile**

```bash
composer require nativephp/mobile
php artisan native:install
```

Quand demandé, choisir Android.

- [ ] **Step 3 : Installer Livewire 3**

```bash
composer require livewire/livewire:^3.0
```

- [ ] **Step 4 : Installer Tailwind CSS v4 + Preline UI**

```bash
npm install tailwindcss @tailwindcss/vite preline
```

Modifier `vite.config.js` :

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true }),
        tailwindcss(),
    ],
})
```

`resources/css/app.css` :

```css
@import 'tailwindcss';
```

`resources/js/app.js` :

```js
import 'preline'
```

- [ ] **Step 5 : Configurer SQLite dans `.env`**

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
TMDB_API_KEY=your_key_here
```

```bash
touch database/database.sqlite
```

- [ ] **Step 6 : Configurer NativePHP** dans `config/nativephp.php` — vérifier que `id` et `name` correspondent au projet.

- [ ] **Step 7 : Commit**

```bash
git add -A
git commit -m "chore: scaffold Laravel 11 + NativePHP + Livewire 3 + Preline"
```

---

## Task 1: Migrations & Modèles

**Goal:** Créer toutes les migrations et modèles Eloquent avec leurs relations.

**Files:**
- Create: `database/migrations/*` (5 fichiers)
- Create: `app/Models/Profile.php`, `Movie.php`, `WatchlistEntry.php`, `CoWatcher.php`
- Test: `tests/Unit/Models/RelationsTest.php`

**Acceptance Criteria:**
- [ ] `php artisan migrate` s'exécute sans erreur
- [ ] Les relations Eloquent (hasMany, belongsToMany) fonctionnent
- [ ] Les tests de relations passent

**Steps:**

- [ ] **Step 1 : Générer les migrations**

```bash
php artisan make:migration create_profiles_table
php artisan make:migration create_movies_table
php artisan make:migration create_watchlist_entries_table
php artisan make:migration create_co_watchers_table
php artisan make:migration create_watchlist_entry_co_watcher_table
```

- [ ] **Step 2 : Écrire la migration `profiles`**

```php
Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

- [ ] **Step 3 : Écrire la migration `movies`**

```php
Schema::create('movies', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tmdb_id')->unique();
    $table->enum('type', ['movie', 'tv']);
    $table->string('title');
    $table->string('original_title')->nullable();
    $table->string('poster_path')->nullable();
    $table->string('backdrop_path')->nullable();
    $table->text('synopsis')->nullable();
    $table->date('release_date')->nullable();
    $table->unsignedSmallInteger('duration')->nullable(); // minutes
    $table->json('genres')->nullable();
    $table->json('tmdb_data')->nullable();
    $table->timestamps();
});
```

- [ ] **Step 4 : Écrire la migration `watchlist_entries`**

```php
Schema::create('watchlist_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
    $table->enum('status', ['to_watch', 'watched'])->default('to_watch');
    $table->unsignedTinyInteger('rating')->nullable();
    $table->text('comment')->nullable();
    $table->date('watched_at')->nullable();
    $table->boolean('is_favorite')->default(false);
    $table->timestamps();
});
```

- [ ] **Step 5 : Écrire la migration `co_watchers`**

```php
Schema::create('co_watchers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

- [ ] **Step 6 : Écrire la migration pivot `watchlist_entry_co_watcher`**

```php
Schema::create('watchlist_entry_co_watcher', function (Blueprint $table) {
    $table->foreignId('watchlist_entry_id')->constrained()->cascadeOnDelete();
    $table->foreignId('co_watcher_id')->constrained()->cascadeOnDelete();
    $table->primary(['watchlist_entry_id', 'co_watcher_id']);
});
```

- [ ] **Step 7 : Créer `app/Models/Profile.php`**

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['name'];
}
```

- [ ] **Step 8 : Créer `app/Models/Movie.php`**

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id', 'type', 'title', 'original_title',
        'poster_path', 'backdrop_path', 'synopsis',
        'release_date', 'duration', 'genres', 'tmdb_data',
    ];

    protected $casts = [
        'genres' => 'array',
        'tmdb_data' => 'array',
        'release_date' => 'date',
    ];

    public function watchlistEntry(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WatchlistEntry::class);
    }

    public function posterUrl(): string
    {
        return $this->poster_path
            ? 'https://image.tmdb.org/t/p/w342' . $this->poster_path
            : '';
    }

    public function backdropUrl(): string
    {
        return $this->backdrop_path
            ? 'https://image.tmdb.org/t/p/w780' . $this->backdrop_path
            : '';
    }
}
```

- [ ] **Step 9 : Créer `app/Models/WatchlistEntry.php`**

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchlistEntry extends Model
{
    protected $fillable = [
        'movie_id', 'status', 'rating', 'comment', 'watched_at', 'is_favorite',
    ];

    protected $casts = [
        'watched_at' => 'date',
        'is_favorite' => 'boolean',
    ];

    public function movie(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function coWatchers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(CoWatcher::class, 'watchlist_entry_co_watcher');
    }
}
```

- [ ] **Step 10 : Créer `app/Models/CoWatcher.php`**

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoWatcher extends Model
{
    protected $fillable = ['name'];

    public function watchlistEntries(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(WatchlistEntry::class, 'watchlist_entry_co_watcher');
    }
}
```

- [ ] **Step 11 : Écrire les tests de relations**

`tests/Unit/Models/RelationsTest.php` :

```php
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
```

- [ ] **Step 12 : Créer les factories** (nécessaires pour les tests)

```bash
php artisan make:factory MovieFactory --model=Movie
php artisan make:factory WatchlistEntryFactory --model=WatchlistEntry
php artisan make:factory CoWatcherFactory --model=CoWatcher
```

`database/factories/MovieFactory.php` :

```php
public function definition(): array
{
    return [
        'tmdb_id' => fake()->unique()->numberBetween(1, 999999),
        'type' => fake()->randomElement(['movie', 'tv']),
        'title' => fake()->sentence(3),
        'poster_path' => '/poster.jpg',
        'synopsis' => fake()->paragraph(),
        'duration' => fake()->numberBetween(20, 180),
        'genres' => ['Action'],
    ];
}
```

`database/factories/WatchlistEntryFactory.php` :

```php
public function definition(): array
{
    return [
        'movie_id' => Movie::factory(),
        'status' => 'to_watch',
        'is_favorite' => false,
    ];
}
```

`database/factories/CoWatcherFactory.php` :

```php
public function definition(): array
{
    return ['name' => fake()->firstName()];
}
```

- [ ] **Step 13 : Lancer les migrations et les tests**

```bash
php artisan migrate
php artisan test tests/Unit/Models/RelationsTest.php
```

Résultat attendu : 3 tests passent.

- [ ] **Step 14 : Commit**

```bash
git add -A
git commit -m "feat: migrations, models and relations"
```

---

## Task 2: TmdbService

**Goal:** Créer le service centralisé d'accès à l'API TMDB avec langue française.

**Files:**
- Create: `app/Services/TmdbService.php`
- Test: `tests/Unit/TmdbServiceTest.php`

**Acceptance Criteria:**
- [ ] `search()` retourne des résultats en français
- [ ] `getMovie()` et `getTvShow()` retournent les détails d'un titre
- [ ] `getRecommendations()` retourne des titres similaires
- [ ] Les tests utilisent `Http::fake()` et passent sans appels réseau

**Steps:**

- [ ] **Step 1 : Écrire le test en premier**

`tests/Unit/TmdbServiceTest.php` :

```php
<?php
namespace Tests\Unit;

use App\Services\TmdbService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TmdbServiceTest extends TestCase
{
    private TmdbService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.tmdb.key' => 'test-key']);
        $this->service = new TmdbService();
    }

    public function test_search_returns_array_of_results(): void
    {
        Http::fake([
            'api.themoviedb.org/3/search/multi*' => Http::response([
                'results' => [
                    ['id' => 1, 'title' => 'Inception', 'media_type' => 'movie'],
                ],
            ]),
        ]);

        $results = $this->service->search('Inception');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('Inception', $results[0]['title']);
    }

    public function test_get_movie_returns_movie_data(): void
    {
        Http::fake([
            'api.themoviedb.org/3/movie/27205*' => Http::response([
                'id' => 27205,
                'title' => 'Inception',
                'runtime' => 148,
            ]),
        ]);

        $movie = $this->service->getMovie(27205);

        $this->assertEquals(27205, $movie['id']);
        $this->assertEquals(148, $movie['runtime']);
    }

    public function test_get_recommendations_returns_array(): void
    {
        Http::fake([
            'api.themoviedb.org/3/movie/27205/recommendations*' => Http::response([
                'results' => [['id' => 2, 'title' => 'Interstellar', 'media_type' => 'movie']],
            ]),
        ]);

        $recs = $this->service->getRecommendations(27205, 'movie');

        $this->assertCount(1, $recs);
        $this->assertEquals('Interstellar', $recs[0]['title']);
    }
}
```

- [ ] **Step 2 : Vérifier que les tests échouent**

```bash
php artisan test tests/Unit/TmdbServiceTest.php
```

Résultat attendu : FAIL (classe non trouvée).

- [ ] **Step 3 : Ajouter la config TMDB** dans `config/services.php`

```php
'tmdb' => [
    'key' => env('TMDB_API_KEY'),
    'base_url' => 'https://api.themoviedb.org/3',
],
```

- [ ] **Step 4 : Créer `app/Services/TmdbService.php`**

```php
<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    private string $baseUrl;
    private string $apiKey;
    private array $defaultParams;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url', 'https://api.themoviedb.org/3');
        $this->apiKey = config('services.tmdb.key');
        $this->defaultParams = [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'region' => 'FR',
        ];
    }

    public function search(string $query): array
    {
        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/search/multi", array_merge($this->defaultParams, [
                'query' => $query,
                'include_adult' => false,
            ]));

        return $response->json('results', []);
    }

    public function getMovie(int $tmdbId): array
    {
        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/movie/{$tmdbId}", array_merge($this->defaultParams, [
                'append_to_response' => 'genres',
            ]));

        return $response->json() ?? [];
    }

    public function getTvShow(int $tmdbId): array
    {
        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/tv/{$tmdbId}", $this->defaultParams);

        return $response->json() ?? [];
    }

    public function getRecommendations(int $tmdbId, string $type): array
    {
        $endpoint = $type === 'tv' ? "tv/{$tmdbId}" : "movie/{$tmdbId}";

        $response = Http::timeout(5)
            ->get("{$this->baseUrl}/{$endpoint}/recommendations", $this->defaultParams);

        return $response->json('results', []);
    }
}
```

- [ ] **Step 5 : Vérifier que les tests passent**

```bash
php artisan test tests/Unit/TmdbServiceTest.php
```

Résultat attendu : 3 tests passent.

- [ ] **Step 6 : Enregistrer le service dans `AppServiceProvider`**

```php
use App\Services\TmdbService;

$this->app->singleton(TmdbService::class);
```

- [ ] **Step 7 : Commit**

```bash
git add -A
git commit -m "feat: TmdbService with French language support"
```

---

## Task 3: Layout principal & Onboarding

**Goal:** Créer le layout avec tabs + avatar et l'écran d'onboarding.

**Files:**
- Create: `resources/views/layouts/app.blade.php`
- Create: `resources/views/layouts/guest.blade.php`
- Create: `app/Livewire/Onboarding.php`
- Create: `resources/views/livewire/onboarding.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Livewire/OnboardingTest.php`

**Acceptance Criteria:**
- [ ] L'onboarding s'affiche si aucun profil n'existe
- [ ] Soumission du prénom crée un `Profile` et redirige vers `/`
- [ ] Le layout principal affiche les 2 tabs et l'avatar

**Steps:**

- [ ] **Step 1 : Écrire le test Onboarding**

```php
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
```

- [ ] **Step 2 : Vérifier que les tests échouent**

```bash
php artisan test tests/Feature/Livewire/OnboardingTest.php
```

Résultat attendu : FAIL.

- [ ] **Step 3 : Créer `app/Livewire/Onboarding.php`**

```php
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
```

- [ ] **Step 4 : Créer `resources/views/layouts/guest.blade.php`**

```html
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesList</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-900 min-h-screen">
    {{ $slot }}
    @livewireScripts
</body>
</html>
```

- [ ] **Step 5 : Créer `resources/views/livewire/onboarding.blade.php`**

```html
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm text-center space-y-6">
        <div class="text-5xl">🎬</div>
        <h1 class="text-2xl font-bold text-white">Bienvenue sur MoviesList</h1>
        <p class="text-slate-400">Comment tu t'appelles ?</p>
        <form wire:submit="save" class="space-y-4">
            <input
                wire:model="name"
                type="text"
                placeholder="Ton prénom…"
                class="py-3 px-4 block w-full bg-slate-800 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:ring-red-500 focus:border-red-500"
                autofocus
            >
            @error('name')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
            <button type="submit" class="w-full py-3 px-4 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition-colors">
                C'est parti →
            </button>
        </form>
    </div>
</div>
```

- [ ] **Step 6 : Créer `resources/views/layouts/app.blade.php`**

```html
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesList</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-900 min-h-screen" x-data="{ activeTab: $persist('dashboard') }">

    <!-- Header -->
    <header class="sticky top-0 z-10 bg-slate-900 border-b border-slate-800 px-4 py-3 flex justify-between items-center">
        <p class="text-white font-semibold text-sm">
            Bonsoir, {{ \App\Models\Profile::first()?->name ?? '' }} 👋
        </p>
        <a href="/settings" wire:navigate>
            <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr(\App\Models\Profile::first()?->name ?? 'M', 0, 1)) }}
            </div>
        </a>
    </header>

    <!-- Tabs -->
    <nav class="border-b border-slate-800 flex">
        <button
            @click="activeTab = 'dashboard'"
            :class="activeTab === 'dashboard' ? 'border-b-2 border-red-500 text-red-500' : 'text-slate-500'"
            class="flex-1 py-3 text-sm font-medium transition-colors"
        >Accueil</button>
        <button
            @click="activeTab = 'movies'"
            :class="activeTab === 'movies' ? 'border-b-2 border-red-500 text-red-500' : 'text-slate-500'"
            class="flex-1 py-3 text-sm font-medium transition-colors"
        >Mes films</button>
    </nav>

    <!-- Content -->
    <main class="pb-safe">
        <div x-show="activeTab === 'dashboard'">
            <livewire:dashboard />
        </div>
        <div x-show="activeTab === 'movies'">
            <livewire:movie-list />
        </div>
    </main>

    @livewireScripts
</body>
</html>
```

- [ ] **Step 7 : Configurer `routes/web.php`**

```php
<?php
use App\Livewire\Onboarding;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;
use App\Models\Profile;

Route::get('/', function () {
    if (!Profile::exists()) {
        return redirect('/onboarding');
    }
    return view('home');
});

Route::get('/onboarding', Onboarding::class);
Route::get('/settings', Settings::class);
```

- [ ] **Step 8 : Créer `resources/views/home.blade.php`**

```html
@extends('layouts.app')
```

Attention : `layouts.app` utilise directement `<livewire:dashboard />` et `<livewire:movie-list />` donc cette vue peut être vide.

En réalité, pour que le layout fonctionne comme une page Blade standard, utiliser la vue directement :

`resources/views/home.blade.php` :
```html
<x-layouts.app />
```

Et modifier `layouts/app.blade.php` pour être un composant Blade anonyme en renommant en `resources/views/components/layouts/app.blade.php` — ou plus simplement, retourner la vue `home` depuis la route et inclure le layout dedans. Approche la plus simple :

`resources/views/home.blade.php` :
```html
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesList</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-900 min-h-screen" x-data="{ activeTab: $persist('dashboard') }">
    <header class="sticky top-0 z-10 bg-slate-900 border-b border-slate-800 px-4 py-3 flex justify-between items-center">
        <p class="text-white font-semibold text-sm">
            Bonsoir, {{ \App\Models\Profile::first()?->name ?? '' }} 👋
        </p>
        <a href="/settings" wire:navigate>
            <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr(\App\Models\Profile::first()?->name ?? 'M', 0, 1)) }}
            </div>
        </a>
    </header>
    <nav class="border-b border-slate-800 flex">
        <button @click="activeTab = 'dashboard'"
            :class="activeTab === 'dashboard' ? 'border-b-2 border-red-500 text-red-500' : 'text-slate-500'"
            class="flex-1 py-3 text-sm font-medium transition-colors">Accueil</button>
        <button @click="activeTab = 'movies'"
            :class="activeTab === 'movies' ? 'border-b-2 border-red-500 text-red-500' : 'text-slate-500'"
            class="flex-1 py-3 text-sm font-medium transition-colors">Mes films</button>
    </nav>
    <main>
        <div x-show="activeTab === 'dashboard'"><livewire:dashboard /></div>
        <div x-show="activeTab === 'movies'"><livewire:movie-list /></div>
    </main>
    @livewireScripts
</body>
</html>
```

- [ ] **Step 9 : Lancer les tests**

```bash
php artisan test tests/Feature/Livewire/OnboardingTest.php
```

Résultat attendu : 3 tests passent.

- [ ] **Step 10 : Commit**

```bash
git add -A
git commit -m "feat: onboarding screen and main app layout with tabs"
```

---

## Task 4: Paramètres (Settings)

**Goal:** Écran de gestion du prénom et des co-watchers.

**Files:**
- Create: `app/Livewire/Settings.php`
- Create: `resources/views/livewire/settings.blade.php`
- Test: `tests/Feature/Livewire/SettingsTest.php`

**Acceptance Criteria:**
- [ ] Modifier le prénom met à jour le profil
- [ ] Ajouter un co-watcher l'insère en base
- [ ] Supprimer un co-watcher le retire de la liste

**Steps:**

- [ ] **Step 1 : Écrire les tests**

```php
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
```

- [ ] **Step 2 : Vérifier que les tests échouent**

```bash
php artisan test tests/Feature/Livewire/SettingsTest.php
```

- [ ] **Step 3 : Créer `app/Livewire/Settings.php`**

```php
<?php
namespace App\Livewire;

use App\Models\CoWatcher;
use App\Models\Profile;
use Livewire\Attributes\Rule;
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
```

- [ ] **Step 4 : Créer `resources/views/livewire/settings.blade.php`**

```html
<div class="min-h-screen bg-slate-900">
    <!-- Header -->
    <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-3">
        <a href="/" wire:navigate class="text-slate-400 hover:text-white">←</a>
        <h1 class="text-white font-semibold">Paramètres</h1>
    </div>

    <div class="px-4 py-6 space-y-8">
        <!-- Profil -->
        <section>
            <div class="bg-slate-800 rounded-xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-white text-lg font-bold">
                    {{ strtoupper(substr($this->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <input
                        wire:model="name"
                        wire:blur="updateName"
                        type="text"
                        class="bg-transparent text-white font-medium w-full focus:outline-none border-b border-transparent focus:border-red-500 transition-colors"
                    >
                    <p class="text-slate-500 text-xs mt-1">Appuyer pour modifier</p>
                </div>
            </div>
        </section>

        <!-- Co-watchers -->
        <section>
            <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">Co-watchers</h2>
            <div class="space-y-2">
                @foreach($coWatchers as $cw)
                <div class="bg-slate-800 rounded-lg px-4 py-3 flex justify-between items-center">
                    <span class="text-white text-sm">{{ $cw->name }}</span>
                    <button wire:click="deleteCoWatcher({{ $cw->id }})" class="text-red-500 hover:text-red-400 text-lg leading-none">✕</button>
                </div>
                @endforeach

                <!-- Add -->
                <form wire:submit="addCoWatcher" class="flex gap-2">
                    <input
                        wire:model="newCoWatcherName"
                        type="text"
                        placeholder="Nouveau co-watcher…"
                        class="flex-1 bg-slate-800 border border-dashed border-slate-600 rounded-lg px-4 py-3 text-white text-sm placeholder-slate-500 focus:border-red-500 focus:outline-none"
                    >
                    <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-4 rounded-lg text-sm font-medium transition-colors">+</button>
                </form>
            </div>
        </section>
    </div>
</div>
```

- [ ] **Step 5 : Lancer les tests**

```bash
php artisan test tests/Feature/Livewire/SettingsTest.php
```

Résultat attendu : 4 tests passent.

- [ ] **Step 6 : Commit**

```bash
git add -A
git commit -m "feat: settings screen with profile name and co-watchers management"
```

---

## Task 5: Recherche & Ajout de film (Modal)

**Goal:** Modal de recherche TMDB en temps réel + formulaire d'ajout à la watchlist.

**Files:**
- Create: `app/Livewire/MovieSearch.php`
- Create: `resources/views/livewire/movie-search.blade.php`
- Test: `tests/Feature/Livewire/MovieSearchTest.php`

**Acceptance Criteria:**
- [ ] La recherche appelle `TmdbService::search()` avec debounce
- [ ] Sélectionner un résultat affiche le formulaire d'ajout
- [ ] Soumettre le formulaire crée `Movie` + `WatchlistEntry` en base
- [ ] Les co-watchers sélectionnés sont attachés à l'entrée

**Steps:**

- [ ] **Step 1 : Écrire les tests**

```php
<?php
namespace Tests\Feature\Livewire;

use App\Livewire\MovieSearch;
use App\Models\CoWatcher;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use App\Services\TmdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class MovieSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_search_calls_tmdb_service(): void
    {
        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('search')
            ->once()
            ->with('Inception')
            ->andReturn([['id' => 1, 'title' => 'Inception', 'media_type' => 'movie', 'poster_path' => null, 'release_date' => '2010-07-16']]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(MovieSearch::class)
            ->set('query', 'Inception')
            ->assertSee('Inception');
    }

    public function test_add_movie_creates_watchlist_entry(): void
    {
        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('getMovie')->once()->with(27205)->andReturn([
            'id' => 27205, 'title' => 'Inception', 'original_title' => 'Inception',
            'poster_path' => '/poster.jpg', 'backdrop_path' => null,
            'overview' => 'Un rêve...', 'release_date' => '2010-07-16',
            'runtime' => 148, 'genres' => [['name' => 'Action']],
        ]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(MovieSearch::class)
            ->call('selectResult', 27205, 'movie')
            ->set('status', 'watched')
            ->set('rating', 9)
            ->set('comment', 'Excellent')
            ->set('watchedAt', '2026-05-01')
            ->call('addToWatchlist')
            ->assertDispatched('movie-added');

        $this->assertDatabaseHas('movies', ['tmdb_id' => 27205]);
        $this->assertDatabaseHas('watchlist_entries', ['status' => 'watched', 'rating' => 9]);
    }

    public function test_co_watchers_are_attached(): void
    {
        $cw = CoWatcher::create(['name' => 'Marie']);
        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('getMovie')->once()->with(1)->andReturn([
            'id' => 1, 'title' => 'Test', 'original_title' => 'Test',
            'poster_path' => null, 'backdrop_path' => null,
            'overview' => '', 'release_date' => '2024-01-01',
            'runtime' => 90, 'genres' => [],
        ]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(MovieSearch::class)
            ->call('selectResult', 1, 'movie')
            ->set('status', 'watched')
            ->set('selectedCoWatcherIds', [$cw->id])
            ->call('addToWatchlist');

        $entry = WatchlistEntry::first();
        $this->assertTrue($entry->coWatchers->contains($cw));
    }
}
```

- [ ] **Step 2 : Créer `app/Livewire/MovieSearch.php`**

```php
<?php
namespace App\Livewire;

use App\Models\CoWatcher;
use App\Models\Movie;
use App\Models\WatchlistEntry;
use App\Services\TmdbService;
use Livewire\Attributes\On;
use Livewire\Component;

class MovieSearch extends Component
{
    public bool $open = false;
    public string $query = '';
    public array $results = [];
    public ?array $selectedMovie = null;

    public string $status = 'to_watch';
    public ?int $rating = null;
    public string $comment = '';
    public string $watchedAt = '';
    public array $selectedCoWatcherIds = [];

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }
        $this->results = app(TmdbService::class)->search($this->query);
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
                'duration' => $data['runtime'] ?? $data['episode_run_time'][0] ?? null,
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
```

- [ ] **Step 3 : Créer `resources/views/livewire/movie-search.blade.php`**

```html
<div>
    <!-- FAB -->
    <button
        wire:click="$set('open', true)"
        class="fixed bottom-6 right-6 z-20 w-14 h-14 bg-red-600 hover:bg-red-500 rounded-full flex items-center justify-center text-white text-2xl shadow-lg shadow-red-900/50 transition-colors"
    >+</button>

    <!-- Modal -->
    <div x-show="$wire.open" class="fixed inset-0 z-30 bg-slate-900/95 flex flex-col" x-transition>
        <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-3">
            <button wire:click="$set('open', false)" class="text-slate-400">✕</button>
            <h2 class="text-white font-semibold">Ajouter un titre</h2>
        </div>

        @if(!$selectedMovie)
        <!-- Recherche -->
        <div class="px-4 py-4">
            <input
                wire:model.live.debounce.300ms="query"
                type="text"
                placeholder="Rechercher un film ou une série…"
                class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:border-red-500 focus:outline-none"
                autofocus
            >
        </div>
        <div class="flex-1 overflow-y-auto px-4 space-y-2">
            @foreach($results as $result)
                @if(in_array($result['media_type'] ?? '', ['movie', 'tv']))
                <button
                    wire:click="selectResult({{ $result['id'] }}, '{{ $result['media_type'] }}')"
                    class="w-full flex items-center gap-3 bg-slate-800 rounded-lg p-3 text-left hover:bg-slate-700 transition-colors"
                >
                    @if($result['poster_path'] ?? null)
                    <img src="https://image.tmdb.org/t/p/w92{{ $result['poster_path'] }}" class="w-10 h-14 object-cover rounded" alt="">
                    @else
                    <div class="w-10 h-14 bg-slate-700 rounded flex items-center justify-center text-slate-500 text-lg">🎬</div>
                    @endif
                    <div>
                        <p class="text-white text-sm font-medium">{{ $result['title'] ?? $result['name'] ?? '' }}</p>
                        <p class="text-slate-500 text-xs">{{ substr($result['release_date'] ?? $result['first_air_date'] ?? '', 0, 4) }} · {{ $result['media_type'] === 'tv' ? 'Série' : 'Film' }}</p>
                    </div>
                </button>
                @endif
            @endforeach
        </div>

        @else
        <!-- Formulaire d'ajout -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-5">
            <!-- Header titre -->
            <div class="flex gap-3">
                @if($selectedMovie['poster_path'] ?? null)
                <img src="https://image.tmdb.org/t/p/w154{{ $selectedMovie['poster_path'] }}" class="w-16 h-24 object-cover rounded-lg" alt="">
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-semibold">{{ $selectedMovie['title'] ?? $selectedMovie['name'] }}</h3>
                    <p class="text-slate-400 text-sm mt-1 line-clamp-3">{{ $selectedMovie['overview'] ?? '' }}</p>
                </div>
            </div>

            <!-- Statut -->
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Statut</label>
                <div class="flex gap-2">
                    <button wire:click="$set('status', 'to_watch')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'to_watch' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        À voir
                    </button>
                    <button wire:click="$set('status', 'watched')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'watched' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        Vu ✓
                    </button>
                </div>
            </div>

            @if($status === 'watched')
            <!-- Date -->
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Date de visionnage</label>
                <input wire:model="watchedAt" type="date"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none">
            </div>

            <!-- Note -->
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Note ({{ $rating ?? '—' }}/10)</label>
                <input wire:model="rating" type="range" min="1" max="10" class="w-full accent-red-500">
            </div>

            <!-- Commentaire -->
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Commentaire</label>
                <textarea wire:model="comment" rows="2"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:border-red-500 focus:outline-none resize-none"
                    placeholder="Ton avis…"></textarea>
            </div>

            <!-- Co-watchers -->
            @if($coWatchers->count())
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Avec qui ?</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($coWatchers as $cw)
                    <button wire:click="$toggle('selectedCoWatcherIds', {{ $cw->id }})"
                        class="px-3 py-1 rounded-full text-sm transition-colors {{ in_array($cw->id, $selectedCoWatcherIds) ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ $cw->name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            <button wire:click="addToWatchlist"
                class="w-full py-3 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition-colors">
                Ajouter à ma liste
            </button>
        </div>
        @endif
    </div>
</div>
```

- [ ] **Step 4 : Lancer les tests**

```bash
php artisan test tests/Feature/Livewire/MovieSearchTest.php
```

Résultat attendu : 3 tests passent.

- [ ] **Step 5 : Commit**

```bash
git add -A
git commit -m "feat: TMDB movie search and add to watchlist modal"
```

---

## Task 6: Listing avec filtres

**Goal:** Grille de films/séries avec filtres rapides et avancés réactifs.

**Files:**
- Create: `app/Livewire/MovieList.php`
- Create: `resources/views/livewire/movie-list.blade.php`
- Test: `tests/Feature/Livewire/MovieListTest.php`

**Acceptance Criteria:**
- [ ] La liste affiche les entrées de la watchlist en grille
- [ ] Les filtres rapides (Tous/À voir/Vus) filtrent correctement
- [ ] Les filtres avancés (type, genre, durée) fonctionnent
- [ ] Le composant `MovieSearch` est inclus avec le bouton FAB

**Steps:**

- [ ] **Step 1 : Écrire les tests**

```php
<?php
namespace Tests\Feature\Livewire;

use App\Livewire\MovieList;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MovieListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_shows_all_watchlist_entries(): void
    {
        $movie = Movie::factory()->create(['title' => 'Inception']);
        WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'status' => 'watched']);

        Livewire::test(MovieList::class)->assertSee('Inception');
    }

    public function test_filter_by_status_to_watch(): void
    {
        $m1 = Movie::factory()->create(['title' => 'Film A']);
        $m2 = Movie::factory()->create(['title' => 'Film B']);
        WatchlistEntry::factory()->create(['movie_id' => $m1->id, 'status' => 'to_watch']);
        WatchlistEntry::factory()->create(['movie_id' => $m2->id, 'status' => 'watched']);

        Livewire::test(MovieList::class)
            ->set('statusFilter', 'to_watch')
            ->assertSee('Film A')
            ->assertDontSee('Film B');
    }

    public function test_filter_by_type(): void
    {
        $m1 = Movie::factory()->create(['title' => 'Un Film', 'type' => 'movie']);
        $m2 = Movie::factory()->create(['title' => 'Une Série', 'type' => 'tv']);
        WatchlistEntry::factory()->create(['movie_id' => $m1->id]);
        WatchlistEntry::factory()->create(['movie_id' => $m2->id]);

        Livewire::test(MovieList::class)
            ->set('typeFilter', 'tv')
            ->assertSee('Une Série')
            ->assertDontSee('Un Film');
    }

    public function test_listens_for_movie_added_event(): void
    {
        Livewire::test(MovieList::class)
            ->dispatch('movie-added')
            ->assertStatus(200);
    }
}
```

- [ ] **Step 2 : Créer `app/Livewire/MovieList.php`**

```php
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
    public bool $showAdvancedFilters = false;

    #[On('movie-added')]
    public function refresh(): void {}

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
                'short' => $query->where('movies.duration', '<=', 60),
                'medium' => $query->whereBetween('movies.duration', [61, 120]),
                'long' => $query->where('movies.duration', '>', 120),
                default => null,
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
            'genres' => $genres,
        ]);
    }
}
```

- [ ] **Step 3 : Créer `resources/views/livewire/movie-list.blade.php`**

```html
<div class="relative min-h-screen">
    <!-- Filtres rapides -->
    <div class="px-4 py-3 flex gap-2 border-b border-slate-800 overflow-x-auto">
        <button wire:click="$set('statusFilter', '')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === '' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">Tous</button>
        <button wire:click="$set('statusFilter', 'to_watch')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'to_watch' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">À voir</button>
        <button wire:click="$set('statusFilter', 'watched')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'watched' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">Vus</button>
        <button wire:click="$toggle('showAdvancedFilters')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $showAdvancedFilters ? 'bg-slate-600 text-white' : 'bg-slate-800 text-slate-400' }}">⚙ Filtres</button>
    </div>

    <!-- Filtres avancés -->
    @if($showAdvancedFilters)
    <div class="px-4 py-3 border-b border-slate-800 space-y-3 bg-slate-800/50">
        <div class="flex gap-2">
            <select wire:model.live="typeFilter" class="flex-1 bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:border-red-500 focus:outline-none">
                <option value="">Tous types</option>
                <option value="movie">Films</option>
                <option value="tv">Séries</option>
            </select>
            <select wire:model.live="durationFilter" class="flex-1 bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:border-red-500 focus:outline-none">
                <option value="">Toutes durées</option>
                <option value="short">Court (≤ 60 min)</option>
                <option value="medium">Moyen (61–120 min)</option>
                <option value="long">Long (> 120 min)</option>
            </select>
        </div>
        @if($genres->count())
        <select wire:model.live="genreFilter" class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:border-red-500 focus:outline-none">
            <option value="">Tous genres</option>
            @foreach($genres as $genre)
            <option value="{{ $genre }}">{{ $genre }}</option>
            @endforeach
        </select>
        @endif
    </div>
    @endif

    <!-- Grille -->
    @if($entries->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="text-5xl mb-4">🎬</div>
        <p class="text-slate-400">Ta liste est vide.</p>
        <p class="text-slate-500 text-sm mt-1">Appuie sur + pour ajouter un titre.</p>
    </div>
    @else
    <div class="grid grid-cols-3 gap-1 p-1">
        @foreach($entries as $entry)
        <a href="/movie/{{ $entry->id }}" wire:navigate class="relative aspect-[2/3] block">
            @if($entry->movie->poster_path)
            <img src="{{ $entry->movie->posterUrl() }}" class="w-full h-full object-cover rounded" alt="{{ $entry->movie->title }}">
            @else
            <div class="w-full h-full bg-slate-800 rounded flex items-center justify-center text-slate-600 text-2xl">🎬</div>
            @endif
            @if($entry->status === 'watched')
            <div class="absolute top-1 right-1 w-5 h-5 bg-green-600 rounded-full flex items-center justify-center text-white text-xs">✓</div>
            @endif
            @if($entry->is_favorite)
            <div class="absolute top-1 left-1 text-yellow-400 text-xs">★</div>
            @endif
        </a>
        @endforeach
    </div>
    @endif

    <!-- Modal recherche (inclut le FAB) -->
    <livewire:movie-search />
</div>
```

- [ ] **Step 4 : Ajouter la route de détail dans `routes/web.php`**

```php
Route::get('/movie/{entry}', \App\Livewire\MovieDetail::class);
```

- [ ] **Step 5 : Lancer les tests**

```bash
php artisan test tests/Feature/Livewire/MovieListTest.php
```

Résultat attendu : 4 tests passent.

- [ ] **Step 6 : Commit**

```bash
git add -A
git commit -m "feat: movie listing with quick and advanced filters"
```

---

## Task 7: Détail d'un film

**Goal:** Page de détail avec infos TMDB, avis personnel, favoris et édition.

**Files:**
- Create: `app/Livewire/MovieDetail.php`
- Create: `resources/views/livewire/movie-detail.blade.php`
- Test: `tests/Feature/Livewire/MovieDetailTest.php`

**Acceptance Criteria:**
- [ ] La page affiche le titre, synopsis, note, co-watchers
- [ ] Toggler le favori met à jour `is_favorite` en base
- [ ] Modifier l'entrée met à jour les champs en base

**Steps:**

- [ ] **Step 1 : Écrire les tests**

```php
<?php
namespace Tests\Feature\Livewire;

use App\Livewire\MovieDetail;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MovieDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_renders_movie_title(): void
    {
        $movie = Movie::factory()->create(['title' => 'Inception']);
        $entry = WatchlistEntry::factory()->create(['movie_id' => $movie->id]);

        Livewire::test(MovieDetail::class, ['entry' => $entry])
            ->assertSee('Inception');
    }

    public function test_toggle_favorite(): void
    {
        $movie = Movie::factory()->create();
        $entry = WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'is_favorite' => false]);

        Livewire::test(MovieDetail::class, ['entry' => $entry])
            ->call('toggleFavorite');

        $this->assertTrue($entry->fresh()->is_favorite);
    }

    public function test_update_entry(): void
    {
        $movie = Movie::factory()->create();
        $entry = WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'status' => 'to_watch']);

        Livewire::test(MovieDetail::class, ['entry' => $entry])
            ->set('status', 'watched')
            ->set('rating', 8)
            ->set('comment', 'Super film')
            ->call('save');

        $this->assertEquals('watched', $entry->fresh()->status);
        $this->assertEquals(8, $entry->fresh()->rating);
    }
}
```

- [ ] **Step 2 : Créer `app/Livewire/MovieDetail.php`**

```php
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
```

- [ ] **Step 3 : Créer `resources/views/livewire/movie-detail.blade.php`**

```html
<div class="min-h-screen bg-slate-900">
    <!-- Backdrop -->
    @if($entry->movie->backdrop_path)
    <div class="relative h-48">
        <img src="{{ $entry->movie->backdropUrl() }}" class="w-full h-full object-cover" alt="">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900"></div>
        <a href="/" wire:navigate class="absolute top-4 left-4 text-white bg-slate-900/60 rounded-full w-8 h-8 flex items-center justify-center">←</a>
    </div>
    @else
    <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-3">
        <a href="/" wire:navigate class="text-slate-400">←</a>
    </div>
    @endif

    <div class="px-4 py-4 space-y-5">
        <!-- Titre + favori -->
        <div class="flex items-start justify-between gap-3">
            <div class="flex gap-3">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-16 h-24 object-cover rounded-lg shrink-0" alt="">
                @endif
                <div>
                    <h1 class="text-white font-bold text-lg leading-tight">{{ $entry->movie->title }}</h1>
                    <p class="text-slate-400 text-sm mt-1">
                        {{ $entry->movie->release_date?->year }}
                        · {{ $entry->movie->type === 'tv' ? 'Série' : 'Film' }}
                        @if($entry->movie->duration) · {{ $entry->movie->duration }} min @endif
                    </p>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($entry->movie->genres ?? [] as $genre)
                        <span class="text-xs bg-slate-700 text-slate-300 px-2 py-0.5 rounded-full">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <button wire:click="toggleFavorite" class="text-2xl shrink-0 mt-1">
                {{ $entry->is_favorite ? '★' : '☆' }}
            </button>
        </div>

        <!-- Synopsis -->
        @if($entry->movie->synopsis)
        <p class="text-slate-400 text-sm leading-relaxed">{{ $entry->movie->synopsis }}</p>
        @endif

        <!-- Mon avis -->
        <div class="border-t border-slate-800 pt-4">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-white font-semibold">Mon avis</h2>
                <button wire:click="$toggle('editing')" class="text-red-500 text-sm">
                    {{ $editing ? 'Annuler' : 'Modifier' }}
                </button>
            </div>

            @if(!$editing)
            <div class="space-y-2">
                <p class="text-sm">
                    <span class="text-slate-500">Statut : </span>
                    <span class="text-white">{{ $entry->status === 'watched' ? 'Vu ✓' : 'À voir' }}</span>
                </p>
                @if($entry->rating)
                <p class="text-sm"><span class="text-slate-500">Note : </span><span class="text-white">{{ $entry->rating }}/10</span></p>
                @endif
                @if($entry->comment)
                <p class="text-sm"><span class="text-slate-500">Commentaire : </span><span class="text-white">{{ $entry->comment }}</span></p>
                @endif
                @if($entry->watched_at)
                <p class="text-sm"><span class="text-slate-500">Vu le : </span><span class="text-white">{{ $entry->watched_at->format('d/m/Y') }}</span></p>
                @endif
                @if($entry->coWatchers->count())
                <p class="text-sm">
                    <span class="text-slate-500">Avec : </span>
                    <span class="text-white">{{ $entry->coWatchers->pluck('name')->join(', ') }}</span>
                </p>
                @endif
            </div>
            @else
            <!-- Formulaire édition -->
            <div class="space-y-4">
                <div class="flex gap-2">
                    <button wire:click="$set('status', 'to_watch')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'to_watch' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">À voir</button>
                    <button wire:click="$set('status', 'watched')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'watched' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">Vu ✓</button>
                </div>
                @if($status === 'watched')
                <input wire:model="watchedAt" type="date" class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none">
                <div>
                    <label class="text-slate-400 text-xs mb-1 block">Note : {{ $rating ?? '—' }}/10</label>
                    <input wire:model="rating" type="range" min="1" max="10" class="w-full accent-red-500">
                </div>
                <textarea wire:model="comment" rows="2" placeholder="Ton avis…"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:border-red-500 focus:outline-none resize-none"></textarea>
                @if($coWatchers->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($coWatchers as $cw)
                    <button wire:click="$toggle('selectedCoWatcherIds', {{ $cw->id }})"
                        class="px-3 py-1 rounded-full text-sm transition-colors {{ in_array($cw->id, $selectedCoWatcherIds) ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ $cw->name }}
                    </button>
                    @endforeach
                </div>
                @endif
                @endif
                <button wire:click="save" class="w-full py-3 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition-colors">Enregistrer</button>
            </div>
            @endif
        </div>
    </div>
</div>
```

- [ ] **Step 4 : Lancer les tests**

```bash
php artisan test tests/Feature/Livewire/MovieDetailTest.php
```

Résultat attendu : 3 tests passent.

- [ ] **Step 5 : Commit**

```bash
git add -A
git commit -m "feat: movie detail page with edit and favorite toggle"
```

---

## Task 8: Dashboard

**Goal:** Écran d'accueil avec les 4 sections (favoris, récents, pioche, recommandations TMDB).

**Files:**
- Create: `app/Livewire/Dashboard.php`
- Create: `resources/views/livewire/dashboard.blade.php`
- Test: `tests/Feature/Livewire/DashboardTest.php`

**Acceptance Criteria:**
- [ ] La section favoris affiche les `is_favorite = true`
- [ ] La section récents affiche les `status = watched` triés par `watched_at` DESC
- [ ] La pioche retourne un titre aléatoire parmi les `to_watch`
- [ ] "Repioche" change la pioche sans rechargement de page
- [ ] Les sections vides sont masquées

**Steps:**

- [ ] **Step 1 : Écrire les tests**

```php
<?php
namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\WatchlistEntry;
use App\Services\TmdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::create(['name' => 'Jonathan']);
    }

    public function test_shows_favorites(): void
    {
        $movie = Movie::factory()->create(['title' => 'Inception']);
        WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'is_favorite' => true]);

        Livewire::test(Dashboard::class)->assertSee('Inception');
    }

    public function test_random_pick_comes_from_to_watch(): void
    {
        $movie = Movie::factory()->create(['title' => 'Film à voir']);
        WatchlistEntry::factory()->create(['movie_id' => $movie->id, 'status' => 'to_watch']);

        $component = Livewire::test(Dashboard::class);
        $component->assertSee('Film à voir');
    }

    public function test_repick_changes_random_movie(): void
    {
        Movie::factory()->count(5)->create()->each(function ($m) {
            WatchlistEntry::factory()->create(['movie_id' => $m->id, 'status' => 'to_watch']);
        });

        Livewire::test(Dashboard::class)
            ->call('repick')
            ->assertStatus(200);
    }

    public function test_tmdb_recommendations_section(): void
    {
        $movie = Movie::factory()->create(['tmdb_id' => 27205, 'type' => 'movie']);
        WatchlistEntry::factory()->create([
            'movie_id' => $movie->id,
            'status' => 'watched',
            'watched_at' => now(),
        ]);

        $tmdb = Mockery::mock(TmdbService::class);
        $tmdb->shouldReceive('getRecommendations')
            ->once()
            ->andReturn([
                ['id' => 999, 'title' => 'Interstellar', 'poster_path' => null, 'media_type' => 'movie', 'release_date' => '2014-11-05'],
            ]);
        $this->app->instance(TmdbService::class, $tmdb);

        Livewire::test(Dashboard::class)->assertSee('Interstellar');
    }
}
```

- [ ] **Step 2 : Créer `app/Livewire/Dashboard.php`**

```php
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
                    $recommendations[] = $rec;
                }
            }
        }

        return array_slice(
            array_values(array_unique($recommendations, SORT_REGULAR)),
            0,
            6
        );
    }
}
```

- [ ] **Step 3 : Créer `resources/views/livewire/dashboard.blade.php`**

```html
<div class="px-4 py-4 space-y-6 pb-24">

    {{-- Favoris --}}
    @if($favorites->count())
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">⭐ Favoris</h2>
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4">
            @foreach($favorites as $entry)
            <a href="/movie/{{ $entry->id }}" wire:navigate class="shrink-0 w-24">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-24 h-36 object-cover rounded-lg" alt="{{ $entry->movie->title }}">
                @else
                <div class="w-24 h-36 bg-slate-800 rounded-lg flex items-center justify-center text-slate-600 text-2xl">🎬</div>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Vus récemment --}}
    @if($recent->count())
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">🕐 Vus récemment</h2>
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4">
            @foreach($recent as $entry)
            <a href="/movie/{{ $entry->id }}" wire:navigate class="shrink-0 w-24">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-24 h-36 object-cover rounded-lg" alt="{{ $entry->movie->title }}">
                @else
                <div class="w-24 h-36 bg-slate-800 rounded-lg flex items-center justify-center text-slate-600 text-2xl">🎬</div>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Pioche du jour --}}
    @if($randomPick)
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">🎲 Pioche du jour</h2>
        <div class="bg-slate-800 rounded-xl overflow-hidden">
            <a href="/movie/{{ $randomPick->id }}" wire:navigate class="flex gap-4 p-4">
                @if($randomPick->movie->poster_path)
                <img src="{{ $randomPick->movie->posterUrl() }}" class="w-16 h-24 object-cover rounded-lg shrink-0" alt="">
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-semibold truncate">{{ $randomPick->movie->title }}</h3>
                    <p class="text-slate-400 text-sm mt-1">
                        {{ $randomPick->movie->release_date?->year }}
                        · {{ $randomPick->movie->type === 'tv' ? 'Série' : 'Film' }}
                    </p>
                    @if($randomPick->movie->synopsis)
                    <p class="text-slate-500 text-xs mt-2 line-clamp-2">{{ $randomPick->movie->synopsis }}</p>
                    @endif
                </div>
            </a>
            <div class="px-4 pb-4">
                <button wire:click="repick" class="w-full py-2 border border-slate-600 hover:border-red-500 text-slate-400 hover:text-red-500 rounded-lg text-sm transition-colors">
                    🔀 Repioche
                </button>
            </div>
        </div>
    </section>
    @endif

    {{-- Recommandations TMDB --}}
    @if(count($recommendations))
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">✨ Tu pourrais aimer</h2>
        <div class="grid grid-cols-3 gap-2">
            @foreach($recommendations as $rec)
            <div class="aspect-[2/3] bg-slate-800 rounded-lg overflow-hidden">
                @if($rec['poster_path'] ?? null)
                <img src="https://image.tmdb.org/t/p/w342{{ $rec['poster_path'] }}" class="w-full h-full object-cover" alt="{{ $rec['title'] ?? $rec['name'] ?? '' }}">
                @else
                <div class="w-full h-full flex items-center justify-center text-slate-600 text-2xl">🎬</div>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- État vide total --}}
    @if($favorites->isEmpty() && $recent->isEmpty() && !$randomPick)
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">🎬</div>
        <p class="text-white font-semibold">Ta liste est vide</p>
        <p class="text-slate-400 text-sm mt-2">Va dans "Mes films" et appuie sur + pour commencer.</p>
    </div>
    @endif

</div>
```

- [ ] **Step 4 : Lancer les tests**

```bash
php artisan test tests/Feature/Livewire/DashboardTest.php
```

Résultat attendu : 4 tests passent.

- [ ] **Step 5 : Lancer la suite complète**

```bash
php artisan test
```

Résultat attendu : tous les tests passent.

- [ ] **Step 6 : Commit**

```bash
git add -A
git commit -m "feat: dashboard with favorites, recents, random pick and TMDB recommendations"
```

---

## Task 9: Build Android & tests manuels

**Goal:** Vérifier que l'app se lance sur un émulateur Android et que le flux complet fonctionne.

**Files:**
- Modify: `config/nativephp.php` (identifiant d'app, icône, splash)

**Acceptance Criteria:**
- [ ] L'app se lance sur l'émulateur Android
- [ ] L'onboarding s'affiche au premier lancement
- [ ] Le flux complet fonctionne : onboarding → dashboard → recherche → ajout → listing → détail → paramètres

**Steps:**

- [ ] **Step 1 : Vérifier la config NativePHP**

`config/nativephp.php` — s'assurer que :
```php
'app_id' => 'com.prestaedit.movieslist',
'app_name' => 'MoviesList',
```

- [ ] **Step 2 : Compiler les assets**

```bash
npm run build
```

- [ ] **Step 3 : Lancer sur émulateur Android**

```bash
php artisan native:run android
```

- [ ] **Step 4 : Tester le flux complet manuellement**

Checklist de test manuel :
- [ ] Onboarding : saisir "Jonathan", appuyer sur "C'est parti" → arrive sur le Dashboard
- [ ] Dashboard vide : le message d'invite s'affiche
- [ ] Tab "Mes films" → liste vide avec message
- [ ] Appuyer sur + → modal de recherche s'ouvre
- [ ] Taper "Inception" → résultats TMDB s'affichent en français
- [ ] Sélectionner un résultat → formulaire d'ajout s'affiche avec affiche et synopsis
- [ ] Choisir "Vu", noter 9/10, commenter, choisir une date → "Ajouter à ma liste"
- [ ] Retour listing → film apparaît avec badge ✓
- [ ] Tap sur le film → page détail avec toutes les infos
- [ ] Toggler favori (★) → re-tap (☆) → re-tap (★)
- [ ] Modifier : changer le commentaire → Enregistrer
- [ ] Retour Dashboard → film apparaît dans "Vus récemment" et "Favoris"
- [ ] Pioche du jour : ajouter un titre "À voir" → apparaît dans la pioche
- [ ] Bouton "Repioche" → change le titre (si plusieurs "À voir")
- [ ] Avatar → Paramètres → modifier le prénom → retour → prénom mis à jour
- [ ] Ajouter un co-watcher "Marie" → supprimer → disparaît

- [ ] **Step 5 : Commit final**

```bash
git add -A
git commit -m "feat: final build config and manual test checklist"
```

---

## Résumé des tâches

| # | Tâche | Tests |
|---|---|---|
| 0 | Scaffold (Laravel + NativePHP + Livewire + Preline) | — |
| 1 | Migrations & Modèles | `RelationsTest` (3) |
| 2 | TmdbService | `TmdbServiceTest` (3) |
| 3 | Layout + Onboarding | `OnboardingTest` (3) |
| 4 | Settings | `SettingsTest` (4) |
| 5 | Recherche & Ajout | `MovieSearchTest` (3) |
| 6 | Listing + Filtres | `MovieListTest` (4) |
| 7 | Détail film | `MovieDetailTest` (3) |
| 8 | Dashboard | `DashboardTest` (4) |
| 9 | Build Android | Tests manuels |
