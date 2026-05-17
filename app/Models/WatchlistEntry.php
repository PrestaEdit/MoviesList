<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchlistEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id', 'status', 'rating', 'comment', 'watched_at', 'is_favorite',
    ];

    protected $casts = [
        'watched_at' => 'date',
        'is_favorite' => 'boolean',
        'rating' => 'float',
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
