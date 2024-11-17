<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->id('mal_id');
            $table->foreignId('anime_id')->constrained('animes', 'mal_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('type')->constrained('setting_attribute_values', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
