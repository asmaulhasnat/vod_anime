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
        Schema::create('trailers', function (Blueprint $table) {
            $table->id('mal_id');
            $table->foreignId('anime_id')->constrained('animes', 'mal_id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('youtube_id');
            $table->string('url')->nullable();
            $table->string('embed_url')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trailers');
    }
};
