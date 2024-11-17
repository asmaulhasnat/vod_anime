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
        Schema::create('animes', function (Blueprint $table) {
            $table->id('mal_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('type')->constrained('setting_attribute_values', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('status')->constrained('setting_attribute_values', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('approved')->default(false);
            $table->string('source')->nullable();
            $table->integer('episodes')->nullable();
            $table->boolean('airing')->default(false);
            $table->dateTime('aired_from')->nullable();
            $table->dateTime('aired_to')->nullable();
            $table->string('aired_timezone_offset')->nullable();
            $table->string('duration')->nullable();
            $table->string('rating')->nullable();
            $table->float('score', 3, 2)->nullable();
            $table->integer('scored_by')->nullable();
            $table->integer('rank')->nullable();
            $table->integer('popularity')->nullable();
            $table->integer('members')->nullable();
            $table->integer('favorites')->nullable();
            $table->text('synopsis')->nullable();
            $table->text('background')->nullable();
            $table->string('season')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
