<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watchlist_entry_co_watcher', function (Blueprint $table) {
            $table->foreignId('watchlist_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('co_watcher_id')->constrained()->cascadeOnDelete();
            $table->primary(['watchlist_entry_id', 'co_watcher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchlist_entry_co_watcher');
    }
};
