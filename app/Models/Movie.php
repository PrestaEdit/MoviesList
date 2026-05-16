<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

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
