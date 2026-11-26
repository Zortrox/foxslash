<?php

use App\Models\SteamGame;
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
        Schema::create('steam_game_aliases', function (Blueprint $table) {
            $table
                ->foreignIdFor(SteamGame::class, 'appid')
                ->constrained('steam_games')
                ->cascadeOnDelete();
            $table->string('alias', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steam_game_aliases');
    }
};
