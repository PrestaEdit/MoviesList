<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoWatcher extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function watchlistEntries(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(WatchlistEntry::class, 'watchlist_entry_co_watcher');
    }
}
