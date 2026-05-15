<?php
use App\Livewire\Onboarding;
use Illuminate\Support\Facades\Route;
use App\Models\Profile;

Route::get('/', function () {
    if (!Profile::exists()) {
        return redirect('/onboarding');
    }
    return view('home');
});

Route::get('/onboarding', Onboarding::class);

// Routes for classes that will be created in later tasks
Route::get('/settings', \App\Livewire\Settings::class);
Route::get('/movie/{entry}', \App\Livewire\MovieDetail::class);
